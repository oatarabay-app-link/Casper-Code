namespace Casper.API.Models
{
    public class Enums
    {
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
            Online,
            Maintenance,
            Offline
        }

        public enum UserPlan
        {
            Premium,
            Basic,
            Standard
        }

       
    }
}
