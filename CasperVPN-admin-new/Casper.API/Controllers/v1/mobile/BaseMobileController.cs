using Microsoft.AspNetCore.Mvc;

// For more information on enabling Web API for empty projects, visit https://go.microsoft.com/fwlink/?LinkID=397860

namespace Casper.API.Controllers.v1.mobile
{
    [ApiController]
    [ApiVersion("1.0")]

    [Route("api/v{version:apiVersion}/mobile/[controller]")]
    [ApiExplorerSettings(GroupName = "v1")]
    //[Authorize(Policy = "Admin")]
    public class BaseMobileController : BaseController
    {
       
    }
}
