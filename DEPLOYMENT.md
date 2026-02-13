# OpsControl - Deployment Guide

## Deploying to Netlify (Frontend Only)

### Option 1: Netlify CLI

```bash
# Install Netlify CLI
npm install -g netlify-cli

# Build frontend
cd frontend
npm run build

# Deploy
netlify deploy --prod
```

### Option 2: Netlify UI

1. Go to https://app.netlify.com
2. Click "Add new site" → "Import an existing project"
3. Connect to your Git repository
4. Configure build settings:
   - **Base directory:** `frontend`
   - **Build command:** `npm run build`
   - **Publish directory:** `frontend/dist`
5. Add environment variable:
   - Key: `VITE_API_URL`
   - Value: `https://your-backend-api.com/api`
6. Click "Deploy site"

### Option 3: Drag and Drop

```bash
cd frontend
npm run build
```

Then drag the `dist/` folder to Netlify's deploy drop zone.

---

## Backend Deployment Options

### Option 1: Laravel Forge

1. Create a new server in Forge
2. Add a new site
3. Deploy repository
4. Configure environment variables from `.env.example`
5. Run deployment script:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan migrate --seed --force
   php artisan config:cache
   php artisan route:cache
   ```

### Option 2: DigitalOcean App Platform

1. Create new app
2. Select Laravel as framework
3. Configure:
   - Build command: `composer install --no-dev`
   - Run command: `heroku-php-apache2 public/`
4. Add MySQL database component
5. Set environment variables
6. Deploy

### Option 3: AWS Elastic Beanstalk

1. Install EB CLI
2. Initialize:
   ```bash
   cd backend
   eb init
   ```
3. Create environment:
   ```bash
   eb create opscontrol-api
   ```
4. Configure RDS MySQL database
5. Set environment variables
6. Deploy:
   ```bash
   eb deploy
   ```

---

## Environment Variables

### Backend (.env)

```env
APP_NAME=OpsControl
APP_ENV=production
APP_KEY=base64:...  # Generate with: php artisan key:generate
APP_DEBUG=false
APP_URL=https://your-api-domain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=opscontrol
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

### Frontend (.env)

```env
VITE_API_URL=https://your-api-domain.com/api
```

---

## Post-Deployment Checklist

### Backend
- [ ] Database migrated and seeded
- [ ] CORS configured for frontend domain
- [ ] SSL certificate installed
- [ ] API endpoints returning JSON
- [ ] Test: `curl https://your-api.com/api/dashboard`

### Frontend
- [ ] Built with production API URL
- [ ] Deployed to Netlify
- [ ] Redirects configured for SPA routing
- [ ] Test: Can navigate to all 3 views
- [ ] Test: API calls work from browser

---

## Database Seeding

### First Deploy
```bash
php artisan migrate:fresh --seed --force
```

### Reset Demo Data (Anytime)
```bash
php artisan migrate:fresh --seed --force
```

Or use the "Reset Demo Data" button in the UI (calls `/api/admin/reset`).

---

## Monitoring

### Health Check Endpoints
- Backend: `https://your-api.com/up`
- Frontend: Any route (SPA)

### Log Locations
- Laravel: `storage/logs/laravel.log`
- Nginx: `/var/log/nginx/error.log`

---

## Scaling Considerations

### Database
- Use connection pooling (e.g., PgBouncer for PostgreSQL)
- Index frequently queried columns
- Consider read replicas for heavy traffic

### API
- Use Laravel caching (Redis/Memcached)
- Cache dashboard responses
- Implement API rate limiting

### Frontend
- Already static, served via CDN (Netlify)
- Minimal scaling needed

---

## Security

### Production Best Practices
1. Set `APP_DEBUG=false`
2. Use HTTPS everywhere
3. Configure CORS properly (whitelist frontend domain)
4. Keep dependencies updated
5. Use environment variables, never commit secrets
6. Enable Laravel's CSRF protection for authenticated routes

---

## Troubleshooting

### Frontend can't reach API
- Check `VITE_API_URL` environment variable
- Verify CORS headers on backend
- Check browser console for errors

### Database connection fails
- Verify `DB_*` environment variables
- Check database server is accessible
- Ensure migrations have run

### Blank page on frontend
- Check browser console
- Verify build completed successfully
- Check Netlify build logs

---

## Cost Estimates

### Netlify (Frontend)
- Free tier: 100GB bandwidth, 300 build minutes
- Pro: $19/month (unlimited builds)

### Backend Hosting
- DigitalOcean Droplet: $6-12/month
- Laravel Forge: $19/month + server costs
- AWS: Variable (typically $20-50/month for small apps)

### Database
- DigitalOcean Managed MySQL: $15/month
- AWS RDS: $15-30/month
- PlanetScale: Free tier available

**Total:** ~$30-100/month for production deployment

---

## Support

For deployment issues:
1. Check logs in hosting platform
2. Verify environment variables
3. Test API endpoints manually
4. Check CORS configuration

---

**Deploy once, run anywhere. Frontend and backend are fully decoupled.**
