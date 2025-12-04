namespace Casper.API.Models
{
    public class BaseModel
    {
        public DateTime CreatedAt { get; set; } = DateTime.UtcNow;
        public string CreatedBy { get; set; } = "Admin";

        public bool IsActive { get; set; } = true; // Default to active
        public DateTime? DeactivatedAt { get; set; }

        public string? DeactivatedBy { get; set; } // Make nullable

        public DateTime? UpdatedAt { get; set; }


      
    }
}
