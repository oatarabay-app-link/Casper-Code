using System.ComponentModel.DataAnnotations;

namespace CasperVPN.Models;

/// <summary>
/// VPN Server entity
/// </summary>
public class VpnServer
{
    [Key]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    [MaxLength(100)]
    public string Name { get; set; } = string.Empty;

    [Required]
    [MaxLength(100)]
    public string Hostname { get; set; } = string.Empty;

    [Required]
    [MaxLength(45)]
    public string IpAddress { get; set; } = string.Empty;

    [MaxLength(100)]
    public string Country { get; set; } = string.Empty;

    [MaxLength(100)]
    public string City { get; set; } = string.Empty;

    [MaxLength(10)]
    public string CountryCode { get; set; } = string.Empty;

    public double Latitude { get; set; }

    public double Longitude { get; set; }

    public int Port { get; set; } = 51820; // WireGuard default port

    public VpnProtocol Protocol { get; set; } = VpnProtocol.WireGuard;

    public ServerStatus Status { get; set; } = ServerStatus.Online;

    public int Load { get; set; } = 0; // 0-100 percentage

    public int MaxConnections { get; set; } = 1000;

    public int CurrentConnections { get; set; } = 0;

    public int ServerAccessLevel { get; set; } = 1; // 1 = basic, 2 = premium, 3 = enterprise

    public bool IsActive { get; set; } = true;

    public bool IsPremium { get; set; } = false;

    public int Ping { get; set; } = 0; // Average ping in ms

    public long BandwidthBps { get; set; } = 0; // Bandwidth in bytes per second

    [MaxLength(256)]
    public string? PublicKey { get; set; } // For WireGuard

    [MaxLength(500)]
    public string? ConfigTemplate { get; set; }

    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;

    public DateTime? LastHealthCheck { get; set; }

    // Navigation properties
    public virtual ICollection<ConnectionLog> ConnectionLogs { get; set; } = new List<ConnectionLog>();
}

public enum VpnProtocol
{
    WireGuard = 0,
    OpenVPN = 1,
    IKEv2 = 2
}

public enum ServerStatus
{
    Online = 0,
    Offline = 1,
    Maintenance = 2,
    Overloaded = 3
}
