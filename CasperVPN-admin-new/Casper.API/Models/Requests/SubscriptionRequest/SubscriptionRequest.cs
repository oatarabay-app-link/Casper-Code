using static Casper.API.Models.Enums;

namespace Casper.API.Models.Requests.SubscriptionRequest
{
    public class CreateSubscriptionRequest
    {
        public string UserId { get; set; }
        public string PackageId { get; set; }
    }

    public class UpdateSubscriptionRequest
    {
        public string Id { get; set; }
        public string PackageId { get; set; }
        public SubscriptionStatus? Status { get; set; }
    }
}
