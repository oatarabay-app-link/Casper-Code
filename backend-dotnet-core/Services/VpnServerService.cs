using Microsoft.EntityFrameworkCore;
using CasperVPN.Data;
using CasperVPN.DTOs;
using CasperVPN.Models;

namespace CasperVPN.Services;

/// <summary>
/// VPN Server service implementation
/// </summary>
public class VpnServerService : IVpnServerService
{
    private readonly ApplicationDbContext _context;
    private readonly ILogger<VpnServerService> _logger;
    private readonly IFreeRadiusService _radiusService;

    public VpnServerService(
        ApplicationDbContext context,
        ILogger<VpnServerService> logger,
        IFreeRadiusService radiusService)
    {
        _context = context;
        _logger = logger;
        _radiusService = radiusService;
    }

    public async Task<List<VpnServerDto>> GetServersAsync(Guid? userId = null)
    {
        int userAccessLevel = 1;

        if (userId.HasValue)
        {
            var user = await _context.Users
                .Include(u => u.Subscription)
                .ThenInclude(s => s!.Plan)
                .FirstOrDefaultAsync(u => u.Id == userId.Value);

            if (user?.Subscription?.Plan != null)
            {
                userAccessLevel = user.Subscription.Plan.ServerAccessLevel;
            }
        }

        var servers = await _context.VpnServers
            .Where(s => s.IsActive && s.ServerAccessLevel <= userAccessLevel)
            .OrderBy(s => s.Country)
            .ThenBy(s => s.City)
            .ToListAsync();

        return servers.Select(s => MapToVpnServerDto(s)).ToList();
    }

    public async Task<VpnServerDto?> GetServerByIdAsync(Guid serverId)
    {
        var server = await _context.VpnServers.FindAsync(serverId);
        if (server == null || !server.IsActive) return null;

        return MapToVpnServerDto(server);
    }

    public async Task<VpnServerDto?> GetRecommendedServerAsync(Guid userId, RecommendationRequest? request = null)
    {
        var user = await _context.Users
            .Include(u => u.Subscription)
            .ThenInclude(s => s!.Plan)
            .FirstOrDefaultAsync(u => u.Id == userId);

        int userAccessLevel = user?.Subscription?.Plan?.ServerAccessLevel ?? 1;

        var query = _context.VpnServers
            .Where(s => s.IsActive && s.Status == ServerStatus.Online && s.ServerAccessLevel <= userAccessLevel);

        // Filter by preferred country
        if (!string.IsNullOrWhiteSpace(request?.PreferredCountry))
        {
            query = query.Where(s => s.Country.ToLower() == request.PreferredCountry.ToLower() ||
                                     s.CountryCode.ToLower() == request.PreferredCountry.ToLower());
        }

        var servers = await query.ToListAsync();

        if (!servers.Any())
        {
            return null;
        }

        VpnServer? recommended;

        // If user provided location, calculate distance
        if (request?.UserLatitude.HasValue == true && request?.UserLongitude.HasValue == true)
        {
            var serversWithDistance = servers.Select(s => new
            {
                Server = s,
                Distance = CalculateDistance(request.UserLatitude.Value, request.UserLongitude.Value, s.Latitude, s.Longitude),
                Score = CalculateScore(s, request.PreferLowLoad)
            });

            recommended = serversWithDistance
                .OrderByDescending(x => x.Score)
                .ThenBy(x => x.Distance)
                .First()
                .Server;
        }
        else
        {
            // Recommend based on load and ping
            recommended = servers
                .OrderBy(s => s.Load)
                .ThenBy(s => s.Ping)
                .First();
        }

        return MapToVpnServerDto(recommended);
    }

    public async Task<VpnConfigResponse> GetServerConfigAsync(Guid serverId, Guid userId)
    {
        var server = await _context.VpnServers.FindAsync(serverId);
        if (server == null || !server.IsActive)
        {
            throw new InvalidOperationException("Server not found");
        }

        var user = await _context.Users
            .Include(u => u.Subscription)
            .ThenInclude(s => s!.Plan)
            .FirstOrDefaultAsync(u => u.Id == userId);

        if (user == null)
        {
            throw new InvalidOperationException("User not found");
        }

        // Check user access level
        int userAccessLevel = user.Subscription?.Plan?.ServerAccessLevel ?? 1;
        if (server.ServerAccessLevel > userAccessLevel)
        {
            throw new UnauthorizedAccessException("You don't have access to this server. Please upgrade your plan.");
        }

        // Generate WireGuard config
        var clientPrivateKey = GenerateWireGuardPrivateKey();
        var clientPublicKey = GenerateWireGuardPublicKey(clientPrivateKey);
        var clientAddress = $"10.66.66.{new Random().Next(2, 254)}/32";

        var config = GenerateWireGuardConfig(server, clientPrivateKey, clientAddress);

        return new VpnConfigResponse
        {
            Config = config,
            Protocol = server.Protocol,
            ServerHostname = server.Hostname,
            ServerPort = server.Port,
            PublicKey = server.PublicKey,
            ClientPrivateKey = clientPrivateKey,
            ClientAddress = clientAddress,
            DnsServers = new[] { "1.1.1.1", "1.0.0.1" },
            KeepAlive = 25
        };
    }

    public async Task<ConnectionLogDto> ConnectToServerAsync(Guid serverId, Guid userId, ConnectRequest request, string? clientIp)
    {
        var server = await _context.VpnServers.FindAsync(serverId);
        if (server == null || !server.IsActive)
        {
            throw new InvalidOperationException("Server not found");
        }

        var user = await _context.Users
            .Include(u => u.Subscription)
            .ThenInclude(s => s!.Plan)
            .FirstOrDefaultAsync(u => u.Id == userId);

        if (user == null)
        {
            throw new InvalidOperationException("User not found");
        }

        // Check data limit
        if (user.DataLimitBytes > 0 && user.DataUsedBytes >= user.DataLimitBytes)
        {
            throw new InvalidOperationException("Data limit exceeded. Please upgrade your plan.");
        }

        // Close any existing active connections for this user
        var activeConnections = await _context.ConnectionLogs
            .Where(c => c.UserId == userId && c.Status == ConnectionStatus.Active)
            .ToListAsync();

        foreach (var conn in activeConnections)
        {
            conn.Status = ConnectionStatus.Disconnected;
            conn.DisconnectedAt = DateTime.UtcNow;
            conn.DisconnectReason = "New connection started";
        }

        // Create connection log
        var connectionLog = new ConnectionLog
        {
            UserId = userId,
            ServerId = serverId,
            ConnectedAt = DateTime.UtcNow,
            ClientIp = clientIp,
            AssignedIp = $"10.66.66.{new Random().Next(2, 254)}",
            DeviceType = request.DeviceType ?? "Unknown",
            DeviceOs = request.DeviceOs ?? "Unknown",
            Status = ConnectionStatus.Active,
            Protocol = request.PreferredProtocol ?? server.Protocol
        };

        _context.ConnectionLogs.Add(connectionLog);

        // Update server connection count
        server.CurrentConnections++;
        server.Load = Math.Min(100, (int)((double)server.CurrentConnections / server.MaxConnections * 100));

        await _context.SaveChangesAsync();

        _logger.LogInformation("User {UserId} connected to server {ServerId}", userId, serverId);

        return new ConnectionLogDto
        {
            Id = connectionLog.Id,
            ServerId = serverId,
            ServerName = server.Name,
            ServerCountry = server.Country,
            ConnectedAt = connectionLog.ConnectedAt,
            ClientIp = connectionLog.ClientIp,
            DeviceType = connectionLog.DeviceType,
            Status = connectionLog.Status,
            Protocol = connectionLog.Protocol
        };
    }

    public async Task<ConnectionLogDto> DisconnectFromServerAsync(Guid serverId, Guid userId, DisconnectRequest request)
    {
        var connectionLog = await _context.ConnectionLogs
            .Include(c => c.Server)
            .FirstOrDefaultAsync(c => c.UserId == userId && c.ServerId == serverId && c.Status == ConnectionStatus.Active);

        if (connectionLog == null)
        {
            throw new InvalidOperationException("No active connection found");
        }

        connectionLog.DisconnectedAt = DateTime.UtcNow;
        connectionLog.BytesUploaded = request.BytesUploaded;
        connectionLog.BytesDownloaded = request.BytesDownloaded;
        connectionLog.DisconnectReason = request.DisconnectReason;
        connectionLog.Status = ConnectionStatus.Disconnected;

        // Update user data usage
        var user = await _context.Users.FindAsync(userId);
        if (user != null)
        {
            user.DataUsedBytes += request.BytesUploaded + request.BytesDownloaded;
        }

        // Update server connection count
        var server = await _context.VpnServers.FindAsync(serverId);
        if (server != null)
        {
            server.CurrentConnections = Math.Max(0, server.CurrentConnections - 1);
            server.Load = Math.Min(100, (int)((double)server.CurrentConnections / server.MaxConnections * 100));
        }

        await _context.SaveChangesAsync();

        _logger.LogInformation("User {UserId} disconnected from server {ServerId}", userId, serverId);

        return new ConnectionLogDto
        {
            Id = connectionLog.Id,
            ServerId = serverId,
            ServerName = connectionLog.Server?.Name ?? string.Empty,
            ServerCountry = connectionLog.Server?.Country ?? string.Empty,
            ConnectedAt = connectionLog.ConnectedAt,
            DisconnectedAt = connectionLog.DisconnectedAt,
            BytesUploaded = connectionLog.BytesUploaded,
            BytesDownloaded = connectionLog.BytesDownloaded,
            ClientIp = connectionLog.ClientIp,
            DeviceType = connectionLog.DeviceType,
            Status = connectionLog.Status,
            Protocol = connectionLog.Protocol
        };
    }

    public async Task<List<VpnServerDetailsDto>> GetAllServersAsync()
    {
        var servers = await _context.VpnServers
            .OrderBy(s => s.Country)
            .ThenBy(s => s.City)
            .ToListAsync();

        return servers.Select(s => new VpnServerDetailsDto
        {
            Id = s.Id,
            Name = s.Name,
            Hostname = s.Hostname,
            IpAddress = s.IpAddress,
            Country = s.Country,
            City = s.City,
            CountryCode = s.CountryCode,
            Latitude = s.Latitude,
            Longitude = s.Longitude,
            Port = s.Port,
            Protocol = s.Protocol,
            Status = s.Status,
            Load = s.Load,
            IsPremium = s.IsPremium,
            Ping = s.Ping,
            MaxConnections = s.MaxConnections,
            CurrentConnections = s.CurrentConnections,
            ServerAccessLevel = s.ServerAccessLevel,
            IsActive = s.IsActive,
            BandwidthBps = s.BandwidthBps,
            PublicKey = s.PublicKey,
            CreatedAt = s.CreatedAt,
            UpdatedAt = s.UpdatedAt,
            LastHealthCheck = s.LastHealthCheck
        }).ToList();
    }

    public async Task<VpnServerDetailsDto> CreateServerAsync(CreateServerRequest request)
    {
        var server = new VpnServer
        {
            Name = request.Name,
            Hostname = request.Hostname,
            IpAddress = request.IpAddress,
            Country = request.Country,
            City = request.City,
            CountryCode = request.CountryCode,
            Latitude = request.Latitude,
            Longitude = request.Longitude,
            Port = request.Port,
            Protocol = request.Protocol,
            MaxConnections = request.MaxConnections,
            ServerAccessLevel = request.ServerAccessLevel,
            IsPremium = request.IsPremium,
            PublicKey = request.PublicKey ?? GenerateWireGuardPublicKey(GenerateWireGuardPrivateKey()),
            ConfigTemplate = request.ConfigTemplate,
            Status = ServerStatus.Online,
            IsActive = true
        };

        _context.VpnServers.Add(server);
        await _context.SaveChangesAsync();

        _logger.LogInformation("Server created: {ServerId}", server.Id);

        return MapToVpnServerDetailsDto(server);
    }

    public async Task<VpnServerDetailsDto> UpdateServerAsync(Guid serverId, UpdateServerRequest request)
    {
        var server = await _context.VpnServers.FindAsync(serverId);
        if (server == null)
        {
            throw new InvalidOperationException("Server not found");
        }

        if (!string.IsNullOrWhiteSpace(request.Name))
            server.Name = request.Name;

        if (!string.IsNullOrWhiteSpace(request.Hostname))
            server.Hostname = request.Hostname;

        if (!string.IsNullOrWhiteSpace(request.IpAddress))
            server.IpAddress = request.IpAddress;

        if (!string.IsNullOrWhiteSpace(request.Country))
            server.Country = request.Country;

        if (!string.IsNullOrWhiteSpace(request.City))
            server.City = request.City;

        if (!string.IsNullOrWhiteSpace(request.CountryCode))
            server.CountryCode = request.CountryCode;

        if (request.Latitude.HasValue)
            server.Latitude = request.Latitude.Value;

        if (request.Longitude.HasValue)
            server.Longitude = request.Longitude.Value;

        if (request.Port.HasValue)
            server.Port = request.Port.Value;

        if (request.Protocol.HasValue)
            server.Protocol = request.Protocol.Value;

        if (request.Status.HasValue)
            server.Status = request.Status.Value;

        if (request.MaxConnections.HasValue)
            server.MaxConnections = request.MaxConnections.Value;

        if (request.ServerAccessLevel.HasValue)
            server.ServerAccessLevel = request.ServerAccessLevel.Value;

        if (request.IsPremium.HasValue)
            server.IsPremium = request.IsPremium.Value;

        if (request.IsActive.HasValue)
            server.IsActive = request.IsActive.Value;

        if (!string.IsNullOrWhiteSpace(request.PublicKey))
            server.PublicKey = request.PublicKey;

        if (!string.IsNullOrWhiteSpace(request.ConfigTemplate))
            server.ConfigTemplate = request.ConfigTemplate;

        server.UpdatedAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        _logger.LogInformation("Server updated: {ServerId}", serverId);

        return MapToVpnServerDetailsDto(server);
    }

    public async Task<bool> DeleteServerAsync(Guid serverId)
    {
        var server = await _context.VpnServers.FindAsync(serverId);
        if (server == null)
        {
            throw new InvalidOperationException("Server not found");
        }

        // Soft delete
        server.IsActive = false;
        server.Status = ServerStatus.Offline;
        server.UpdatedAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        _logger.LogInformation("Server deleted: {ServerId}", serverId);

        return true;
    }

    public async Task UpdateServerHealthAsync(Guid serverId, int load, int currentConnections, bool isOnline)
    {
        var server = await _context.VpnServers.FindAsync(serverId);
        if (server == null) return;

        server.Load = load;
        server.CurrentConnections = currentConnections;
        server.Status = isOnline ? ServerStatus.Online : ServerStatus.Offline;
        server.LastHealthCheck = DateTime.UtcNow;
        await _context.SaveChangesAsync();
    }

    private static VpnServerDto MapToVpnServerDto(VpnServer server)
    {
        return new VpnServerDto
        {
            Id = server.Id,
            Name = server.Name,
            Country = server.Country,
            City = server.City,
            CountryCode = server.CountryCode,
            Latitude = server.Latitude,
            Longitude = server.Longitude,
            Protocol = server.Protocol,
            Status = server.Status,
            Load = server.Load,
            IsPremium = server.IsPremium,
            Ping = server.Ping,
            FlagUrl = $"/flags/{server.CountryCode.ToLower()}.png"
        };
    }

    private static VpnServerDetailsDto MapToVpnServerDetailsDto(VpnServer server)
    {
        return new VpnServerDetailsDto
        {
            Id = server.Id,
            Name = server.Name,
            Hostname = server.Hostname,
            IpAddress = server.IpAddress,
            Country = server.Country,
            City = server.City,
            CountryCode = server.CountryCode,
            Latitude = server.Latitude,
            Longitude = server.Longitude,
            Port = server.Port,
            Protocol = server.Protocol,
            Status = server.Status,
            Load = server.Load,
            IsPremium = server.IsPremium,
            Ping = server.Ping,
            MaxConnections = server.MaxConnections,
            CurrentConnections = server.CurrentConnections,
            ServerAccessLevel = server.ServerAccessLevel,
            IsActive = server.IsActive,
            BandwidthBps = server.BandwidthBps,
            PublicKey = server.PublicKey,
            CreatedAt = server.CreatedAt,
            UpdatedAt = server.UpdatedAt,
            LastHealthCheck = server.LastHealthCheck
        };
    }

    private static double CalculateDistance(double lat1, double lon1, double lat2, double lon2)
    {
        const double R = 6371; // Earth's radius in kilometers
        var dLat = ToRad(lat2 - lat1);
        var dLon = ToRad(lon2 - lon1);
        var a = Math.Sin(dLat / 2) * Math.Sin(dLat / 2) +
                Math.Cos(ToRad(lat1)) * Math.Cos(ToRad(lat2)) *
                Math.Sin(dLon / 2) * Math.Sin(dLon / 2);
        var c = 2 * Math.Atan2(Math.Sqrt(a), Math.Sqrt(1 - a));
        return R * c;
    }

    private static double ToRad(double deg) => deg * Math.PI / 180;

    private static double CalculateScore(VpnServer server, bool preferLowLoad)
    {
        double score = 100;
        
        // Penalize based on load
        if (preferLowLoad)
        {
            score -= server.Load * 0.5;
        }

        // Penalize based on ping
        score -= server.Ping * 0.1;

        // Bonus for premium servers
        if (server.IsPremium)
        {
            score += 10;
        }

        return score;
    }

    private static string GenerateWireGuardPrivateKey()
    {
        // In production, use proper WireGuard key generation
        var bytes = new byte[32];
        using var rng = System.Security.Cryptography.RandomNumberGenerator.Create();
        rng.GetBytes(bytes);
        return Convert.ToBase64String(bytes);
    }

    private static string GenerateWireGuardPublicKey(string privateKey)
    {
        // In production, derive public key from private key using Curve25519
        var bytes = new byte[32];
        using var rng = System.Security.Cryptography.RandomNumberGenerator.Create();
        rng.GetBytes(bytes);
        return Convert.ToBase64String(bytes);
    }

    private static string GenerateWireGuardConfig(VpnServer server, string clientPrivateKey, string clientAddress)
    {
        return $@"[Interface]
PrivateKey = {clientPrivateKey}
Address = {clientAddress}
DNS = 1.1.1.1, 1.0.0.1

[Peer]
PublicKey = {server.PublicKey}
AllowedIPs = 0.0.0.0/0, ::/0
Endpoint = {server.Hostname}:{server.Port}
PersistentKeepalive = 25
";
    }
}
