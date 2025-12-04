using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace Casper.API.Controllers.v1.admin
{
    [ApiController]
    [ApiVersion("1.0")]

    [Route("api/v{version:apiVersion}/admin/[controller]")]
    [ApiExplorerSettings(GroupName = "v1")]
    //[Authorize(Policy = "Admin")]
    public abstract class BaseAdminController : BaseController
    {
        // Common admin functionality can go here
    }
}