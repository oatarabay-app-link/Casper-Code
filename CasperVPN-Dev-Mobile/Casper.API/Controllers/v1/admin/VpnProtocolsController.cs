using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Logging;
using Swashbuckle.AspNetCore.Annotations;

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

        // -----------------------------------------------------------
        // GET: All Protocols
        // -----------------------------------------------------------
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

        // -----------------------------------------------------------
        [HttpGet("{id}")]
        [SwaggerOperation(
       Summary = "Get ProtocolName by ID",
       Description = "Retrieve VPN protocol details using the protocol ID."
   )]
        public async Task<IActionResult> GetProtocolById(string id)
        {

            var result = await _vpnProtocolService.GetProtocolByIdAsync(id);
            return HandleResult(result);
        }

        // -----------------------------------------------------------
        // POST: Create ProtocolName
        // -----------------------------------------------------------
        [HttpPost]
        [SwaggerOperation(
            Summary = "Create VPN ProtocolName",
            Description = "Create a new VPN protocol entry."
        )]
        public async Task<IActionResult> CreateProtocol([FromBody] CreateVpnProtocolRequest request)
        {
            var result = await _vpnProtocolService.CreateProtocolAsync(request);
            return HandleResult(result);
        }

        // -----------------------------------------------------------
        // PUT: Update ProtocolName
        // -----------------------------------------------------------
        [HttpPut]
        [SwaggerOperation(
           Summary = "Update VPN ProtocolName",
           Description = "Update an existing VPN protocol."
        )]
        public async Task<IActionResult> UpdateProtocol([FromBody] UpdateVpnProtocolRequest request)
        {
            if (string.IsNullOrEmpty(request.Id))
            {
                return BadRequest(new { message = "ProtocolName ID is required" });
            }

            var result = await _vpnProtocolService.UpdateProtocolAsync(request);
            return HandleResult(result);
        }

     

        // -----------------------------------------------------------
        // DELETE: Delete ProtocolName
        // -----------------------------------------------------------
        [HttpDelete("{id}")]
        [SwaggerOperation(
            Summary = "Delete VPN ProtocolName",
            Description = "Delete a VPN protocol using its ID."
        )]
        public async Task<IActionResult> DeleteProtocol(string id)
        {
            var result = await _vpnProtocolService.DeleteProtocolAsync(id);
            return HandleResult(result);
        }
    }
}
