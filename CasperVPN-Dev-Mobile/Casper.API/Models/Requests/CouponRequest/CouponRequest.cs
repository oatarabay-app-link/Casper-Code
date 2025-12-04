namespace Casper.API.Models.Requests.CouponRequest
{
    public class CreateCouponRequest
    {
        public string Code { get; set; } = string.Empty;
        public string Description { get; set; } = string.Empty;
        public string Type { get; set; } = string.Empty;
        public decimal Value { get; set; }
        public DateTime ValidFrom { get; set; }
        public DateTime ValidTo { get; set; }
        public int UsageLimit { get; set; }
        public string Status { get; set; } = string.Empty;
    }

    public class UpdateCouponRequest
    {
        public string Id { get; set; } = string.Empty;
        public string? Code { get; set; }
        public string? Description { get; set; }
        public string? Type { get; set; }
        public decimal? Value { get; set; }
        public DateTime? ValidFrom { get; set; }
        public DateTime? ValidTo { get; set; }
        public int? UsageLimit { get; set; }
        public string? Status { get; set; }
    }
}
