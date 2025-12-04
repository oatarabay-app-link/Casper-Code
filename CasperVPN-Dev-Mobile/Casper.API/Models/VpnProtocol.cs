using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace Casper.API.Models
{
    public class VpnProtocol:BaseModel
    {
        public Guid Id { get; set; }

        [Column("Protocol")] // Explicitly map to correct column name
        public string Protocol { get; set; } = string.Empty;


    }

    
}
