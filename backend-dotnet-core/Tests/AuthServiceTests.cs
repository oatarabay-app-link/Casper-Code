using FluentAssertions;
using Xunit;
using CasperVPN.DTOs;
using CasperVPN.Models;
using CasperVPN.Services;

namespace CasperVPN.Tests;

public class AuthServiceTests : TestBase
{
    private readonly AuthService _authService;

    public AuthServiceTests()
    {
        _authService = new AuthService(
            DbContext,
            JwtSettings,
            AuthLoggerMock.Object,
            EmailServiceMock.Object,
            RadiusServiceMock.Object);
    }

    [Fact]
    public async Task Register_WithValidData_ShouldCreateUser()
    {
        // Arrange
        var request = new RegisterRequest
        {
            Email = "test@example.com",
            Password = "TestPassword123!",
            ConfirmPassword = "TestPassword123!",
            FirstName = "John",
            LastName = "Doe"
        };

        // Act
        var result = await _authService.RegisterAsync(request);

        // Assert
        result.Should().NotBeNull();
        result.User.Should().NotBeNull();
        result.User.Email.Should().Be("test@example.com");
        result.AccessToken.Should().NotBeNullOrEmpty();
        result.RefreshToken.Should().NotBeNullOrEmpty();
    }

    [Fact]
    public async Task Register_WithExistingEmail_ShouldThrowException()
    {
        // Arrange
        var request = new RegisterRequest
        {
            Email = "existing@example.com",
            Password = "TestPassword123!",
            ConfirmPassword = "TestPassword123!"
        };

        // Create existing user
        await _authService.RegisterAsync(request);

        // Act & Assert
        await Assert.ThrowsAsync<InvalidOperationException>(() => 
            _authService.RegisterAsync(request));
    }

    [Fact]
    public async Task Login_WithValidCredentials_ShouldReturnTokens()
    {
        // Arrange
        var registerRequest = new RegisterRequest
        {
            Email = "login@example.com",
            Password = "TestPassword123!",
            ConfirmPassword = "TestPassword123!"
        };
        await _authService.RegisterAsync(registerRequest);

        var loginRequest = new LoginRequest
        {
            Email = "login@example.com",
            Password = "TestPassword123!"
        };

        // Act
        var result = await _authService.LoginAsync(loginRequest);

        // Assert
        result.Should().NotBeNull();
        result.AccessToken.Should().NotBeNullOrEmpty();
        result.RefreshToken.Should().NotBeNullOrEmpty();
    }

    [Fact]
    public async Task Login_WithInvalidPassword_ShouldThrowException()
    {
        // Arrange
        var registerRequest = new RegisterRequest
        {
            Email = "login2@example.com",
            Password = "TestPassword123!",
            ConfirmPassword = "TestPassword123!"
        };
        await _authService.RegisterAsync(registerRequest);

        var loginRequest = new LoginRequest
        {
            Email = "login2@example.com",
            Password = "WrongPassword!"
        };

        // Act & Assert
        await Assert.ThrowsAsync<UnauthorizedAccessException>(() => 
            _authService.LoginAsync(loginRequest));
    }

    [Fact]
    public async Task Login_WithNonExistentUser_ShouldThrowException()
    {
        // Arrange
        var loginRequest = new LoginRequest
        {
            Email = "nonexistent@example.com",
            Password = "TestPassword123!"
        };

        // Act & Assert
        await Assert.ThrowsAsync<UnauthorizedAccessException>(() => 
            _authService.LoginAsync(loginRequest));
    }

    [Fact]
    public async Task RefreshToken_WithValidToken_ShouldReturnNewTokens()
    {
        // Arrange
        var registerRequest = new RegisterRequest
        {
            Email = "refresh@example.com",
            Password = "TestPassword123!",
            ConfirmPassword = "TestPassword123!"
        };
        var authResult = await _authService.RegisterAsync(registerRequest);

        // Act
        var result = await _authService.RefreshTokenAsync(authResult.RefreshToken);

        // Assert
        result.Should().NotBeNull();
        result.AccessToken.Should().NotBeNullOrEmpty();
        result.RefreshToken.Should().NotBeNullOrEmpty();
    }

    [Fact]
    public async Task RefreshToken_WithInvalidToken_ShouldThrowException()
    {
        // Act & Assert
        await Assert.ThrowsAsync<UnauthorizedAccessException>(() => 
            _authService.RefreshTokenAsync("invalid-token"));
    }

    [Fact]
    public async Task Logout_ShouldInvalidateRefreshToken()
    {
        // Arrange
        var registerRequest = new RegisterRequest
        {
            Email = "logout@example.com",
            Password = "TestPassword123!",
            ConfirmPassword = "TestPassword123!"
        };
        var authResult = await _authService.RegisterAsync(registerRequest);

        // Act
        await _authService.LogoutAsync(authResult.User.Id);

        // Assert
        await Assert.ThrowsAsync<UnauthorizedAccessException>(() => 
            _authService.RefreshTokenAsync(authResult.RefreshToken));
    }

    [Fact]
    public void GenerateJwtToken_ShouldCreateValidToken()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "jwt@example.com",
            FirstName = "John",
            LastName = "Doe",
            Role = UserRole.User
        };

        // Act
        var token = _authService.GenerateJwtToken(user);

        // Assert
        token.Should().NotBeNullOrEmpty();
        token.Should().Contain(".");
    }

    [Fact]
    public void GenerateRefreshToken_ShouldCreateRandomToken()
    {
        // Act
        var token1 = _authService.GenerateRefreshToken();
        var token2 = _authService.GenerateRefreshToken();

        // Assert
        token1.Should().NotBeNullOrEmpty();
        token2.Should().NotBeNullOrEmpty();
        token1.Should().NotBe(token2);
    }

    [Fact]
    public async Task ChangePassword_WithValidCurrentPassword_ShouldSucceed()
    {
        // Arrange
        var registerRequest = new RegisterRequest
        {
            Email = "changepass@example.com",
            Password = "OldPassword123!",
            ConfirmPassword = "OldPassword123!"
        };
        var authResult = await _authService.RegisterAsync(registerRequest);

        var changeRequest = new ChangePasswordRequest
        {
            CurrentPassword = "OldPassword123!",
            NewPassword = "NewPassword456!",
            ConfirmPassword = "NewPassword456!"
        };

        // Act
        var result = await _authService.ChangePasswordAsync(authResult.User.Id, changeRequest);

        // Assert
        result.Should().BeTrue();

        // Verify new password works
        var loginRequest = new LoginRequest
        {
            Email = "changepass@example.com",
            Password = "NewPassword456!"
        };
        var loginResult = await _authService.LoginAsync(loginRequest);
        loginResult.Should().NotBeNull();
    }

    [Fact]
    public async Task ChangePassword_WithInvalidCurrentPassword_ShouldThrowException()
    {
        // Arrange
        var registerRequest = new RegisterRequest
        {
            Email = "changepass2@example.com",
            Password = "OldPassword123!",
            ConfirmPassword = "OldPassword123!"
        };
        var authResult = await _authService.RegisterAsync(registerRequest);

        var changeRequest = new ChangePasswordRequest
        {
            CurrentPassword = "WrongPassword!",
            NewPassword = "NewPassword456!",
            ConfirmPassword = "NewPassword456!"
        };

        // Act & Assert
        await Assert.ThrowsAsync<InvalidOperationException>(() => 
            _authService.ChangePasswordAsync(authResult.User.Id, changeRequest));
    }
}
