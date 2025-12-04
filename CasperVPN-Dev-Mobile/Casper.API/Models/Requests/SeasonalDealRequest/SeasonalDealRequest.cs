namespace Casper.API.Models.Requests.SeasonalDealRequest
{
    public class CreateSeasonalDealRequest
    {
        public string Name { get; set; } = string.Empty;
        public string Description { get; set; } = string.Empty;
        public string Type { get; set; } = string.Empty;
        public decimal Value { get; set; }
        public DateTime StartDate { get; set; }
        public DateTime EndDate { get; set; }
        public string AppliesTo { get; set; } = string.Empty;
        public string Status { get; set; } = string.Empty;
    }

    public class UpdateSeasonalDealRequest
    {
        public string Id { get; set; } = string.Empty;
        public string? Name { get; set; }
        public string? Description { get; set; }
        public string? Type { get; set; }
        public decimal? Value { get; set; }
        public DateTime? StartDate { get; set; }
        public DateTime? EndDate { get; set; }
        public string? AppliesTo { get; set; }
        public string? Status { get; set; }
    }
}
