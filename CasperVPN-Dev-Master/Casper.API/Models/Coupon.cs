namespace Casper.API.Models
{
    public class Coupon:BaseModel
    {
        public Guid Id { get; set; }
        public string Code { get; set; } = string.Empty;
        public string Description { get; set; } = string.Empty;
        public string Type { get; set; } = string.Empty; // "Percent" or "Amount"
        public decimal Value { get; set; }
        public DateTime ValidFrom { get; set; }
        public DateTime ValidTo { get; set; }
        public int UsageLimit { get; set; }
        public int UsedCount { get; set; }
        public string Status { get; set; } = string.Empty; // "Active", "Scheduled", "Expired"
    }
}
