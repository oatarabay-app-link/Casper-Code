using Casper.API.Models.Common;
using Casper.API.Models.Responses;
using Microsoft.AspNetCore.Mvc;

namespace Casper.API.Controllers.v1
{
    public abstract class BaseController : Controller
    {
        // New method for handling validation errors
        protected IActionResult ValidationError(List<string> errors)
        {
            return BadRequest(Result<object>.Failure(
                "Validation failed",
                ErrorType.Validation
            ));
        }

        // For ServiceResponse (legacy)
        protected IActionResult CreateResponse(ServiceResponse result)
        {
            if (result.Status >= 200 && result.Status < 300)
            {
                if (result.Pagination != null)
                {
                    return Ok(new
                    {
                        result.Data,
                        result.Pagination
                    });
                }
                return Ok(result.Data);
            }

            return StatusCode(result.Status, new { message = result.Message, data = result.Data });
        }

        // For Result<T> pattern
        protected IActionResult HandleResult<T>(Result<T> result)
        {
            if (result.IsSuccess)
            {
                return Ok(result.Data);
            }

            return result.ErrorType switch
            {
                ErrorType.Unauthorized => Unauthorized(new { message = result.ErrorMessage, errors = result.Errors }),
                ErrorType.NotFound => NotFound(new { message = result.ErrorMessage, errors = result.Errors }),
                ErrorType.Conflict => Conflict(new { message = result.ErrorMessage, errors = result.Errors }),
                ErrorType.Validation => BadRequest(new { message = result.ErrorMessage, errors = result.Errors }),
                ErrorType.InternalError => StatusCode(500, new { message = result.ErrorMessage, errors = result.Errors }),
                _ => BadRequest(new { message = result.ErrorMessage, errors = result.Errors })
            };
        }

        // For Result (non-generic) pattern
        protected IActionResult HandleResult(Result result)
        {
            if (result.IsSuccess)
            {
                return Ok(new { message = "Operation completed successfully" });
            }

            return result.ErrorType switch
            {
                ErrorType.Unauthorized => Unauthorized(new { message = result.ErrorMessage, errors = result.Errors }),
                ErrorType.NotFound => NotFound(new { message = result.ErrorMessage, errors = result.Errors }),
                ErrorType.Conflict => Conflict(new { message = result.ErrorMessage, errors = result.Errors }),
                ErrorType.Validation => BadRequest(new { message = result.ErrorMessage, errors = result.Errors }),
                ErrorType.InternalError => StatusCode(500, new { message = result.ErrorMessage, errors = result.Errors }),
                _ => BadRequest(new { message = result.ErrorMessage, errors = result.Errors })
            };
        }

        // Async versions
        protected async Task<IActionResult> HandleResultAsync<T>(Task<Result<T>> task)
        {
            var result = await task;
            return HandleResult(result);
        }

        protected async Task<IActionResult> HandleResultAsync(Task<Result> task)
        {
            var result = await task;
            return HandleResult(result);
        }
    }
}