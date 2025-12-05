using System.ComponentModel.DataAnnotations;

namespace CasperVPN.Models;

/// <summary>
/// Subscription plan entity
/// </summary>
public class Plan
{
    [Key]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    [MaxLength(100)]
    public string Name { get; set; } = string.Empty;

    [MaxLength(500)]
    public string Description { get; set; } = string.Empty;

    [Required]
    public decimal PriceMonthly { get; set; }

    [Required]
    public decimal PriceYearly { get; set; }

    [MaxLength(100)]
    public string? StripePriceIdMonthly { get; set; }

    [MaxLength(100)]
    public string? StripePriceIdYearly { get; set; }

    [MaxLength(100)]
    public string? StripeProductId { get; set; }

    public int MaxDevices { get; set; } = 5;

    public long DataLimitBytes { get; set; } = 0; // 0 = unlimited

    public int ServerAccessLevel { get; set; } = 1; // 1 = basic, 2 = premium, 3 = all

    public bool IsActive { get; set; } = true;

    public int SortOrder { get; set; } = 0;

    public PlanType Type { get; set; } = PlanType.Basic;

    public BillingInterval DefaultBillingInterval { get; set; } = BillingInterval.Monthly;

    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;

    // Features as JSON or comma-separated
    [MaxLength(2000)]
    public string Features { get; set; } = string.Empty;

    // Navigation properties
    public virtual ICollection<Subscription> Subscriptions { get; set; } = new List<Subscription>();
}

public enum PlanType
{
    Free = 0,
    Basic = 1,
    Premium = 2,
    Enterprise = 3
}

public enum BillingInterval
{
    Monthly = 0,
    Yearly = 1
}
