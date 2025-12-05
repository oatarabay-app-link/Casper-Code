using Microsoft.AspNetCore.Mvc;
using System;
using System.Collections.Generic;

namespace CasperVPN.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class VpnController : ControllerBase
    {
        [HttpGet("servers")]
        public IActionResult GetServers()
        {
            var servers = new[]
            {
                new { id = 1, name = "US-East-1", location = "New York", load = 45 },
                new { id = 2, name = "US-West-1", location = "Los Angeles", load = 67 },
                new { id = 3, name = "EU-West-1", location = "London", load = 23 }
            };
            return Ok(servers);
        }

        [HttpGet("status")]
        public IActionResult GetStatus()
        {
            return Ok(new { status = "connected", server = "US-East-1", uptime = "2h 34m" });
        }
    }
}
