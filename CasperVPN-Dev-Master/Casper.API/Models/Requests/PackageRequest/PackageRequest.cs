using System.ComponentModel.DataAnnotations;

namespace Casper.API.Models.Requests.PackageRequest
{
    public class CreatePackageRequest
    {
        [Required(ErrorMessage = "Package name is required")]
        [MaxLength(100, ErrorMessage = "Package name cannot exceed 100 characters")]
        public string Name { get; set; }

        [Range(0, double.MaxValue, ErrorMessage = "Price cannot be negative")]
        public decimal Price { get; set; }

        [Required(ErrorMessage = "Currency is required")]
        public string Currency { get; set; }

        [Required(ErrorMessage = "Billing interval is required")]
        public string BillingInterval { get; set; }

        [Range(1, int.MaxValue, ErrorMessage = "Device limit must be at least 1")]
        public int DeviceLimit { get; set; }

        [Required(ErrorMessage = "Data policy is required")]
        public string DataPolicy { get; set; }

        public bool EligibleForAddons { get; set; }
        public bool IsPopular { get; set; }
        public string Features { get; set; }
    }

    public class UpdatePackageRequest : IValidatableObject
    {
        [Required(ErrorMessage = "Package ID is required")]
        public string Id { get; set; }

        [MaxLength(100, ErrorMessage = "Package name cannot exceed 100 characters")]
        public string? Name { get; set; }

        [Range(0, double.MaxValue, ErrorMessage = "Price cannot be negative")]
        public decimal? Price { get; set; }

        public string? Currency { get; set; }

        public string? BillingInterval { get; set; }

        [Range(1, int.MaxValue, ErrorMessage = "Device limit must be at least 1")]
        public int? DeviceLimit { get; set; }

        public string? DataPolicy { get; set; }

        public bool? EligibleForAddons { get; set; }
        public bool? IsPopular { get; set; }
        public string? Features { get; set; }

        public IEnumerable<ValidationResult> Validate(ValidationContext validationContext)
        {
            var results = new List<ValidationResult>();

            // Check if at least one field (other than Id) is provided for update
            bool hasAnyUpdateField = !string.IsNullOrWhiteSpace(Name) ||
                                   Price.HasValue ||
                                   !string.IsNullOrWhiteSpace(Currency) ||
                                   !string.IsNullOrWhiteSpace(BillingInterval) ||
                                   DeviceLimit.HasValue ||
                                   !string.IsNullOrWhiteSpace(DataPolicy) ||
                                   EligibleForAddons.HasValue ||
                                   IsPopular.HasValue ||
                                   !string.IsNullOrWhiteSpace(Features);

            if (!hasAnyUpdateField)
            {
                results.Add(new ValidationResult("At least one field must be provided for update (Name, Price, Currency, BillingInterval, DeviceLimit, DataPolicy, EligibleForAddons, IsPopular, or Features)"));
            }

            return results;
        }

        // Helper method to check if any field has been provided
        public bool HasAnyUpdateField()
        {
            return !string.IsNullOrWhiteSpace(Name) ||
                   Price.HasValue ||
                   !string.IsNullOrWhiteSpace(Currency) ||
                   !string.IsNullOrWhiteSpace(BillingInterval) ||
                   DeviceLimit.HasValue ||
                   !string.IsNullOrWhiteSpace(DataPolicy) ||
                   EligibleForAddons.HasValue ||
                   IsPopular.HasValue ||
                   !string.IsNullOrWhiteSpace(Features);
        }
    }
}