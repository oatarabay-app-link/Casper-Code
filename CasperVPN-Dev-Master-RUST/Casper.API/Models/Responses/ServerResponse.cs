using Casper.API.Models;

namespace Casper.API.Models.Responses
{
    public class ServerResponse
    {
        public class ServerSettings
        {
            public int MaxUsers { get; set; }
            public int ConnectionTimeout { get; set; }
            public int HealthCheckInterval { get; set; }
            public Enums.ServerStatus ServerStatus { get; set; }
            public bool AutoProvision { get; set; }
        }
    }
}
