namespace Casper.API.Models.Requests
{
  
        public class CreateUserRequest
        {
            public string UserName { get; set; }
            public string Email { get; set; }
            public string Password { get; set; }
            public string PhoneNumber { get; set; }
            public string Role { get; set; }
        }

        public class LoginRequest
        {
            public string UserName { get; set; }
            public string Password { get; set; }
        }

        public class UpdateUserRequest
        {
            public string Id { get; set; }
            public string UserName { get; set; }
            public string Email { get; set; }
            public string PhoneNumber { get; set; }
        }

        public class ChangePasswordRequest
        {
            public string UserId { get; set; }
            public string CurrentPassword { get; set; }
            public string NewPassword { get; set; }
        }
    
}
