using Casper.API.Interfaces;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;
using FluentValidation;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Casper.API.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
   
    public class AuthController : BaseController
    {
        private readonly IAuthService _authService;
        private readonly IValidator<UserRequest.Login> _loginValidator;
        private readonly IValidator<UserRequest.CreateUser> _createUserValidator;

        public AuthController(
            IAuthService authService,
            IValidator<UserRequest.Login> loginValidator,
            IValidator<UserRequest.CreateUser> createUserValidator)
        {
            _authService = authService;
            _loginValidator = loginValidator;
            _createUserValidator = createUserValidator;
        }

        [HttpPost("register")]
        [AllowAnonymous]
        public async Task<IActionResult> Register([FromBody] UserRequest.CreateUser model)
        {
            // Validate request
            var validationResult = await _createUserValidator.ValidateAsync(model);
            if (!validationResult.IsValid)
            {
                var errors = validationResult.Errors.Select(e => e.ErrorMessage).ToList();
                var result = Result<CreateUserResponse>.ValidationFailure(errors);
                return HandleResult(result);
            }

            return await HandleResultAsync(_authService.CreateUserAsync(model));
        }

        [HttpPost("login")]
        [AllowAnonymous]
        public async Task<IActionResult> Login([FromBody] UserRequest.Login model)
        {
            // Validate request
            var validationResult = await _loginValidator.ValidateAsync(model);
            if (!validationResult.IsValid)
            {
                var errors = validationResult.Errors.Select(e => e.ErrorMessage).ToList();
                var result = Result<LoginResponse>.ValidationFailure(errors);
                return HandleResult(result);
            }

            return await HandleResultAsync(_authService.LoginUserAsync(model));
        }


        // GET User APIs
        [HttpGet("users")]
        [Authorize(Policy = "Admin")]
        public async Task<IActionResult> GetUsers(
            [FromQuery] int pageNumber = 1,
            [FromQuery] int pageSize = 10)
        {
            return await HandleResultAsync(_authService.GetUsersAsync(pageNumber, pageSize));
        }


        [HttpPost("revoke/{userId}")]
        [Authorize(Policy = "Admin")]
        public async Task<IActionResult> Revoke(string userId)
        {
            return await HandleResultAsync(_authService.RevokeRefreshToken(userId));
        }

        [HttpPut("update")]
        [Authorize(Policy = "Admin")]
        public async Task<IActionResult> Update([FromBody] UserRequest.UpdateUser model)
        {
            return await HandleResultAsync(_authService.UpdateUserAsync(model));
        }

        [HttpDelete("delete/{id}")]
        [Authorize(Policy = "Admin")]
        public async Task<IActionResult> Delete(string id)
        {
            return await HandleResultAsync(_authService.DeleteUserAsync(id));
        }

        [HttpPost("change-password")]
        [Authorize]
        public async Task<IActionResult> ChangePassword([FromBody] UserRequest.ChangePassword model)
        {
            return await HandleResultAsync(_authService.ChangePasswordAsync(model));
        }

        [HttpPost("refresh-token")]
        [AllowAnonymous]
        public async Task<IActionResult> RefreshToken([FromBody] RefreshRequest model)
        {
            return await HandleResultAsync(_authService.RefreshTokenAsync(model.AccessToken, model.RefreshToken));
        }
    }

   
}
