using System.ComponentModel.DataAnnotations;

namespace Casper.API.Models.Requests
{
    public abstract class VpnProtocolRequest
    {
        [Required(ErrorMessage = "Protocol name is required")]
        [MinLength(2, ErrorMessage = "Protocol name must be at least 2 characters")]
        public string Protocol { get; set; } = string.Empty;

        public bool IsActive { get; set; } = true;
    }

    public class CreateVpnProtocolRequest : VpnProtocolRequest
    {
        // No additional properties needed
    }

    public class UpdateVpnProtocolRequest : VpnProtocolRequest
    {
        [Required(ErrorMessage = "Protocol ID is required")]
        public string Id { get; set; } = string.Empty;
    }
}