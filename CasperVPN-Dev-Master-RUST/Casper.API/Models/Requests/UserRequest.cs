namespace Casper.API.Models.Requests
{
    public class UserRequest
    {
        public class CreateUser
        {
            public string UserName { get; set; }
            public string Email { get; set; }
            public string Password { get; set; }
            public string PhoneNumber { get; set; }
            public string Role { get; set; }
        }

        public class Login
        {
            public string UserName { get; set; }
            public string Password { get; set; }
        }

        public class UpdateUser
        {
            public string Id { get; set; }
            public string UserName { get; set; }
            public string Email { get; set; }
            public string PhoneNumber { get; set; }
        }

        public class ChangePassword
        {
            public string UserId { get; set; }
            public string CurrentPassword { get; set; }
            public string NewPassword { get; set; }
        }
    }
}
