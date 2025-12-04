using Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Common;
using Casper.API.Models.Requests.DiscountRequest;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.EntityFrameworkCore;
using static Casper.API.Models.Enums;

namespace Casper.API.Services
{
    public class DiscountService : IDiscountService
    {
        private readonly CasperVpnDbContext _context;
        private readonly ILogger<DiscountService> _logger;

        public DiscountService(CasperVpnDbContext context, ILogger<DiscountService> logger)
        {
            _context = context;
            _logger = logger;
        }

        public async Task<Result<dynamic>> CreateDiscountAsync(CreateDiscountRequest request)
        {
            try
            {
                if (request == null)
                    return Result<dynamic>.Failure("Request body is required", ErrorType.Validation);

                // Check for duplicate discount name for the same plan
                var existingDiscount = await _context.PlanDiscounts
                    .FirstOrDefaultAsync(d => d.Name.ToLower() == request.Name.ToLower() &&
                                            d.Plan.ToLower() == request.Plan.ToLower() && d.IsActive);

                if (existingDiscount != null)
                    return Result<dynamic>.Failure($"Discount '{request.Name}' already exists for plan '{request.Plan}'", ErrorType.Conflict);

                // Create discount
                var discount = new PlanDiscount
                {
                    Id = Guid.NewGuid(),
                    Name = request.Name.Trim(),
                    Plan = request.Plan.Trim().ToUpper(),
                    Type = request.Type,
                    Value = request.Value,
                    Status = request.Status,
                    IsActive = true,
                    CreatedAt = DateTime.UtcNow,
                    UpdatedAt = DateTime.UtcNow
                };

                _context.PlanDiscounts.Add(discount);
                await _context.SaveChangesAsync();

                var response = new
                {
                    id = discount.Id,
                    name = discount.Name,
                    plan = discount.Plan,
                    type = discount.Type,
                    value = discount.Type == "Percent" ? $"{discount.Value}%" : $"${discount.Value} USD",
                    status = discount.Status,
                    isActive = discount.IsActive,
                    createdAt = discount.CreatedAt
                };

                _logger.LogInformation("Discount created: {Name} for plan {Plan}", discount.Name, discount.Plan);
                return Result<dynamic>.Success(response);
            }
            catch (DbUpdateException dbEx)
            {
                _logger.LogError(dbEx, "Database error creating discount");
                return Result<dynamic>.Failure("Database error creating discount", ErrorType.InternalError);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Unexpected error creating discount");
                return Result<dynamic>.Failure("Unexpected error creating discount", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> UpdateDiscountAsync(UpdateDiscountRequest request)
        {
            try
            {
                if (request == null)
                    return Result<dynamic>.Failure("Request body is required", ErrorType.Validation);

                if (!Guid.TryParse(request.Id, out Guid discountId))
                    return Result<dynamic>.Failure("Invalid discount ID", ErrorType.Validation);

                var discount = await _context.PlanDiscounts.FindAsync(discountId);
                if (discount == null || !discount.IsActive)
                    return Result<dynamic>.NotFound("Discount not found");

                // Check for duplicate name
                if ((!string.IsNullOrWhiteSpace(request.Name) && request.Name != discount.Name) ||
                    (!string.IsNullOrWhiteSpace(request.Plan) && request.Plan != discount.Plan))
                {
                    var newName = !string.IsNullOrWhiteSpace(request.Name) ? request.Name : discount.Name;
                    var newPlan = !string.IsNullOrWhiteSpace(request.Plan) ? request.Plan : discount.Plan;

                    var existingDiscount = await _context.PlanDiscounts
                        .FirstOrDefaultAsync(d => d.Name.ToLower() == newName.ToLower() &&
                                                d.Plan.ToLower() == newPlan.ToLower() &&
                                                d.Id != discountId);

                    if (existingDiscount != null)
                        return Result<dynamic>.Failure($"Discount '{newName}' already exists for plan '{newPlan}'", ErrorType.Conflict);
                }

                // Update fields
                bool hasChanges = false;

                if (!string.IsNullOrWhiteSpace(request.Name) && request.Name != discount.Name)
                {
                    discount.Name = request.Name.Trim();
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Plan) && request.Plan != discount.Plan)
                {
                    discount.Plan = request.Plan.Trim().ToUpper();
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Type) && request.Type != discount.Type)
                {
                    discount.Type = request.Type;
                    hasChanges = true;
                }

                if (request.Value.HasValue && request.Value != discount.Value)
                {
                    discount.Value = request.Value.Value;
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Status) && request.Status != discount.Status)
                {
                    discount.Status = request.Status;
                    hasChanges = true;
                }

                if (!hasChanges)
                    return Result<dynamic>.Failure("No changes detected", ErrorType.Validation);

                discount.UpdatedAt = DateTime.UtcNow;
                await _context.SaveChangesAsync();

                var response = new
                {
                    id = discount.Id,
                    name = discount.Name,
                    plan = discount.Plan,
                    type = discount.Type,
                    value = discount.Type == "Percent" ? $"{discount.Value}%" : $"${discount.Value} USD",
                    status = discount.Status,
                    isActive = discount.IsActive,
                    updatedAt = discount.UpdatedAt
                };

                return Result<dynamic>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating discount {DiscountId}", request?.Id);
                return Result<dynamic>.Failure("Error updating discount", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> GetDiscountsAsync(int pageNumber = 1, int pageSize = 10)
        {
            try
            {
                if (pageNumber < 1) pageNumber = 1;
                if (pageSize < 1 || pageSize > 100) pageSize = 10;

                var totalDiscounts = await _context.PlanDiscounts.CountAsync(d => d.IsActive);

                var discounts = await _context.PlanDiscounts
                    .Where(d => d.IsActive)
                    .OrderByDescending(d => d.CreatedAt)
                    .Skip((pageNumber - 1) * pageSize)
                    .Take(pageSize)
                    .Select(d => new
                    {
                        id = d.Id,
                        name = d.Name,
                        plan = d.Plan,
                        type = d.Type,
                        value = d.Type == "Percent" ? $"{d.Value:F2}%" : $"${d.Value:F4} USD",
                        status = d.Status,
                        isActive = d.IsActive,
                        createdAt = d.CreatedAt
                    })
                    .ToListAsync();

                var summary = new
                {
                    TotalDiscounts = totalDiscounts,
                    ActiveDiscounts = await _context.PlanDiscounts.CountAsync(d => d.IsActive && d.Status == "Active"),
                    ScheduledDiscounts = await _context.PlanDiscounts.CountAsync(d => d.IsActive && d.Status == "Scheduled")
                };

                var pagination = new PaginationInfo
                {
                    PageNumber = pageNumber,
                    PageSize = pageSize,
                    TotalCount = totalDiscounts
                };

                return Result<dynamic>.Success(new
                {
                    Discounts = discounts,
                    Summary = summary,
                    Pagination = pagination
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving discounts");
                return Result<dynamic>.Failure("Error retrieving discounts", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> DeleteDiscountAsync(string id)
        {
            try
            {
                if (!Guid.TryParse(id, out Guid discountId))
                    return Result<dynamic>.Failure("Invalid discount ID", ErrorType.Validation);

                var discount = await _context.PlanDiscounts.FindAsync(discountId);
                if (discount == null)
                    return Result<dynamic>.NotFound("Discount not found");

                if (!discount.IsActive)
                    return Result<dynamic>.Failure("Discount is already deactivated", ErrorType.Conflict);

                discount.IsActive = false;
                discount.UpdatedAt = DateTime.UtcNow;

                await _context.SaveChangesAsync();

                var response = new
                {
                    id = discount.Id,
                    name = discount.Name,
                    deactivatedAt = discount.UpdatedAt
                };

                return Result<dynamic>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error deactivating discount {DiscountId}", id);
                return Result<dynamic>.Failure("Error deactivating discount", ErrorType.InternalError);
            }
        }
    }
}