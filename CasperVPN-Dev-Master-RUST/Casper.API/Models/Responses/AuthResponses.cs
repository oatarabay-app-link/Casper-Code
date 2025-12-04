namespace Casper.API.Models.Responses
{
    public class LoginResponse
    {
        public string AccessToken { get; set; }
        public DateTime AccessTokenExpiration { get; set; }
        public string RefreshToken { get; set; }
        public UserInfo User { get; set; }
    }

    public class UserInfo
    {
        public string Id { get; set; }
        public string UserName { get; set; }
        public string Email { get; set; }
    }

    public class CreateUserResponse
    {
        public string Id { get; set; }
        public string UserName { get; set; }
        public string Email { get; set; }
    }

    public class UsersListResponse
    {
        public List<UserListItem> Data { get; set; }
        public PaginationInfo Pagination { get; set; }
    }

    public class UserListItem
    {
        public string Id { get; set; }
        public string UserName { get; set; }
        public string Email { get; set; }
        public string PhoneNumber { get; set; }
        public bool EmailConfirmed { get; set; }
        public bool PhoneNumberConfirmed { get; set; }
    }

    public class RefreshTokenResponse
    {
        public string AccessToken { get; set; }
        public DateTime AccessTokenExpiration { get; set; }
        public string RefreshToken { get; set; }
    }
}