# Deploy to Netlify - Quick Reference

## 🚀 Fastest Way (3 Steps)

```bash
# 1. Build
npm install
npm run build

# 2. Deploy
# Go to: https://app.netlify.com/drop
# Drag the 'dist' folder

# 3. Configure
# In Netlify: Add environment variable
# VITE_USE_MOCK = true
```

**Done! Your site is live.**

---

## 📋 Deploy Checklist

- [ ] `npm run build` works
- [ ] `dist` folder created
- [ ] Deployed to Netlify
- [ ] `VITE_USE_MOCK=true` set
- [ ] Site loads correctly
- [ ] All 3 views work

---

## 🔗 Important Files

- `dist/` - Your production build (deploy this)
- `.env` - Local config (not deployed)
- `netlify.toml` - Netlify config (auto-detected)

---

## 💡 Tips

1. **Mock data is already configured** - just set the env var
2. **No backend needed** - frontend works standalone
3. **100% free** - Netlify free tier is generous
4. **Custom domain** - Add later in Netlify settings

---

## 🆘 Having Issues?

See `NETLIFY_DEPLOY_STEPS.md` in project root for detailed guide.
