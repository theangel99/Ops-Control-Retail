# OpsControl - File Index

## Documentation Files

| File | Purpose |
|------|---------|
| `README.md` | Main documentation for B2B buyers |
| `QUICK_START.md` | Fast startup guide |
| `DEPLOYMENT.md` | Production deployment instructions |
| `PROJECT_SUMMARY.md` | Complete project overview |
| `FILE_INDEX.md` | This file - index of all files |

---

## Configuration Files

| File | Purpose |
|------|---------|
| `docker-compose.yml` | Docker orchestration |
| `.gitignore` | Git ignore rules |
| `Makefile` | Developer commands |
| `start.sh` | Interactive startup script |

---

## Backend Files (Laravel)

### Core Application

| File | Purpose |
|------|---------|
| `backend/app/Models/Location.php` | Location model |
| `backend/app/Models/Supplier.php` | Supplier model |
| `backend/app/Models/Product.php` | Product model |
| `backend/app/Models/Inventory.php` | Inventory model |
| `backend/app/Models/SalesTransaction.php` | Sales transaction model |
| `backend/app/Models/PurchaseOrder.php` | PO model |
| `backend/app/Models/PurchaseOrderLine.php` | PO line model |
| `backend/app/Models/CashSettings.php` | Cash settings model |
| `backend/app/Models/CashEvent.php` | Cash event model |

### Services (Business Logic)

| File | Purpose |
|------|---------|
| `backend/app/Services/InventoryAnalyticsService.php` | Inventory calculations |
| `backend/app/Services/CashForecastService.php` | Cash projections |
| `backend/app/Services/DashboardService.php` | Dashboard KPIs |

### Controllers (API)

| File | Purpose |
|------|---------|
| `backend/app/Http/Controllers/DashboardController.php` | Dashboard endpoint |
| `backend/app/Http/Controllers/InventoryController.php` | Inventory endpoint |
| `backend/app/Http/Controllers/PurchaseOrderController.php` | PO endpoints |
| `backend/app/Http/Controllers/CashForecastController.php` | Cash endpoint |
| `backend/app/Http/Controllers/AdminController.php` | Admin endpoint |

### Database

| File | Purpose |
|------|---------|
| `backend/database/migrations/2024_01_01_000001_create_locations_table.php` | Locations schema |
| `backend/database/migrations/2024_01_01_000002_create_suppliers_table.php` | Suppliers schema |
| `backend/database/migrations/2024_01_01_000003_create_products_table.php` | Products schema |
| `backend/database/migrations/2024_01_01_000004_create_inventory_table.php` | Inventory schema |
| `backend/database/migrations/2024_01_01_000005_create_sales_transactions_table.php` | Sales schema |
| `backend/database/migrations/2024_01_01_000006_create_purchase_orders_table.php` | PO schema |
| `backend/database/migrations/2024_01_01_000007_create_purchase_order_lines_table.php` | PO lines schema |
| `backend/database/migrations/2024_01_01_000008_create_cash_settings_table.php` | Cash settings schema |
| `backend/database/migrations/2024_01_01_000009_create_cash_events_table.php` | Cash events schema |

### Seeders

| File | Purpose |
|------|---------|
| `backend/database/seeders/DatabaseSeeder.php` | Seeder orchestrator |
| `backend/database/seeders/LocationSeeder.php` | 9 locations |
| `backend/database/seeders/SupplierSeeder.php` | 3 suppliers |
| `backend/database/seeders/ProductSeeder.php` | 1,200 SKUs |
| `backend/database/seeders/CashSettingsSeeder.php` | Cash config |
| `backend/database/seeders/InventorySeeder.php` | Inventory levels |
| `backend/database/seeders/SalesTransactionSeeder.php` | 12 months sales |

### Routes

| File | Purpose |
|------|---------|
| `backend/routes/api.php` | All API endpoints |
| `backend/routes/web.php` | Web routes (minimal) |
| `backend/routes/console.php` | Artisan commands |

### Config

| File | Purpose |
|------|---------|
| `backend/.env.example` | Environment template |
| `backend/bootstrap/app.php` | App bootstrap with CORS |

---

## Frontend Files (Vue 3)

### Views (Main Pages)

| File | Purpose |
|------|---------|
| `frontend/src/views/DashboardView.vue` | Executive dashboard |
| `frontend/src/views/InventoryView.vue` | Inventory control panel |
| `frontend/src/views/PurchaseOrderView.vue` | PO workflow |

### Stores (Pinia State)

| File | Purpose |
|------|---------|
| `frontend/src/stores/app.js` | Global app state |
| `frontend/src/stores/dashboard.js` | Dashboard state |
| `frontend/src/stores/inventory.js` | Inventory state |
| `frontend/src/stores/purchaseOrders.js` | PO state |

### API Client

| File | Purpose |
|------|---------|
| `frontend/src/api/client.js` | Axios HTTP client |
| `frontend/src/api/index.js` | API methods |

### Core App

| File | Purpose |
|------|---------|
| `frontend/src/App.vue` | Root component with nav |
| `frontend/src/main.js` | App entry point |
| `frontend/src/router/index.js` | Vue Router config |
| `frontend/src/style.css` | Global styles |

### Config

| File | Purpose |
|------|---------|
| `frontend/.env.example` | Environment template |
| `frontend/package.json` | NPM dependencies |
| `frontend/vite.config.js` | Vite config |
| `frontend/netlify.toml` | Netlify deploy config |

---

## Docker Files

### Dockerfiles

| File | Purpose |
|------|---------|
| `docker/php/Dockerfile` | PHP 8.2-FPM container |
| `docker/node/Dockerfile` | Node 20 container |
| `docker/nginx/default.conf` | Nginx reverse proxy config |

---

## File Count Summary

| Category | Count |
|----------|-------|
| Models | 9 |
| Services | 3 |
| Controllers | 5 |
| Migrations | 9 |
| Seeders | 6 |
| Views (Vue) | 3 |
| Stores (Pinia) | 4 |
| Documentation | 5 |
| Docker configs | 4 |
| **Total Core Files** | **~48** |

---

## Key Files for Understanding the System

### Start Here (In Order)

1. **README.md** - Understand the business problem
2. **PROJECT_SUMMARY.md** - See what's built
3. **QUICK_START.md** - Run the demo
4. **backend/app/Services/InventoryAnalyticsService.php** - See the logic
5. **frontend/src/views/DashboardView.vue** - See the UI
6. **DEPLOYMENT.md** - Deploy to production

### Business Logic Deep Dive

1. `InventoryAnalyticsService.php` - All calculations
2. `CashForecastService.php` - Cash projections
3. `DashboardService.php` - KPI aggregation

### Frontend Architecture

1. `App.vue` - Layout and navigation
2. `router/index.js` - Routing setup
3. `stores/` - State management
4. `views/` - Main pages

### Database Schema

1. `database/migrations/` - All table definitions
2. `database/seeders/` - Demo data generation

---

## Files NOT to Edit (Auto-Generated)

- `backend/vendor/` - Composer dependencies
- `frontend/node_modules/` - NPM dependencies
- `frontend/dist/` - Production build output
- `backend/bootstrap/cache/` - Laravel cache
- `backend/storage/` - Laravel storage

---

## Customization Points

### Add New SKU Categories
Edit: `backend/database/seeders/ProductSeeder.php`

### Change Calculation Logic
Edit: `backend/app/Services/InventoryAnalyticsService.php`

### Adjust UI Styling
Edit: Vue component `<style>` sections

### Add New API Endpoints
1. Add route to `backend/routes/api.php`
2. Create controller method
3. Add API client method to `frontend/src/api/index.js`
4. Call from Pinia store

---

## Most Important Files (Top 10)

1. `README.md` - Start here
2. `docker-compose.yml` - Run everything
3. `backend/app/Services/InventoryAnalyticsService.php` - Core logic
4. `backend/database/seeders/SalesTransactionSeeder.php` - Demo data
5. `frontend/src/views/DashboardView.vue` - Main UI
6. `frontend/src/stores/dashboard.js` - Dashboard state
7. `backend/routes/api.php` - All endpoints
8. `frontend/src/App.vue` - App layout
9. `DEPLOYMENT.md` - Go to production
10. `QUICK_START.md` - Fast demo

---

**Total Project Size:** ~50 core application files (excluding dependencies)

**Lines of Code:** ~5,000 (excluding vendor/node_modules)

**Technologies:** Laravel 11, Vue 3, MySQL 8, Docker, Chart.js

**Purpose:** Portfolio demo for B2B operational intelligence
