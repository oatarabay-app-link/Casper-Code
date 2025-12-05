using System.ComponentModel.DataAnnotations;
using CasperVPN.Models;

namespace CasperVPN.DTOs;

/// <summary>
/// Subscription plan DTO
/// </summary>
public class PlanDto
{
    public Guid Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public decimal PriceMonthly { get; set; }
    public decimal PriceYearly { get; set; }
    public int MaxDevices { get; set; }
    public long DataLimitBytes { get; set; }
    public int ServerAccessLevel { get; set; }
    public PlanType Type { get; set; }
    public List<string> Features { get; set; } = new();
    public bool IsPopular { get; set; }
    public decimal? SavingsPercentage { get; set; }
}

/// <summary>
/// Subscription DTO
/// </summary>
public class SubscriptionDto
{
    public Guid Id { get; set; }
    public Guid PlanId { get; set; }
    public string PlanName { get; set; } = string.Empty;
    public PlanType PlanType { get; set; }
    public SubscriptionStatus Status { get; set; }
    public DateTime StartDate { get; set; }
    public DateTime? EndDate { get; set; }
    public DateTime? CurrentPeriodStart { get; set; }
    public DateTime? CurrentPeriodEnd { get; set; }
    public bool CancelAtPeriodEnd { get; set; }
    public BillingInterval BillingInterval { get; set; }
    public decimal CurrentPrice { get; set; }
}

/// <summary>
/// Create subscription request
/// </summary>
public class CreateSubscriptionRequest
{
    [Required]
    public Guid PlanId { get; set; }

    public BillingInterval BillingInterval { get; set; } = BillingInterval.Monthly;

    [MaxLength(100)]
    public string? CouponCode { get; set; }
}

/// <summary>
/// Update subscription request
/// </summary>
public class UpdateSubscriptionRequest
{
    public Guid? NewPlanId { get; set; }

    public BillingInterval? BillingInterval { get; set; }

    public bool? CancelAtPeriodEnd { get; set; }
}

/// <summary>
/// Admin create plan request
/// </summary>
public class CreatePlanRequest
{
    [Required]
    [MaxLength(100)]
    public string Name { get; set; } = string.Empty;

    [MaxLength(500)]
    public string Description { get; set; } = string.Empty;

    [Required]
    [Range(0, 10000)]
    public decimal PriceMonthly { get; set; }

    [Required]
    [Range(0, 100000)]
    public decimal PriceYearly { get; set; }

    [Range(1, 100)]
    public int MaxDevices { get; set; } = 5;

    public long DataLimitBytes { get; set; } = 0;

    [Range(1, 3)]
    public int ServerAccessLevel { get; set; } = 1;

    public PlanType Type { get; set; } = PlanType.Basic;

    public List<string> Features { get; set; } = new();

    public bool IsActive { get; set; } = true;

    public int SortOrder { get; set; } = 0;
}

/// <summary>
/// Admin update plan request
/// </summary>
public class UpdatePlanRequest
{
    [MaxLength(100)]
    public string? Name { get; set; }

    [MaxLength(500)]
    public string? Description { get; set; }

    [Range(0, 10000)]
    public decimal? PriceMonthly { get; set; }

    [Range(0, 100000)]
    public decimal? PriceYearly { get; set; }

    [Range(1, 100)]
    public int? MaxDevices { get; set; }

    public long? DataLimitBytes { get; set; }

    [Range(1, 3)]
    public int? ServerAccessLevel { get; set; }

    public PlanType? Type { get; set; }

    public List<string>? Features { get; set; }

    public bool? IsActive { get; set; }

    public int? SortOrder { get; set; }
}
