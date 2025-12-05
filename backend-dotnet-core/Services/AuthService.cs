using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Security.Cryptography;
using System.Text;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Options;
using Microsoft.IdentityModel.Tokens;
using CasperVPN.Data;
using CasperVPN.DTOs;
using CasperVPN.Models;
using CasperVPN.Helpers;

namespace CasperVPN.Services;

/// <summary>
/// Authentication service implementation
/// </summary>
public class AuthService : IAuthService
{
    private readonly ApplicationDbContext _context;
    private readonly JwtSettings _jwtSettings;
    private readonly ILogger<AuthService> _logger;
    private readonly IEmailService _emailService;
    private readonly IFreeRadiusService _radiusService;

    public AuthService(
        ApplicationDbContext context,
        IOptions<JwtSettings> jwtSettings,
        ILogger<AuthService> logger,
        IEmailService emailService,
        IFreeRadiusService radiusService)
    {
        _context = context;
        _jwtSettings = jwtSettings.Value;
        _logger = logger;
        _emailService = emailService;
        _radiusService = radiusService;
    }

    public async Task<AuthResponse> RegisterAsync(RegisterRequest request)
    {
        _logger.LogInformation("Attempting to register user: {Email}", request.Email);

        // Check if user already exists
        var existingUser = await _context.Users.FirstOrDefaultAsync(u => u.Email.ToLower() == request.Email.ToLower());
        if (existingUser != null)
        {
            throw new InvalidOperationException("A user with this email already exists");
        }

        // Create new user
        var user = new User
        {
            Email = request.Email.ToLower(),
            PasswordHash = BCrypt.Net.BCrypt.HashPassword(request.Password),
            FirstName = request.FirstName ?? string.Empty,
            LastName = request.LastName ?? string.Empty,
            IsEmailVerified = false,
            IsActive = true,
            Role = UserRole.User,
            EmailVerificationToken = GenerateSecureToken(),
            EmailVerificationTokenExpiry = DateTime.UtcNow.AddDays(7)
        };

        // Generate RADIUS credentials
        user.RadiusUsername = GenerateRadiusUsername(user.Email);
        user.RadiusPassword = GenerateSecureToken(16);

        _context.Users.Add(user);
        await _context.SaveChangesAsync();

        // Create RADIUS user
        try
        {
            await _radiusService.CreateUserAsync(user.RadiusUsername, user.RadiusPassword);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to create RADIUS user for {Email}", user.Email);
            // Continue anyway - can be fixed later
        }

        // Assign free plan
        var freePlan = await _context.Plans.FirstOrDefaultAsync(p => p.Type == PlanType.Free && p.IsActive);
        if (freePlan != null)
        {
            var subscription = new Subscription
            {
                UserId = user.Id,
                PlanId = freePlan.Id,
                Status = SubscriptionStatus.Active,
                StartDate = DateTime.UtcNow
            };
            user.DataLimitBytes = freePlan.DataLimitBytes;
            _context.Subscriptions.Add(subscription);
            await _context.SaveChangesAsync();
        }

        // Send verification email
        try
        {
            await _emailService.SendVerificationEmailAsync(user.Email, user.EmailVerificationToken!);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to send verification email to {Email}", user.Email);
        }

        // Generate tokens
        var accessToken = GenerateJwtToken(user);
        var refreshToken = GenerateRefreshToken();

        user.RefreshToken = refreshToken;
        user.RefreshTokenExpiry = DateTime.UtcNow.AddDays(_jwtSettings.RefreshTokenExpirationDays);
        user.LastLoginAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        _logger.LogInformation("User registered successfully: {Email}", user.Email);

        return new AuthResponse
        {
            AccessToken = accessToken,
            RefreshToken = refreshToken,
            ExpiresAt = DateTime.UtcNow.AddMinutes(_jwtSettings.ExpirationMinutes),
            User = MapUserToDto(user)
        };
    }

    public async Task<AuthResponse> LoginAsync(LoginRequest request)
    {
        _logger.LogInformation("Login attempt for: {Email}", request.Email);

        var user = await _context.Users
            .Include(u => u.Subscription)
            .ThenInclude(s => s!.Plan)
            .FirstOrDefaultAsync(u => u.Email.ToLower() == request.Email.ToLower());

        if (user == null)
        {
            _logger.LogWarning("Login failed - user not found: {Email}", request.Email);
            throw new UnauthorizedAccessException("Invalid email or password");
        }

        if (!user.IsActive)
        {
            _logger.LogWarning("Login failed - user inactive: {Email}", request.Email);
            throw new UnauthorizedAccessException("Account is deactivated");
        }

        if (!BCrypt.Net.BCrypt.Verify(request.Password, user.PasswordHash))
        {
            _logger.LogWarning("Login failed - invalid password: {Email}", request.Email);
            throw new UnauthorizedAccessException("Invalid email or password");
        }

        // Generate tokens
        var accessToken = GenerateJwtToken(user);
        var refreshToken = GenerateRefreshToken();

        user.RefreshToken = refreshToken;
        user.RefreshTokenExpiry = request.RememberMe 
            ? DateTime.UtcNow.AddDays(_jwtSettings.RefreshTokenExpirationDays * 2)
            : DateTime.UtcNow.AddDays(_jwtSettings.RefreshTokenExpirationDays);
        user.LastLoginAt = DateTime.UtcNow;
        await _context.SaveChangesAsync();

        _logger.LogInformation("User logged in successfully: {Email}", user.Email);

        return new AuthResponse
        {
            AccessToken = accessToken,
            RefreshToken = refreshToken,
            ExpiresAt = DateTime.UtcNow.AddMinutes(_jwtSettings.ExpirationMinutes),
            User = MapUserToDto(user)
        };
    }

    public async Task<TokenResponse> RefreshTokenAsync(string refreshToken)
    {
        var user = await ValidateRefreshTokenAsync(refreshToken);
        if (user == null)
        {
            throw new UnauthorizedAccessException("Invalid or expired refresh token");
        }

        // Generate new tokens
        var newAccessToken = GenerateJwtToken(user);
        var newRefreshToken = GenerateRefreshToken();

        user.RefreshToken = newRefreshToken;
        user.RefreshTokenExpiry = DateTime.UtcNow.AddDays(_jwtSettings.RefreshTokenExpirationDays);
        await _context.SaveChangesAsync();

        return new TokenResponse
        {
            AccessToken = newAccessToken,
            RefreshToken = newRefreshToken,
            ExpiresAt = DateTime.UtcNow.AddMinutes(_jwtSettings.ExpirationMinutes)
        };
    }

    public async Task LogoutAsync(Guid userId)
    {
        var user = await _context.Users.FindAsync(userId);
        if (user != null)
        {
            user.RefreshToken = null;
            user.RefreshTokenExpiry = null;
            await _context.SaveChangesAsync();
            _logger.LogInformation("User logged out: {UserId}", userId);
        }
    }

    public async Task<bool> ForgotPasswordAsync(ForgotPasswordRequest request)
    {
        var user = await _context.Users.FirstOrDefaultAsync(u => u.Email.ToLower() == request.Email.ToLower());
        if (user == null)
        {
            // Don't reveal if user exists
            return true;
        }

        user.PasswordResetToken = GenerateSecureToken();
        user.PasswordResetTokenExpiry = DateTime.UtcNow.AddHours(24);
        await _context.SaveChangesAsync();

        try
        {
            await _emailService.SendPasswordResetEmailAsync(user.Email, user.PasswordResetToken);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to send password reset email to {Email}", user.Email);
            return false;
        }

        return true;
    }

    public async Task<bool> ResetPasswordAsync(ResetPasswordRequest request)
    {
        var user = await _context.Users.FirstOrDefaultAsync(u => 
            u.Email.ToLower() == request.Email.ToLower() &&
            u.PasswordResetToken == request.Token &&
            u.PasswordResetTokenExpiry > DateTime.UtcNow);

        if (user == null)
        {
            throw new InvalidOperationException("Invalid or expired reset token");
        }

        user.PasswordHash = BCrypt.Net.BCrypt.HashPassword(request.NewPassword);
        user.PasswordResetToken = null;
        user.PasswordResetTokenExpiry = null;
        user.RefreshToken = null;
        user.RefreshTokenExpiry = null;
        await _context.SaveChangesAsync();

        // Update RADIUS password
        try
        {
            if (!string.IsNullOrEmpty(user.RadiusUsername))
            {
                var newRadiusPassword = GenerateSecureToken(16);
                await _radiusService.UpdateUserPasswordAsync(user.RadiusUsername, newRadiusPassword);
                user.RadiusPassword = newRadiusPassword;
                await _context.SaveChangesAsync();
            }
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to update RADIUS password for {Email}", user.Email);
        }

        _logger.LogInformation("Password reset for user: {Email}", user.Email);
        return true;
    }

    public async Task<bool> VerifyEmailAsync(VerifyEmailRequest request)
    {
        var user = await _context.Users.FirstOrDefaultAsync(u =>
            u.Email.ToLower() == request.Email.ToLower() &&
            u.EmailVerificationToken == request.Token &&
            u.EmailVerificationTokenExpiry > DateTime.UtcNow);

        if (user == null)
        {
            throw new InvalidOperationException("Invalid or expired verification token");
        }

        user.IsEmailVerified = true;
        user.EmailVerificationToken = null;
        user.EmailVerificationTokenExpiry = null;
        await _context.SaveChangesAsync();

        _logger.LogInformation("Email verified for user: {Email}", user.Email);
        return true;
    }

    public async Task<bool> ResendVerificationEmailAsync(string email)
    {
        var user = await _context.Users.FirstOrDefaultAsync(u => u.Email.ToLower() == email.ToLower());
        if (user == null || user.IsEmailVerified)
        {
            return true; // Don't reveal if user exists
        }

        user.EmailVerificationToken = GenerateSecureToken();
        user.EmailVerificationTokenExpiry = DateTime.UtcNow.AddDays(7);
        await _context.SaveChangesAsync();

        await _emailService.SendVerificationEmailAsync(user.Email, user.EmailVerificationToken);
        return true;
    }

    public async Task<bool> ChangePasswordAsync(Guid userId, ChangePasswordRequest request)
    {
        var user = await _context.Users.FindAsync(userId);
        if (user == null)
        {
            throw new InvalidOperationException("User not found");
        }

        if (!BCrypt.Net.BCrypt.Verify(request.CurrentPassword, user.PasswordHash))
        {
            throw new InvalidOperationException("Current password is incorrect");
        }

        user.PasswordHash = BCrypt.Net.BCrypt.HashPassword(request.NewPassword);
        user.RefreshToken = null;
        user.RefreshTokenExpiry = null;
        await _context.SaveChangesAsync();

        _logger.LogInformation("Password changed for user: {UserId}", userId);
        return true;
    }

    public string GenerateJwtToken(User user)
    {
        var key = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(_jwtSettings.SecretKey));
        var credentials = new SigningCredentials(key, SecurityAlgorithms.HmacSha256);

        var claims = new[]
        {
            new Claim(JwtRegisteredClaimNames.Sub, user.Id.ToString()),
            new Claim(JwtRegisteredClaimNames.Email, user.Email),
            new Claim(ClaimTypes.NameIdentifier, user.Id.ToString()),
            new Claim(ClaimTypes.Email, user.Email),
            new Claim(ClaimTypes.Role, user.Role.ToString()),
            new Claim("firstName", user.FirstName),
            new Claim("lastName", user.LastName),
            new Claim(JwtRegisteredClaimNames.Jti, Guid.NewGuid().ToString())
        };

        var token = new JwtSecurityToken(
            issuer: _jwtSettings.Issuer,
            audience: _jwtSettings.Audience,
            claims: claims,
            expires: DateTime.UtcNow.AddMinutes(_jwtSettings.ExpirationMinutes),
            signingCredentials: credentials
        );

        return new JwtSecurityTokenHandler().WriteToken(token);
    }

    public string GenerateRefreshToken()
    {
        return GenerateSecureToken(64);
    }

    public async Task<User?> ValidateRefreshTokenAsync(string refreshToken)
    {
        return await _context.Users.FirstOrDefaultAsync(u =>
            u.RefreshToken == refreshToken &&
            u.RefreshTokenExpiry > DateTime.UtcNow &&
            u.IsActive);
    }

    private static string GenerateSecureToken(int length = 32)
    {
        var randomBytes = new byte[length];
        using (var rng = RandomNumberGenerator.Create())
        {
            rng.GetBytes(randomBytes);
        }
        return Convert.ToBase64String(randomBytes).Replace("+", "-").Replace("/", "_").TrimEnd('=');
    }

    private static string GenerateRadiusUsername(string email)
    {
        var prefix = email.Split('@')[0].ToLower();
        var suffix = Guid.NewGuid().ToString("N").Substring(0, 8);
        return $"{prefix}_{suffix}";
    }

    private static UserDto MapUserToDto(User user)
    {
        return new UserDto
        {
            Id = user.Id,
            Email = user.Email,
            FirstName = user.FirstName,
            LastName = user.LastName,
            IsEmailVerified = user.IsEmailVerified,
            IsActive = user.IsActive,
            Role = user.Role,
            CreatedAt = user.CreatedAt,
            LastLoginAt = user.LastLoginAt,
            DataUsedBytes = user.DataUsedBytes,
            DataLimitBytes = user.DataLimitBytes,
            Subscription = user.Subscription != null ? new SubscriptionDto
            {
                Id = user.Subscription.Id,
                PlanId = user.Subscription.PlanId,
                PlanName = user.Subscription.Plan?.Name ?? string.Empty,
                PlanType = user.Subscription.Plan?.Type ?? PlanType.Free,
                Status = user.Subscription.Status,
                StartDate = user.Subscription.StartDate,
                EndDate = user.Subscription.EndDate,
                CurrentPeriodStart = user.Subscription.CurrentPeriodStart,
                CurrentPeriodEnd = user.Subscription.CurrentPeriodEnd,
                CancelAtPeriodEnd = user.Subscription.CancelAtPeriodEnd
            } : null
        };
    }
}
