using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;
using CasperVPN.DTOs;
using CasperVPN.Models;
using CasperVPN.Services;

namespace CasperVPN.Controllers;

/// <summary>
/// Admin controller for management operations
/// </summary>
[ApiController]
[Route("api/[controller]")]
[Authorize(Roles = "Admin,SuperAdmin")]
[Produces("application/json")]
public class AdminController : ControllerBase
{
    private readonly IUserService _userService;
    private readonly IVpnServerService _serverService;
    private readonly ISubscriptionService _subscriptionService;
    private readonly IAdminService _adminService;
    private readonly ILogger<AdminController> _logger;

    public AdminController(
        IUserService userService,
        IVpnServerService serverService,
        ISubscriptionService subscriptionService,
        IAdminService adminService,
        ILogger<AdminController> logger)
    {
        _userService = userService;
        _serverService = serverService;
        _subscriptionService = subscriptionService;
        _adminService = adminService;
        _logger = logger;
    }

    #region User Management

    /// <summary>
    /// Get all users (paginated)
    /// </summary>
    /// <param name="pagination">Pagination parameters</param>
    /// <returns>Paginated list of users</returns>
    [HttpGet("users")]
    [SwaggerOperation(Summary = "List users", Description = "Gets all users with pagination")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<PaginatedResult<UserListItemDto>>))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    [SwaggerResponse(403, "Forbidden", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<PaginatedResult<UserListItemDto>>>> GetUsers([FromQuery] PaginationParams pagination)
    {
        try
        {
            var users = await _userService.GetUsersAsync(pagination);
            return Ok(ApiResponse<PaginatedResult<UserListItemDto>>.SuccessResponse(users));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting users");
            return BadRequest(ApiResponse<PaginatedResult<UserListItemDto>>.ErrorResponse("Failed to get users"));
        }
    }

    /// <summary>
    /// Get user details by ID
    /// </summary>
    /// <param name="id">User ID</param>
    /// <returns>User details</returns>
    [HttpGet("users/{id}")]
    [SwaggerOperation(Summary = "Get user details", Description = "Gets detailed information about a specific user")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<UserDetailsDto>))]
    [SwaggerResponse(404, "User not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<UserDetailsDto>>> GetUser(Guid id)
    {
        try
        {
            var user = await _userService.GetUserDetailsAsync(id);
            if (user == null)
                return NotFound(ApiResponse<UserDetailsDto>.ErrorResponse("User not found"));

            return Ok(ApiResponse<UserDetailsDto>.SuccessResponse(user));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting user {UserId}", id);
            return BadRequest(ApiResponse<UserDetailsDto>.ErrorResponse("Failed to get user"));
        }
    }

    /// <summary>
    /// Update user
    /// </summary>
    /// <param name="id">User ID</param>
    /// <param name="request">Update request</param>
    /// <returns>Updated user</returns>
    [HttpPut("users/{id}")]
    [SwaggerOperation(Summary = "Update user", Description = "Updates a user's information")]
    [SwaggerResponse(200, "User updated", typeof(ApiResponse<UserDto>))]
    [SwaggerResponse(400, "Invalid request", typeof(ApiResponse))]
    [SwaggerResponse(404, "User not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<UserDto>>> UpdateUser(Guid id, [FromBody] AdminUpdateUserRequest request)
    {
        try
        {
            var user = await _userService.AdminUpdateUserAsync(id, request);
            return Ok(ApiResponse<UserDto>.SuccessResponse(user, "User updated"));
        }
        catch (InvalidOperationException ex)
        {
            return NotFound(ApiResponse<UserDto>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error updating user {UserId}", id);
            return BadRequest(ApiResponse<UserDto>.ErrorResponse("Failed to update user"));
        }
    }

    /// <summary>
    /// Delete user
    /// </summary>
    /// <param name="id">User ID</param>
    /// <returns>Confirmation message</returns>
    [HttpDelete("users/{id}")]
    [SwaggerOperation(Summary = "Delete user", Description = "Permanently deletes a user")]
    [SwaggerResponse(200, "User deleted", typeof(ApiResponse))]
    [SwaggerResponse(404, "User not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> DeleteUser(Guid id)
    {
        try
        {
            await _userService.AdminDeleteUserAsync(id);
            return Ok(ApiResponse.SuccessResponse("User deleted"));
        }
        catch (InvalidOperationException ex)
        {
            return NotFound(ApiResponse.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error deleting user {UserId}", id);
            return BadRequest(ApiResponse.ErrorResponse("Failed to delete user"));
        }
    }

    #endregion

    #region Server Management

    /// <summary>
    /// Get all servers (admin view with full details)
    /// </summary>
    /// <returns>List of all servers</returns>
    [HttpGet("servers")]
    [SwaggerOperation(Summary = "List servers", Description = "Gets all VPN servers with full admin details")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<List<VpnServerDetailsDto>>))]
    public async Task<ActionResult<ApiResponse<List<VpnServerDetailsDto>>>> GetServers()
    {
        try
        {
            var servers = await _serverService.GetAllServersAsync();
            return Ok(ApiResponse<List<VpnServerDetailsDto>>.SuccessResponse(servers));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting servers");
            return BadRequest(ApiResponse<List<VpnServerDetailsDto>>.ErrorResponse("Failed to get servers"));
        }
    }

    /// <summary>
    /// Create a new server
    /// </summary>
    /// <param name="request">Server creation request</param>
    /// <returns>Created server</returns>
    [HttpPost("servers")]
    [SwaggerOperation(Summary = "Create server", Description = "Creates a new VPN server")]
    [SwaggerResponse(201, "Server created", typeof(ApiResponse<VpnServerDetailsDto>))]
    [SwaggerResponse(400, "Invalid request", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<VpnServerDetailsDto>>> CreateServer([FromBody] CreateServerRequest request)
    {
        try
        {
            var server = await _serverService.CreateServerAsync(request);
            return CreatedAtAction(nameof(GetServers), new { id = server.Id }, 
                ApiResponse<VpnServerDetailsDto>.SuccessResponse(server, "Server created"));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error creating server");
            return BadRequest(ApiResponse<VpnServerDetailsDto>.ErrorResponse("Failed to create server"));
        }
    }

    /// <summary>
    /// Update a server
    /// </summary>
    /// <param name="id">Server ID</param>
    /// <param name="request">Update request</param>
    /// <returns>Updated server</returns>
    [HttpPut("servers/{id}")]
    [SwaggerOperation(Summary = "Update server", Description = "Updates a VPN server")]
    [SwaggerResponse(200, "Server updated", typeof(ApiResponse<VpnServerDetailsDto>))]
    [SwaggerResponse(404, "Server not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<VpnServerDetailsDto>>> UpdateServer(Guid id, [FromBody] UpdateServerRequest request)
    {
        try
        {
            var server = await _serverService.UpdateServerAsync(id, request);
            return Ok(ApiResponse<VpnServerDetailsDto>.SuccessResponse(server, "Server updated"));
        }
        catch (InvalidOperationException ex)
        {
            return NotFound(ApiResponse<VpnServerDetailsDto>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error updating server {ServerId}", id);
            return BadRequest(ApiResponse<VpnServerDetailsDto>.ErrorResponse("Failed to update server"));
        }
    }

    /// <summary>
    /// Delete a server
    /// </summary>
    /// <param name="id">Server ID</param>
    /// <returns>Confirmation message</returns>
    [HttpDelete("servers/{id}")]
    [SwaggerOperation(Summary = "Delete server", Description = "Deletes a VPN server (soft delete)")]
    [SwaggerResponse(200, "Server deleted", typeof(ApiResponse))]
    [SwaggerResponse(404, "Server not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> DeleteServer(Guid id)
    {
        try
        {
            await _serverService.DeleteServerAsync(id);
            return Ok(ApiResponse.SuccessResponse("Server deleted"));
        }
        catch (InvalidOperationException ex)
        {
            return NotFound(ApiResponse.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error deleting server {ServerId}", id);
            return BadRequest(ApiResponse.ErrorResponse("Failed to delete server"));
        }
    }

    #endregion

    #region Plan Management

    /// <summary>
    /// Create a new subscription plan
    /// </summary>
    /// <param name="request">Plan creation request</param>
    /// <returns>Created plan</returns>
    [HttpPost("plans")]
    [SwaggerOperation(Summary = "Create plan", Description = "Creates a new subscription plan")]
    [SwaggerResponse(201, "Plan created", typeof(ApiResponse<PlanDto>))]
    [SwaggerResponse(400, "Invalid request", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<PlanDto>>> CreatePlan([FromBody] CreatePlanRequest request)
    {
        try
        {
            var plan = await _subscriptionService.CreatePlanAsync(request);
            return CreatedAtAction("GetPlan", "Plans", new { id = plan.Id }, 
                ApiResponse<PlanDto>.SuccessResponse(plan, "Plan created"));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error creating plan");
            return BadRequest(ApiResponse<PlanDto>.ErrorResponse("Failed to create plan"));
        }
    }

    /// <summary>
    /// Update a subscription plan
    /// </summary>
    /// <param name="id">Plan ID</param>
    /// <param name="request">Update request</param>
    /// <returns>Updated plan</returns>
    [HttpPut("plans/{id}")]
    [SwaggerOperation(Summary = "Update plan", Description = "Updates a subscription plan")]
    [SwaggerResponse(200, "Plan updated", typeof(ApiResponse<PlanDto>))]
    [SwaggerResponse(404, "Plan not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<PlanDto>>> UpdatePlan(Guid id, [FromBody] UpdatePlanRequest request)
    {
        try
        {
            var plan = await _subscriptionService.UpdatePlanAsync(id, request);
            return Ok(ApiResponse<PlanDto>.SuccessResponse(plan, "Plan updated"));
        }
        catch (InvalidOperationException ex)
        {
            return NotFound(ApiResponse<PlanDto>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error updating plan {PlanId}", id);
            return BadRequest(ApiResponse<PlanDto>.ErrorResponse("Failed to update plan"));
        }
    }

    /// <summary>
    /// Delete a subscription plan
    /// </summary>
    /// <param name="id">Plan ID</param>
    /// <returns>Confirmation message</returns>
    [HttpDelete("plans/{id}")]
    [SwaggerOperation(Summary = "Delete plan", Description = "Deletes a subscription plan (soft delete)")]
    [SwaggerResponse(200, "Plan deleted", typeof(ApiResponse))]
    [SwaggerResponse(400, "Plan has active subscriptions", typeof(ApiResponse))]
    [SwaggerResponse(404, "Plan not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> DeletePlan(Guid id)
    {
        try
        {
            await _subscriptionService.DeletePlanAsync(id);
            return Ok(ApiResponse.SuccessResponse("Plan deleted"));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error deleting plan {PlanId}", id);
            return BadRequest(ApiResponse.ErrorResponse("Failed to delete plan"));
        }
    }

    #endregion

    #region Analytics & Revenue

    /// <summary>
    /// Get analytics data
    /// </summary>
    /// <param name="filter">Date range filter</param>
    /// <returns>Analytics data</returns>
    [HttpGet("analytics")]
    [SwaggerOperation(Summary = "Get analytics", Description = "Gets platform analytics data")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<AnalyticsDto>))]
    public async Task<ActionResult<ApiResponse<AnalyticsDto>>> GetAnalytics([FromQuery] DateRangeFilter? filter = null)
    {
        try
        {
            var analytics = await _adminService.GetAnalyticsAsync(filter);
            return Ok(ApiResponse<AnalyticsDto>.SuccessResponse(analytics));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting analytics");
            return BadRequest(ApiResponse<AnalyticsDto>.ErrorResponse("Failed to get analytics"));
        }
    }

    /// <summary>
    /// Get revenue data
    /// </summary>
    /// <param name="filter">Date range filter</param>
    /// <returns>Revenue data</returns>
    [HttpGet("revenue")]
    [SwaggerOperation(Summary = "Get revenue", Description = "Gets revenue and financial data")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<RevenueDto>))]
    public async Task<ActionResult<ApiResponse<RevenueDto>>> GetRevenue([FromQuery] DateRangeFilter? filter = null)
    {
        try
        {
            var revenue = await _adminService.GetRevenueDataAsync(filter);
            return Ok(ApiResponse<RevenueDto>.SuccessResponse(revenue));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting revenue data");
            return BadRequest(ApiResponse<RevenueDto>.ErrorResponse("Failed to get revenue data"));
        }
    }

    /// <summary>
    /// Get dashboard statistics
    /// </summary>
    /// <returns>Dashboard stats</returns>
    [HttpGet("dashboard")]
    [SwaggerOperation(Summary = "Get dashboard", Description = "Gets dashboard summary statistics")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<DashboardStats>))]
    public async Task<ActionResult<ApiResponse<DashboardStats>>> GetDashboard()
    {
        try
        {
            var stats = await _adminService.GetDashboardStatsAsync();
            return Ok(ApiResponse<DashboardStats>.SuccessResponse(stats));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting dashboard stats");
            return BadRequest(ApiResponse<DashboardStats>.ErrorResponse("Failed to get dashboard stats"));
        }
    }

    #endregion
}
