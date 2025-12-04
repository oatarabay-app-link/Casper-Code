using System.ComponentModel.DataAnnotations;

namespace Casper.API.Models.Requests.CouponRequest
{
    public class CreateCouponRequest
    {
        [Required(ErrorMessage = "Coupon code is required")]
        [MaxLength(50, ErrorMessage = "Coupon code cannot exceed 50 characters")]
        public string Code { get; set; } = string.Empty;

        [Required(ErrorMessage = "Description is required")]
        public string Description { get; set; } = string.Empty;

        [Required(ErrorMessage = "Type is required")]
        [RegularExpression("Percent|Amount", ErrorMessage = "Type must be 'Percent' or 'Amount'")]
        public string Type { get; set; } = string.Empty;

        [Range(0.01, double.MaxValue, ErrorMessage = "Value must be greater than 0")]
        public decimal Value { get; set; }

        [Required(ErrorMessage = "Valid from date is required")]
        public DateTime ValidFrom { get; set; }

        [Required(ErrorMessage = "Valid to date is required")]
        public DateTime ValidTo { get; set; }

        [Range(1, int.MaxValue, ErrorMessage = "Usage limit must be at least 1")]
        public int UsageLimit { get; set; }

        [Required(ErrorMessage = "Status is required")]
        public string Status { get; set; } = string.Empty;
    }

    public class UpdateCouponRequest : IValidatableObject
    {
        [Required(ErrorMessage = "Coupon ID is required")]
        public string Id { get; set; } = string.Empty;

        [MaxLength(50, ErrorMessage = "Coupon code cannot exceed 50 characters")]
        public string? Code { get; set; }

        public string? Description { get; set; }

        [RegularExpression("Percent|Amount", ErrorMessage = "Type must be 'Percent' or 'Amount'")]
        public string? Type { get; set; }

        [Range(0.01, double.MaxValue, ErrorMessage = "Value must be greater than 0")]
        public decimal? Value { get; set; }

        public DateTime? ValidFrom { get; set; }

        public DateTime? ValidTo { get; set; }

        [Range(1, int.MaxValue, ErrorMessage = "Usage limit must be at least 1")]
        public int? UsageLimit { get; set; }

        public string? Status { get; set; }

        public IEnumerable<ValidationResult> Validate(ValidationContext validationContext)
        {
            var results = new List<ValidationResult>();

            // Check if at least one field (other than Id) is provided for update
            bool hasAnyUpdateField = !string.IsNullOrWhiteSpace(Code) ||
                                   !string.IsNullOrWhiteSpace(Description) ||
                                   !string.IsNullOrWhiteSpace(Type) ||
                                   Value.HasValue ||
                                   ValidFrom.HasValue ||
                                   ValidTo.HasValue ||
                                   UsageLimit.HasValue ||
                                   !string.IsNullOrWhiteSpace(Status);

            if (!hasAnyUpdateField)
            {
                results.Add(new ValidationResult("At least one field must be provided for update (Code, Description, Type, Value, ValidFrom, ValidTo, UsageLimit, or Status)"));
            }

            // If both ValidFrom and ValidTo are provided, validate the date range
            if (ValidFrom.HasValue && ValidTo.HasValue && ValidFrom >= ValidTo)
            {
                results.Add(new ValidationResult("Valid From date must be before Valid To date"));
            }

            // If only one date is provided, require both
            if ((ValidFrom.HasValue && !ValidTo.HasValue) || (!ValidFrom.HasValue && ValidTo.HasValue))
            {
                results.Add(new ValidationResult("Both ValidFrom and ValidTo must be provided together"));
            }

            return results;
        }

        // Helper method to check if any field has been provided
        public bool HasAnyUpdateField()
        {
            return !string.IsNullOrWhiteSpace(Code) ||
                   !string.IsNullOrWhiteSpace(Description) ||
                   !string.IsNullOrWhiteSpace(Type) ||
                   Value.HasValue ||
                   ValidFrom.HasValue ||
                   ValidTo.HasValue ||
                   UsageLimit.HasValue ||
                   !string.IsNullOrWhiteSpace(Status);
        }
    }
}