using Casper.API.Models.Casper.API.Models;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using static Casper.API.Models.Enums;

namespace Casper.API.Services.Interfaces
{
    public interface IConnectionService
    {
        Task<Result<dynamic>> GetConnectionsWithSummary();
        Task<Result<dynamic>> GetConnectionsByUserIdAsync(string userId);
        Task<Result<dynamic>> GetConnectionsByServerIdAsync(string serverId);
        Task<Result<Connection>> CreateConnectionAsync(CreateConnectionRequest request);
        Task<Result<Connection>> UpdateConnectionAsync(UpdateConnectionRequest request);
        Task<Result> DeleteConnectionAsync(string id);
        Task<Result> UpdateConnectionStatusAsync(string id, ConnectionStatus status);
        Task<Result> UpdateConnectionStatsAsync(string id, double upload, double download);
    }
}
