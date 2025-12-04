using Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Common;
using Casper.API.Models.Requests.CouponRequest;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.EntityFrameworkCore;
using static Casper.API.Models.Enums;

namespace Casper.API.Services
{
    public class CouponService : ICouponService
    {
        private readonly CasperVpnDbContext _context;
        private readonly ILogger<CouponService> _logger;

        public CouponService(CasperVpnDbContext context, ILogger<CouponService> logger)
        {
            _context = context;
            _logger = logger;
        }

        public async Task<Result<dynamic>> CreateCouponAsync(CreateCouponRequest request)
        {
            try
            {
                if (request == null)
                    return Result<dynamic>.Failure("Request body is required", ErrorType.Validation);

                // Check for duplicate coupon code
                var existingCoupon = await _context.Coupons
                    .FirstOrDefaultAsync(c => c.Code.ToLower() == request.Code.ToLower() && c.IsActive);

                if (existingCoupon != null)
                    return Result<dynamic>.Failure($"Coupon code '{request.Code}' already exists", ErrorType.Conflict);

                // Create coupon
                var coupon = new Coupon
                {
                    Id = Guid.NewGuid(),
                    Code = request.Code.Trim().ToUpper(),
                    Description = request.Description.Trim(),
                    Type = request.Type,
                    Value = request.Value,
                    ValidFrom = request.ValidFrom,
                    ValidTo = request.ValidTo,
                    UsageLimit = request.UsageLimit,
                    UsedCount = 0,
                    Status = request.Status,
                    IsActive = true,
                    CreatedAt = DateTime.UtcNow,
                    UpdatedAt = DateTime.UtcNow
                };

                _context.Coupons.Add(coupon);
                await _context.SaveChangesAsync();

                var response = new
                {
                    id = coupon.Id,
                    code = coupon.Code,
                    description = coupon.Description,
                    type = coupon.Type,
                    value = coupon.Type == "Percent" ? $"{coupon.Value:F4}%" : $"${coupon.Value:F4} USD",
                    validFrom = coupon.ValidFrom.ToString("yyyy-MM-dd"),
                    validTo = coupon.ValidTo.ToString("yyyy-MM-dd"),
                    usage = $"{coupon.UsedCount}/{coupon.UsageLimit}",
                    status = coupon.Status,
                    isActive = coupon.IsActive,
                    createdAt = coupon.CreatedAt
                };

                _logger.LogInformation("Coupon created: {Code}", coupon.Code);
                return Result<dynamic>.Success(response);
            }
            catch (DbUpdateException dbEx)
            {
                _logger.LogError(dbEx, "Database error creating coupon");
                return Result<dynamic>.Failure("Database error creating coupon", ErrorType.InternalError);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Unexpected error creating coupon");
                return Result<dynamic>.Failure("Unexpected error creating coupon", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> UpdateCouponAsync(UpdateCouponRequest request)
        {
            try
            {
                if (request == null)
                    return Result<dynamic>.Failure("Request body is required", ErrorType.Validation);

                if (!Guid.TryParse(request.Id, out Guid couponId))
                    return Result<dynamic>.Failure("Invalid coupon ID", ErrorType.Validation);

                var coupon = await _context.Coupons.FindAsync(couponId);
                if (coupon == null || !coupon.IsActive)
                    return Result<dynamic>.NotFound("Coupon not found");

                // Check for duplicate code
                if (!string.IsNullOrWhiteSpace(request.Code) && request.Code != coupon.Code)
                {
                    var existingCoupon = await _context.Coupons
                        .FirstOrDefaultAsync(c => c.Code.ToLower() == request.Code.ToLower() &&
                                                c.Id != couponId);
                    if (existingCoupon != null)
                        return Result<dynamic>.Failure($"Coupon code '{request.Code}' already exists", ErrorType.Conflict);
                }

                // Update fields
                bool hasChanges = false;

                if (!string.IsNullOrWhiteSpace(request.Code) && request.Code != coupon.Code)
                {
                    coupon.Code = request.Code.Trim().ToUpper();
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Description) && request.Description != coupon.Description)
                {
                    coupon.Description = request.Description.Trim();
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Type) && request.Type != coupon.Type)
                {
                    coupon.Type = request.Type;
                    hasChanges = true;
                }

                if (request.Value.HasValue && request.Value != coupon.Value)
                {
                    coupon.Value = request.Value.Value;
                    hasChanges = true;
                }

                if (request.ValidFrom.HasValue && request.ValidFrom != coupon.ValidFrom)
                {
                    coupon.ValidFrom = request.ValidFrom.Value;
                    hasChanges = true;
                }

                if (request.ValidTo.HasValue && request.ValidTo != coupon.ValidTo)
                {
                    coupon.ValidTo = request.ValidTo.Value;
                    hasChanges = true;
                }

                if (request.UsageLimit.HasValue && request.UsageLimit != coupon.UsageLimit)
                {
                    coupon.UsageLimit = request.UsageLimit.Value;
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Status) && request.Status != coupon.Status)
                {
                    coupon.Status = request.Status;
                    hasChanges = true;
                }

                if (!hasChanges)
                    return Result<dynamic>.Failure("No changes detected", ErrorType.Validation);

                coupon.UpdatedAt = DateTime.UtcNow;
                await _context.SaveChangesAsync();

                var response = new
                {
                    id = coupon.Id,
                    code = coupon.Code,
                    description = coupon.Description,
                    type = coupon.Type,

                    value = coupon.Type == "Percent" ? $"{coupon.Value:F4}%" : $"${coupon.Value:F4} USD",
                    validFrom = coupon.ValidFrom.ToString("yyyy-MM-dd"),
                    validTo = coupon.ValidTo.ToString("yyyy-MM-dd"),
                    usage = $"{coupon.UsedCount}/{coupon.UsageLimit}",
                    status = coupon.Status,
                    isActive = coupon.IsActive,
                    updatedAt = coupon.UpdatedAt
                };

                return Result<dynamic>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating coupon {CouponId}", request?.Id);
                return Result<dynamic>.Failure("Error updating coupon", ErrorType.InternalError);
            }
        }

        // Other methods remain the same...
        public async Task<Result<dynamic>> GetCouponsAsync(int pageNumber = 1, int pageSize = 10)
        {
            try
            {
                if (pageNumber < 1) pageNumber = 1;
                if (pageSize < 1 || pageSize > 100) pageSize = 10;

                var totalCoupons = await _context.Coupons.CountAsync(c => c.IsActive);

                var coupons = await _context.Coupons
                    .Where(c => c.IsActive)
                    .OrderByDescending(c => c.CreatedAt)
                    .Skip((pageNumber - 1) * pageSize)
                    .Take(pageSize)
                    .Select(c => new
                    {
                        id = c.Id,
                        code = c.Code,
                        description = c.Description,
                        type = c.Type,

                        value = c.Type == "Percent" ? $"{c.Value:F2}%" : $"${c.Value:F4} USD",
                        validFrom = c.ValidFrom.ToString("yyyy-MM-dd"),
                        validTo = c.ValidTo.ToString("yyyy-MM-dd"),
                        usage = $"{c.UsedCount}/{c.UsageLimit}",
                        status = c.Status,
                        isActive = c.IsActive,
                        createdAt = c.CreatedAt
                    })
                    .ToListAsync();

                var summary = new
                {
                    TotalCoupons = totalCoupons,
                    ActiveCoupons = await _context.Coupons.CountAsync(c => c.IsActive && c.Status == "Active"),
                    ScheduledCoupons = await _context.Coupons.CountAsync(c => c.IsActive && c.Status == "Scheduled")
                };

                var pagination = new PaginationInfo
                {
                    PageNumber = pageNumber,
                    PageSize = pageSize,
                    TotalCount = totalCoupons
                };

                return Result<dynamic>.Success(new
                {
                    Coupons = coupons,
                    Summary = summary,
                    Pagination = pagination
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving coupons");
                return Result<dynamic>.Failure("Error retrieving coupons", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> DeleteCouponAsync(string id)
        {
            try
            {
                if (!Guid.TryParse(id, out Guid couponId))
                    return Result<dynamic>.Failure("Invalid coupon ID", ErrorType.Validation);

                var coupon = await _context.Coupons.FindAsync(couponId);
                if (coupon == null)
                    return Result<dynamic>.NotFound("Coupon not found");

                if (!coupon.IsActive)
                    return Result<dynamic>.Failure("Coupon is already deactivated", ErrorType.Conflict);

                coupon.IsActive = false;
                coupon.UpdatedAt = DateTime.UtcNow;

                await _context.SaveChangesAsync();

                var response = new
                {
                    id = coupon.Id,
                    code = coupon.Code,
                    deactivatedAt = coupon.UpdatedAt
                };

                return Result<dynamic>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error deactivating coupon {CouponId}", id);
                return Result<dynamic>.Failure("Error deactivating coupon", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> ValidateCouponAsync(string code)
        {
            try
            {
                if (string.IsNullOrWhiteSpace(code))
                    return Result<dynamic>.Failure("Coupon code is required", ErrorType.Validation);

                var coupon = await _context.Coupons
                    .FirstOrDefaultAsync(c => c.Code.ToLower() == code.Trim().ToLower() && c.IsActive);

                if (coupon == null)
                    return Result<dynamic>.Failure("Invalid coupon code", ErrorType.NotFound);

                if (coupon.Status != "Active")
                    return Result<dynamic>.Failure("Coupon is not active", ErrorType.Validation);

                var now = DateTime.UtcNow;
                if (coupon.ValidTo < now)
                    return Result<dynamic>.Failure("Coupon has expired", ErrorType.Validation);

                if (coupon.ValidFrom > now)
                    return Result<dynamic>.Failure("Coupon is not yet valid", ErrorType.Validation);

                if (coupon.UsedCount >= coupon.UsageLimit)
                    return Result<dynamic>.Failure("Coupon usage limit reached", ErrorType.Validation);

                var response = new
                {
                    id = coupon.Id,
                    code = coupon.Code,
                    description = coupon.Description,
                    type = coupon.Type,
                    value = coupon.Value,
                    isValid = true
                };

                return Result<dynamic>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error validating coupon {Code}", code);
                return Result<dynamic>.Failure("Error validating coupon", ErrorType.InternalError);
            }
        }
    }
}