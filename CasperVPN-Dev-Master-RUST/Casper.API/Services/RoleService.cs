// Services/RoleService.cs
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;
using Casper.API.Interfaces;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using System.Security.Claims;
using Casper.API.Models.Requests.RoleRequest;
using Casper.API.Models;

namespace Casper.API.Services
{
    public class RoleService : IRoleService
    {
        private readonly CasperVpnDbContext _context;
        private readonly RoleManager<IdentityRole> _roleManager;
        private readonly UserManager<Users> _userManager;
        private readonly ILogger<RoleService> _logger;

        public RoleService(
            CasperVpnDbContext context,
            RoleManager<IdentityRole> roleManager,
            UserManager<Users> userManager,
            ILogger<RoleService> logger)
        {
            _context = context;
            _roleManager = roleManager;
            _userManager = userManager;
            _logger = logger;
        }

        // Role Management Methods
        public async Task<Result<RoleCreateResponse>> CreateRoleAsync(string roleName)
        {
            try
            {
                _logger.LogInformation("Creating role: {RoleName}", roleName);

                if (string.IsNullOrEmpty(roleName))
                    return Result<RoleCreateResponse>.Failure("Role name is required");

                if (await _roleManager.RoleExistsAsync(roleName))
                    return Result<RoleCreateResponse>.Conflict("Role already exists");

                var role = new IdentityRole(roleName);
                var result = await _roleManager.CreateAsync(role);

                if (!result.Succeeded)
                {
                    var errors = result.Errors.Select(e => e.Description).ToList();
                    return Result<RoleCreateResponse>.ValidationFailure(errors);
                }

                var response = new RoleCreateResponse
                {
                    Id = role.Id,
                    Name = role.Name ?? ""
                };

                _logger.LogInformation("Role created successfully: {RoleName}", roleName);
                return Result<RoleCreateResponse>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error creating role: {RoleName}", roleName);
                return Result<RoleCreateResponse>.Failure("An error occurred while creating role", ErrorType.InternalError);
            }
        }

        public async Task<Result<RoleWithDetails>> CreateRoleWithPermissionsAsync(CreateRoleRequest request)
        {
            try
            {
                _logger.LogInformation("Creating role with permissions: {RoleName}", request.RoleName);

                if (request == null)
                    return Result<RoleWithDetails>.Failure("Request body required");

                if (string.IsNullOrEmpty(request.RoleName))
                    return Result<RoleWithDetails>.Failure("Role name is required");

                if (await _roleManager.RoleExistsAsync(request.RoleName))
                    return Result<RoleWithDetails>.Conflict("Role already exists");

                var role = new IdentityRole(request.RoleName);
                var result = await _roleManager.CreateAsync(role);

                if (!result.Succeeded)
                {
                    var errors = result.Errors.Select(e => e.Description).ToList();
                    return Result<RoleWithDetails>.ValidationFailure(errors);
                }

                // Assign permissions if any
                if (request.Permissions.Any())
                {
                    foreach (var permissionName in request.Permissions)
                    {
                        var permissionResult = await AssignPermissionToRoleAsync(role.Id, permissionName);
                        if (!permissionResult.IsSuccess)
                        {
                            _logger.LogWarning("Failed to assign permission {Permission} to role {RoleName}: {Error}",
                                permissionName, request.RoleName, permissionResult.ErrorMessage);
                        }
                    }
                }

                // Store description in claims
                if (!string.IsNullOrEmpty(request.Description))
                {
                    await _roleManager.AddClaimAsync(role, new Claim("Description", request.Description));
                }

                var roleDetails = await GetRoleDetailsAsync(role);
                _logger.LogInformation("Role created successfully with permissions: {RoleName}", request.RoleName);
                return Result<RoleWithDetails>.Success(roleDetails);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error creating role with permissions: {RoleName}", request.RoleName);
                return Result<RoleWithDetails>.Failure("An error occurred while creating role", ErrorType.InternalError);
            }
        }

        public async Task<Result<List<IdentityRole>>> GetAllRolesAsync()
        {
            try
            {
                _logger.LogInformation("Retrieving all roles");

                var roles = await _roleManager.Roles.ToListAsync();
                _logger.LogInformation("Successfully retrieved {RoleCount} roles", roles.Count);
                return Result<List<IdentityRole>>.Success(roles);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving all roles");
                return Result<List<IdentityRole>>.Failure("An error occurred while retrieving roles", ErrorType.InternalError);
            }
        }

        public async Task<Result<List<RoleWithDetails>>> GetAllRolesWithDetailsAsync()
        {
            try
            {
                _logger.LogInformation("Retrieving all roles with details");

                var roles = await _roleManager.Roles.ToListAsync();
                var rolesWithDetails = new List<RoleWithDetails>();

                foreach (var role in roles)
                {
                    var roleDetails = await GetRoleDetailsAsync(role);
                    rolesWithDetails.Add(roleDetails);
                }

                _logger.LogInformation("Successfully retrieved {RoleCount} roles with details", rolesWithDetails.Count);
                return Result<List<RoleWithDetails>>.Success(rolesWithDetails);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving roles with details");
                return Result<List<RoleWithDetails>>.Failure("An error occurred while retrieving roles", ErrorType.InternalError);
            }
        }

        public async Task<Result<RoleSummaryResponse>> GetRoleSummaryAsync()
        {
            try
            {
                _logger.LogInformation("Retrieving role summary");

                var roles = await _roleManager.Roles.ToListAsync();
                var totalAssignments = 0;
                var rolesWithDetails = new List<RoleWithDetails>();

                foreach (var role in roles)
                {
                    var usersInRole = await _userManager.GetUsersInRoleAsync(role.Name);
                    totalAssignments += usersInRole.Count;

                    var roleDetails = await GetRoleDetailsAsync(role);
                    rolesWithDetails.Add(roleDetails);
                }

                var summary = new RoleSummaryResponse
                {
                    TotalRoles = roles.Count,
                    TotalAssignments = totalAssignments,
                    Roles = rolesWithDetails
                };

                _logger.LogInformation("Role summary retrieved: {TotalRoles} roles, {TotalAssignments} assignments",
                    roles.Count, totalAssignments);
                return Result<RoleSummaryResponse>.Success(summary);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving role summary");
                return Result<RoleSummaryResponse>.Failure("An error occurred while retrieving role summary", ErrorType.InternalError);
            }
        }

        public async Task<Result<IdentityRole>> GetRoleByIdAsync(string roleId)
        {
            try
            {
                _logger.LogInformation("Retrieving role by ID: {RoleId}", roleId);

                if (string.IsNullOrEmpty(roleId))
                    return Result<IdentityRole>.Failure("Role ID is required");

                var role = await _roleManager.FindByIdAsync(roleId);
                if (role == null)
                    return Result<IdentityRole>.NotFound("Role not found");

                _logger.LogInformation("Role retrieved successfully: {RoleName}", role.Name);
                return Result<IdentityRole>.Success(role);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving role by ID: {RoleId}", roleId);
                return Result<IdentityRole>.Failure("An error occurred while retrieving role", ErrorType.InternalError);
            }
        }

        public async Task<Result<RoleWithDetails>> GetRoleWithPermissionsAsync(string roleId)
        {
            try
            {
                _logger.LogInformation("Retrieving role with permissions: {RoleId}", roleId);

                if (string.IsNullOrEmpty(roleId))
                    return Result<RoleWithDetails>.Failure("Role ID is required");

                var role = await _roleManager.FindByIdAsync(roleId);
                if (role == null)
                    return Result<RoleWithDetails>.NotFound("Role not found");

                var roleDetails = await GetRoleDetailsAsync(role);
                _logger.LogInformation("Role with permissions retrieved successfully: {RoleName}", role.Name);
                return Result<RoleWithDetails>.Success(roleDetails);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving role with permissions: {RoleId}", roleId);
                return Result<RoleWithDetails>.Failure("An error occurred while retrieving role", ErrorType.InternalError);
            }
        }

        public async Task<Result<IdentityRole>> UpdateRoleAsync(string roleId, string newRoleName)
        {
            try
            {
                _logger.LogInformation("Updating role: {RoleId} to {NewRoleName}", roleId, newRoleName);

                if (string.IsNullOrEmpty(roleId))
                    return Result<IdentityRole>.Failure("Role ID is required");

                if (string.IsNullOrEmpty(newRoleName))
                    return Result<IdentityRole>.Failure("New role name is required");

                var role = await _roleManager.FindByIdAsync(roleId);
                if (role == null)
                    return Result<IdentityRole>.NotFound("Role not found");

                if (await _roleManager.RoleExistsAsync(newRoleName) && role.Name != newRoleName)
                    return Result<IdentityRole>.Conflict("Role name already exists");

                role.Name = newRoleName;
                var result = await _roleManager.UpdateAsync(role);

                if (!result.Succeeded)
                {
                    var errors = result.Errors.Select(e => e.Description).ToList();
                    return Result<IdentityRole>.ValidationFailure(errors);
                }

                _logger.LogInformation("Role updated successfully: {RoleId}", roleId);
                return Result<IdentityRole>.Success(role);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating role: {RoleId}", roleId);
                return Result<IdentityRole>.Failure("An error occurred while updating role", ErrorType.InternalError);
            }
        }

        public async Task<Result<RoleWithDetails>> UpdateRoleWithPermissionsAsync(string roleId, UpdateRoleRequest request)
        {
            try
            {
                _logger.LogInformation("Updating role with permissions: {RoleId}", roleId);

                if (request == null)
                    return Result<RoleWithDetails>.Failure("Request body required");

                if (string.IsNullOrEmpty(roleId))
                    return Result<RoleWithDetails>.Failure("Role ID is required");

                var role = await _roleManager.FindByIdAsync(roleId);
                if (role == null)
                    return Result<RoleWithDetails>.NotFound("Role not found");

                // Update role name if changed
                if (!string.IsNullOrEmpty(request.NewRoleName) && role.Name != request.NewRoleName)
                {
                    if (await _roleManager.RoleExistsAsync(request.NewRoleName))
                        return Result<RoleWithDetails>.Conflict("Role name already exists");

                    role.Name = request.NewRoleName;
                    var result = await _roleManager.UpdateAsync(role);
                    if (!result.Succeeded)
                    {
                        var errors = result.Errors.Select(e => e.Description).ToList();
                        return Result<RoleWithDetails>.ValidationFailure(errors);
                    }
                }

                // Update description
                var existingClaims = await _roleManager.GetClaimsAsync(role);
                var descriptionClaim = existingClaims.FirstOrDefault(c => c.Type == "Description");

                if (descriptionClaim != null)
                    await _roleManager.RemoveClaimAsync(role, descriptionClaim);

                if (!string.IsNullOrEmpty(request.Description))
                    await _roleManager.AddClaimAsync(role, new Claim("Description", request.Description));

                // Update permissions if provided
                if (request.Permissions != null)
                {
                    // Remove all current permissions
                    var currentPermissions = await _context.RolePermissions
                        .Where(rp => rp.RoleId == roleId)
                        .ToListAsync();

                    _context.RolePermissions.RemoveRange(currentPermissions);
                    await _context.SaveChangesAsync();

                    // Add new permissions
                    foreach (var permissionName in request.Permissions)
                    {
                        var permissionResult = await AssignPermissionToRoleAsync(roleId, permissionName);
                        if (!permissionResult.IsSuccess)
                        {
                            _logger.LogWarning("Failed to assign permission {Permission} to role {RoleId}: {Error}",
                                permissionName, roleId, permissionResult.ErrorMessage);
                        }
                    }
                }

                var updatedRole = await GetRoleDetailsAsync(role);
                _logger.LogInformation("Role with permissions updated successfully: {RoleId}", roleId);
                return Result<RoleWithDetails>.Success(updatedRole);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating role with permissions: {RoleId}", roleId);
                return Result<RoleWithDetails>.Failure("An error occurred while updating role", ErrorType.InternalError);
            }
        }

        public async Task<Result> DeleteRoleAsync(string roleId)
        {
            try
            {
                _logger.LogInformation("Deleting role: {RoleId}", roleId);

                if (string.IsNullOrEmpty(roleId))
                    return Result.Failure("Role ID is required");

                var role = await _roleManager.FindByIdAsync(roleId);
                if (role == null)
                    return Result.NotFound("Role not found");

                // Check if role is system role
                if (await IsSystemRoleAsync(role.Name))
                    return Result.Failure("Cannot delete system roles");

                var result = await _roleManager.DeleteAsync(role);

                if (!result.Succeeded)
                {
                    var errors = result.Errors.Select(e => e.Description).ToList();
                    return Result.ValidationFailure(errors);
                }

                _logger.LogInformation("Role deleted successfully: {RoleId}", roleId);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error deleting role: {RoleId}", roleId);
                return Result.Failure("An error occurred while deleting role", ErrorType.InternalError);
            }
        }

        // Permission Management Methods (formerly in PermissionService)
        public async Task<Result> UserHasPermissionAsync(string userId, string permission)
        {
            try
            {
                var user = await _userManager.FindByIdAsync(userId);
                if (user == null)
                    return Result.NotFound("User not found");

                var userRoles = await _userManager.GetRolesAsync(user);

                foreach (var roleName in userRoles)
                {
                    var role = await _roleManager.FindByNameAsync(roleName);
                    if (role != null)
                    {
                        var hasPermission = await RoleHasPermissionAsync(role.Id, permission);
                        if (hasPermission.IsSuccess && (bool)hasPermission.Data)
                            return Result.Success();
                    }
                }

                return Result.Failure("User does not have the required permission", ErrorType.Unauthorized);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error checking user permission: {UserId}, {Permission}", userId, permission);
                return Result.Failure($"Error checking user permission: {ex.Message}");
            }
        }

        public async Task<Result<List<Permission>>> GetUserPermissionsAsync(string userId)
        {
            try
            {
                var user = await _userManager.FindByIdAsync(userId);
                if (user == null)
                    return Result<List<Permission>>.NotFound("User not found");

                var userRoles = await _userManager.GetRolesAsync(user);
                var permissions = new List<Permission>();

                foreach (var roleName in userRoles)
                {
                    var role = await _roleManager.FindByNameAsync(roleName);
                    if (role != null)
                    {
                        var rolePermissions = await _context.RolePermissions
                            .Where(rp => rp.RoleId == role.Id)
                            .Include(rp => rp.Permission)
                            .Select(rp => rp.Permission)
                            .ToListAsync();

                        permissions.AddRange(rolePermissions);
                    }
                }

                var distinctPermissions = permissions.Distinct().ToList();
                return Result<List<Permission>>.Success(distinctPermissions);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving user permissions: {UserId}", userId);
                return Result<List<Permission>>.Failure($"Error retrieving user permissions: {ex.Message}");
            }
        }

        public async Task<Result<bool>> RoleHasPermissionAsync(string roleId, string permission)
        {
            try
            {
                var hasPermission = await _context.RolePermissions
                    .AnyAsync(rp => rp.RoleId == roleId && rp.Permission.Name == permission);

                return Result<bool>.Success(hasPermission);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error checking role permission: {RoleId}, {Permission}", roleId, permission);
                return Result<bool>.Failure($"Error checking role permission: {ex.Message}");
            }
        }

        public async Task<Result> AssignPermissionToRoleAsync(string roleId, string permissionName)
        {
            try
            {
                var role = await _roleManager.FindByIdAsync(roleId);
                if (role == null)
                    return Result.NotFound("Role not found");

                var permission = await _context.Permissions
                    .FirstOrDefaultAsync(p => p.Name == permissionName);

                if (permission == null)
                {
                    // Create the permission if it doesn't exist
                    permission = new Permission
                    {
                        Name = permissionName,
                        Category = "General",
                        Description = $"Auto-generated permission: {permissionName}"
                    };
                    _context.Permissions.Add(permission);
                    await _context.SaveChangesAsync();
                }

                var existing = await _context.RolePermissions
                    .FirstOrDefaultAsync(rp => rp.RoleId == roleId && rp.PermissionId == permission.Id);

                if (existing != null)
                    return Result.Conflict("Permission already assigned to role");

                _context.RolePermissions.Add(new RolePermission
                {
                    RoleId = roleId,
                    PermissionId = permission.Id
                });
                await _context.SaveChangesAsync();

                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error assigning permission to role: {RoleId}, {Permission}", roleId, permissionName);
                return Result.Failure($"Error assigning permission to role: {ex.Message}");
            }
        }

        public async Task<Result> RemovePermissionFromRoleAsync(string roleId, string permissionName)
        {
            try
            {
                var rolePermission = await _context.RolePermissions
                    .Include(rp => rp.Permission)
                    .FirstOrDefaultAsync(rp => rp.RoleId == roleId && rp.Permission.Name == permissionName);

                if (rolePermission == null)
                    return Result.NotFound("Permission not found for this role");

                _context.RolePermissions.Remove(rolePermission);
                await _context.SaveChangesAsync();

                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error removing permission from role: {RoleId}, {Permission}", roleId, permissionName);
                return Result.Failure($"Error removing permission from role: {ex.Message}");
            }
        }

        public async Task<Result<List<Permission>>> GetAllPermissionsAsync()
        {
            try
            {
                var permissions = await _context.Permissions.ToListAsync();
                return Result<List<Permission>>.Success(permissions);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving permissions");
                return Result<List<Permission>>.Failure($"Error retrieving permissions: {ex.Message}");
            }
        }

        public async Task<Result<List<Permission>>> GetRolePermissionsAsync(string roleId)
        {
            try
            {
                var role = await _roleManager.FindByIdAsync(roleId);
                if (role == null)
                    return Result<List<Permission>>.NotFound("Role not found");

                var permissions = await _context.RolePermissions
                    .Where(rp => rp.RoleId == roleId)
                    .Include(rp => rp.Permission)
                    .Select(rp => rp.Permission)
                    .ToListAsync();

                return Result<List<Permission>>.Success(permissions);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving role permissions: {RoleId}", roleId);
                return Result<List<Permission>>.Failure($"Error retrieving role permissions: {ex.Message}");
            }
        }

        public async Task<Result> SeedPermissionsAsync()
        {
            try
            {
                var defaultPermissions = new[]
                {
                    new { Name = "Dashboard.View", Category = "Dashboard", Description = "View dashboard" },
                    new { Name = "Users.Read", Category = "Users", Description = "View users" },
                    new { Name = "Users.Create", Category = "Users", Description = "Create users" },
                    new { Name = "Users.Update", Category = "Users", Description = "Update users" },
                    new { Name = "Users.Delete", Category = "Users", Description = "Delete users" },
                    new { Name = "Roles.Read", Category = "Roles", Description = "View roles" },
                    new { Name = "Roles.Create", Category = "Roles", Description = "Create roles" },
                    new { Name = "Roles.Update", Category = "Roles", Description = "Update roles" },
                    new { Name = "Roles.Delete", Category = "Roles", Description = "Delete roles" },
                    new { Name = "Roles.ManagePermissions", Category = "Roles", Description = "Manage role permissions" },
                    new { Name = "Courses.Read", Category = "Courses", Description = "View courses" },
                    new { Name = "Courses.Create", Category = "Courses", Description = "Create courses" },
                    new { Name = "Courses.Update", Category = "Courses", Description = "Update courses" },
                    new { Name = "Courses.Delete", Category = "Courses", Description = "Delete courses" },
                    new { Name = "Blogs.Read", Category = "Blogs", Description = "View blogs" },
                    new { Name = "Blogs.Create", Category = "Blogs", Description = "Create blogs" },
                    new { Name = "Blogs.Update", Category = "Blogs", Description = "Update blogs" },
                    new { Name = "Blogs.Delete", Category = "Blogs", Description = "Delete blogs" },
                    new { Name = "Analytics.View", Category = "Analytics", Description = "View analytics" }
                };

                var seededPermissions = new List<Permission>();
                foreach (var perm in defaultPermissions)
                {
                    if (!await _context.Permissions.AnyAsync(p => p.Name == perm.Name))
                    {
                        var permission = new Permission
                        {
                            Name = perm.Name,
                            Category = perm.Category,
                            Description = perm.Description
                        };
                        _context.Permissions.Add(permission);
                        seededPermissions.Add(permission);
                    }
                }

                await _context.SaveChangesAsync();

                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error seeding permissions");
                return Result.Failure($"Error seeding permissions: {ex.Message}");
            }
        }

        public async Task<Result<object>> GetPermissionsByCategoryAsync()
        {
            try
            {
                var permissionsByCategory = await _context.Permissions
                    .GroupBy(p => p.Category)
                    .Select(g => new
                    {
                        Category = g.Key,
                        Permissions = g.Select(p => new { p.Id, p.Name, p.Description }).ToList()
                    })
                    .ToListAsync();

                return Result<object>.Success(permissionsByCategory);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving permissions by category");
                return Result<object>.Failure($"Error retrieving permissions by category: {ex.Message}");
            }
        }

        public async Task<Result<Permission>> CreatePermissionAsync(string name, string category, string description)
        {
            try
            {
                if (await _context.Permissions.AnyAsync(p => p.Name == name))
                    return Result<Permission>.Conflict("Permission with this name already exists");

                var permission = new Permission
                {
                    Name = name,
                    Category = category,
                    Description = description
                };

                _context.Permissions.Add(permission);
                await _context.SaveChangesAsync();

                return Result<Permission>.Success(permission);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error creating permission: {Name}", name);
                return Result<Permission>.Failure($"Error creating permission: {ex.Message}");
            }
        }

        // Permission Assignment Methods
        public async Task<Result> AssignPermissionsToRoleAsync(string roleId, List<string> permissionNames)
        {
            try
            {
                _logger.LogInformation("Assigning permissions to role: {RoleId}", roleId);

                if (string.IsNullOrEmpty(roleId))
                    return Result.Failure("Role ID is required");

                if (permissionNames == null || !permissionNames.Any())
                    return Result.Failure("At least one permission is required");

                var role = await _roleManager.FindByIdAsync(roleId);
                if (role == null)
                    return Result.NotFound("Role not found");

                foreach (var permissionName in permissionNames)
                {
                    var result = await AssignPermissionToRoleAsync(roleId, permissionName);
                    if (!result.IsSuccess)
                    {
                        _logger.LogWarning("Failed to assign permission {Permission} to role {RoleId}: {Error}",
                            permissionName, roleId, result.ErrorMessage);
                    }
                }

                _logger.LogInformation("Permissions assigned successfully to role: {RoleId}", roleId);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error assigning permissions to role: {RoleId}", roleId);
                return Result.Failure("An error occurred while assigning permissions", ErrorType.InternalError);
            }
        }

        public async Task<Result> RemovePermissionsFromRoleAsync(string roleId, List<string> permissionNames)
        {
            try
            {
                _logger.LogInformation("Removing permissions from role: {RoleId}", roleId);

                if (string.IsNullOrEmpty(roleId))
                    return Result.Failure("Role ID is required");

                if (permissionNames == null || !permissionNames.Any())
                    return Result.Failure("At least one permission is required");

                var role = await _roleManager.FindByIdAsync(roleId);
                if (role == null)
                    return Result.NotFound("Role not found");

                foreach (var permissionName in permissionNames)
                {
                    var result = await RemovePermissionFromRoleAsync(roleId, permissionName);
                    if (!result.IsSuccess)
                    {
                        _logger.LogWarning("Failed to remove permission {Permission} from role {RoleId}: {Error}",
                            permissionName, roleId, result.ErrorMessage);
                    }
                }

                _logger.LogInformation("Permissions removed successfully from role: {RoleId}", roleId);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error removing permissions from role: {RoleId}", roleId);
                return Result.Failure("An error occurred while removing permissions", ErrorType.InternalError);
            }
        }

        public async Task<Result<List<PermissionCategoryResponse>>> GetPermissionsByCategoryForRoleAsync(string roleId = null)
        {
            try
            {
                _logger.LogInformation("Retrieving permissions by category for role: {RoleId}", roleId);

                var allPermissions = await _context.Permissions.ToListAsync();
                var permissionsByCategory = allPermissions
                    .GroupBy(p => p.Category)
                    .Select(g => new PermissionCategoryResponse
                    {
                        Category = g.Key ?? "Uncategorized",
                        Permissions = g.Select(p => new PermissionItem
                        {
                            Id = p.Id,
                            Name = p.Name ?? "",
                            Description = p.Description ?? "",
                            IsSelected = roleId != null && _context.RolePermissions
                                .Any(rp => rp.RoleId == roleId && rp.PermissionId == p.Id)
                        }).ToList()
                    })
                    .ToList();

                _logger.LogInformation("Permissions by category retrieved successfully");
                return Result<List<PermissionCategoryResponse>>.Success(permissionsByCategory);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving permissions by category");
                return Result<List<PermissionCategoryResponse>>.Failure("An error occurred while retrieving permissions", ErrorType.InternalError);
            }
        }

        // User-Role Assignment Methods
        public async Task<Result> AssignUserToRoleAsync(string userId, string roleName)
        {
            try
            {
                _logger.LogInformation("Assigning user {UserId} to role {RoleName}", userId, roleName);

                if (string.IsNullOrEmpty(userId))
                    return Result.Failure("User ID is required");

                if (string.IsNullOrEmpty(roleName))
                    return Result.Failure("Role name is required");

                var user = await _userManager.FindByIdAsync(userId);
                if (user == null)
                    return Result.NotFound("User not found");

                if (!await _roleManager.RoleExistsAsync(roleName))
                    return Result.NotFound("Role not found");

                var result = await _userManager.AddToRoleAsync(user, roleName);

                if (!result.Succeeded)
                {
                    var errors = result.Errors.Select(e => e.Description).ToList();
                    return Result.ValidationFailure(errors);
                }

                _logger.LogInformation("User assigned to role successfully: {UserId} -> {RoleName}", userId, roleName);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error assigning user to role: {UserId} -> {RoleName}", userId, roleName);
                return Result.Failure("An error occurred while assigning user to role", ErrorType.InternalError);
            }
        }

        public async Task<Result> RemoveUserFromRoleAsync(string userId, string roleName)
        {
            try
            {
                _logger.LogInformation("Removing user {UserId} from role {RoleName}", userId, roleName);

                if (string.IsNullOrEmpty(userId))
                    return Result.Failure("User ID is required");

                if (string.IsNullOrEmpty(roleName))
                    return Result.Failure("Role name is required");

                var user = await _userManager.FindByIdAsync(userId);
                if (user == null)
                    return Result.NotFound("User not found");

                var result = await _userManager.RemoveFromRoleAsync(user, roleName);

                if (!result.Succeeded)
                {
                    var errors = result.Errors.Select(e => e.Description).ToList();
                    return Result.ValidationFailure(errors);
                }

                _logger.LogInformation("User removed from role successfully: {UserId} -> {RoleName}", userId, roleName);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error removing user from role: {UserId} -> {RoleName}", userId, roleName);
                return Result.Failure("An error occurred while removing user from role", ErrorType.InternalError);
            }
        }

        public async Task<Result<List<Users>>> GetUsersInRoleAsync(string roleName)
        {
            try
            {
                _logger.LogInformation("Retrieving users in role: {RoleName}", roleName);

                if (string.IsNullOrEmpty(roleName))
                    return Result<List<Users>>.Failure("Role name is required");

                if (!await _roleManager.RoleExistsAsync(roleName))
                    return Result<List<Users>>.NotFound("Role not found");

                var users = await _userManager.GetUsersInRoleAsync(roleName);
                _logger.LogInformation("Retrieved {UserCount} users in role: {RoleName}", users.Count, roleName);
                return Result<List<Users>>.Success(users.ToList());
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving users in role: {RoleName}", roleName);
                return Result<List<Users>>.Failure("An error occurred while retrieving users in role", ErrorType.InternalError);
            }
        }

        // Helper Methods
        private async Task<RoleWithDetails> GetRoleDetailsAsync(IdentityRole role)
        {
            var usersInRole = await _userManager.GetUsersInRoleAsync(role.Name ?? "");
            var claims = await _roleManager.GetClaimsAsync(role);
            var descriptionClaim = claims.FirstOrDefault(c => c.Type == "Description");

            var permissions = await _context.RolePermissions
                .Where(rp => rp.RoleId == role.Id)
                .Include(rp => rp.Permission)
                .Select(rp => rp.Permission)
                .ToListAsync();

            return new RoleWithDetails
            {
                Id = role.Id,
                Name = role.Name ?? "",
                Description = descriptionClaim?.Value ?? "No description",
                Type = await IsSystemRoleAsync(role.Name ?? "") ? "System" : "Custom",
                UsersAssigned = usersInRole.Count,
                CreatedAt = await GetRoleCreationDateAsync(role.Id),
                CreatedAgo = GetTimeAgo(await GetRoleCreationDateAsync(role.Id)),
                Permissions = permissions
            };
        }

        private async Task<bool> IsSystemRoleAsync(string roleName)
        {
            var systemRoles = new[] { "Admin", "User" };
            return systemRoles.Contains(roleName);
        }

        private async Task<DateTime> GetRoleCreationDateAsync(string roleId)
        {
            // Simplified implementation - you might want to store creation date properly
            return DateTime.UtcNow.AddMonths(-2);
        }

        private string GetTimeAgo(DateTime date)
        {
            var timeSpan = DateTime.UtcNow - date;

            if (timeSpan.TotalDays > 60)
                return $"{(int)timeSpan.TotalDays / 30} months ago";
            else if (timeSpan.TotalDays > 30)
                return "about 1 month ago";
            else if (timeSpan.TotalDays > 14)
                return $"{(int)timeSpan.TotalDays / 7} weeks ago";
            else if (timeSpan.TotalDays > 7)
                return "about 1 week ago";
            else if (timeSpan.TotalDays > 1)
                return $"{(int)timeSpan.TotalDays} days ago";
            else if (timeSpan.TotalHours > 1)
                return $"{(int)timeSpan.TotalHours} hours ago";
            else
                return "recently";
        }
    }
}