using System.Windows.Input;
using Creamy.WPF.Models;
using Creamy.WPF.Services;

namespace Creamy.WPF.ViewModels
{
    public class LoginViewModel : ViewModelBase
    {
        private readonly IApiService _apiService;

        private string _email = "owner@creamy.com";
        public string Email
        {
            get => _email;
            set => SetField(ref _email, value);
        }

        private string _password = "Owner@123";
        public string Password
        {
            get => _password;
            set => SetField(ref _password, value);
        }

        private string? _errorMessage;
        public string? ErrorMessage
        {
            get => _errorMessage;
            set
            {
                if (SetField(ref _errorMessage, value))
                {
                    OnPropertyChanged(nameof(HasError));
                }
            }
        }

        public bool HasError => !string.IsNullOrEmpty(ErrorMessage);

        private bool _isLoading;
        public bool IsLoading
        {
            get => _isLoading;
            set => SetField(ref _isLoading, value);
        }

        public ICommand LoginCommand { get; }
        public ICommand SetOwnerCredentialsCommand { get; }
        public ICommand SetManagerCredentialsCommand { get; }
        public ICommand SetCashierCredentialsCommand { get; }

        public event Action<User>? LoginSuccessful;

        public LoginViewModel(IApiService? apiService = null)
        {
            _apiService = apiService ?? ApiService.Instance;

            LoginCommand = new RelayCommand(async () => await ExecuteLoginAsync(), () => !IsLoading);

            SetOwnerCredentialsCommand = new RelayCommand(() =>
            {
                Email = "owner@creamy.com";
                Password = "Owner@123";
                ErrorMessage = null;
            });

            SetManagerCredentialsCommand = new RelayCommand(() =>
            {
                Email = "manager1@creamy.com";
                Password = "Manager1@123";
                ErrorMessage = null;
            });

            SetCashierCredentialsCommand = new RelayCommand(() =>
            {
                Email = "cashier1@creamy.com";
                Password = "cashier1@123";
                ErrorMessage = null;
            });
        }

        private async Task ExecuteLoginAsync()
        {
            if (string.IsNullOrWhiteSpace(Email) || string.IsNullOrWhiteSpace(Password))
            {
                ErrorMessage = "Please enter both email and password.";
                return;
            }

            IsLoading = true;
            ErrorMessage = null;

            try
            {
                var (success, errorMessage, user) = await _apiService.LoginAsync(Email, Password);
                if (success && user != null)
                {
                    LoginSuccessful?.Invoke(user);
                }
                else
                {
                    ErrorMessage = errorMessage ?? "Failed to log in. Please check your credentials.";
                }
            }
            finally
            {
                IsLoading = false;
            }
        }
    }
}
