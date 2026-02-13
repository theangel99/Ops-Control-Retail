# Deploy OpsControl to Netlify - Complete Guide

## ✨ Frontend-Only Deployment (100% Free)

Your OpsControl demo will work **perfectly** with mock data - all features functional, zero backend needed.

---

## 🚀 Quick Steps (5 Minutes)

### 1. Build the Project

```bash
cd frontend
npm install
npm run build
```

✅ This creates `frontend/dist/` folder

### 2. Deploy to Netlify

**🎯 Easiest Method: Drag & Drop**

1. Open browser: https://app.netlify.com/drop
2. Sign up/login (free account)
3. **Drag the `frontend/dist` folder** onto the page
4. Wait 10 seconds
5. ✅ You get a live URL!

### 3. Configure Mock Data

In your new Netlify site:
1. Go to **Site settings** → **Environment variables**
2. Click **Add a variable**
3. Set:
   - **Key:** `VITE_USE_MOCK`
   - **Value:** `true`
4. Click **Save**
5. Click **Deploys** → **Trigger deploy** → **Deploy site**

### 4. Done! 🎉

Your site is live at: `https://[random-name].netlify.app`

---

## 🎬 What Works

### ✅ Fully Functional:
- **Executive Dashboard** with KPIs and charts
- **Inventory Control Panel** with filters
- **Purchase Workflow** with status transitions
- Role switcher (Executive / Ops Manager)
- Location filter
- Flag filters (Stockout, Dead Stock, Low Margin)
- Generate PO button (simulated)
- PO status transitions (simulated)

### 📊 Demo Data Includes:
- 12-month revenue trend
- 23 SKUs at stockout risk
- $125,400 in dead stock
- 4 sample inventory items
- 5 purchase orders in various states
- Cash projections (30/60/90 days)

---

## 💰 Cost Breakdown

| Service | Cost | What You Get |
|---------|------|--------------|
| **Netlify** | **$0** | 100GB bandwidth, HTTPS, CDN, Custom domains |
| **Total** | **$0/month** | ✨ |

---

## 📱 Alternative Deploy Methods

### Method 2: Netlify CLI

```bash
# Install
npm install -g netlify-cli

# Login
netlify login

# Deploy
cd frontend
netlify deploy --prod --dir=dist
```

### Method 3: GitHub Auto-Deploy

```bash
# Push to GitHub
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/yourusername/opscontrol.git
git push -u origin main
```

Then in Netlify:
1. "Add new site" → "Import an existing project"
2. Connect GitHub
3. Select repository
4. Build settings:
   - **Base directory:** `frontend`
   - **Build command:** `npm run build`
   - **Publish directory:** `frontend/dist`
5. Environment variables:
   - `VITE_USE_MOCK` = `true`
6. Deploy!

---

## 🔧 Troubleshooting

### Build fails locally
```bash
cd frontend
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Site shows blank page after deploy
1. Check browser console (F12) for errors
2. In Netlify: Site settings → Environment variables
3. Make sure `VITE_USE_MOCK` = `true` exists
4. Trigger a new deploy

### Charts don't appear
- Hard refresh browser: `Cmd+Shift+R` (Mac) or `Ctrl+Shift+R` (Windows)
- Check if Chart.js loaded: Browser console → Network tab

### 404 error on refresh
- Already fixed by `netlify.toml` configuration
- Verify `frontend/netlify.toml` exists

---

## 🎨 Custom Domain (Optional)

1. Buy domain (Namecheap, Google Domains, etc.)
2. In Netlify: **Domain settings** → **Add custom domain**
3. Update DNS records as instructed
4. HTTPS is automatic

Example: `opscontrol-demo.yourname.com`

---

## 📝 For Your Portfolio

### What to Say:

> "**OpsControl** - Operational intelligence platform for retail/distribution companies.
>
> Built with Vue 3, Pinia, Chart.js frontend. Designed to integrate with Laravel API.
>
> **Live Demo:** [your-netlify-url]
>
> Features:
> - Executive dashboard with KPIs and trend analysis
> - Inventory control with automated risk flagging
> - Purchase workflow with cash impact modeling
> - Business logic: velocity calculations, reorder points, cash forecasting
>
> **Tech:** Vue 3, Pinia, Vue Router, Chart.js, Vite"

### Links to Include:
- 🌐 Live Demo: Your Netlify URL
- 💻 GitHub: Your repository
- 📄 Documentation: Link to README

---

## 🚀 Deploy Command (One-Liner)

```bash
cd frontend && npm install && npm run build && netlify deploy --prod --dir=dist
```

Or use the script:
```bash
./deploy-frontend.sh
```

---

## 📊 What Recruiters/Clients See

When they visit your Netlify URL:

1. **Professional UI** - Clean, business-focused design
2. **Real Functionality** - All buttons work, data updates
3. **Complex Features** - Charts, filtering, workflows
4. **Responsive** - Works on all screen sizes
5. **Fast** - Served via global CDN

They **won't know** it's using mock data unless you tell them!

---

## 🎯 Demo Script (30 seconds)

> "This is OpsControl - it helps retail companies prevent stockouts and identify dead inventory.
>
> [Dashboard] Here we see 23 SKUs at risk of stocking out, and $125k trapped in dead stock.
>
> [Inventory Panel] We can filter by risk flags and see recommended reorder quantities.
>
> [PO Workflow] Before approving a purchase order, we see the cash impact over 90 days.
>
> The frontend is built with Vue 3. In production, it integrates with a Laravel API for real-time calculations."

---

## ✅ Final Checklist

- [ ] Run `npm run build` successfully
- [ ] Deploy to Netlify (drag & drop or CLI)
- [ ] Set `VITE_USE_MOCK=true` environment variable
- [ ] Trigger redeploy after setting env var
- [ ] Test all 3 views in browser
- [ ] Test on mobile device
- [ ] Add to portfolio/resume
- [ ] Share URL on LinkedIn

---

## 🔄 Updating Your Site

Whenever you make changes:

```bash
cd frontend
npm run build
netlify deploy --prod --dir=dist
```

Or if using GitHub auto-deploy, just push:
```bash
git add .
git commit -m "Update UI"
git push
```

Netlify auto-deploys in ~2 minutes.

---

## 📞 Need Help?

### Netlify Support:
- Docs: https://docs.netlify.com
- Forums: https://answers.netlify.com

### Common Issues:
- **Build fails:** Check Node version (need v18+)
- **Blank page:** Set `VITE_USE_MOCK=true`
- **Charts missing:** Hard refresh browser

---

## 🎉 You're Done!

Your OpsControl demo is now:
- ✅ Live on the internet
- ✅ Free forever (Netlify free tier)
- ✅ Fast (global CDN)
- ✅ Professional (HTTPS + custom domain)
- ✅ Portfolio-ready

**Share your URL everywhere! 🚀**

---

**Next Steps:**
1. Test your live site
2. Add to LinkedIn profile
3. Include in resume
4. Show to recruiters/clients
5. Celebrate! 🎊

**Your URL:** After deploy, Netlify shows it at the top of your dashboard.

Example: `https://opscontrol-demo-abc123.netlify.app`
