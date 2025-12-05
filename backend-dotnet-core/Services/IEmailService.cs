namespace CasperVPN.Services;

/// <summary>
/// Email service interface
/// </summary>
public interface IEmailService
{
    Task SendVerificationEmailAsync(string email, string token);
    Task SendPasswordResetEmailAsync(string email, string token);
    Task SendWelcomeEmailAsync(string email, string firstName);
    Task SendSubscriptionConfirmationAsync(string email, string planName);
    Task SendPaymentReceiptAsync(string email, decimal amount, string invoiceUrl);
    Task SendPaymentFailedAsync(string email);
    Task SendSubscriptionCancelledAsync(string email);
}
