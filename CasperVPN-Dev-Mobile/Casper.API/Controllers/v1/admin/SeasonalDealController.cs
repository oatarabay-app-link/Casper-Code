// Controllers/v1/admin/SeasonalDealController.cs
using Casper.API.Models.Requests;
using Casper.API.Models.Requests.SeasonalDealRequest;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;

namespace Casper.API.Controllers.v1.admin
{
    //[Authorize(Policy = "Admin")]
    public class SeasonalDealController : BaseAdminController
    {
        private readonly ISeasonalDealService _seasonalDealService;

        public SeasonalDealController(ISeasonalDealService seasonalDealService)
        {
            _seasonalDealService = seasonalDealService;
        }

        [HttpPost]
        [SwaggerOperation(Summary = "Create Seasonal Deal", Description = "Create a new seasonal deal")]
        public async Task<IActionResult> Create([FromBody] CreateSeasonalDealRequest request)
        {
            var response = await _seasonalDealService.CreateSeasonalDealAsync(request);
            return HandleResult(response);
        }

        [HttpPut]
        [SwaggerOperation(Summary = "Update Seasonal Deal", Description = "Update an existing seasonal deal")]
        public async Task<IActionResult> Update([FromBody] UpdateSeasonalDealRequest request)
        {
            var response = await _seasonalDealService.UpdateSeasonalDealAsync(request);
            return HandleResult(response);
        }

        [HttpGet]
        [SwaggerOperation(Summary = "Get All Seasonal Deals", Description = "Get paginated list of all seasonal deals")]
        public async Task<IActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 10)
        {
            var response = await _seasonalDealService.GetSeasonalDealsAsync(pageNumber, pageSize);
            return HandleResult(response);
        }

        [HttpDelete("{id}")]
        [SwaggerOperation(Summary = "Delete Seasonal Deal", Description = "Delete a seasonal deal by ID")]
        public async Task<IActionResult> Delete(string id)
        {
            var response = await _seasonalDealService.DeleteSeasonalDealAsync(id);
            return HandleResult(response);
        }

        [HttpGet("active")]
        [SwaggerOperation(Summary = "Get Active Deals", Description = "Get all currently active seasonal deals")]
        public async Task<IActionResult> GetActive()
        {
            var response = await _seasonalDealService.GetActiveDealsAsync();
            return HandleResult(response);
        }
    }
}