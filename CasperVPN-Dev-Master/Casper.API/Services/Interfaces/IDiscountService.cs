using Casper.API.Models.Common;
using Casper.API.Models.Requests.DiscountRequest;

namespace Casper.API.Services.Interfaces
{
    public interface IDiscountService
    {
        Task<Result<dynamic>> CreateDiscountAsync(CreateDiscountRequest request);
        Task<Result<dynamic>> UpdateDiscountAsync(UpdateDiscountRequest request);
        Task<Result<dynamic>> GetDiscountsAsync(int pageNumber = 1, int pageSize = 10);
        Task<Result<dynamic>> DeleteDiscountAsync(string id);
    }
}
