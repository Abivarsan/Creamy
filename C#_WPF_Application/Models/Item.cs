using System.Text.Json.Serialization;

namespace Creamy.WPF.Models
{
    public class Item
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }

        [JsonPropertyName("name")]
        public string Name { get; set; } = string.Empty;

        [JsonPropertyName("flavor_category")]
        public string FlavorCategory { get; set; } = string.Empty;

        [JsonPropertyName("price")]
        public decimal Price { get; set; }

        [JsonPropertyName("quantity")]
        public int Quantity { get; set; }

        [JsonPropertyName("unit")]
        public string Unit { get; set; } = "scoop";

        [JsonPropertyName("description")]
        public string? Description { get; set; }

        [JsonPropertyName("is_active")]
        public bool IsActive { get; set; } = true;

        public string PriceFormatted => $"${Price:F2}";
        public string StockStatus => Quantity > 0 ? $"{Quantity} {Unit}(s)" : "Out of Stock";
    }
}
