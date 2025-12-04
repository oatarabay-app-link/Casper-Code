namespace Casper.API.Models.Requests.RoleRequest
{
    public class CreateRoleRequest
    {
        public string RoleName { get; set; }
        public string Description { get; set; }
        public List<string> Permissions { get; set; } = new List<string>();
    }

    public class UpdateRoleRequest
    {
        public string NewRoleName { get; set; }
        public string Description { get; set; }
        public List<string> Permissions { get; set; } = new List<string>();
    }

    public class AssignPermissionsRequest
    {
        public List<string> PermissionNames { get; set; } = new List<string>();
    }
}
