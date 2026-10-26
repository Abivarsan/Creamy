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

        public string RoleDisplayName => Role switch
        {
            "owner" => "Store Owner",
            "manager" => "Store Manager",
            "cashier" => "Cashier",
            _ => Role
        };
    }
}
