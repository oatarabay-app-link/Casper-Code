namespace Casper.API.Models
{
    namespace Casper.API.Models
    {
        public class Connection
        {
            public Guid Id { get; set; }
            public Guid UserId { get; set; }
            public Guid ServerId { get; set; }
            public string Location { get; set; }
            public Enums.ConnectionProtocol Connection_Protocol { get; set; }
            public DateTime UserConnectTime { get; set; }
            public double Upload { get; set; } 
            public double Download { get; set; }
            public Enums.ConnectionStatus Connection_Status { get; set; }
        }
    }
}
