// Interfaces/IRoleService.cs
using Casper.API.Models;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Requests.RoleRequest;
using Casper.API.Models.Responses;
using Microsoft.AspNetCore.Identity;

namespace Casper.API.Services.Interfaces
{
    public interface IRoleService
    {
        // Role Management Methods
        Task<Result<RoleCreateResponse>> CreateRoleAsync(string roleName);
        Task<Result<RoleWithDetails>> CreateRoleWithPermissionsAsync(CreateRoleRequest request);
        Task<Result<List<IdentityRole>>> GetAllRolesAsync();
        Task<Result<List<RoleWithDetails>>> GetAllRolesWithDetailsAsync();
        Task<Result<RoleSummaryResponse>> GetRoleSummaryAsync();
        Task<Result<IdentityRole>> GetRoleByIdAsync(string roleId);
        Task<Result<RoleWithDetails>> GetRoleWithPermissionsAsync(string roleId);
        Task<Result<IdentityRole>> UpdateRoleAsync(string roleId, string newRoleName);
        Task<Result<RoleWithDetails>> UpdateRoleWithPermissionsAsync(string roleId, UpdateRoleRequest request);
        Task<Result> DeleteRoleAsync(string roleId);

        // Permission Management Methods
        Task<Result> UserHasPermissionAsync(string userId, string permission);
        Task<Result<List<Permission>>> GetUserPermissionsAsync(string userId);
        Task<Result<bool>> RoleHasPermissionAsync(string roleId, string permission);
        Task<Result> AssignPermissionToRoleAsync(string roleId, string permissionName);
        Task<Result> RemovePermissionFromRoleAsync(string roleId, string permissionName);
        Task<Result<List<Permission>>> GetAllPermissionsAsync();
        Task<Result<List<Permission>>> GetRolePermissionsAsync(string roleId);
        Task<Result> SeedPermissionsAsync();
        Task<Result<object>> GetPermissionsByCategoryAsync();
        Task<Result<Permission>> CreatePermissionAsync(string name, string category, string description);

        // Permission Assignment Methods
        Task<Result> AssignPermissionsToRoleAsync(string roleId, List<string> permissionNames);
        Task<Result> RemovePermissionsFromRoleAsync(string roleId, List<string> permissionNames);
        Task<Result<List<PermissionCategoryResponse>>> GetPermissionsByCategoryForRoleAsync(string roleId = null);

        // User-Role Assignment Methods
        Task<Result> AssignUserToRoleAsync(string userId, string roleName);
        Task<Result> RemoveUserFromRoleAsync(string userId, string roleName);
        Task<Result<List<Users>>> GetUsersInRoleAsync(string roleName);
    }
}