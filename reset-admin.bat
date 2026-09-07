@echo off
echo ====================================
echo RESET ADMIN ACCOUNT
echo ====================================
echo.

echo [1/4] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/4] Clearing sessions...
php artisan session:flush

echo.
echo [3/4] Resetting admin password...
php artisan tinker --execute="$admin = App\Models\Admin::where('username', 'admin')->first(); if($admin) { $admin->password = bcrypt('admin123'); $admin->save(); echo 'Password reset to: admin123'; } else { echo 'Admin not found!'; }"

echo.
echo [4/4] Testing login credentials...
php test-admin-login.php

echo.
echo ====================================
echo DONE! Try logging in with:
echo Username: admin
echo Password: admin123
echo ====================================
pause
