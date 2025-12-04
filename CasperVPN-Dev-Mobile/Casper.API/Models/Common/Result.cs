namespace Casper.API.Models.Common
{
    public class Result<T>
    {
        public bool IsSuccess { get; }
        public T? Data { get; }
        public string? ErrorMessage { get; }
        public List<string> Errors { get; }
        public ErrorType ErrorType { get; }

        private Result(bool isSuccess, T? data, string? errorMessage, List<string>? errors, ErrorType errorType)
        {
            IsSuccess = isSuccess;
            Data = data;
            ErrorMessage = errorMessage;
            Errors = errors ?? new List<string>();
            ErrorType = errorType;
        }

        public static Result<T> Success(T data) => new(true, data, null, null, ErrorType.None);

        public static Result<T> Failure(string errorMessage, ErrorType errorType = ErrorType.BadRequest)
            => new(false, default, errorMessage, null, errorType);

        public static Result<T> Failure(List<string> errors, ErrorType errorType = ErrorType.BadRequest)
            => new(false, default, null, errors, errorType);

        public static Result<T> NotFound(string message = "Resource not found")
            => new(false, default, message, null, ErrorType.NotFound);

        public static Result<T> Unauthorized(string message = "Unauthorized access")
            => new(false, default, message, null, ErrorType.Unauthorized);

        public static Result<T> Conflict(string message = "Resource conflict")
            => new(false, default, message, null, ErrorType.Conflict);

        public static Result<T> ValidationFailure(List<string> errors)
            => new(false, default, "Validation failed", errors, ErrorType.Validation);
    }

    public class Result
    {
        public bool IsSuccess { get; }
        public string? ErrorMessage { get; }
        public List<string> Errors { get; }
        public ErrorType ErrorType { get; }

        public Result(bool isSuccess, string? errorMessage, List<string>? errors, ErrorType errorType)
        {
            IsSuccess = isSuccess;
            ErrorMessage = errorMessage;
            Errors = errors ?? new List<string>();
            ErrorType = errorType;
        }

        public static Result Success() => new(true, null, null, ErrorType.None);

        public static Result Failure(string errorMessage, ErrorType errorType = ErrorType.BadRequest)
            => new(false, errorMessage, null, errorType);

        public static Result Failure(List<string> errors, ErrorType errorType = ErrorType.BadRequest)
            => new(false, null, errors, errorType);

        public static Result NotFound(string message = "Resource not found")
            => new(false, message, null, ErrorType.NotFound);

        public static Result Unauthorized(string message = "Unauthorized access")
            => new(false, message, null, ErrorType.Unauthorized);

        public static Result Conflict(string message = "Resource conflict")
            => new(false, message, null, ErrorType.Conflict);

        public static Result ValidationFailure(List<string> errors)
            => new(false, "Validation failed", errors, ErrorType.Validation);
    }

    public enum ErrorType
    {
        None,
        BadRequest,
        Unauthorized,
        NotFound,
        Conflict,
        Validation,
        InternalError
    }
}