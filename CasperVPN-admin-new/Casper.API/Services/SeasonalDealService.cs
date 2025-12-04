using Casper.API.Models;
using Casper.API.Models.CasperVpnDbContext;
using Casper.API.Models.Common;
using Casper.API.Models.Requests.SeasonalDealRequest;
using Casper.API.Models.Responses;
using Casper.API.Services.Interfaces;
using Microsoft.EntityFrameworkCore;
using static Casper.API.Models.Enums;

namespace Casper.API.Services
{
    public class SeasonalDealService : ISeasonalDealService
    {
        private readonly CasperVpnDbContext _context;
        private readonly ILogger<SeasonalDealService> _logger;

        public SeasonalDealService(CasperVpnDbContext context, ILogger<SeasonalDealService> logger)
        {
            _context = context;
            _logger = logger;
        }

        public async Task<Result<dynamic>> CreateSeasonalDealAsync(CreateSeasonalDealRequest request)
        {
            try
            {
                if (request == null)
                    return Result<dynamic>.Failure("Request body is required", ErrorType.Validation);

                // Check for duplicate deal name
                var existingDeal = await _context.SeasonalDeals
                    .FirstOrDefaultAsync(d => d.Name.ToLower() == request.Name.ToLower() && d.IsActive);

                if (existingDeal != null)
                    return Result<dynamic>.Failure($"Seasonal deal '{request.Name}' already exists", ErrorType.Conflict);

                // Create seasonal deal
                var deal = new SeasonalDeal
                {
                    Id = Guid.NewGuid(),
                    Name = request.Name.Trim(),
                    Description = request.Description.Trim(),
                    Type = request.Type,
                    Value = request.Value,
                    StartDate = request.StartDate,
                    EndDate = request.EndDate,
                    AppliesTo = request.AppliesTo.Trim().ToLower(),
                    Status = request.Status,
                    IsActive = true,
                    CreatedAt = DateTime.UtcNow,
                    UpdatedAt = DateTime.UtcNow
                };

                _context.SeasonalDeals.Add(deal);
                await _context.SaveChangesAsync();

                var response = new
                {
                    id = deal.Id,
                    name = deal.Name,
                    description = deal.Description,
                    type = deal.Type,
                    value = deal.Type == "Percent" ? $"{deal.Value}%" : $"${deal.Value} USD",
                    startDate = deal.StartDate.ToString("yyyy-MM-dd"),
                    endDate = deal.EndDate.ToString("yyyy-MM-dd"),
                    appliesTo = deal.AppliesTo,
                    status = deal.Status,
                    isActive = deal.IsActive,
                    createdAt = deal.CreatedAt
                };

                _logger.LogInformation("Seasonal deal created: {Name}", deal.Name);
                return Result<dynamic>.Success(response);
            }
            catch (DbUpdateException dbEx)
            {
                _logger.LogError(dbEx, "Database error creating seasonal deal");
                return Result<dynamic>.Failure("Database error creating seasonal deal", ErrorType.InternalError);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Unexpected error creating seasonal deal");
                return Result<dynamic>.Failure("Unexpected error creating seasonal deal", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> UpdateSeasonalDealAsync(UpdateSeasonalDealRequest request)
        {
            try
            {
                if (request == null)
                    return Result<dynamic>.Failure("Request body is required", ErrorType.Validation);

                if (!Guid.TryParse(request.Id, out Guid dealId))
                    return Result<dynamic>.Failure("Invalid seasonal deal ID", ErrorType.Validation);

                var deal = await _context.SeasonalDeals.FindAsync(dealId);
                if (deal == null || !deal.IsActive)
                    return Result<dynamic>.NotFound("Seasonal deal not found");

                // Check for duplicate name
                if (!string.IsNullOrWhiteSpace(request.Name) && request.Name != deal.Name)
                {
                    var existingDeal = await _context.SeasonalDeals
                        .FirstOrDefaultAsync(d => d.Name.ToLower() == request.Name.ToLower() &&
                                                d.Id != dealId && d.IsActive);
                    if (existingDeal != null)
                        return Result<dynamic>.Failure($"Seasonal deal '{request.Name}' already exists", ErrorType.Conflict);
                }

                // Update fields
                bool hasChanges = false;

                if (!string.IsNullOrWhiteSpace(request.Name) && request.Name != deal.Name)
                {
                    deal.Name = request.Name.Trim();
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Description) && request.Description != deal.Description)
                {
                    deal.Description = request.Description.Trim();
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Type) && request.Type != deal.Type)
                {
                    deal.Type = request.Type;
                    hasChanges = true;
                }

                if (request.Value.HasValue && request.Value != deal.Value)
                {
                    deal.Value = request.Value.Value;
                    hasChanges = true;
                }

                if (request.StartDate.HasValue && request.StartDate != deal.StartDate)
                {
                    deal.StartDate = request.StartDate.Value;
                    hasChanges = true;
                }

                if (request.EndDate.HasValue && request.EndDate != deal.EndDate)
                {
                    deal.EndDate = request.EndDate.Value;
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.AppliesTo) && request.AppliesTo != deal.AppliesTo)
                {
                    deal.AppliesTo = request.AppliesTo.Trim().ToLower();
                    hasChanges = true;
                }

                if (!string.IsNullOrWhiteSpace(request.Status) && request.Status != deal.Status)
                {
                    deal.Status = request.Status;
                    hasChanges = true;
                }

                if (!hasChanges)
                    return Result<dynamic>.Failure("No changes detected", ErrorType.Validation);

                deal.UpdatedAt = DateTime.UtcNow;
                await _context.SaveChangesAsync();

                var response = new
                {
                    id = deal.Id,
                    name = deal.Name,
                    description = deal.Description,
                    type = deal.Type,
                    value = deal.Type == "Percent" ? $"{deal.Value}%" : $"${deal.Value} USD",
                    startDate = deal.StartDate.ToString("yyyy-MM-dd"),
                    endDate = deal.EndDate.ToString("yyyy-MM-dd"),
                    appliesTo = deal.AppliesTo,
                    status = deal.Status,
                    isActive = deal.IsActive,
                    updatedAt = deal.UpdatedAt
                };

                return Result<dynamic>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error updating seasonal deal {DealId}", request?.Id);
                return Result<dynamic>.Failure("Error updating seasonal deal", ErrorType.InternalError);
            }
        }

        // Other methods remain the same...
        public async Task<Result<dynamic>> GetSeasonalDealsAsync(int pageNumber = 1, int pageSize = 10)
        {
            try
            {
                if (pageNumber < 1) pageNumber = 1;
                if (pageSize < 1 || pageSize > 100) pageSize = 10;

                var totalDeals = await _context.SeasonalDeals.CountAsync(d => d.IsActive);

                var deals = await _context.SeasonalDeals
                    .Where(d => d.IsActive)
                    .OrderByDescending(d => d.CreatedAt)
                    .Skip((pageNumber - 1) * pageSize)
                    .Take(pageSize)
                    .Select(d => new
                    {
                        id = d.Id,
                        name = d.Name,
                        description = d.Description,
                        type = d.Type,
                        value = d.Type == "Percent" ? $"{d.Value:F2}%" : $"${d.Value:F4} USD",
                        startDate = d.StartDate.ToString("yyyy-MM-dd"),
                        endDate = d.EndDate.ToString("yyyy-MM-dd"),
                        appliesTo = d.AppliesTo,
                        status = d.Status,
                        isActive = d.IsActive,
                        createdAt = d.CreatedAt
                    })
                    .ToListAsync();

                var summary = new
                {
                    TotalDeals = totalDeals,
                    ActiveDeals = await _context.SeasonalDeals.CountAsync(d => d.IsActive && d.Status == "Active"),
                    ScheduledDeals = await _context.SeasonalDeals.CountAsync(d => d.IsActive && d.Status == "Scheduled")
                };

                var pagination = new PaginationInfo
                {
                    PageNumber = pageNumber,
                    PageSize = pageSize,
                    TotalCount = totalDeals
                };

                return Result<dynamic>.Success(new
                {
                    SeasonalDeals = deals,
                    Summary = summary,
                    Pagination = pagination
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving seasonal deals");
                return Result<dynamic>.Failure("Error retrieving seasonal deals", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> DeleteSeasonalDealAsync(string id)
        {
            try
            {
                if (!Guid.TryParse(id, out Guid dealId))
                    return Result<dynamic>.Failure("Invalid seasonal deal ID", ErrorType.Validation);

                var deal = await _context.SeasonalDeals.FindAsync(dealId);
                if (deal == null)
                    return Result<dynamic>.NotFound("Seasonal deal not found");

                if (!deal.IsActive)
                    return Result<dynamic>.Failure("Seasonal deal is already deactivated", ErrorType.Conflict);

                deal.IsActive = false;
                deal.UpdatedAt = DateTime.UtcNow;

                await _context.SaveChangesAsync();

                var response = new
                {
                    id = deal.Id,
                    name = deal.Name,
                    deactivatedAt = deal.UpdatedAt
                };

                return Result<dynamic>.Success(response);
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error deactivating seasonal deal {DealId}", id);
                return Result<dynamic>.Failure("Error deactivating seasonal deal", ErrorType.InternalError);
            }
        }

        public async Task<Result<dynamic>> GetActiveDealsAsync()
        {
            try
            {
                var now = DateTime.UtcNow;

                var activeDeals = await _context.SeasonalDeals
                    .Where(d => d.IsActive &&
                               d.Status == "Active")
                    .OrderBy(d => d.StartDate)
                    .Select(d => new
                    {
                        id = d.Id,
                        name = d.Name,
                        description = d.Description,
                        type = d.Type,
                        value = d.Type == "Percent" ? $"{d.Value:F2}%" : $"${d.Value:F4} USD",
                        startDate = d.StartDate,
                        endDate = d.EndDate,
                        appliesTo = d.AppliesTo
                    })
                    .ToListAsync();

                return Result<dynamic>.Success(new
                {
                    ActiveDeals = activeDeals,
                    Count = activeDeals.Count
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving active seasonal deals");
                return Result<dynamic>.Failure("Error retrieving active seasonal deals", ErrorType.InternalError);
            }
        }
    }
}