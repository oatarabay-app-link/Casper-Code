using System.ComponentModel.DataAnnotations;
using CasperVPN.Models;

namespace CasperVPN.DTOs;

/// <summary>
/// User data transfer object
/// </summary>
public class UserDto
{
    public Guid Id { get; set; }
    public string Email { get; set; } = string.Empty;
    public string FirstName { get; set; } = string.Empty;
    public string LastName { get; set; } = string.Empty;
    public bool IsEmailVerified { get; set; }
    public bool IsActive { get; set; }
    public UserRole Role { get; set; }
    public DateTime CreatedAt { get; set; }
    public DateTime? LastLoginAt { get; set; }
    public SubscriptionDto? Subscription { get; set; }
    public long DataUsedBytes { get; set; }
    public long DataLimitBytes { get; set; }
}

/// <summary>
/// User profile update request
/// </summary>
public class UpdateProfileRequest
{
    [MaxLength(100)]
    public string? FirstName { get; set; }

    [MaxLength(100)]
    public string? LastName { get; set; }

    [EmailAddress]
    [MaxLength(100)]
    public string? Email { get; set; }
}

/// <summary>
/// Admin user update request
/// </summary>
public class AdminUpdateUserRequest
{
    [MaxLength(100)]
    public string? FirstName { get; set; }

    [MaxLength(100)]
    public string? LastName { get; set; }

    [EmailAddress]
    [MaxLength(100)]
    public string? Email { get; set; }

    public bool? IsActive { get; set; }

    public bool? IsEmailVerified { get; set; }

    public UserRole? Role { get; set; }

    public long? DataLimitBytes { get; set; }
}

/// <summary>
/// User list item for admin panel
/// </summary>
public class UserListItemDto
{
    public Guid Id { get; set; }
    public string Email { get; set; } = string.Empty;
    public string FirstName { get; set; } = string.Empty;
    public string LastName { get; set; } = string.Empty;
    public bool IsActive { get; set; }
    public bool IsEmailVerified { get; set; }
    public UserRole Role { get; set; }
    public DateTime CreatedAt { get; set; }
    public DateTime? LastLoginAt { get; set; }
    public string? SubscriptionStatus { get; set; }
    public string? PlanName { get; set; }
}

/// <summary>
/// User details for admin panel
/// </summary>
public class UserDetailsDto : UserDto
{
    public string? StripeCustomerId { get; set; }
    public string? RadiusUsername { get; set; }
    public List<ConnectionLogDto> RecentConnections { get; set; } = new();
    public List<PaymentDto> RecentPayments { get; set; } = new();
}

/// <summary>
/// Paginated result wrapper
/// </summary>
public class PaginatedResult<T>
{
    public List<T> Items { get; set; } = new();
    public int TotalCount { get; set; }
    public int Page { get; set; }
    public int PageSize { get; set; }
    public int TotalPages => (int)Math.Ceiling((double)TotalCount / PageSize);
    public bool HasNextPage => Page < TotalPages;
    public bool HasPreviousPage => Page > 1;
}
