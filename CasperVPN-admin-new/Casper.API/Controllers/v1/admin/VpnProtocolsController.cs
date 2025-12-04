using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Logging;
using Swashbuckle.AspNetCore.Annotations;
using static Casper.API.Models.Enums;

namespace Casper.API.Controllers.v1.admin
{
    public class VpnProtocolsController : BaseAdminController
    {
        private readonly IVpnProtocolService _vpnProtocolService;
        private readonly ILogger<VpnProtocolsController> _logger;

        public VpnProtocolsController(
            IVpnProtocolService vpnProtocolService,
            ILogger<VpnProtocolsController> logger)
        {
            _vpnProtocolService = vpnProtocolService;
            _logger = logger;
        }

        [HttpGet]
        [SwaggerOperation(
            Summary = "Get All VPN Protocols",
            Description = "Retrieve a list of all available VPN protocols."
        )]
        public async Task<IActionResult> GetAllProtocols()
        {
            var result = await _vpnProtocolService.GetAllProtocolsAsync();
            return HandleResult(result);
        }

        [HttpGet("{id}")]
        [SwaggerOperation(
            Summary = "Get Protocol by ID",
            Description = "Retrieve VPN protocol details using the protocol ID."
        )]
        public async Task<IActionResult> GetProtocolById(string id)
        {
            var result = await _vpnProtocolService.GetProtocolByIdAsync(id);
            return HandleResult(result);
        }

        [HttpPost]
        [SwaggerOperation(
            Summary = "Create VPN Protocol",
            Description = "Create a new VPN protocol entry."
        )]
        public async Task<IActionResult> CreateProtocol([FromBody] CreateVpnProtocolRequest request)
        {
            if (!ModelState.IsValid)
            {
                var errors = ModelState.Values
                    .SelectMany(v => v.Errors)
                    .Select(e => e.ErrorMessage)
                    .ToList();
                return BadRequest(Result<dynamic>.Failure(string.Join("; ", errors), ErrorType.Validation));
            }

            var result = await _vpnProtocolService.CreateProtocolAsync(request);
            return HandleResult(result);
        }

        [HttpPut]
        [SwaggerOperation(
            Summary = "Update VPN Protocol",
            Description = "Update an existing VPN protocol."
        )]
        public async Task<IActionResult> UpdateProtocol([FromBody] UpdateVpnProtocolRequest request)
        {
            if (!ModelState.IsValid)
            {
                var errors = ModelState.Values
                    .SelectMany(v => v.Errors)
                    .Select(e => e.ErrorMessage)
                    .ToList();
                return BadRequest(Result<dynamic>.Failure(string.Join("; ", errors), ErrorType.Validation));
            }

            var result = await _vpnProtocolService.UpdateProtocolAsync(request);
            return HandleResult(result);
        }

        [HttpDelete("{id}")]
        [SwaggerOperation(
            Summary = "Delete VPN Protocol",
            Description = "Delete a VPN protocol using its ID."
        )]
        public async Task<IActionResult> DeleteProtocol(string id)
        {
            var result = await _vpnProtocolService.DeleteProtocolAsync(id);
            return HandleResult(result);
        }
    }
}