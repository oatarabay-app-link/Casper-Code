using Casper.API.Models.Requests.PackageRequest;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;

namespace Casper.API.Controllers.v1.admin
{
    //[Authorize(Policy = "Admin")]
    public class PackageController : BaseAdminController
    {
        private readonly IPackageService _packageService;

        public PackageController(IPackageService packageService)
        {
            _packageService = packageService;
        }

        [HttpPost]
        [SwaggerOperation(
            Summary = "Create Package",
            Description = "Create a new VPN package"
        )]
        public async Task<IActionResult> Create([FromBody] CreatePackageRequest request)
        {
            var response = await _packageService.CreatePackageAsync(request);
            return HandleResult(response);
        }

        [HttpPut]
        [SwaggerOperation(
            Summary = "Update Package",
            Description = "Update an existing VPN package"
        )]
        public async Task<IActionResult> Update([FromBody] UpdatePackageRequest request)
        {
            var response = await _packageService.UpdatePackageAsync(request);
            return HandleResult(response);
        }

        [HttpGet]
        [SwaggerOperation(
            Summary = "Get All Packages",
            Description = "Get paginated list of all VPN packages"
        )]
        public async Task<IActionResult> GetAll([FromQuery] int pageNumber = 1, [FromQuery] int pageSize = 10)
        {
            var response = await _packageService.GetPackagesAsync(pageNumber, pageSize);
            return HandleResult(response);
        }

        [HttpDelete("{id}")]
        [SwaggerOperation(
            Summary = "Delete Package",
            Description = "Delete a VPN package by ID"
        )]
        public async Task<IActionResult> Delete(string id)
        {
            var response = await _packageService.DeletePackageAsync(id);
            return HandleResult(response);
        }
    }
}