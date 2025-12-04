using Casper.API.Models.Common;
using Casper.API.Models.Requests.CouponRequest;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;
using static Casper.API.Models.Enums;

namespace Casper.API.Controllers.v1.admin
{
    //[Authorize(Policy = "Admin")]
    public class CouponController : BaseAdminController
    {
        private readonly ICouponService _couponService;

        public CouponController(ICouponService couponService)
        {
            _couponService = couponService;
        }

        [HttpPost]
        [SwaggerOperation(Summary = "Create Coupon", Description = "Create a new coupon")]
        public async Task<IActionResult> Create([FromBody] CreateCouponRequest request)
        {
            if (!ModelState.IsValid)
            {
                var errors = ModelState.Values
                    .SelectMany(v => v.Errors)
                    .Select(e => e.ErrorMessage)
                    .ToList();
                return BadRequest(Result<dynamic>.Failure(string.Join("; ", errors), ErrorType.Validation));
            }

            var response = await _couponService.CreateCouponAsync(request);
            return HandleResult(response);
        }

        [HttpPut]
        [SwaggerOperation(Summary = "Update Coupon", Description = "Update an existing coupon")]
        public async Task<IActionResult> Update([FromBody] UpdateCouponRequest request)
        {
            if (!ModelState.IsValid)
            {
                var errors = ModelState.Values
                    .SelectMany(v => v.Errors)
                    .Select(e => e.ErrorMessage)
                    .ToList();
                return BadRequest(Result<dynamic>.Failure(string.Join("; ", errors), ErrorType.Validation));
            }

            var response = await _couponService.UpdateCouponAsync(request);
            return HandleResult(response);
        }

        [HttpGet]
        [SwaggerOperation(Summary = "Get All Coupons", Description = "Get paginated list of all coupons")]
        public async Task<IActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 10)
        {
            var response = await _couponService.GetCouponsAsync(pageNumber, pageSize);
            return HandleResult(response);
        }

        [HttpDelete("{id}")]
        [SwaggerOperation(Summary = "Delete Coupon", Description = "Delete a coupon by ID")]
        public async Task<IActionResult> Delete(string id)
        {
            var response = await _couponService.DeleteCouponAsync(id);
            return HandleResult(response);
        }

        [HttpGet("validate/{code}")]
        [SwaggerOperation(Summary = "Validate Coupon", Description = "Validate a coupon code")]
        public async Task<IActionResult> Validate(string code)
        {
            var response = await _couponService.ValidateCouponAsync(code);
            return HandleResult(response);
        }
    }
}