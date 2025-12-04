using Casper.API.Models.Common;
using Casper.API.Models.Requests.PackageRequest;

namespace Casper.API.Services.Interfaces
{
    public interface IPackageService
    {
        Task<Result<dynamic>> CreatePackageAsync(CreatePackageRequest request);
        Task<Result<dynamic>> UpdatePackageAsync(UpdatePackageRequest request);
        Task<Result<dynamic>> GetPackagesAsync(int pageNumber = 1, int pageSize = 10);
        Task<Result<dynamic>> DeletePackageAsync(string id);
    }
}
