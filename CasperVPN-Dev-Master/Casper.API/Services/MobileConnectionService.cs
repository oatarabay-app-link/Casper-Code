using System.Linq;
using Casper.API.Models;
using Casper.API.Models.Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.EntityFrameworkCore;
using static Casper.API.Models.Enums;

namespace Casper.API.Services
{
   
    public class MobileConnectionService : IMobileConnectionService
    {
        // Hardcoded mock data for connections
        private readonly List<MobileConnection> _mockConnections = new()
    {
        new MobileConnection
        {
            PublicKey = "RDR8anBA6RVnnQQP6EenZXmzbfDKwMIAlu5xlsTT1UI=",
            AllowIps = "10.0.0.2/32",
            EndPoint = "192.168.36.100:51820",
            PersistentKeepAlive = 25,
            LastHandshake = DateTime.UtcNow.AddHours(-1),
            Status = "Active",
            CreatedAt = DateTime.UtcNow.AddDays(-7)
        }
    };

        // Hardcoded server configuration - CONSTANT DATA
        private const string ServerPublicKey = "r6PtCilLzg0ekju2TgoIByjQQkpD/VtkWf0bJMCuegM=";
        private const string ServerEndpoint = "192.168.36.100:51820";

        public async Task<Result<object>> CreateConnectionAsync(CreateMobileConnectionRequest request)
        {
            // Validate the client public key
            if (string.IsNullOrWhiteSpace(request.ClientPublicKey))
            {
                return Result<object>.Failure("Client public key is required", ErrorType.Validation);
            }

            if (request.ClientPublicKey.Length != 44 || !IsBase64String(request.ClientPublicKey))
            {
                return Result<object>.ValidationFailure(
                    new List<string> { "Client public key must be a valid base64 encoded WireGuard public key (44 characters)" }
                );
            }

            // Check if connection already exists
            if (_mockConnections.Any(c => c.PublicKey == request.ClientPublicKey))
            {
                return Result<object>.Conflict(
                    $"Connection with public key '{request.ClientPublicKey}' already exists"
                );
            }

            // Create the exact JSON response structure with CONSTANT data
            var response = new
            {
                clientPublicKey = request.ClientPublicKey,
                serverPublicKey = ServerPublicKey,
                serverEndpoint = ServerEndpoint,
                AllowIps = $"{GenerateClientIp()}/32",

            };

            // Add to mock connections
            var newConnection = new MobileConnection
            {
                PublicKey = request.ClientPublicKey,
                AllowIps = $"{GenerateClientIp()}/32",
                EndPoint = ServerEndpoint,
                PersistentKeepAlive = 25,
                LastHandshake = null,
                Status = "Pending",
                CreatedAt = DateTime.UtcNow
            };

            _mockConnections.Add(newConnection);

            return Result<object>.Success(response);
        }

      
      
        private string GenerateClientIp()
        {
            var existingIps = _mockConnections
                .Select(c => c.AllowIps.Split('/')[0])
                .Where(ip => ip.StartsWith("10.0.0."))
                .Select(ip => int.Parse(ip.Split('.')[3]))
                .ToList();

            var nextIp = 2;
            while (existingIps.Contains(nextIp))
            {
                nextIp++;
            }

            //return $"10.0.0.{nextIp}";

            return "192.168.30.2";
        }

   

        private bool IsBase64String(string base64)
        {
            try
            {
                Convert.FromBase64String(base64);
                return true;
            }
            catch
            {
                return false;
            }
        }
    }
}


