using System.Collections.ObjectModel;
using System.Windows.Input;
using Creamy.WPF.Models;
using Creamy.WPF.Services;

namespace Creamy.WPF.ViewModels
{
    public class InventoryViewModel : ViewModelBase
    {
        private readonly IApiService _apiService;

        public ObservableCollection<Item> Items { get; } = new();
        public ObservableCollection<string> Categories { get; } = new()
        {
            "All", "Chocolate", "Vanilla", "Strawberry", "Mint", "Fruit", "Caramel", "Nut"
        };

        private Item? _selectedItem;
        public Item? SelectedItem
        {
            get => _selectedItem;
            set
            {
                if (SetField(ref _selectedItem, value))
                {
                    OnPropertyChanged(nameof(IsItemSelected));
                }
            }
        }

        public bool IsItemSelected => SelectedItem != null;

        private string _searchTerm = string.Empty;
        public string SearchTerm
        {
            get => _searchTerm;
            set => SetField(ref _searchTerm, value);
        }

        private string _selectedCategory = "All";
        public string SelectedCategory
        {
            get => _selectedCategory;
            set
            {
                if (SetField(ref _selectedCategory, value))
                {
                    _ = LoadItemsAsync();
                }
            }
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
        public bool CanCreateItem => AuthSession.Instance.CanCreateItem;
        public bool CanEditItem => AuthSession.Instance.CanEditItem;
        public bool CanDeleteItem => AuthSession.Instance.CanDeleteItem;

        // Editor Form properties
        private bool _isEditorOpen;
        public bool IsEditorOpen
        {
            get => _isEditorOpen;
            set => SetField(ref _isEditorOpen, value);
        }

        private string _editorTitle = "New Ice Cream Item";
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

        private string _formCategory = "Chocolate";
        public string FormCategory
        {
            get => _formCategory;
            set => SetField(ref _formCategory, value);
        }

        private decimal _formPrice = 4.50m;
        public decimal FormPrice
        {
            get => _formPrice;
            set => SetField(ref _formPrice, value);
        }

        private int _formQuantity = 50;
        public int FormQuantity
        {
            get => _formQuantity;
            set => SetField(ref _formQuantity, value);
        }

        private string _formUnit = "scoop";
        public string FormUnit
        {
            get => _formUnit;
            set => SetField(ref _formUnit, value);
        }

        private string _formDescription = string.Empty;
        public string FormDescription
        {
            get => _formDescription;
            set => SetField(ref _formDescription, value);
        }

        public int TotalFlavorsCount => Items?.Count ?? 0;
        public int TotalStockQuantity => Items?.Sum(i => i.Quantity) ?? 0;
        public int LowStockCount => Items?.Count(i => i.Quantity > 0 && i.Quantity <= 10) ?? 0;
        public int OutOfStockCount => Items?.Count(i => i.Quantity == 0) ?? 0;
        public int ActiveCategoriesCount => Items?.Select(i => i.FlavorCategory).Distinct(StringComparer.OrdinalIgnoreCase).Count() ?? 0;

        public ICommand LoadItemsCommand { get; }
        public ICommand SearchCommand { get; }
        public ICommand RefreshCommand { get; }
        public ICommand OpenCreateDialogCommand { get; }
        public ICommand OpenEditDialogCommand { get; }
        public ICommand SaveFormCommand { get; }
        public ICommand CloseFormCommand { get; }
        public ICommand DeleteItemCommand { get; }

        public InventoryViewModel() : this(null)
        {
        }

        public InventoryViewModel(IApiService? apiService)
        {
            _apiService = apiService ?? ApiService.Instance;

            LoadItemsCommand = new RelayCommand(async () => await LoadItemsAsync());
            SearchCommand = new RelayCommand(async () => await LoadItemsAsync());
            RefreshCommand = new RelayCommand(async () => await LoadItemsAsync());

            OpenCreateDialogCommand = new RelayCommand(OpenCreateDialog);
            OpenEditDialogCommand = new RelayCommand(OpenEditDialog, () => SelectedItem != null);
            SaveFormCommand = new RelayCommand(async () => await SaveFormAsync());
            CloseFormCommand = new RelayCommand(() => IsEditorOpen = false);
            DeleteItemCommand = new RelayCommand(async () => await DeleteSelectedItemAsync(), () => SelectedItem != null);

            AuthSession.Instance.SessionChanged += RefreshPermissions;
            _ = LoadItemsAsync();
        }

        private void RefreshPermissions()
        {
            OnPropertyChanged(nameof(CanCreateItem));
            OnPropertyChanged(nameof(CanEditItem));
            OnPropertyChanged(nameof(CanDeleteItem));
        }

        private void NotifyKpisChanged()
        {
            OnPropertyChanged(nameof(TotalFlavorsCount));
            OnPropertyChanged(nameof(TotalStockQuantity));
            OnPropertyChanged(nameof(LowStockCount));
            OnPropertyChanged(nameof(OutOfStockCount));
            OnPropertyChanged(nameof(ActiveCategoriesCount));
        }

        public async Task LoadItemsAsync()
        {
            IsLoading = true;
            Message = null;

            try
            {
                var (success, errorMessage, items) = await _apiService.GetItemsAsync(SearchTerm, SelectedCategory);
                if (success)
                {
                    Items.Clear();
                    foreach (var item in items)
                    {
                        Items.Add(item);
                    }
                    NotifyKpisChanged();
                }
                else
                {
                    IsErrorMessage = true;
                    Message = errorMessage ?? "Failed to retrieve items.";
                }
            }
            finally
            {
                IsLoading = false;
            }
        }

        private void OpenCreateDialog()
        {
            if (!CanCreateItem)
            {
                IsErrorMessage = true;
                Message = "Permission Denied: Your role does not allow adding new inventory items.";
                return;
            }

            _formId = 0;
            FormName = string.Empty;
            FormCategory = "Chocolate";
            FormPrice = 4.50m;
            FormQuantity = 50;
            FormUnit = "scoop";
            FormDescription = string.Empty;
            EditorTitle = "Add New Ice Cream Item";
            IsEditorOpen = true;
            Message = null;
        }

        private void OpenEditDialog()
        {
            if (SelectedItem == null) return;

            _formId = SelectedItem.Id;
            FormName = SelectedItem.Name;
            FormCategory = SelectedItem.FlavorCategory;
            FormPrice = SelectedItem.Price;
            FormQuantity = SelectedItem.Quantity;
            FormUnit = SelectedItem.Unit;
            FormDescription = SelectedItem.Description ?? string.Empty;
            EditorTitle = $"Edit Item #{SelectedItem.Id} - {SelectedItem.Name}";
            IsEditorOpen = true;
            Message = null;
        }

        private async Task SaveFormAsync()
        {
            if (string.IsNullOrWhiteSpace(FormName))
            {
                IsErrorMessage = true;
                Message = "Item name is required.";
                return;
            }

            if (FormPrice < 0 || FormQuantity < 0)
            {
                IsErrorMessage = true;
                Message = "Price and quantity cannot be negative.";
                return;
            }

            IsLoading = true;
            var item = new Item
            {
                Name = FormName,
                FlavorCategory = FormCategory,
                Price = FormPrice,
                Quantity = FormQuantity,
                Unit = FormUnit,
                Description = FormDescription,
                IsActive = true
            };

            if (_formId == 0)
            {
                var (success, error, created) = await _apiService.CreateItemAsync(item);
                if (success && created != null)
                {
                    IsEditorOpen = false;
                    IsErrorMessage = false;
                    Message = $"Created '{created.Name}' successfully.";
                    await LoadItemsAsync();
                }
                else
                {
                    IsErrorMessage = true;
                    Message = error ?? "Failed to create item.";
                }
            }
            else
            {
                var (success, error, updated) = await _apiService.UpdateItemAsync(_formId, item);
                if (success && updated != null)
                {
                    IsEditorOpen = false;
                    IsErrorMessage = false;
                    Message = $"Updated '{updated.Name}' successfully.";
                    await LoadItemsAsync();
                }
                else
                {
                    IsErrorMessage = true;
                    Message = error ?? "Failed to update item.";
                }
            }

            IsLoading = false;
        }

        private async Task DeleteSelectedItemAsync()
        {
            if (SelectedItem == null) return;

            if (!CanDeleteItem)
            {
                IsErrorMessage = true;
                Message = "Action Denied: Only Store Owners are permitted to delete inventory items.";
                return;
            }

            IsLoading = true;
            var (success, error) = await _apiService.DeleteItemAsync(SelectedItem.Id);
            if (success)
            {
                IsErrorMessage = false;
                Message = $"Deleted item '{SelectedItem.Name}' successfully.";
                SelectedItem = null;
                await LoadItemsAsync();
            }
            else
            {
                IsErrorMessage = true;
                Message = error ?? "Failed to delete item.";
            }

            IsLoading = false;
        }
    }
}
