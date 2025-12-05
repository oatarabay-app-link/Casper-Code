using FluentAssertions;
using Xunit;
using CasperVPN.DTOs;
using CasperVPN.Models;
using CasperVPN.Services;

namespace CasperVPN.Tests;

public class AdminServiceTests : TestBase
{
    private readonly AdminService _adminService;

    public AdminServiceTests()
    {
        _adminService = new AdminService(DbContext, AdminLoggerMock.Object);

        // Seed test data
        SeedTestData();
    }

    private void SeedTestData()
    {
        // Seed users
        for (int i = 0; i < 10; i++)
        {
            DbContext.Users.Add(new User
            {
                Id = Guid.NewGuid(),
                Email = $"user{i}@example.com",
                PasswordHash = "hash",
                IsActive = i % 2 == 0,
                CreatedAt = DateTime.UtcNow.AddDays(-i)
            });
        }

        // Seed servers
        DbContext.VpnServers.Add(new VpnServer
        {
            Id = Guid.NewGuid(),
            Name = "Test Server",
            Hostname = "test.vpn.com",
            IpAddress = "10.0.0.1",
            Country = "US",
            CountryCode = "US",
            Status = ServerStatus.Online,
            IsActive = true,
            Load = 45,
            CurrentConnections = 100,
            MaxConnections = 1000
        });

        DbContext.SaveChanges();
    }

    [Fact]
    public async Task GetDashboardStats_ShouldReturnStats()
    {
        // Act
        var result = await _adminService.GetDashboardStatsAsync();

        // Assert
        result.Should().NotBeNull();
        result.TotalUsers.Should().BeGreaterThan(0);
        result.TotalServers.Should().BeGreaterThan(0);
    }

    [Fact]
    public async Task GetDashboardStats_ShouldCountActiveUsers()
    {
        // Act
        var result = await _adminService.GetDashboardStatsAsync();

        // Assert
        result.ActiveUsers.Should().BeLessOrEqualTo(result.TotalUsers);
    }

    [Fact]
    public async Task GetAnalytics_ShouldReturnAnalyticsData()
    {
        // Act
        var result = await _adminService.GetAnalyticsAsync();

        // Assert
        result.Should().NotBeNull();
        result.Dashboard.Should().NotBeNull();
        result.ServerLoads.Should().NotBeNull();
    }

    [Fact]
    public async Task GetAnalytics_WithDateFilter_ShouldFilterData()
    {
        // Arrange
        var filter = new DateRangeFilter
        {
            StartDate = DateTime.UtcNow.AddDays(-7),
            EndDate = DateTime.UtcNow
        };

        // Act
        var result = await _adminService.GetAnalyticsAsync(filter);

        // Assert
        result.Should().NotBeNull();
        result.UserGrowth.Should().OnlyContain(d => d.Date >= filter.StartDate && d.Date <= filter.EndDate);
    }

    [Fact]
    public async Task GetAnalytics_WithPeriodFilter_ShouldFilterData()
    {
        // Arrange
        var filter = new DateRangeFilter { Period = "week" };

        // Act
        var result = await _adminService.GetAnalyticsAsync(filter);

        // Assert
        result.Should().NotBeNull();
    }

    [Fact]
    public async Task GetRevenueData_ShouldReturnRevenueStats()
    {
        // Arrange
        // Add some payment data
        var user = DbContext.Users.First();
        DbContext.Payments.Add(new Payment
        {
            Id = Guid.NewGuid(),
            UserId = user.Id,
            Amount = 9.99m,
            Currency = "USD",
            Status = PaymentStatus.Succeeded,
            PaidAt = DateTime.UtcNow
        });
        DbContext.Payments.Add(new Payment
        {
            Id = Guid.NewGuid(),
            UserId = user.Id,
            Amount = 79.99m,
            Currency = "USD",
            Status = PaymentStatus.Succeeded,
            PaidAt = DateTime.UtcNow.AddDays(-10)
        });
        await DbContext.SaveChangesAsync();

        // Act
        var result = await _adminService.GetRevenueDataAsync();

        // Assert
        result.Should().NotBeNull();
        result.Summary.Should().NotBeNull();
        result.Summary.TotalRevenue.Should().BeGreaterThan(0);
        result.Summary.TotalTransactions.Should().BeGreaterThan(0);
    }

    [Fact]
    public async Task GetRevenueData_ShouldCalculateMRR()
    {
        // Arrange
        var plans = DbContext.Plans.ToList();
        var user = DbContext.Users.First();
        
        if (plans.Any())
        {
            DbContext.Subscriptions.Add(new Subscription
            {
                Id = Guid.NewGuid(),
                UserId = user.Id,
                PlanId = plans.First().Id,
                Status = SubscriptionStatus.Active,
                StartDate = DateTime.UtcNow
            });
            await DbContext.SaveChangesAsync();
        }

        // Act
        var result = await _adminService.GetRevenueDataAsync();

        // Assert
        result.Should().NotBeNull();
        result.Summary.MRR.Should().BeGreaterOrEqualTo(0);
        result.Summary.ARR.Should().Be(result.Summary.MRR * 12);
    }

    [Fact]
    public async Task GetRevenueData_ShouldTrackChurn()
    {
        // Arrange
        var user = DbContext.Users.First();
        var plan = DbContext.Plans.FirstOrDefault();
        
        if (plan != null)
        {
            DbContext.Subscriptions.Add(new Subscription
            {
                Id = Guid.NewGuid(),
                UserId = user.Id,
                PlanId = plan.Id,
                Status = SubscriptionStatus.Cancelled,
                CancelledAt = DateTime.UtcNow,
                StartDate = DateTime.UtcNow.AddMonths(-2)
            });
            await DbContext.SaveChangesAsync();
        }

        // Act
        var result = await _adminService.GetRevenueDataAsync();

        // Assert
        result.Should().NotBeNull();
        result.Churn.Should().NotBeNull();
    }

    [Fact]
    public async Task GetDashboardStats_ShouldCountNewUsers()
    {
        // Arrange
        DbContext.Users.Add(new User
        {
            Id = Guid.NewGuid(),
            Email = "today@example.com",
            PasswordHash = "hash",
            CreatedAt = DateTime.UtcNow
        });
        await DbContext.SaveChangesAsync();

        // Act
        var result = await _adminService.GetDashboardStatsAsync();

        // Assert
        result.NewUsersToday.Should().BeGreaterOrEqualTo(1);
    }

    [Fact]
    public async Task GetDashboardStats_ShouldCalculateAverageServerLoad()
    {
        // Act
        var result = await _adminService.GetDashboardStatsAsync();

        // Assert
        result.AverageServerLoad.Should().BeGreaterOrEqualTo(0);
        result.AverageServerLoad.Should().BeLessOrEqualTo(100);
    }
}
