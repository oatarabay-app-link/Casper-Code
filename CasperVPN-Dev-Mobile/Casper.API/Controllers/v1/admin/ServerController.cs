using System.ComponentModel.DataAnnotations;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;

namespace Casper.API.Controllers.v1.admin
{
    //[Authorize(Policy = "Admin")]
    public class ServerController : BaseAdminController
    {
        private readonly IServerService _serverServices;
        private readonly ILogger<ServerController> _logger;

        public ServerController(IServerService serverServices, ILogger<ServerController> logger)
        {
            _serverServices = serverServices;
            _logger = logger;
        }

        [HttpPost]
        [SwaggerOperation(
            Summary = "Create Server",
            Description = "Create a new VPN server"
        )]
        public async Task<IActionResult> Create([FromBody] CreateServerRequest request)
        {
            try
            {
                // Validate ModelState (data annotations)
                if (!ModelState.IsValid)
                {
                    var errors = ModelState.Values
                        .SelectMany(v => v.Errors)
                        .Select(e => e.ErrorMessage)
                        .ToList();

                    _logger.LogWarning("Model validation failed for CreateServer: {Errors}", string.Join(", ", errors));

                    // Join errors into a single message
                    var errorMessage = string.Join("; ", errors);
                    return BadRequest(Result<object>.Failure(
                        errorMessage,
                        ErrorType.Validation));
                }

                // Perform custom validation (IValidatableObject)
                var validationContext = new ValidationContext(request);
                var validationResults = new List<ValidationResult>();

                if (!Validator.TryValidateObject(request, validationContext, validationResults, true))
                {
                    var customErrors = validationResults.Select(vr => vr.ErrorMessage).ToList();
                    _logger.LogWarning("Custom validation failed for CreateServer: {Errors}", string.Join(", ", customErrors));

                    // Join errors into a single message
                    var errorMessage = string.Join("; ", customErrors);
                    return BadRequest(Result<object>.Failure(
                        errorMessage,
                        ErrorType.Validation));
                }

                var response = await _serverServices.CreateServerAsync(request);
                return HandleResult(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error creating server");
                return StatusCode(500, Result<object>.Failure(
                    "An unexpected error occurred while creating the server",
                    ErrorType.InternalError));
            }
        }
        [HttpPut]
        [SwaggerOperation(
      Summary = "Update Server",
      Description = "Update an existing VPN server. Only provide fields you want to update."
  )]
        public async Task<IActionResult> Update([FromBody] UpdateServerRequest request)
        {
            try
            {
                // Basic ModelState validation (only validates fields that are provided)
                if (!ModelState.IsValid)
                {
                    var errors = ModelState.Values
                        .SelectMany(v => v.Errors)
                        .Select(e => e.ErrorMessage)
                        .ToList();

                    _logger.LogWarning("Model validation failed for UpdateServer: {Errors}", string.Join(", ", errors));

                    var errorMessage = string.Join("; ", errors);
                    return BadRequest(Result<object>.Failure(
                        errorMessage,
                        ErrorType.Validation));
                }

                // Perform custom validation (IValidatableObject)
                var validationContext = new ValidationContext(request);
                var validationResults = new List<ValidationResult>();

                if (!Validator.TryValidateObject(request, validationContext, validationResults, true))
                {
                    var customErrors = validationResults.Select(vr => vr.ErrorMessage).ToList();
                    _logger.LogWarning("Custom validation failed for UpdateServer: {Errors}", string.Join(", ", customErrors));

                    var errorMessage = string.Join("; ", customErrors);
                    return BadRequest(Result<object>.Failure(
                        errorMessage,
                        ErrorType.Validation));
                }

                // Additional check: Ensure at least one update field is provided
                if (!request.HasAnyUpdateField())
                {
                    _logger.LogWarning("No update fields provided for server ID: {ServerId}", request.Id);
                    return BadRequest(Result<object>.Failure(
                        "At least one field must be provided for update (ServerName, Location, IPAddress, Username, Password, Sshkey, or ServerStatus)",
                        ErrorType.Validation));
                }

                var response = await _serverServices.UpdateServerAsync(request);
                return HandleResult(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating server");
                return StatusCode(500, Result<object>.Failure(
                    "An unexpected error occurred while updating the server",
                    ErrorType.InternalError));
            }
        }

        [HttpGet]
        [SwaggerOperation(
            Summary = "Get All Servers",
            Description = "Get paginated list of all VPN servers"
        )]
        public async Task<IActionResult> GetAll(
            [FromQuery, Range(1, int.MaxValue, ErrorMessage = "Page number must be greater than 0")]
            int pageNumber = 1,

            [FromQuery, Range(1, 100, ErrorMessage = "Page size must be between 1 and 100")]
            int pageSize = 10)
        {
            try
            {
                // Validate query parameters using ModelState
                if (!ModelState.IsValid)
                {
                    var errors = ModelState.Values
                        .SelectMany(v => v.Errors)
                        .Select(e => e.ErrorMessage)
                        .ToList();

                    _logger.LogWarning("Query parameter validation failed: {Errors}", string.Join(", ", errors));

                    // Join errors into a single message
                    var errorMessage = string.Join("; ", errors);
                    return BadRequest(Result<object>.Failure(
                        errorMessage,
                        ErrorType.Validation));
                }

                var response = await _serverServices.GetServersAsync(pageNumber, pageSize);
                return HandleResult(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving servers");
                return StatusCode(500, Result<object>.Failure(
                    "An unexpected error occurred while retrieving servers",
                    ErrorType.InternalError));
            }
        }

        [HttpDelete("{id}")]
        [SwaggerOperation(
            Summary = "Delete Server",
            Description = "Delete a VPN server by ID"
        )]
        public async Task<IActionResult> Delete(
            [FromRoute, Required(ErrorMessage = "Server ID is required")]
            [RegularExpression(@"^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$",
                ErrorMessage = "Invalid server ID format")]
            string id)
        {
            try
            {
                // Validate route parameter using ModelState
                if (!ModelState.IsValid)
                {
                    var errors = ModelState.Values
                        .SelectMany(v => v.Errors)
                        .Select(e => e.ErrorMessage)
                        .ToList();

                    _logger.LogWarning("Route parameter validation failed: {Errors}", string.Join(", ", errors));

                    // Join errors into a single message
                    var errorMessage = string.Join("; ", errors);
                    return BadRequest(Result<object>.Failure(
                        errorMessage,
                        ErrorType.Validation));
                }

                var response = await _serverServices.DeleteServerAsync(id);
                return HandleResult(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error deleting server");
                return StatusCode(500, Result<object>.Failure(
                    "An unexpected error occurred while deleting the server",
                    ErrorType.InternalError));
            }
        }
    }
}