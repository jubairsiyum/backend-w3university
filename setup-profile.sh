#!/bin/bash

echo "🚀 Setting up W3University Backend Profile System"
echo "================================================="
echo ""

# Navigate to backend directory
cd backend-w3university

echo "📦 Step 1: Running database migrations..."
php artisan migrate

if [ $? -eq 0 ]; then
    echo "✅ Migrations completed successfully"
else
    echo "❌ Migrations failed"
    exit 1
fi

echo ""
echo "📁 Step 2: Creating storage link for avatars..."
php artisan storage:link

if [ $? -eq 0 ]; then
    echo "✅ Storage link created successfully"
else
    echo "❌ Storage link creation failed"
    exit 1
fi

echo ""
echo "🔍 Step 3: Checking database tables..."
php artisan db:show

echo ""
echo "✅ Setup Complete!"
echo ""
echo "📝 Next steps:"
echo "   1. Start the Laravel server: php artisan serve"
echo "   2. Test the API endpoint: http://localhost:8000/api/profile"
echo "   3. Update frontend API_BASE_URL to: http://localhost:8000/api"
echo ""
echo "🌐 For production (Vercel):"
echo "   1. Deploy backend to Vercel"
echo "   2. Run migrations on production database"
echo "   3. Set up storage for avatars"
echo ""
