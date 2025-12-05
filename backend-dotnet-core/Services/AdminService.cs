using Microsoft.EntityFrameworkCore;
using CasperVPN.Data;
using CasperVPN.DTOs;
using CasperVPN.Models;

namespace CasperVPN.Services;

/// <summary>
/// Admin service implementation
/// </summary>
public class AdminService : IAdminService
{
    private readonly ApplicationDbContext _context;
    private readonly ILogger<AdminService> _logger;

    public AdminService(ApplicationDbContext context, ILogger<AdminService> logger)
    {
        _context = context;
        _logger = logger;
    }

    public async Task<DashboardStats> GetDashboardStatsAsync()
    {
        var now = DateTime.UtcNow;
        var todayStart = now.Date;
        var weekStart = now.AddDays(-(int)now.DayOfWeek);
        var monthStart = new DateTime(now.Year, now.Month, 1);

        return new DashboardStats
        {
            TotalUsers = await _context.Users.CountAsync(),
            ActiveUsers = await _context.Users.CountAsync(u => u.IsActive),
            NewUsersToday = await _context.Users.CountAsync(u => u.CreatedAt >= todayStart),
            NewUsersThisWeek = await _context.Users.CountAsync(u => u.CreatedAt >= weekStart),
            NewUsersThisMonth = await _context.Users.CountAsync(u => u.CreatedAt >= monthStart),
            TotalServers = await _context.VpnServers.CountAsync(),
            OnlineServers = await _context.VpnServers.CountAsync(s => s.IsActive && s.Status == ServerStatus.Online),
            ActiveConnections = await _context.ConnectionLogs.CountAsync(c => c.Status == ConnectionStatus.Active),
            TotalSubscriptions = await _context.Subscriptions.CountAsync(),
            ActiveSubscriptions = await _context.Subscriptions.CountAsync(s => s.Status == SubscriptionStatus.Active),
            TotalBandwidthBytes = await _context.ConnectionLogs.SumAsync(c => c.BytesUploaded + c.BytesDownloaded),
            AverageServerLoad = await _context.VpnServers.Where(s => s.IsActive).AverageAsync(s => (double?)s.Load) ?? 0
        };
    }

    public async Task<AnalyticsDto> GetAnalyticsAsync(DateRangeFilter? filter = null)
    {
        var (startDate, endDate) = GetDateRange(filter);

        var dashboard = await GetDashboardStatsAsync();

        // User growth over time
        var userGrowth = await _context.Users
            .Where(u => u.CreatedAt >= startDate && u.CreatedAt <= endDate)
            .GroupBy(u => u.CreatedAt.Date)
            .Select(g => new ChartDataPoint
            {
                Label = g.Key.ToString("MMM dd"),
                Value = g.Count(),
                Date = g.Key
            })
            .OrderBy(x => x.Date)
            .ToListAsync();

        // Connections over time
        var connectionsOverTime = await _context.ConnectionLogs
            .Where(c => c.ConnectedAt >= startDate && c.ConnectedAt <= endDate)
            .GroupBy(c => c.ConnectedAt.Date)
            .Select(g => new ChartDataPoint
            {
                Label = g.Key.ToString("MMM dd"),
                Value = g.Count(),
                Date = g.Key
            })
            .OrderBy(x => x.Date)
            .ToListAsync();

        // Server loads
        var serverLoads = await _context.VpnServers
            .Where(s => s.IsActive)
            .Select(s => new ServerLoadData
            {
                ServerId = s.Id,
                ServerName = s.Name,
                Country = s.Country,
                Load = s.Load,
                CurrentConnections = s.CurrentConnections,
                MaxConnections = s.MaxConnections,
                Status = s.Status
            })
            .OrderByDescending(s => s.Load)
            .ToListAsync();

        // Top countries by connections
        var topCountries = await _context.ConnectionLogs
            .Where(c => c.ConnectedAt >= startDate && c.ConnectedAt <= endDate)
            .GroupBy(c => c.Server.Country)
            .Select(g => new CountryStats
            {
                Country = g.Key,
                CountryCode = g.First().Server.CountryCode,
                ConnectionCount = g.Count(),
                BandwidthBytes = g.Sum(c => c.BytesUploaded + c.BytesDownloaded)
            })
            .OrderByDescending(x => x.ConnectionCount)
            .Take(10)
            .ToListAsync();

        // Device breakdown
        var deviceStats = await _context.ConnectionLogs
            .Where(c => c.ConnectedAt >= startDate && c.ConnectedAt <= endDate && c.DeviceType != null)
            .GroupBy(c => c.DeviceType)
            .Select(g => new { DeviceType = g.Key, Count = g.Count() })
            .ToListAsync();

        var totalDevices = deviceStats.Sum(d => d.Count);
        var deviceBreakdown = deviceStats.Select(d => new DeviceStats
        {
            DeviceType = d.DeviceType ?? "Unknown",
            Count = d.Count,
            Percentage = totalDevices > 0 ? Math.Round((double)d.Count / totalDevices * 100, 1) : 0
        }).ToList();

        return new AnalyticsDto
        {
            Dashboard = dashboard,
            UserGrowth = userGrowth,
            ConnectionsOverTime = connectionsOverTime,
            ServerLoads = serverLoads,
            TopCountries = topCountries,
            DeviceBreakdown = deviceBreakdown
        };
    }

    public async Task<RevenueDto> GetRevenueDataAsync(DateRangeFilter? filter = null)
    {
        var (startDate, endDate) = GetDateRange(filter);
        var now = DateTime.UtcNow;
        var todayStart = now.Date;
        var weekStart = now.AddDays(-(int)now.DayOfWeek);
        var monthStart = new DateTime(now.Year, now.Month, 1);
        var yearStart = new DateTime(now.Year, 1, 1);

        // Revenue summary
        var summary = new RevenueStats
        {
            TotalRevenue = await _context.Payments
                .Where(p => p.Status == PaymentStatus.Succeeded)
                .SumAsync(p => p.Amount),
            RevenueToday = await _context.Payments
                .Where(p => p.Status == PaymentStatus.Succeeded && p.PaidAt >= todayStart)
                .SumAsync(p => p.Amount),
            RevenueThisWeek = await _context.Payments
                .Where(p => p.Status == PaymentStatus.Succeeded && p.PaidAt >= weekStart)
                .SumAsync(p => p.Amount),
            RevenueThisMonth = await _context.Payments
                .Where(p => p.Status == PaymentStatus.Succeeded && p.PaidAt >= monthStart)
                .SumAsync(p => p.Amount),
            RevenueThisYear = await _context.Payments
                .Where(p => p.Status == PaymentStatus.Succeeded && p.PaidAt >= yearStart)
                .SumAsync(p => p.Amount),
            TotalTransactions = await _context.Payments.CountAsync(),
            FailedTransactions = await _context.Payments.CountAsync(p => p.Status == PaymentStatus.Failed),
            RefundedAmount = await _context.Payments
                .Where(p => p.RefundedAmount.HasValue)
                .SumAsync(p => p.RefundedAmount ?? 0)
        };

        // Calculate MRR and ARR
        var activeSubscriptions = await _context.Subscriptions
            .Include(s => s.Plan)
            .Where(s => s.Status == SubscriptionStatus.Active)
            .ToListAsync();

        summary.MRR = activeSubscriptions.Sum(s => s.Plan?.PriceMonthly ?? 0);
        summary.ARR = summary.MRR * 12;

        var totalUsers = await _context.Users.CountAsync();
        summary.AverageRevenuePerUser = totalUsers > 0 ? summary.TotalRevenue / totalUsers : 0;

        // Revenue over time
        var revenueOverTime = await _context.Payments
            .Where(p => p.Status == PaymentStatus.Succeeded && p.PaidAt >= startDate && p.PaidAt <= endDate)
            .GroupBy(p => p.PaidAt!.Value.Date)
            .Select(g => new ChartDataPoint
            {
                Label = g.Key.ToString("MMM dd"),
                Value = g.Sum(p => p.Amount),
                Date = g.Key
            })
            .OrderBy(x => x.Date)
            .ToListAsync();

        // Revenue by plan
        var revenueByPlan = await _context.Payments
            .Include(p => p.Subscription)
            .ThenInclude(s => s!.Plan)
            .Where(p => p.Status == PaymentStatus.Succeeded && p.SubscriptionId.HasValue)
            .GroupBy(p => new { p.Subscription!.PlanId, p.Subscription.Plan!.Name })
            .Select(g => new { g.Key.PlanId, g.Key.Name, Revenue = g.Sum(p => p.Amount), Count = g.Count() })
            .ToListAsync();

        var totalRevenue = revenueByPlan.Sum(r => r.Revenue);
        var planRevenueStats = revenueByPlan.Select(r => new PlanRevenueStats
        {
            PlanId = r.PlanId,
            PlanName = r.Name,
            SubscriberCount = r.Count,
            Revenue = r.Revenue,
            Percentage = totalRevenue > 0 ? Math.Round((double)r.Revenue / (double)totalRevenue * 100, 1) : 0
        }).ToList();

        // Subscription growth
        var subscriptionGrowth = await _context.Subscriptions
            .Where(s => s.CreatedAt >= startDate && s.CreatedAt <= endDate)
            .GroupBy(s => s.CreatedAt.Date)
            .Select(g => new ChartDataPoint
            {
                Label = g.Key.ToString("MMM dd"),
                Value = g.Count(),
                Date = g.Key
            })
            .OrderBy(x => x.Date)
            .ToListAsync();

        // Payment methods
        var paymentMethods = await _context.Payments
            .Where(p => p.Status == PaymentStatus.Succeeded && p.PaidAt >= startDate && p.PaidAt <= endDate)
            .GroupBy(p => p.Method)
            .Select(g => new { Method = g.Key, Count = g.Count(), Amount = g.Sum(p => p.Amount) })
            .ToListAsync();

        var totalPayments = paymentMethods.Sum(p => p.Amount);
        var paymentMethodStats = paymentMethods.Select(p => new PaymentMethodStats
        {
            Method = p.Method,
            Count = p.Count,
            Amount = p.Amount,
            Percentage = totalPayments > 0 ? Math.Round((double)p.Amount / (double)totalPayments * 100, 1) : 0
        }).ToList();

        // Churn stats
        var cancelledThisMonth = await _context.Subscriptions
            .CountAsync(s => s.CancelledAt >= monthStart);
        var cancelledThisYear = await _context.Subscriptions
            .CountAsync(s => s.CancelledAt >= yearStart);
        var activeCount = await _context.Subscriptions
            .CountAsync(s => s.Status == SubscriptionStatus.Active);
        var totalSubs = await _context.Subscriptions.CountAsync();

        var churnStats = new ChurnStats
        {
            ChurnedThisMonth = cancelledThisMonth,
            ChurnedThisYear = cancelledThisYear,
            ChurnRate = totalSubs > 0 ? Math.Round((double)cancelledThisMonth / totalSubs * 100, 2) : 0,
            RetentionRate = totalSubs > 0 ? Math.Round((double)activeCount / totalSubs * 100, 2) : 0,
            LostRevenue = await _context.Subscriptions
                .Include(s => s.Plan)
                .Where(s => s.CancelledAt >= monthStart)
                .SumAsync(s => s.Plan!.PriceMonthly)
        };

        return new RevenueDto
        {
            Summary = summary,
            RevenueOverTime = revenueOverTime,
            RevenueByPlan = planRevenueStats,
            SubscriptionGrowth = subscriptionGrowth,
            PaymentMethods = paymentMethodStats,
            Churn = churnStats
        };
    }

    private static (DateTime startDate, DateTime endDate) GetDateRange(DateRangeFilter? filter)
    {
        var now = DateTime.UtcNow;

        if (filter?.StartDate.HasValue == true && filter?.EndDate.HasValue == true)
        {
            return (filter.StartDate.Value, filter.EndDate.Value);
        }

        return filter?.Period?.ToLower() switch
        {
            "day" => (now.AddDays(-1), now),
            "week" => (now.AddDays(-7), now),
            "month" => (now.AddMonths(-1), now),
            "year" => (now.AddYears(-1), now),
            _ => (now.AddDays(-30), now) // Default to last 30 days
        };
    }
}
