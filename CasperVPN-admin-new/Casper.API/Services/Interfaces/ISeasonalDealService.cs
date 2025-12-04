using Casper.API.Models.Common;
using Casper.API.Models.Requests.SeasonalDealRequest;

namespace Casper.API.Services.Interfaces
{
    public interface ISeasonalDealService
    {
        Task<Result<dynamic>> CreateSeasonalDealAsync(CreateSeasonalDealRequest request);
        Task<Result<dynamic>> UpdateSeasonalDealAsync(UpdateSeasonalDealRequest request);
        Task<Result<dynamic>> GetSeasonalDealsAsync(int pageNumber = 1, int pageSize = 10);
        Task<Result<dynamic>> DeleteSeasonalDealAsync(string id);
        Task<Result<dynamic>> GetActiveDealsAsync();
    }
}
