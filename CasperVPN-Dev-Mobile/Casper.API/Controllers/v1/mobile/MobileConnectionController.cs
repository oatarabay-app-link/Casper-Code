using Casper.API.Models;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Services;
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using static Casper.API.Models.Enums;

namespace Casper.API.Controllers.v1.mobile
{
    public class MobileConnectionsController : BaseMobileController
    {

        private readonly IMobileConnectionService _connectionService;

        public MobileConnectionsController(IMobileConnectionService connectionService)
        {
            _connectionService = connectionService;
        }

        [HttpPost("create")]
        public async Task<IActionResult> CreateConnection([FromBody] CreateMobileConnectionRequest request)
        {
            var result = await _connectionService.CreateConnectionAsync(request);
            return HandleResult(result);
        }















    }

  
}