using Casper.API.Models.Common;
using Casper.API.Models.Requests.CouponRequest;

namespace Casper.API.Services.Interfaces
{
    public interface ICouponService
    {
        Task<Result<dynamic>> CreateCouponAsync(CreateCouponRequest request);
        Task<Result<dynamic>> UpdateCouponAsync(UpdateCouponRequest request);
        Task<Result<dynamic>> GetCouponsAsync(int pageNumber = 1, int pageSize = 10);
        Task<Result<dynamic>> DeleteCouponAsync(string id);
        Task<Result<dynamic>> ValidateCouponAsync(string code);
    }
}
