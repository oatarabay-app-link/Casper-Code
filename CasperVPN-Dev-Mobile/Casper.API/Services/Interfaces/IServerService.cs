

using Casper.API.Models.Casper.API.Models;
using Casper.API.Models.Responses;
using Casper.API.Models;
using Casper.API.Models.Requests;
using Casper.API.Models.Common;

namespace Casper.API.Services.Interfaces
{
    public interface IServerService
    {
        Task<Result<dynamic>> CreateServerAsync(CreateServerRequest createServer);
        Task<Result<dynamic>> UpdateServerAsync(UpdateServerRequest request);
        Task<Result<dynamic>> GetServersAsync(int pageNumber = 1, int pageSize = 10);
        Task<Result<dynamic>> GetServersAsync(); // For backward compatibility
        Task<Result<dynamic>> DeleteServerAsync(string id);
    }
}