using CasperVPN.DTOs;

namespace CasperVPN.Services;

/// <summary>
/// Subscription service interface
/// </summary>
public interface ISubscriptionService
{
    Task<List<PlanDto>> GetPlansAsync();
    Task<PlanDto?> GetPlanByIdAsync(Guid planId);
    Task<SubscriptionDto?> GetUserSubscriptionAsync(Guid userId);
    Task<SubscriptionDto> CreateSubscriptionAsync(Guid userId, CreateSubscriptionRequest request);
    Task<SubscriptionDto> UpdateSubscriptionAsync(Guid userId, UpdateSubscriptionRequest request);
    Task<bool> CancelSubscriptionAsync(Guid userId);
    
    // Admin operations
    Task<PlanDto> CreatePlanAsync(CreatePlanRequest request);
    Task<PlanDto> UpdatePlanAsync(Guid planId, UpdatePlanRequest request);
    Task<bool> DeletePlanAsync(Guid planId);
}
