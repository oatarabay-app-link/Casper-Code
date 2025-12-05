using CasperVPN.DTOs;
using CasperVPN.Models;

namespace CasperVPN.Services;

/// <summary>
/// Authentication service interface
/// </summary>
public interface IAuthService
{
    Task<AuthResponse> RegisterAsync(RegisterRequest request);
    Task<AuthResponse> LoginAsync(LoginRequest request);
    Task<TokenResponse> RefreshTokenAsync(string refreshToken);
    Task LogoutAsync(Guid userId);
    Task<bool> ForgotPasswordAsync(ForgotPasswordRequest request);
    Task<bool> ResetPasswordAsync(ResetPasswordRequest request);
    Task<bool> VerifyEmailAsync(VerifyEmailRequest request);
    Task<bool> ResendVerificationEmailAsync(string email);
    Task<bool> ChangePasswordAsync(Guid userId, ChangePasswordRequest request);
    string GenerateJwtToken(User user);
    string GenerateRefreshToken();
    Task<User?> ValidateRefreshTokenAsync(string refreshToken);
}
