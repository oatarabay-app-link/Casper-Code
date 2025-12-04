namespace Casper.API.Models
{
    public class RefreshTokens
    {
        public int Id { get; set; }


        public string? UserId { get; set; }  // Links to IdentityUser


        public string Token { get; set; }


        public DateTime Expires { get; set; }

        public DateTime? Revoked { get; set; }

        public string? ReplacedByToken { get; set; }

        // Helper properties
        public bool IsExpired => DateTime.UtcNow >= Expires;
        public bool IsActive => Revoked == null && !IsExpired;

        // Navigation property
        public virtual Users User { get; set; }
    }
}
