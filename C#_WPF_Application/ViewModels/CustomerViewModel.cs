using System.Collections.ObjectModel;
using System.Windows.Input;
using Creamy.WPF.Models;
using Creamy.WPF.Services;

namespace Creamy.WPF.ViewModels
{
    public class CustomerViewModel : ViewModelBase
    {
        private readonly IApiService _apiService;

        public ObservableCollection<Customer> Customers { get; } = new();

        private Customer? _selectedCustomer;
        public Customer? SelectedCustomer
        {
            get => _selectedCustomer;
            set
            {
                if (SetField(ref _selectedCustomer, value))
                {
                    OnPropertyChanged(nameof(IsCustomerSelected));
                }
            }
        }

        public bool IsCustomerSelected => SelectedCustomer != null;

        private string _searchTerm = string.Empty;
        public string SearchTerm
        {
            get => _searchTerm;
            set => SetField(ref _searchTerm, value);
        }

        private bool _isLoading;
        public bool IsLoading
        {
            get => _isLoading;
            set => SetField(ref _isLoading, value);
        }

        private string? _message;
        public string? Message
        {
            get => _message;
            set
            {
                if (SetField(ref _message, value))
                {
                    OnPropertyChanged(nameof(HasMessage));
                }
            }
        }

        public bool HasMessage => !string.IsNullOrEmpty(Message);

        private bool _isErrorMessage;
        public bool IsErrorMessage
        {
            get => _isErrorMessage;
            set => SetField(ref _isErrorMessage, value);
        }

        // RBAC Properties
        public bool CanCreateCustomer => AuthSession.Instance.CanCreateCustomer;
        public bool CanEditCustomer => AuthSession.Instance.CanEditCustomer;
        public bool CanDeleteCustomer => AuthSession.Instance.CanDeleteCustomer;

        // Editor Form properties
        private bool _isEditorOpen;
        public bool IsEditorOpen
        {
            get => _isEditorOpen;
            set => SetField(ref _isEditorOpen, value);
        }

        private string _editorTitle = "New Customer";
        public string EditorTitle
        {
            get => _editorTitle;
            set => SetField(ref _editorTitle, value);
        }

        private int _formId;
        private string _formName = string.Empty;
        public string FormName
        {
            get => _formName;
            set => SetField(ref _formName, value);
        }

        private string _formEmail = string.Empty;
        public string FormEmail
        {
            get => _formEmail;
            set => SetField(ref _formEmail, value);
        }

        private string _formPhone = string.Empty;
        public string FormPhone
        {
            get => _formPhone;
            set => SetField(ref _formPhone, value);
        }

        private string _formAddress = string.Empty;
        public string FormAddress
        {
            get => _formAddress;
            set => SetField(ref _formAddress, value);
        }

        private string _formNotes = string.Empty;
        public string FormNotes
        {
            get => _formNotes;
            set => SetField(ref _formNotes, value);
        }

        public ICommand LoadCustomersCommand { get; }
        public ICommand SearchCommand { get; }
        public ICommand OpenCreateDialogCommand { get; }
        public ICommand OpenEditDialogCommand { get; }
        public ICommand SaveFormCommand { get; }
        public ICommand CloseFormCommand { get; }
        public ICommand DeleteCustomerCommand { get; }

        public CustomerViewModel(IApiService? apiService = null)
        {
            _apiService = apiService ?? ApiService.Instance;

            LoadCustomersCommand = new RelayCommand(async () => await LoadCustomersAsync());
            SearchCommand = new RelayCommand(async () => await LoadCustomersAsync());

            OpenCreateDialogCommand = new RelayCommand(OpenCreateDialog);
            OpenEditDialogCommand = new RelayCommand(OpenEditDialog, () => SelectedCustomer != null);
            SaveFormCommand = new RelayCommand(async () => await SaveFormAsync());
            CloseFormCommand = new RelayCommand(() => IsEditorOpen = false);
            DeleteCustomerCommand = new RelayCommand(async () => await DeleteSelectedCustomerAsync(), () => SelectedCustomer != null);

            AuthSession.Instance.SessionChanged += RefreshPermissions;
            _ = LoadCustomersAsync();
        }

        private void RefreshPermissions()
        {
            OnPropertyChanged(nameof(CanCreateCustomer));
            OnPropertyChanged(nameof(CanEditCustomer));
            OnPropertyChanged(nameof(CanDeleteCustomer));
        }

        public async Task LoadCustomersAsync()
        {
            IsLoading = true;
            Message = null;

            try
            {
                var (success, errorMessage, customers) = await _apiService.GetCustomersAsync(SearchTerm);
                if (success)
                {
                    Customers.Clear();
                    foreach (var customer in customers)
                    {
                        Customers.Add(customer);
                    }
                }
                else
                {
                    IsErrorMessage = true;
                    Message = errorMessage ?? "Failed to retrieve customer records.";
                }
            }
            finally
            {
                IsLoading = false;
            }
        }

        private void OpenCreateDialog()
        {
            _formId = 0;
            FormName = string.Empty;
            FormEmail = string.Empty;
            FormPhone = string.Empty;
            FormAddress = string.Empty;
            FormNotes = string.Empty;
            EditorTitle = "Register New Customer";
            IsEditorOpen = true;
            Message = null;
        }

        private void OpenEditDialog()
        {
            if (SelectedCustomer == null) return;

            _formId = SelectedCustomer.Id;
            FormName = SelectedCustomer.Name;
            FormEmail = SelectedCustomer.Email ?? string.Empty;
            FormPhone = SelectedCustomer.Phone;
            FormAddress = SelectedCustomer.Address ?? string.Empty;
            FormNotes = SelectedCustomer.Notes ?? string.Empty;
            EditorTitle = $"Edit Customer #{SelectedCustomer.Id} - {SelectedCustomer.Name}";
            IsEditorOpen = true;
            Message = null;
        }

        private async Task SaveFormAsync()
        {
            if (string.IsNullOrWhiteSpace(FormName) || string.IsNullOrWhiteSpace(FormPhone))
            {
                IsErrorMessage = true;
                Message = "Customer name and phone number are required.";
                return;
            }

            IsLoading = true;
            var customer = new Customer
            {
                Name = FormName,
                Email = string.IsNullOrWhiteSpace(FormEmail) ? null : FormEmail,
                Phone = FormPhone,
                Address = string.IsNullOrWhiteSpace(FormAddress) ? null : FormAddress,
                Notes = string.IsNullOrWhiteSpace(FormNotes) ? null : FormNotes
            };

            if (_formId == 0)
            {
                var (success, error, created) = await _apiService.CreateCustomerAsync(customer);
                if (success && created != null)
                {
                    IsEditorOpen = false;
                    IsErrorMessage = false;
                    Message = $"Registered customer '{created.Name}' successfully.";
                    await LoadCustomersAsync();
                }
                else
                {
                    IsErrorMessage = true;
                    Message = error ?? "Failed to register customer.";
                }
            }
            else
            {
                var (success, error, updated) = await _apiService.UpdateCustomerAsync(_formId, customer);
                if (success && updated != null)
                {
                    IsEditorOpen = false;
                    IsErrorMessage = false;
                    Message = $"Updated customer '{updated.Name}' successfully.";
                    await LoadCustomersAsync();
                }
                else
                {
                    IsErrorMessage = true;
                    Message = error ?? "Failed to update customer.";
                }
            }

            IsLoading = false;
        }

        private async Task DeleteSelectedCustomerAsync()
        {
            if (SelectedCustomer == null) return;

            if (!CanDeleteCustomer)
            {
                IsErrorMessage = true;
                Message = "Action Denied: Only Store Owners are permitted to delete customer records.";
                return;
            }

            IsLoading = true;
            var (success, error) = await _apiService.DeleteCustomerAsync(SelectedCustomer.Id);
            if (success)
            {
                IsErrorMessage = false;
                Message = $"Deleted customer '{SelectedCustomer.Name}' successfully.";
                SelectedCustomer = null;
                await LoadCustomersAsync();
            }
            else
            {
                IsErrorMessage = true;
                Message = error ?? "Failed to delete customer.";
            }

            IsLoading = false;
        }
    }
}
