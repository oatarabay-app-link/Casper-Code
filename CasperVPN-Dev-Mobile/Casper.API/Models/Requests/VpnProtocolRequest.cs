using System.ComponentModel.DataAnnotations;

namespace Casper.API.Models.Requests
{

    public abstract class VpnProtocolRequest
    {

        public string Protocol { get; set; } = string.Empty;

        public bool IsActive { get; set; } = true; // Default to active

    }
    public class CreateVpnProtocolRequest:VpnProtocolRequest
    {
       
    }

    public class UpdateVpnProtocolRequest:VpnProtocolRequest
    {
        public string Id { get; set; }


    }
}
