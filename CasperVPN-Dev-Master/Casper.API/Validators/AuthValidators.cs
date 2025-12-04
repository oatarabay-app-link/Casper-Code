using Casper.API.Models.Requests;
using FluentValidation;

namespace Casper.API.Validators
{
    public class LoginRequestValidator : AbstractValidator<LoginRequest>
    {
        public LoginRequestValidator()
        {
            RuleFor(x => x.UserName)
                .NotEmpty()
                .WithMessage("Username is required");

            RuleFor(x => x.Password)
                .NotEmpty()
                .WithMessage("Password is required")
                .MinimumLength(6)
                .WithMessage("Password must be at least 6 characters long");
        }
    }

    public class CreateUserRequestValidator : AbstractValidator<CreateUserRequest>
    {
        public CreateUserRequestValidator()
        {
            RuleFor(x => x.UserName)
                .NotEmpty()
                .WithMessage("Username is required")
                .Length(3, 50)
                .WithMessage("Username must be between 3 and 50 characters");

            RuleFor(x => x.Email)
                .NotEmpty()
                .WithMessage("Email is required")
                .EmailAddress()
                .WithMessage("Invalid email format");

            RuleFor(x => x.Password)
                .NotEmpty()
                .WithMessage("Password is required")
                .MinimumLength(8)
                .WithMessage("Password must be at least 8 characters long")
                .Matches(@"^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]")
                .WithMessage("Password must contain uppercase, lowercase, number and special character");

            RuleFor(x => x.PhoneNumber)
                .Matches(@"^\+?[1-9]\d{1,14}$")
                .When(x => !string.IsNullOrEmpty(x.PhoneNumber))
                .WithMessage("Invalid phone number format");
        }
    }

    public class UpdateUserRequestValidator : AbstractValidator<UpdateUserRequest>
    {
        public UpdateUserRequestValidator()
        {
            RuleFor(x => x.Id)
                .NotEmpty()
                .WithMessage("User ID is required");

            RuleFor(x => x.UserName)
                .Length(3, 50)
                .WithMessage("Username must be between 3 and 50 characters")
                .When(x => !string.IsNullOrEmpty(x.UserName));

            RuleFor(x => x.Email)
                .EmailAddress()
                .WithMessage("Invalid email format")
                .When(x => !string.IsNullOrEmpty(x.Email));

            RuleFor(x => x.PhoneNumber)
                .Matches(@"^\+?[1-9]\d{1,14}$")
                .WithMessage("Invalid phone number format")
                .When(x => !string.IsNullOrEmpty(x.PhoneNumber));
        }
    }

    public class ChangePasswordRequestValidator : AbstractValidator<ChangePasswordRequest>
    {
        public ChangePasswordRequestValidator()
        {
            RuleFor(x => x.UserId)
                .NotEmpty()
                .WithMessage("User ID is required");

            RuleFor(x => x.CurrentPassword)
                .NotEmpty()
                .WithMessage("Current password is required");

            RuleFor(x => x.NewPassword)
                .NotEmpty()
                .WithMessage("New password is required")
                .MinimumLength(8)
                .WithMessage("New password must be at least 8 characters long")
                .Matches(@"^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]")
                .WithMessage("New password must contain uppercase, lowercase, number and special character")
                .NotEqual(x => x.CurrentPassword)
                .WithMessage("New password must be different from current password");
        }
    }
}