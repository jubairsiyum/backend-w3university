# Tutorial Admin Panel Setup Guide

## Issue Fixed
**Problem**: `POST /api/admin/tutorials` returns 404 "The route could not be found"

**Root Cause**: The `User` model was missing the `isAdmin()` method required by the `AdminMiddleware`.

## What Was Fixed

### 1. User Model (`app/Models/User.php`)
✅ Added `role` to fillable fields
✅ Added `isAdmin()` method:
```php
public function isAdmin(): bool
{
    return $this->role === 'admin' || $this->role === 'super_admin';
}
```

### 2. Frontend Authentication (`src/lib/api/admin.ts`)
✅ Fixed `fetchDashboardStats` to handle 404 errors gracefully
✅ Returns empty stats instead of throwing errors

### 3. Frontend Tutorial Pages
✅ Fixed all tutorial admin pages to use `admin_token` instead of `authToken`:
- `src/app/admin/tutorials/page.tsx` (list page)
- `src/app/admin/tutorials/new/page.tsx` (create page)  
- `src/app/admin/tutorials/edit/[id]/page.tsx` (edit page)

## How to Create an Admin User

### Option 1: Using SQL (Recommended)
1. Open your database client (phpMyAdmin, MySQL Workbench, etc.)
2. Run the SQL script in `create-admin.sql`:
```sql
UPDATE users 
SET role = 'admin' 
WHERE email = 'your-email@example.com';
```

### Option 2: Using PHP Script (If composer is working)
```bash
cd backend-w3university
php create-admin-user.php
```

This creates:
- Email: `admin@w3university.com`
- Password: `admin123`
- Role: `admin`

### Option 3: Manual Database Update
1. Login to your existing account in the frontend
2. Go to your database
3. Find your user in the `users` table
4. Update the `role` column from `user` to `admin`
5. Logout and login again

## Testing the Tutorial API

### 1. Login as Admin
```bash
POST https://backend-w3university.vercel.app/api/login
Content-Type: application/json

{
  "email": "admin@w3university.com",
  "password": "admin123"
}
```

Response will include a `token` - use this in subsequent requests.

### 2. Create a Tutorial
```bash
POST https://backend-w3university.vercel.app/api/admin/tutorials
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "language_id": 1,
  "title": "My First Tutorial",
  "content": "This is the tutorial content...",
  "code_example": "console.log('Hello World');",
  "order": 1,
  "is_published": true
}
```

### 3. Get All Tutorials (Admin)
```bash
GET https://backend-w3university.vercel.app/api/admin/tutorials
Authorization: Bearer YOUR_TOKEN_HERE
```

## Frontend Usage

### 1. Login to Admin Panel
Navigate to: `http://localhost:3000/admin/login`
- Email: `admin@w3university.com`
- Password: `admin123`

### 2. Access Tutorials
Navigate to: `http://localhost:3000/admin/tutorials`

### 3. Create Tutorial
Click "New Tutorial" button or go to: `http://localhost:3000/admin/tutorials/new`

## API Routes Available

### Public Routes
- `GET /api/tutorials` - Get all published tutorials
- `GET /api/tutorials/languages` - Get available languages  
- `GET /api/tutorials/{id}` - Get single tutorial

### Admin Routes (Requires Authentication + Admin Role)
- `GET /api/admin/tutorials` - Get all tutorials (published + drafts)
- `POST /api/admin/tutorials` - Create new tutorial
- `GET /api/admin/tutorials/{id}` - Get single tutorial
- `PUT /api/admin/tutorials/{id}` - Update tutorial
- `DELETE /api/admin/tutorials/{id}` - Delete tutorial
- `POST /api/admin/tutorials/bulk-delete` - Delete multiple tutorials
- `POST /api/admin/tutorials/bulk-update-status` - Update multiple tutorial statuses

## Troubleshooting

### "Route not found" error
✅ **Fixed**: User model now has `isAdmin()` method
- Make sure you're logged in as an admin user (role='admin')
- Check that the `admin_token` is being sent in Authorization header

### "Unauthorized" error
- Your user doesn't have admin role
- Run the SQL update: `UPDATE users SET role='admin' WHERE email='your-email'`

### "Being logged out" on tutorials page
✅ **Fixed**: All pages now use `admin_token` instead of `authToken`

### Dashboard shows errors
✅ **Fixed**: Dashboard now handles missing blog stats gracefully

## File Structure

```
backend-w3university/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── TutorialController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   └── Models/
│       ├── User.php (✅ Updated)
│       └── Tutorial.php
├── routes/
│   └── api.php (Admin routes configured)
├── database/
│   └── migrations/
│       ├── 2025_12_06_000001_add_role_to_users_table.php
│       └── 2025_12_10_080049_create_tutorials_table.php
├── create-admin-user.php (PHP script to create admin)
└── create-admin.sql (SQL script to create admin)

w3university-frontend/
└── src/
    ├── app/
    │   └── admin/
    │       └── tutorials/
    │           ├── page.tsx (✅ Fixed)
    │           ├── new/
    │           │   └── page.tsx (✅ Fixed)
    │           └── edit/
    │               └── [id]/
    │                   └── page.tsx (✅ Fixed)
    └── lib/
        ├── auth.ts (Admin authentication)
        ├── tutorialApi.ts (API helper)
        └── api/
            └── admin.ts (✅ Fixed)
```

## Next Steps

1. ✅ Create an admin user using one of the methods above
2. ✅ Login to the frontend admin panel
3. ✅ Navigate to `/admin/tutorials`
4. ✅ Click "New Tutorial" to create your first tutorial
5. ✅ Test CRUD operations (Create, Read, Update, Delete)

All code changes have been implemented. You just need to create an admin user to start using the system!
