using Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Common;
using Casper.API.Models.Requests.PackageRequest;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.EntityFrameworkCore;
using static Casper.API.Models.Enums;

namespace Casper.API.Services
{
    public class PackageService : IPackageService
    {
        private readonly CasperVpnDbContext _context;
        private readonly ILogger<PackageService> _logger;

        public PackageService(CasperVpnDbContext context, ILogger<PackageService> logger)
        {
            _context = context;
            _logger = logger;
        }

        public async Task<Result<dynamic>> CreatePackageAsync(CreatePackageRequest request)
        {
            try
            {
                if (request == null)
                    return Result<dynamic>.Failure("Request body is required", ErrorType.Validation);

                // Check for duplicate package name
                var existingPackage = await _context.Packages
                    .FirstOrDefaultAsync(p => p.Name.ToLower() == request.Name.ToLower() && p.IsActive);

                if (existingPackage != null)
                    return Result<dynamic>.Failure($"Package '{request.Name}' already exists", ErrorType.Conflict);

                // Create package
                var package = new Package
                {
                    Id = Guid.NewGuid(),
                    Name = request.Name.Trim(),
                    Price = request.Price,
                    Currency = request.Currency.Trim(),
                    BillingInterval = request.BillingInterval.Trim(),
                    DeviceLimit = request.DeviceLimit,
                    DataPolicy = request.DataPolicy.Trim(),
                    EligibleForAddons = request.EligibleForAddons,
                    IsPopular = request.IsPopular,
                    Features = request.Features,
                    IsActive = true,
                    CreatedAt = DateTime.UtcNow,
                    UpdatedAt = DateTime.UtcNow
                };

                _context.Packages.Add(package);
                await _context.SaveChangesAsync();

                // Return response
                var response = new
                {
                    id = package.Id,
                    name = package.Name,
                    price = package.Price,
                    currency = package.Currency,
                    billingInterval = package.BillingInterval,
                    deviceLimit = package.DeviceLimit,
                    dataPolicy = package.DataPolicy,
                    eligibleForAddons = package.EligibleForAddons,
                    isPopular = package.IsPopular,
                    features = package.Features,
                    isActive = package.IsActive,
                    createdAt = package.CreatedAt
                };

                _logger.LogInformation("Package created: {PackageName}", package.Name);
                return Result<dynamic>.Success(response);
            }
            catch (DbUpdateException dbEx)
            {
                _logger.LogError(dbEx, "Database error creating package");
                return Result<dynamic>.Failure("Database error creating package", ErrorType.InternalError);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Unexpected error creating package");
                return Result<dynamic>.Failure("Unexpected error creating package", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> UpdatePackageAsync(UpdatePackageRequest request)
        {
            try
            {
                if (request == null)
                    return Result<dynamic>.Failure("Request body is required", ErrorType.Validation);

                if (!Guid.TryParse(request.Id, out Guid packageId))
                    return Result<dynamic>.Failure("Invalid package ID", ErrorType.Validation);

                var package = await _context.Packages.FindAsync(packageId);
                if (package == null || !package.IsActive)
                    return Result<dynamic>.NotFound("Package not found");

                // Check for duplicate name
                if (!string.IsNullOrWhiteSpace(request.Name) && request.Name != package.Name)
                {
                    var existingPackage = await _context.Packages
                        .FirstOrDefaultAsync(p => p.Name.ToLower() == request.Name.ToLower() &&
                                                p.Id != packageId && p.IsActive);
                    if (existingPackage != null)
                        return Result<dynamic>.Failure($"Package '{request.Name}' already exists", ErrorType.Conflict);
                }

                // Update fields
                bool hasChanges = false;

                if (!string.IsNullOrWhiteSpace(request.Name) && request.Name != package.Name)
                {
                    package.Name = request.Name.Trim();
                    hasChanges = true;
                }

                if (request.Price.HasValue && request.Price != package.Price)
                {
                    package.Price = request.Price.Value;
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Currency) && request.Currency != package.Currency)
                {
                    package.Currency = request.Currency.Trim();
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.BillingInterval) && request.BillingInterval != package.BillingInterval)
                {
                    package.BillingInterval = request.BillingInterval.Trim();
                    hasChanges = true;
                }

                if (request.DeviceLimit.HasValue && request.DeviceLimit != package.DeviceLimit)
                {
                    package.DeviceLimit = request.DeviceLimit.Value;
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.DataPolicy) && request.DataPolicy != package.DataPolicy)
                {
                    package.DataPolicy = request.DataPolicy.Trim();
                    hasChanges = true;
                }

                if (request.EligibleForAddons.HasValue && request.EligibleForAddons != package.EligibleForAddons)
                {
                    package.EligibleForAddons = request.EligibleForAddons.Value;
                    hasChanges = true;
                }

                if (request.IsPopular.HasValue && request.IsPopular != package.IsPopular)
                {
                    package.IsPopular = request.IsPopular.Value;
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Features) && request.Features != package.Features)
                {
                    package.Features = request.Features.Trim();
                    hasChanges = true;
                }

                if (!hasChanges)
                    return Result<dynamic>.Failure("No changes detected", ErrorType.Validation);

                package.UpdatedAt = DateTime.UtcNow;
                await _context.SaveChangesAsync();

                var response = new
                {
                    id = package.Id,
                    name = package.Name,
                    price = package.Price,
                    currency = package.Currency,
                    billingInterval = package.BillingInterval,
                    deviceLimit = package.DeviceLimit,
                    dataPolicy = package.DataPolicy,
                    eligibleForAddons = package.EligibleForAddons,
                    isPopular = package.IsPopular,
                    features = package.Features,
                    isActive = package.IsActive,
                    updatedAt = package.UpdatedAt
                };

                return Result<dynamic>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating package {PackageId}", request?.Id);
                return Result<dynamic>.Failure("Error updating package", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> GetPackagesAsync(int pageNumber = 1, int pageSize = 10)
        {
            try
            {
                if (pageNumber < 1) pageNumber = 1;
                if (pageSize < 1 || pageSize > 100) pageSize = 10;

                var totalPackages = await _context.Packages.CountAsync(p => p.IsActive);

                var packages = await _context.Packages
                    .Where(p => p.IsActive)
                    .OrderBy(p => p.Name)
                    .Skip((pageNumber - 1) * pageSize)
                    .Take(pageSize)
                    .Select(p => new
                    {
                        id = p.Id,
                        name = p.Name,
                        price = p.Price,
                        currency = p.Currency,
                        billingInterval = p.BillingInterval,
                        deviceLimit = p.DeviceLimit,
                        dataPolicy = p.DataPolicy,
                        eligibleForAddons = p.EligibleForAddons,
                        isPopular = p.IsPopular,
                        features = p.Features,
                        isActive = p.IsActive,
                        createdAt = p.CreatedAt
                    })
                    .ToListAsync();

                var summary = new
                {
                    TotalPackages = totalPackages,
                    PopularPackages = await _context.Packages.CountAsync(p => p.IsActive && p.IsPopular),
                    PackagesWithAddons = await _context.Packages.CountAsync(p => p.IsActive && p.EligibleForAddons)
                };

                var pagination = new PaginationInfo
                {
                    PageNumber = pageNumber,
                    PageSize = pageSize,
                    TotalCount = totalPackages
                };

                return Result<dynamic>.Success(new
                {
                    Packages = packages,
                    Summary = summary,
                    Pagination = pagination
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving packages");
                return Result<dynamic>.Failure("Error retrieving packages", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> DeletePackageAsync(string id)
        {
            try
            {
                if (!Guid.TryParse(id, out Guid packageId))
                    return Result<dynamic>.Failure("Invalid package ID", ErrorType.Validation);

                var package = await _context.Packages.FindAsync(packageId);
                if (package == null)
                    return Result<dynamic>.NotFound("Package not found");

                if (!package.IsActive)
                    return Result<dynamic>.Failure("Package is already deactivated", ErrorType.Conflict);

                // Check if package has active subscriptions
                var activeSubscriptions = await _context.Subscriptions
                    .CountAsync(s => s.PackageId == packageId && s.Status == SubscriptionStatus.Active);

                if (activeSubscriptions > 0)
                    return Result<dynamic>.Failure("Cannot deactivate package with active subscriptions", ErrorType.Conflict);

                package.IsActive = false;
                package.UpdatedAt = DateTime.UtcNow;

                await _context.SaveChangesAsync();

                var response = new
                {
                    id = package.Id,
                    name = package.Name,
                    deactivatedAt = package.UpdatedAt
                };

                return Result<dynamic>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error deactivating package {PackageId}", id);
                return Result<dynamic>.Failure("Error deactivating package", ErrorType.InternalError);
            }
        }
    }
}