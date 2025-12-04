using System.ComponentModel.DataAnnotations;

namespace Casper.API.Models.Requests.DiscountRequest
{
    public class CreateDiscountRequest
    {
        [Required(ErrorMessage = "Discount name is required")]
        public string Name { get; set; } = string.Empty;

        [Required(ErrorMessage = "Plan is required")]
        public string Plan { get; set; } = string.Empty;

        [Required(ErrorMessage = "Type is required")]
        [RegularExpression("Percent|Amount", ErrorMessage = "Type must be 'Percent' or 'Amount'")]
        public string Type { get; set; } = string.Empty;

        [Range(0.01, double.MaxValue, ErrorMessage = "Value must be greater than 0")]
        public decimal Value { get; set; }

        [Required(ErrorMessage = "Status is required")]
        public string Status { get; set; } = string.Empty;
    }

    public class UpdateDiscountRequest : IValidatableObject
    {
        [Required(ErrorMessage = "Discount ID is required")]
        public string Id { get; set; } = string.Empty;

        public string? Name { get; set; }

        public string? Plan { get; set; }

        [RegularExpression("Percent|Amount", ErrorMessage = "Type must be 'Percent' or 'Amount'")]
        public string? Type { get; set; }

        [Range(0.01, double.MaxValue, ErrorMessage = "Value must be greater than 0")]
        public decimal? Value { get; set; }

        public string? Status { get; set; }

        public IEnumerable<ValidationResult> Validate(ValidationContext validationContext)
        {
            var results = new List<ValidationResult>();

            // Check if at least one field (other than Id) is provided for update
            bool hasAnyUpdateField = !string.IsNullOrWhiteSpace(Name) ||
                                   !string.IsNullOrWhiteSpace(Plan) ||
                                   !string.IsNullOrWhiteSpace(Type) ||
                                   Value.HasValue ||
                                   !string.IsNullOrWhiteSpace(Status);

            if (!hasAnyUpdateField)
            {
                results.Add(new ValidationResult("At least one field must be provided for update (Name, Plan, Type, Value, or Status)"));
            }

            return results;
        }

        // Helper method to check if any field has been provided
        public bool HasAnyUpdateField()
        {
            return !string.IsNullOrWhiteSpace(Name) ||
                   !string.IsNullOrWhiteSpace(Plan) ||
                   !string.IsNullOrWhiteSpace(Type) ||
                   Value.HasValue ||
                   !string.IsNullOrWhiteSpace(Status);
        }
    }
}