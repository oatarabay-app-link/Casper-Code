using System.Collections.Generic;
using System.IdentityModel.Tokens.Jwt;
using System.Security.Claims;
using System.Threading.Tasks;
using Casper.API.Models;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;
using Casper.API.Models.Responses.OauthDto;
using Casper.API.Services;

namespace Casper.API.Services.Interfaces
{
    public interface IAuthService
    {
        // Existing methods
        Task<Result<CreateUserResponse>> CreateUserAsync(CreateUserRequest model);
        Task<Result<LoginResponse>> LoginUserAsync(LoginRequest model);
        Task<Result<UsersListResponse>> GetUsersAsync(int pageNumber, int pageSize);
        Task<Result> RevokeRefreshToken(string userId);
        Task<Result<UserInfo>> UpdateUserAsync(UpdateUserRequest model);
        Task<Result> DeleteUserAsync(string id);
        Task<Result> ChangePasswordAsync(ChangePasswordRequest model);
        Task<Result<RefreshTokenResponse>> RefreshTokenAsync(string accessToken, string refreshToken);

        // OAuth methods
        string GetGoogleAuthUrl(string redirectUrl, string state);
        Task<GoogleUserInfo> GetGoogleUserInfoAsync(string code, string redirectUrl);
        Task<Result<LoginResponse>> HandleGoogleOAuthAsync(string code, string redirectUrl);
    }
}
