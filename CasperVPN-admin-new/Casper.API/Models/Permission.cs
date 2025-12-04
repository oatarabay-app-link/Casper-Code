namespace Casper.API.Models
{
    public class Permission:BaseModel
    {
        public string Id { get; set; } = Guid.NewGuid().ToString();
        public string Name { get; set; } // e.g., "Users.Read", "Roles.Create"
        public string Description { get; set; }
        public string Category { get; set; } // e.g., "Dashboard", "Users", "Roles"

        public ICollection<RolePermission> RolePermissions { get; set; }
    }

}
