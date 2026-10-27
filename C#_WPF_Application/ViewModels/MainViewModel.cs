using System.Windows.Input;
using Creamy.WPF.Models;
using Creamy.WPF.Services;

namespace Creamy.WPF.ViewModels
{
    public class MainViewModel : ViewModelBase
    {
        private readonly IApiService _apiService;

        private object? _currentViewModel;
        public object? CurrentViewModel
        {
            get => _currentViewModel;
            set => SetField(ref _currentViewModel, value);
        }

        private string _activeScreen = "Inventory";
        public string ActiveScreen
        {
            get => _activeScreen;
            set => SetField(ref _activeScreen, value);
        }

        public bool IsAuthenticated => AuthSession.Instance.IsAuthenticated;
        public User? CurrentUser => AuthSession.Instance.CurrentUser;

        public string UserDisplayName => CurrentUser?.Name ?? "Guest";
        public string RoleBadge => CurrentUser?.RoleDisplayName?.ToUpperInvariant() ?? "UNAUTHENTICATED";

        public string RoleBadgeColor => CurrentUser?.Role switch
        {
            "owner" => "#f59e0b",    // Amber/Gold
            "manager" => "#3b82f6",  // Blue
            "cashier" => "#10b981",  // Emerald
            _ => "#6b7280"
        };

        public ICommand NavigateInventoryCommand { get; }
        public ICommand NavigateCustomersCommand { get; }
        public ICommand LogoutCommand { get; }

        public MainViewModel() : this(null)
        {
        }

        public MainViewModel(IApiService? apiService)
        {
            _apiService = apiService ?? ApiService.Instance;

            NavigateInventoryCommand = new RelayCommand(NavigateToInventory, () => IsAuthenticated);
            NavigateCustomersCommand = new RelayCommand(NavigateToCustomers, () => IsAuthenticated);
            LogoutCommand = new RelayCommand(async () => await ExecuteLogoutAsync(), () => IsAuthenticated);

            AuthSession.Instance.SessionChanged += HandleSessionChanged;

            ShowLogin();
        }

        private void HandleSessionChanged()
        {
            OnPropertyChanged(nameof(IsAuthenticated));
            OnPropertyChanged(nameof(CurrentUser));
            OnPropertyChanged(nameof(UserDisplayName));
            OnPropertyChanged(nameof(RoleBadge));
            OnPropertyChanged(nameof(RoleBadgeColor));

            if (!IsAuthenticated)
            {
                ShowLogin();
            }
        }

        public void ShowLogin()
        {
            var loginVm = new LoginViewModel(_apiService);
            loginVm.LoginSuccessful += user =>
            {
                NavigateToInventory();
            };

            ActiveScreen = "Login";
            CurrentViewModel = loginVm;
        }

        public void NavigateToInventory()
        {
            ActiveScreen = "Inventory";
            CurrentViewModel = new InventoryViewModel(_apiService);
        }

        public void NavigateToCustomers()
        {
            ActiveScreen = "Customers";
            CurrentViewModel = new CustomerViewModel(_apiService);
        }

        private async Task ExecuteLogoutAsync()
        {
            await _apiService.LogoutAsync();
            ShowLogin();
        }
    }
}
