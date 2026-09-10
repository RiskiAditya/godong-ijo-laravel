<?php

namespace Tests\Unit;

use App\Http\Controllers\BookingController;
use App\Models\Jadwal;
use App\Models\PaketWisata;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\BookingEmailNotificationService;
use App\Services\NotificationService;
use App\Services\PaymentStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Midtrans\Config;
use Tests\TestCase;

class MidtransConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_controller_initializes_midtrans_curl_headers(): void
    {
        config()->set('app.env', 'local');
        config()->set('midtrans.server_key', 'Mid-server-test');
        config()->set('midtrans.client_key', 'Mid-client-test');
        config()->set('midtrans.is_production', false);
        config()->set('midtrans.is_sanitized', true);
        config()->set('midtrans.is_3ds', true);

        $bookingEmailNotifications = $this->createMock(BookingEmailNotificationService::class);
        $paymentStatusService = $this->createMock(PaymentStatusService::class);
        $notificationService = $this->createMock(NotificationService::class);
        new BookingController($bookingEmailNotifications, $paymentStatusService, $notificationService);

        $this->assertArrayHasKey(CURLOPT_HTTPHEADER, Config::$curlOptions);
        $this->assertSame([], Config::$curlOptions[CURLOPT_HTTPHEADER]);
    }

    public function test_manual_mark_as_paid_uses_shared_payment_status_service(): void
    {
        config()->set('app.env', 'local');
        Route::get('/booking/confirmation/{kode}', fn () => 'ok')->name('booking.confirmation');

        $paket = PaketWisata::create([
            'nama_paket' => 'Paket Uji Manual Pay',
            'slug' => 'paket-uji-manual-pay',
            'deskripsi' => 'Fixture package',
            'harga' => 200000,
            'kuota' => 20,
            'is_active' => true,
        ]);

        $booking = Pemesanan::create([
            'paket_wisata_id' => $paket->id,
            'kode_booking' => 'BK20260907TEST1',
            'nama_lengkap' => 'Test User',
            'email' => 'test@example.com',
            'no_hp' => '08123456789',
            'jumlah_orang' => 2,
            'total_harga' => 200000,
            'status' => 'pending',
            'tanggal_kunjungan' => now()->toDateString(),
        ]);

        Pembayaran::create([
            'pemesanan_id' => $booking->id,
            'order_id' => 'ORDER-TEST-001',
            'gross_amount' => 200000,
            'status' => 'pending',
        ]);

        $bookingEmailNotifications = $this->createMock(BookingEmailNotificationService::class);
        $notificationService = $this->createMock(NotificationService::class);

        $paymentStatusService = new PaymentStatusService(
            $bookingEmailNotifications,
            $notificationService,
        );

        $bookingEmailNotifications->expects($this->once())
            ->method('paymentSuccess')
            ->with($this->callback(fn ($model) => $model->id === $booking->id));

        $notificationService->expects($this->once())
            ->method('createPaymentNotification')
            ->with($this->callback(fn ($model) => $model->id === $booking->id));

        $controller = new BookingController(
            $bookingEmailNotifications,
            $paymentStatusService,
            $notificationService,
        );

        $response = $controller->markAsPaid('ORDER-TEST-001');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertSame('paid', $booking->fresh()->status);
        $this->assertSame('success', $booking->pembayaran->fresh()->status);
    }

    public function test_payment_success_email_template_handles_missing_package_relation(): void
    {
        $booking = new Pemesanan();
        $booking->kode_booking = 'BK-TEST-EMAIL';
        $booking->nama_lengkap = 'Test User';
        $booking->email = 'test@example.com';
        $booking->tanggal_kunjungan = now()->toDateString();
        $booking->jumlah_orang = 2;
        $booking->total_harga = 200000;

        $html = view('emails.payment-success', [
            'pemesanan' => $booking,
            'pembayaran' => (object) ['payment_type' => 'gopay'],
            'paket' => null,
        ])->render();

        $this->assertStringContainsString('Pembayaran Berhasil', $html);
        $this->assertStringContainsString('Paket Wisata', $html);
    }

    public function test_expired_payment_releases_quota_once_and_expires_booking(): void
    {
        $paket = PaketWisata::create([
            'nama_paket' => 'Paket Uji Expired',
            'slug' => 'paket-uji-expired',
            'deskripsi' => 'Fixture package',
            'harga' => 200000,
            'kuota' => 10,
            'is_active' => true,
        ]);

        $jadwal = Jadwal::create([
            'paket_id' => $paket->id,
            'tanggal' => now()->addDay()->toDateString(),
            'kuota_tersedia' => 8,
        ]);

        $booking = Pemesanan::create([
            'paket_wisata_id' => $paket->id,
            'jadwal_id' => $jadwal->id,
            'kode_booking' => 'BK-EXPIRED-TEST',
            'nama_lengkap' => 'Expired User',
            'email' => 'expired@example.com',
            'no_hp' => '08123456789',
            'jumlah_orang' => 2,
            'total_harga' => 400000,
            'status' => 'pending',
            'tanggal_kunjungan' => now()->addDay()->toDateString(),
        ]);

        $pembayaran = Pembayaran::create([
            'pemesanan_id' => $booking->id,
            'order_id' => 'ORDER-EXPIRED-001',
            'gross_amount' => 400000,
            'status' => 'pending',
        ]);

        $service = new PaymentStatusService(
            $this->createMock(BookingEmailNotificationService::class),
            $this->createMock(NotificationService::class),
        );

        $service->apply($pembayaran, 'expire');
        $service->apply($pembayaran->fresh(), 'expire');

        $this->assertSame('expired', $booking->fresh()->status);
        $this->assertSame('expired', $pembayaran->fresh()->status);
        $this->assertSame(10, $jadwal->fresh()->kuota_tersedia);
    }
}
