namespace Casper.API.Models.Requests.DiscountRequest
{
    public class CreateDiscountRequest
    {
        public string Name { get; set; } = string.Empty;
        public string Plan { get; set; } = string.Empty;
        public string Type { get; set; } = string.Empty;
        public decimal Value { get; set; }
        public string Status { get; set; } = string.Empty;
    }

    public class UpdateDiscountRequest
    {
        public string Id { get; set; } = string.Empty;
        public string? Name { get; set; }
        public string? Plan { get; set; }
        public string? Type { get; set; }
        public decimal? Value { get; set; }
        public string? Status { get; set; }
    }
}
