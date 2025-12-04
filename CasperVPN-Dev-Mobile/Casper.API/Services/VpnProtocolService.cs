using Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Services.Interfaces;
using Microsoft.EntityFrameworkCore;

namespace Casper.API.Services
{
    public class VpnProtocolService : IVpnProtocolService
    {
        private readonly CasperVpnDbContext _context;
        private readonly ILogger<VpnProtocolService> _logger;

        public VpnProtocolService(
            CasperVpnDbContext context,
            ILogger<VpnProtocolService> logger)
        {
            _context = context;
            _logger = logger;
        }

        public async Task<Result<List<object>>> GetAllProtocolsAsync()
        {
            try
            {
                _logger.LogInformation("Retrieving all VPN protocols");

                // Test database connection first
                try
                {
                    var canConnect = await _context.Database.CanConnectAsync();
                    if (!canConnect)
                    {
                        _logger.LogError("Cannot connect to database");
                        return Result<List<object>>.Failure("Database connection failed", ErrorType.InternalError);
                    }
                }
                catch (Exception dbEx)
                {
                    _logger.LogError(dbEx, "Database connection failed");
                    return Result<List<object>>.Failure($"Database connection failed: {dbEx.Message}", ErrorType.InternalError);
                }

                // Try without IsActive filter first
                var protocols = await _context.VpnProtocols
                     .Where(p => p.IsActive) // Comment out to test
                    .OrderBy(p => p.Protocol)
                    .Select(p => new
                    {
                        Id = p.Id,
                        Protocol = p.Protocol,

                        IsActive = p.IsActive,
                    })
                    .ToListAsync();

                var protocolObjects = protocols.Cast<object>().ToList();

                _logger.LogInformation("Successfully retrieved {Count} VPN protocols", protocolObjects.Count);
                return Result<List<object>>.Success(protocolObjects);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving VPN protocols: {ErrorMessage}", ex.ToString()); // Use ToString() for full details
                return Result<List<object>>.Failure($"An error occurred while retri eving VPN protocols: {ex.Message}", ErrorType.InternalError);
            }
        }

        public async Task<Result<VpnProtocol>> CreateProtocolAsync(CreateVpnProtocolRequest request)
        {
            try
            {
                _logger.LogInformation("Creating new VPN protocol: {ProtocolName}", request.Protocol);

                if (string.IsNullOrWhiteSpace(request.Protocol))
                    return Result<VpnProtocol>.Failure("Protocol name is required", ErrorType.Validation);

                // Trim and validate protocol name
                request.Protocol    = request.Protocol.Trim();
                if (request.Protocol.Length < 2)
                    return Result<VpnProtocol>.Failure("Protocol name must be at least 2 characters", ErrorType.Validation);

                // Check if protocol already exists (case-insensitive)
                var existingProtocol = await _context.VpnProtocols
                    .FirstOrDefaultAsync(p => p.Protocol.ToLower() == request.Protocol.ToLower());

                if (existingProtocol != null)
                {
                    _logger.LogWarning("VPN protocol {ProtocolName} already exists", request.Protocol);
                    return Result<VpnProtocol>.Conflict("VPN protocol already exists");
                }

                var protocol = new VpnProtocol
                {
                    Protocol = request.Protocol,
                   IsActive = request.IsActive, // Remove temporarily
                    CreatedAt = DateTime.UtcNow,
                };

                _context.VpnProtocols.Add(protocol);
                await _context.SaveChangesAsync();

                _logger.LogInformation("Successfully created VPN protocol: {ProtocolName} with ID: {ProtocolId}",
                    protocol.Protocol, protocol.Id);
                return Result<VpnProtocol>.Success(protocol);
            }
            catch (DbUpdateException dbEx)
            {
                _logger.LogError(dbEx, "Database error creating VPN protocol: {ProtocolName}. Inner: {InnerException}",
                    request.Protocol, dbEx.InnerException?.Message);
                return Result<VpnProtocol>.Failure($"Database error: {dbEx.InnerException?.Message ?? dbEx.Message}", ErrorType.InternalError);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Unexpected error creating VPN protocol: {ProtocolName}", request.Protocol);
                return Result<VpnProtocol>.Failure($"An error occurred: {ex.Message}", ErrorType.InternalError);
            }
        }
        public async Task<Result<object>> GetProtocolByIdAsync(string id)
        {
            try
            {
                _logger.LogInformation("Retrieving VPN protocol with ID: {ProtocolId}", id);

                var protocol = await _context.VpnProtocols
                    .Where(p => p.Id.ToString() == id && p.IsActive)
                    .Select(p => new
                    {
                        Id = p.Id,
                        Protocol = p.Protocol,
                        
                        IsActive=p.IsActive,
                    })
                    .FirstOrDefaultAsync();

                if (protocol == null)
                {
                    _logger.LogWarning("VPN protocol with ID {ProtocolId} not found", id);
                    return Result<object>.NotFound("VPN protocol not found");
                }

                _logger.LogInformation("Successfully retrieved VPN protocol: {ProtocolName}", protocol.Protocol);
                return Result<object>.Success(protocol);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving VPN protocol with ID: {ProtocolId}", id);
                return Result<object>.Failure("An error occurred while retrieving VPN protocol", ErrorType.InternalError);
            }
        }

      
        public async Task<Result<VpnProtocol>> UpdateProtocolAsync(UpdateVpnProtocolRequest request)
        {
            try
            {
                _logger.LogInformation("Updating VPN protocol with ID: {ProtocolId}", request.Id);

                if (string.IsNullOrEmpty(request.Id))
                    return Result<VpnProtocol>.Failure("ProtocolName ID is required", ErrorType.Validation);

                var protocol = await _context.VpnProtocols
                    .FirstOrDefaultAsync(p => p.Id.ToString() == request.Id);

                if (protocol == null)
                {
                    _logger.LogWarning("VPN protocol with ID {ProtocolId} not found", request.Id);
                    return Result<VpnProtocol>.NotFound("VPN protocol not found");
                }

                // Check for duplicate protocol name if protocol name is being updated
                if (!string.IsNullOrEmpty(request.Protocol) && request.Protocol != protocol.Protocol)
                {
                    var duplicateProtocol = await _context.VpnProtocols
                        .FirstOrDefaultAsync(p => p.Protocol == request.Protocol  && p.Id.ToString() != request.Id);

                    if (duplicateProtocol != null)
                    {
                        _logger.LogWarning("VPN protocol {ProtocolName} already exists", request.Protocol);
                        return Result<VpnProtocol>.Conflict("VPN protocol with this name already exists");
                    }
                }

                // Update fields if provided
                if (!string.IsNullOrEmpty(request.Protocol))
                    protocol.Protocol = request.Protocol;
               


                protocol.UpdatedAt = DateTime.UtcNow;



                await _context.SaveChangesAsync();

                _logger.LogInformation("Successfully updated VPN protocol: {ProtocolName}", protocol.Protocol);
                return Result<VpnProtocol>.Success(protocol);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating VPN protocol with ID: {ProtocolId}", request.Id);
                return Result<VpnProtocol>.Failure("An error occurred while updating VPN protocol", ErrorType.InternalError);
            }
        }

       
        public async Task<Result> DeleteProtocolAsync(string id)
        {
            try
            {
                _logger.LogInformation("Deleting VPN protocol with ID: {ProtocolId}", id);

                var protocol = await _context.VpnProtocols
                    .FirstOrDefaultAsync(p => p.Id.ToString() == id);

                if (protocol == null)
                {
                    _logger.LogWarning("VPN protocol with ID {ProtocolId} not found", id);
                    return Result.NotFound("VPN protocol not found");
                }

                protocol.UpdatedAt = DateTime.UtcNow;
                protocol.IsActive = false;

                await _context.SaveChangesAsync();

                _logger.LogInformation("Successfully deleted VPN protocol: {ProtocolName}", protocol.Protocol);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error deleting VPN protocol with ID: {ProtocolId}", id);
                return Result.Failure("An error occurred while deleting VPN protocol", ErrorType.InternalError);
            }
        }

   }
}
