using System.ComponentModel.DataAnnotations;
using static Casper.API.Models.Enums;

namespace Casper.API.Models.Requests
{
    public class CreateServerRequest
    {
        [Required(ErrorMessage = "Server name is required")]
        [StringLength(100, MinimumLength = 2, ErrorMessage = "Server name must be between 2 and 100 characters")]
        public string ServerName { get; set; }

        [Required(ErrorMessage = "Location is required")]
        [StringLength(50, MinimumLength = 2, ErrorMessage = "Location must be between 2 and 50 characters")]
        public string Location { get; set; }

        [Required(ErrorMessage = "IP address is required")]
        [RegularExpression(@"^(?:[0-9]{1,3}\.){3}[0-9]{1,3}|([a-fA-F0-9:]+:+)+[a-fA-F0-9]+$",
            ErrorMessage = "Invalid IP address format")]
        [StringLength(45, ErrorMessage = "IP address is too long")]
        public string IPAddress { get; set; }

        [Required(ErrorMessage = "Username is required")]
        [StringLength(50, MinimumLength = 3, ErrorMessage = "Username must be between 3 and 50 characters")]
        [RegularExpression(@"^\S+$", ErrorMessage = "Username cannot contain whitespace")]
        public string Username { get; set; }

        [StringLength(100, MinimumLength = 6, ErrorMessage = "Password must be between 6 and 100 characters")]
        public string Password { get; set; }

        [StringLength(5000, MinimumLength = 50, ErrorMessage = "SSH key must be between 50 and 5000 characters")]
        public string Sshkey { get; set; }

        [Required(ErrorMessage = "Server status is required")]
        public ServerStatus? ServerStatus { get; set; }

        // Custom validation method (optional)
        public IEnumerable<ValidationResult> Validate(ValidationContext validationContext)
        {
            var results = new List<ValidationResult>();

            // Validate that either SSH key OR Password is provided
            bool hasSshKey = !string.IsNullOrWhiteSpace(Sshkey);
            bool hasPassword = !string.IsNullOrWhiteSpace(Password);

            if (!hasSshKey && !hasPassword)
            {
                results.Add(new ValidationResult("Either SSH key or Password must be provided"));
            }

            // Validate that we don't have both (if business rule)
            if (hasSshKey && hasPassword)
            {
                results.Add(new ValidationResult("Cannot provide both SSH key and Password. Please choose one authentication method."));
            }

            // Basic SSH key format validation
            if (hasSshKey && !Sshkey.StartsWith("ssh-") &&
                !Sshkey.StartsWith("ecdsa-") &&
                !Sshkey.StartsWith("-----BEGIN"))
            {
                results.Add(new ValidationResult("SSH key format appears to be invalid"));
            }

            return results;
        }
    }

    public class UpdateServerRequest : IValidatableObject
    {
        [Required(ErrorMessage = "Server ID is required")]
        [RegularExpression(@"^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$",
            ErrorMessage = "Invalid server ID format")]
        public string Id { get; set; }

        [StringLength(100, MinimumLength = 2, ErrorMessage = "Server name must be between 2 and 100 characters")]
        public string? ServerName { get; set; } // Make nullable

        [StringLength(50, MinimumLength = 2, ErrorMessage = "Location must be between 2 and 50 characters")]
        public string? Location { get; set; } // Make nullable

        [RegularExpression(@"^(?:[0-9]{1,3}\.){3}[0-9]{1,3}|([a-fA-F0-9:]+:+)+[a-fA-F0-9]+$",
            ErrorMessage = "Invalid IP address format")]
        [StringLength(45, ErrorMessage = "IP address is too long")]
        public string? IPAddress { get; set; } // Make nullable

        [StringLength(50, MinimumLength = 3, ErrorMessage = "Username must be between 3 and 50 characters")]
        [RegularExpression(@"^\S+$", ErrorMessage = "Username cannot contain whitespace")]
        public string? Username { get; set; } // Make nullable

        [StringLength(100, MinimumLength = 6, ErrorMessage = "Password must be between 6 and 100 characters")]
        public string? Password { get; set; } // Make nullable

        [StringLength(5000, MinimumLength = 50, ErrorMessage = "SSH key must be between 50 and 5000 characters")]
        public string? Sshkey { get; set; } // Make nullable

        public ServerStatus? ServerStatus { get; set; }

        public IEnumerable<ValidationResult> Validate(ValidationContext validationContext)
        {
            var results = new List<ValidationResult>();

            // Trim inputs for consistency
            ServerName = ServerName?.Trim();
            Location = Location?.Trim();
            IPAddress = IPAddress?.Trim();
            Username = Username?.Trim();
            Password = Password?.Trim();
            Sshkey = Sshkey?.Trim();

            // Check if at least one field (other than Id) is provided for update
            bool hasAnyUpdateField = !string.IsNullOrWhiteSpace(ServerName) ||
                                   !string.IsNullOrWhiteSpace(Location) ||
                                   !string.IsNullOrWhiteSpace(IPAddress) ||
                                   !string.IsNullOrWhiteSpace(Username) ||
                                   !string.IsNullOrWhiteSpace(Password) ||
                                   !string.IsNullOrWhiteSpace(Sshkey) ||
                                   ServerStatus.HasValue;

            if (!hasAnyUpdateField)
            {
                results.Add(new ValidationResult("At least one field must be provided for update (ServerName, Location, IPAddress, Username, Password, Sshkey, or ServerStatus)"));
            }

            // Validate authentication for update
            bool hasSshKey = !string.IsNullOrWhiteSpace(Sshkey);
            bool hasPassword = !string.IsNullOrWhiteSpace(Password);

            // If updating authentication, ensure only one method is provided
            if (hasSshKey && hasPassword)
            {
                results.Add(new ValidationResult("Cannot provide both SSH key and Password. Please choose one authentication method."));
            }

            // Basic SSH key format validation if provided
            if (hasSshKey && !Sshkey.StartsWith("ssh-") &&
                !Sshkey.StartsWith("ecdsa-") &&
                !Sshkey.StartsWith("-----BEGIN"))
            {
                results.Add(new ValidationResult("SSH key format appears to be invalid"));
            }

            return results;
        }

        // Helper method to check if any field has been provided
        public bool HasAnyUpdateField()
        {
            return !string.IsNullOrWhiteSpace(ServerName) ||
                   !string.IsNullOrWhiteSpace(Location) ||
                   !string.IsNullOrWhiteSpace(IPAddress) ||
                   !string.IsNullOrWhiteSpace(Username) ||
                   !string.IsNullOrWhiteSpace(Password) ||
                   !string.IsNullOrWhiteSpace(Sshkey) ||
                   ServerStatus.HasValue;
        }
    }

}
