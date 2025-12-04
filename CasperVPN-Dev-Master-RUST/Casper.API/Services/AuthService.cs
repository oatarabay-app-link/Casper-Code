using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Security.Cryptography;
using System.Text;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using Casper.API.Models;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;
using Casper.API.Interfaces;
using Casper.API.Models.CasperVpnDbContext;

namespace Casper.API.Services
{
    public class AuthService : IAuthService
    {
        private readonly UserManager<Users> _userManager;
        private readonly RoleManager<IdentityRole> _roleManager;
        private readonly CasperVpnDbContext _context;
        private readonly IConfiguration _configuration;
        private readonly ILogger<AuthService> _logger;

        public AuthService(
            UserManager<Users> userManager,
            RoleManager<IdentityRole> roleManager,
            CasperVpnDbContext context,
            IConfiguration configuration,
            ILogger<AuthService> logger)
        {
            _userManager = userManager;
            _roleManager = roleManager;
            _context = context;
            _configuration = configuration;
            _logger = logger;
        }

     

        public async Task<Result<CreateUserResponse>> CreateUserAsync(UserRequest.CreateUser createUser)
        {
            try
            {
                _logger.LogInformation("Creating user: {UserName}", createUser?.UserName);

                if (createUser == null)
                    return Result<CreateUserResponse>.Failure("Request body required");

                var existingUser = await _userManager.FindByEmailAsync(createUser.Email);
                if (existingUser != null)
                    return Result<CreateUserResponse>.Conflict("Email already in use");

                existingUser = await _userManager.FindByNameAsync(createUser.UserName);
                if (existingUser != null)
                    return Result<CreateUserResponse>.Conflict("Username already taken");

                var user = new Users
                {
                    UserName = createUser.UserName,
                    Email = createUser.Email,
                    PhoneNumber = createUser.PhoneNumber
                };

                var result = await _userManager.CreateAsync(user, createUser.Password);

                if (!result.Succeeded)
                {
                    var errors = result.Errors.Select(e => e.Description).ToList();
                    return Result<CreateUserResponse>.ValidationFailure(errors);
                }

                if (!string.IsNullOrEmpty(createUser.Role))
                {
                    var roleExists = await _roleManager.RoleExistsAsync(createUser.Role);
                    if (!roleExists)
                    {
                        return Result<CreateUserResponse>.Failure($"Role '{createUser.Role}' does not exist");
                    }

                    var roleResult = await _userManager.AddToRoleAsync(user, createUser.Role);
                    if (!roleResult.Succeeded)
                    {
                        var errors = roleResult.Errors.Select(e => e.Description).ToList();
                        return Result<CreateUserResponse>.ValidationFailure(errors);
                    }
                }

                var response = new CreateUserResponse
                {
                    Id = user.Id,
                    UserName = user.UserName ?? "",
                    Email = user.Email ?? ""
                };

                _logger.LogInformation("User created successfully: {UserName}", createUser.UserName);
                return Result<CreateUserResponse>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error creating user: {UserName}", createUser.UserName);
                return Result<CreateUserResponse>.Failure("An error occurred while creating user", ErrorType.InternalError);
            }
        }

        public async Task<Result<UsersListResponse>> GetUsersAsync(int pageNumber = 1, int pageSize = 10)
        {
            try
            {
                _logger.LogInformation("Retrieving users - Page: {PageNumber}, Size: {PageSize}", pageNumber, pageSize);

                if (pageNumber < 1) pageNumber = 1;
                if (pageSize < 1 || pageSize > 100) pageSize = 10;

                // Get total count of users
                var totalCount = await _userManager.Users.CountAsync();

                // Get paginated users with their roles
                var users = await _userManager.Users
                    .OrderBy(u => u.UserName)
                    .Skip((pageNumber - 1) * pageSize)
                    .Take(pageSize)
                    .Select(u => new UserListItem
                    {
                        Id = u.Id,
                        UserName = u.UserName ?? "",
                        Email = u.Email ?? "",
                        PhoneNumber = u.PhoneNumber ?? "",
                        EmailConfirmed = u.EmailConfirmed,
                        PhoneNumberConfirmed = u.PhoneNumberConfirmed,
                    })
                    .ToListAsync();

                var paginationInfo = new PaginationInfo
                {
                    PageNumber = pageNumber,
                    PageSize = pageSize,
                    TotalCount = totalCount
                };

                var response = new UsersListResponse
                {
                    Data = users,
                    Pagination = paginationInfo
                };

                _logger.LogInformation("Successfully retrieved {UserCount} users", users.Count);
                return Result<UsersListResponse>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving users");
                return Result<UsersListResponse>.Failure("An error occurred while retrieving users", ErrorType.InternalError);
            }
        }

        public async Task<Result<UserInfo>> UpdateUserAsync(UserRequest.UpdateUser updateUser)
        {
            try
            {
                _logger.LogInformation("Updating user: {UserId}", updateUser?.Id);

                if (updateUser == null)
                    return Result<UserInfo>.Failure("Request body required");

                var user = await _userManager.FindByIdAsync(updateUser.Id);
                if (user == null)
                    return Result<UserInfo>.NotFound("User not found");

                if (!string.IsNullOrEmpty(updateUser.Email) && user.Email != updateUser.Email)
                {
                    var emailUser = await _userManager.FindByEmailAsync(updateUser.Email);
                    if (emailUser != null && emailUser.Id != user.Id)
                        return Result<UserInfo>.Conflict("Email already in use by another user");
                }

                if (!string.IsNullOrEmpty(updateUser.UserName) && user.UserName != updateUser.UserName)
                {
                    var nameUser = await _userManager.FindByNameAsync(updateUser.UserName);
                    if (nameUser != null && nameUser.Id != user.Id)
                        return Result<UserInfo>.Conflict("Username already taken");
                }

                // Update only if new values are provided
                if (!string.IsNullOrEmpty(updateUser.UserName))
                    user.UserName = updateUser.UserName;

                if (!string.IsNullOrEmpty(updateUser.Email))
                    user.Email = updateUser.Email;

                if (!string.IsNullOrEmpty(updateUser.PhoneNumber))
                    user.PhoneNumber = updateUser.PhoneNumber;

                var result = await _userManager.UpdateAsync(user);

                if (!result.Succeeded)
                {
                    var errors = result.Errors.Select(e => e.Description).ToList();
                    return Result<UserInfo>.ValidationFailure(errors);
                }

                var response = new UserInfo
                {
                    Id = user.Id,
                    UserName = user.UserName ?? "",
                    Email = user.Email ?? ""
                };

                _logger.LogInformation("User updated successfully: {UserId}", updateUser.Id);
                return Result<UserInfo>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating user: {UserId}", updateUser?.Id);
                return Result<UserInfo>.Failure("An error occurred while updating user", ErrorType.InternalError);
            }
        }
        public async Task<Result> DeleteUserAsync(string id)
        {
            try
            {
                _logger.LogInformation("Deleting user: {UserId}", id);

                if (string.IsNullOrEmpty(id))
                    return Result.Failure("User ID is required");

                var user = await _userManager.FindByIdAsync(id);
                if (user == null)
                    return Result.NotFound("User not found");

                var result = await _userManager.DeleteAsync(user);

                if (!result.Succeeded)
                {
                    var errors = result.Errors.Select(e => e.Description).ToList();
                    return Result.ValidationFailure(errors);
                }

                _logger.LogInformation("User deleted successfully: {UserId}", id);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error deleting user: {UserId}", id);
                return Result.Failure("An error occurred while deleting user", ErrorType.InternalError);
            }
        }
        public async Task<Result> ChangePasswordAsync(UserRequest.ChangePassword changePassword)
        {
            try
            {
                _logger.LogInformation("Changing password for user: {UserId}", changePassword?.UserId);

                if (changePassword == null)
                    return Result.Failure("Request body required");

                var user = await _userManager.FindByIdAsync(changePassword.UserId);
                if (user == null)
                    return Result.NotFound("User not found");

                var result = await _userManager.ChangePasswordAsync(
                    user,
                    changePassword.CurrentPassword,
                    changePassword.NewPassword);

                if (!result.Succeeded)
                {
                    var errors = result.Errors.Select(e => e.Description).ToList();
                    return Result.ValidationFailure(errors);
                }

                _logger.LogInformation("Password changed successfully for user: {UserId}", changePassword.UserId);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error changing password for user: {UserId}", changePassword?.UserId);
                return Result.Failure("An error occurred while changing password", ErrorType.InternalError);
            }
        }
        public async Task<Result> RevokeRefreshToken(string userId)
        {
            try
            {
                _logger.LogInformation("Revoking refresh tokens for user: {UserId}", userId);

                if (string.IsNullOrEmpty(userId))
                    return Result.Failure("User ID is required");

                var activeTokens = await _context.RefreshTokens
                    .Where(rt => rt.UserId == userId && rt.IsActive)
                    .ToListAsync();

                foreach (var token in activeTokens)
                {
                    token.Revoked = DateTime.UtcNow;
                }

                await _context.SaveChangesAsync();
                
                _logger.LogInformation("Successfully revoked {TokenCount} refresh tokens for user: {UserId}", 
                    activeTokens.Count, userId);
                return Result.Success();
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error revoking refresh token for user: {UserId}", userId);
                return Result.Failure("An error occurred while revoking refresh token", ErrorType.InternalError);
            }
        }

        public async Task<Result<RefreshTokenResponse>> RefreshTokenAsync(string accessToken, string refreshToken)
        {
            try
            {
                _logger.LogInformation("Refreshing token for access token");

                if (string.IsNullOrEmpty(accessToken) || string.IsNullOrEmpty(refreshToken))
                    return Result<RefreshTokenResponse>.Failure("Access token and refresh token are required");

                var principal = GetPrincipalFromExpiredToken(accessToken);
                var userId = principal.FindFirstValue(ClaimTypes.NameIdentifier);

                if (userId == null || !await ValidateRefreshToken(userId, refreshToken))
                {
                    _logger.LogWarning("Invalid refresh token for user: {UserId}", userId);
                    return Result<RefreshTokenResponse>.Unauthorized("Invalid refresh token");
                }

                var user = await _userManager.FindByIdAsync(userId);
                if (user == null)
                {
                    return Result<RefreshTokenResponse>.Unauthorized("User not found");
                }

                var newClaims = await GetUserClaims(user);
                var newToken = GenerateAccessToken(newClaims);
                var newRefreshToken = GenerateRefreshToken();

                await UpdateRefreshToken(userId, refreshToken, newRefreshToken);

                var response = new RefreshTokenResponse
                {
                    AccessToken = new JwtSecurityTokenHandler().WriteToken(newToken),
                    AccessTokenExpiration = newToken.ValidTo,
                    RefreshToken = newRefreshToken
                };

                _logger.LogInformation("Token refreshed successfully for user: {UserId}", userId);
                return Result<RefreshTokenResponse>.Success(response);
            }
            catch (SecurityTokenException ex)
            {
                _logger.LogWarning(ex, "Invalid token format during refresh");
                return Result<RefreshTokenResponse>.Unauthorized("Invalid token format");
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error refreshing token");
                return Result<RefreshTokenResponse>.Failure("An error occurred while refreshing token", ErrorType.InternalError);
            }
        }


        public async Task<Result<LoginResponse>> LoginUserAsync(UserRequest.Login loginRequest)
        {
            try
            {
                _logger.LogInformation("Login attempt for user: {UserName}", loginRequest.UserName);

                var user = await _userManager.FindByNameAsync(loginRequest.UserName);
                if (user == null || !await _userManager.CheckPasswordAsync(user, loginRequest.Password))
                {
                    _logger.LogWarning("Failed login attempt for user: {UserName}", loginRequest.UserName);
                    return Result<LoginResponse>.Unauthorized("Invalid username or password");
                }

                var authClaims = await GetUserClaims(user);
                var token = GenerateAccessToken(authClaims);
                var refreshToken = GenerateRefreshToken();

                await SaveRefreshToken(user.Id, refreshToken);

                var loginResponse = new LoginResponse
                {
                    AccessToken = new JwtSecurityTokenHandler().WriteToken(token),
                    AccessTokenExpiration = token.ValidTo,
                    RefreshToken = refreshToken,
                    User = new UserInfo 
                    { 
                        Id = user.Id, 
                        UserName = user.UserName ?? "", 
                        Email = user.Email ?? "" 
                    }
                };

                _logger.LogInformation("Successful login for user: {UserName}", loginRequest.UserName);
                return Result<LoginResponse>.Success(loginResponse);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error during login for user: {UserName}", loginRequest.UserName);
                return Result<LoginResponse>.Failure("An error occurred during login", ErrorType.InternalError);
            }
        }
       
        
        private async Task<List<Claim>> GetUserClaims(Users user)
        {
            var authClaims = new List<Claim>
            {
                new Claim(ClaimTypes.NameIdentifier, user.Id),
                new Claim(ClaimTypes.Name, user.UserName ?? ""),
                new Claim(JwtRegisteredClaimNames.Jti, Guid.NewGuid().ToString())
            };

            var userRoles = await _userManager.GetRolesAsync(user);
            foreach (var role in userRoles)
            {
                authClaims.Add(new Claim(ClaimTypes.Role, role));
            }

            return authClaims;
        }

        private JwtSecurityToken GenerateAccessToken(List<Claim> authClaims)
        {
            var key = _configuration["Jwt:Key"] ?? throw new InvalidOperationException("JWT Key not configured");

            if (key.Length < 32)
            {
                key = key.PadRight(32, '!'); // Ensure minimum 32 characters
            }

            var authSigningKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(key));

            // Create token descriptor with explicit signing credentials
            var tokenDescriptor = new SecurityTokenDescriptor
            {
                Issuer = _configuration["Jwt:Issuer"],
                Audience = _configuration["Jwt:Audience"],
                Expires = DateTime.UtcNow.AddMinutes(Convert.ToDouble(_configuration["Jwt:AccessTokenExpiry"])),
                Subject = new ClaimsIdentity(authClaims),
                SigningCredentials = new SigningCredentials(authSigningKey, SecurityAlgorithms.HmacSha256)
            };

            var tokenHandler = new JwtSecurityTokenHandler();
            return tokenHandler.CreateJwtSecurityToken(tokenDescriptor);
        }
        private string GenerateRefreshToken()
        {
            var randomNumber = new byte[32];
            using var rng = RandomNumberGenerator.Create();
            rng.GetBytes(randomNumber);
            return Convert.ToBase64String(randomNumber);
        }

        private async Task SaveRefreshToken(string userId, string refreshToken)
        {
            var oldTokens = await _context.RefreshTokens
                .Where(rt => rt.UserId == userId && rt.Expires <= DateTime.UtcNow)
                .ToListAsync();

            _context.RefreshTokens.RemoveRange(oldTokens);

            var newRefreshToken = new RefreshTokens
            {
                UserId = userId,
                Token = HashRefreshToken(refreshToken),
                Expires = DateTime.UtcNow.AddDays(
                    Convert.ToDouble(_configuration["Jwt:RefreshTokenExpiry"]))
            };

            _context.RefreshTokens.Add(newRefreshToken);
            await _context.SaveChangesAsync();
        }

        private string HashRefreshToken(string token)
        {
            return _userManager.PasswordHasher.HashPassword(null!, token);
        }

        private async Task<bool> ValidateRefreshToken(string userId, string token)
        {
            var storedToken = await _context.RefreshTokens
                .FirstOrDefaultAsync(rt => rt.UserId == userId && rt.IsActive);

            if (storedToken == null) return false;

            var result = _userManager.PasswordHasher
                .VerifyHashedPassword(null!, storedToken.Token, token);

            return result == PasswordVerificationResult.Success;
        }

       
        private ClaimsPrincipal GetPrincipalFromExpiredToken(string token)
        {
            var tokenHandler = new JwtSecurityTokenHandler();
            var validationParameters = new TokenValidationParameters
            {
                ValidateIssuerSigningKey = true,
                IssuerSigningKey = new SymmetricSecurityKey(
                    Encoding.UTF8.GetBytes(_configuration["Jwt:Key"] ?? throw new InvalidOperationException("JWT Key not configured"))),
                ValidateIssuer = true,
                ValidIssuer = _configuration["Jwt:Issuer"],
                ValidateAudience = true,
                ValidAudience = _configuration["Jwt:Audience"],
                ValidateLifetime = false,
                ClockSkew = TimeSpan.Zero
            };

            SecurityToken validatedToken;
            return tokenHandler.ValidateToken(token, validationParameters, out validatedToken);
        }


        private async Task UpdateRefreshToken(string userId, string oldToken, string newToken)
        {
            var oldRefreshToken = await _context.RefreshTokens
                .FirstOrDefaultAsync(rt => rt.UserId == userId && rt.Token == HashRefreshToken(oldToken));

            if (oldRefreshToken != null)
            {
                oldRefreshToken.Revoked = DateTime.UtcNow;
                oldRefreshToken.ReplacedByToken = HashRefreshToken(newToken);
            }

            await SaveRefreshToken(userId, newToken);
        }

      
      
    }
}