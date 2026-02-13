# OpsControl - Quick Start Guide

## TL;DR

```bash
./start.sh
```

Then open http://localhost:5173

---

## Requirements

- Docker Desktop
- Ports 3306, 5173, 8000 available

---

## Startup Options

### Option 1: Interactive Script
```bash
./start.sh
```

### Option 2: Docker Compose
```bash
docker-compose up -d
```

### Option 3: Makefile
```bash
make up
```

---

## Access Points

- **Frontend UI:** http://localhost:5173
- **Backend API:** http://localhost:8000/api
- **API Health:** http://localhost:8000/up

---

## Common Tasks

### View Logs
```bash
docker-compose logs -f
# or
make logs
```

### Reset Demo Data
```bash
docker-compose exec backend php artisan migrate:fresh --seed --force
# or
make reset
# or use "Reset Demo Data" button in UI
```

### Stop Services
```bash
docker-compose down
# or
make down
```

### Restart Services
```bash
docker-compose restart
# or
make restart
```

### Clean Everything
```bash
docker-compose down -v
# or
make clean
```

---

## Testing the 4 Executive Questions

### 1. Where are we going to stock out?
1. Go to **Dashboard**
2. Check **Stockout Risk Count** KPI
3. Scroll to **Highest Stockout Risk SKUs** table
4. See products like "HV-001" (Premium Wireless Headphones)

### 2. Where is capital trapped in dead inventory?
1. Go to **Dashboard**
2. Check **Dead Stock Value** KPI
3. Scroll to **Largest Dead Stock Exposure** table
4. See products like "DS-001" (VHS Player Retro)

### 3. What do we need to reorder this week?
1. Go to **Inventory Control Panel**
2. Click **Stockout Risk** filter
3. Review **Suggested Qty** column
4. Select items to reorder
5. Click **Generate PO for Selected**

### 4. What happens to cash if we do?
1. Go to **Purchase Workflow**
2. Select a **Draft** PO
3. View **Cash Impact Preview** section
4. See projected cash at 30/60/90 days
5. Click **Submit** → **Approve** → **Mark as Ordered**
6. Cash forecast updates automatically

---

## Demo Data Overview

- **9 locations** (8 stores + 1 warehouse)
- **1,200 SKUs**
- **3 suppliers**
- **12 months** of sales history
- **~3.5 million** sales transactions

### Problematic SKUs (intentional):
- **HV-001 to HV-010:** High velocity, stockout risk
- **DS-001 to DS-020:** Dead stock, aged inventory
- **LM-001 to LM-005:** Low margin, misleading revenue

---

## UI Navigation

### Top Bar
- **Role:** Switch between Executive / Ops Manager (presentation only)
- **Location:** Filter data by location or view "All Locations"
- **Reset Demo Data:** Re-seed database with fresh data

### Main Menu
- **Dashboard:** Executive KPIs and charts
- **Inventory Control:** Detailed table with analytics
- **Purchase Workflow:** PO pipeline and cash impact

---

## Troubleshooting

### Containers won't start
```bash
docker-compose down -v
docker-compose up -d
```

### Frontend shows blank page
- Check http://localhost:5173 in browser
- Open browser console for errors
- Verify containers are running: `docker ps`

### Backend API not responding
```bash
docker-compose logs backend
```

### Database connection error
```bash
docker-compose restart mysql
docker-compose restart backend
```

### Reset everything
```bash
make clean
make up
```

---

## First-Time Setup (Automatic)

When you run `docker-compose up`, it automatically:
1. Pulls Docker images
2. Builds custom PHP/Node containers
3. Installs Composer dependencies
4. Installs NPM dependencies
5. Generates Laravel app key
6. Runs database migrations
7. Seeds 12 months of demo data
8. Starts all services

**This takes ~60 seconds on first run.**

---

## Keyboard Shortcuts

None. Use mouse/trackpad to interact with UI.

---

## Browser Compatibility

Tested on:
- Chrome/Chromium
- Firefox
- Safari
- Edge

---

## Next Steps

1. Explore the Dashboard
2. Filter Inventory by flags
3. Generate a Purchase Order
4. View cash impact
5. Read full README.md for business context
6. Check DEPLOYMENT.md for production setup

---

## Need Help?

1. Check logs: `make logs`
2. Review error messages
3. Restart services: `make restart`
4. Reset data: `make reset`
5. Read README.md for details

---

**You're ready to demo OpsControl!**

Open http://localhost:5173 and start exploring.
