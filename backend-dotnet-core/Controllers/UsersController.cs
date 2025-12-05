using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;
using CasperVPN.DTOs;
using CasperVPN.Services;

namespace CasperVPN.Controllers;

/// <summary>
/// User profile management controller
/// </summary>
[ApiController]
[Route("api/[controller]")]
[Authorize]
[Produces("application/json")]
public class UsersController : ControllerBase
{
    private readonly IUserService _userService;
    private readonly ILogger<UsersController> _logger;

    public UsersController(IUserService userService, ILogger<UsersController> logger)
    {
        _userService = userService;
        _logger = logger;
    }

    /// <summary>
    /// Get current user profile
    /// </summary>
    /// <returns>User profile data</returns>
    [HttpGet("me")]
    [SwaggerOperation(Summary = "Get current user", Description = "Gets the authenticated user's profile")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<UserDto>))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    [SwaggerResponse(404, "User not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<UserDto>>> GetMe()
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<UserDto>.ErrorResponse("Unauthorized"));

            var user = await _userService.GetUserByIdAsync(userId.Value);
            if (user == null)
                return NotFound(ApiResponse<UserDto>.ErrorResponse("User not found"));

            return Ok(ApiResponse<UserDto>.SuccessResponse(user));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting user profile");
            return BadRequest(ApiResponse<UserDto>.ErrorResponse("Failed to get user profile"));
        }
    }

    /// <summary>
    /// Update current user profile
    /// </summary>
    /// <param name="request">Profile update data</param>
    /// <returns>Updated user profile</returns>
    [HttpPut("me")]
    [SwaggerOperation(Summary = "Update profile", Description = "Updates the authenticated user's profile")]
    [SwaggerResponse(200, "Profile updated", typeof(ApiResponse<UserDto>))]
    [SwaggerResponse(400, "Invalid request", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<UserDto>>> UpdateMe([FromBody] UpdateProfileRequest request)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<UserDto>.ErrorResponse("Unauthorized"));

            var user = await _userService.UpdateUserProfileAsync(userId.Value, request);
            return Ok(ApiResponse<UserDto>.SuccessResponse(user, "Profile updated successfully"));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse<UserDto>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error updating user profile");
            return BadRequest(ApiResponse<UserDto>.ErrorResponse("Failed to update profile"));
        }
    }

    /// <summary>
    /// Delete current user account
    /// </summary>
    /// <returns>Confirmation message</returns>
    [HttpDelete("me")]
    [SwaggerOperation(Summary = "Delete account", Description = "Deletes the authenticated user's account")]
    [SwaggerResponse(200, "Account deleted", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> DeleteMe()
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse.ErrorResponse("Unauthorized"));

            await _userService.DeleteUserAsync(userId.Value);
            return Ok(ApiResponse.SuccessResponse("Account deleted successfully"));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error deleting user account");
            return BadRequest(ApiResponse.ErrorResponse("Failed to delete account"));
        }
    }

    private Guid? GetCurrentUserId()
    {
        var userIdClaim = User.FindFirst(System.Security.Claims.ClaimTypes.NameIdentifier)?.Value;
        return Guid.TryParse(userIdClaim, out var userId) ? userId : null;
    }
}
