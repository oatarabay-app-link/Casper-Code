using System.ComponentModel.DataAnnotations;
using CasperVPN.Models;

namespace CasperVPN.DTOs;

/// <summary>
/// VPN Server DTO for client
/// </summary>
public class VpnServerDto
{
    public Guid Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Country { get; set; } = string.Empty;
    public string City { get; set; } = string.Empty;
    public string CountryCode { get; set; } = string.Empty;
    public double Latitude { get; set; }
    public double Longitude { get; set; }
    public VpnProtocol Protocol { get; set; }
    public ServerStatus Status { get; set; }
    public int Load { get; set; }
    public bool IsPremium { get; set; }
    public int Ping { get; set; }
    public string? FlagUrl { get; set; }
}

/// <summary>
/// VPN Server details for admin
/// </summary>
public class VpnServerDetailsDto : VpnServerDto
{
    public string Hostname { get; set; } = string.Empty;
    public string IpAddress { get; set; } = string.Empty;
    public int Port { get; set; }
    public int MaxConnections { get; set; }
    public int CurrentConnections { get; set; }
    public int ServerAccessLevel { get; set; }
    public bool IsActive { get; set; }
    public long BandwidthBps { get; set; }
    public string? PublicKey { get; set; }
    public DateTime CreatedAt { get; set; }
    public DateTime UpdatedAt { get; set; }
    public DateTime? LastHealthCheck { get; set; }
}

/// <summary>
/// VPN configuration response
/// </summary>
public class VpnConfigResponse
{
    public string Config { get; set; } = string.Empty;
    public VpnProtocol Protocol { get; set; }
    public string ServerHostname { get; set; } = string.Empty;
    public int ServerPort { get; set; }
    public string? PublicKey { get; set; }
    public string? ClientPrivateKey { get; set; }
    public string? ClientAddress { get; set; }
    public string[] DnsServers { get; set; } = Array.Empty<string>();
    public int KeepAlive { get; set; } = 25;
}

/// <summary>
/// Create server request (Admin)
/// </summary>
public class CreateServerRequest
{
    [Required]
    [MaxLength(100)]
    public string Name { get; set; } = string.Empty;

    [Required]
    [MaxLength(100)]
    public string Hostname { get; set; } = string.Empty;

    [Required]
    [MaxLength(45)]
    public string IpAddress { get; set; } = string.Empty;

    [Required]
    [MaxLength(100)]
    public string Country { get; set; } = string.Empty;

    [MaxLength(100)]
    public string City { get; set; } = string.Empty;

    [Required]
    [MaxLength(10)]
    public string CountryCode { get; set; } = string.Empty;

    public double Latitude { get; set; }

    public double Longitude { get; set; }

    [Range(1, 65535)]
    public int Port { get; set; } = 51820;

    public VpnProtocol Protocol { get; set; } = VpnProtocol.WireGuard;

    [Range(1, 100000)]
    public int MaxConnections { get; set; } = 1000;

    [Range(1, 3)]
    public int ServerAccessLevel { get; set; } = 1;

    public bool IsPremium { get; set; } = false;

    [MaxLength(256)]
    public string? PublicKey { get; set; }

    [MaxLength(500)]
    public string? ConfigTemplate { get; set; }
}

/// <summary>
/// Update server request (Admin)
/// </summary>
public class UpdateServerRequest
{
    [MaxLength(100)]
    public string? Name { get; set; }

    [MaxLength(100)]
    public string? Hostname { get; set; }

    [MaxLength(45)]
    public string? IpAddress { get; set; }

    [MaxLength(100)]
    public string? Country { get; set; }

    [MaxLength(100)]
    public string? City { get; set; }

    [MaxLength(10)]
    public string? CountryCode { get; set; }

    public double? Latitude { get; set; }

    public double? Longitude { get; set; }

    [Range(1, 65535)]
    public int? Port { get; set; }

    public VpnProtocol? Protocol { get; set; }

    public ServerStatus? Status { get; set; }

    [Range(1, 100000)]
    public int? MaxConnections { get; set; }

    [Range(1, 3)]
    public int? ServerAccessLevel { get; set; }

    public bool? IsPremium { get; set; }

    public bool? IsActive { get; set; }

    [MaxLength(256)]
    public string? PublicKey { get; set; }

    [MaxLength(500)]
    public string? ConfigTemplate { get; set; }
}

/// <summary>
/// Connection request
/// </summary>
public class ConnectRequest
{
    [MaxLength(100)]
    public string? DeviceType { get; set; }

    [MaxLength(100)]
    public string? DeviceOs { get; set; }

    public VpnProtocol? PreferredProtocol { get; set; }
}

/// <summary>
/// Disconnect request
/// </summary>
public class DisconnectRequest
{
    public long BytesUploaded { get; set; } = 0;

    public long BytesDownloaded { get; set; } = 0;

    [MaxLength(500)]
    public string? DisconnectReason { get; set; }
}

/// <summary>
/// Connection log DTO
/// </summary>
public class ConnectionLogDto
{
    public Guid Id { get; set; }
    public Guid ServerId { get; set; }
    public string ServerName { get; set; } = string.Empty;
    public string ServerCountry { get; set; } = string.Empty;
    public DateTime ConnectedAt { get; set; }
    public DateTime? DisconnectedAt { get; set; }
    public long BytesUploaded { get; set; }
    public long BytesDownloaded { get; set; }
    public string? ClientIp { get; set; }
    public string? DeviceType { get; set; }
    public ConnectionStatus Status { get; set; }
    public VpnProtocol Protocol { get; set; }
    public TimeSpan? Duration => DisconnectedAt.HasValue ? DisconnectedAt.Value - ConnectedAt : null;
}

/// <summary>
/// Server recommendation request
/// </summary>
public class RecommendationRequest
{
    public double? UserLatitude { get; set; }
    public double? UserLongitude { get; set; }
    public bool PreferLowLoad { get; set; } = true;
    public string? PreferredCountry { get; set; }
}
