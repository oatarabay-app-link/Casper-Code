using Casper.API.Models.Casper.API.Models;
using Casper.API.Models.Common;
using Casper.API.Models.Requests;
using Casper.API.Models.Responses;
using static Casper.API.Models.Enums;
using static Microsoft.EntityFrameworkCore.DbLoggerCategory.Database;

namespace Casper.API.Services.Interfaces
{
    public interface IMobileConnectionService
    {
       


        Task<Result<object>> CreateConnectionAsync(CreateMobileConnectionRequest request); // Changed to return object






    }
}
