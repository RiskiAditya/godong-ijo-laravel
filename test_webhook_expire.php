<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

use Illuminate\Http\Request;

// Create proper request with all needed data
$request = Request::create('http://localhost/TA/public/notification', 'POST', [
    'order_id' => 'FISHING-27-TEST',
    'transaction_status' => 'expire',
    'transaction_id' => 'TEST-TXN-EXPIRE-123',
    'payment_type' => 'bank_transfer',
    'fraud_status' => 'accept',
]);

try {
    // Process the request through kernel
    $response = $kernel->handle($request);
    
    echo "=== WEBHOOK RESPONSE ===\n";
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n\n";
    
    // Check database status
    $pemesanan = App\Models\Pemesanan::find(27);
    $pembayaran = App\Models\Pembayaran::where('order_id', 'FISHING-27-TEST')->first();
    
    echo "=== DATABASE STATUS AFTER WEBHOOK ===\n";
    echo "Pemesanan Status: " . $pemesanan->status . "\n";
    echo "Pembayaran Status: " . $pembayaran->status . "\n";
    echo "jadwal_id: " . ($pemesanan->jadwal_id ?? 'NULL') . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

// Check logs
echo "\n=== CHECKING LOGS ===\n";
$laravelLog = 'storage/logs/laravel.log';
$bookingLog = 'storage/logs/booking-errors.log';

if (file_exists($laravelLog)) {
    $lines = file($laravelLog);
    $recent = array_slice($lines, -20);
    echo "Recent Laravel Log (last 20 lines):\n";
    echo implode('', $recent);
} else {
    echo "Laravel log not found\n";
}

echo "\n";

if (file_exists($bookingLog)) {
    echo "Booking Errors Log:\n";
    echo file_get_contents($bookingLog);
} else {
    echo "No booking-errors.log (no errors occurred)\n";
}

$kernel->terminate($request, $response);
