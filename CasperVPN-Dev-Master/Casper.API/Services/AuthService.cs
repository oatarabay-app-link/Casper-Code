using System.IdentityModel.Tokens.Jwt;
using System.Net.Http;
using System.Security.Claims;
using System.Security.Cryptography;
using System.Text;
using System.Text.Json;
using System.Text.Json.Serialization;
using Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;
using Casper.API.Models.Responses.OauthDto;
using Casper.API.Services.Interfaces;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;

namespace Casper.API.Services
{
    public class AuthService : IAuthService
    {
        private readonly UserManager<Users> _userManager;
        private readonly SignInManager<Users> _signInManager;
        private readonly HttpClient _httpClient;
        private readonly RoleManager<IdentityRole> _roleManager;
        private readonly CasperVpnDbContext _context;
        private readonly IConfiguration _configuration;
        private readonly ILogger<AuthService> _logger;

        public AuthService(
            UserManager<Users> userManager,
            SignInManager<Users> signInManager,
            HttpClient httpClient,
            RoleManager<IdentityRole> roleManager,
            CasperVpnDbContext context,
            IConfiguration configuration,
            ILogger<AuthService> logger)
        {
            _userManager = userManager;
            _signInManager = signInManager;
            _httpClient = httpClient;
            _roleManager = roleManager;
            _context = context;
            _configuration = configuration;
            _logger = logger;
        }

        // ========== OAUTH METHODS ==========

        public string GetGoogleAuthUrl(string redirectUrl, string state)
        {
            var clientId = _configuration["Authentication:Google:ClientId"];

            // Use simpler scopes that are more reliable
            var scope = "email profile openid";

            var encodedRedirectUrl = Uri.EscapeDataString(redirectUrl);

            var url = "https://accounts.google.com/o/oauth2/v2/auth" +
                     $"?client_id={clientId}" +
                     $"&redirect_uri={encodedRedirectUrl}" +
                     "&response_type=code" +
                     $"&scope={Uri.EscapeDataString(scope)}" +
                     $"&state={state}" +
                     "&access_type=online" +
                     "&prompt=select_account";

            Console.WriteLine($"Google Auth URL: {url}");
            return url;
        }

        public async Task<GoogleUserInfo> GetGoogleUserInfoAsync(string code, string redirectUrl)
        {
            try
            {
                Console.WriteLine("Exchanging authorization code for tokens...");

                var tokenResponse = await ExchangeCodeForTokenAsync(code, redirectUrl);
                if (tokenResponse == null)
                {
                    Console.WriteLine("❌ Token exchange failed");
                    return null;
                }

                Console.WriteLine($"✅ Token exchange successful");
                Console.WriteLine($"ID Token present: {!string.IsNullOrEmpty(tokenResponse.IdToken)}");

                // Use the ID token to get user info
                if (string.IsNullOrEmpty(tokenResponse.IdToken))
                {
                    Console.WriteLine("❌ ID token is missing");
                    return null;
                }

                return ExtractUserInfoFromIdToken(tokenResponse.IdToken);
            }
            catch (Exception ex)
            {
                Console.WriteLine($"💥 Exception: {ex}");
                return null;
            }
        }

        public async Task<Result<LoginResponse>> HandleGoogleOAuthAsync(string code, string redirectUrl)
        {
            try
            {
                Console.WriteLine($"=== STARTING GOOGLE OAUTH PROCESSING ===");

                // 1. Get user info from Google
                var googleUser = await GetGoogleUserInfoAsync(code, redirectUrl);
                if (googleUser == null)
                {
                    return Result<LoginResponse>.Failure("Failed to get user information from Google", ErrorType.Unauthorized);
                }

                // 2. Validate email
                if (string.IsNullOrEmpty(googleUser.Email))
                {
                    return Result<LoginResponse>.Failure("Email claim is missing from Google response", ErrorType.Unauthorized);
                }

                Console.WriteLine($"✅ Google user info: {googleUser.Email} - {googleUser.Name}");

                // 3. Find user by Google ID first, then by email
                var user = await FindUserByGoogleId(googleUser.Sub) ?? await _userManager.FindByEmailAsync(googleUser.Email);

                bool isNewUser = false;

                if (user == null)
                {
                    Console.WriteLine("Creating new Google user...");
                    // Create new user with Google info
                    user = new Users
                    {
                        UserName = googleUser.Email,
                        Email = googleUser.Email,
                        EmailConfirmed = true,
                        IsGoogleUser = true,
                        GoogleId = googleUser.Sub,
                        GoogleProfilePicture = googleUser.Picture,
                        FirstName = googleUser.GivenName,
                        LastName = googleUser.FamilyName
                    };

                    var createResult = await _userManager.CreateAsync(user);
                    if (!createResult.Succeeded)
                    {
                        var errors = string.Join(", ", createResult.Errors.Select(e => e.Description));
                        return Result<LoginResponse>.Failure($"User creation failed: {errors}", ErrorType.Validation);
                    }

                    Console.WriteLine("✅ Google user created successfully");
                    await _userManager.AddToRoleAsync(user, "User");
                    isNewUser = true;
                }
                else
                {
                    Console.WriteLine($"✅ User found: {user.UserName}");

                    // Update user with Google info if not already a Google user
                    if (!user.IsGoogleUser)
                    {
                        user.IsGoogleUser = true;
                        user.GoogleId = googleUser.Sub;
                        user.GoogleProfilePicture = googleUser.Picture;
                        user.FirstName = googleUser.GivenName ?? user.FirstName;
                        user.LastName = googleUser.FamilyName ?? user.LastName;

                        // ✅ ADDED ERROR CHECKING for update operation
                        var updateResult = await _userManager.UpdateAsync(user);
                        if (!updateResult.Succeeded)
                        {
                            var errors = string.Join(", ", updateResult.Errors.Select(e => e.Description));
                            Console.WriteLine($"❌ Failed to update user with Google information: {errors}");
                            return Result<LoginResponse>.Failure($"Failed to update user: {errors}", ErrorType.Validation);
                        }

                        Console.WriteLine("✅ Updated user with Google information");
                    }
                }

                // 4. Generate tokens directly (same as login flow)
                var authClaims = await GetUserClaims(user);
                var token = GenerateAccessToken(authClaims);
                var refreshToken = GenerateRefreshToken();

                await SaveRefreshToken(user.Id, refreshToken);

                // 5. Create login response (EXACTLY same structure as LoginUserAsync)
                var loginResponse = new LoginResponse
                {
                    AccessToken = new JwtSecurityTokenHandler().WriteToken(token),
                    AccessTokenExpiration = token.ValidTo,
                    RefreshToken = refreshToken,
                    User = new UserInfo
                    {
                        Id = user.Id,
                        UserName = user.UserName ?? "",
                        Email = user.Email ?? "",
                        FirstName = user.FirstName,
                        LastName = user.LastName,
                        IsGoogleUser = user.IsGoogleUser,
                        ProfilePicture = user.GoogleProfilePicture,
                        HasPassword = await _userManager.HasPasswordAsync(user)
                    }
                };

                // 6. Sign in the user
                await _signInManager.SignInAsync(user, isPersistent: false);
                Console.WriteLine($"=== GOOGLE OAUTH PROCESSING COMPLETE ===");

                return Result<LoginResponse>.Success(loginResponse);
            }
            catch (Exception ex)
            {
                Console.WriteLine($"💥 Exception in HandleGoogleOAuthAsync: {ex}");
                return Result<LoginResponse>.Failure($"OAuth processing failed: {ex.Message}", ErrorType.InternalError);
            }
        }   // ========== PRIVATE OAUTH HELPER METHODS ==========

        private GoogleUserInfo ExtractUserInfoFromIdToken(string idToken)
        {
            try
            {
                Console.WriteLine("Decoding ID token...");

                if (string.IsNullOrEmpty(idToken))
                {
                    Console.WriteLine("ID token is null or empty");
                    return null;
                }

                // Split the JWT
                var parts = idToken.Split('.');
                if (parts.Length != 3)
                {
                    Console.WriteLine("Invalid JWT format");
                    return null;
                }

                // Decode the payload
                var payload = parts[1];

                // Fix Base64 URL encoding
                payload = payload.Replace('_', '/').Replace('-', '+');
                switch (payload.Length % 4)
                {
                    case 2: payload += "=="; break;
                    case 3: payload += "="; break;
                }

                var payloadBytes = Convert.FromBase64String(payload);
                var payloadJson = Encoding.UTF8.GetString(payloadBytes);

                Console.WriteLine($"ID Token Payload: {payloadJson}");

                // Parse JSON
                using var document = JsonDocument.Parse(payloadJson);
                var root = document.RootElement;

                var userInfo = new GoogleUserInfo();

                // Use TryGetProperty to handle missing properties gracefully
                if (root.TryGetProperty("sub", out var subProp))
                    userInfo.Sub = subProp.GetString();

                if (root.TryGetProperty("email", out var emailProp))
                    userInfo.Email = emailProp.GetString();

                if (root.TryGetProperty("email_verified", out var emailVerifiedProp))
                    userInfo.EmailVerified = emailVerifiedProp.GetBoolean();

                if (root.TryGetProperty("name", out var nameProp))
                    userInfo.Name = nameProp.GetString();

                if (root.TryGetProperty("given_name", out var givenNameProp))
                    userInfo.GivenName = givenNameProp.GetString();

                if (root.TryGetProperty("family_name", out var familyNameProp))
                    userInfo.FamilyName = familyNameProp.GetString();

                if (root.TryGetProperty("picture", out var pictureProp))
                    userInfo.Picture = pictureProp.GetString();

                if (string.IsNullOrEmpty(userInfo.Email))
                {
                    Console.WriteLine("❌ Email not found in ID token");
                    return null;
                }

                Console.WriteLine($"✅ Successfully decoded user info: {userInfo.Email}");
                return userInfo;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"❌ Failed to decode ID token: {ex}");
                return null;
            }
        }

        private async Task<GoogleTokenResponse> ExchangeCodeForTokenAsync(string code, string redirectUrl)
        {
            try
            {
                Console.WriteLine("Exchanging code for token...");

                var clientId = _configuration["Authentication:Google:ClientId"];
                var clientSecret = _configuration["Authentication:Google:ClientSecret"];

                Console.WriteLine($"Client ID configured: {!string.IsNullOrEmpty(clientId)}");
                Console.WriteLine($"Client Secret configured: {!string.IsNullOrEmpty(clientSecret)}");
                Console.WriteLine($"Redirect URL: {redirectUrl}");
                Console.WriteLine($"Code length: {code?.Length}");

                var content = new FormUrlEncodedContent(new[]
                {
                    new KeyValuePair<string, string>("code", code),
                    new KeyValuePair<string, string>("client_id", clientId),
                    new KeyValuePair<string, string>("client_secret", clientSecret),
                    new KeyValuePair<string, string>("redirect_uri", redirectUrl),
                    new KeyValuePair<string, string>("grant_type", "authorization_code")
                });

                Console.WriteLine("Sending token exchange request to Google...");
                var response = await _httpClient.PostAsync("https://oauth2.googleapis.com/token", content);

                var responseContent = await response.Content.ReadAsStringAsync();
                Console.WriteLine($"Token exchange response status: {response.StatusCode}");
                Console.WriteLine($"Response content: {responseContent}");

                if (response.IsSuccessStatusCode)
                {
                    Console.WriteLine("✅ Token exchange successful");
                    try
                    {
                        // Use case-insensitive JSON deserialization
                        var options = new JsonSerializerOptions
                        {
                            PropertyNameCaseInsensitive = true,
                            PropertyNamingPolicy = JsonNamingPolicy.CamelCase
                        };

                        var tokenResponse = JsonSerializer.Deserialize<GoogleTokenResponse>(responseContent, options);
                        Console.WriteLine($"Deserialized token response: {tokenResponse != null}");
                        Console.WriteLine($"ID Token from response: {!string.IsNullOrEmpty(tokenResponse?.IdToken)}");
                        return tokenResponse;
                    }
                    catch (JsonException jsonEx)
                    {
                        Console.WriteLine($"❌ JSON deserialization failed: {jsonEx}");
                        return null;
                    }
                }
                else
                {
                    Console.WriteLine($"❌ Token exchange failed with status: {response.StatusCode}");
                    Console.WriteLine($"Error response: {responseContent}");
                    return null;
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"💥 Exception in ExchangeCodeForTokenAsync: {ex}");
                return null;
            }
        }

        private async Task<GoogleUserInfo> GetUserInfoFromGoogleAsync(string accessToken)
        {
            try
            {
                Console.WriteLine($"Getting user info with access token...");

                // Try multiple userinfo endpoints
                var endpoints = new[]
                {
                    "https://www.googleapis.com/oauth2/v3/userinfo",
                    "https://www.googleapis.com/oauth2/v2/userinfo",
                    "https://openidconnect.googleapis.com/v1/userinfo"
                };

                foreach (var endpoint in endpoints)
                {
                    Console.WriteLine($"Trying endpoint: {endpoint}");

                    _httpClient.DefaultRequestHeaders.Authorization =
                        new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", accessToken);

                    var response = await _httpClient.GetAsync(endpoint);
                    var responseContent = await response.Content.ReadAsStringAsync();

                    Console.WriteLine($"Userinfo response status from {endpoint}: {response.StatusCode}");
                    Console.WriteLine($"Userinfo response content: {responseContent}");

                    if (response.IsSuccessStatusCode)
                    {
                        try
                        {
                            var userInfo = JsonSerializer.Deserialize<GoogleUserInfo>(responseContent);
                            if (userInfo != null && !string.IsNullOrEmpty(userInfo.Email))
                            {
                                Console.WriteLine($"✅ Successfully got user info from {endpoint}");
                                Console.WriteLine($"User Email: {userInfo.Email}");
                                Console.WriteLine($"User Name: {userInfo.Name}");
                                return userInfo;
                            }
                        }
                        catch (JsonException jsonEx)
                        {
                            Console.WriteLine($"JSON deserialization failed for {endpoint}: {jsonEx}");
                        }
                    }
                    else
                    {
                        Console.WriteLine($"Endpoint {endpoint} failed: {response.StatusCode}");
                    }

                    // Clear headers for next attempt
                    _httpClient.DefaultRequestHeaders.Remove("Authorization");
                }

                Console.WriteLine("❌ All userinfo endpoints failed");
                return null;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"💥 Exception in GetUserInfoFromGoogleAsync: {ex}");
                return null;
            }
        }

        private async Task<Users?> FindUserByGoogleId(string googleId)
        {
            // You might need to query your database directly if UserManager doesn't support custom queries
            var users = _userManager.Users.Where(u => u.GoogleId == googleId).ToList();
            return users.FirstOrDefault();
        }

        // ========== EXISTING AUTH METHODS ==========

        public JwtSecurityToken GenerateAccessToken(List<Claim> claims)
        {
            var key = _configuration["Jwt:Key"] ?? throw new InvalidOperationException("JWT Key not configured");

            if (key.Length < 32)
            {
                key = key.PadRight(32, '!');
            }

            var authSigningKey = new SymmetricSecurityKey(Encoding.UTF8.GetBytes(key));

            var tokenDescriptor = new SecurityTokenDescriptor
            {
                Issuer = _configuration["Jwt:Issuer"],
                Audience = _configuration["Jwt:Audience"],
                Expires = DateTime.UtcNow.AddMinutes(Convert.ToDouble(_configuration["Jwt:AccessTokenExpiry"])),
                Subject = new ClaimsIdentity(claims),
                SigningCredentials = new SigningCredentials(authSigningKey, SecurityAlgorithms.HmacSha256)
            };

            var tokenHandler = new JwtSecurityTokenHandler();
            return tokenHandler.CreateJwtSecurityToken(tokenDescriptor);
        }

        public async Task<Result<CreateUserResponse>> CreateUserAsync(CreateUserRequest createUser)
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

                var totalCount = await _userManager.Users.CountAsync();

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

        public async Task<Result<UserInfo>> UpdateUserAsync(UpdateUserRequest updateUser)
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

        public async Task<Result> ChangePasswordAsync(ChangePasswordRequest changePassword)
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

        public async Task<Result<LoginResponse>> LoginUserAsync(LoginRequest loginRequest)
        {
            try
            {
                _logger.LogInformation("Login attempt for user: {UserName}", loginRequest.UserName);

                var user = await _userManager.FindByNameAsync(loginRequest.UserName);
                if (user == null)
                {
                    _logger.LogWarning("User not found: {UserName}", loginRequest.UserName);
                    return Result<LoginResponse>.Unauthorized("Invalid username or password");
                }

                // Handle Google users
                if (user.IsGoogleUser)
                {
                    // Check if Google user has a password set (they might have set it later)
                    var hasPassword = await _userManager.HasPasswordAsync(user);

                    if (!hasPassword)
                    {
                        _logger.LogWarning("Google user attempted password login but no password set: {UserName}", loginRequest.UserName);
                        return Result<LoginResponse>.Unauthorized("This account uses Google login. Please use Google OAuth to sign in, or set a password first.");
                    }

                    // If Google user has set a password, verify it
                    if (!await _userManager.CheckPasswordAsync(user, loginRequest.Password))
                    {
                        _logger.LogWarning("Invalid password for Google user: {UserName}", loginRequest.UserName);
                        return Result<LoginResponse>.Unauthorized("Invalid username or password");
                    }

                    _logger.LogInformation("Google user logged in with password: {UserName}", loginRequest.UserName);
                }
                else
                {
                    // Regular password check for non-Google users
                    if (!await _userManager.CheckPasswordAsync(user, loginRequest.Password))
                    {
                        _logger.LogWarning("Invalid password for user: {UserName}", loginRequest.UserName);
                        return Result<LoginResponse>.Unauthorized("Invalid username or password");
                    }
                }

                // Generate tokens for both types of users
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
                        Email = user.Email ?? "",
                        FirstName = user.FirstName,
                        LastName = user.LastName,
                        IsGoogleUser = user.IsGoogleUser,
                        ProfilePicture = user.GoogleProfilePicture,
                        HasPassword = await _userManager.HasPasswordAsync(user)
                    }
                };

                _logger.LogInformation("Successful login for user: {UserName} (Google: {IsGoogle})",
                    loginRequest.UserName, user.IsGoogleUser);
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
            var claims = new List<Claim>
            {
                new Claim(ClaimTypes.NameIdentifier, user.Id),
                new Claim(ClaimTypes.Name, user.UserName ?? ""),
                new Claim(JwtRegisteredClaimNames.Jti, Guid.NewGuid().ToString())
            };

            var userRoles = await _userManager.GetRolesAsync(user);
            foreach (var role in userRoles)
            {
                claims.Add(new Claim(ClaimTypes.Role, role));
            }

            return claims;
        }

        private static string GenerateRefreshToken()
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
            var hashedOldToken = HashRefreshToken(oldToken);
            var oldRefreshToken = await _context.RefreshTokens
                .FirstOrDefaultAsync(rt => rt.UserId == userId && rt.Token == hashedOldToken && rt.IsActive);

            if (oldRefreshToken != null)
            {
                oldRefreshToken.Revoked = DateTime.UtcNow;
                oldRefreshToken.ReplacedByToken = HashRefreshToken(newToken);
            }

            await SaveRefreshToken(userId, newToken);
        }
    }

}