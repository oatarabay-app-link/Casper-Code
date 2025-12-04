namespace Casper.API.Models
{
    namespace Casper.API.Models
    {
        public class Connection : BaseModel
        {
            public Guid Id { get; set; }
            public string UserId { get; set; } = string.Empty;
            public string ServerId { get; set; }
            public string Location { get; set; }
            public DateTime UserConnectTime { get; set; }
            public double Upload { get; set; }
            public double Download { get; set; }
            public Enums.ConnectionStatus ConnectionStatus { get; set; }
            public string ProtocolId { get; set; }
        }



        public class MobileConnection: BaseModel
        {
            public string PublicKey { get; set; } = string.Empty;
            public string AllowIps { get; set; } = string.Empty;
            public string EndPoint { get; set; } = string.Empty;
            public int PersistentKeepAlive { get; set; } = 25;
            public DateTime? LastHandshake { get; set; }
            public string Status { get; set; } = "Inactive";
        }




    }
}
