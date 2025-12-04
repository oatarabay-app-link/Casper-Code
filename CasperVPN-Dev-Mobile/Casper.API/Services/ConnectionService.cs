using Casper.API.Models;
using Casper.API.Models.Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Services.Interfaces;
using Microsoft.EntityFrameworkCore;
using static Casper.API.Models.Enums;

namespace Casper.API.Services
{
    public class ConnectionService : IConnectionService
    {
        private readonly CasperVpnDbContext _context;
        private readonly ILogger<ConnectionService> _logger;

        public ConnectionService(
            CasperVpnDbContext context,
            ILogger<ConnectionService> logger)
        {
            _context = context;
            _logger = logger;
        }

        public async Task<Result<dynamic>> GetConnectionsWithSummary()
        {
            try
            {
                _logger.LogInformation("Retrieving connections with summary");

                var today = DateTime.UtcNow.Date;

                // 1) Fetch today's connections into memory
                var todayConnections = await _context.Connections
                    .Where(c => c.UserConnectTime >= today && c.IsActive)
                    .ToListAsync();

                // 2) Compute summary in memory (safe for empty list)
                var summary = new
                {
                    ActiveConnections = todayConnections.Count(c => c.ConnectionStatus == Enums.ConnectionStatus.Connected),
                    TotalUpload = todayConnections.Sum(c => c.Upload),
                    TotalDownload = todayConnections.Sum(c => c.Download),
                    AverageSessionSeconds = todayConnections.Any()
                        ? todayConnections.Average(c => (DateTime.UtcNow - c.UserConnectTime).TotalSeconds)
                        : 0,
                    TotalConnections = todayConnections.Count
                };

                // 3) Take latest 100 connections
                var latestConnections = todayConnections
                    .OrderByDescending(c => c.UserConnectTime)
                    .Take(100)
                    .ToList();

                if (!latestConnections.Any())
                {
                    return Result<dynamic>.Success(new
                    {
                        Connections = new List<dynamic>(),
                        Summary = summary
                    });
                }

                // 4) Extract related IDs and convert to proper types
                var userIds = latestConnections.Select(c => c.UserId).Distinct().ToList();

                // Convert string ServerIds to Guid
                var serverIds = latestConnections
                    .Where(c => Guid.TryParse(c.ServerId, out _))
                    .Select(c => Guid.Parse(c.ServerId))
                    .Distinct()
                    .ToList();

                // Convert string ProtocolIds to Guid  
                var protocolIds = latestConnections
                    .Where(c => Guid.TryParse(c.ProtocolId, out _))
                    .Select(c => Guid.Parse(c.ProtocolId))
                    .Distinct()
                    .ToList();

                // 5) Fetch related data
                var users = await _context.Users
                    .Where(u => userIds.Contains(u.Id))
                    .ToDictionaryAsync(u => u.Id, u => u.Email);

                var servers = await _context.Servers
                    .Where(s => serverIds.Contains(s.Id))
                    .ToDictionaryAsync(s => s.Id, s => new { s.ServerName, s.Location });

                var protocols = await _context.VpnProtocols
                    .Where(p => protocolIds.Contains(p.Id))
                    .ToDictionaryAsync(p => p.Id, p => p.Protocol);

                // 6) Build final connection objects
                var connections = latestConnections.Select(c => new
                {
                    Id = c.Id,
                    UserEmail = users.TryGetValue(c.UserId, out var email) ? email : "Unknown",
                    Server = Guid.TryParse(c.ServerId, out var serverGuid) && servers.TryGetValue(serverGuid, out var server)
                        ? server.ServerName : "Unknown",
                    Location = Guid.TryParse(c.ServerId, out var locGuid) && servers.TryGetValue(locGuid, out var loc)
                        ? loc.Location : "Unknown",
                    Protocol = Guid.TryParse(c.ProtocolId, out var protoGuid) && protocols.TryGetValue(protoGuid, out var proto)
                        ? proto : "Unknown",
                    SessionSeconds = (DateTime.UtcNow - c.UserConnectTime).TotalSeconds,
                    Upload = c.Upload,
                    Download = c.Download,
                    Status = c.ConnectionStatus.ToString()
                }).ToList();

                // 7) Return final result
                return Result<dynamic>.Success(new
                {
                    Connections = connections,
                    Summary = summary
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving connections with summary: {Message}", ex.Message);
                return Result<dynamic>.Failure(ex.ToString(), ErrorType.InternalError);
            }
        }







        public async Task<Result<dynamic>> GetConnectionsByUserIdAsync(string userId)
        {
            try
            {
                _logger.LogInformation("Retrieving connections for user ID: {UserId}", userId);

                // 1) Get user's connections
                var connectionsData = await _context.Connections
                    .Where(c => c.UserId == userId && c.IsActive)
                    .OrderByDescending(c => c.UserConnectTime)
                    .Take(100) // optional limit
                    .ToListAsync();

                if (!connectionsData.Any())
                {
                    return Result<dynamic>.Success(new
                    {
                        Connections = new List<dynamic>()
                    });
                }

                // 2) Extract related IDs and convert to proper types
                var serverIds = connectionsData
                    .Where(c => Guid.TryParse(c.ServerId, out _))
                    .Select(c => Guid.Parse(c.ServerId))
                    .Distinct()
                    .ToList();

                var protocolIds = connectionsData
                    .Where(c => Guid.TryParse(c.ProtocolId, out _))
                    .Select(c => Guid.Parse(c.ProtocolId))
                    .Distinct()
                    .ToList();

                // 3) Fetch related data sequentially
                var user = await _context.Users
                    .Where(u => u.Id == userId)
                    .Select(u => new { u.Id, u.Email })
                    .FirstOrDefaultAsync();

                var servers = await _context.Servers
                    .Where(s => serverIds.Contains(s.Id))
                    .ToDictionaryAsync(s => s.Id, s => new { s.ServerName, s.Location });

                var protocols = await _context.VpnProtocols
                    .Where(p => protocolIds.Contains(p.Id))
                    .ToDictionaryAsync(p => p.Id, p => p.Protocol);

                // 4) Build final enriched connections list
                var connections = connectionsData.Select(c => new
                {
                    Id = c.Id,
                    Email = user?.Email ?? "Unknown",
                    Server = Guid.TryParse(c.ServerId, out var serverGuid) && servers.TryGetValue(serverGuid, out var server)
                        ? server.ServerName : "Unknown",
                    Location = Guid.TryParse(c.ServerId, out var locGuid) && servers.TryGetValue(locGuid, out var server2)
                        ? server2.Location : "Unknown",
                    Protocol = Guid.TryParse(c.ProtocolId, out var protoGuid) && protocols.TryGetValue(protoGuid, out var proto)
                        ? proto : "Unknown",
                    SessionSeconds = (DateTime.UtcNow - c.UserConnectTime).TotalSeconds,
                    Upload = c.Upload,
                    Download = c.Download,
                    Status = c.ConnectionStatus.ToString()
                }).ToList();

                return Result<dynamic>.Success(new
                {
                    Connections = connections
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving connections for user ID: {UserId}", userId);
                return Result<dynamic>.Failure("An error occurred while retrieving user connections", ErrorType.InternalError);
            }
        }



        public async Task<Result<dynamic>> GetConnectionsByServerIdAsync(string serverId)
        {
            try
            {
                _logger.LogInformation("Retrieving connections for server ID: {ServerId}", serverId);

                // Parse serverId to Guid for query
                if (!Guid.TryParse(serverId, out Guid serverGuid))
                {
                    return Result<dynamic>.Failure("Invalid server ID format", ErrorType.Validation);
                }

                // 1) Get server connections - fixed query
                var connectionsData = await _context.Connections
                    .Where(c => c.ServerId == serverId && c.IsActive) // ServerId is string, so direct comparison works
                    .OrderByDescending(c => c.UserConnectTime)
                    .Take(100)
                    .ToListAsync();

                if (!connectionsData.Any())
                {
                    return Result<dynamic>.Success(new
                    {
                        Connections = new List<dynamic>()
                    });
                }

                // 2) Extract related IDs and convert to proper types
                var userIds = connectionsData.Select(c => c.UserId).Distinct().ToList();

                var protocolIds = connectionsData
                    .Where(c => Guid.TryParse(c.ProtocolId, out _))
                    .Select(c => Guid.Parse(c.ProtocolId))
                    .Distinct()
                    .ToList();

                // 3) Fetch related data sequentially
                var users = await _context.Users
                    .Where(u => userIds.Contains(u.Id))
                    .ToDictionaryAsync(u => u.Id, u => u.Email);

                var protocols = await _context.VpnProtocols
                    .Where(p => protocolIds.Contains(p.Id))
                    .ToDictionaryAsync(p => p.Id, p => p.Protocol);

                // 4) Fetch server info
                var server = await _context.Servers
                    .Where(s => s.Id == serverGuid)
                    .Select(s => new { s.ServerName, s.Location })
                    .FirstOrDefaultAsync();

                // 5) Build final enriched connections list
                var connections = connectionsData.Select(c => new
                {
                    Id = c.Id,
                    Email = users.TryGetValue(c.UserId, out var email) ? email : "Unknown",
                    Server = server?.ServerName ?? "Unknown",
                    Location = server?.Location ?? "Unknown",
                    Protocol = Guid.TryParse(c.ProtocolId, out var protoGuid) && protocols.TryGetValue(protoGuid, out var proto)
                        ? proto : "Unknown",
                    SessionSeconds = (DateTime.UtcNow - c.UserConnectTime).TotalSeconds,
                    Upload = c.Upload,
                    Download = c.Download,
                    Status = c.ConnectionStatus.ToString()
                }).ToList();

                return Result<dynamic>.Success(new
                {
                    Connections = connections
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving connections for server ID: {ServerId}", serverId);
                return Result<dynamic>.Failure("An error occurred while retrieving server connections", ErrorType.InternalError);
            }
        }


        public async Task<Result<Connection>> CreateConnectionAsync(CreateConnectionRequest request)
        {
            try
            {
                _logger.LogInformation("Creating new connection for user: {UserId}", request.UserId);

                // Comprehensive request validation
                var validationErrors = new List<string>();

                if (string.IsNullOrWhiteSpace(request.UserId))
                    validationErrors.Add("User ID is required");

                if (string.IsNullOrWhiteSpace(request.ServerId))
                    validationErrors.Add("Server ID is required");

                if (string.IsNullOrWhiteSpace(request.ProtocolId))
                    validationErrors.Add("Protocol ID is required");

                if (string.IsNullOrWhiteSpace(request.Location))
                    validationErrors.Add("Location is required");
                else if (request.Location.Length > 100)
                    validationErrors.Add("Location cannot exceed 100 characters");

                if (validationErrors.Any())
                {
                    _logger.LogWarning("Validation failed for connection creation: {Errors}", string.Join("; ", validationErrors));
                    return Result<Connection>.ValidationFailure(validationErrors);
                }

                // Validate user exists
                var userExists = await _context.Users
                    .AnyAsync(u => u.Id == request.UserId && u.IsActive);

                if (!userExists)
                {
                    _logger.LogWarning("User with ID {UserId} not found or inactive", request.UserId);
                    return Result<Connection>.NotFound($"User with ID '{request.UserId}' not found or inactive");
                }

                // Validate server exists and is active
                if (!Guid.TryParse(request.ServerId, out Guid serverGuid))
                {
                    _logger.LogWarning("Invalid server ID format: {ServerId}", request.ServerId);
                    return Result<Connection>.Failure("Invalid server ID format", ErrorType.Validation);
                }

                var serverExists = await _context.Servers
                    .AnyAsync(s => s.Id == serverGuid && s.IsActive);

                if (!serverExists)
                {
                    _logger.LogWarning("Server with ID {ServerId} not found or inactive", request.ServerId);
                    return Result<Connection>.NotFound($"Server with ID '{request.ServerId}' not found or inactive");
                }

                // Validate protocol exists and is active
                if (!Guid.TryParse(request.ProtocolId, out Guid protocolGuid))
                {
                    _logger.LogWarning("Invalid protocol ID format: {ProtocolId}", request.ProtocolId);
                    return Result<Connection>.Failure("Invalid protocol ID format", ErrorType.Validation);
                }

                var protocolExists = await _context.VpnProtocols
                    .AnyAsync(p => p.Id == protocolGuid && p.IsActive);

                if (!protocolExists)
                {
                    _logger.LogWarning("VPN protocol with ID {ProtocolId} not found or inactive", request.ProtocolId);
                    return Result<Connection>.NotFound($"VPN protocol with ID '{request.ProtocolId}' not found or inactive");
                }

                // Check if user already has an active connection (optional business rule)
                var existingActiveConnection = await _context.Connections
                    .FirstOrDefaultAsync(c => c.UserId == request.UserId &&
                                             c.ConnectionStatus == ConnectionStatus.Connected);

                if (existingActiveConnection != null)
                {
                    _logger.LogWarning("User {UserId} already has an active connection: {ConnectionId}",
                        request.UserId, existingActiveConnection.Id);
                    return Result<Connection>.Conflict($"User already has an active connection. Please disconnect first.");
                }

                // Create connection
                var connection = new Connection
                {
                    UserId = request.UserId,
                    ServerId = request.ServerId,
                    Location = request.Location.Trim(),
                    UserConnectTime = DateTime.UtcNow,
                    Upload = 0,
                    Download = 0,
                    ConnectionStatus = ConnectionStatus.Connected,
                    ProtocolId = request.ProtocolId,
                    IsActive = true,
                    CreatedAt = DateTime.UtcNow
                };

                _context.Connections.Add(connection);
                await _context.SaveChangesAsync();

                _logger.LogInformation("Successfully created connection with ID: {ConnectionId} for user: {UserId} to server: {ServerId}",
                    connection.Id, connection.UserId, connection.ServerId);

                return Result<Connection>.Success(connection);
            }
            catch (DbUpdateException dbEx)
            {
                _logger.LogError(dbEx, "Database error creating connection for user: {UserId}. Inner: {InnerException}",
                    request.UserId, dbEx.InnerException?.Message);

                // Handle specific database errors
                if (dbEx.InnerException?.Message.Contains("foreign key constraint") == true)
                {
                    return Result<Connection>.Failure("Referenced user, server, or protocol does not exist", ErrorType.Validation);
                }

                return Result<Connection>.Failure("A database error occurred while creating the connection", ErrorType.InternalError);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Unexpected error creating connection for user: {UserId}. Error: {ErrorMessage}",
                    request.UserId, ex.Message);
                return Result<Connection>.Failure($"An unexpected error occurred: {ex.Message}", ErrorType.InternalError);
            }
        }
        public async Task<Result<Connection>> UpdateConnectionAsync(UpdateConnectionRequest request)
        {
            try
            {
                _logger.LogInformation("Updating connection with ID: {ConnectionId}", request.Id);

                // Comprehensive request validation
                var validationErrors = new List<string>();

                if (string.IsNullOrWhiteSpace(request.Id))
                    validationErrors.Add("Connection ID is required");

                // Check if at least one field is being updated
                bool hasUpdates = !string.IsNullOrWhiteSpace(request.UserId) ||
                                 !string.IsNullOrWhiteSpace(request.ServerId) ||
                                 !string.IsNullOrWhiteSpace(request.Location) ||
                                 !string.IsNullOrWhiteSpace(request.ProtocolId) ||
                                 request.Upload.HasValue ||
                                 request.Download.HasValue ||
                                 request.ConnectionStatus.HasValue;

                if (!hasUpdates)
                    validationErrors.Add("At least one field must be provided for update");

                // Validate individual fields if provided
                if (!string.IsNullOrWhiteSpace(request.Location) && request.Location.Length > 100)
                    validationErrors.Add("Location cannot exceed 100 characters");

                if (request.Upload.HasValue && request.Upload.Value < 0)
                    validationErrors.Add("Upload value cannot be negative");

                if (request.Download.HasValue && request.Download.Value < 0)
                    validationErrors.Add("Download value cannot be negative");

                if (validationErrors.Any())
                {
                    _logger.LogWarning("Validation failed for connection update: {Errors}", string.Join("; ", validationErrors));
                    return Result<Connection>.ValidationFailure(validationErrors);
                }

                // Parse connection ID
                if (!Guid.TryParse(request.Id, out Guid connectionId))
                {
                    _logger.LogWarning("Invalid connection ID format: {ConnectionId}", request.Id);
                    return Result<Connection>.Failure("Invalid connection ID format", ErrorType.Validation);
                }

                // Find connection
                var connection = await _context.Connections
                    .FirstOrDefaultAsync(c => c.Id == connectionId && c.IsActive);

                if (connection == null)
                {
                    _logger.LogWarning("Connection with ID {ConnectionId} not found or inactive", request.Id);
                    return Result<Connection>.NotFound($"Connection with ID '{request.Id}' not found or inactive");
                }

                bool hasActualChanges = false;

                // Validate and update UserId if provided
                if (!string.IsNullOrWhiteSpace(request.UserId))
                {
                    var userExists = await _context.Users
                        .AnyAsync(u => u.Id == request.UserId && u.IsActive);

                    if (!userExists)
                    {
                        _logger.LogWarning("User with ID {UserId} not found or inactive", request.UserId);
                        return Result<Connection>.NotFound($"User with ID '{request.UserId}' not found or inactive");
                    }

                    if (request.UserId != connection.UserId)
                    {
                        connection.UserId = request.UserId.Trim();
                        hasActualChanges = true;
                    }
                }

                // Validate and update ServerId if provided
                if (!string.IsNullOrWhiteSpace(request.ServerId))
                {
                    if (!Guid.TryParse(request.ServerId, out Guid serverGuid))
                    {
                        _logger.LogWarning("Invalid server ID format: {ServerId}", request.ServerId);
                        return Result<Connection>.Failure("Invalid server ID format", ErrorType.Validation);
                    }

                    var serverExists = await _context.Servers
                        .AnyAsync(s => s.Id == serverGuid && s.IsActive);

                    if (!serverExists)
                    {
                        _logger.LogWarning("Server with ID {ServerId} not found or inactive", request.ServerId);
                        return Result<Connection>.NotFound($"Server with ID '{request.ServerId}' not found or inactive");
                    }

                    if (request.ServerId != connection.ServerId)
                    {
                        connection.ServerId = request.ServerId;
                        hasActualChanges = true;
                    }
                }

                // Validate and update ProtocolId if provided
                if (!string.IsNullOrWhiteSpace(request.ProtocolId))
                {
                    if (!Guid.TryParse(request.ProtocolId, out Guid protocolGuid))
                    {
                        _logger.LogWarning("Invalid protocol ID format: {ProtocolId}", request.ProtocolId);
                        return Result<Connection>.Failure("Invalid protocol ID format", ErrorType.Validation);
                    }

                    var protocolExists = await _context.VpnProtocols
                        .AnyAsync(p => p.Id == protocolGuid && p.IsActive);

                    if (!protocolExists)
                    {
                        _logger.LogWarning("VPN protocol with ID {ProtocolId} not found or inactive", request.ProtocolId);
                        return Result<Connection>.NotFound($"VPN protocol with ID '{request.ProtocolId}' not found or inactive");
                    }

                    if (request.ProtocolId != connection.ProtocolId)
                    {
                        connection.ProtocolId = request.ProtocolId;
                        hasActualChanges = true;
                    }
                }

                // Update Location if provided and different
                if (!string.IsNullOrWhiteSpace(request.Location) &&
                    request.Location.Trim() != connection.Location)
                {
                    connection.Location = request.Location.Trim();
                    hasActualChanges = true;
                }

                // Update Upload if provided and valid
                if (request.Upload.HasValue && request.Upload.Value >= 0 &&
                    request.Upload.Value != connection.Upload)
                {
                    connection.Upload = request.Upload.Value;
                    hasActualChanges = true;
                }

                // Update Download if provided and valid
                if (request.Download.HasValue && request.Download.Value >= 0 &&
                    request.Download.Value != connection.Download)
                {
                    connection.Download = request.Download.Value;
                    hasActualChanges = true;
                }

                // Update ConnectionStatus if provided and different
                if (request.ConnectionStatus.HasValue &&
                    request.ConnectionStatus.Value != connection.ConnectionStatus)
                {
                    connection.ConnectionStatus = request.ConnectionStatus.Value;
                    hasActualChanges = true;
                }

                // Check if any actual changes were made
                if (!hasActualChanges)
                {
                    _logger.LogInformation("No changes detected for connection ID: {ConnectionId}", request.Id);
                    return Result<Connection>.Success(connection); // Removed second parameter
                }

                connection.UpdatedAt = DateTime.UtcNow;

                await _context.SaveChangesAsync();

                _logger.LogInformation("Successfully updated connection: {ConnectionId}", connection.Id);
                return Result<Connection>.Success(connection); // Removed second parameter
            }
            catch (DbUpdateException dbEx)
            {
                _logger.LogError(dbEx, "Database error updating connection with ID: {ConnectionId}. Inner: {InnerException}",
                    request.Id, dbEx.InnerException?.Message);

                if (dbEx.InnerException?.Message.Contains("foreign key constraint") == true)
                {
                    return Result<Connection>.Failure("Referenced user, server, or protocol does not exist", ErrorType.Validation);
                }

                return Result<Connection>.Failure("A database error occurred while updating the connection", ErrorType.InternalError);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Unexpected error updating connection with ID: {ConnectionId}. Error: {ErrorMessage}",
                    request.Id, ex.Message);
                return Result<Connection>.Failure($"An unexpected error occurred: {ex.Message}", ErrorType.InternalError);
            }
        }
        public async Task<Result> DeleteConnectionAsync(string id)
        {
            try
            {
                _logger.LogInformation("Soft deleting connection with ID: {ConnectionId}", id);

                // Parse string ID to Guid
                if (!Guid.TryParse(id, out Guid connectionId))
                {
                    return Result.Failure("Invalid connection ID format", ErrorType.Validation);
                }

                var connection = await _context.Connections
                    .FirstOrDefaultAsync(c => c.Id == connectionId);

                if (connection == null)
                {
                    _logger.LogWarning("Connection with ID {ConnectionId} not found", id);
                    return Result.NotFound("Connection not found");
                }

                // Soft delete by setting IsActive = false
                connection.IsActive = false;
                connection.UpdatedAt = DateTime.UtcNow;

                await _context.SaveChangesAsync();

                _logger.LogInformation("Successfully soft deleted connection: {ConnectionId}", id);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error soft deleting connection with ID: {ConnectionId}", id);
                return Result.Failure("An error occurred while deleting connection", ErrorType.InternalError);
            }
        }


        public async Task<Result> UpdateConnectionStatusAsync(string id, ConnectionStatus status)
        {
            try
            {
                _logger.LogInformation("Updating connection status to {Status} for connection: {ConnectionId}", status, id);
                var connection = await _context.Connections
                          .FirstOrDefaultAsync(c => c.Id.ToString() == id);


                if (connection == null)
                {
                    _logger.LogWarning("Connection with ID {ConnectionId} not found", id);
                    return Result.NotFound("Connection not found");
                }

                connection.ConnectionStatus = status;

                await _context.SaveChangesAsync();

                _logger.LogInformation("Successfully updated connection status to {Status} for connection: {ConnectionId}", status, id);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating connection status for connection: {ConnectionId}", id);
                return Result.Failure("An error occurred while updating connection status", ErrorType.InternalError);
            }
        }

        public async Task<Result> UpdateConnectionStatsAsync(string id, double upload, double download)
        {
            try
            {
                _logger.LogInformation("Updating connection stats for connection: {ConnectionId}", id);

                var connection = await _context.Connections
              .FirstOrDefaultAsync(c => c.Id.ToString() == id);


                if (connection == null)
                {
                    _logger.LogWarning("Connection with ID {ConnectionId} not found", id);
                    return Result.NotFound("Connection not found");
                }

                connection.Upload = upload;
                connection.Download = download;

                await _context.SaveChangesAsync();

                _logger.LogInformation("Successfully updated connection stats for connection: {ConnectionId}", id);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating connection stats for connection: {ConnectionId}", id);
                return Result.Failure("An error occurred while updating connection stats", ErrorType.InternalError);
            }
        }
    }


}
