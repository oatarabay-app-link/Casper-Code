using System.ComponentModel.DataAnnotations;
using CasperVPN.Models;

namespace CasperVPN.DTOs;

/// <summary>
/// Payment DTO
/// </summary>
public class PaymentDto
{
    public Guid Id { get; set; }
    public decimal Amount { get; set; }
    public string Currency { get; set; } = string.Empty;
    public PaymentStatus Status { get; set; }
    public PaymentMethod Method { get; set; }
    public string? Description { get; set; }
    public DateTime? PaidAt { get; set; }
    public string? ReceiptUrl { get; set; }
    public DateTime CreatedAt { get; set; }
}

/// <summary>
/// Invoice DTO
/// </summary>
public class InvoiceDto
{
    public Guid Id { get; set; }
    public string InvoiceNumber { get; set; } = string.Empty;
    public decimal Amount { get; set; }
    public decimal Tax { get; set; }
    public decimal Total { get; set; }
    public string Currency { get; set; } = string.Empty;
    public InvoiceStatus Status { get; set; }
    public DateTime DueDate { get; set; }
    public DateTime? PaidAt { get; set; }
    public string? InvoicePdfUrl { get; set; }
    public string? HostedInvoiceUrl { get; set; }
    public DateTime CreatedAt { get; set; }
    public string? PlanName { get; set; }
}

/// <summary>
/// Create checkout session request
/// </summary>
public class CreateCheckoutSessionRequest
{
    [Required]
    public Guid PlanId { get; set; }

    public BillingInterval BillingInterval { get; set; } = BillingInterval.Monthly;

    [MaxLength(500)]
    public string? SuccessUrl { get; set; }

    [MaxLength(500)]
    public string? CancelUrl { get; set; }

    [MaxLength(100)]
    public string? CouponCode { get; set; }
}

/// <summary>
/// Checkout session response
/// </summary>
public class CheckoutSessionResponse
{
    public string SessionId { get; set; } = string.Empty;
    public string? SessionUrl { get; set; }
    public string PublishableKey { get; set; } = string.Empty;
}

/// <summary>
/// Billing portal session response
/// </summary>
public class BillingPortalResponse
{
    public string Url { get; set; } = string.Empty;
}

/// <summary>
/// Create billing portal request
/// </summary>
public class CreateBillingPortalRequest
{
    [MaxLength(500)]
    public string? ReturnUrl { get; set; }
}

/// <summary>
/// Payment history response
/// </summary>
public class PaymentHistoryResponse
{
    public List<PaymentDto> Payments { get; set; } = new();
    public int TotalCount { get; set; }
    public decimal TotalSpent { get; set; }
}

/// <summary>
/// Invoice list response
/// </summary>
public class InvoiceListResponse
{
    public List<InvoiceDto> Invoices { get; set; } = new();
    public int TotalCount { get; set; }
    public decimal TotalAmount { get; set; }
}

/// <summary>
/// Stripe webhook event (internal use)
/// </summary>
public class StripeWebhookEvent
{
    public string Type { get; set; } = string.Empty;
    public string Id { get; set; } = string.Empty;
    public object? Data { get; set; }
}
