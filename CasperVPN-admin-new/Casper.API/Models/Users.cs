using Microsoft.AspNetCore.Identity;

namespace Casper.API.Models
{
    public class Users : IdentityUser
    {
        public bool IsActive { get; set; } = true;
        public DateTime? DeactivatedAt { get; set; }
        public string? DeactivatedBy { get; set; }
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
        public string? CreatedBy { get; set; }
        public DateTime? UpdatedAt { get; set; }




        // Add these fields for Google OAuth
        public bool IsGoogleUser { get; set; } = false;
        public string? GoogleId { get; set; }
        public string? GoogleProfilePicture { get; set; }
        public string? FirstName { get; set; }
        public string? LastName { get; set; }
    } 





}