using Microsoft.AspNetCore.Identity;

namespace Casper.API.Models
{
    public class RolePermission
    {
        public string RoleId { get; set; }
        public IdentityRole Role { get; set; }

        public string PermissionId { get; set; }
        public Permission Permission { get; set; }
    }
}
