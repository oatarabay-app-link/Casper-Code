using System.Net;
using System.Net.Mail;
using Microsoft.Extensions.Options;
using CasperVPN.Helpers;

namespace CasperVPN.Services;

/// <summary>
/// Email service implementation using SMTP
/// </summary>
public class EmailService : IEmailService
{
    private readonly EmailSettings _settings;
    private readonly ILogger<EmailService> _logger;

    public EmailService(IOptions<EmailSettings> settings, ILogger<EmailService> logger)
    {
        _settings = settings.Value;
        _logger = logger;
    }

    public async Task SendVerificationEmailAsync(string email, string token)
    {
        var verifyUrl = $"{_settings.BaseUrl}/verify-email?token={token}&email={Uri.EscapeDataString(email)}";

        var subject = "Verify your CasperVPN email address";
        var body = $@"
            <html>
            <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>CasperVPN</h1>
                </div>
                <div style='padding: 40px; background: #f9f9f9;'>
                    <h2 style='color: #333;'>Verify your email address</h2>
                    <p style='color: #666; line-height: 1.6;'>
                        Thank you for registering with CasperVPN. Please click the button below to verify your email address.
                    </p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{verifyUrl}' style='background: #667eea; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            Verify Email
                        </a>
                    </div>
                    <p style='color: #999; font-size: 12px;'>
                        This link will expire in 7 days. If you didn't create an account, please ignore this email.
                    </p>
                </div>
            </body>
            </html>";

        await SendEmailAsync(email, subject, body);
    }

    public async Task SendPasswordResetEmailAsync(string email, string token)
    {
        var resetUrl = $"{_settings.BaseUrl}/reset-password?token={token}&email={Uri.EscapeDataString(email)}";

        var subject = "Reset your CasperVPN password";
        var body = $@"
            <html>
            <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>CasperVPN</h1>
                </div>
                <div style='padding: 40px; background: #f9f9f9;'>
                    <h2 style='color: #333;'>Reset your password</h2>
                    <p style='color: #666; line-height: 1.6;'>
                        We received a request to reset your password. Click the button below to create a new password.
                    </p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{resetUrl}' style='background: #667eea; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            Reset Password
                        </a>
                    </div>
                    <p style='color: #999; font-size: 12px;'>
                        This link will expire in 24 hours. If you didn't request this, please ignore this email.
                    </p>
                </div>
            </body>
            </html>";

        await SendEmailAsync(email, subject, body);
    }

    public async Task SendWelcomeEmailAsync(string email, string firstName)
    {
        var subject = "Welcome to CasperVPN!";
        var body = $@"
            <html>
            <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>Welcome to CasperVPN!</h1>
                </div>
                <div style='padding: 40px; background: #f9f9f9;'>
                    <h2 style='color: #333;'>Hello {firstName}!</h2>
                    <p style='color: #666; line-height: 1.6;'>
                        Thank you for joining CasperVPN. Your secure browsing journey begins now.
                    </p>
                    <h3 style='color: #333;'>Getting Started:</h3>
                    <ul style='color: #666; line-height: 1.8;'>
                        <li>Download our app for iOS or Android</li>
                        <li>Connect to any of our global servers</li>
                        <li>Enjoy fast, secure, and private internet access</li>
                    </ul>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{_settings.BaseUrl}' style='background: #667eea; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            Get Started
                        </a>
                    </div>
                </div>
            </body>
            </html>";

        await SendEmailAsync(email, subject, body);
    }

    public async Task SendSubscriptionConfirmationAsync(string email, string planName)
    {
        var subject = "Subscription Confirmed - CasperVPN";
        var body = $@"
            <html>
            <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>CasperVPN</h1>
                </div>
                <div style='padding: 40px; background: #f9f9f9;'>
                    <h2 style='color: #333;'>🎉 Subscription Confirmed!</h2>
                    <p style='color: #666; line-height: 1.6;'>
                        Your <strong>{planName}</strong> subscription is now active. You have access to all premium features!
                    </p>
                    <ul style='color: #666; line-height: 1.8;'>
                        <li>✓ All premium servers unlocked</li>
                        <li>✓ Unlimited bandwidth</li>
                        <li>✓ Up to 5 devices</li>
                        <li>✓ Priority support</li>
                    </ul>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{_settings.BaseUrl}/dashboard' style='background: #667eea; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            </body>
            </html>";

        await SendEmailAsync(email, subject, body);
    }

    public async Task SendPaymentReceiptAsync(string email, decimal amount, string invoiceUrl)
    {
        var subject = "Payment Receipt - CasperVPN";
        var body = $@"
            <html>
            <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>Payment Receipt</h1>
                </div>
                <div style='padding: 40px; background: #f9f9f9;'>
                    <h2 style='color: #333;'>Thank you for your payment!</h2>
                    <p style='color: #666; line-height: 1.6;'>
                        We've received your payment of <strong>${amount:F2}</strong>.
                    </p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{invoiceUrl}' style='background: #667eea; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            View Invoice
                        </a>
                    </div>
                </div>
            </body>
            </html>";

        await SendEmailAsync(email, subject, body);
    }

    public async Task SendPaymentFailedAsync(string email)
    {
        var subject = "Payment Failed - Action Required";
        var body = $@"
            <html>
            <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #dc3545; padding: 40px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>Payment Failed</h1>
                </div>
                <div style='padding: 40px; background: #f9f9f9;'>
                    <h2 style='color: #333;'>Action Required</h2>
                    <p style='color: #666; line-height: 1.6;'>
                        We were unable to process your payment. Please update your payment method to continue using CasperVPN Premium.
                    </p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{_settings.BaseUrl}/account/billing' style='background: #dc3545; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            Update Payment Method
                        </a>
                    </div>
                </div>
            </body>
            </html>";

        await SendEmailAsync(email, subject, body);
    }

    public async Task SendSubscriptionCancelledAsync(string email)
    {
        var subject = "Subscription Cancelled - CasperVPN";
        var body = $@"
            <html>
            <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>CasperVPN</h1>
                </div>
                <div style='padding: 40px; background: #f9f9f9;'>
                    <h2 style='color: #333;'>We're sad to see you go</h2>
                    <p style='color: #666; line-height: 1.6;'>
                        Your CasperVPN subscription has been cancelled. You can still use your account with the Free plan.
                    </p>
                    <p style='color: #666; line-height: 1.6;'>
                        If you change your mind, you're always welcome back!
                    </p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{_settings.BaseUrl}/pricing' style='background: #667eea; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            View Plans
                        </a>
                    </div>
                </div>
            </body>
            </html>";

        await SendEmailAsync(email, subject, body);
    }

    private async Task SendEmailAsync(string to, string subject, string body)
    {
        try
        {
            using var client = new SmtpClient(_settings.SmtpHost, _settings.SmtpPort)
            {
                Credentials = new NetworkCredential(_settings.SmtpUsername, _settings.SmtpPassword),
                EnableSsl = _settings.UseSsl
            };

            var message = new MailMessage
            {
                From = new MailAddress(_settings.FromEmail, _settings.FromName),
                Subject = subject,
                Body = body,
                IsBodyHtml = true
            };
            message.To.Add(to);

            await client.SendMailAsync(message);

            _logger.LogInformation("Email sent to {Email}: {Subject}", to, subject);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to send email to {Email}: {Subject}", to, subject);
            throw;
        }
    }
}

/// <summary>
/// Mock email service for development/testing
/// </summary>
public class MockEmailService : IEmailService
{
    private readonly ILogger<MockEmailService> _logger;

    public MockEmailService(ILogger<MockEmailService> logger)
    {
        _logger = logger;
    }

    public Task SendVerificationEmailAsync(string email, string token)
    {
        _logger.LogInformation("[MOCK EMAIL] Verification email to {Email}, token: {Token}", email, token);
        return Task.CompletedTask;
    }

    public Task SendPasswordResetEmailAsync(string email, string token)
    {
        _logger.LogInformation("[MOCK EMAIL] Password reset email to {Email}, token: {Token}", email, token);
        return Task.CompletedTask;
    }

    public Task SendWelcomeEmailAsync(string email, string firstName)
    {
        _logger.LogInformation("[MOCK EMAIL] Welcome email to {Email}", email);
        return Task.CompletedTask;
    }

    public Task SendSubscriptionConfirmationAsync(string email, string planName)
    {
        _logger.LogInformation("[MOCK EMAIL] Subscription confirmation to {Email}, plan: {Plan}", email, planName);
        return Task.CompletedTask;
    }

    public Task SendPaymentReceiptAsync(string email, decimal amount, string invoiceUrl)
    {
        _logger.LogInformation("[MOCK EMAIL] Payment receipt to {Email}, amount: {Amount}", email, amount);
        return Task.CompletedTask;
    }

    public Task SendPaymentFailedAsync(string email)
    {
        _logger.LogInformation("[MOCK EMAIL] Payment failed notification to {Email}", email);
        return Task.CompletedTask;
    }

    public Task SendSubscriptionCancelledAsync(string email)
    {
        _logger.LogInformation("[MOCK EMAIL] Subscription cancelled notification to {Email}", email);
        return Task.CompletedTask;
    }
}
