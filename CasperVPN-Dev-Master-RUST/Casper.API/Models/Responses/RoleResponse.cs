namespace Casper.API.Models.Responses
{
    public class RoleWithDetails
    {
        public string Id { get; set; } = string.Empty;
        public string Name { get; set; } = string.Empty;
        public string Description { get; set; } = string.Empty;
        public string Type { get; set; } = "Custom";
        public int UsersAssigned { get; set; }
        public DateTime CreatedAt { get; set; }
        public string CreatedAgo { get; set; } = string.Empty;
        public List<Permission> Permissions { get; set; } = new List<Permission>();
    }

    public class RoleSummaryResponse
    {
        public int TotalRoles { get; set; }
        public int TotalAssignments { get; set; }
        public List<RoleWithDetails> Roles { get; set; } = new List<RoleWithDetails>();
    }

    public class PermissionCategoryResponse
    {
        public string Category { get; set; } = string.Empty;
        public List<PermissionItem> Permissions { get; set; } = new List<PermissionItem>();
    }

    public class PermissionItem
    {
        public string Id { get; set; } = string.Empty;
        public string Name { get; set; } = string.Empty;
        public string Description { get; set; } = string.Empty;
        public bool IsSelected { get; set; }
    }

    public class RoleCreateResponse
    {
        public string Id { get; set; } = string.Empty;
        public string Name { get; set; } = string.Empty;
        public string Description { get; set; } = string.Empty;
    }
}
