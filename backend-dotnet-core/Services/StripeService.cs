using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Options;
using Stripe;
using Stripe.Checkout;
using CasperVPN.Data;
using CasperVPN.DTOs;
using CasperVPN.Helpers;
using CasperVPN.Models;

namespace CasperVPN.Services;

/// <summary>
/// Stripe payment service implementation
/// </summary>
public class StripeService : IStripeService
{
    private readonly ApplicationDbContext _context;
    private readonly StripeSettings _settings;
    private readonly ILogger<StripeService> _logger;

    public StripeService(
        ApplicationDbContext context,
        IOptions<StripeSettings> settings,
        ILogger<StripeService> logger)
    {
        _context = context;
        _settings = settings.Value;
        _logger = logger;

        StripeConfiguration.ApiKey = _settings.SecretKey;
    }

    public async Task<Customer> CreateCustomerAsync(string email, string name)
    {
        var options = new CustomerCreateOptions
        {
            Email = email,
            Name = name,
            Metadata = new Dictionary<string, string>
            {
                { "source", "CasperVPN" }
            }
        };

        var service = new CustomerService();
        return await service.CreateAsync(options);
    }

    public async Task<Customer> GetCustomerAsync(string customerId)
    {
        var service = new CustomerService();
        return await service.GetAsync(customerId);
    }

    public async Task<Customer> UpdateCustomerAsync(string customerId, string? email = null, string? name = null)
    {
        var options = new CustomerUpdateOptions();
        if (!string.IsNullOrEmpty(email)) options.Email = email;
        if (!string.IsNullOrEmpty(name)) options.Name = name;

        var service = new CustomerService();
        return await service.UpdateAsync(customerId, options);
    }

    public async Task<CheckoutSessionResponse> CreateCheckoutSessionAsync(Guid userId, CreateCheckoutSessionRequest request)
    {
        var user = await _context.Users.FindAsync(userId);
        if (user == null)
        {
            throw new InvalidOperationException("User not found");
        }

        var plan = await _context.Plans.FindAsync(request.PlanId);
        if (plan == null || !plan.IsActive)
        {
            throw new InvalidOperationException("Plan not found or inactive");
        }

        // Ensure user has a Stripe customer ID
        if (string.IsNullOrEmpty(user.StripeCustomerId))
        {
            var customer = await CreateCustomerAsync(user.Email, $"{user.FirstName} {user.LastName}".Trim());
            user.StripeCustomerId = customer.Id;
            await _context.SaveChangesAsync();
        }

        // Determine price ID based on billing interval
        var priceId = request.BillingInterval == BillingInterval.Yearly
            ? plan.StripePriceIdYearly
            : plan.StripePriceIdMonthly;

        if (string.IsNullOrEmpty(priceId))
        {
            throw new InvalidOperationException("Plan does not have a configured Stripe price");
        }

        var successUrl = request.SuccessUrl ?? $"{_settings.BaseUrl}/payment/success?session_id={{CHECKOUT_SESSION_ID}}";
        var cancelUrl = request.CancelUrl ?? $"{_settings.BaseUrl}/payment/cancel";

        var options = new SessionCreateOptions
        {
            Customer = user.StripeCustomerId,
            Mode = "subscription",
            PaymentMethodTypes = new List<string> { "card" },
            LineItems = new List<SessionLineItemOptions>
            {
                new SessionLineItemOptions
                {
                    Price = priceId,
                    Quantity = 1
                }
            },
            SuccessUrl = successUrl,
            CancelUrl = cancelUrl,
            Metadata = new Dictionary<string, string>
            {
                { "userId", userId.ToString() },
                { "planId", plan.Id.ToString() },
                { "billingInterval", request.BillingInterval.ToString() }
            },
            SubscriptionData = new SessionSubscriptionDataOptions
            {
                Metadata = new Dictionary<string, string>
                {
                    { "userId", userId.ToString() },
                    { "planId", plan.Id.ToString() }
                }
            },
            AllowPromotionCodes = true
        };

        // Apply coupon if provided
        if (!string.IsNullOrEmpty(request.CouponCode))
        {
            options.Discounts = new List<SessionDiscountOptions>
            {
                new SessionDiscountOptions { Coupon = request.CouponCode }
            };
            options.AllowPromotionCodes = false;
        }

        var service = new SessionService();
        var session = await service.CreateAsync(options);

        _logger.LogInformation("Checkout session created for user {UserId}, session {SessionId}", userId, session.Id);

        return new CheckoutSessionResponse
        {
            SessionId = session.Id,
            SessionUrl = session.Url,
            PublishableKey = _settings.PublishableKey
        };
    }

    public async Task<Session> GetCheckoutSessionAsync(string sessionId)
    {
        var service = new SessionService();
        return await service.GetAsync(sessionId);
    }

    public async Task<Stripe.Subscription> GetSubscriptionAsync(string subscriptionId)
    {
        var service = new SubscriptionService();
        return await service.GetAsync(subscriptionId);
    }

    public async Task<Stripe.Subscription> CancelSubscriptionAsync(string subscriptionId, bool immediately = false)
    {
        var service = new SubscriptionService();

        if (immediately)
        {
            return await service.CancelAsync(subscriptionId);
        }
        else
        {
            var options = new SubscriptionUpdateOptions
            {
                CancelAtPeriodEnd = true
            };
            return await service.UpdateAsync(subscriptionId, options);
        }
    }

    public async Task CancelAllSubscriptionsAsync(string customerId)
    {
        var service = new SubscriptionService();
        var options = new SubscriptionListOptions { Customer = customerId };
        var subscriptions = await service.ListAsync(options);

        foreach (var subscription in subscriptions.Data)
        {
            if (subscription.Status != "canceled")
            {
                await service.CancelAsync(subscription.Id);
            }
        }
    }

    public async Task<Stripe.Subscription> UpdateSubscriptionAsync(string subscriptionId, string newPriceId)
    {
        var service = new SubscriptionService();
        var subscription = await service.GetAsync(subscriptionId);

        var options = new SubscriptionUpdateOptions
        {
            Items = new List<SubscriptionItemOptions>
            {
                new SubscriptionItemOptions
                {
                    Id = subscription.Items.Data[0].Id,
                    Price = newPriceId
                }
            },
            ProrationBehavior = "create_prorations"
        };

        return await service.UpdateAsync(subscriptionId, options);
    }

    public async Task<BillingPortalResponse> CreateBillingPortalSessionAsync(Guid userId, string? returnUrl = null)
    {
        var user = await _context.Users.FindAsync(userId);
        if (user == null || string.IsNullOrEmpty(user.StripeCustomerId))
        {
            throw new InvalidOperationException("User not found or has no billing account");
        }

        var options = new Stripe.BillingPortal.SessionCreateOptions
        {
            Customer = user.StripeCustomerId,
            ReturnUrl = returnUrl ?? $"{_settings.BaseUrl}/account"
        };

        var service = new Stripe.BillingPortal.SessionService();
        var session = await service.CreateAsync(options);

        return new BillingPortalResponse { Url = session.Url };
    }

    public async Task<Product> CreateProductAsync(string name, string description)
    {
        var options = new ProductCreateOptions
        {
            Name = name,
            Description = description,
            Metadata = new Dictionary<string, string>
            {
                { "source", "CasperVPN" }
            }
        };

        var service = new ProductService();
        return await service.CreateAsync(options);
    }

    public async Task<Price> CreatePriceAsync(string productId, decimal amount, string interval)
    {
        var options = new PriceCreateOptions
        {
            Product = productId,
            UnitAmount = (long)(amount * 100), // Convert to cents
            Currency = "usd",
            Recurring = new PriceRecurringOptions
            {
                Interval = interval // "month" or "year"
            }
        };

        var service = new PriceService();
        return await service.CreateAsync(options);
    }

    public async Task<PaymentHistoryResponse> GetPaymentHistoryAsync(Guid userId, int limit = 20)
    {
        var payments = await _context.Payments
            .Where(p => p.UserId == userId)
            .OrderByDescending(p => p.CreatedAt)
            .Take(limit)
            .ToListAsync();

        var totalSpent = await _context.Payments
            .Where(p => p.UserId == userId && p.Status == PaymentStatus.Succeeded)
            .SumAsync(p => p.Amount);

        return new PaymentHistoryResponse
        {
            Payments = payments.Select(p => new PaymentDto
            {
                Id = p.Id,
                Amount = p.Amount,
                Currency = p.Currency,
                Status = p.Status,
                Method = p.Method,
                Description = p.Description,
                PaidAt = p.PaidAt,
                ReceiptUrl = p.ReceiptUrl,
                CreatedAt = p.CreatedAt
            }).ToList(),
            TotalCount = await _context.Payments.CountAsync(p => p.UserId == userId),
            TotalSpent = totalSpent
        };
    }

    public async Task<InvoiceListResponse> GetInvoicesAsync(Guid userId, int limit = 20)
    {
        var invoices = await _context.Invoices
            .Include(i => i.Subscription)
            .ThenInclude(s => s!.Plan)
            .Where(i => i.UserId == userId)
            .OrderByDescending(i => i.CreatedAt)
            .Take(limit)
            .ToListAsync();

        var totalAmount = await _context.Invoices
            .Where(i => i.UserId == userId && i.Status == InvoiceStatus.Paid)
            .SumAsync(i => i.Total);

        return new InvoiceListResponse
        {
            Invoices = invoices.Select(i => new InvoiceDto
            {
                Id = i.Id,
                InvoiceNumber = i.InvoiceNumber,
                Amount = i.Amount,
                Tax = i.Tax,
                Total = i.Total,
                Currency = i.Currency,
                Status = i.Status,
                DueDate = i.DueDate,
                PaidAt = i.PaidAt,
                InvoicePdfUrl = i.InvoicePdfUrl,
                HostedInvoiceUrl = i.HostedInvoiceUrl,
                CreatedAt = i.CreatedAt,
                PlanName = i.Subscription?.Plan?.Name
            }).ToList(),
            TotalCount = await _context.Invoices.CountAsync(i => i.UserId == userId),
            TotalAmount = totalAmount
        };
    }

    public async Task HandleWebhookEventAsync(string json, string signature)
    {
        Event stripeEvent;

        try
        {
            stripeEvent = EventUtility.ConstructEvent(json, signature, _settings.WebhookSecret);
        }
        catch (StripeException ex)
        {
            _logger.LogError(ex, "Webhook signature verification failed");
            throw new InvalidOperationException("Invalid webhook signature");
        }

        _logger.LogInformation("Processing Stripe webhook: {EventType}", stripeEvent.Type);

        switch (stripeEvent.Type)
        {
            case "checkout.session.completed":
                await HandleCheckoutSessionCompleted(stripeEvent);
                break;

            case "customer.subscription.created":
                await HandleSubscriptionCreated(stripeEvent);
                break;

            case "customer.subscription.updated":
                await HandleSubscriptionUpdated(stripeEvent);
                break;

            case "customer.subscription.deleted":
                await HandleSubscriptionDeleted(stripeEvent);
                break;

            case "invoice.paid":
                await HandleInvoicePaid(stripeEvent);
                break;

            case "invoice.payment_failed":
                await HandleInvoicePaymentFailed(stripeEvent);
                break;

            default:
                _logger.LogInformation("Unhandled webhook event type: {EventType}", stripeEvent.Type);
                break;
        }
    }

    private async Task HandleCheckoutSessionCompleted(Event stripeEvent)
    {
        var session = stripeEvent.Data.Object as Session;
        if (session == null) return;

        var userIdStr = session.Metadata.GetValueOrDefault("userId");
        var planIdStr = session.Metadata.GetValueOrDefault("planId");

        if (!Guid.TryParse(userIdStr, out var userId) || !Guid.TryParse(planIdStr, out var planId))
        {
            _logger.LogWarning("Invalid metadata in checkout session: {SessionId}", session.Id);
            return;
        }

        var user = await _context.Users
            .Include(u => u.Subscription)
            .FirstOrDefaultAsync(u => u.Id == userId);

        if (user == null) return;

        var plan = await _context.Plans.FindAsync(planId);
        if (plan == null) return;

        // Update or create subscription
        var subscription = user.Subscription ?? new Models.Subscription { UserId = userId };
        subscription.PlanId = planId;
        subscription.Status = SubscriptionStatus.Active;
        subscription.StripeSubscriptionId = session.SubscriptionId;
        subscription.StartDate = DateTime.UtcNow;
        subscription.CurrentPeriodStart = DateTime.UtcNow;
        subscription.UpdatedAt = DateTime.UtcNow;

        if (user.Subscription == null)
        {
            _context.Subscriptions.Add(subscription);
        }

        // Update user data limits
        user.DataLimitBytes = plan.DataLimitBytes;
        user.DataUsedBytes = 0; // Reset usage on new subscription
        user.Role = plan.Type == PlanType.Premium ? UserRole.Premium : UserRole.User;

        await _context.SaveChangesAsync();

        _logger.LogInformation("Checkout completed for user {UserId}, plan {PlanId}", userId, planId);
    }

    private async Task HandleSubscriptionCreated(Event stripeEvent)
    {
        var stripeSubscription = stripeEvent.Data.Object as Stripe.Subscription;
        if (stripeSubscription == null) return;

        var userIdStr = stripeSubscription.Metadata.GetValueOrDefault("userId");
        if (!Guid.TryParse(userIdStr, out var userId)) return;

        var subscription = await _context.Subscriptions.FirstOrDefaultAsync(s => s.UserId == userId);
        if (subscription == null) return;

        subscription.StripeSubscriptionId = stripeSubscription.Id;
        subscription.StripePriceId = stripeSubscription.Items.Data[0].Price.Id;
        subscription.CurrentPeriodStart = stripeSubscription.CurrentPeriodStart;
        subscription.CurrentPeriodEnd = stripeSubscription.CurrentPeriodEnd;
        subscription.Status = SubscriptionStatus.Active;
        subscription.UpdatedAt = DateTime.UtcNow;

        await _context.SaveChangesAsync();

        _logger.LogInformation("Subscription created for user {UserId}: {SubscriptionId}", userId, stripeSubscription.Id);
    }

    private async Task HandleSubscriptionUpdated(Event stripeEvent)
    {
        var stripeSubscription = stripeEvent.Data.Object as Stripe.Subscription;
        if (stripeSubscription == null) return;

        var subscription = await _context.Subscriptions
            .Include(s => s.User)
            .FirstOrDefaultAsync(s => s.StripeSubscriptionId == stripeSubscription.Id);

        if (subscription == null) return;

        subscription.CurrentPeriodStart = stripeSubscription.CurrentPeriodStart;
        subscription.CurrentPeriodEnd = stripeSubscription.CurrentPeriodEnd;
        subscription.CancelAtPeriodEnd = stripeSubscription.CancelAtPeriodEnd;
        subscription.StripePriceId = stripeSubscription.Items.Data[0].Price.Id;
        subscription.UpdatedAt = DateTime.UtcNow;

        // Update status based on Stripe status
        subscription.Status = stripeSubscription.Status switch
        {
            "active" => SubscriptionStatus.Active,
            "past_due" => SubscriptionStatus.PastDue,
            "canceled" => SubscriptionStatus.Cancelled,
            "trialing" => SubscriptionStatus.Trialing,
            "paused" => SubscriptionStatus.Paused,
            _ => subscription.Status
        };

        await _context.SaveChangesAsync();

        _logger.LogInformation("Subscription updated: {SubscriptionId}, status: {Status}", 
            stripeSubscription.Id, subscription.Status);
    }

    private async Task HandleSubscriptionDeleted(Event stripeEvent)
    {
        var stripeSubscription = stripeEvent.Data.Object as Stripe.Subscription;
        if (stripeSubscription == null) return;

        var subscription = await _context.Subscriptions
            .Include(s => s.User)
            .FirstOrDefaultAsync(s => s.StripeSubscriptionId == stripeSubscription.Id);

        if (subscription == null) return;

        subscription.Status = SubscriptionStatus.Cancelled;
        subscription.CancelledAt = DateTime.UtcNow;
        subscription.EndDate = DateTime.UtcNow;
        subscription.UpdatedAt = DateTime.UtcNow;

        // Downgrade to free plan
        var freePlan = await _context.Plans.FirstOrDefaultAsync(p => p.Type == PlanType.Free && p.IsActive);
        if (freePlan != null)
        {
            subscription.PlanId = freePlan.Id;
            subscription.User.DataLimitBytes = freePlan.DataLimitBytes;
            subscription.User.Role = UserRole.User;
        }

        await _context.SaveChangesAsync();

        _logger.LogInformation("Subscription cancelled: {SubscriptionId}", stripeSubscription.Id);
    }

    private async Task HandleInvoicePaid(Event stripeEvent)
    {
        var stripeInvoice = stripeEvent.Data.Object as Stripe.Invoice;
        if (stripeInvoice == null) return;

        // Find user by customer ID
        var user = await _context.Users
            .Include(u => u.Subscription)
            .FirstOrDefaultAsync(u => u.StripeCustomerId == stripeInvoice.CustomerId);

        if (user == null) return;

        // Create payment record
        var payment = new Payment
        {
            UserId = user.Id,
            SubscriptionId = user.Subscription?.Id,
            Amount = stripeInvoice.AmountPaid / 100m, // Convert from cents
            Currency = stripeInvoice.Currency.ToUpper(),
            Status = PaymentStatus.Succeeded,
            Method = PaymentMethod.Card,
            StripeInvoiceId = stripeInvoice.Id,
            StripeChargeId = stripeInvoice.ChargeId,
            Description = stripeInvoice.Description ?? "Subscription payment",
            PaidAt = DateTime.UtcNow,
            ReceiptUrl = stripeInvoice.HostedInvoiceUrl
        };

        _context.Payments.Add(payment);

        // Create invoice record
        var invoice = new Invoice
        {
            UserId = user.Id,
            SubscriptionId = user.Subscription?.Id,
            PaymentId = payment.Id,
            InvoiceNumber = stripeInvoice.Number ?? $"INV-{DateTime.UtcNow:yyyyMMddHHmmss}",
            Amount = stripeInvoice.Subtotal / 100m,
            Tax = (stripeInvoice.Tax ?? 0) / 100m,
            Total = stripeInvoice.Total / 100m,
            Currency = stripeInvoice.Currency.ToUpper(),
            Status = InvoiceStatus.Paid,
            DueDate = stripeInvoice.DueDate ?? DateTime.UtcNow,
            PaidAt = DateTime.UtcNow,
            StripeInvoiceId = stripeInvoice.Id,
            InvoicePdfUrl = stripeInvoice.InvoicePdf,
            HostedInvoiceUrl = stripeInvoice.HostedInvoiceUrl
        };

        _context.Invoices.Add(invoice);
        await _context.SaveChangesAsync();

        _logger.LogInformation("Invoice paid for user {UserId}: {InvoiceId}", user.Id, stripeInvoice.Id);
    }

    private async Task HandleInvoicePaymentFailed(Event stripeEvent)
    {
        var stripeInvoice = stripeEvent.Data.Object as Stripe.Invoice;
        if (stripeInvoice == null) return;

        var user = await _context.Users
            .Include(u => u.Subscription)
            .FirstOrDefaultAsync(u => u.StripeCustomerId == stripeInvoice.CustomerId);

        if (user == null) return;

        // Create failed payment record
        var payment = new Payment
        {
            UserId = user.Id,
            SubscriptionId = user.Subscription?.Id,
            Amount = stripeInvoice.AmountDue / 100m,
            Currency = stripeInvoice.Currency.ToUpper(),
            Status = PaymentStatus.Failed,
            Method = PaymentMethod.Card,
            StripeInvoiceId = stripeInvoice.Id,
            Description = stripeInvoice.Description ?? "Subscription payment",
            FailureReason = "Payment failed - please update payment method"
        };

        _context.Payments.Add(payment);

        // Update subscription status
        if (user.Subscription != null)
        {
            user.Subscription.Status = SubscriptionStatus.PastDue;
            user.Subscription.UpdatedAt = DateTime.UtcNow;
        }

        await _context.SaveChangesAsync();

        _logger.LogWarning("Invoice payment failed for user {UserId}: {InvoiceId}", user.Id, stripeInvoice.Id);
    }
}
