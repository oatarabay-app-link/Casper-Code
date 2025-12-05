using Microsoft.EntityFrameworkCore;
using CasperVPN.Data;
using CasperVPN.DTOs;
using CasperVPN.Models;

namespace CasperVPN.Services;

/// <summary>
/// User service implementation
/// </summary>
public class UserService : IUserService
{
    private readonly ApplicationDbContext _context;
    private readonly ILogger<UserService> _logger;
    private readonly IFreeRadiusService _radiusService;
    private readonly IStripeService _stripeService;

    public UserService(
        ApplicationDbContext context,
        ILogger<UserService> logger,
        IFreeRadiusService radiusService,
        IStripeService stripeService)
    {
        _context = context;
        _logger = logger;
        _radiusService = radiusService;
        _stripeService = stripeService;
    }

    public async Task<UserDto?> GetUserByIdAsync(Guid userId)
    {
        var user = await _context.Users
            .Include(u => u.Subscription)
            .ThenInclude(s => s!.Plan)
            .FirstOrDefaultAsync(u => u.Id == userId);

        if (user == null) return null;

        return MapToUserDto(user);
    }

    public async Task<UserDetailsDto?> GetUserDetailsAsync(Guid userId)
    {
        var user = await _context.Users
            .Include(u => u.Subscription)
            .ThenInclude(s => s!.Plan)
            .Include(u => u.ConnectionLogs.OrderByDescending(c => c.ConnectedAt).Take(10))
            .ThenInclude(c => c.Server)
            .Include(u => u.Payments.OrderByDescending(p => p.CreatedAt).Take(10))
            .FirstOrDefaultAsync(u => u.Id == userId);

        if (user == null) return null;

        return new UserDetailsDto
        {
            Id = user.Id,
            Email = user.Email,
            FirstName = user.FirstName,
            LastName = user.LastName,
            IsEmailVerified = user.IsEmailVerified,
            IsActive = user.IsActive,
            Role = user.Role,
            CreatedAt = user.CreatedAt,
            LastLoginAt = user.LastLoginAt,
            DataUsedBytes = user.DataUsedBytes,
            DataLimitBytes = user.DataLimitBytes,
            StripeCustomerId = user.StripeCustomerId,
            RadiusUsername = user.RadiusUsername,
            Subscription = user.Subscription != null ? MapToSubscriptionDto(user.Subscription) : null,
            RecentConnections = user.ConnectionLogs.Select(c => new ConnectionLogDto
            {
                Id = c.Id,
                ServerId = c.ServerId,
                ServerName = c.Server?.Name ?? string.Empty,
                ServerCountry = c.Server?.Country ?? string.Empty,
                ConnectedAt = c.ConnectedAt,
                DisconnectedAt = c.DisconnectedAt,
                BytesUploaded = c.BytesUploaded,
                BytesDownloaded = c.BytesDownloaded,
                ClientIp = c.ClientIp,
                DeviceType = c.DeviceType,
                Status = c.Status,
                Protocol = c.Protocol
            }).ToList(),
            RecentPayments = user.Payments.Select(p => new PaymentDto
            {
                Id = p.Id,
                Amount = p.Amount,
                Currency = p.Currency,
                Status = p.Status,
                Method = p.Method,
                Description = p.Description,
                PaidAt = p.PaidAt,
                ReceiptUrl = p.ReceiptUrl,
                CreatedAt = p.CreatedAt
            }).ToList()
        };
    }

    public async Task<UserDto> UpdateUserProfileAsync(Guid userId, UpdateProfileRequest request)
    {
        var user = await _context.Users
            .Include(u => u.Subscription)
            .ThenInclude(s => s!.Plan)
            .FirstOrDefaultAsync(u => u.Id == userId);

        if (user == null)
        {
            throw new InvalidOperationException("User not found");
        }

        if (!string.IsNullOrWhiteSpace(request.FirstName))
            user.FirstName = request.FirstName;

        if (!string.IsNullOrWhiteSpace(request.LastName))
            user.LastName = request.LastName;

        if (!string.IsNullOrWhiteSpace(request.Email) && request.Email != user.Email)
        {
            // Check if new email is already taken
            var existingUser = await _context.Users
                .FirstOrDefaultAsync(u => u.Email.ToLower() == request.Email.ToLower() && u.Id != userId);
            if (existingUser != null)
            {
                throw new InvalidOperationException("Email is already in use");
            }
            user.Email = request.Email.ToLower();
            user.IsEmailVerified = false; // Require re-verification
        }

        user.UpdatedAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        _logger.LogInformation("User profile updated: {UserId}", userId);

        return MapToUserDto(user);
    }

    public async Task<bool> DeleteUserAsync(Guid userId)
    {
        var user = await _context.Users.FindAsync(userId);
        if (user == null)
        {
            throw new InvalidOperationException("User not found");
        }

        // Cancel Stripe subscription if exists
        if (!string.IsNullOrEmpty(user.StripeCustomerId))
        {
            try
            {
                await _stripeService.CancelAllSubscriptionsAsync(user.StripeCustomerId);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Failed to cancel Stripe subscriptions for user {UserId}", userId);
            }
        }

        // Delete RADIUS user
        if (!string.IsNullOrEmpty(user.RadiusUsername))
        {
            try
            {
                await _radiusService.DeleteUserAsync(user.RadiusUsername);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Failed to delete RADIUS user for {UserId}", userId);
            }
        }

        // Soft delete - just deactivate
        user.IsActive = false;
        user.Email = $"deleted_{user.Id}@deleted.local";
        user.RefreshToken = null;
        user.UpdatedAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        _logger.LogInformation("User account deleted: {UserId}", userId);
        return true;
    }

    public async Task<PaginatedResult<UserListItemDto>> GetUsersAsync(PaginationParams pagination)
    {
        var query = _context.Users
            .Include(u => u.Subscription)
            .ThenInclude(s => s!.Plan)
            .AsQueryable();

        // Search
        if (!string.IsNullOrWhiteSpace(pagination.Search))
        {
            var searchLower = pagination.Search.ToLower();
            query = query.Where(u =>
                u.Email.ToLower().Contains(searchLower) ||
                u.FirstName.ToLower().Contains(searchLower) ||
                u.LastName.ToLower().Contains(searchLower));
        }

        // Sorting
        query = pagination.SortBy?.ToLower() switch
        {
            "email" => pagination.SortDescending ? query.OrderByDescending(u => u.Email) : query.OrderBy(u => u.Email),
            "createdat" => pagination.SortDescending ? query.OrderByDescending(u => u.CreatedAt) : query.OrderBy(u => u.CreatedAt),
            "lastloginat" => pagination.SortDescending ? query.OrderByDescending(u => u.LastLoginAt) : query.OrderBy(u => u.LastLoginAt),
            _ => query.OrderByDescending(u => u.CreatedAt)
        };

        var totalCount = await query.CountAsync();

        var users = await query
            .Skip((pagination.Page - 1) * pagination.PageSize)
            .Take(pagination.PageSize)
            .ToListAsync();

        return new PaginatedResult<UserListItemDto>
        {
            Items = users.Select(u => new UserListItemDto
            {
                Id = u.Id,
                Email = u.Email,
                FirstName = u.FirstName,
                LastName = u.LastName,
                IsActive = u.IsActive,
                IsEmailVerified = u.IsEmailVerified,
                Role = u.Role,
                CreatedAt = u.CreatedAt,
                LastLoginAt = u.LastLoginAt,
                SubscriptionStatus = u.Subscription?.Status.ToString(),
                PlanName = u.Subscription?.Plan?.Name
            }).ToList(),
            TotalCount = totalCount,
            Page = pagination.Page,
            PageSize = pagination.PageSize
        };
    }

    public async Task<UserDto> AdminUpdateUserAsync(Guid userId, AdminUpdateUserRequest request)
    {
        var user = await _context.Users
            .Include(u => u.Subscription)
            .ThenInclude(s => s!.Plan)
            .FirstOrDefaultAsync(u => u.Id == userId);

        if (user == null)
        {
            throw new InvalidOperationException("User not found");
        }

        if (!string.IsNullOrWhiteSpace(request.FirstName))
            user.FirstName = request.FirstName;

        if (!string.IsNullOrWhiteSpace(request.LastName))
            user.LastName = request.LastName;

        if (!string.IsNullOrWhiteSpace(request.Email) && request.Email != user.Email)
        {
            var existingUser = await _context.Users
                .FirstOrDefaultAsync(u => u.Email.ToLower() == request.Email.ToLower() && u.Id != userId);
            if (existingUser != null)
            {
                throw new InvalidOperationException("Email is already in use");
            }
            user.Email = request.Email.ToLower();
        }

        if (request.IsActive.HasValue)
            user.IsActive = request.IsActive.Value;

        if (request.IsEmailVerified.HasValue)
            user.IsEmailVerified = request.IsEmailVerified.Value;

        if (request.Role.HasValue)
            user.Role = request.Role.Value;

        if (request.DataLimitBytes.HasValue)
            user.DataLimitBytes = request.DataLimitBytes.Value;

        user.UpdatedAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        _logger.LogInformation("Admin updated user: {UserId}", userId);

        return MapToUserDto(user);
    }

    public async Task<bool> AdminDeleteUserAsync(Guid userId)
    {
        var user = await _context.Users.FindAsync(userId);
        if (user == null)
        {
            throw new InvalidOperationException("User not found");
        }

        // Cancel Stripe subscription if exists
        if (!string.IsNullOrEmpty(user.StripeCustomerId))
        {
            try
            {
                await _stripeService.CancelAllSubscriptionsAsync(user.StripeCustomerId);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Failed to cancel Stripe subscriptions for user {UserId}", userId);
            }
        }

        // Delete RADIUS user
        if (!string.IsNullOrEmpty(user.RadiusUsername))
        {
            try
            {
                await _radiusService.DeleteUserAsync(user.RadiusUsername);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Failed to delete RADIUS user for {UserId}", userId);
            }
        }

        _context.Users.Remove(user);
        await _context.SaveChangesAsync();

        _logger.LogInformation("Admin deleted user: {UserId}", userId);
        return true;
    }

    private static UserDto MapToUserDto(User user)
    {
        return new UserDto
        {
            Id = user.Id,
            Email = user.Email,
            FirstName = user.FirstName,
            LastName = user.LastName,
            IsEmailVerified = user.IsEmailVerified,
            IsActive = user.IsActive,
            Role = user.Role,
            CreatedAt = user.CreatedAt,
            LastLoginAt = user.LastLoginAt,
            DataUsedBytes = user.DataUsedBytes,
            DataLimitBytes = user.DataLimitBytes,
            Subscription = user.Subscription != null ? MapToSubscriptionDto(user.Subscription) : null
        };
    }

    private static SubscriptionDto MapToSubscriptionDto(Subscription sub)
    {
        return new SubscriptionDto
        {
            Id = sub.Id,
            PlanId = sub.PlanId,
            PlanName = sub.Plan?.Name ?? string.Empty,
            PlanType = sub.Plan?.Type ?? PlanType.Free,
            Status = sub.Status,
            StartDate = sub.StartDate,
            EndDate = sub.EndDate,
            CurrentPeriodStart = sub.CurrentPeriodStart,
            CurrentPeriodEnd = sub.CurrentPeriodEnd,
            CancelAtPeriodEnd = sub.CancelAtPeriodEnd
        };
    }
}
