using FluentAssertions;
using Xunit;
using CasperVPN.DTOs;
using CasperVPN.Models;
using CasperVPN.Services;

namespace CasperVPN.Tests;

public class UserServiceTests : TestBase
{
    private readonly UserService _userService;

    public UserServiceTests()
    {
        _userService = new UserService(
            DbContext,
            UserLoggerMock.Object,
            RadiusServiceMock.Object,
            StripeServiceMock.Object);
    }

    [Fact]
    public async Task GetUserById_WithExistingUser_ShouldReturnUser()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "test@example.com",
            PasswordHash = "hash",
            FirstName = "John",
            LastName = "Doe"
        };
        DbContext.Users.Add(user);
        await DbContext.SaveChangesAsync();

        // Act
        var result = await _userService.GetUserByIdAsync(user.Id);

        // Assert
        result.Should().NotBeNull();
        result!.Email.Should().Be("test@example.com");
        result.FirstName.Should().Be("John");
    }

    [Fact]
    public async Task GetUserById_WithNonExistentUser_ShouldReturnNull()
    {
        // Act
        var result = await _userService.GetUserByIdAsync(Guid.NewGuid());

        // Assert
        result.Should().BeNull();
    }

    [Fact]
    public async Task UpdateUserProfile_ShouldUpdateFields()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "update@example.com",
            PasswordHash = "hash",
            FirstName = "John",
            LastName = "Doe"
        };
        DbContext.Users.Add(user);
        await DbContext.SaveChangesAsync();

        var updateRequest = new UpdateProfileRequest
        {
            FirstName = "Jane",
            LastName = "Smith"
        };

        // Act
        var result = await _userService.UpdateUserProfileAsync(user.Id, updateRequest);

        // Assert
        result.Should().NotBeNull();
        result.FirstName.Should().Be("Jane");
        result.LastName.Should().Be("Smith");
    }

    [Fact]
    public async Task UpdateUserProfile_WithNewEmail_ShouldUpdateAndUnverify()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "oldemail@example.com",
            PasswordHash = "hash",
            IsEmailVerified = true
        };
        DbContext.Users.Add(user);
        await DbContext.SaveChangesAsync();

        var updateRequest = new UpdateProfileRequest
        {
            Email = "newemail@example.com"
        };

        // Act
        var result = await _userService.UpdateUserProfileAsync(user.Id, updateRequest);

        // Assert
        result.Should().NotBeNull();
        result.Email.Should().Be("newemail@example.com");
        result.IsEmailVerified.Should().BeFalse();
    }

    [Fact]
    public async Task UpdateUserProfile_WithDuplicateEmail_ShouldThrowException()
    {
        // Arrange
        var user1 = new User
        {
            Id = Guid.NewGuid(),
            Email = "user1@example.com",
            PasswordHash = "hash"
        };
        var user2 = new User
        {
            Id = Guid.NewGuid(),
            Email = "user2@example.com",
            PasswordHash = "hash"
        };
        DbContext.Users.AddRange(user1, user2);
        await DbContext.SaveChangesAsync();

        var updateRequest = new UpdateProfileRequest
        {
            Email = "user1@example.com" // Try to use existing email
        };

        // Act & Assert
        await Assert.ThrowsAsync<InvalidOperationException>(() => 
            _userService.UpdateUserProfileAsync(user2.Id, updateRequest));
    }

    [Fact]
    public async Task DeleteUser_ShouldDeactivateUser()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "delete@example.com",
            PasswordHash = "hash",
            IsActive = true
        };
        DbContext.Users.Add(user);
        await DbContext.SaveChangesAsync();

        // Act
        var result = await _userService.DeleteUserAsync(user.Id);

        // Assert
        result.Should().BeTrue();
        var deletedUser = await DbContext.Users.FindAsync(user.Id);
        deletedUser!.IsActive.Should().BeFalse();
    }

    [Fact]
    public async Task GetUsers_WithPagination_ShouldReturnPaginatedResults()
    {
        // Arrange
        for (int i = 0; i < 25; i++)
        {
            DbContext.Users.Add(new User
            {
                Id = Guid.NewGuid(),
                Email = $"user{i}@example.com",
                PasswordHash = "hash"
            });
        }
        await DbContext.SaveChangesAsync();

        var pagination = new PaginationParams
        {
            Page = 1,
            PageSize = 10
        };

        // Act
        var result = await _userService.GetUsersAsync(pagination);

        // Assert
        result.Should().NotBeNull();
        result.Items.Count.Should().Be(10);
        result.TotalCount.Should().BeGreaterOrEqualTo(25);
        result.HasNextPage.Should().BeTrue();
    }

    [Fact]
    public async Task GetUsers_WithSearch_ShouldFilterResults()
    {
        // Arrange
        DbContext.Users.Add(new User
        {
            Id = Guid.NewGuid(),
            Email = "john.doe@example.com",
            PasswordHash = "hash",
            FirstName = "John",
            LastName = "Doe"
        });
        DbContext.Users.Add(new User
        {
            Id = Guid.NewGuid(),
            Email = "jane.smith@example.com",
            PasswordHash = "hash",
            FirstName = "Jane",
            LastName = "Smith"
        });
        await DbContext.SaveChangesAsync();

        var pagination = new PaginationParams
        {
            Page = 1,
            PageSize = 10,
            Search = "john"
        };

        // Act
        var result = await _userService.GetUsersAsync(pagination);

        // Assert
        result.Should().NotBeNull();
        result.Items.Should().OnlyContain(u => 
            u.Email.Contains("john", StringComparison.OrdinalIgnoreCase) ||
            u.FirstName.Contains("john", StringComparison.OrdinalIgnoreCase));
    }

    [Fact]
    public async Task AdminUpdateUser_ShouldUpdateAllFields()
    {
        // Arrange
        var user = new User
        {
            Id = Guid.NewGuid(),
            Email = "admin@example.com",
            PasswordHash = "hash",
            FirstName = "Admin",
            LastName = "User",
            IsActive = true,
            Role = UserRole.User
        };
        DbContext.Users.Add(user);
        await DbContext.SaveChangesAsync();

        var updateRequest = new AdminUpdateUserRequest
        {
            FirstName = "Updated",
            Role = UserRole.Admin,
            IsActive = false
        };

        // Act
        var result = await _userService.AdminUpdateUserAsync(user.Id, updateRequest);

        // Assert
        result.Should().NotBeNull();
        result.FirstName.Should().Be("Updated");
        result.Role.Should().Be(UserRole.Admin);
        result.IsActive.Should().BeFalse();
    }
}
