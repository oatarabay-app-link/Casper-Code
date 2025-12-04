namespace Casper.API.Models.Requests
{
    public abstract class BaseServerRequest
    {
        public string ServerName { get; set; }
        public Enums.ConnectionProtocol ConnectionProtocol { get; set; }
        public string Location { get; set; }
        public Enums.ServerStatus ServerStatus { get; set; }
        public int MaxUsers { get; set; }
        public int ConnectionTimeout { get; set; }
        public int HealthCheckInterval { get; set; }
        public string IPAddress { get; set; }
    }

    public class ServerRequest 
    {
        public class CreateServer : BaseServerRequest
        { }
        
        public class UpdateServer: BaseServerRequest
        {
            public Guid Id { get; set; }
        }

        public class UpdateServerStatus
        {
            public string ServerName { get; set; }
            public Enums.ServerStatus ServerStatus { get; set; }
            public int ConnectedUsers { get; set; }
            public int Load { get; set; }
            public float CpuUsage { get; set; }
            public float MemoryUsage { get; set; }
            public float DiskUsage { get; set; }
        }
    }
}
