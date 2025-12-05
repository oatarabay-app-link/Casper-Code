using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Swashbuckle.AspNetCore.Annotations;
using CasperVPN.DTOs;
using CasperVPN.Services;

namespace CasperVPN.Controllers;

/// <summary>
/// Payment and billing controller
/// </summary>
[ApiController]
[Route("api/[controller]")]
[Produces("application/json")]
public class PaymentsController : ControllerBase
{
    private readonly IStripeService _stripeService;
    private readonly ILogger<PaymentsController> _logger;

    public PaymentsController(IStripeService stripeService, ILogger<PaymentsController> logger)
    {
        _stripeService = stripeService;
        _logger = logger;
    }

    /// <summary>
    /// Create a Stripe checkout session
    /// </summary>
    /// <param name="request">Checkout session request</param>
    /// <returns>Checkout session details</returns>
    [HttpPost("create-checkout-session")]
    [Authorize]
    [SwaggerOperation(Summary = "Create checkout session", Description = "Creates a Stripe checkout session for subscription payment")]
    [SwaggerResponse(200, "Session created", typeof(ApiResponse<CheckoutSessionResponse>))]
    [SwaggerResponse(400, "Invalid request", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<CheckoutSessionResponse>>> CreateCheckoutSession([FromBody] CreateCheckoutSessionRequest request)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<CheckoutSessionResponse>.ErrorResponse("Unauthorized"));

            var session = await _stripeService.CreateCheckoutSessionAsync(userId.Value, request);
            return Ok(ApiResponse<CheckoutSessionResponse>.SuccessResponse(session));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse<CheckoutSessionResponse>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error creating checkout session");
            return BadRequest(ApiResponse<CheckoutSessionResponse>.ErrorResponse("Failed to create checkout session"));
        }
    }

    /// <summary>
    /// Handle Stripe webhook events
    /// </summary>
    /// <returns>Webhook acknowledgement</returns>
    [HttpPost("webhook")]
    [SwaggerOperation(Summary = "Stripe webhook", Description = "Handles Stripe webhook events for payment processing")]
    [SwaggerResponse(200, "Webhook processed")]
    [SwaggerResponse(400, "Invalid webhook")]
    public async Task<IActionResult> HandleWebhook()
    {
        try
        {
            var json = await new StreamReader(HttpContext.Request.Body).ReadToEndAsync();
            var signature = Request.Headers["Stripe-Signature"].FirstOrDefault();

            if (string.IsNullOrEmpty(signature))
            {
                _logger.LogWarning("Missing Stripe-Signature header");
                return BadRequest("Missing signature");
            }

            await _stripeService.HandleWebhookEventAsync(json, signature);
            return Ok();
        }
        catch (InvalidOperationException ex)
        {
            _logger.LogError(ex, "Webhook validation failed");
            return BadRequest(ex.Message);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error processing webhook");
            return BadRequest("Webhook processing failed");
        }
    }

    /// <summary>
    /// Get payment history
    /// </summary>
    /// <param name="limit">Number of payments to return (default: 20)</param>
    /// <returns>Payment history</returns>
    [HttpGet("history")]
    [Authorize]
    [SwaggerOperation(Summary = "Get payment history", Description = "Gets the user's payment history")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<PaymentHistoryResponse>))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<PaymentHistoryResponse>>> GetPaymentHistory([FromQuery] int limit = 20)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<PaymentHistoryResponse>.ErrorResponse("Unauthorized"));

            var history = await _stripeService.GetPaymentHistoryAsync(userId.Value, limit);
            return Ok(ApiResponse<PaymentHistoryResponse>.SuccessResponse(history));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting payment history");
            return BadRequest(ApiResponse<PaymentHistoryResponse>.ErrorResponse("Failed to get payment history"));
        }
    }

    /// <summary>
    /// Get invoices
    /// </summary>
    /// <param name="limit">Number of invoices to return (default: 20)</param>
    /// <returns>Invoice list</returns>
    [HttpGet("invoices")]
    [Authorize]
    [SwaggerOperation(Summary = "Get invoices", Description = "Gets the user's invoices")]
    [SwaggerResponse(200, "Success", typeof(ApiResponse<InvoiceListResponse>))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<InvoiceListResponse>>> GetInvoices([FromQuery] int limit = 20)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<InvoiceListResponse>.ErrorResponse("Unauthorized"));

            var invoices = await _stripeService.GetInvoicesAsync(userId.Value, limit);
            return Ok(ApiResponse<InvoiceListResponse>.SuccessResponse(invoices));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting invoices");
            return BadRequest(ApiResponse<InvoiceListResponse>.ErrorResponse("Failed to get invoices"));
        }
    }

    /// <summary>
    /// Create billing portal session
    /// </summary>
    /// <param name="request">Portal session request</param>
    /// <returns>Billing portal URL</returns>
    [HttpPost("portal")]
    [Authorize]
    [SwaggerOperation(Summary = "Create billing portal", Description = "Creates a Stripe billing portal session for subscription management")]
    [SwaggerResponse(200, "Portal session created", typeof(ApiResponse<BillingPortalResponse>))]
    [SwaggerResponse(400, "No billing account", typeof(ApiResponse))]
    [SwaggerResponse(401, "Unauthorized", typeof(ApiResponse))]
    public async Task<ActionResult<ApiResponse<BillingPortalResponse>>> CreateBillingPortal([FromBody] CreateBillingPortalRequest? request = null)
    {
        try
        {
            var userId = GetCurrentUserId();
            if (userId == null) return Unauthorized(ApiResponse<BillingPortalResponse>.ErrorResponse("Unauthorized"));

            var portal = await _stripeService.CreateBillingPortalSessionAsync(userId.Value, request?.ReturnUrl);
            return Ok(ApiResponse<BillingPortalResponse>.SuccessResponse(portal));
        }
        catch (InvalidOperationException ex)
        {
            return BadRequest(ApiResponse<BillingPortalResponse>.ErrorResponse(ex.Message));
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error creating billing portal");
            return BadRequest(ApiResponse<BillingPortalResponse>.ErrorResponse("Failed to create billing portal"));
        }
    }

    private Guid? GetCurrentUserId()
    {
        var userIdClaim = User.FindFirst(System.Security.Claims.ClaimTypes.NameIdentifier)?.Value;
        return Guid.TryParse(userIdClaim, out var userId) ? userId : null;
    }
}
