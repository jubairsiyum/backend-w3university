@echo off
echo ========================================
echo W3University Backend Profile Setup
echo ========================================
echo.

cd backend-w3university

echo Step 1: Running database migrations...
php artisan migrate
if %errorlevel% neq 0 (
    echo Error: Migrations failed
    pause
    exit /b 1
)
echo Migrations completed successfully
echo.

echo Step 2: Creating storage link for avatars...
php artisan storage:link
if %errorlevel% neq 0 (
    echo Error: Storage link creation failed
    pause
    exit /b 1
)
echo Storage link created successfully
echo.

echo Step 3: Clearing cache...
php artisan config:clear
php artisan route:clear
php artisan cache:clear
echo Cache cleared
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next steps:
echo   1. Start Laravel server: php artisan serve
echo   2. Test API at: http://localhost:8000/api/profile
echo   3. Update frontend API URL to: http://localhost:8000/api
echo.
pause
