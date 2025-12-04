//using Casper.API.Models.Requests.SubscriptionRequest;
//using Casper.API.Models.Responses;
//using Casper.API.Services.Interfaces;
//using Microsoft.AspNetCore.Authorization;
//using Microsoft.AspNetCore.Mvc;
//using Swashbuckle.AspNetCore.Annotations;

//namespace Casper.API.Controllers.v1.admin
//{
//    //[Authorize(Policy = "Admin")]
//    public class SubscriptionController : BaseAdminController
//    {
//        private readonly ISubscriptionService _subscriptionService;

//        public SubscriptionController(ISubscriptionService subscriptionService)
//        {
//            _subscriptionService = subscriptionService;
//        }

//        [HttpPost]
//        [SwaggerOperation(
//            Summary = "Create Subscription",
//            Description = "Create a new subscription for a user"
//        )]
//        public async Task<IActionResult> Create([FromBody] CreateSubscriptionRequest request)
//        {
//            var response = await _subscriptionService.CreateSubscriptionAsync(request);
//            return HandleResult(response);
//        }

//        [HttpPut]
//        [SwaggerOperation(
//            Summary = "Update Subscription",
//            Description = "Update an existing subscription (upgrade/downgrade package or change status)"
//        )]
//        public async Task<IActionResult> Update([FromBody] UpdateSubscriptionRequest request)
//        {
//            var response = await _subscriptionService.UpdateSubscriptionAsync(request);
//            return HandleResult(response);
//        }

//        [HttpGet]
//        [SwaggerOperation(
//            Summary = "Get All Subscriptions",
//            Description = "Get paginated list of all subscriptions. Optionally filter by user ID."
//        )]
//        public async Task<IActionResult> GetAll(
//            [FromQuery] string userId = null,
//            [FromQuery] int pageNumber = 1,
//            [FromQuery] int pageSize = 10)
//        {
//            var response = await _subscriptionService.GetSubscriptionsAsync(userId, pageNumber, pageSize);
//            return HandleResult(response);
//        }

//        [HttpDelete("{id}")]
//        [SwaggerOperation(
//            Summary = "Cancel Subscription",
//            Description = "Cancel a subscription by ID"
//        )]
//        public async Task<IActionResult> Cancel(string id)
//        {
//            var response = await _subscriptionService.CancelSubscriptionAsync(id);
//            return HandleResult(response);
//        }
//    }
//}