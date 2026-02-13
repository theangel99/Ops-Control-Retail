# OpsControl

**Internal Operational Control System for Multi-Location Retail & Distribution**

A portfolio demo built for B2B decision-makers (Founder/COO/CFO) that showcases operational intelligence, process automation, and decision support capabilities.

---

## What OpsControl Solves

Most retail/distribution companies operate with fragmented systems: sales data in one place, inventory in another, purchasing disconnected from cash flow. Executives can't answer critical questions in real-time:

- **Where are we going to stock out?**
- **Where is capital trapped in dead inventory?**
- **What do we need to reorder this week?**
- **What happens to cash if we do?**

OpsControl consolidates operational data and applies deterministic business logic to answer these questions in under 30 seconds.

---

## What Decisions It Enables

### 1. Inventory Risk Mitigation
Automatically flags SKUs at risk of stockout based on:
- Current stock levels
- Sales velocity (30-day rolling)
- Supplier lead times (60 days default)
- Safety stock requirements (14-21 days)

**Result:** Prevent lost sales before they happen.

### 2. Capital Optimization
Identifies dead stock based on:
- Inventory age (>120 days)
- Low sales velocity (<0.05 units/day)
- Current on-hand value

**Result:** Free up trapped capital and reduce carrying costs.

### 3. Intelligent Reordering
Calculates suggested purchase quantities using:
- Historical velocity patterns
- Lead time + 30-day buffer coverage
- Current on-hand and on-order units

**Result:** Order the right amount at the right time.

### 4. Cash Flow Forecasting
Projects cash position at 30/60/90 days by modeling:
- Revenue collection delays (14 days)
- Purchase order payment terms (Net 30)
- Approved PO commitments

**Result:** Know your cash position before committing to purchases.

---

## How the Automation Works

### Business Logic (Server-Side, Single Source of Truth)

#### Sales Velocity
```
velocity = total_units_sold_last_30_days / 30
```

#### Days on Hand (DOH)
```
DOH = current_on_hand / velocity
```

#### Reorder Point
```
reorder_point = velocity × (lead_time_days + safety_stock_days)
```

#### Suggested Reorder Quantity
```
target_inventory = velocity × (lead_time + 30_day_buffer)
suggested_qty = max(0, target_inventory - on_hand - on_order)
```

#### Stockout Risk Flag
- **Critical:** DOH < lead_time_days
- **Warning:** DOH < (lead_time + safety_stock)

#### Dead Stock Flag
- Inventory age > 120 days **AND** velocity < 0.05 units/day

#### Cash Forecast
- **Inflows:** Revenue collections delayed by 14 days
- **Outflows:** Approved POs due Net 30 from order date
- Projects running balance over 30/60/90-day windows

---

## Domain Model (Included Demo Data)

### Company Profile
- **8 retail locations** + **1 central warehouse**
- **1,200 SKUs** across 8 categories
- **3 suppliers** (one with intermittent delays)
- **Lead time:** 60 days default
- **Payment terms:** Net 30
- **12 months** of sales history with seasonal patterns

### Problematic SKUs (Intentional)
- **10 high-velocity SKUs:** Low stock, stockout risk
- **20 dead-stock SKUs:** Slow-moving, capital trapped
- **5 low-margin SKUs:** High revenue but poor margin contribution

This creates realistic scenarios that demonstrate the system's decision support capabilities.

---

## Tech Stack

### Backend
- **Laravel 11** (PHP 8.2+)
- **MySQL 8.0**
- REST API with deterministic seeders
- Business logic in dedicated service classes

### Frontend
- **Vue 3** (Composition API)
- **Pinia** for state management
- **Vue Router** for navigation
- **Chart.js** for visualizations
- **Vite** for build tooling

### Infrastructure
- **Docker Compose** for local environment
- **Nginx** reverse proxy
- **PHP-FPM** application server

---

## Quick Start (Docker)

### Prerequisites
- Docker Desktop installed
- Ports 3306, 5173, 8000 available

### Run the Application

```bash
# Clone or extract the project
cd OpsControl-DEMO

# Start all services
docker-compose up -d

# Wait ~30 seconds for database initialization
# Frontend: http://localhost:5173
# Backend API: http://localhost:8000/api
```

That's it. The system will:
1. Build containers
2. Install dependencies
3. Run migrations
4. Seed 12 months of demo data
5. Start the application

---

## Using the Demo

### Role Switcher
Toggle between **Executive** and **Ops Manager** views (no functional difference—designed for presentation).

### Location Filter
Filter data by specific locations or view aggregated "All Locations" data.

### Reset Demo Data
Click "Reset Demo Data" to re-seed the database with fresh deterministic data.

---

## Three Core Views

### 1. Executive Dashboard
- **KPI tiles:** Revenue, margin, cash position, risk counts
- **Charts:** 12-month revenue trend, stockout risk breakdown
- **Top lists:**
  - Highest stockout risk SKUs
  - Largest dead stock exposure
  - Projected cash low-water mark

**Purpose:** Answer the 4 executive questions in <30 seconds.

### 2. Inventory Control Panel
- **Table view:** All SKUs with computed analytics
  - Sales velocity, DOH, reorder point, suggested qty
  - Margin %, flags (stockout, dead stock, low margin)
- **Filters:** By location, supplier, risk flags
- **Action:** Select SKUs and generate suggested POs

**Purpose:** Operational command center for inventory decisions.

### 3. Purchase Workflow
- **PO pipeline:** Draft → Submitted → Approved → Ordered → Received
- **PO details:** Line items, cash impact preview
- **Workflow actions:** Transition POs through states
- **Cash impact:** See projected cash after PO approval

**Purpose:** Model cash consequences before committing capital.

---

## API Endpoints

### Dashboard
```
GET /api/dashboard?location_id={id}
```

### Inventory
```
GET /api/inventory?location_id={id}&supplier_id={id}&flags=stockout,dead_stock
```

### Purchase Orders
```
GET /api/po
GET /api/po/{id}
POST /api/po/suggest { location_id, product_ids[] }
POST /api/po/{id}/transition { next_status }
```

### Cash Forecast
```
GET /api/cash/forecast
```

### Admin
```
POST /api/admin/reset
```

### Reference Data
```
GET /api/locations
GET /api/suppliers
```

---

## Deployment (Production)

### Backend (Laravel API)
Deploy to any PHP hosting:
- Laravel Forge
- AWS Elastic Beanstalk
- DigitalOcean App Platform

Configure:
- MySQL database
- Environment variables from `.env.example`
- Run `php artisan migrate --seed`

### Frontend (Vue SPA)

#### Build for production:
```bash
cd frontend
npm run build
```

This creates `dist/` folder with static assets.

#### Deploy to Netlify:
1. Create new site in Netlify
2. Link to Git repository or drag-and-drop `dist/` folder
3. Set build command: `npm run build`
4. Set publish directory: `dist`
5. Add environment variable: `VITE_API_URL=https://your-api.com/api`

The frontend is a static SPA—no server required.

---

## Project Structure

```
OpsControl-DEMO/
├── backend/
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── Services/          # Business logic
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/           # Deterministic demo data
│   └── routes/api.php
├── frontend/
│   ├── src/
│   │   ├── api/              # API client
│   │   ├── components/
│   │   ├── stores/           # Pinia state
│   │   ├── views/            # 3 main views
│   │   └── router/
│   └── package.json
├── docker/
│   ├── nginx/
│   ├── php/
│   └── node/
└── docker-compose.yml
```

---

## Business Narrative (For Buyers)

**Before OpsControl:**
- Inventory managers rely on spreadsheets and gut feel
- Stockouts discovered after lost sales
- Dead stock identified months too late
- Cash crunches surprise the CFO

**After OpsControl:**
- Automated risk flagging prevents stockouts
- Dead stock identified immediately with value quantification
- Reorder suggestions based on real velocity + lead times
- Cash impact visible before PO approval

**ROI Drivers:**
- Reduced stockouts → captured sales
- Optimized inventory → freed capital
- Automated reordering → reduced labor
- Cash visibility → better financing decisions

---

## Design Principles

### No Clutter
- No marketing copy
- Business terms, not tech jargon
- Calm, readable UI

### Real Logic
- No fake "AI insights"
- Deterministic calculations
- Traceable business rules

### Decision Focus
- Every view answers a question
- Every metric drives an action
- No vanity metrics

---

## Limitations (Demo Scope)

This is a portfolio demo, not production software. It does not include:
- User authentication (simplified role switcher)
- Multi-tenancy
- Audit trails
- Advanced permissions
- External integrations
- Real-time sync

For a production deployment, these would be implemented.

---

## Support

This is a demo project. For questions or issues:
- Review code comments
- Check API endpoint responses
- Use "Reset Demo Data" to restore state

---

## License

Demo project for portfolio purposes. Not licensed for production use.

---

**Built to demonstrate operational intelligence for decision-makers who need answers, not dashboards.**
# Ops-Control-Retail-
