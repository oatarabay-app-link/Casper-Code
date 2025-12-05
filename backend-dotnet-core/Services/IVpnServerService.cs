using CasperVPN.DTOs;

namespace CasperVPN.Services;

/// <summary>
/// VPN Server service interface
/// </summary>
public interface IVpnServerService
{
    Task<List<VpnServerDto>> GetServersAsync(Guid? userId = null);
    Task<VpnServerDto?> GetServerByIdAsync(Guid serverId);
    Task<VpnServerDto?> GetRecommendedServerAsync(Guid userId, RecommendationRequest? request = null);
    Task<VpnConfigResponse> GetServerConfigAsync(Guid serverId, Guid userId);
    Task<ConnectionLogDto> ConnectToServerAsync(Guid serverId, Guid userId, ConnectRequest request, string? clientIp);
    Task<ConnectionLogDto> DisconnectFromServerAsync(Guid serverId, Guid userId, DisconnectRequest request);
    
    // Admin operations
    Task<List<VpnServerDetailsDto>> GetAllServersAsync();
    Task<VpnServerDetailsDto> CreateServerAsync(CreateServerRequest request);
    Task<VpnServerDetailsDto> UpdateServerAsync(Guid serverId, UpdateServerRequest request);
    Task<bool> DeleteServerAsync(Guid serverId);
    Task UpdateServerHealthAsync(Guid serverId, int load, int currentConnections, bool isOnline);
}
