using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;

namespace Casper.API.Interfaces
{
    public interface IAuthService
    {
        Task<Result<CreateUserResponse>> CreateUserAsync(UserRequest.CreateUser createUser);
        Task<Result<UsersListResponse>> GetUsersAsync(int pageNumber = 1, int pageSize = 10);
        Task<Result<LoginResponse>> LoginUserAsync(UserRequest.Login loginRequest);
        Task<Result<RefreshTokenResponse>> RefreshTokenAsync(string accessToken, string refreshToken);
        Task<Result> RevokeRefreshToken(string userId);
        Task<Result<UserInfo>> UpdateUserAsync(UserRequest.UpdateUser updateUser);
        Task<Result> DeleteUserAsync(string id);
        Task<Result> ChangePasswordAsync(UserRequest.ChangePassword changePassword);
    }
}
