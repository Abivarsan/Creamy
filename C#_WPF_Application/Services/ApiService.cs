using System.Net.Http;
using System.Net.Http.Headers;
using System.Text;
using System.Text.Json;
using Creamy.WPF.Models;

namespace Creamy.WPF.Services
{
    public class ApiService : IApiService
    {
        private static ApiService? _instance;
        public static ApiService Instance => _instance ??= new ApiService();

        private readonly HttpClient _httpClient;
        public string BaseUrl { get; set; } = "http://127.0.0.1:8000/api";

        public ApiService()
        {
            _httpClient = new HttpClient();
            _httpClient.DefaultRequestHeaders.Accept.Clear();
            _httpClient.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));
        }

        private void SetAuthHeader()
        {
            if (!string.IsNullOrEmpty(AuthSession.Instance.Token))
            {
                _httpClient.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", AuthSession.Instance.Token);
            }
            else
            {
                _httpClient.DefaultRequestHeaders.Authorization = null;
            }
        }

        public async Task<(bool Success, string? ErrorMessage, User? User)> LoginAsync(string email, string password)
        {
            try
            {
                var payload = new { email, password };
                var json = JsonSerializer.Serialize(payload);
                var content = new StringContent(json, Encoding.UTF8, "application/json");

                var response = await _httpClient.PostAsync($"{BaseUrl}/auth/login", content);
                var responseBody = await response.Content.ReadAsStringAsync();

                if (!response.IsSuccessStatusCode)
                {
                    if (response.StatusCode == System.Net.HttpStatusCode.Unauthorized)
                    {
                        return (false, "Invalid email or password.", null);
                    }
                    if (response.StatusCode == System.Net.HttpStatusCode.UnprocessableEntity)
                    {
                        return (false, "Please provide a valid email and password.", null);
                    }
                    return (false, $"Login failed: {response.ReasonPhrase}", null);
                }

                var result = JsonSerializer.Deserialize<ApiResponse<LoginData>>(responseBody);
                if (result?.Data?.Token != null && result.Data.User != null)
                {
                    AuthSession.Instance.SetSession(result.Data.Token, result.Data.User);
                    return (true, null, result.Data.User);
                }

                return (false, "Unexpected response from authentication server.", null);
            }
            catch (Exception ex)
            {
                return (false, $"Connection error: {ex.Message}. Make sure Laravel is running (php artisan serve).", null);
            }
        }

        public async Task<bool> LogoutAsync()
        {
            try
            {
                SetAuthHeader();
                await _httpClient.PostAsync($"{BaseUrl}/auth/logout", null);
            }
            catch
            {
                // Ignore logout network failure
            }
            finally
            {
                AuthSession.Instance.ClearSession();
            }

            return true;
        }

        public async Task<(bool Success, string? ErrorMessage, List<Item> Items)> GetItemsAsync(string? search = null, string? category = null)
        {
            try
            {
                SetAuthHeader();
                var queryParams = new List<string>();
                if (!string.IsNullOrWhiteSpace(search)) queryParams.Add($"search={Uri.EscapeDataString(search)}");
                if (!string.IsNullOrWhiteSpace(category) && category != "All") queryParams.Add($"category={Uri.EscapeDataString(category)}");

                var url = $"{BaseUrl}/items" + (queryParams.Count > 0 ? "?" + string.Join("&", queryParams) : "");
                var response = await _httpClient.GetAsync(url);
                var responseBody = await response.Content.ReadAsStringAsync();

                if (response.IsSuccessStatusCode)
                {
                    var result = JsonSerializer.Deserialize<PaginatedResponse<Item>>(responseBody);
                    return (true, null, result?.Data ?? new List<Item>());
                }

                if (response.StatusCode == System.Net.HttpStatusCode.Unauthorized)
                {
                    return (false, "Session expired. Please log in again.", new List<Item>());
                }

                return (false, $"Failed to load items: {response.ReasonPhrase}", new List<Item>());
            }
            catch (Exception ex)
            {
                return (false, $"Error connecting to backend: {ex.Message}", new List<Item>());
            }
        }

        public async Task<(bool Success, string? ErrorMessage, Item? Item)> CreateItemAsync(Item item)
        {
            try
            {
                SetAuthHeader();
                var json = JsonSerializer.Serialize(item);
                var content = new StringContent(json, Encoding.UTF8, "application/json");

                var response = await _httpClient.PostAsync($"{BaseUrl}/items", content);
                var responseBody = await response.Content.ReadAsStringAsync();

                if (response.IsSuccessStatusCode)
                {
                    var result = JsonSerializer.Deserialize<ApiResponse<Item>>(responseBody);
                    return (true, null, result?.Data);
                }

                if (response.StatusCode == System.Net.HttpStatusCode.Forbidden)
                {
                    return (false, "Action Forbidden: Your role is not allowed to add new inventory items.", null);
                }

                return (false, $"Failed to create item: {responseBody}", null);
            }
            catch (Exception ex)
            {
                return (false, $"Error connecting to backend: {ex.Message}", null);
            }
        }

        public async Task<(bool Success, string? ErrorMessage, Item? Item)> UpdateItemAsync(int id, Item item)
        {
            try
            {
                SetAuthHeader();
                var json = JsonSerializer.Serialize(item);
                var content = new StringContent(json, Encoding.UTF8, "application/json");

                var response = await _httpClient.PutAsync($"{BaseUrl}/items/{id}", content);
                var responseBody = await response.Content.ReadAsStringAsync();

                if (response.IsSuccessStatusCode)
                {
                    var result = JsonSerializer.Deserialize<ApiResponse<Item>>(responseBody);
                    return (true, null, result?.Data);
                }

                if (response.StatusCode == System.Net.HttpStatusCode.Forbidden)
                {
                    return (false, "Action Forbidden: Your role does not have permission to update this item.", null);
                }

                return (false, $"Failed to update item: {responseBody}", null);
            }
            catch (Exception ex)
            {
                return (false, $"Error connecting to backend: {ex.Message}", null);
            }
        }

        public async Task<(bool Success, string? ErrorMessage)> DeleteItemAsync(int id)
        {
            try
            {
                SetAuthHeader();
                var response = await _httpClient.DeleteAsync($"{BaseUrl}/items/{id}");

                if (response.IsSuccessStatusCode)
                {
                    return (true, null);
                }

                if (response.StatusCode == System.Net.HttpStatusCode.Forbidden)
                {
                    return (false, "Action Forbidden: Only Store Owners are permitted to delete inventory items.");
                }

                return (false, $"Failed to delete item: {response.ReasonPhrase}");
            }
            catch (Exception ex)
            {
                return (false, $"Error connecting to backend: {ex.Message}");
            }
        }

        public async Task<(bool Success, string? ErrorMessage, List<Customer> Customers)> GetCustomersAsync(string? search = null)
        {
            try
            {
                SetAuthHeader();
                var url = $"{BaseUrl}/customers" + (!string.IsNullOrWhiteSpace(search) ? $"?search={Uri.EscapeDataString(search)}" : "");
                var response = await _httpClient.GetAsync(url);
                var responseBody = await response.Content.ReadAsStringAsync();

                if (response.IsSuccessStatusCode)
                {
                    var result = JsonSerializer.Deserialize<PaginatedResponse<Customer>>(responseBody);
                    return (true, null, result?.Data ?? new List<Customer>());
                }

                if (response.StatusCode == System.Net.HttpStatusCode.Unauthorized)
                {
                    return (false, "Session expired. Please log in again.", new List<Customer>());
                }

                return (false, $"Failed to load customers: {response.ReasonPhrase}", new List<Customer>());
            }
            catch (Exception ex)
            {
                return (false, $"Error connecting to backend: {ex.Message}", new List<Customer>());
            }
        }

        public async Task<(bool Success, string? ErrorMessage, Customer? Customer)> CreateCustomerAsync(Customer customer)
        {
            try
            {
                SetAuthHeader();
                var json = JsonSerializer.Serialize(customer);
                var content = new StringContent(json, Encoding.UTF8, "application/json");

                var response = await _httpClient.PostAsync($"{BaseUrl}/customers", content);
                var responseBody = await response.Content.ReadAsStringAsync();

                if (response.IsSuccessStatusCode)
                {
                    var result = JsonSerializer.Deserialize<ApiResponse<Customer>>(responseBody);
                    return (true, null, result?.Data);
                }

                return (false, $"Failed to create customer: {responseBody}", null);
            }
            catch (Exception ex)
            {
                return (false, $"Error connecting to backend: {ex.Message}", null);
            }
        }

        public async Task<(bool Success, string? ErrorMessage, Customer? Customer)> UpdateCustomerAsync(int id, Customer customer)
        {
            try
            {
                SetAuthHeader();
                var json = JsonSerializer.Serialize(customer);
                var content = new StringContent(json, Encoding.UTF8, "application/json");

                var response = await _httpClient.PutAsync($"{BaseUrl}/customers/{id}", content);
                var responseBody = await response.Content.ReadAsStringAsync();

                if (response.IsSuccessStatusCode)
                {
                    var result = JsonSerializer.Deserialize<ApiResponse<Customer>>(responseBody);
                    return (true, null, result?.Data);
                }

                return (false, $"Failed to update customer: {responseBody}", null);
            }
            catch (Exception ex)
            {
                return (false, $"Error connecting to backend: {ex.Message}", null);
            }
        }

        public async Task<(bool Success, string? ErrorMessage)> DeleteCustomerAsync(int id)
        {
            try
            {
                SetAuthHeader();
                var response = await _httpClient.DeleteAsync($"{BaseUrl}/customers/{id}");

                if (response.IsSuccessStatusCode)
                {
                    return (true, null);
                }

                if (response.StatusCode == System.Net.HttpStatusCode.Forbidden)
                {
                    return (false, "Action Forbidden: Only Store Owners are permitted to delete customer records.");
                }

                return (false, $"Failed to delete customer: {response.ReasonPhrase}");
            }
            catch (Exception ex)
            {
                return (false, $"Error connecting to backend: {ex.Message}");
            }
        }
    }
}
