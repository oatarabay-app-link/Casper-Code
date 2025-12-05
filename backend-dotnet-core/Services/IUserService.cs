using CasperVPN.DTOs;
using CasperVPN.Models;

namespace CasperVPN.Services;

/// <summary>
/// User service interface
/// </summary>
public interface IUserService
{
    Task<UserDto?> GetUserByIdAsync(Guid userId);
    Task<UserDetailsDto?> GetUserDetailsAsync(Guid userId);
    Task<UserDto> UpdateUserProfileAsync(Guid userId, UpdateProfileRequest request);
    Task<bool> DeleteUserAsync(Guid userId);
    Task<PaginatedResult<UserListItemDto>> GetUsersAsync(PaginationParams pagination);
    Task<UserDto> AdminUpdateUserAsync(Guid userId, AdminUpdateUserRequest request);
    Task<bool> AdminDeleteUserAsync(Guid userId);
}
