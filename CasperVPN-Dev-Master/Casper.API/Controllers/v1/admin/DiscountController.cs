using Casper.API.Models.Common;
using Casper.API.Models.Requests.DiscountRequest;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;
using static Casper.API.Models.Enums;

namespace Casper.API.Controllers.v1.admin
{
    //[Authorize(Policy = "Admin")]
    public class DiscountController : BaseAdminController
    {
        private readonly IDiscountService _discountService;

        public DiscountController(IDiscountService discountService)
        {
            _discountService = discountService;
        }

        [HttpPost]
        [SwaggerOperation(Summary = "Create Discount", Description = "Create a new plan discount")]
        public async Task<IActionResult> Create([FromBody] CreateDiscountRequest request)
        {
            if (!ModelState.IsValid)
            {
                var errors = ModelState.Values
                    .SelectMany(v => v.Errors)
                    .Select(e => e.ErrorMessage)
                    .ToList();
                return BadRequest(Result<dynamic>.Failure(string.Join("; ", errors), ErrorType.Validation));
            }

            var response = await _discountService.CreateDiscountAsync(request);
            return HandleResult(response);
        }

        [HttpPut]
        [SwaggerOperation(Summary = "Update Discount", Description = "Update an existing plan discount")]
        public async Task<IActionResult> Update([FromBody] UpdateDiscountRequest request)
        {
            if (!ModelState.IsValid)
            {
                var errors = ModelState.Values
                    .SelectMany(v => v.Errors)
                    .Select(e => e.ErrorMessage)
                    .ToList();
                return BadRequest(Result<dynamic>.Failure(string.Join("; ", errors), ErrorType.Validation));
            }

            var response = await _discountService.UpdateDiscountAsync(request);
            return HandleResult(response);
        }

        [HttpGet]
        [SwaggerOperation(Summary = "Get All Discounts", Description = "Get paginated list of all plan discounts")]
        public async Task<IActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 10)
        {
            var response = await _discountService.GetDiscountsAsync(pageNumber, pageSize);
            return HandleResult(response);
        }

        [HttpDelete("{id}")]
        [SwaggerOperation(Summary = "Delete Discount", Description = "Delete a plan discount by ID")]
        public async Task<IActionResult> Delete(string id)
        {
            var response = await _discountService.DeleteDiscountAsync(id);
            return HandleResult(response);
        }
    }
}