using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace CasperVPN.Models;

/// <summary>
/// Payment entity for tracking payment transactions
/// </summary>
public class Payment
{
    [Key]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    public Guid UserId { get; set; }

    public Guid? SubscriptionId { get; set; }

    [Required]
    public decimal Amount { get; set; }

    [Required]
    [MaxLength(10)]
    public string Currency { get; set; } = "USD";

    public PaymentStatus Status { get; set; } = PaymentStatus.Pending;

    public PaymentMethod Method { get; set; } = PaymentMethod.Card;

    [MaxLength(100)]
    public string? StripePaymentIntentId { get; set; }

    [MaxLength(100)]
    public string? StripeInvoiceId { get; set; }

    [MaxLength(100)]
    public string? StripeChargeId { get; set; }

    [MaxLength(500)]
    public string? Description { get; set; }

    [MaxLength(500)]
    public string? FailureReason { get; set; }

    public DateTime? PaidAt { get; set; }

    public DateTime? RefundedAt { get; set; }

    public decimal? RefundedAmount { get; set; }

    [MaxLength(500)]
    public string? ReceiptUrl { get; set; }

    [MaxLength(500)]
    public string? InvoiceUrl { get; set; }

    [MaxLength(1000)]
    public string? Metadata { get; set; } // JSON metadata

    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;

    // Navigation properties
    [ForeignKey("UserId")]
    public virtual User User { get; set; } = null!;

    [ForeignKey("SubscriptionId")]
    public virtual Subscription? Subscription { get; set; }
}

public enum PaymentStatus
{
    Pending = 0,
    Succeeded = 1,
    Failed = 2,
    Cancelled = 3,
    Refunded = 4,
    PartiallyRefunded = 5
}

public enum PaymentMethod
{
    Card = 0,
    Paypal = 1,
    BankTransfer = 2,
    Crypto = 3
}

/// <summary>
/// Invoice entity for subscription billing
/// </summary>
public class Invoice
{
    [Key]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    public Guid UserId { get; set; }

    public Guid? SubscriptionId { get; set; }

    public Guid? PaymentId { get; set; }

    [Required]
    [MaxLength(50)]
    public string InvoiceNumber { get; set; } = string.Empty;

    [Required]
    public decimal Amount { get; set; }

    [Required]
    public decimal Tax { get; set; } = 0;

    [Required]
    public decimal Total { get; set; }

    [Required]
    [MaxLength(10)]
    public string Currency { get; set; } = "USD";

    public InvoiceStatus Status { get; set; } = InvoiceStatus.Draft;

    public DateTime DueDate { get; set; }

    public DateTime? PaidAt { get; set; }

    [MaxLength(100)]
    public string? StripeInvoiceId { get; set; }

    [MaxLength(500)]
    public string? InvoicePdfUrl { get; set; }

    [MaxLength(500)]
    public string? HostedInvoiceUrl { get; set; }

    [MaxLength(1000)]
    public string? LineItems { get; set; } // JSON line items

    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;

    // Navigation properties
    [ForeignKey("UserId")]
    public virtual User User { get; set; } = null!;

    [ForeignKey("SubscriptionId")]
    public virtual Subscription? Subscription { get; set; }

    [ForeignKey("PaymentId")]
    public virtual Payment? Payment { get; set; }
}

public enum InvoiceStatus
{
    Draft = 0,
    Open = 1,
    Paid = 2,
    Void = 3,
    Uncollectible = 4
}
