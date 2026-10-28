using System.Text.Json.Serialization;

namespace Creamy.WPF.Models
{
    public class Customer
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }

        [JsonPropertyName("name")]
        public string Name { get; set; } = string.Empty;

        [JsonPropertyName("email")]
        public string? Email { get; set; }

        [JsonPropertyName("phone")]
        public string Phone { get; set; } = string.Empty;

        [JsonPropertyName("address")]
        public string? Address { get; set; }

        [JsonPropertyName("notes")]
        public string? Notes { get; set; }

        // Modern UI Helpers
        public string Initials
        {
            get
            {
                if (string.IsNullOrWhiteSpace(Name)) return "C";
                var parts = Name.Split(' ', StringSplitOptions.RemoveEmptyEntries);
                if (parts.Length == 1) return parts[0][0].ToString().ToUpperInvariant();
                return $"{parts[0][0]}{parts[^1][0]}".ToUpperInvariant();
            }
        }

        public string DisplayEmail => string.IsNullOrWhiteSpace(Email) ? "—" : Email;
        public string DisplayAddress => string.IsNullOrWhiteSpace(Address) ? "No address specified" : Address;
        public string DisplayNotes => string.IsNullOrWhiteSpace(Notes) ? "None" : Notes;

        public string AvatarBg
        {
            get
            {
                // Consistent pastel avatar colors based on ID
                var colors = new[] { "#6366f1", "#ec4899", "#10b981", "#f59e0b", "#8b5cf6", "#06b6d4" };
                return colors[Math.Abs(Id) % colors.Length];
            }
        }
    }
}
