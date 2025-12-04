using Casper.API.Models;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;

namespace Casper.API.Services.Interfaces
{
    public interface IVpnProtocolService
    {
        Task<Result<List<object>>> GetAllProtocolsAsync();
        Task<Result<object>> GetProtocolByIdAsync(string id);
        Task<Result<VpnProtocol>> CreateProtocolAsync(CreateVpnProtocolRequest request);
        Task<Result<VpnProtocol>> UpdateProtocolAsync(UpdateVpnProtocolRequest request);
        Task<Result> DeleteProtocolAsync(string id);
    }
}
