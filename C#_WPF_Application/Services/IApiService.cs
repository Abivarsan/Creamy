using Creamy.WPF.Models;

namespace Creamy.WPF.Services
{
    public interface IApiService
    {
        string BaseUrl { get; set; }

        Task<(bool Success, string? ErrorMessage, User? User)> LoginAsync(string email, string password);
        Task<bool> LogoutAsync();

        Task<(bool Success, string? ErrorMessage, List<Item> Items)> GetItemsAsync(string? search = null, string? category = null);
        Task<(bool Success, string? ErrorMessage, Item? Item)> CreateItemAsync(Item item);
        Task<(bool Success, string? ErrorMessage, Item? Item)> UpdateItemAsync(int id, Item item);
        Task<(bool Success, string? ErrorMessage)> DeleteItemAsync(int id);

        Task<(bool Success, string? ErrorMessage, List<Customer> Customers)> GetCustomersAsync(string? search = null);
        Task<(bool Success, string? ErrorMessage, Customer? Customer)> CreateCustomerAsync(Customer customer);
        Task<(bool Success, string? ErrorMessage, Customer? Customer)> UpdateCustomerAsync(int id, Customer customer);
        Task<(bool Success, string? ErrorMessage)> DeleteCustomerAsync(int id);
    }
}
