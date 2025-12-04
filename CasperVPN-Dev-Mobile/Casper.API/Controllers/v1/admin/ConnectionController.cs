
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Mvc;
using static Casper.API.Models.Enums;

namespace Casper.API.Controllers.v1.admin
{
    public class ConnectionsController : BaseAdminController
    {



        private readonly IConnectionService _connectionService;
        private readonly ILogger<ConnectionsController> _logger;

        public ConnectionsController(
            IConnectionService connectionService,
            ILogger<ConnectionsController> logger)
        {
            _connectionService = connectionService;
            _logger = logger;
        }

        [HttpGet]
        public async Task<IActionResult> GetAllConnections()
        {
            var result = await _connectionService.GetConnectionsWithSummary();
            return HandleResult(result);
        }


        [HttpGet("user/{userId:guid}")]
        public async Task<IActionResult> GetConnectionsByUserId(string userId)
        {
            var result = await _connectionService.GetConnectionsByUserIdAsync(userId);
            return HandleResult(result);
        }

        [HttpGet("server/{serverId:guid}")]
        public async Task<IActionResult> GetConnectionsByServerId(string serverId)
        {
            var result = await _connectionService.GetConnectionsByServerIdAsync(serverId);
            return HandleResult(result);
        }

        //[HttpPost("create")]
        //public async Task<IActionResult> CreateConnection([FromBody] CreateConnectionRequest request)
        //{
        //    var result = await _connectionService.CreateConnectionAsync(request);
        //    return HandleResult(result);
        //}

        //[HttpPut("update")]
        //public async Task<IActionResult> UpdateConnection([FromBody] UpdateConnectionRequest request)
        //{
        //    if (string.IsNullOrEmpty(request.Id))
        //    {
        //        return BadRequest(new { message = "Connection ID is required" });
        //    }

        //    var result = await _connectionService.UpdateConnectionAsync(request);
        //    return HandleResult(result);
        //}

        [HttpPut("{id:guid}/status")]
        public async Task<IActionResult> UpdateConnectionStatus(string id, [FromBody] ConnectionStatus status)
        {
            var result = await _connectionService.UpdateConnectionStatusAsync(id, status);
            return HandleResult(result);
        }

        [HttpPut("{id:guid}/stats")]
        public async Task<IActionResult> UpdateConnectionStats(string id, [FromBody] ConnectionStatsRequest stats)
        {
            var result = await _connectionService.UpdateConnectionStatsAsync(id, stats.Upload, stats.Download);
            return HandleResult(result);
        }

        [HttpDelete("{id:guid}")]
        public async Task<IActionResult> DeleteConnection(string id)
        {
            var result = await _connectionService.DeleteConnectionAsync(id);
            return HandleResult(result);
        }


    }

    public class ConnectionStatsRequest
    {
        public double Upload { get; set; }
        public double Download { get; set; }
    }
}