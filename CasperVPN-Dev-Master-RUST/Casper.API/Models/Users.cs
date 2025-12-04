using Microsoft.AspNetCore.Identity;

namespace Casper.API.Models
{
    public class Users : IdentityUser
    {
        public bool IsActive { get; set; } = true;
        public DateTime? DeactivatedAt { get; set; }
        public string? DeactivatedBy { get; set; } // Make nullable
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
        public string? CreatedBy { get; set; } // Make nullable
        public DateTime? UpdatedAt { get; set; }
    }
}
