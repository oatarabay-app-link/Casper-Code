using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace CasperVPN.Models;

/// <summary>
/// User entity representing VPN service users
/// </summary>
public class User
{
    [Key]
    public Guid Id { get; set; } = Guid.NewGuid();

    [Required]
    [MaxLength(100)]
    public string Email { get; set; } = string.Empty;

    [Required]
    [MaxLength(256)]
    public string PasswordHash { get; set; } = string.Empty;

    [MaxLength(100)]
    public string FirstName { get; set; } = string.Empty;

    [MaxLength(100)]
    public string LastName { get; set; } = string.Empty;

    public bool IsEmailVerified { get; set; } = false;

    public bool IsActive { get; set; } = true;

    public UserRole Role { get; set; } = UserRole.User;

    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    public DateTime UpdatedAt { get; set; } = DateTime.UtcNow;

    public DateTime? LastLoginAt { get; set; }

    [MaxLength(100)]
    public string? StripeCustomerId { get; set; }

    [MaxLength(256)]
    public string? PasswordResetToken { get; set; }

    public DateTime? PasswordResetTokenExpiry { get; set; }

    [MaxLength(256)]
    public string? EmailVerificationToken { get; set; }

    public DateTime? EmailVerificationTokenExpiry { get; set; }

    [MaxLength(256)]
    public string? RefreshToken { get; set; }

    public DateTime? RefreshTokenExpiry { get; set; }

    // Navigation properties
    public virtual Subscription? Subscription { get; set; }
    public virtual ICollection<ConnectionLog> ConnectionLogs { get; set; } = new List<ConnectionLog>();
    public virtual ICollection<Payment> Payments { get; set; } = new List<Payment>();

    // RADIUS credentials (for VPN authentication)
    [MaxLength(100)]
    public string? RadiusUsername { get; set; }

    [MaxLength(256)]
    public string? RadiusPassword { get; set; }

    public long DataUsedBytes { get; set; } = 0;

    public long DataLimitBytes { get; set; } = 0; // 0 = unlimited
}

public enum UserRole
{
    User = 0,
    Premium = 1,
    Admin = 2,
    SuperAdmin = 3
}
