using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;
using CasperVPN.DTOs;
using CasperVPN.Services;

namespace CasperVPN.Controllers;

/// <summary>
/// VPN servers controller for client operations
/// </summary>
[ApiController]
[Route("api/[controller]")]
[Produces("application/json")]
public class ServersController : ControllerBase
{
    private readonly IVpnServerService _serverService;
    private readonly ILogger<ServersController> _logger;

    public ServersController(IVpnServerService serverService, ILogger<ServersController> logger)
    {
        _serverService = serverService;
        _logger = logger;
    }

    /// <summary>
    /// Get all available VPN servers
    /// </summary>
    /// <returns>List of VPN servers</returns>
    [HttpGet]
    [Authorize]
    [SwaggerOperation(Summary = "List servers", Description = "Gets all VPN servers available to the user based on their subscription")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<List<VpnServerDto>>))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<List<VpnServerDto>>>> GetServers()
    {
        try
        {
            var userId = GetCurrentUserId();
            var servers = await _serverService.GetServersAsync(userId);
            return Ok(ApiResponse<List<VpnServerDto>>.SuccessResponse(servers));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting servers");
            return BadRequest(ApiResponse<List<VpnServerDto>>.ErrorResponse("Failed to get servers"));
        }
    }

    /// <summary>
    /// Get a specific VPN server by ID
    /// </summary>
    /// <param name="id">Server ID</param>
    /// <returns>Server details</returns>
    [HttpGet("{id}")]
    [Authorize]
    [SwaggerOperation(Summary = "Get server", Description = "Gets a specific VPN server by ID")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<VpnServerDto>))]
    [SwaggerResponse(404, "Server not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<VpnServerDto>>> GetServer(Guid id)
    {
        try
        {
            var server = await _serverService.GetServerByIdAsync(id);
            if (server == null)
                return NotFound(ApiResponse<VpnServerDto>.ErrorResponse("Server not found"));

            return Ok(ApiResponse<VpnServerDto>.SuccessResponse(server));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting server {ServerId}", id);
            return BadRequest(ApiResponse<VpnServerDto>.ErrorResponse("Failed to get server"));
        }
    }

    /// <summary>
    /// Get recommended VPN server
    /// </summary>
    /// <param name="request">Recommendation preferences (optional)</param>
    /// <returns>Recommended server</returns>
    [HttpGet("recommended")]
    [HttpPost("recommended")]
    [Authorize]
    [SwaggerOperation(Summary = "Get recommended server", Description = "Gets the best VPN server based on user location and preferences")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<VpnServerDto>))]
    [SwaggerResponse(404, "No servers available", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<VpnServerDto>>> GetRecommendedServer([FromBody] RecommendationRequest? request = null)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<VpnServerDto>.ErrorResponse("Unauthorized"));

            var server = await _serverService.GetRecommendedServerAsync(userId.Value, request);
            if (server == null)
                return NotFound(ApiResponse<VpnServerDto>.ErrorResponse("No servers available"));

            return Ok(ApiResponse<VpnServerDto>.SuccessResponse(server));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting recommended server");
            return BadRequest(ApiResponse<VpnServerDto>.ErrorResponse("Failed to get recommended server"));
        }
    }

    /// <summary>
    /// Get VPN configuration for a server
    /// </summary>
    /// <param name="id">Server ID</param>
    /// <returns>VPN configuration</returns>
    [HttpGet("{id}/config")]
    [Authorize]
    [SwaggerOperation(Summary = "Get VPN config", Description = "Gets the VPN configuration for connecting to a server")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<VpnConfigResponse>))]
    [SwaggerResponse(401, "Unauthorized or insufficient access", typeof(ApiResponse))]
    [SwaggerResponse(404, "Server not found", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<VpnConfigResponse>>> GetServerConfig(Guid id)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<VpnConfigResponse>.ErrorResponse("Unauthorized"));

            var config = await _serverService.GetServerConfigAsync(id, userId.Value);
            return Ok(ApiResponse<VpnConfigResponse>.SuccessResponse(config));
        }
        catch (UnauthorizedAccessException ex)
        {
            return StatusCode(403, ApiResponse<VpnConfigResponse>.ErrorResponse(ex.Message));
        }
        catch (InvalidOperationException ex)
        {
            return NotFound(ApiResponse<VpnConfigResponse>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting server config {ServerId}", id);
            return BadRequest(ApiResponse<VpnConfigResponse>.ErrorResponse("Failed to get server config"));
        }
    }

    /// <summary>
    /// Log connection start to a server
    /// </summary>
    /// <param name="id">Server ID</param>
    /// <param name="request">Connection details</param>
    /// <returns>Connection log entry</returns>
    [HttpPost("{id}/connect")]
    [Authorize]
    [SwaggerOperation(Summary = "Connect to server", Description = "Logs the start of a VPN connection")]
    [SwaggerResponse(200, "Connection logged", typeof(ApiResponse<ConnectionLogDto>))]
    [SwaggerResponse(400, "Connection failed", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<ConnectionLogDto>>> Connect(Guid id, [FromBody] ConnectRequest request)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<ConnectionLogDto>.ErrorResponse("Unauthorized"));

            var clientIp = GetClientIpAddress();
            var connectionLog = await _serverService.ConnectToServerAsync(id, userId.Value, request, clientIp);
            return Ok(ApiResponse<ConnectionLogDto>.SuccessResponse(connectionLog, "Connected successfully"));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse<ConnectionLogDto>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error connecting to server {ServerId}", id);
            return BadRequest(ApiResponse<ConnectionLogDto>.ErrorResponse("Failed to connect"));
        }
    }

    /// <summary>
    /// Log connection end from a server
    /// </summary>
    /// <param name="id">Server ID</param>
    /// <param name="request">Disconnection details</param>
    /// <returns>Updated connection log</returns>
    [HttpPost("{id}/disconnect")]
    [Authorize]
    [SwaggerOperation(Summary = "Disconnect from server", Description = "Logs the end of a VPN connection")]
    [SwaggerResponse(200, "Disconnection logged", typeof(ApiResponse<ConnectionLogDto>))]
    [SwaggerResponse(400, "No active connection", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<ConnectionLogDto>>> Disconnect(Guid id, [FromBody] DisconnectRequest request)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<ConnectionLogDto>.ErrorResponse("Unauthorized"));

            var connectionLog = await _serverService.DisconnectFromServerAsync(id, userId.Value, request);
            return Ok(ApiResponse<ConnectionLogDto>.SuccessResponse(connectionLog, "Disconnected successfully"));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse<ConnectionLogDto>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error disconnecting from server {ServerId}", id);
            return BadRequest(ApiResponse<ConnectionLogDto>.ErrorResponse("Failed to disconnect"));
        }
    }

    private Guid? GetCurrentUserId()
    {
        var userIdClaim = User.FindFirst(System.Security.Claims.ClaimTypes.NameIdentifier)?.Value;
        return Guid.TryParse(userIdClaim, out var userId) ? userId : null;
    }

    private string? GetClientIpAddress()
    {
        return HttpContext.Connection.RemoteIpAddress?.ToString();
    }
}
