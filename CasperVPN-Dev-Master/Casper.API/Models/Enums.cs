namespace Casper.API.Models
{
    public class Enums
    {

        // Enums/SubscriptionStatus.cs
        public enum SubscriptionStatus
        {
            Active,
            Cancelled,
            Expired
        }
        public enum ConnectionStatus
        {
            Connected,
            Disconnected
        }
        public enum ConnectionProtocol
        {
            OpenVPN,
            WireGuard,
            IKEv2
        }

        public enum ServerStatus
        {
            Online = 1,      // Make sure this exists
            Active = 2,
            Inactive = 3,
            Maintenance = 4,
            Offline = 5
        }

        public enum UserPlan
        {
            Premium,
            Basic,
            Standard
        }

        public enum ProtocolStatus
        {
            Active,
            Suspended,
            Inactive
        }

    }
}
