using System.Text.Json.Serialization;

namespace Casper.API.Models.Responses.OauthDto
{

    // ========== OAUTH DTO CLASSES ==========

    public class GoogleTokenResponse
    {
        [JsonPropertyName("access_token")]
        public string AccessToken { get; set; }

        [JsonPropertyName("refresh_token")]
        public string RefreshToken { get; set; }

        [JsonPropertyName("token_type")]
        public string TokenType { get; set; }

        [JsonPropertyName("expires_in")]
        public int ExpiresIn { get; set; }

        [JsonPropertyName("scope")]
        public string Scope { get; set; }

        [JsonPropertyName("id_token")]
        public string IdToken { get; set; }
    }

    public class GoogleUserInfo
    {
        public string Sub { get; set; } // Unique identifier
        public string Name { get; set; }
        public string GivenName { get; set; }
        public string FamilyName { get; set; }
        public string Picture { get; set; }
        public string Email { get; set; }
        public bool EmailVerified { get; set; }
        public string Locale { get; set; }
    }
}
