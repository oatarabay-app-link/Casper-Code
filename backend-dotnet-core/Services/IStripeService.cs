using CasperVPN.DTOs;
using Stripe;

namespace CasperVPN.Services;

/// <summary>
/// Stripe payment service interface
/// </summary>
public interface IStripeService
{
    // Customer operations
    Task<Customer> CreateCustomerAsync(string email, string name);
    Task<Customer> GetCustomerAsync(string customerId);
    Task<Customer> UpdateCustomerAsync(string customerId, string? email = null, string? name = null);

    // Checkout operations
    Task<CheckoutSessionResponse> CreateCheckoutSessionAsync(Guid userId, CreateCheckoutSessionRequest request);
    Task<Session> GetCheckoutSessionAsync(string sessionId);

    // Subscription operations
    Task<Subscription> GetSubscriptionAsync(string subscriptionId);
    Task<Subscription> CancelSubscriptionAsync(string subscriptionId, bool immediately = false);
    Task CancelAllSubscriptionsAsync(string customerId);
    Task<Subscription> UpdateSubscriptionAsync(string subscriptionId, string newPriceId);

    // Billing portal
    Task<BillingPortalResponse> CreateBillingPortalSessionAsync(Guid userId, string? returnUrl = null);

    // Product and price operations
    Task<Product> CreateProductAsync(string name, string description);
    Task<Price> CreatePriceAsync(string productId, decimal amount, string interval);

    // Payment history
    Task<PaymentHistoryResponse> GetPaymentHistoryAsync(Guid userId, int limit = 20);
    Task<InvoiceListResponse> GetInvoicesAsync(Guid userId, int limit = 20);

    // Webhook handling
    Task HandleWebhookEventAsync(string json, string signature);
}
