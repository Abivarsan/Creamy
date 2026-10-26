# Creamy - Ice Cream Shop Management System

> **Delivergate Backend Engineer Intern Practical Exam**  
> **Candidate Repository**: [https://github.com/Abivarsan/Creamy.git](https://github.com/Abivarsan/Creamy.git)

This repository contains the complete technical assessment solution for the **Backend Engineer Intern** position at **Delivergate**. It includes both the core **Laravel RESTful Backend API** (Option B) and the optional bonus **C# WPF Desktop Application**.

---

## 🍨 Project Overview

The objective is to build an end-to-end Ice Cream Shop Management System managing **inventory items** across flavor categories (e.g., Chocolate, Vanilla, Strawberry, Mint, Fruit) and **customer records**, secured with token-based authentication and strict **Role-Based Access Control (RBAC)** across three user roles: **Owner**, **Manager**, and **Cashier**.

---

## 📁 Repository Directory Structure

```text
Creamy/
├── Laravel_Backend/                     # Option B: Core Laravel Backend Implementation
│   ├── app/                             # Enums, Http Controllers, Form Requests, Resources, Policies, Models
│   ├── bootstrap/                       # Laravel app bootstrap & route configuration
│   ├── config/                          # App, auth, sanctum, and database configurations
│   ├── database/                        # Migrations and Seeders (Users, Items, Customers)
│   ├── docs/                            # ER Diagram (SVG & MD) & Postman Collection JSON
│   ├── routes/                          # REST API route definitions (/api)
│   ├── tests/                           # Feature and Unit tests (100% passing RBAC & CRUD test suite)
│   ├── .env.example                     # Environment template (MySQL & SQLite)
│   ├── composer.json                    # PHP dependencies
│   ├── phpunit.xml                      # Automated testing configuration
│   └── README.md                        # Dedicated Laravel backend documentation
│
├── C#_WPF_Application/                  # Bonus: C# WPF Desktop Client (.NET 8)
│   ├── Models/                          # Data models (User, Item, Customer, ApiResponse)
│   ├── Services/                        # ApiService (HttpClient + Token Auth) & AuthSession
│   ├── ViewModels/                      # MVVM ViewModels (Main, Login, Inventory, Customer)
│   ├── Views/                           # Modern XAML Views (MainWindow, Login, Inventory, Customer)
│   ├── Resources/                       # Styles.xaml (Modern color palette, buttons, DataGrid)
│   ├── App.xaml                         # WPF application configuration
│   ├── Creamy.WPF.csproj                # .NET 8 project specification
│   └── README.md                        # Dedicated C# WPF desktop documentation
│
├── docs/                                # Project-wide deliverables
│   ├── er-diagram.svg                   # Standalone vector SVG ER diagram
│   ├── er-diagram.md                    # Data dictionary & Mermaid source
│   └── Delivergate_Ice_Cream_Shop_API.postman_collection.json # Ready-to-import Postman collection
│
├── .gitignore                           # Unified gitignore for Laravel and .NET
└── README.md                            # Monorepo overview and navigation
```

---

## 🚀 Implementations Summary & Quick Links

### 1. [Laravel Backend (Option B)](Laravel_Backend/README.md)
* **Technology**: PHP 8.3, Laravel 11+, MySQL, Laravel Sanctum
* **Architecture**: Controllers, Form Requests, API Resources, Eloquent Models, Policies
* **Key Features**:
  * Token-based authentication (`/api/auth/login`, `/api/auth/logout`, `/api/auth/me`).
  * Full CRUD for inventory items with search by name and flavor category filter.
  * Full CRUD for customer records with instant search.
  * Strict RBAC enforced via `ItemPolicy` and `CustomerPolicy` (Owner, Manager, Cashier).
  * 29 automated tests with 103 assertions passing in under 2 seconds.
* 👉 **[Read the Full Laravel Backend Documentation & Setup Guide](Laravel_Backend/README.md)**

### 2. [C# WPF Desktop Application (Bonus Track)](C#_WPF_Application/README.md)
* **Technology**: C#, WPF, .NET 8 (`net8.0-windows`)
* **Architecture**: Model-View-ViewModel (MVVM) pattern with `HttpClient` service layer
* **Key Features**:
  * **Login Screen**: Token authentication, error handling, and one-click quick-select test role buttons (Owner, Manager, Cashier).
  * **Inventory Management Screen**: DataGrid display, real-time search, category dropdown filter, role-aware Add/Edit/Delete buttons.
  * **Customer Management Screen**: Contact management, search by name/phone/email, Add/Edit modal dialogs.
  * **Role-Based UI Adaptations**: UI buttons adapt dynamically according to the logged-in role's permissions with informative error alerts.
* 👉 **[Read the Full C# WPF Application Documentation & Build Guide](C#_WPF_Application/README.md)**

---

## 🛡️ Role-Based Access Control (RBAC) Matrix

| Resource & Action | HTTP Method & URI | Owner | Manager | Cashier | Assessment Rule |
|---|---|:---:|:---:|:---:|---|
| **View Items** | `GET /api/items` | ✅ | ✅ | ✅ | All roles can view |
| **Create Item** | `POST /api/items` | ✅ | ✅ | ❌ *(403)* | Cashier cannot add new items |
| **Update Item** | `PUT/PATCH /api/items/{id}` | ✅ | ✅ | ✅ | Cashier can update existing items (e.g. stock) |
| **Delete Item** | `DELETE /api/items/{id}` | ✅ | ❌ *(403)* | ❌ *(403)* | Only Owner can delete items |
| **View Customers** | `GET /api/customers` | ✅ | ✅ | ✅ | All roles can view |
| **Create Customer** | `POST /api/customers` | ✅ | ✅ | ✅ | All roles can register customers |
| **Update Customer** | `PUT/PATCH /api/customers/{id}` | ✅ | ✅ | ✅ | All roles can edit customers |
| **Delete Customer** | `DELETE /api/customers/{id}` | ✅ | ❌ *(403)* | ❌ *(403)* | Only Owner can delete customers |

---

## 🔑 Pre-Seeded Evaluation Credentials

The database seeder pre-populates three test users with known credentials:

| Role | Email Address | Password | Permissions Summary |
|---|---|---|---|
| **Owner** | `owner@delivergate.com` | `Password123!` | Full control over inventory items and customer records (CRUD). |
| **Manager** | `manager@delivergate.com` | `Password123!` | Can add, view, and edit items and customers; **cannot delete** either. |
| **Cashier** | `cashier@delivergate.com` | `Password123!` | Can view and edit items (adjust stock), can add/view/edit customers; **cannot add/delete items, cannot delete customers**. |

---

## ⚡ Quick Start: Running the Full Stack

### 1. Start the Laravel Backend
```bash
cd Laravel_Backend

# Install dependencies and setup environment
composer install
cp .env.example .env
php artisan key:generate

# Run migrations and seed data
php artisan migrate --seed

# Start the API server
php artisan serve
```
*API will be running at `http://127.0.0.1:8000/api`.*

### 2. Run the C# WPF Application
In a separate terminal:
```bash
cd C#_WPF_Application

# Build and run desktop app
dotnet build
dotnet run
```

---

## 🧪 Running Automated Tests

Run the backend feature test suite:
```bash
cd Laravel_Backend
php artisan test
```

Expected result:
```text
Tests:    29 passed (103 assertions)
Duration: 1.48s
```

---

## 📦 Deliverables Checklist

Per Section 10 of the assessment instructions:
- [x] **Source Code**: Public GitHub repository structure with clean commit history.
- [x] **Postman API Collection**: Included at [`docs/Delivergate_Ice_Cream_Shop_API.postman_collection.json`](docs/Delivergate_Ice_Cream_Shop_API.postman_collection.json).
- [x] **ER Diagram**: Included at [`docs/er-diagram.svg`](docs/er-diagram.svg) and [`docs/er-diagram.md`](docs/er-diagram.md).
- [x] **C# WPF Application**: Fully implemented under `C#_WPF_Application/`.
