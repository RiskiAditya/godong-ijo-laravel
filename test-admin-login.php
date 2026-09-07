<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

echo "=== TESTING ADMIN LOGIN ===\n\n";

// Check if admins table has data
$admins = Admin::all();
echo "Total admins in database: " . $admins->count() . "\n\n";

if ($admins->count() === 0) {
    echo "❌ ERROR: No admin accounts found!\n";
    echo "Running AdminSeeder...\n";
    Artisan::call('db:seed', ['--class' => 'AdminSeeder']);
    echo "✅ AdminSeeder executed\n\n";
    $admins = Admin::all();
}

// Display all admins
echo "Admin accounts:\n";
foreach ($admins as $admin) {
    echo "  - ID: {$admin->id}\n";
    echo "    Name: {$admin->name}\n";
    echo "    Username: {$admin->username}\n";
    echo "    Email: {$admin->email}\n";
    echo "    Password Hash: " . substr($admin->password, 0, 20) . "...\n\n";
}

// Test password verification
echo "=== TESTING PASSWORD VERIFICATION ===\n";
$testUsername = 'admin';
$testPassword = 'admin123';

$admin = Admin::where('username', $testUsername)->first();

if ($admin) {
    echo "✅ Admin found with username: {$testUsername}\n";
    
    $passwordCheck = Hash::check($testPassword, $admin->password);
    
    if ($passwordCheck) {
        echo "✅ Password '{$testPassword}' is CORRECT!\n";
        echo "\nYou can login with:\n";
        echo "  Username: {$testUsername}\n";
        echo "  Password: {$testPassword}\n";
    } else {
        echo "❌ Password '{$testPassword}' is INCORRECT!\n";
        echo "Regenerating password...\n";
        $admin->password = Hash::make($testPassword);
        $admin->save();
        echo "✅ Password has been reset to: {$testPassword}\n";
    }
} else {
    echo "❌ Admin not found with username: {$testUsername}\n";
}
