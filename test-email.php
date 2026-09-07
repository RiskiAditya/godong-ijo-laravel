<?php

// Test Email Script
// Run: php test-email.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Testing Gmail SMTP connection...\n";
echo "Sending test email to: rizkyfahri081@gmail.com\n\n";

try {
    Mail::raw('Test email dari Laravel - The Waterfall Tourism', function($message) {
        $message->to('rizkyfahri081@gmail.com')
                ->subject('Test Email - The Waterfall');
    });
    
    echo "✅ SUCCESS! Email berhasil dikirim!\n";
    echo "Cek inbox Gmail kamu: rizkyfahri081@gmail.com\n";
    echo "\nKalau tidak ada di Inbox, cek folder Spam/Junk!\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR! Email gagal dikirim.\n";
    echo "Error message: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Pastikan App Password benar (16 karakter tanpa spasi)\n";
    echo "2. Pastikan 2FA Gmail sudah aktif\n";
    echo "3. Cek .env file: MAIL_USERNAME dan MAIL_PASSWORD\n";
    echo "4. Run: php artisan config:clear\n";
}
