namespace Casper.API.Models
{
    public class PlanDiscount:BaseModel
    {
        public Guid Id { get; set; }
        public string Name { get; set; } = string.Empty;
        public string Plan { get; set; } = string.Empty; // "MONTHLY", "QUARTERLY", "YEARLY"
        public string Type { get; set; } = string.Empty; // "Percent" or "Amount"
        public decimal Value { get; set; }
        public string Status { get; set; } = string.Empty; // "Active", "Scheduled"
    }
}
