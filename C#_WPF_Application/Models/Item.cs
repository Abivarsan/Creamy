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

        // Modern UI Helpers
        public string StockStatusBadge => Quantity > 10 ? "In Stock" : (Quantity > 0 ? "Low Stock" : "Out of Stock");
        public string StockBadgeBg => Quantity > 10 ? "#ecfdf5" : (Quantity > 0 ? "#fffbeb" : "#fef2f2");
        public string StockBadgeFg => Quantity > 10 ? "#059669" : (Quantity > 0 ? "#b45309" : "#dc2626");
        public string StockBadgeDot => Quantity > 10 ? "#10b981" : (Quantity > 0 ? "#f59e0b" : "#ef4444");

        public string CategoryBadgeBg => FlavorCategory?.ToLowerInvariant() switch
        {
            "chocolate" => "#fdf4ff",
            "vanilla" => "#fffbeb",
            "strawberry" => "#fdf2f8",
            "mint" => "#ecfdf5",
            "fruit" => "#fff7ed",
            _ => "#eef2ff"
        };

        public string CategoryBadgeFg => FlavorCategory?.ToLowerInvariant() switch
        {
            "chocolate" => "#86198f",
            "vanilla" => "#b45309",
            "strawberry" => "#be185d",
            "mint" => "#047857",
            "fruit" => "#c2410c",
            _ => "#4338ca"
        };
    }
}
