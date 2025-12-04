using Casper.API.Models.Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Responses;
using Casper.API.Models;
using Microsoft.EntityFrameworkCore;
using Casper.API.Models.Requests;
using Casper.API.Models.Common;
using static Casper.API.Models.Enums;
using Casper.API.Services.Interfaces;

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

        public async Task<Result<dynamic>> CreateServerAsync(CreateServerRequest createServer)
        {
            try
            {
                _logger.LogInformation("Creating server: {ServerName}", createServer.ServerName);

                // Check for duplicate server name (BUSINESS LOGIC - not validation)
                var existingServer = await _context.Servers
                    .FirstOrDefaultAsync(s => s.ServerName.ToLower() == createServer.ServerName.ToLower() && s.IsActive);

                if (existingServer != null)
                {
                    _logger.LogWarning("Server name '{ServerName}' already exists", createServer.ServerName);
                    return Result<dynamic>.Failure($"Server name '{createServer.ServerName}' already exists", ErrorType.Conflict);
                }

                // Check for duplicate IP address (BUSINESS LOGIC - not validation)
                var existingIpServer = await _context.Servers
                    .FirstOrDefaultAsync(s => s.IpAddress == createServer.IPAddress && s.IsActive);

                if (existingIpServer != null)
                {
                    _logger.LogWarning("IP address '{IPAddress}' is already assigned to another server", createServer.IPAddress);
                    return Result<dynamic>.Failure($"IP address '{createServer.IPAddress}' is already assigned to another server", ErrorType.Conflict);
                }

                // Determine authentication method (already validated in controller)
                bool hasPassword = !string.IsNullOrWhiteSpace(createServer.Password);
                bool hasSshKey = !string.IsNullOrWhiteSpace(createServer.Sshkey);

                // Create server entity
                var server = new Server
                {
                    ServerName = createServer.ServerName,
                    Location = createServer.Location,
                    Server_Status = createServer.ServerStatus.Value,
                    IpAddress = createServer.IPAddress,
                    Username = createServer.Username,
                    Password = hasPassword ? createServer.Password : null,
                    Sshkey = hasSshKey ? createServer.Sshkey : null,
                    IsActive = true,
                    CreatedAt = DateTime.UtcNow,
                    UpdatedAt = DateTime.UtcNow
                };

                // Save to database
                _context.Servers.Add(server);
                await _context.SaveChangesAsync();

                // Return response without sensitive data
                var responseServer = new
                {
                    id = server.Id,
                    serverName = server.ServerName,
                    location = server.Location,
                    serverStatus = server.Server_Status.ToString(),
                    ipAddress = server.IpAddress,
                    username = server.Username,
                    hasPassword = !string.IsNullOrEmpty(server.Password),
                    hasSshKey = !string.IsNullOrEmpty(server.Sshkey),
                    isActive = server.IsActive,
                    createdAt = server.CreatedAt
                };

                _logger.LogInformation("Server created successfully: {ServerName} (ID: {ServerId})", server.ServerName, server.Id);
                return Result<dynamic>.Success(responseServer);
            }
            catch (DbUpdateException dbEx)
            {
                _logger.LogError(dbEx, "Database update error while creating server");

                // MySQL duplicate entry handling
                if (dbEx.InnerException != null &&
                    (dbEx.InnerException.Message.Contains("Duplicate entry") ||
                     dbEx.InnerException.Message.Contains("UNIQUE constraint")))
                {
                    return Result<dynamic>.Failure("Server name or IP address already exists", ErrorType.Conflict);
                }

                return Result<dynamic>.Failure("A database error occurred while creating the server", ErrorType.InternalError);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Unexpected error while creating server");
                return Result<dynamic>.Failure("An unexpected error occurred while creating the server", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> UpdateServerAsync(UpdateServerRequest updateServer)
        {
            try
            {
                _logger.LogInformation("Updating server ID: {ServerId}", updateServer.Id);

                // Parse ID (already validated in controller, but keep for safety)
                if (!Guid.TryParse(updateServer.Id, out Guid serverId))
                {
                    return Result<dynamic>.Failure("Invalid server ID format", ErrorType.Validation);
                }

                // Find server
                var server = await _context.Servers
                    .FirstOrDefaultAsync(s => s.Id == serverId);

                if (server == null)
                {
                    _logger.LogWarning("Server not found: {ServerId}", updateServer.Id);
                    return Result<dynamic>.NotFound("Server not found");
                }

                bool hasActualChanges = false;

                // Update only provided fields (NO VALIDATION - just apply changes)
                if (!string.IsNullOrWhiteSpace(updateServer.ServerName))
                {
                    var trimmedName = updateServer.ServerName.Trim();

                    // Check for duplicate server name (BUSINESS LOGIC - not validation)
                    var existing = await _context.Servers
                        .FirstOrDefaultAsync(s =>
                            s.ServerName.ToLower() == trimmedName.ToLower() &&
                            s.Id != serverId &&
                            s.IsActive);

                    if (existing != null)
                    {
                        _logger.LogWarning("Server name '{ServerName}' already exists", trimmedName);
                        return Result<dynamic>.Failure($"Server name '{trimmedName}' already exists", ErrorType.Conflict);
                    }

                    if (trimmedName != server.ServerName)
                    {
                        server.ServerName = trimmedName;
                        hasActualChanges = true;
                    }
                }

                if (!string.IsNullOrWhiteSpace(updateServer.Location))
                {
                    var trimmedLocation = updateServer.Location.Trim();
                    if (trimmedLocation != server.Location)
                    {
                        server.Location = trimmedLocation;
                        hasActualChanges = true;
                    }
                }

                if (!string.IsNullOrWhiteSpace(updateServer.IPAddress))
                {
                    var trimmedIp = updateServer.IPAddress.Trim();

                    // Check for duplicate IP address (BUSINESS LOGIC - not validation)
                    var existingIp = await _context.Servers
                        .FirstOrDefaultAsync(s =>
                            s.IpAddress == trimmedIp &&
                            s.Id != serverId &&
                            s.IsActive);

                    if (existingIp != null)
                    {
                        _logger.LogWarning("IP address '{IPAddress}' already exists", trimmedIp);
                        return Result<dynamic>.Failure($"IP address '{trimmedIp}' is already assigned to another server", ErrorType.Conflict);
                    }

                    if (trimmedIp != server.IpAddress)
                    {
                        server.IpAddress = trimmedIp;
                        hasActualChanges = true;
                    }
                }

                if (updateServer.ServerStatus.HasValue &&
                    updateServer.ServerStatus != server.Server_Status)
                {
                    server.Server_Status = updateServer.ServerStatus.Value;
                    hasActualChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(updateServer.Username))
                {
                    var trimmedUsername = updateServer.Username.Trim();
                    if (trimmedUsername != server.Username)
                    {
                        server.Username = trimmedUsername;
                        hasActualChanges = true;
                    }
                }

                // Handle authentication updates
                if (!string.IsNullOrWhiteSpace(updateServer.Password))
                {
                    var trimmedPassword = updateServer.Password.Trim();
                    if (trimmedPassword != server.Password)
                    {
                        server.Password = trimmedPassword;
                        server.Sshkey = null; // Clear SSH key if password is set
                        hasActualChanges = true;
                    }
                }

                if (!string.IsNullOrWhiteSpace(updateServer.Sshkey))
                {
                    var trimmedSshkey = updateServer.Sshkey.Trim();
                    if (trimmedSshkey != server.Sshkey)
                    {
                        server.Sshkey = trimmedSshkey;
                        server.Password = null; // Clear password if SSH key is set
                        hasActualChanges = true;
                    }
                }

                // No change made (this is business logic, not input validation)
                if (!hasActualChanges)
                {
                    _logger.LogInformation("No actual changes detected for server ID: {ServerId}", updateServer.Id);
                    return Result<dynamic>.Failure("No changes detected or all provided values are the same as current values", ErrorType.Validation);
                }

                // Save changes
                server.UpdatedAt = DateTime.UtcNow;
                await _context.SaveChangesAsync();

                _logger.LogInformation("Server updated successfully: {ServerId}", server.Id);

                return Result<dynamic>.Success(new
                {
                    id = server.Id,
                    serverName = server.ServerName,
                    location = server.Location,
                    ipAddress = server.IpAddress,
                    serverStatus = server.Server_Status.ToString(),
                    username = server.Username,
                    hasPassword = !string.IsNullOrEmpty(server.Password),
                    hasSshKey = !string.IsNullOrEmpty(server.Sshkey),
                    updatedAt = server.UpdatedAt,
                    isActive = server.IsActive,
                    message = "Server updated successfully"
                });
            }
            catch (DbUpdateException dbEx)
            {
                _logger.LogError(dbEx, "Database update error while updating server");

                if (dbEx.InnerException != null &&
                    (dbEx.InnerException.Message.Contains("Duplicate entry") ||
                     dbEx.InnerException.Message.Contains("UNIQUE constraint")))
                {
                    return Result<dynamic>.Failure("Server name or IP address already exists", ErrorType.Conflict);
                }

                return Result<dynamic>.Failure("A database error occurred while updating the server", ErrorType.InternalError);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Unexpected error updating server");
                return Result<dynamic>.Failure("An unexpected error occurred while updating the server", ErrorType.InternalError);
            }
        }
        public async Task<Result<dynamic>> GetServersAsync(int pageNumber = 1, int pageSize = 10)
        {
            try
            {
                // Validate pagination parameters (minimal validation since controller already validated)
                pageNumber = Math.Max(1, pageNumber);
                pageSize = Math.Clamp(pageSize, 1, 100);

                _logger.LogInformation("Getting servers - Page: {PageNumber}, Size: {PageSize}", pageNumber, pageSize);

                // Only count active servers
                var totalServers = await _context.Servers.AsNoTracking().CountAsync(s => s.IsActive);

                // Get current date/time for active connections (last 5 minutes)
                var activeConnectionThreshold = DateTime.UtcNow.AddMinutes(-5);

                var servers = await _context.Servers.AsNoTracking()
                    .Where(s => s.IsActive)
                    .OrderBy(s => s.ServerName)
                    .Skip((pageNumber - 1) * pageSize)
                    .Take(pageSize)
                    .Select(s => new
                    {
                        Id = s.Id,
                        ServerName = s.ServerName,
                        Location = s.Location,
                        IpAddress = s.IpAddress,
                        ServerStatus = s.Server_Status.ToString(),
                        Users = _context.Connections
                            .Count(c => c.ServerId == s.Id.ToString() &&
                                       c.ConnectionStatus == ConnectionStatus.Connected &&
                                       c.UserConnectTime >= activeConnectionThreshold),
                        Load = Math.Min(100, (_context.Connections
                            .Count(c => c.ServerId == s.Id.ToString() &&
                                       c.ConnectionStatus == ConnectionStatus.Connected &&
                                       c.UserConnectTime >= activeConnectionThreshold) * 100) / 1000),
                    })
                    .ToListAsync();

                // Get summary statistics
                var activeConnectionThresholdSummary = DateTime.UtcNow.AddMinutes(-5);
                var totalActiveUsers = await _context.Connections
                    .CountAsync(c => c.ConnectionStatus == ConnectionStatus.Connected &&
                                   c.UserConnectTime >= activeConnectionThresholdSummary);

                var summary = new
                {
                    TotalServers = totalServers,
                    ActiveServers = totalServers,
                    TotalActiveUsers = totalActiveUsers,
                    ServersWithPassword = await _context.Servers.CountAsync(s => s.IsActive && !string.IsNullOrEmpty(s.Password)),
                    ServersWithSshKey = await _context.Servers.CountAsync(s => s.IsActive && !string.IsNullOrEmpty(s.Sshkey))
                };

                // Create pagination info
                var paginationInfo = new PaginationInfo
                {
                    PageNumber = pageNumber,
                    PageSize = pageSize,
                    TotalCount = totalServers
                };

                _logger.LogInformation("Retrieved {Count} servers", servers.Count);

                // Return data in the same structure as MobileConnectionService
                return Result<dynamic>.Success(new
                {
                    Servers = servers,
                    Summary = summary,
                    Pagination = paginationInfo
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving servers");
                return Result<dynamic>.Failure("An unexpected error occurred while retrieving servers", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> DeleteServerAsync(string id)
        {
            try
            {
                _logger.LogInformation("Deleting server: {ServerId}", id);

                // Parse ID
                if (!Guid.TryParse(id, out Guid serverId))
                {
                    return Result<dynamic>.Failure("Invalid server ID format", ErrorType.Validation);
                }

                var server = await _context.Servers.FirstOrDefaultAsync(s => s.Id == serverId);

                if (server == null)
                {
                    _logger.LogWarning("Server not found: {ServerId}", id);
                    return Result<dynamic>.NotFound("Server not found");
                }

                // Check if already deactivated
                if (!server.IsActive)
                {
                    _logger.LogWarning("Server already deactivated: {ServerId}", id);
                    return Result<dynamic>.Failure("Server is already deactivated", ErrorType.Conflict);
                }

                // Soft delete
                server.IsActive = false;
                server.UpdatedAt = DateTime.UtcNow;

                _context.Servers.Update(server);
                await _context.SaveChangesAsync();

                _logger.LogInformation("Server deactivated successfully: {ServerId}", id);

                // Return response
                var responseData = new
                {
                    id = server.Id,
                    serverName = server.ServerName,
                    deactivatedAt = server.UpdatedAt
                };

                return Result<dynamic>.Success(responseData);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error deactivating server");
                return Result<dynamic>.Failure("An unexpected error occurred while deactivating the server", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> GetServersAsync() // Keep for backward compatibility
        {
            return await GetServersAsync(1, 10);
        }
    }
}