using Casper.API.Interfaces;
using Casper.API.Models.Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Responses;
using Casper.API.Models;
using Microsoft.EntityFrameworkCore;
using Casper.API.Models.Requests;

namespace Casper.API.Services
{
    public class ServerService : IServerService
    {
        private readonly CasperVpnDbContext _context;
        private readonly ILogger<ServerService> _logger;

        public ServerService(CasperVpnDbContext context, ILogger<ServerService> logger)
        {
            _context = context;
            _logger = logger;
        }

        public async Task<ServiceResponse> CreateServerAsync(ServerRequest.CreateServer createServer)
        {
            try
            {
                if (createServer == null)
                    return ServiceResponse.BadRequest("Request body required");

                var existingServer = await _context.Servers.FirstOrDefaultAsync(s => s.ServerName == createServer.ServerName);
                if (existingServer != null)
                    return ServiceResponse.Conflict("Server name already exists");

                var server = new Server
                {
                    ServerName = createServer.ServerName,
                    Connection_Protocol = createServer.ConnectionProtocol,
                    Location = createServer.Location,
                    Server_Status = createServer.ServerStatus,
                    ConnectedUsers = 0,
                    MaxUsers = createServer.MaxUsers,
                    ConnectionTimeout = createServer.ConnectionTimeout,
                    HealthCheckInterval = createServer.HealthCheckInterval,
                    Load = 0,
                    IPAddress = createServer.IPAddress,
                    CreatedAt = DateTime.UtcNow,
                    UpdatedAt = DateTime.UtcNow
                };

                _context.Servers.Add(server);
                await _context.SaveChangesAsync();

                return ServiceResponse.Created(server, "Server created successfully");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error creating server");
                return ServiceResponse.Error("Error creating server", 500, ex.Message);
            }
        }

        public async Task<ServiceResponse> UpdateServerAsync(ServerRequest.UpdateServer updateServer)
        {
            try
            {
                var server = await _context.Servers.FindAsync(updateServer.Id);
                if (server == null)
                    return ServiceResponse.NotFound("Server not found");

                // For strings - check if not null or empty
                if (!string.IsNullOrEmpty(updateServer.ServerName) && updateServer.ServerName != server.ServerName)
                    server.ServerName = updateServer.ServerName;

                // For enums and value types - check if different from current value
                if (updateServer.ConnectionProtocol != server.Connection_Protocol)
                    server.Connection_Protocol = updateServer.ConnectionProtocol;

                if (!string.IsNullOrEmpty(updateServer.Location) && updateServer.Location != server.Location)
                    server.Location = updateServer.Location;

                if (updateServer.ServerStatus != server.Server_Status)
                    server.Server_Status = updateServer.ServerStatus;

                if (updateServer.MaxUsers != server.MaxUsers)
                    server.MaxUsers = updateServer.MaxUsers;

                if (updateServer.ConnectionTimeout != server.ConnectionTimeout)
                    server.ConnectionTimeout = updateServer.ConnectionTimeout;

                if (updateServer.HealthCheckInterval != server.HealthCheckInterval)
                    server.HealthCheckInterval = updateServer.HealthCheckInterval;

                if (!string.IsNullOrEmpty(updateServer.IPAddress) && updateServer.IPAddress != server.IPAddress)
                    server.IPAddress = updateServer.IPAddress;

                server.UpdatedAt = DateTime.UtcNow;

                _context.Servers.Update(server);
                await _context.SaveChangesAsync();

                return ServiceResponse.Success(server, "Server updated successfully");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating server");
                return ServiceResponse.Error("Error updating server", 500, ex.Message);
            }
        }

        public async Task<ServiceResponse> UpdateServerStatusAsync(ServerRequest.UpdateServerStatus statusUpdate)
        {
            try
            {
                var server = await _context.Servers
                    .FirstOrDefaultAsync(s => s.ServerName == statusUpdate.ServerName);
                
                if (server == null)
                    return ServiceResponse.NotFound("Server not found");

                server.Server_Status = statusUpdate.ServerStatus;
                server.ConnectedUsers = statusUpdate.ConnectedUsers;
                server.Load = statusUpdate.Load;
                server.UpdatedAt = DateTime.UtcNow;

                _context.Servers.Update(server);
                await _context.SaveChangesAsync();

                return ServiceResponse.Success(null, "Server status updated successfully");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating server status");
                return ServiceResponse.Error("Error updating server status", 500, ex.Message);
            }
        }

        public async Task<ServiceResponse> GetServerSettingsAsync(string serverName)
        {
            try
            {
                var server = await _context.Servers
                    .FirstOrDefaultAsync(s => s.ServerName == serverName);
                
                if (server == null)
                    return ServiceResponse.NotFound("Server not found");

                var settings = new ServerResponse.ServerSettings
                {
                    MaxUsers = server.MaxUsers,
                    ConnectionTimeout = server.ConnectionTimeout,
                    HealthCheckInterval = server.HealthCheckInterval,
                    ServerStatus = server.Server_Status,
                    AutoProvision = true // Default value, can be made configurable
                };

                return ServiceResponse.Success(settings, "Server settings retrieved successfully");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving server settings");
                return ServiceResponse.Error("Error retrieving server settings", 500, ex.Message);
            }
        }



        public async Task<ServiceResponse> GetServersAsync(int pageNumber = 1, int pageSize = 10)
        {
            try
            {
                if (pageNumber < 1) pageNumber = 1;
                if (pageSize < 1 || pageSize > 100) pageSize = 10;

                var totalCount = await _context.Servers.CountAsync();

                var servers = await _context.Servers
                    .OrderBy(s => s.ServerName)
                    .Skip((pageNumber - 1) * pageSize)
                    .Take(pageSize)
                    .ToListAsync();

                var paginationInfo = new PaginationInfo
                {
                    PageNumber = pageNumber,
                    PageSize = pageSize,
                    TotalCount = totalCount
                };

                return ServiceResponse.Success(servers, "Servers retrieved successfully", paginationInfo);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving servers");
                return ServiceResponse.Error("Error retrieving servers", 500, ex.Message);
            }
        }

        public async Task<ServiceResponse> GetServersAsync() // Keep the original method for backward compatibility
        {
            return await GetServersAsync(1, 10);
        }

        public async Task<ServiceResponse> DeleteServerAsync(Guid id)
        {
            try
            {
                var server = await _context.Servers.FindAsync(id);
                if (server == null)
                    return ServiceResponse.NotFound("Server not found");

                _context.Servers.Remove(server);
                await _context.SaveChangesAsync();

                return ServiceResponse.Success(null, "Server deleted successfully");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error deleting server");
                return ServiceResponse.Error("Error deleting server", 500, ex.Message);
            }
        }
    }
}