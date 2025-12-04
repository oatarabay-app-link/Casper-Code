using Casper.API.Models.Requests;
using Casper.API.Models.Requests.RoleRequest;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;

namespace Casper.API.Controllers.v1.admin
{
  
    public class RolesController : BaseAdminController
    {
        private readonly IRoleService _roleService;
        private readonly RoleManager<IdentityRole> _roleManager;

        public RolesController(IRoleService roleService, RoleManager<IdentityRole> roleManager)
        {
            _roleService = roleService;
            _roleManager = roleManager;
        }

        // GET: /api/v1/admin/roles/summary
        [HttpGet("summary")]
        public async Task<IActionResult> GetRoleSummary()
        {
            var result = await _roleService.GetRoleSummaryAsync();
            return HandleResult(result);
        }

        // GET: /api/v1/admin/roles
        [HttpGet]
        public async Task<IActionResult> GetAllRoles()
        {
            var result = await _roleService.GetAllRolesWithDetailsAsync();
            return HandleResult(result);
        }

        // GET: /api/v1/admin/roles/{roleId}
        [HttpGet("{roleId}")]
        public async Task<IActionResult> GetRoleById(string roleId)
        {
            var result = await _roleService.GetRoleWithPermissionsAsync(roleId);
            return HandleResult(result);
        }

        // POST: /api/v1/admin/roles
        [HttpPost]
        public async Task<IActionResult> CreateRole([FromBody] CreateRoleRequest request)
        {
            var result = await _roleService.CreateRoleWithPermissionsAsync(request);
            return HandleResult(result);
        }

        // PUT: /api/v1/admin/roles/{roleId}
        [HttpPut("{roleId}")]
        public async Task<IActionResult> UpdateRole(string roleId, [FromBody] UpdateRoleRequest request)
        {
            var result = await _roleService.UpdateRoleWithPermissionsAsync(roleId, request);
            return HandleResult(result);
        }

        // DELETE: /api/v1/admin/roles/{roleId}
        [HttpDelete("{roleId}")]
        public async Task<IActionResult> DeleteRole(string roleId)
        {
            var result = await _roleService.DeleteRoleAsync(roleId);
            return HandleResult(result);
        }

        // POST: /api/v1/admin/roles/{roleId}/permissions
        [HttpPost("{roleId}/permissions")]
        public async Task<IActionResult> AssignPermissions(string roleId, [FromBody] AssignPermissionsRequest request)
        {
            var result = await _roleService.AssignPermissionsToRoleAsync(roleId, request.PermissionNames);
            return HandleResult(result);
        }

        // DELETE: /api/v1/admin/roles/{roleId}/permissions
        [HttpDelete("{roleId}/permissions")]
        public async Task<IActionResult> RemovePermissions(string roleId, [FromBody] AssignPermissionsRequest request)
        {
            var result = await _roleService.RemovePermissionsFromRoleAsync(roleId, request.PermissionNames);
            return HandleResult(result);
        }

        // GET: /api/v1/admin/roles/{roleId}/permissions
        [HttpGet("{roleId}/permissions")]
        public async Task<IActionResult> GetRolePermissions(string roleId)
        {
            var result = await _roleService.GetRolePermissionsAsync(roleId);
            return HandleResult(result);
        }

        // POST: /api/v1/admin/roles/{roleId}/users/{userId}
        [HttpPost("{roleId}/users/{userId}")]
        public async Task<IActionResult> AssignUserToRole(string roleId, string userId)
        {
            var role = await _roleManager.FindByIdAsync(roleId);
            if (role == null)
                return NotFound(new { message = "Role not found" });

            var result = await _roleService.AssignUserToRoleAsync(userId, role.Name);
            return HandleResult(result);
        }

        // DELETE: /api/v1/admin/roles/{roleId}/users/{userId}
        [HttpDelete("{roleId}/users/{userId}")]
        public async Task<IActionResult> RemoveUserFromRole(string roleId, string userId)
        {
            var role = await _roleManager.FindByIdAsync(roleId);
            if (role == null)
                return NotFound(new { message = "Role not found" });

            var result = await _roleService.RemoveUserFromRoleAsync(userId, role.Name);
            return HandleResult(result);
        }

        // GET: /api/v1/admin/roles/{roleId}/users
        [HttpGet("{roleId}/users")]
        public async Task<IActionResult> GetUsersInRole(string roleId)
        {
            var role = await _roleManager.FindByIdAsync(roleId);
            if (role == null)
                return NotFound(new { message = "Role not found" });

            var result = await _roleService.GetUsersInRoleAsync(role.Name);
            return HandleResult(result);
        }
    }
}
