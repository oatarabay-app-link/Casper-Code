using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;
using CasperVPN.DTOs;
using CasperVPN.Services;

namespace CasperVPN.Controllers;

/// <summary>
/// User subscription management controller
/// </summary>
[ApiController]
[Route("api/[controller]")]
[Authorize]
[Produces("application/json")]
public class SubscriptionsController : ControllerBase
{
    private readonly ISubscriptionService _subscriptionService;
    private readonly ILogger<SubscriptionsController> _logger;

    public SubscriptionsController(ISubscriptionService subscriptionService, ILogger<SubscriptionsController> logger)
    {
        _subscriptionService = subscriptionService;
        _logger = logger;
    }

    /// <summary>
    /// Create a new subscription (internal use - checkout is preferred)
    /// </summary>
    /// <param name="request">Subscription request</param>
    /// <returns>Created subscription</returns>
    [HttpPost]
    [SwaggerOperation(Summary = "Create subscription", Description = "Creates a new subscription for the user")]
    [SwaggerResponse(200, "Subscription created", typeof(ApiResponse<SubscriptionDto>))]
    [SwaggerResponse(400, "Invalid request", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<SubscriptionDto>>> CreateSubscription([FromBody] CreateSubscriptionRequest request)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<SubscriptionDto>.ErrorResponse("Unauthorized"));

            var subscription = await _subscriptionService.CreateSubscriptionAsync(userId.Value, request);
            return Ok(ApiResponse<SubscriptionDto>.SuccessResponse(subscription, "Subscription created"));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse<SubscriptionDto>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error creating subscription");
            return BadRequest(ApiResponse<SubscriptionDto>.ErrorResponse("Failed to create subscription"));
        }
    }

    /// <summary>
    /// Get current user's subscription
    /// </summary>
    /// <returns>Subscription details</returns>
    [HttpGet("me")]
    [SwaggerOperation(Summary = "Get my subscription", Description = "Gets the current user's subscription")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<SubscriptionDto>))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    [SwaggerResponse(404, "No subscription found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<SubscriptionDto>>> GetMySubscription()
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<SubscriptionDto>.ErrorResponse("Unauthorized"));

            var subscription = await _subscriptionService.GetUserSubscriptionAsync(userId.Value);
            if (subscription == null)
                return NotFound(ApiResponse<SubscriptionDto>.ErrorResponse("No subscription found"));

            return Ok(ApiResponse<SubscriptionDto>.SuccessResponse(subscription));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting subscription");
            return BadRequest(ApiResponse<SubscriptionDto>.ErrorResponse("Failed to get subscription"));
        }
    }

    /// <summary>
    /// Update current user's subscription
    /// </summary>
    /// <param name="request">Update request</param>
    /// <returns>Updated subscription</returns>
    [HttpPut("me")]
    [SwaggerOperation(Summary = "Update subscription", Description = "Updates the current user's subscription")]
    [SwaggerResponse(200, "Subscription updated", typeof(ApiResponse<SubscriptionDto>))]
    [SwaggerResponse(400, "Invalid request", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<SubscriptionDto>>> UpdateMySubscription([FromBody] UpdateSubscriptionRequest request)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<SubscriptionDto>.ErrorResponse("Unauthorized"));

            var subscription = await _subscriptionService.UpdateSubscriptionAsync(userId.Value, request);
            return Ok(ApiResponse<SubscriptionDto>.SuccessResponse(subscription, "Subscription updated"));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse<SubscriptionDto>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error updating subscription");
            return BadRequest(ApiResponse<SubscriptionDto>.ErrorResponse("Failed to update subscription"));
        }
    }

    /// <summary>
    /// Cancel current user's subscription
    /// </summary>
    /// <returns>Confirmation message</returns>
    [HttpDelete("me")]
    [SwaggerOperation(Summary = "Cancel subscription", Description = "Cancels the current user's subscription")]
    [SwaggerResponse(200, "Subscription cancelled", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    [SwaggerResponse(404, "No subscription found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> CancelMySubscription()
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse.ErrorResponse("Unauthorized"));

            await _subscriptionService.CancelSubscriptionAsync(userId.Value);
            return Ok(ApiResponse.SuccessResponse("Subscription cancelled"));
        }
        catch (InvalidOperationException ex)
        {
            return NotFound(ApiResponse.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error cancelling subscription");
            return BadRequest(ApiResponse.ErrorResponse("Failed to cancel subscription"));
        }
    }

    private Guid? GetCurrentUserId()
    {
        var userIdClaim = User.FindFirst(System.Security.Claims.ClaimTypes.NameIdentifier)?.Value;
        return Guid.TryParse(userIdClaim, out var userId) ? userId : null;
    }
}
