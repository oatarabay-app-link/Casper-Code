using Casper.API.Models.Casper.API.Models;
using Casper.API.Models.Responses;
using Casper.API.Models;
using Casper.API.Models.Requests;

namespace Casper.API.Interfaces
{
    public interface IServerService
    {
        Task<ServiceResponse> CreateServerAsync(ServerRequest.CreateServer createServer);
        Task<ServiceResponse> UpdateServerAsync(ServerRequest.UpdateServer updateServer);
        Task<ServiceResponse> UpdateServerStatusAsync(ServerRequest.UpdateServerStatus statusUpdate);
        Task<ServiceResponse> GetServerSettingsAsync(string serverName);
        Task<ServiceResponse> GetServersAsync(int pageNumber = 1, int pageSize = 10);
        Task<ServiceResponse> GetServersAsync(); // For backward compatibility
        Task<ServiceResponse> DeleteServerAsync(Guid id);
    }
}
