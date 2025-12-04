using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Security.Cryptography;
using Casper.API.Models;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using FluentValidation;
using Microsoft.AspNetCore.Authentication;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;

namespace Casper.API.Controllers.v1.auth
{
    [ApiController]
    [ApiVersion("1.0")]

    [Route("api/v{version:apiVersion}/[controller]")]
    [ApiExplorerSettings(GroupName = "v1")]
    public class AuthController : BaseController
    {
        private readonly IAuthService _authService;
        private readonly IValidator<LoginRequest> _loginValidator;
        private readonly IValidator<CreateUserRequest> _createUserValidator;
        private readonly UserManager<Users> _userManager;
        private readonly SignInManager<Users> _signInManager;

        public AuthController(
            IAuthService authService,
            IValidator<LoginRequest> loginValidator,
            IValidator<CreateUserRequest> createUserValidator,
            UserManager<Users> userManager,
            SignInManager<Users> signInManager)
        {
            _authService = authService;
            _loginValidator = loginValidator;
            _createUserValidator = createUserValidator;
            _userManager = userManager;
            _signInManager = signInManager;
        }

        // =======================
        // GOOGLE OAUTH
        // =======================

        [HttpGet("oauth/google")]
        [AllowAnonymous]
        public IActionResult GoogleOAuth([FromQuery] string returnUrl = "/")
        {
            try
            {
                var state = GenerateState();
                HttpContext.Session.SetString("oauth_state", state);

                var callbackUrl = Url.Action("GoogleOAuthCallback", "Auth", null, Request.Scheme);

                var authUrl = _authService.GetGoogleAuthUrl(callbackUrl, state);
                return Redirect(authUrl);
            }
            catch (Exception ex)
            {
                return BadRequest(new { error = "OAuth initiation failed", message = ex.Message });
            }
        }


        [HttpGet("oauth/google/callback")]
        [AllowAnonymous]
        public async Task<IActionResult> GoogleOAuthCallback(
            [FromQuery] string code,
            [FromQuery] string state,
            [FromQuery] string error = null,
            [FromQuery] string error_description = null)
        {
            try
            {
                if (!string.IsNullOrEmpty(error))
                {
                    var errorMessage = string.IsNullOrEmpty(error_description)
                        ? error
                        : error_description;

                    var result = Result<LoginResponse>.Failure(
                        $"OAuth failed: {errorMessage}",
                        ErrorType.Unauthorized
                    );

                    return HandleResult(result);
                }

                if (string.IsNullOrEmpty(code))
                {
                    var result = Result<LoginResponse>.Failure(
                        "Authorization code not received from Google",
                        ErrorType.BadRequest
                    );

                    return HandleResult(result);
                }

                var storedState = HttpContext.Session.GetString("oauth_state");
                if (!string.IsNullOrEmpty(storedState) && state != storedState)
                {
                    var result = Result<LoginResponse>.Failure(
                        "State parameter mismatch",
                        ErrorType.Unauthorized
                    );

                    return HandleResult(result);
                }

                if (!string.IsNullOrEmpty(storedState))
                {
                    HttpContext.Session.Remove("oauth_state");
                }

                var callbackUrl = Url.Action("GoogleOAuthCallback", "Auth", null, Request.Scheme);

                var oauthResult = await _authService.HandleGoogleOAuthAsync(code, callbackUrl);
                return HandleResult(oauthResult);
            }
            catch (Exception ex)
            {
                var result = Result<LoginResponse>.Failure(
                    $"OAuth callback failed: {ex.Message}",
                    ErrorType.InternalError
                );

                return HandleResult(result);
            }
        }


        // =======================
        // REGISTER
        // =======================
        [HttpPost("register")]
        [AllowAnonymous]
        public async Task<IActionResult> Register([FromBody] CreateUserRequest model)
        {
            var validationResult = await _createUserValidator.ValidateAsync(model);

            if (!validationResult.IsValid)
            {
                var errors = validationResult.Errors.Select(e => e.ErrorMessage).ToList();
                var result = Result<CreateUserResponse>.ValidationFailure(errors);
                return HandleResult(result);
            }

            return await HandleResultAsync(_authService.CreateUserAsync(model));
        }


        // =======================
        // LOGIN
        // =======================
        [HttpPost("login")]
        [AllowAnonymous]
        public async Task<IActionResult> Login([FromBody] LoginRequest model)
        {
            var validationResult = await _loginValidator.ValidateAsync(model);

            if (!validationResult.IsValid)
            {
                var errors = validationResult.Errors.Select(e => e.ErrorMessage).ToList();
                var result = Result<LoginResponse>.ValidationFailure(errors);
                return HandleResult(result);
            }

            return await HandleResultAsync(_authService.LoginUserAsync(model));
        }


        // =======================
        // USER MANAGEMENT (ADMIN ONLY)
        // =======================
        [HttpGet("users")]
       // [Authorize(Policy = "Admin")]
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
        public async Task<IActionResult> Update([FromBody] UpdateUserRequest model)
        {
            return await HandleResultAsync(_authService.UpdateUserAsync(model));
        }

        [HttpDelete("delete/{id}")]
        [Authorize(Policy = "Admin")]
        public async Task<IActionResult> Delete(string id)
        {
            return await HandleResultAsync(_authService.DeleteUserAsync(id));
        }


        // =======================
        // USER FUNCTIONS
        // =======================
        [HttpPost("change-password")]
        [Authorize]
        public async Task<IActionResult> ChangePassword([FromBody] ChangePasswordRequest model)
        {
            return await HandleResultAsync(_authService.ChangePasswordAsync(model));
        }

        [HttpPost("refresh-token")]
        [AllowAnonymous]
        public async Task<IActionResult> RefreshToken([FromBody] RefreshRequest model)
        {
            return await HandleResultAsync(
                _authService.RefreshTokenAsync(model.AccessToken, model.RefreshToken)
            );
        }

        [HttpPost("logout")]
        [Authorize]
        public async Task<IActionResult> Logout()
        {
            try
            {
                await _signInManager.SignOutAsync();
                return Ok(new { message = "Logged out successfully" });
            }
            catch
            {
                return StatusCode(500, new { error = "Failed to logout" });
            }
        }


        // =======================
        // HEALTH CHECK
        // =======================
        [HttpGet("health-check")]
        [AllowAnonymous]
        public IActionResult HealthCheck()
        {
            return Ok(new { status = "Healthy", timestamp = DateTime.UtcNow });
        }


        // =======================
        // HELPER
        // =======================
        private string GenerateState()
        {
            var randomBytes = new byte[32];
            using var rng = RandomNumberGenerator.Create();
            rng.GetBytes(randomBytes);

            return Convert.ToBase64String(randomBytes)
                .Replace("+", "-")
                .Replace("/", "_")
                .Replace("=", "");
        }
    }
}
