using Casper.API.Interfaces;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;
using Casper.API.Services;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Casper.API.Controllers
{
    [Route("api/[controller]")]
    [Authorize(Policy = "Admin")]
    [ApiController]
    public class ServerController : BaseController
    {
        private readonly IServerService _serverServices;

        public ServerController(IServerService serverServices)
        {
            _serverServices = serverServices;
        }

        [HttpPost("create")]
        public async Task<IActionResult> Create([FromBody] ServerRequest.CreateServer request)
        {
            var response = await _serverServices.CreateServerAsync(request);
            return CreateResponse(response);
        }

        [HttpPut("update")]
        public async Task<IActionResult> Update([FromBody] ServerRequest.UpdateServer request)
        {
            var response = await _serverServices.UpdateServerAsync(request);
            return CreateResponse(response);
        }

        [HttpPut("status")]
        [AllowAnonymous] // Allow agent to send status without full auth
        public async Task<IActionResult> UpdateStatus([FromBody] ServerRequest.UpdateServerStatus request)
        {
            var response = await _serverServices.UpdateServerStatusAsync(request);
            return CreateResponse(response);
        }

        [HttpGet("settings/{serverName}")]
        [AllowAnonymous] // Allow agent to fetch settings
        public async Task<IActionResult> GetSettings(string serverName)
        {
            var response = await _serverServices.GetServerSettingsAsync(serverName);
            return CreateResponse(response);
        }

        [HttpGet("all")]
        public async Task<IActionResult> GetAll(
            [FromQuery] int pageNumber = 1,
            [FromQuery] int pageSize = 10)
        {
            var response = await _serverServices.GetServersAsync(pageNumber, pageSize);
            return CreateResponse(response);
        }

      

        [HttpDelete("{id}")]
        public async Task<IActionResult> Delete(Guid id)
        {
            var response = await _serverServices.DeleteServerAsync(id);
            return CreateResponse(response);
        }
    }
}