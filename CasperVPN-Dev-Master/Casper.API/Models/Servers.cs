namespace Casper.API.Models
{
    namespace Casper.API.Models
    {
        public class Server : BaseModel
        {
            public Guid Id { get; set; }
            public string ServerName { get; set; } = string.Empty;
            public string Location { get; set; } = string.Empty;
            public Enums.ServerStatus Server_Status { get; set; }
         
            public string IpAddress { get; set; } = string.Empty;

            // Authentication fields
            public string Username { get; set; } // New username field
            public string? Password { get; set; }
            public string? Sshkey { get; set; }

        }
    }
}
