using Creamy.WPF.Models;

namespace Creamy.WPF.Services
{
    public class AuthSession
    {
        private static AuthSession? _instance;
        public static AuthSession Instance => _instance ??= new AuthSession();

        public string? Token { get; private set; }
        public User? CurrentUser { get; private set; }

        public event Action? SessionChanged;

        public bool IsAuthenticated => !string.IsNullOrEmpty(Token) && CurrentUser != null;

        // RBAC Permissions matching the Delivergate Assessment specification:
        // 1. Owner: Full control (create, view, edit, delete both items and customers)
        // 2. Manager: Can add, view, edit items and customers; CANNOT delete either
        // 3. Cashier: Can view and edit items (e.g. stock); CANNOT add or delete items; Can add, view, edit customers; CANNOT delete customers
        public bool CanCreateItem => CurrentUser != null && (CurrentUser.IsOwner || CurrentUser.IsManager);
        public bool CanEditItem => CurrentUser != null;
        public bool CanDeleteItem => CurrentUser != null && CurrentUser.IsOwner;

        public bool CanCreateCustomer => CurrentUser != null;
        public bool CanEditCustomer => CurrentUser != null;
        public bool CanDeleteCustomer => CurrentUser != null && CurrentUser.IsOwner;

        public void SetSession(string token, User user)
        {
            Token = token;
            CurrentUser = user;
            SessionChanged?.Invoke();
        }

        public void ClearSession()
        {
            Token = null;
            CurrentUser = null;
            SessionChanged?.Invoke();
        }
    }
}
