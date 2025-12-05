using System.Net.Http.Headers;
using System.Text;
using System.Text.Json;
using Microsoft.Extensions.Options;
using CasperVPN.Helpers;

namespace CasperVPN.Services;

/// <summary>
/// FreeRADIUS service implementation
/// Connects to FreeRADIUS via REST API (requires rest module) or direct DB access
/// </summary>
public class FreeRadiusService : IFreeRadiusService
{
    private readonly HttpClient _httpClient;
    private readonly RadiusSettings _settings;
    private readonly ILogger<FreeRadiusService> _logger;

    public FreeRadiusService(
        HttpClient httpClient,
        IOptions<RadiusSettings> settings,
        ILogger<FreeRadiusService> logger)
    {
        _httpClient = httpClient;
        _settings = settings.Value;
        _logger = logger;

        // Configure HTTP client
        _httpClient.BaseAddress = new Uri(_settings.ApiEndpoint);
        _httpClient.Timeout = TimeSpan.FromSeconds(30);

        if (!string.IsNullOrEmpty(_settings.ApiKey))
        {
            _httpClient.DefaultRequestHeaders.Authorization = 
                new AuthenticationHeaderValue("Bearer", _settings.ApiKey);
        }
    }

    public async Task<bool> CreateUserAsync(string username, string password)
    {
        try
        {
            _logger.LogInformation("Creating RADIUS user: {Username}", username);

            var payload = new
            {
                username = username,
                password = password,
                attribute = "Cleartext-Password",
                op = ":=",
                value = password
            };

            var content = new StringContent(
                JsonSerializer.Serialize(payload),
                Encoding.UTF8,
                "application/json"
            );

            var response = await _httpClient.PostAsync("/user", content);

            if (response.IsSuccessStatusCode)
            {
                _logger.LogInformation("RADIUS user created: {Username}", username);
                return true;
            }

            _logger.LogWarning("Failed to create RADIUS user: {Username}, Status: {Status}",
                username, response.StatusCode);
            return false;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error creating RADIUS user: {Username}", username);
            // Don't throw - return false to allow graceful handling
            return false;
        }
    }

    public async Task<bool> UpdateUserPasswordAsync(string username, string newPassword)
    {
        try
        {
            _logger.LogInformation("Updating RADIUS password for: {Username}", username);

            var payload = new
            {
                username = username,
                password = newPassword,
                attribute = "Cleartext-Password",
                op = ":=",
                value = newPassword
            };

            var content = new StringContent(
                JsonSerializer.Serialize(payload),
                Encoding.UTF8,
                "application/json"
            );

            var response = await _httpClient.PutAsync($"/user/{username}", content);

            if (response.IsSuccessStatusCode)
            {
                _logger.LogInformation("RADIUS password updated: {Username}", username);
                return true;
            }

            _logger.LogWarning("Failed to update RADIUS password: {Username}, Status: {Status}",
                username, response.StatusCode);
            return false;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error updating RADIUS password: {Username}", username);
            return false;
        }
    }

    public async Task<bool> DeleteUserAsync(string username)
    {
        try
        {
            _logger.LogInformation("Deleting RADIUS user: {Username}", username);

            var response = await _httpClient.DeleteAsync($"/user/{username}");

            if (response.IsSuccessStatusCode)
            {
                _logger.LogInformation("RADIUS user deleted: {Username}", username);
                return true;
            }

            _logger.LogWarning("Failed to delete RADIUS user: {Username}, Status: {Status}",
                username, response.StatusCode);
            return false;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error deleting RADIUS user: {Username}", username);
            return false;
        }
    }

    public async Task<bool> VerifyCredentialsAsync(string username, string password)
    {
        try
        {
            var payload = new
            {
                username = username,
                password = password
            };

            var content = new StringContent(
                JsonSerializer.Serialize(payload),
                Encoding.UTF8,
                "application/json"
            );

            var response = await _httpClient.PostAsync("/authenticate", content);
            return response.IsSuccessStatusCode;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error verifying RADIUS credentials: {Username}", username);
            return false;
        }
    }

    public async Task<bool> UserExistsAsync(string username)
    {
        try
        {
            var response = await _httpClient.GetAsync($"/user/{username}");
            return response.IsSuccessStatusCode;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error checking RADIUS user existence: {Username}", username);
            return false;
        }
    }

    public async Task<bool> UpdateBandwidthLimitAsync(string username, long maxBytesIn, long maxBytesOut)
    {
        try
        {
            _logger.LogInformation("Updating RADIUS bandwidth limits for: {Username}", username);

            // Set bandwidth limits using RADIUS attributes
            var payload = new
            {
                username = username,
                attributes = new[]
                {
                    new { attribute = "Max-Daily-Session", op = ":=", value = "86400" },
                    new { attribute = "WISPr-Bandwidth-Max-Down", op = ":=", value = maxBytesIn.ToString() },
                    new { attribute = "WISPr-Bandwidth-Max-Up", op = ":=", value = maxBytesOut.ToString() }
                }
            };

            var content = new StringContent(
                JsonSerializer.Serialize(payload),
                Encoding.UTF8,
                "application/json"
            );

            var response = await _httpClient.PutAsync($"/user/{username}/attributes", content);
            return response.IsSuccessStatusCode;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error updating RADIUS bandwidth: {Username}", username);
            return false;
        }
    }

    public async Task<RadiusSessionInfo?> GetUserSessionAsync(string username)
    {
        try
        {
            var response = await _httpClient.GetAsync($"/session/{username}");
            
            if (!response.IsSuccessStatusCode)
            {
                return null;
            }

            var json = await response.Content.ReadAsStringAsync();
            var options = new JsonSerializerOptions { PropertyNameCaseInsensitive = true };
            return JsonSerializer.Deserialize<RadiusSessionInfo>(json, options);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting RADIUS session: {Username}", username);
            return null;
        }
    }

    public async Task<bool> DisconnectUserAsync(string username)
    {
        try
        {
            _logger.LogInformation("Disconnecting RADIUS user: {Username}", username);

            var response = await _httpClient.PostAsync($"/session/{username}/disconnect", null);
            
            if (response.IsSuccessStatusCode)
            {
                _logger.LogInformation("RADIUS user disconnected: {Username}", username);
                return true;
            }

            return false;
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error disconnecting RADIUS user: {Username}", username);
            return false;
        }
    }

    public async Task<RadiusAccountingData?> GetAccountingDataAsync(string username)
    {
        try
        {
            var response = await _httpClient.GetAsync($"/accounting/{username}");
            
            if (!response.IsSuccessStatusCode)
            {
                return null;
            }

            var json = await response.Content.ReadAsStringAsync();
            var options = new JsonSerializerOptions { PropertyNameCaseInsensitive = true };
            return JsonSerializer.Deserialize<RadiusAccountingData>(json, options);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Error getting RADIUS accounting data: {Username}", username);
            return null;
        }
    }
}

/// <summary>
/// Mock FreeRADIUS service for development/testing
/// </summary>
public class MockFreeRadiusService : IFreeRadiusService
{
    private readonly Dictionary<string, string> _users = new();
    private readonly ILogger<MockFreeRadiusService> _logger;

    public MockFreeRadiusService(ILogger<MockFreeRadiusService> logger)
    {
        _logger = logger;
    }

    public Task<bool> CreateUserAsync(string username, string password)
    {
        _logger.LogInformation("[MOCK] Creating RADIUS user: {Username}", username);
        _users[username] = password;
        return Task.FromResult(true);
    }

    public Task<bool> UpdateUserPasswordAsync(string username, string newPassword)
    {
        _logger.LogInformation("[MOCK] Updating RADIUS password: {Username}", username);
        if (_users.ContainsKey(username))
        {
            _users[username] = newPassword;
            return Task.FromResult(true);
        }
        return Task.FromResult(false);
    }

    public Task<bool> DeleteUserAsync(string username)
    {
        _logger.LogInformation("[MOCK] Deleting RADIUS user: {Username}", username);
        return Task.FromResult(_users.Remove(username));
    }

    public Task<bool> VerifyCredentialsAsync(string username, string password)
    {
        return Task.FromResult(_users.TryGetValue(username, out var storedPassword) && storedPassword == password);
    }

    public Task<bool> UserExistsAsync(string username)
    {
        return Task.FromResult(_users.ContainsKey(username));
    }

    public Task<bool> UpdateBandwidthLimitAsync(string username, long maxBytesIn, long maxBytesOut)
    {
        _logger.LogInformation("[MOCK] Updating bandwidth for {Username}: In={BytesIn}, Out={BytesOut}", 
            username, maxBytesIn, maxBytesOut);
        return Task.FromResult(true);
    }

    public Task<RadiusSessionInfo?> GetUserSessionAsync(string username)
    {
        if (!_users.ContainsKey(username))
            return Task.FromResult<RadiusSessionInfo?>(null);

        return Task.FromResult<RadiusSessionInfo?>(new RadiusSessionInfo
        {
            Username = username,
            SessionId = Guid.NewGuid().ToString(),
            NasIpAddress = "10.0.0.1",
            FramedIpAddress = "10.66.66.1",
            SessionStartTime = DateTime.UtcNow.AddMinutes(-30),
            InputOctets = 1024 * 1024 * 50, // 50 MB
            OutputOctets = 1024 * 1024 * 200, // 200 MB
            SessionTime = 1800
        });
    }

    public Task<bool> DisconnectUserAsync(string username)
    {
        _logger.LogInformation("[MOCK] Disconnecting RADIUS user: {Username}", username);
        return Task.FromResult(true);
    }

    public Task<RadiusAccountingData?> GetAccountingDataAsync(string username)
    {
        if (!_users.ContainsKey(username))
            return Task.FromResult<RadiusAccountingData?>(null);

        return Task.FromResult<RadiusAccountingData?>(new RadiusAccountingData
        {
            Username = username,
            TotalInputOctets = 1024 * 1024 * 500, // 500 MB
            TotalOutputOctets = 1024 * 1024 * 2000, // 2 GB
            TotalSessionTime = 86400,
            SessionCount = 10,
            LastSession = DateTime.UtcNow.AddHours(-2)
        });
    }
}
