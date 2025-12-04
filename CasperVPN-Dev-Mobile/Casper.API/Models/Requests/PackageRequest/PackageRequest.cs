namespace Casper.API.Models.Requests.PackageRequest
{
    public class CreatePackageRequest
    {
        public string Name { get; set; }
        public decimal Price { get; set; }
        public string Currency { get; set; }
        public string BillingInterval { get; set; }
        public int DeviceLimit { get; set; }
        public string DataPolicy { get; set; }
        public bool EligibleForAddons { get; set; }
        public bool IsPopular { get; set; }
    }


    public class UpdatePackageRequest
    {
        public string Id { get; set; }
        public string Name { get; set; }
        public decimal? Price { get; set; }
        public string Currency { get; set; }
        public string BillingInterval { get; set; }
        public int? DeviceLimit { get; set; }
        public string DataPolicy { get; set; }
        public bool? EligibleForAddons { get; set; }
        public bool? IsPopular { get; set; }
    }
}
