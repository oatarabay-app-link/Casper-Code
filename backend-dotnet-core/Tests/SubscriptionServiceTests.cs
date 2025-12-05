using FluentAssertions;
using Xunit;
using CasperVPN.DTOs;
using CasperVPN.Models;
using CasperVPN.Services;

namespace CasperVPN.Tests;

public class SubscriptionServiceTests : TestBase
{
    private readonly SubscriptionService _subscriptionService;

    public SubscriptionServiceTests()
    {
        _subscriptionService = new SubscriptionService(
            DbContext,
            SubscriptionLoggerMock.Object,
            StripeServiceMock.Object);
    }

    [Fact]
    public async Task GetPlans_ShouldReturnActivePlans()
    {
        // The database is seeded with default plans

        // Act
        var result = await _subscriptionService.GetPlansAsync();

        // Assert
        result.Should().NotBeNull();
        result.Should().NotBeEmpty();
        result.Should().OnlyContain(p => p.Name != null);
    }

    [Fact]
    public async Task GetPlanById_WithExistingPlan_ShouldReturnPlan()
    {
        // Arrange
        var plans = await _subscriptionService.GetPlansAsync();
        var planId = plans.First().Id;

        // Act
        var result = await _subscriptionService.GetPlanByIdAsync(planId);

        // Assert
        result.Should().NotBeNull();
        result!.Id.Should().Be(planId);
    }

    [Fact]
    public async Task GetPlanById_WithNonExistentPlan_ShouldReturnNull()
    {
        // Act
        var result = await _subscriptionService.GetPlanByIdAsync(Guid.NewGuid());

        // Assert
        result.Should().BeNull();
    }

    [Fact]
    public async Task CreateSubscription_ShouldCreateNewSubscription()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "sub@example.com",
            PasswordHash = "hash"
        };
        DbContext.Users.Add(user);
        await DbContext.SaveChangesAsync();

        var plans = await _subscriptionService.GetPlansAsync();
        var freePlan = plans.First(p => p.Type == PlanType.Free);

        var request = new CreateSubscriptionRequest
        {
            PlanId = freePlan.Id,
            BillingInterval = BillingInterval.Monthly
        };

        // Act
        var result = await _subscriptionService.CreateSubscriptionAsync(user.Id, request);

        // Assert
        result.Should().NotBeNull();
        result.PlanId.Should().Be(freePlan.Id);
        result.Status.Should().Be(SubscriptionStatus.Active);
    }

    [Fact]
    public async Task GetUserSubscription_WithExistingSubscription_ShouldReturnSubscription()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "getsub@example.com",
            PasswordHash = "hash"
        };
        DbContext.Users.Add(user);

        var plans = await _subscriptionService.GetPlansAsync();
        var plan = plans.First();

        var subscription = new Subscription
        {
            Id = Guid.NewGuid(),
            UserId = user.Id,
            PlanId = plan.Id,
            Status = SubscriptionStatus.Active,
            StartDate = DateTime.UtcNow
        };
        DbContext.Subscriptions.Add(subscription);
        await DbContext.SaveChangesAsync();

        // Act
        var result = await _subscriptionService.GetUserSubscriptionAsync(user.Id);

        // Assert
        result.Should().NotBeNull();
        result!.Id.Should().Be(subscription.Id);
    }

    [Fact]
    public async Task CancelSubscription_ShouldCancelAndDowngradeToFree()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "cancel@example.com",
            PasswordHash = "hash"
        };
        DbContext.Users.Add(user);

        var plans = await _subscriptionService.GetPlansAsync();
        var premiumPlan = plans.First(p => p.Type == PlanType.Premium);

        var subscription = new Subscription
        {
            Id = Guid.NewGuid(),
            UserId = user.Id,
            PlanId = premiumPlan.Id,
            Status = SubscriptionStatus.Active,
            StartDate = DateTime.UtcNow
        };
        DbContext.Subscriptions.Add(subscription);
        await DbContext.SaveChangesAsync();

        // Act
        var result = await _subscriptionService.CancelSubscriptionAsync(user.Id);

        // Assert
        result.Should().BeTrue();

        var updatedSub = await _subscriptionService.GetUserSubscriptionAsync(user.Id);
        updatedSub!.Status.Should().Be(SubscriptionStatus.Cancelled);
    }

    [Fact]
    public async Task CreatePlan_ShouldCreateNewPlan()
    {
        // Arrange
        var request = new CreatePlanRequest
        {
            Name = "Test Plan",
            Description = "A test plan",
            PriceMonthly = 4.99m,
            PriceYearly = 49.99m,
            MaxDevices = 3,
            ServerAccessLevel = 2,
            Type = PlanType.Basic,
            Features = new List<string> { "Feature 1", "Feature 2" }
        };

        // Act
        var result = await _subscriptionService.CreatePlanAsync(request);

        // Assert
        result.Should().NotBeNull();
        result.Name.Should().Be("Test Plan");
        result.PriceMonthly.Should().Be(4.99m);
    }

    [Fact]
    public async Task UpdatePlan_ShouldUpdateExistingPlan()
    {
        // Arrange
        var createRequest = new CreatePlanRequest
        {
            Name = "Original Name",
            PriceMonthly = 9.99m,
            PriceYearly = 99.99m
        };
        var plan = await _subscriptionService.CreatePlanAsync(createRequest);

        var updateRequest = new UpdatePlanRequest
        {
            Name = "Updated Name",
            PriceMonthly = 14.99m
        };

        // Act
        var result = await _subscriptionService.UpdatePlanAsync(plan.Id, updateRequest);

        // Assert
        result.Should().NotBeNull();
        result.Name.Should().Be("Updated Name");
        result.PriceMonthly.Should().Be(14.99m);
        result.PriceYearly.Should().Be(99.99m); // Unchanged
    }

    [Fact]
    public async Task DeletePlan_WithoutSubscriptions_ShouldDeactivatePlan()
    {
        // Arrange
        var createRequest = new CreatePlanRequest
        {
            Name = "To Delete",
            PriceMonthly = 9.99m,
            PriceYearly = 99.99m
        };
        var plan = await _subscriptionService.CreatePlanAsync(createRequest);

        // Act
        var result = await _subscriptionService.DeletePlanAsync(plan.Id);

        // Assert
        result.Should().BeTrue();

        var deletedPlan = await _subscriptionService.GetPlanByIdAsync(plan.Id);
        deletedPlan.Should().BeNull(); // GetPlans filters inactive
    }
}
