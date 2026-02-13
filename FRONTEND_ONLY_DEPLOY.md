# Frontend-Only Deployment (No Backend Required)

Deploy OpsControl frontend to Netlify **100% FREE** with mock data.

---

## Quick Deploy (3 Steps)

### Step 1: Build the Frontend

```bash
cd frontend
npm install
npm run build
```

This creates a `dist` folder with your static site.

### Step 2: Deploy to Netlify

**Option A: Drag & Drop (Easiest)**

1. Go to https://app.netlify.com/drop
2. Drag the `frontend/dist` folder onto the page
3. Done! You get a live URL instantly

**Option B: Netlify CLI**

```bash
# Install Netlify CLI
npm install -g netlify-cli

# Login
netlify login

# Deploy
cd frontend
netlify deploy --prod --dir=dist
```

### Step 3: Configure Environment

In Netlify dashboard:
1. Go to **Site settings** → **Environment variables**
2. Add variable:
   - Key: `VITE_USE_MOCK`
   - Value: `true`
3. Click **Trigger deploy** to rebuild

---

## What Works Without Backend

✅ **All 3 views fully functional:**
- Executive Dashboard with real-looking data
- Inventory Control Panel with filtering
- Purchase Workflow with status transitions

✅ **Interactive features:**
- Role switcher
- Location filter
- Flag filters (stockout, dead stock, low margin)
- PO generation (simulated)
- Status transitions (simulated)

✅ **Data shown:**
- 12-month revenue trend
- KPI metrics
- Sample SKUs with analytics
- Purchase orders in all states
- Cash projections

⚠️ **What doesn't work:**
- "Reset Demo Data" button (no backend to reset)
- Real-time calculations
- Data persistence (refreshes to mock data)

---

## Cost: $0/month

Netlify Free Tier includes:
- 100GB bandwidth/month
- Unlimited sites
- Automatic HTTPS
- Global CDN
- Custom domain support

Perfect for portfolio demos!

---

## Complete Build & Deploy Script

```bash
#!/bin/bash

# Navigate to frontend
cd frontend

# Install dependencies
npm install

# Build for production
npm run build

# Deploy to Netlify
netlify deploy --prod --dir=dist

echo "✅ Deployed! Your site is live."
```

Save as `deploy.sh`, make executable with `chmod +x deploy.sh`, then run `./deploy.sh`

---

## Custom Domain (Optional)

1. In Netlify dashboard: **Domain settings** → **Add custom domain**
2. Follow DNS instructions
3. HTTPS is automatic

Example: `opscontrol.yourname.com`

---

## GitHub + Auto-Deploy (Optional)

For automatic deploys on every push:

1. **Push to GitHub:**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin https://github.com/yourusername/opscontrol.git
   git push -u origin main
   ```

2. **Connect Netlify to GitHub:**
   - In Netlify: "Add new site" → "Import an existing project"
   - Connect to your repo
   - Build settings:
     - Base directory: `frontend`
     - Build command: `npm run build`
     - Publish directory: `frontend/dist`
   - Environment variables:
     - `VITE_USE_MOCK` = `true`

3. **Auto-deploy:**
   - Every push to `main` triggers a new deploy
   - Pull requests get preview URLs

---

## Testing Locally First

Before deploying, test the build locally:

```bash
cd frontend
npm install
npm run build
npm run preview
```

Open http://localhost:4173 - this is exactly what will be deployed.

---

## Troubleshooting

### Build fails with module errors
```bash
cd frontend
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Site shows blank page
- Check browser console for errors
- Verify `VITE_USE_MOCK=true` is set in Netlify
- Rebuild site after adding env variables

### Charts don't render
- Clear browser cache
- Check if Chart.js is in dependencies: `npm list chart.js`
- Rebuild if needed

### 404 on page refresh
- This is fixed by `netlify.toml` redirects (already configured)
- If missing, add to `frontend/netlify.toml`:
  ```toml
  [[redirects]]
    from = "/*"
    to = "/index.html"
    status = 200
  ```

---

## Mock Data Configuration

The mock data is in `frontend/src/api/mockData.js`

**To customize:**

1. Edit the mock data values
2. Rebuild: `npm run build`
3. Redeploy to Netlify

**Example - Change revenue:**
```javascript
// In mockData.js
revenue_30d: 500000.00,  // Change this
```

---

## Converting to Real Backend Later

When you want to add a real backend:

1. **Deploy backend** (Railway/Render/Fly.io)
2. **Update Netlify env variable:**
   - Change `VITE_USE_MOCK` to `false`
   - Add `VITE_API_URL` = `https://your-backend.railway.app/api`
3. **Trigger redeploy**

No code changes needed! The app automatically switches.

---

## Portfolio Presentation Tips

When showing this demo:

1. **Open the live URL** from any device
2. **Walk through the 4 questions:**
   - "Where are we going to stock out?" → Dashboard stockout KPI
   - "Where is capital trapped?" → Dead stock value
   - "What to reorder?" → Inventory panel + PO generation
   - "What happens to cash?" → Purchase workflow cash impact

3. **Show interactivity:**
   - Switch roles
   - Filter by flags
   - Generate a PO
   - Transition PO status

4. **Mention the architecture:**
   - "This is the Vue 3 frontend"
   - "In production, it talks to a Laravel API"
   - "For demo purposes, it's using mock data"
   - "Same UI, same UX, just without server costs"

---

## Example Live URLs

After deploying, you'll get URLs like:
- https://opscontrol-demo.netlify.app
- https://your-site-name.netlify.app

Share this in your portfolio, resume, LinkedIn!

---

## Next Steps

1. ✅ Build: `npm run build`
2. ✅ Deploy: Drag `dist/` to Netlify
3. ✅ Set `VITE_USE_MOCK=true`
4. ✅ Share your live URL!

**Total time: 5 minutes**

**Total cost: $0**

---

## Questions?

- Build errors? Check Node version: `node -v` (need v18+)
- Deploy issues? Check Netlify build logs
- Want real backend? See `DEPLOYMENT.md`

**You now have a fully functional portfolio demo with zero hosting costs!**
