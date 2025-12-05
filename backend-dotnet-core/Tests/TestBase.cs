using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;
using Moq;
using CasperVPN.Data;
using CasperVPN.Helpers;
using CasperVPN.Services;

namespace CasperVPN.Tests;

/// <summary>
/// Base class for unit tests with common setup
/// </summary>
public abstract class TestBase : IDisposable
{
    protected readonly ApplicationDbContext DbContext;
    protected readonly Mock<ILogger<AuthService>> AuthLoggerMock;
    protected readonly Mock<ILogger<UserService>> UserLoggerMock;
    protected readonly Mock<ILogger<SubscriptionService>> SubscriptionLoggerMock;
    protected readonly Mock<ILogger<VpnServerService>> ServerLoggerMock;
    protected readonly Mock<ILogger<StripeService>> StripeLoggerMock;
    protected readonly Mock<ILogger<AdminService>> AdminLoggerMock;
    protected readonly Mock<IEmailService> EmailServiceMock;
    protected readonly Mock<IFreeRadiusService> RadiusServiceMock;
    protected readonly Mock<IStripeService> StripeServiceMock;
    protected readonly IOptions<JwtSettings> JwtSettings;
    protected readonly IOptions<StripeSettings> StripeSettings;

    protected TestBase()
    {
        // Create in-memory database
        var options = new DbContextOptionsBuilder<ApplicationDbContext>()
            .UseInMemoryDatabase(databaseName: Guid.NewGuid().ToString())
            .Options;

        DbContext = new ApplicationDbContext(options);
        DbContext.Database.EnsureCreated();

        // Create mocks
        AuthLoggerMock = new Mock<ILogger<AuthService>>();
        UserLoggerMock = new Mock<ILogger<UserService>>();
        SubscriptionLoggerMock = new Mock<ILogger<SubscriptionService>>();
        ServerLoggerMock = new Mock<ILogger<VpnServerService>>();
        StripeLoggerMock = new Mock<ILogger<StripeService>>();
        AdminLoggerMock = new Mock<ILogger<AdminService>>();

        EmailServiceMock = new Mock<IEmailService>();
        RadiusServiceMock = new Mock<IFreeRadiusService>();
        StripeServiceMock = new Mock<IStripeService>();

        // Setup mock behaviors
        EmailServiceMock.Setup(x => x.SendVerificationEmailAsync(It.IsAny<string>(), It.IsAny<string>()))
            .Returns(Task.CompletedTask);
        EmailServiceMock.Setup(x => x.SendPasswordResetEmailAsync(It.IsAny<string>(), It.IsAny<string>()))
            .Returns(Task.CompletedTask);
        EmailServiceMock.Setup(x => x.SendWelcomeEmailAsync(It.IsAny<string>(), It.IsAny<string>()))
            .Returns(Task.CompletedTask);

        RadiusServiceMock.Setup(x => x.CreateUserAsync(It.IsAny<string>(), It.IsAny<string>()))
            .ReturnsAsync(true);
        RadiusServiceMock.Setup(x => x.DeleteUserAsync(It.IsAny<string>()))
            .ReturnsAsync(true);
        RadiusServiceMock.Setup(x => x.UpdateUserPasswordAsync(It.IsAny<string>(), It.IsAny<string>()))
            .ReturnsAsync(true);

        // Create settings
        JwtSettings = Options.Create(new JwtSettings
        {
            SecretKey = "TestSecretKeyForJWTAuthentication2024!",
            Issuer = "CasperVPN-Test",
            Audience = "CasperVPN-Test",
            ExpirationMinutes = 60,
            RefreshTokenExpirationDays = 7
        });

        StripeSettings = Options.Create(new StripeSettings
        {
            SecretKey = "sk_test_fake",
            PublishableKey = "pk_test_fake",
            WebhookSecret = "whsec_fake",
            BaseUrl = "https://test.caspervpn.com"
        });
    }

    public void Dispose()
    {
        DbContext.Dispose();
        GC.SuppressFinalize(this);
    }
}
