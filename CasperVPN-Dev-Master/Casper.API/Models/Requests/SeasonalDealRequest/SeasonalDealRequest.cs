using System.ComponentModel.DataAnnotations;

namespace Casper.API.Models.Requests.SeasonalDealRequest
{
    public class CreateSeasonalDealRequest
    {
        [Required(ErrorMessage = "Deal name is required")]
        public string Name { get; set; } = string.Empty;

        [Required(ErrorMessage = "Description is required")]
        public string Description { get; set; } = string.Empty;

        [Required(ErrorMessage = "Type is required")]
        [RegularExpression("Percent|Amount", ErrorMessage = "Type must be 'Percent' or 'Amount'")]
        public string Type { get; set; } = string.Empty;

        [Range(0.01, double.MaxValue, ErrorMessage = "Value must be greater than 0")]
        public decimal Value { get; set; }

        [Required(ErrorMessage = "Start date is required")]
        public DateTime StartDate { get; set; }

        [Required(ErrorMessage = "End date is required")]
        public DateTime EndDate { get; set; }

        [Required(ErrorMessage = "Applies to is required")]
        public string AppliesTo { get; set; } = string.Empty;

        [Required(ErrorMessage = "Status is required")]
        public string Status { get; set; } = string.Empty;
    }

    public class UpdateSeasonalDealRequest : IValidatableObject
    {
        [Required(ErrorMessage = "Seasonal deal ID is required")]
        public string Id { get; set; } = string.Empty;

        public string? Name { get; set; }

        public string? Description { get; set; }

        [RegularExpression("Percent|Amount", ErrorMessage = "Type must be 'Percent' or 'Amount'")]
        public string? Type { get; set; }

        [Range(0.01, double.MaxValue, ErrorMessage = "Value must be greater than 0")]
        public decimal? Value { get; set; }

        public DateTime? StartDate { get; set; }

        public DateTime? EndDate { get; set; }

        public string? AppliesTo { get; set; }

        public string? Status { get; set; }

        public IEnumerable<ValidationResult> Validate(ValidationContext validationContext)
        {
            var results = new List<ValidationResult>();

            // Check if at least one field (other than Id) is provided for update
            bool hasAnyUpdateField = !string.IsNullOrWhiteSpace(Name) ||
                                   !string.IsNullOrWhiteSpace(Description) ||
                                   !string.IsNullOrWhiteSpace(Type) ||
                                   Value.HasValue ||
                                   StartDate.HasValue ||
                                   EndDate.HasValue ||
                                   !string.IsNullOrWhiteSpace(AppliesTo) ||
                                   !string.IsNullOrWhiteSpace(Status);

            if (!hasAnyUpdateField)
            {
                results.Add(new ValidationResult("At least one field must be provided for update (Name, Description, Type, Value, StartDate, EndDate, AppliesTo, or Status)"));
            }

            // If both StartDate and EndDate are provided, validate the date range
            if (StartDate.HasValue && EndDate.HasValue && StartDate >= EndDate)
            {
                results.Add(new ValidationResult("Start date must be before end date"));
            }

            // If only one date is provided, require both
            if ((StartDate.HasValue && !EndDate.HasValue) || (!StartDate.HasValue && EndDate.HasValue))
            {
                results.Add(new ValidationResult("Both StartDate and EndDate must be provided together"));
            }

            return results;
        }

        // Helper method to check if any field has been provided
        public bool HasAnyUpdateField()
        {
            return !string.IsNullOrWhiteSpace(Name) ||
                   !string.IsNullOrWhiteSpace(Description) ||
                   !string.IsNullOrWhiteSpace(Type) ||
                   Value.HasValue ||
                   StartDate.HasValue ||
                   EndDate.HasValue ||
                   !string.IsNullOrWhiteSpace(AppliesTo) ||
                   !string.IsNullOrWhiteSpace(Status);
        }
    }
}