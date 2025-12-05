using CasperVPN.DTOs;

namespace CasperVPN.Services;

/// <summary>
/// Admin service interface
/// </summary>
public interface IAdminService
{
    Task<AnalyticsDto> GetAnalyticsAsync(DateRangeFilter? filter = null);
    Task<RevenueDto> GetRevenueDataAsync(DateRangeFilter? filter = null);
    Task<DashboardStats> GetDashboardStatsAsync();
}
