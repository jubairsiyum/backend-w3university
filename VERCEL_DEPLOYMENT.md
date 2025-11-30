# Vercel Deployment Guide - FIXED ✅

## 🎉 Solution Applied

The HTTP 500 "bootstrap/cache directory must be writable" error has been fixed!

**What was wrong:** Vercel's filesystem is read-only, but Laravel needs writable cache directories.

**Solution:** Updated Laravel to use `/tmp` directory which is writable on Vercel.

---

## 🚀 Quick Deploy Steps

### 1. Commit and Push Changes

```bash
git add .
git commit -m "Fix Vercel deployment - use /tmp for cache"
git push
```

### 2. Verify Deployment

After Vercel redeploys, test:
```
https://backend-w3university.vercel.app/api/blogs/popular
```

---

## 🔧 Environment Variables in Vercel Dashboard

Go to: **Vercel Dashboard → Settings → Environment Variables**

Add these if not already added:

```
VERCEL=1
APP_KEY=base64:ivzQgIx+mUnQjupf+ln0ff4IQiWfTday4VpfV9udstA=
APP_ENV=production
APP_DEBUG=false
APP_URL=https://backend-w3university.vercel.app
APP_STORAGE=/tmp/storage
VIEW_COMPILED_PATH=/tmp/storage/framework/views

FRONTEND_URL=https://w3u.vercel.app
SANCTUM_STATEFUL_DOMAINS=w3u.vercel.app

LOG_CHANNEL=stderr
CACHE_DRIVER=array
SESSION_DRIVER=array
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
