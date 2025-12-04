namespace Casper.API.Models
{
    public class UserPlans
    {
        public int Id { get; set; }
        public string PlanName { get; set; } // Premium, Basic, Standard, etc.
        public string Description { get; set; }
        public decimal Price { get; set; }
        public int MaxDevices { get; set; }
        public double DataLimit { get; set; } // in GB
        public int SpeedLimit { get; set; } // in Mbps
        public bool IsActive { get; set; }
    }
}
