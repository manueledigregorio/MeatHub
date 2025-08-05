# MeatHub 🥩 – Butchery & Market Management System

**MeatHub** is a Laravel-based management system designed for butcher shops and small markets. The platform offers complete control over product inventory (sold by weight or unit), supplier purchases, point-of-sale operations, warehouse movements, and financial analysis (food cost, gross margins, etc.).

---

## 🎯 Project Objectives

- Simplify the management of sales, inventory, and supplier purchases
- Provide real-time data on margins, stock levels, and product performance
- Offer an intuitive and role-based admin interface using Filament
- Enable operational insights with detailed statistics and reporting

---

## 🧩 Core Features

### 📦 Product Management
- Full CRUD for products with optional image and description
- Categorization by type (e.g., fresh meat, frozen, cured, etc.)
- Sales by weight or unit
- Real-time stock levels
- Automatic average purchase price calculation

### 🏪 Inventory Management
- Product loading (supplier purchases)
- Product unloading (sales and adjustments)
- Historical log of all movements
- Stock level alerts for critical items

### 💰 Sales System
- Quick sale entry for counter operations
- Automatic total calculation and stock deduction
- Order history with detailed lines, timestamps, and operator tracking

### 📉 Food Cost & Margins
- Automated food cost and gross margin calculation
- Configurable margin thresholds with alerts
- Product and category profitability reports

### 🧾 Supplier Management
- Supplier directory with contact info and notes
- Purchase history per product and supplier

### 📊 Statistics & Reports
- Daily / weekly / monthly sales
- Best-selling products
- Low-stock alerts
- Profitability per product or category
- Average food cost per category

### 👤 User Roles & Permissions
- **Admin**: full access
- **Sales**: POS operations only
- **Warehouse**: stock operations only
- Each user sees a custom dashboard tailored to their role

---

## 🛠️ Tech Stack

| Component          | Technology                     |
|--------------------|--------------------------------|
| Backend            | Laravel                        |
| Admin Panel        | Filament                       |
| Database           | MySQL / SQLite (local)         |
| Media Management   | Spatie Media Library           |
| Charts & Reports   | Filament Charts, Laravel Excel |
| Authentication     | Filament Auth / Laravel Breeze |

---

## 🔮 Planned Features

- PDF invoice generation for clients
- Barcode scanning support
- Mobile app for stock management
- Customer-facing frontend for placing orders
- Integration with eCommerce platforms (Shopify, WooCommerce)

---

## ⚙️ Local Development Setup

```bash
git clone https://github.com/manueledigregorio/MeatHub.git
cd MeatHub

composer install
cp .env.example .env
php artisan key:generate

# Configure your database credentials in .env
php artisan migrate
php artisan serve
