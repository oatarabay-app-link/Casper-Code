namespace Casper.API.Models.Responses
{
    public class ServiceResponse
    {
        public int Status { get; set; }
        public string Message { get; set; }
        public object Data { get; set; }
        public PaginationInfo Pagination { get; set; }

        // Helper method for success responses with pagination
        public static ServiceResponse Success(object data, string message = "Success", PaginationInfo pagination = null)
        {
            return new ServiceResponse
            {
                Status = 200,
                Message = message,
                Data = data,
                Pagination = pagination
            };
        }

        // Helper method for error responses
        public static ServiceResponse Error(string message, int status = 500, object data = null)
        {
            return new ServiceResponse
            {
                Status = status,
                Message = message,
                Data = data,
                Pagination = null
            };
        }

        // Helper method for created responses (201)
        public static ServiceResponse Created(object data, string message = "Resource created successfully")
        {
            return new ServiceResponse
            {
                Status = 201,
                Message = message,
                Data = data,
                Pagination = null
            };
        }

        // Helper method for bad request responses (400)
        public static ServiceResponse BadRequest(string message = "Bad request", object data = null)
        {
            return new ServiceResponse
            {
                Status = 400,
                Message = message,
                Data = data,
                Pagination = null
            };
        }

        // Helper method for not found responses (404)
        public static ServiceResponse NotFound(string message = "Resource not found", object data = null)
        {
            return new ServiceResponse
            {
                Status = 404,
                Message = message,
                Data = data,
                Pagination = null
            };
        }

        // Helper method for conflict responses (409)
        public static ServiceResponse Conflict(string message = "Conflict", object data = null)
        {
            return new ServiceResponse
            {
                Status = 409,
                Message = message,
                Data = data,
                Pagination = null
            };
        }
    }

    public class PaginationInfo
    {
        public int PageNumber { get; set; }
        public int PageSize { get; set; }
        public int TotalCount { get; set; }
        public int TotalPages => PageSize > 0 ? (int)Math.Ceiling(TotalCount / (double)PageSize) : 0;
        public bool HasPrevious => PageNumber > 1;
        public bool HasNext => PageNumber < TotalPages;
    }
}