# Creamy - C# WPF Desktop Application

> **Delivergate Backend Engineer Intern Practical Exam — Bonus Track (C# WPF Application)**

A modern, responsive desktop application built with **C#**, **WPF**, and **.NET 8** following the **Model-View-ViewModel (MVVM)** architectural pattern. It directly consumes the RESTful APIs exposed by the Laravel backend (`Laravel_Backend`).

---

## 📋 Features & Screen Implementation

### 1. Login Screen (`Views/LoginView.xaml`)
* Secure user authentication against the Laravel API (`POST /api/auth/login`).
* Automatic bearer token storage in `AuthSession` singleton.
* Real-time input validation and error banner display on invalid credentials (HTTP 401).
* **Quick-Select Test Role Presets**: One-click autofill buttons for **Owner**, **Manager**, and **Cashier** to make evaluator testing effortless.

### 2. Inventory Management Screen (`Views/InventoryView.xaml`)
* Displays inventory items in a responsive, styled DataGrid (Name, Category, Unit Price, Stock Status, Description).
* **Live Search**: Search items by name with immediate backend querying (`GET /api/items?search=...`).
* **Category Filtering**: Dropdown filter for specific flavors (All, Chocolate, Vanilla, Strawberry, Mint, Fruit, etc.).
* **Role-Based Controls (RBAC)**:
  * **Add Item**: Enabled for **Owner** and **Manager**. Displays a clear permission notice if attempted by Cashier.
  * **Edit Item**: Enabled for all roles (**Owner**, **Manager**, **Cashier** - allowing cashiers to update stock counts when sales occur).
  * **Delete Item**: Enabled **only for Owner**. The button is disabled with explanatory tooltips for Manager and Cashier.
* Add/Edit modal dialog with client-side price and stock validation.

### 3. Customer Management Screen (`Views/CustomerView.xaml`)
* Displays customer records with contact information, addresses, and dietary notes.
* **Instant Search**: Search customers across names, emails, and phone numbers (`GET /api/customers?search=...`).
* **Role-Based Controls (RBAC)**:
  * **Add Customer**: Enabled for **all roles** (Owner, Manager, Cashier).
  * **Edit Customer**: Enabled for **all roles**.
  * **Delete Customer**: Enabled **only for Owner**. Disabled with tooltips for Manager and Cashier.
* Add/Edit modal dialog.

### 4. Application Shell & Sidebar (`Views/MainWindow.xaml`)
* Responsive sidebar with view navigation.
* User profile badge displaying current logged-in user name and role badge (`OWNER` in gold, `MANAGER` in blue, `CASHIER` in emerald).
* One-click Sign Out (`POST /api/auth/logout`) that clears token state and returns to the login screen.

---

## 🏗️ Architecture & Project Structure

The project strictly follows the **MVVM** pattern with zero unnecessary third-party dependencies:

```
C#_WPF_Application/
├── Models/
│   ├── User.cs              # User model with role check helpers
│   ├── Item.cs              # Inventory item model with formatting
│   ├── Customer.cs          # Customer record model
│   └── ApiResponse.cs       # Generic JSON deserialization wrappers
├── Services/
│   ├── IApiService.cs       # Contract for backend communication
│   ├── ApiService.cs        # HttpClient implementation with Bearer auth headers
│   └── AuthSession.cs       # Singleton state managing token, user, and RBAC permissions
├── ViewModels/
│   ├── ViewModelBase.cs     # INotifyPropertyChanged base implementation
│   ├── RelayCommand.cs      # ICommand implementation
│   ├── MainViewModel.cs     # Shell viewmodel managing active screens and navigation
│   ├── LoginViewModel.cs    # Manages authentication state and credentials
│   ├── InventoryViewModel.cs# Manages inventory CRUD, filtering, and role checks
│   └── CustomerViewModel.cs # Manages customer CRUD, search, and role checks
├── Views/
│   ├── MainWindow.xaml (.cs)# Host window with sidebar navigation
│   ├── LoginView.xaml (.cs) # Polished login card view
│   ├── InventoryView.xaml   # Inventory DataGrid and modal editor
│   └── CustomerView.xaml    # Customer DataGrid and modal editor
├── Resources/
│   └── Styles.xaml          # Modern color palette, buttons, textboxes, and DataGrid styles
├── App.xaml (.cs)           # Application entry point & resource dictionary merge
├── Creamy.WPF.csproj        # .NET 8 WPF project file
└── README.md                # WPF documentation
```

---

## ⚙️ Prerequisites & Setup

### Requirements
* Windows 10/11
* **.NET 8.0 SDK** or higher (`dotnet --version`)
* Running Laravel backend instance (`php artisan serve` at `http://127.0.0.1:8000`)

### Step 1: Ensure Laravel Backend is Running
Before running the desktop application, start the Laravel API server from the `Laravel_Backend` directory:
```bash
cd Laravel_Backend
php artisan serve
```
*(Verify the API is accessible at `http://127.0.0.1:8000/api`)*

### Step 2: Build the WPF Application
From the `C#_WPF_Application` directory:
```bash
dotnet build
```

### Step 3: Run the Application
```bash
dotnet run
```

---

## 🔑 Test Credentials (Quick Reference)

| Role | Email | Password | Allowed Capabilities in WPF |
|---|---|---|---|
| **Owner** | `owner@delivergate.com` | `Password123!` | Full CRUD on inventory and customers. Can delete records. |
| **Manager** | `manager@delivergate.com` | `Password123!` | Can view, add, and edit items and customers. Delete button is disabled. |
| **Cashier** | `cashier@delivergate.com` | `Password123!` | Can view and edit items (update stock). Can view, add, and edit customers. Add item and Delete buttons are disabled. |

*(Tip: On the login screen, click any of the 3 quick-select role buttons to instantly populate the credentials!)*
