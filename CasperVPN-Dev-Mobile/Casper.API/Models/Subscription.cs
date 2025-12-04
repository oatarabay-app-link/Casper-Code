namespace Casper.API.Models
{
    // Models/Subscription.cs
    public class Subscription : BaseModel
    {
        public Guid Id { get; set; }
        public string UserId { get; set; }
        public Guid PackageId { get; set; }
        public string PackageName { get; set; }
        public decimal Price { get; set; }
        public string Currency { get; set; }
        public string BillingCycle { get; set; }
        public int DeviceLimit { get; set; }
        public Enums.SubscriptionStatus Status { get; set; }
    }
}

