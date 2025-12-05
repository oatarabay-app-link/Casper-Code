using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace CasperVPN.Models;

/// <summary>
/// Connection log entity for tracking VPN connections
/// </summary>
public class ConnectionLog
{
    [Key]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    public Guid UserId { get; set; }

    [Required]
    public Guid ServerId { get; set; }

    public DateTime ConnectedAt { get; set; } = DateTime.UtcNow;

    public DateTime? DisconnectedAt { get; set; }

    public long BytesUploaded { get; set; } = 0;

    public long BytesDownloaded { get; set; } = 0;

    [MaxLength(45)]
    public string? ClientIp { get; set; }

    [MaxLength(45)]
    public string? AssignedIp { get; set; }

    [MaxLength(100)]
    public string? DeviceType { get; set; }

    [MaxLength(100)]
    public string? DeviceOs { get; set; }

    [MaxLength(256)]
    public string? UserAgent { get; set; }

    public ConnectionStatus Status { get; set; } = ConnectionStatus.Active;

    [MaxLength(500)]
    public string? DisconnectReason { get; set; }

    public VpnProtocol Protocol { get; set; } = VpnProtocol.WireGuard;

    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    // Navigation properties
    [ForeignKey("UserId")]
    public virtual User User { get; set; } = null!;

    [ForeignKey("ServerId")]
    public virtual VpnServer Server { get; set; } = null!;
}

public enum ConnectionStatus
{
    Active = 0,
    Disconnected = 1,
    Failed = 2,
    Timeout = 3
}
