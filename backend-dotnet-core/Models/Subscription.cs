using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace CasperVPN.Models;

/// <summary>
/// Subscription entity representing user subscriptions
/// </summary>
public class Subscription
{
    [Key]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    public Guid UserId { get; set; }

    [Required]
    public Guid PlanId { get; set; }

    public SubscriptionStatus Status { get; set; } = SubscriptionStatus.Active;

    public DateTime StartDate { get; set; } = DateTime.UtcNow;

    public DateTime? EndDate { get; set; }

    public DateTime? CancelledAt { get; set; }

    public DateTime? CurrentPeriodStart { get; set; }

    public DateTime? CurrentPeriodEnd { get; set; }

    [MaxLength(100)]
    public string? StripeSubscriptionId { get; set; }

    [MaxLength(100)]
    public string? StripePriceId { get; set; }

    public bool CancelAtPeriodEnd { get; set; } = false;

    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;

    // Navigation properties
    [ForeignKey("UserId")]
    public virtual User User { get; set; } = null!;

    [ForeignKey("PlanId")]
    public virtual Plan Plan { get; set; } = null!;
}

public enum SubscriptionStatus
{
    Active = 0,
    Inactive = 1,
    Cancelled = 2,
    PastDue = 3,
    Trialing = 4,
    Paused = 5,
    Expired = 6
}
