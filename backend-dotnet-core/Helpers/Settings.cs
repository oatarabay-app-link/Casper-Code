namespace CasperVPN.Helpers;

/// <summary>
/// JWT authentication settings
/// </summary>
public class JwtSettings
{
    public string SecretKey { get; set; } = string.Empty;
    public string Issuer { get; set; } = "CasperVPN";
    public string Audience { get; set; } = "CasperVPN";
    public int ExpirationMinutes { get; set; } = 60;
    public int RefreshTokenExpirationDays { get; set; } = 7;
}

/// <summary>
/// Stripe payment settings
/// </summary>
public class StripeSettings
{
    public string SecretKey { get; set; } = string.Empty;
    public string PublishableKey { get; set; } = string.Empty;
    public string WebhookSecret { get; set; } = string.Empty;
    public string BaseUrl { get; set; } = "https://caspervpn.com";
}

/// <summary>
/// FreeRADIUS settings
/// </summary>
public class RadiusSettings
{
    public string ApiEndpoint { get; set; } = "http://localhost:3000/api/radius";
    public string ApiKey { get; set; } = string.Empty;
    public string DatabaseHost { get; set; } = "localhost";
    public int DatabasePort { get; set; } = 3306;
    public string DatabaseName { get; set; } = "radius";
    public string DatabaseUser { get; set; } = "radius";
    public string DatabasePassword { get; set; } = string.Empty;
}

/// <summary>
/// Email settings
/// </summary>
public class EmailSettings
{
    public string SmtpHost { get; set; } = "smtp.gmail.com";
    public int SmtpPort { get; set; } = 587;
    public string SmtpUsername { get; set; } = string.Empty;
    public string SmtpPassword { get; set; } = string.Empty;
    public bool UseSsl { get; set; } = true;
    public string FromEmail { get; set; } = "noreply@caspervpn.com";
    public string FromName { get; set; } = "CasperVPN";
    public string BaseUrl { get; set; } = "https://caspervpn.com";
}

/// <summary>
/// Application settings
/// </summary>
public class AppSettings
{
    public string AppName { get; set; } = "CasperVPN";
    public string Environment { get; set; } = "Development";
    public string BaseUrl { get; set; } = "https://caspervpn.com";
    public string ApiVersion { get; set; } = "1.0.0";
    public bool EnableSwagger { get; set; } = true;
    public bool UseMockServices { get; set; } = true;
}
