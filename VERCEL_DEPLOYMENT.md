# Vercel Deployment Guide

## Current Issue & Solution

You're getting HTTP 500 error because of missing or incorrect environment variables in Vercel.

## 🔧 Fix Steps

### 1. Set Environment Variables in Vercel Dashboard

Go to your Vercel project → **Settings** → **Environment Variables** and add these:

**Required Variables:**
```
APP_NAME=ekusheyCoding
APP_ENV=production
APP_KEY=base64:ivzQgIx+mUnQjupf+ln0ff4IQiWfTday4VpfV9udstA=
APP_DEBUG=true
APP_URL=https://backend-w3university.vercel.app

LOG_CHANNEL=stderr
LOG_LEVEL=debug

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

**For Database (if using):**

Since Vercel is serverless, you need a **hosted database**. Options:

- **PlanetScale** (MySQL - Free tier available)
- **Railway** (MySQL/PostgreSQL)
- **Neon** (PostgreSQL - Serverless)
- **Supabase** (PostgreSQL)

After setting up a hosted database, add these variables:
```
DB_CONNECTION=mysql
DB_HOST=your-database-host.com
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

### 2. Update CORS for Frontend

Add your frontend URL:
```
FRONTEND_URL=https://your-nextjs-app.vercel.app
SANCTUM_STATEFUL_DOMAINS=your-nextjs-app.vercel.app
```

### 3. Redeploy

After adding environment variables:
1. Go to **Deployments** tab
2. Click on the latest deployment
3. Click **"Redeploy"**

OR push a new commit:
```bash
git add .
git commit -m "Update Vercel configuration"
git push
```

---

## 📋 Testing Endpoints

Once deployed successfully, test these URLs:

```
https://backend-w3university.vercel.app/
https://backend-w3university.vercel.app/api/blogs
https://backend-w3university.vercel.app/api/blogs/popular
```

---

## ⚠️ Important Notes for Vercel Deployment

1. **No Local Database:** Vercel is serverless - you MUST use a hosted database service
2. **File Storage:** Use cloud storage (S3, Cloudinary) instead of local storage
3. **Sessions:** Use `array` or `cookie` driver (not `database` or `file`)
4. **Cache:** Use `array` driver for cache
5. **Logs:** Use `stderr` channel so logs appear in Vercel dashboard

---

## 🗄️ Recommended Database Setup (PlanetScale - Free)

1. Go to [planetscale.com](https://planetscale.com)
2. Create a free account
3. Create a new database
4. Get connection details
5. Add to Vercel environment variables:

```
DB_CONNECTION=mysql
DB_HOST=aws.connect.psdb.cloud
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-username
DB_PASSWORD=your-password
DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt
```

6. Run migrations from local:
```bash
php artisan migrate --force
php artisan db:seed --class=BlogSeeder --force
```

---

## 🔍 Debugging

If you still get errors, check Vercel logs:
1. Go to your deployment
2. Click **"View Function Logs"**
3. Look for PHP errors

Common issues:
- Missing `APP_KEY`
- Wrong database credentials
- File permission issues
- Missing composer dependencies

---

## ✅ Success Checklist

- [ ] All environment variables added to Vercel
- [ ] Database hosted and connected
- [ ] Frontend URL updated in CORS settings
- [ ] Redeployed after changes
- [ ] Test API endpoints working

---

## 🚀 Quick Test

Try this in your browser after deployment:
```
https://backend-w3university.vercel.app/api/blogs/popular
```

You should see JSON response with blog data.

If you see `{"message":"SQLSTATE[HY000] [2002] Connection refused"}`, it means database isn't configured yet.
