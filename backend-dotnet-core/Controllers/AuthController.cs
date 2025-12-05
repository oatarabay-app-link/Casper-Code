using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;
using CasperVPN.DTOs;
using CasperVPN.Services;

namespace CasperVPN.Controllers;

/// <summary>
/// Authentication controller for user registration, login, and token management
/// </summary>
[ApiController]
[Route("api/[controller]")]
[Produces("application/json")]
public class AuthController : ControllerBase
{
    private readonly IAuthService _authService;
    private readonly ILogger<AuthController> _logger;

    public AuthController(IAuthService authService, ILogger<AuthController> logger)
    {
        _authService = authService;
        _logger = logger;
    }

    /// <summary>
    /// Register a new user account
    /// </summary>
    /// <param name="request">Registration details</param>
    /// <returns>Authentication response with tokens</returns>
    [HttpPost("register")]
    [SwaggerOperation(Summary = "Register new user", Description = "Creates a new user account and returns authentication tokens")]
    [SwaggerResponse(200, "Registration successful", typeof(ApiResponse<AuthResponse>))]
    [SwaggerResponse(400, "Invalid request", typeof(ApiResponse))]
    [SwaggerResponse(409, "User already exists", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<AuthResponse>>> Register([FromBody] RegisterRequest request)
    {
        try
        {
            var result = await _authService.RegisterAsync(request);
            return Ok(ApiResponse<AuthResponse>.SuccessResponse(result, "Registration successful"));
        }
        catch (InvalidOperationException ex)
        {
            return Conflict(ApiResponse<AuthResponse>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Registration failed for {Email}", request.Email);
            return BadRequest(ApiResponse<AuthResponse>.ErrorResponse("Registration failed"));
        }
    }

    /// <summary>
    /// Login with email and password
    /// </summary>
    /// <param name="request">Login credentials</param>
    /// <returns>Authentication response with tokens</returns>
    [HttpPost("login")]
    [SwaggerOperation(Summary = "User login", Description = "Authenticates user and returns JWT tokens")]
    [SwaggerResponse(200, "Login successful", typeof(ApiResponse<AuthResponse>))]
    [SwaggerResponse(401, "Invalid credentials", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<AuthResponse>>> Login([FromBody] LoginRequest request)
    {
        try
        {
            var result = await _authService.LoginAsync(request);
            return Ok(ApiResponse<AuthResponse>.SuccessResponse(result, "Login successful"));
        }
        catch (UnauthorizedAccessException ex)
        {
            return Unauthorized(ApiResponse<AuthResponse>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Login failed for {Email}", request.Email);
            return BadRequest(ApiResponse<AuthResponse>.ErrorResponse("Login failed"));
        }
    }

    /// <summary>
    /// Refresh access token using refresh token
    /// </summary>
    /// <param name="request">Refresh token</param>
    /// <returns>New token pair</returns>
    [HttpPost("refresh")]
    [SwaggerOperation(Summary = "Refresh token", Description = "Gets new access token using refresh token")]
    [SwaggerResponse(200, "Token refreshed", typeof(ApiResponse<TokenResponse>))]
    [SwaggerResponse(401, "Invalid or expired refresh token", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<TokenResponse>>> RefreshToken([FromBody] RefreshTokenRequest request)
    {
        try
        {
            var result = await _authService.RefreshTokenAsync(request.RefreshToken);
            return Ok(ApiResponse<TokenResponse>.SuccessResponse(result, "Token refreshed"));
        }
        catch (UnauthorizedAccessException ex)
        {
            return Unauthorized(ApiResponse<TokenResponse>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Token refresh failed");
            return BadRequest(ApiResponse<TokenResponse>.ErrorResponse("Token refresh failed"));
        }
    }

    /// <summary>
    /// Logout and invalidate refresh token
    /// </summary>
    /// <returns>Logout confirmation</returns>
    [HttpPost("logout")]
    [Authorize]
    [SwaggerOperation(Summary = "Logout", Description = "Invalidates the user's refresh token")]
    [SwaggerResponse(200, "Logout successful", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> Logout()
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse.ErrorResponse("Unauthorized"));

            await _authService.LogoutAsync(userId.Value);
            return Ok(ApiResponse.SuccessResponse("Logout successful"));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Logout failed");
            return BadRequest(ApiResponse.ErrorResponse("Logout failed"));
        }
    }

    /// <summary>
    /// Request password reset email
    /// </summary>
    /// <param name="request">Email address</param>
    /// <returns>Confirmation message</returns>
    [HttpPost("forgot-password")]
    [SwaggerOperation(Summary = "Forgot password", Description = "Sends password reset email")]
    [SwaggerResponse(200, "Reset email sent", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> ForgotPassword([FromBody] ForgotPasswordRequest request)
    {
        try
        {
            await _authService.ForgotPasswordAsync(request);
            return Ok(ApiResponse.SuccessResponse("If an account exists with this email, a reset link has been sent"));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Forgot password failed for {Email}", request.Email);
            // Always return success to prevent email enumeration
            return Ok(ApiResponse.SuccessResponse("If an account exists with this email, a reset link has been sent"));
        }
    }

    /// <summary>
    /// Reset password using token
    /// </summary>
    /// <param name="request">Reset token and new password</param>
    /// <returns>Confirmation message</returns>
    [HttpPost("reset-password")]
    [SwaggerOperation(Summary = "Reset password", Description = "Resets password using email token")]
    [SwaggerResponse(200, "Password reset successful", typeof(ApiResponse))]
    [SwaggerResponse(400, "Invalid or expired token", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> ResetPassword([FromBody] ResetPasswordRequest request)
    {
        try
        {
            await _authService.ResetPasswordAsync(request);
            return Ok(ApiResponse.SuccessResponse("Password reset successful"));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Password reset failed");
            return BadRequest(ApiResponse.ErrorResponse("Password reset failed"));
        }
    }

    /// <summary>
    /// Verify email address
    /// </summary>
    /// <param name="request">Verification token</param>
    /// <returns>Confirmation message</returns>
    [HttpPost("verify-email")]
    [SwaggerOperation(Summary = "Verify email", Description = "Verifies email address using token")]
    [SwaggerResponse(200, "Email verified", typeof(ApiResponse))]
    [SwaggerResponse(400, "Invalid or expired token", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> VerifyEmail([FromBody] VerifyEmailRequest request)
    {
        try
        {
            await _authService.VerifyEmailAsync(request);
            return Ok(ApiResponse.SuccessResponse("Email verified successfully"));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Email verification failed");
            return BadRequest(ApiResponse.ErrorResponse("Email verification failed"));
        }
    }

    /// <summary>
    /// Resend verification email
    /// </summary>
    /// <param name="email">Email address</param>
    /// <returns>Confirmation message</returns>
    [HttpPost("resend-verification")]
    [SwaggerOperation(Summary = "Resend verification email", Description = "Sends a new verification email")]
    [SwaggerResponse(200, "Verification email sent", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> ResendVerification([FromQuery] string email)
    {
        try
        {
            await _authService.ResendVerificationEmailAsync(email);
            return Ok(ApiResponse.SuccessResponse("Verification email sent"));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Resend verification failed for {Email}", email);
            return Ok(ApiResponse.SuccessResponse("Verification email sent"));
        }
    }

    /// <summary>
    /// Change password (authenticated)
    /// </summary>
    /// <param name="request">Current and new password</param>
    /// <returns>Confirmation message</returns>
    [HttpPost("change-password")]
    [Authorize]
    [SwaggerOperation(Summary = "Change password", Description = "Changes password for authenticated user")]
    [SwaggerResponse(200, "Password changed", typeof(ApiResponse))]
    [SwaggerResponse(400, "Invalid current password", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse>> ChangePassword([FromBody] ChangePasswordRequest request)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse.ErrorResponse("Unauthorized"));

            await _authService.ChangePasswordAsync(userId.Value, request);
            return Ok(ApiResponse.SuccessResponse("Password changed successfully"));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Change password failed");
            return BadRequest(ApiResponse.ErrorResponse("Change password failed"));
        }
    }

    private Guid? GetCurrentUserId()
    {
        var userIdClaim = User.FindFirst(System.Security.Claims.ClaimTypes.NameIdentifier)?.Value;
        return Guid.TryParse(userIdClaim, out var userId) ? userId : null;
    }
}
