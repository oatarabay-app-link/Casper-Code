using CasperVPN.Models;

namespace CasperVPN.DTOs;

/// <summary>
/// Analytics data DTO
/// </summary>
public class AnalyticsDto
{
    public DashboardStats Dashboard { get; set; } = new();
    public List<ChartDataPoint> UserGrowth { get; set; } = new();
    public List<ChartDataPoint> ConnectionsOverTime { get; set; } = new();
    public List<ServerLoadData> ServerLoads { get; set; } = new();
    public List<CountryStats> TopCountries { get; set; } = new();
    public List<DeviceStats> DeviceBreakdown { get; set; } = new();
}

/// <summary>
/// Dashboard statistics
/// </summary>
public class DashboardStats
{
    public int TotalUsers { get; set; }
    public int ActiveUsers { get; set; }
    public int NewUsersToday { get; set; }
    public int NewUsersThisWeek { get; set; }
    public int NewUsersThisMonth { get; set; }
    public int TotalServers { get; set; }
    public int OnlineServers { get; set; }
    public int ActiveConnections { get; set; }
    public int TotalSubscriptions { get; set; }
    public int ActiveSubscriptions { get; set; }
    public long TotalBandwidthBytes { get; set; }
    public double AverageServerLoad { get; set; }
}

/// <summary>
/// Chart data point
/// </summary>
public class ChartDataPoint
{
    public string Label { get; set; } = string.Empty;
    public decimal Value { get; set; }
    public DateTime Date { get; set; }
}

/// <summary>
/// Server load data
/// </summary>
public class ServerLoadData
{
    public Guid ServerId { get; set; }
    public string ServerName { get; set; } = string.Empty;
    public string Country { get; set; } = string.Empty;
    public int Load { get; set; }
    public int CurrentConnections { get; set; }
    public int MaxConnections { get; set; }
    public ServerStatus Status { get; set; }
}

/// <summary>
/// Country statistics
/// </summary>
public class CountryStats
{
    public string Country { get; set; } = string.Empty;
    public string CountryCode { get; set; } = string.Empty;
    public int UserCount { get; set; }
    public int ConnectionCount { get; set; }
    public long BandwidthBytes { get; set; }
}

/// <summary>
/// Device statistics
/// </summary>
public class DeviceStats
{
    public string DeviceType { get; set; } = string.Empty;
    public int Count { get; set; }
    public double Percentage { get; set; }
}

/// <summary>
/// Revenue data DTO
/// </summary>
public class RevenueDto
{
    public RevenueStats Summary { get; set; } = new();
    public List<ChartDataPoint> RevenueOverTime { get; set; } = new();
    public List<PlanRevenueStats> RevenueByPlan { get; set; } = new();
    public List<ChartDataPoint> SubscriptionGrowth { get; set; } = new();
    public List<PaymentMethodStats> PaymentMethods { get; set; } = new();
    public ChurnStats Churn { get; set; } = new();
}

/// <summary>
/// Revenue summary statistics
/// </summary>
public class RevenueStats
{
    public decimal TotalRevenue { get; set; }
    public decimal RevenueToday { get; set; }
    public decimal RevenueThisWeek { get; set; }
    public decimal RevenueThisMonth { get; set; }
    public decimal RevenueThisYear { get; set; }
    public decimal MRR { get; set; } // Monthly Recurring Revenue
    public decimal ARR { get; set; } // Annual Recurring Revenue
    public decimal AverageRevenuePerUser { get; set; }
    public int TotalTransactions { get; set; }
    public int FailedTransactions { get; set; }
    public decimal RefundedAmount { get; set; }
}

/// <summary>
/// Plan revenue statistics
/// </summary>
public class PlanRevenueStats
{
    public Guid PlanId { get; set; }
    public string PlanName { get; set; } = string.Empty;
    public int SubscriberCount { get; set; }
    public decimal Revenue { get; set; }
    public double Percentage { get; set; }
}

/// <summary>
/// Payment method statistics
/// </summary>
public class PaymentMethodStats
{
    public PaymentMethod Method { get; set; }
    public int Count { get; set; }
    public decimal Amount { get; set; }
    public double Percentage { get; set; }
}

/// <summary>
/// Churn statistics
/// </summary>
public class ChurnStats
{
    public double ChurnRate { get; set; }
    public int ChurnedThisMonth { get; set; }
    public int ChurnedThisYear { get; set; }
    public double RetentionRate { get; set; }
    public decimal LostRevenue { get; set; }
}

/// <summary>
/// Admin activity log
/// </summary>
public class AdminActivityLog
{
    public Guid Id { get; set; }
    public Guid AdminUserId { get; set; }
    public string AdminEmail { get; set; } = string.Empty;
    public string Action { get; set; } = string.Empty;
    public string EntityType { get; set; } = string.Empty;
    public Guid? EntityId { get; set; }
    public string? Details { get; set; }
    public string? IpAddress { get; set; }
    public DateTime CreatedAt { get; set; }
}

/// <summary>
/// Date range filter
/// </summary>
public class DateRangeFilter
{
    public DateTime? StartDate { get; set; }
    public DateTime? EndDate { get; set; }
    public string? Period { get; set; } // "day", "week", "month", "year"
}
