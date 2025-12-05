using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;
using CasperVPN.DTOs;
using CasperVPN.Services;

namespace CasperVPN.Controllers;

/// <summary>
/// Subscription plans controller
/// </summary>
[ApiController]
[Route("api/[controller]")]
[Produces("application/json")]
public class PlansController : ControllerBase
{
    private readonly ISubscriptionService _subscriptionService;
    private readonly ILogger<PlansController> _logger;

    public PlansController(ISubscriptionService subscriptionService, ILogger<PlansController> logger)
    {
        _subscriptionService = subscriptionService;
        _logger = logger;
    }

    /// <summary>
    /// Get all available subscription plans
    /// </summary>
    /// <returns>List of subscription plans</returns>
    [HttpGet]
    [SwaggerOperation(Summary = "List plans", Description = "Gets all available subscription plans")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<List<PlanDto>>))]
    public async Task<ActionResult<ApiResponse<List<PlanDto>>>> GetPlans()
    {
        try
        {
            var plans = await _subscriptionService.GetPlansAsync();
            return Ok(ApiResponse<List<PlanDto>>.SuccessResponse(plans));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting plans");
            return BadRequest(ApiResponse<List<PlanDto>>.ErrorResponse("Failed to get plans"));
        }
    }

    /// <summary>
    /// Get a specific plan by ID
    /// </summary>
    /// <param name="id">Plan ID</param>
    /// <returns>Plan details</returns>
    [HttpGet("{id}")]
    [SwaggerOperation(Summary = "Get plan", Description = "Gets a specific subscription plan by ID")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<PlanDto>))]
    [SwaggerResponse(404, "Plan not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<PlanDto>>> GetPlan(Guid id)
    {
        try
        {
            var plan = await _subscriptionService.GetPlanByIdAsync(id);
            if (plan == null)
                return NotFound(ApiResponse<PlanDto>.ErrorResponse("Plan not found"));

            return Ok(ApiResponse<PlanDto>.SuccessResponse(plan));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting plan {PlanId}", id);
            return BadRequest(ApiResponse<PlanDto>.ErrorResponse("Failed to get plan"));
        }
    }
}
