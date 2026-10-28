using System.Text.Json.Serialization;

namespace Creamy.WPF.Models
{
    public class User
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }

        [JsonPropertyName("name")]
        public string Name { get; set; } = string.Empty;

        [JsonPropertyName("email")]
        public string Email { get; set; } = string.Empty;

        [JsonPropertyName("role")]
        public string Role { get; set; } = string.Empty;

        public bool IsOwner => string.Equals(Role, "owner", StringComparison.OrdinalIgnoreCase);
        public bool IsManager => string.Equals(Role, "manager", StringComparison.OrdinalIgnoreCase);
        public bool IsCashier => string.Equals(Role, "cashier", StringComparison.OrdinalIgnoreCase);

        public string RoleDisplayName => Role?.ToLowerInvariant() switch
        {
            "owner" => "Store Owner",
            "manager" => "Store Manager",
            "cashier" => "Cashier",
            _ => Role ?? "Staff"
        };

        public string RoleEmoji => Role?.ToLowerInvariant() switch
        {
            "owner" => "👑",
            "manager" => "👔",
            "cashier" => "🛒",
            _ => "👤"
        };

        public string Initials
        {
            get
            {
                if (string.IsNullOrWhiteSpace(Name)) return "U";
                var parts = Name.Split(' ', StringSplitOptions.RemoveEmptyEntries);
                if (parts.Length == 1) return parts[0][0].ToString().ToUpperInvariant();
                return $"{parts[0][0]}{parts[^1][0]}".ToUpperInvariant();
            }
        }
    }
}
