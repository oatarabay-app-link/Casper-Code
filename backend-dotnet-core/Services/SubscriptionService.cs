using Microsoft.EntityFrameworkCore;
using CasperVPN.Data;
using CasperVPN.DTOs;
using CasperVPN.Models;

namespace CasperVPN.Services;

/// <summary>
/// Subscription service implementation
/// </summary>
public class SubscriptionService : ISubscriptionService
{
    private readonly ApplicationDbContext _context;
    private readonly ILogger<SubscriptionService> _logger;
    private readonly IStripeService _stripeService;

    public SubscriptionService(
        ApplicationDbContext context,
        ILogger<SubscriptionService> logger,
        IStripeService stripeService)
    {
        _context = context;
        _logger = logger;
        _stripeService = stripeService;
    }

    public async Task<List<PlanDto>> GetPlansAsync()
    {
        var plans = await _context.Plans
            .Where(p => p.IsActive)
            .OrderBy(p => p.SortOrder)
            .ToListAsync();

        return plans.Select(MapToPlanDto).ToList();
    }

    public async Task<PlanDto?> GetPlanByIdAsync(Guid planId)
    {
        var plan = await _context.Plans.FindAsync(planId);
        if (plan == null) return null;

        return MapToPlanDto(plan);
    }

    public async Task<SubscriptionDto?> GetUserSubscriptionAsync(Guid userId)
    {
        var subscription = await _context.Subscriptions
            .Include(s => s.Plan)
            .FirstOrDefaultAsync(s => s.UserId == userId);

        if (subscription == null) return null;

        return MapToSubscriptionDto(subscription);
    }

    public async Task<SubscriptionDto> CreateSubscriptionAsync(Guid userId, CreateSubscriptionRequest request)
    {
        var user = await _context.Users
            .Include(u => u.Subscription)
            .FirstOrDefaultAsync(u => u.Id == userId);

        if (user == null)
        {
            throw new InvalidOperationException("User not found");
        }

        var plan = await _context.Plans.FindAsync(request.PlanId);
        if (plan == null || !plan.IsActive)
        {
            throw new InvalidOperationException("Plan not found or inactive");
        }

        // Check if user already has a subscription
        if (user.Subscription != null && user.Subscription.Status == SubscriptionStatus.Active)
        {
            throw new InvalidOperationException("User already has an active subscription");
        }

        // Create or update subscription
        var subscription = user.Subscription ?? new Subscription { UserId = userId };
        subscription.PlanId = request.PlanId;
        subscription.Status = plan.Type == PlanType.Free ? SubscriptionStatus.Active : SubscriptionStatus.Inactive;
        subscription.StartDate = DateTime.UtcNow;
        subscription.CurrentPeriodStart = DateTime.UtcNow;
        subscription.CurrentPeriodEnd = request.BillingInterval == BillingInterval.Yearly
            ? DateTime.UtcNow.AddYears(1)
            : DateTime.UtcNow.AddMonths(1);
        subscription.UpdatedAt = DateTime.UtcNow;

        if (user.Subscription == null)
        {
            _context.Subscriptions.Add(subscription);
        }

        // Update user data limits
        user.DataLimitBytes = plan.DataLimitBytes;
        user.UpdatedAt = DateTime.UtcNow;

        await _context.SaveChangesAsync();

        _logger.LogInformation("Subscription created for user {UserId}, plan {PlanId}", userId, request.PlanId);

        subscription.Plan = plan;
        return MapToSubscriptionDto(subscription);
    }

    public async Task<SubscriptionDto> UpdateSubscriptionAsync(Guid userId, UpdateSubscriptionRequest request)
    {
        var subscription = await _context.Subscriptions
            .Include(s => s.Plan)
            .Include(s => s.User)
            .FirstOrDefaultAsync(s => s.UserId == userId);

        if (subscription == null)
        {
            throw new InvalidOperationException("Subscription not found");
        }

        if (request.NewPlanId.HasValue)
        {
            var newPlan = await _context.Plans.FindAsync(request.NewPlanId.Value);
            if (newPlan == null || !newPlan.IsActive)
            {
                throw new InvalidOperationException("New plan not found or inactive");
            }

            subscription.PlanId = request.NewPlanId.Value;
            subscription.Plan = newPlan;
            subscription.User.DataLimitBytes = newPlan.DataLimitBytes;
        }

        if (request.CancelAtPeriodEnd.HasValue)
        {
            subscription.CancelAtPeriodEnd = request.CancelAtPeriodEnd.Value;
        }

        subscription.UpdatedAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        _logger.LogInformation("Subscription updated for user {UserId}", userId);

        return MapToSubscriptionDto(subscription);
    }

    public async Task<bool> CancelSubscriptionAsync(Guid userId)
    {
        var subscription = await _context.Subscriptions
            .Include(s => s.User)
            .FirstOrDefaultAsync(s => s.UserId == userId);

        if (subscription == null)
        {
            throw new InvalidOperationException("Subscription not found");
        }

        // Cancel in Stripe if applicable
        if (!string.IsNullOrEmpty(subscription.StripeSubscriptionId))
        {
            try
            {
                await _stripeService.CancelSubscriptionAsync(subscription.StripeSubscriptionId);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Failed to cancel Stripe subscription for user {UserId}", userId);
            }
        }

        subscription.Status = SubscriptionStatus.Cancelled;
        subscription.CancelledAt = DateTime.UtcNow;
        subscription.UpdatedAt = DateTime.UtcNow;

        // Downgrade to free plan
        var freePlan = await _context.Plans.FirstOrDefaultAsync(p => p.Type == PlanType.Free && p.IsActive);
        if (freePlan != null)
        {
            subscription.PlanId = freePlan.Id;
            subscription.User.DataLimitBytes = freePlan.DataLimitBytes;
        }

        await _context.SaveChangesAsync();

        _logger.LogInformation("Subscription cancelled for user {UserId}", userId);

        return true;
    }

    public async Task<PlanDto> CreatePlanAsync(CreatePlanRequest request)
    {
        var plan = new Plan
        {
            Name = request.Name,
            Description = request.Description,
            PriceMonthly = request.PriceMonthly,
            PriceYearly = request.PriceYearly,
            MaxDevices = request.MaxDevices,
            DataLimitBytes = request.DataLimitBytes,
            ServerAccessLevel = request.ServerAccessLevel,
            Type = request.Type,
            Features = string.Join(",", request.Features),
            IsActive = request.IsActive,
            SortOrder = request.SortOrder
        };

        // Create Stripe product and prices
        try
        {
            var stripeProduct = await _stripeService.CreateProductAsync(plan.Name, plan.Description);
            plan.StripeProductId = stripeProduct.Id;

            if (plan.PriceMonthly > 0)
            {
                var monthlyPrice = await _stripeService.CreatePriceAsync(stripeProduct.Id, plan.PriceMonthly, "month");
                plan.StripePriceIdMonthly = monthlyPrice.Id;
            }

            if (plan.PriceYearly > 0)
            {
                var yearlyPrice = await _stripeService.CreatePriceAsync(stripeProduct.Id, plan.PriceYearly, "year");
                plan.StripePriceIdYearly = yearlyPrice.Id;
            }
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to create Stripe product for plan {PlanName}", request.Name);
        }

        _context.Plans.Add(plan);
        await _context.SaveChangesAsync();

        _logger.LogInformation("Plan created: {PlanId}", plan.Id);

        return MapToPlanDto(plan);
    }

    public async Task<PlanDto> UpdatePlanAsync(Guid planId, UpdatePlanRequest request)
    {
        var plan = await _context.Plans.FindAsync(planId);
        if (plan == null)
        {
            throw new InvalidOperationException("Plan not found");
        }

        if (!string.IsNullOrWhiteSpace(request.Name))
            plan.Name = request.Name;

        if (!string.IsNullOrWhiteSpace(request.Description))
            plan.Description = request.Description;

        if (request.PriceMonthly.HasValue)
            plan.PriceMonthly = request.PriceMonthly.Value;

        if (request.PriceYearly.HasValue)
            plan.PriceYearly = request.PriceYearly.Value;

        if (request.MaxDevices.HasValue)
            plan.MaxDevices = request.MaxDevices.Value;

        if (request.DataLimitBytes.HasValue)
            plan.DataLimitBytes = request.DataLimitBytes.Value;

        if (request.ServerAccessLevel.HasValue)
            plan.ServerAccessLevel = request.ServerAccessLevel.Value;

        if (request.Type.HasValue)
            plan.Type = request.Type.Value;

        if (request.Features != null)
            plan.Features = string.Join(",", request.Features);

        if (request.IsActive.HasValue)
            plan.IsActive = request.IsActive.Value;

        if (request.SortOrder.HasValue)
            plan.SortOrder = request.SortOrder.Value;

        plan.UpdatedAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        _logger.LogInformation("Plan updated: {PlanId}", planId);

        return MapToPlanDto(plan);
    }

    public async Task<bool> DeletePlanAsync(Guid planId)
    {
        var plan = await _context.Plans.FindAsync(planId);
        if (plan == null)
        {
            throw new InvalidOperationException("Plan not found");
        }

        // Check if plan has active subscriptions
        var hasSubscriptions = await _context.Subscriptions
            .AnyAsync(s => s.PlanId == planId && s.Status == SubscriptionStatus.Active);

        if (hasSubscriptions)
        {
            throw new InvalidOperationException("Cannot delete plan with active subscriptions");
        }

        // Soft delete
        plan.IsActive = false;
        plan.UpdatedAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        _logger.LogInformation("Plan deleted: {PlanId}", planId);

        return true;
    }

    private static PlanDto MapToPlanDto(Plan plan)
    {
        var yearlyMonthlyPrice = plan.PriceYearly / 12;
        decimal? savingsPercentage = plan.PriceMonthly > 0 
            ? Math.Round((1 - yearlyMonthlyPrice / plan.PriceMonthly) * 100, 0)
            : null;

        return new PlanDto
        {
            Id = plan.Id,
            Name = plan.Name,
            Description = plan.Description,
            PriceMonthly = plan.PriceMonthly,
            PriceYearly = plan.PriceYearly,
            MaxDevices = plan.MaxDevices,
            DataLimitBytes = plan.DataLimitBytes,
            ServerAccessLevel = plan.ServerAccessLevel,
            Type = plan.Type,
            Features = plan.Features.Split(',', StringSplitOptions.RemoveEmptyEntries).ToList(),
            IsPopular = plan.Type == PlanType.Premium && plan.DefaultBillingInterval == BillingInterval.Yearly,
            SavingsPercentage = savingsPercentage
        };
    }

    private static SubscriptionDto MapToSubscriptionDto(Subscription sub)
    {
        var billingInterval = sub.StripePriceId == sub.Plan?.StripePriceIdYearly 
            ? BillingInterval.Yearly 
            : BillingInterval.Monthly;

        var currentPrice = billingInterval == BillingInterval.Yearly 
            ? sub.Plan?.PriceYearly ?? 0 
            : sub.Plan?.PriceMonthly ?? 0;

        return new SubscriptionDto
        {
            Id = sub.Id,
            PlanId = sub.PlanId,
            PlanName = sub.Plan?.Name ?? string.Empty,
            PlanType = sub.Plan?.Type ?? PlanType.Free,
            Status = sub.Status,
            StartDate = sub.StartDate,
            EndDate = sub.EndDate,
            CurrentPeriodStart = sub.CurrentPeriodStart,
            CurrentPeriodEnd = sub.CurrentPeriodEnd,
            CancelAtPeriodEnd = sub.CancelAtPeriodEnd,
            BillingInterval = billingInterval,
            CurrentPrice = currentPrice
        };
    }
}
