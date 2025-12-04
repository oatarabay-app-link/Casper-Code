// Controllers/RolesController.cs
using Casper.API.Interfaces;
using Casper.API.Models.Requests;
using Casper.API.Models.Requests.RoleRequest;
using Casper.API.Models.Responses;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;

namespace Casper.API.Controllers
{
    [ApiController]
    [Authorize(Policy = "Admin")]
    [Route("api/[controller]")]
    public class RolesController : BaseController
    {
        private readonly IRoleService _roleService;
        private readonly RoleManager<IdentityRole> _roleManager;

        public RolesController(IRoleService roleService, RoleManager<IdentityRole> roleManager)
        {
            _roleService = roleService;
            _roleManager = roleManager;
        }

        [HttpGet("summary")]
        public async Task<IActionResult> GetRoleSummary()
        {
            var result = await _roleService.GetRoleSummaryAsync();
            return HandleResult(result);
        }

        [HttpGet]
        public async Task<IActionResult> GetAllRoles()
        {
            var result = await _roleService.GetAllRolesWithDetailsAsync();
            return HandleResult(result);
        }

        [HttpGet("{roleId}")]
        public async Task<IActionResult> GetRoleById(string roleId)
        {
            var result = await _roleService.GetRoleWithPermissionsAsync(roleId);
            return HandleResult(result);
        }

        [HttpPost]
        public async Task<IActionResult> CreateRole([FromBody] CreateRoleRequest request)
        {
            var result = await _roleService.CreateRoleWithPermissionsAsync(request);
            return HandleResult(result);
        }

        [HttpPut("{roleId}")]
        public async Task<IActionResult> UpdateRole(string roleId, [FromBody] UpdateRoleRequest request)
        {
            var result = await _roleService.UpdateRoleWithPermissionsAsync(roleId, request);
            return HandleResult(result);
        }

        [HttpDelete("{roleId}")]
        public async Task<IActionResult> DeleteRole(string roleId)
        {
            var result = await _roleService.DeleteRoleAsync(roleId);
            return HandleResult(result);
        }

        

        [HttpPost("{roleId}/permissions")]
        public async Task<IActionResult> AssignPermissions(string roleId, [FromBody] AssignPermissionsRequest request)
        {
            var result = await _roleService.AssignPermissionsToRoleAsync(roleId, request.PermissionNames);
            return HandleResult(result);
        }

        [HttpDelete("{roleId}/permissions")]
        public async Task<IActionResult> RemovePermissions(string roleId, [FromBody] AssignPermissionsRequest request)
        {
            var result = await _roleService.RemovePermissionsFromRoleAsync(roleId, request.PermissionNames);
            return HandleResult(result);
        }

        [HttpGet("{roleId}/permissions")]
        public async Task<IActionResult> GetRolePermissions(string roleId)
        {
            var result = await _roleService.GetRolePermissionsAsync(roleId);
            return HandleResult(result);
        }

        [HttpPost("{roleId}/users/{userId}")]
        public async Task<IActionResult> AssignUserToRole(string roleId, string userId)
        {
            var role = await _roleManager.FindByIdAsync(roleId);
            if (role == null)
                return NotFound(new { message = "Role not found" });

            var result = await _roleService.AssignUserToRoleAsync(userId, role.Name);
            return HandleResult(result);
        }

        [HttpDelete("{roleId}/users/{userId}")]
        public async Task<IActionResult> RemoveUserFromRole(string roleId, string userId)
        {
            var role = await _roleManager.FindByIdAsync(roleId);
            if (role == null)
                return NotFound(new { message = "Role not found" });

            var result = await _roleService.RemoveUserFromRoleAsync(userId, role.Name);
            return HandleResult(result);
        }

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