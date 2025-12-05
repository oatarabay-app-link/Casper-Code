namespace CasperVPN.Services;

/// <summary>
/// FreeRADIUS service interface for VPN authentication
/// </summary>
public interface IFreeRadiusService
{
    /// <summary>
    /// Create a new RADIUS user
    /// </summary>
    Task<bool> CreateUserAsync(string username, string password);

    /// <summary>
    /// Update user password
    /// </summary>
    Task<bool> UpdateUserPasswordAsync(string username, string newPassword);

    /// <summary>
    /// Delete a RADIUS user
    /// </summary>
    Task<bool> DeleteUserAsync(string username);

    /// <summary>
    /// Verify user credentials
    /// </summary>
    Task<bool> VerifyCredentialsAsync(string username, string password);

    /// <summary>
    /// Check if user exists
    /// </summary>
    Task<bool> UserExistsAsync(string username);

    /// <summary>
    /// Update user bandwidth limits
    /// </summary>
    Task<bool> UpdateBandwidthLimitAsync(string username, long maxBytesIn, long maxBytesOut);

    /// <summary>
    /// Get user session info
    /// </summary>
    Task<RadiusSessionInfo?> GetUserSessionAsync(string username);

    /// <summary>
    /// Disconnect user session
    /// </summary>
    Task<bool> DisconnectUserAsync(string username);

    /// <summary>
    /// Get user accounting data (bandwidth usage)
    /// </summary>
    Task<RadiusAccountingData?> GetAccountingDataAsync(string username);
}

/// <summary>
/// RADIUS session info
/// </summary>
public class RadiusSessionInfo
{
    public string Username { get; set; } = string.Empty;
    public string SessionId { get; set; } = string.Empty;
    public string NasIpAddress { get; set; } = string.Empty;
    public string FramedIpAddress { get; set; } = string.Empty;
    public DateTime SessionStartTime { get; set; }
    public long InputOctets { get; set; }
    public long OutputOctets { get; set; }
    public int SessionTime { get; set; }
}

/// <summary>
/// RADIUS accounting data
/// </summary>
public class RadiusAccountingData
{
    public string Username { get; set; } = string.Empty;
    public long TotalInputOctets { get; set; }
    public long TotalOutputOctets { get; set; }
    public int TotalSessionTime { get; set; }
    public int SessionCount { get; set; }
    public DateTime? LastSession { get; set; }
}
