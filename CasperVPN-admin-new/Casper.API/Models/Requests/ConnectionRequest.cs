using System.ComponentModel.DataAnnotations;
using static Casper.API.Models.Enums;

namespace Casper.API.Models.Requests
{

    public class CreateConnectionRequest
    {
        public string UserId { get; set; }

        public string ServerId { get; set; }

        public string Location { get; set; } = string.Empty;

        public string ProtocolId { get; set; }

        //public double Upload { get; set; }
        //public double Download { get; set; }
    }

    public class UpdateConnectionRequest
    {
        public string Id { get; set; }

        public string? UserId { get; set; }
        public string? ServerId { get; set; }
        public string? Location { get; set; }
        public double? Upload { get; set; }
        public double? Download { get; set; }
        public ConnectionStatus? ConnectionStatus { get; set; }
        public string? ProtocolId { get; set; }
    }


    public class CreateMobileConnectionRequest
    {
        public string ClientPublicKey { get; set; } = string.Empty;
        public string DeviceName { get; set; } = string.Empty;
        public string AllowedIPs { get; set; } = "0.0.0.0/0";
        public string? Description { get; set; }
    }

    public class MobileConnectionResponse
    {
        public string ClientPublicKey { get; set; } = string.Empty;
        public string ServerPublicKey { get; set; } = string.Empty;
        public string ServerEndpoint { get; set; } = string.Empty;
        public string ClientConfig { get; set; } = string.Empty; // Just the string config
        public string QRCodeBase64 { get; set; } = string.Empty;
        public DateTime ExpiresAt { get; set; }
    }



}
