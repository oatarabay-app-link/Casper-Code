namespace Casper.API.Models
{
    namespace Casper.API.Models
    {
        public class Server : BaseModel
        {
            public Guid Id { get; set; }
            public string ServerName { get; set; }


            public Enums.ConnectionProtocol Connection_Protocol { get; set; }

            public string Location { get; set; }
            public Enums.ServerStatus Server_Status { get; set; }
            public int ConnectedUsers { get; set; }

            public int MaxUsers { get; set; }

            public int ConnectionTimeout { get; set; } // in seconds

            public int HealthCheckInterval { get; set; } // in seconds

            public int Load { get; set; }
            public string IPAddress { get; set; }
        }
    }
}
