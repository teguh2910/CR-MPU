# CR-MPU (Cost Reduction Management System)

A comprehensive cost reduction tracking and reporting system built with Laravel 11 and Filament 3.

## About This Project

CR-MPU is a management system designed to track cost reduction activities, manage part numbers, and generate detailed reports with fiscal year analysis. The system provides advanced reporting capabilities with monthly pivot views and integrates quantity forecasting and budgeting features.

## Features

### Master Data Management
- **Part Numbers**: Manage product part numbers with suppliers, products, and categories
- **Activities**: Track cost reduction activities with CR numbers, CR/Satuan values, and SVP dates
- **Suppliers**: Maintain supplier information and relationships
- **Products**: Product catalog management
- **Categories**: Product categorization

### Quantity Management
- **Quantity Forecasts**: Monthly quantity forecasting with update tracking
- **Quantity Budgets**: Budget quantity planning per month
- **Update Qty Months**: Track monthly quantity updates

### Reporting
- **CR Report**: Comprehensive cost reduction report with:
  - Fiscal year pivot columns (April to March)
  - Budget and forecast amounts per month
  - Filterable by year and update qty month
  - Integrated supplier, product, and activity data
  - Automatic CR/Satuan calculation and formatting

### Data Import
- Excel import functionality for activities with automatic date parsing
- Support for Excel serial dates and formatted date strings
- Validation and error handling

## Tech Stack

- **Framework**: Laravel 11.x
- **Admin Panel**: Filament 3.x
- **Database**: PostgreSQL/MySQL
- **Frontend**: Livewire, Alpine.js, Tailwind CSS
- **Excel Processing**: Maatwebsite/Laravel-Excel

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd CR-MPU
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Set up database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cr_mpu
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations:
```bash
php artisan migrate
```

6. Build assets:
```bash
npm run build
```

7. Start the development server:
```bash
php artisan serve
```

8. Access the admin panel at `http://localhost:8000/admin`

## Database Structure

### Core Tables
- `part_numbers` - Part number master data
- `activities` - Cost reduction activities with CR No
- `suppliers` - Supplier information
- `products` - Product catalog
- `categories` - Product categories
- `qty_forecasts` - Monthly quantity forecasts
- `qty_budgets` - Monthly quantity budgets
- `update_qty_months` - Quantity update tracking

### Key Relationships
- Part Numbers → Activities (One to Many)
- Part Numbers → Suppliers (Many to One)
- Part Numbers → Products (Many to One)
- Part Numbers → Categories (Many to One)
- Part Numbers → Qty Forecasts (One to Many)
- Part Numbers → Qty Budgets (One to Many)

## Fiscal Year

The system operates on a fiscal year basis running from **April to March** (not calendar year).

## Usage

### Managing Activities
1. Navigate to **Master Data → Activities**
2. Create new activity with:
   - Part Number (required)
   - CR No
   - Activity description
   - Year
   - CR/Satuan value
   - Satuan (unit)
   - Plan SVP Month
   - Act SVP Month

### Importing Data
1. Prepare Excel file with columns:
   - part_no, cr_no, activity, year, cr_satuan, satuan, plan_svp_month, act_svp_month
2. Use the import feature in Activities page
3. System will automatically parse dates and validate data

### Generating CR Reports
1. Navigate to **Report → CR**
2. Apply filters:
   - Year (required)
   - Update Qty Month (optional)
3. View report with:
   - Part Number, Activity, CR No, Supplier, Product
   - CR/Satuan, Satuan, Plan SVP, Act SVP
   - Monthly budget and forecast amounts (Apr-Mar)

## Development

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Run Tests
```bash
php artisan test
```

## Contributing

Contributions are welcome! Please follow the standard Laravel contribution guidelines.

## Security

If you discover any security vulnerabilities, please contact the development team immediately.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
