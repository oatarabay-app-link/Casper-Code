namespace Casper.API.Models
{
    public class SeasonalDeal :BaseModel
    {
        public Guid Id { get; set; }
        public string Name { get; set; } = string.Empty;
        public string Description { get; set; } = string.Empty;
        public string Type { get; set; } = string.Empty; // "Percent" or "Amount"
        public decimal Value { get; set; }
        public DateTime StartDate { get; set; }
        public DateTime EndDate { get; set; }
        public string AppliesTo { get; set; } = string.Empty; // "all" or comma-separated plans
        public string Status { get; set; } = string.Empty; // "Active", "Scheduled"
    }
}
