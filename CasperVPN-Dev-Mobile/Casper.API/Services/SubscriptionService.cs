//using Casper.API.Models;
//using Casper.API.Models.CasperVpnDbContext;
//using Casper.API.Models.Common;
//using Casper.API.Models.Requests.SubscriptionRequest;
//using Casper.API.Models.Responses;
//using Casper.API.Services.Interfaces;
//using Microsoft.EntityFrameworkCore;
//using static Casper.API.Models.Enums;

//namespace Casper.API.Services
//{
//    public class SubscriptionService : ISubscriptionService
//    {
//        private readonly CasperVpnDbContext _context;
//        private readonly ILogger<SubscriptionService> _logger;

//        public SubscriptionService(CasperVpnDbContext context, ILogger<SubscriptionService> logger)
//        {
//            _context = context;
//            _logger = logger;
//        }

//        public async Task<Result<dynamic>> CreateSubscriptionAsync(CreateSubscriptionRequest request)
//        {
//            try
//            {
//                if (request == null)
//                    return Result<dynamic>.Failure("Request body is required", ErrorType.Validation);

//                // Validation
//                if (string.IsNullOrWhiteSpace(request.UserId))
//                    return Result<dynamic>.Failure("User ID is required", ErrorType.Validation);

//                if (string.IsNullOrWhiteSpace(request.PackageId))
//                    return Result<dynamic>.Failure("Package ID is required", ErrorType.Validation);

//                if (!Guid.TryParse(request.PackageId, out Guid packageId))
//                    return Result<dynamic>.Failure("Invalid package ID", ErrorType.Validation);

//                // Check if package exists and is active
//                var package = await _context.Packages
//                    .FirstOrDefaultAsync(p => p.Id == packageId && p.IsActive);

//                if (package == null)
//                    return Result<dynamic>.NotFound("Package not found or inactive");

//                // Check if user already has active subscription for this package
//                var existingSubscription = await _context.Subscriptions
//                    .FirstOrDefaultAsync(s => s.UserId == request.UserId &&
//                                            s.PackageId == packageId &&
//                                            s.Status == SubscriptionStatus.Active);

//                if (existingSubscription != null)
//                    return Result<dynamic>.Failure("User already has an active subscription for this package", ErrorType.Conflict);

//                // Create subscription
//                var subscription = new Subscription
//                {
//                    Id = Guid.NewGuid(),
//                    UserId = request.UserId.Trim(),
//                    PackageId = packageId,
//                    PackageName = package.Name,
//                    Price = package.Price,
//                    Currency = package.Currency,
//                    BillingCycle = package.BillingInterval,
//                    DeviceLimit = package.DeviceLimit,
//                    Status = SubscriptionStatus.Active,
//                    CreatedAt = DateTime.UtcNow,
//                    UpdatedAt = DateTime.UtcNow
//                };

//                _context.Subscriptions.Add(subscription);
//                await _context.SaveChangesAsync();

//                var response = new
//                {
//                    id = subscription.Id,
//                    userId = subscription.UserId,
//                    packageId = subscription.PackageId,
//                    packageName = subscription.PackageName,
//                    price = subscription.Price,
//                    currency = subscription.Currency,
//                    billingCycle = subscription.BillingCycle,
//                    deviceLimit = subscription.DeviceLimit,
//                    status = subscription.Status.ToString(),
//                    createdAt = subscription.CreatedAt
//                };

//                _logger.LogInformation("Subscription created for user {UserId}", subscription.UserId);
//                return Result<dynamic>.Success(response);
//            }
//            catch (Exception ex)
//            {
//                _logger.LogError(ex, "Error creating subscription");
//                return Result<dynamic>.Failure("Error creating subscription", ErrorType.InternalError);
//            }
//        }

//        public async Task<Result<dynamic>> UpdateSubscriptionAsync(UpdateSubscriptionRequest request)
//        {
//            try
//            {
//                if (request == null)
//                    return Result<dynamic>.Failure("Request body is required", ErrorType.Validation);

//                if (string.IsNullOrWhiteSpace(request.Id))
//                    return Result<dynamic>.Failure("Subscription ID is required", ErrorType.Validation);

//                if (!Guid.TryParse(request.Id, out Guid subscriptionId))
//                    return Result<dynamic>.Failure("Invalid subscription ID", ErrorType.Validation);

//                var subscription = await _context.Subscriptions.FindAsync(subscriptionId);
//                if (subscription == null)
//                    return Result<dynamic>.NotFound("Subscription not found");

//                // Handle package upgrade/downgrade
//                if (!string.IsNullOrWhiteSpace(request.PackageId))
//                {
//                    if (!Guid.TryParse(request.PackageId, out Guid newPackageId))
//                        return Result<dynamic>.Failure("Invalid package ID", ErrorType.Validation);

//                    var newPackage = await _context.Packages
//                        .FirstOrDefaultAsync(p => p.Id == newPackageId && p.IsActive);

//                    if (newPackage == null)
//                        return Result<dynamic>.NotFound("New package not found or inactive");

//                    // Update package details
//                    subscription.PackageId = newPackageId;
//                    subscription.PackageName = newPackage.Name;
//                    subscription.Price = newPackage.Price;
//                    subscription.Currency = newPackage.Currency;
//                    subscription.BillingCycle = newPackage.BillingInterval;
//                    subscription.DeviceLimit = newPackage.DeviceLimit;
//                }

//                // Update status if provided
//                if (request.Status.HasValue && request.Status != subscription.Status)
//                {
//                    subscription.Status = request.Status.Value;
//                }

//                subscription.UpdatedAt = DateTime.UtcNow;
//                await _context.SaveChangesAsync();

//                var response = new
//                {
//                    id = subscription.Id,
//                    userId = subscription.UserId,
//                    packageId = subscription.PackageId,
//                    packageName = subscription.PackageName,
//                    price = subscription.Price,
//                    currency = subscription.Currency,
//                    billingCycle = subscription.BillingCycle,
//                    deviceLimit = subscription.DeviceLimit,
//                    status = subscription.Status.ToString(),
//                    updatedAt = subscription.UpdatedAt
//                };

//                return Result<dynamic>.Success(response);
//            }
//            catch (Exception ex)
//            {
//                _logger.LogError(ex, "Error updating subscription {SubscriptionId}", request?.Id);
//                return Result<dynamic>.Failure("Error updating subscription", ErrorType.InternalError);
//            }
//        }

//        public async Task<Result<dynamic>> GetSubscriptionsAsync(string userId = null, int pageNumber = 1, int pageSize = 10)
//        {
//            try
//            {
//                if (pageNumber < 1) pageNumber = 1;
//                if (pageSize < 1 || pageSize > 100) pageSize = 10;

//                var query = _context.Subscriptions.AsQueryable();

//                // Filter by user if provided
//                if (!string.IsNullOrWhiteSpace(userId))
//                {
//                    query = query.Where(s => s.UserId == userId);
//                }

//                var totalSubscriptions = await query.CountAsync();

//                var subscriptions = await query
//                    .OrderByDescending(s => s.CreatedAt)
//                    .Skip((pageNumber - 1) * pageSize)
//                    .Take(pageSize)
//                    .Select(s => new
//                    {
//                        id = s.Id,
//                        userId = s.UserId,
//                        packageId = s.PackageId,
//                        packageName = s.PackageName,
//                        price = s.Price,
//                        currency = s.Currency,
//                        billingCycle = s.BillingCycle,
//                        deviceLimit = s.DeviceLimit,
//                        status = s.Status.ToString(),
//                        createdAt = s.CreatedAt,
//                        updatedAt = s.UpdatedAt
//                    })
//                    .ToListAsync();

//                var summary = new
//                {
//                    TotalSubscriptions = totalSubscriptions,
//                    ActiveSubscriptions = await _context.Subscriptions.CountAsync(s => s.Status == SubscriptionStatus.Active),
//                    CancelledSubscriptions = await _context.Subscriptions.CountAsync(s => s.Status == SubscriptionStatus.Cancelled)
//                };

//                var pagination = new PaginationInfo
//                {
//                    PageNumber = pageNumber,
//                    PageSize = pageSize,
//                    TotalCount = totalSubscriptions
//                };

//                return Result<dynamic>.Success(new
//                {
//                    Subscriptions = subscriptions,
//                    Summary = summary,
//                    Pagination = pagination
//                });
//            }
//            catch (Exception ex)
//            {
//                _logger.LogError(ex, "Error retrieving subscriptions");
//                return Result<dynamic>.Failure("Error retrieving subscriptions", ErrorType.InternalError);
//            }
//        }

//        public async Task<Result<dynamic>> CancelSubscriptionAsync(string id)
//        {
//            try
//            {
//                if (!Guid.TryParse(id, out Guid subscriptionId))
//                    return Result<dynamic>.Failure("Invalid subscription ID", ErrorType.Validation);

//                var subscription = await _context.Subscriptions.FindAsync(subscriptionId);
//                if (subscription == null)
//                    return Result<dynamic>.NotFound("Subscription not found");

//                if (subscription.Status == SubscriptionStatus.Cancelled)
//                    return Result<dynamic>.Failure("Subscription is already cancelled", ErrorType.Conflict);

//                subscription.Status = SubscriptionStatus.Cancelled;
//                subscription.UpdatedAt = DateTime.UtcNow;

//                await _context.SaveChangesAsync();

//                var response = new
//                {
//                    id = subscription.Id,
//                    userId = subscription.UserId,
//                    packageName = subscription.PackageName,
//                    status = subscription.Status.ToString(),
//                    cancelledAt = subscription.UpdatedAt
//                };

//                return Result<dynamic>.Success(response);
//            }
//            catch (Exception ex)
//            {
//                _logger.LogError(ex, "Error cancelling subscription {SubscriptionId}", id);
//                return Result<dynamic>.Failure("Error cancelling subscription", ErrorType.InternalError);
//            }
//        }
//    }
//}