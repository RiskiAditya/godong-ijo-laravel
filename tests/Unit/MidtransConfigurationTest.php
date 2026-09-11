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

    public function test_local_midtrans_defaults_to_live_sandbox_mode_when_not_explicitly_simulated(): void
    {
        config()->set('app.env', 'local');
        config()->set('midtrans.server_key', 'SB-Mid-server-VALID_SANDBOX_KEY');
        config()->set('midtrans.payment_mode', 'live');
        config()->set('midtrans.is_production', false);

        $service = new \App\Services\MidtransConfigService();
        $service->configure();

        $this->assertSame('live', config('midtrans.payment_mode'));
        $this->assertFalse(Config::$isProduction);
    }

    public function test_valid_sandbox_key_overrides_stale_simulation_mode(): void
    {
        config()->set('app.env', 'local');
        config()->set('midtrans.server_key', 'SB-Mid-server-VALID_SANDBOX_KEY');
        config()->set('midtrans.client_key', 'SB-Mid-client-VALID_SANDBOX_KEY');
        config()->set('midtrans.payment_mode', 'simulation');
        config()->set('midtrans.is_production', true);

        $service = new \App\Services\MidtransConfigService();
        $service->configure();

        $this->assertSame('live', config('midtrans.payment_mode'));
        $this->assertFalse(config('midtrans.is_production'));
        $this->assertFalse(Config::$isProduction);
    }

    public function test_local_environment_forces_sandbox_mode_even_with_production_key(): void
    {
        config()->set('app.env', 'local');
        config()->set('midtrans.server_key', 'Mid-server-PRODUCTION_KEY_EXAMPLE');
        config()->set('midtrans.client_key', 'Mid-client-PRODUCTION_KEY_EXAMPLE');
        config()->set('midtrans.payment_mode', 'live');
        config()->set('midtrans.is_production', true);

        $service = new \App\Services\MidtransConfigService();
        $service->configure();

        $this->assertSame('live', config('midtrans.payment_mode'));
        $this->assertFalse(config('midtrans.is_production'));
        $this->assertFalse(Config::$isProduction);
    }

    public function test_fishing_confirmation_view_matches_selected_form_details(): void
    {
        $paket = PaketWisata::create([
            'nama_paket' => 'Paket Sport Fishing',
            'slug' => 'paket-sport-fishing',
            'deskripsi' => 'Fixture fishing package',
            'jenis_paket' => 'Fishing Lake',
            'harga' => 75000,
            'kuota' => 20,
            'is_active' => true,
        ]);

        $booking = Pemesanan::create([
            'paket_wisata_id' => $paket->id,
            'kode_booking' => 'BK-FISHING-DETAILS',
            'nama_lengkap' => 'Test Fisher',
            'email' => 'test@example.com',
            'no_hp' => '08123456789',
            'tanggal_kunjungan' => '2026-09-18',
            'jam_kunjungan' => '19:23',
            'jumlah_orang' => 1,
            'jumlah_joran' => 1,
            'total_harga' => 75000,
            'status' => 'pending',
            'package_specific_data' => [
                'jenis_pemancingan' => 'tarikan',
                'durasi' => '4',
                'tambahan_jam' => 2,
                'jumlah_joran' => 1,
                'perlu_sewa_alat' => true,
                'ukuran_joran' => 'besar',
                'jam_kunjungan' => '19:23',
                'umpan' => [
                    'anak_ikan_komet' => 2,
                    'umpan_jadi_godongijo' => 1,
                ],
            ],
        ]);

        $html = view('booking.confirmation', [
            'booking' => $booking,
            'navigation' => [],
            'cta' => [],
            'seoData' => [],
        ])->render();

        $this->assertStringContainsString('Durasi', $html);
        $this->assertStringContainsString('4 jam', $html);
        $this->assertStringContainsString('Tambahan Jam', $html);
        $this->assertStringContainsString('2 jam', $html);
        $this->assertStringContainsString('Anak Ikan Komet', $html);
        $this->assertStringContainsString('Umpan Jadi Godongijo', $html);
        $this->assertStringContainsString('Ukuran Joran', $html);
        $this->assertStringContainsString('Besar', $html);
    }

    public function test_kiloan_fishing_booking_uses_real_midtrans_token_instead_of_simulation(): void
    {
        config()->set('app.env', 'local');
        config()->set('midtrans.server_key', 'SB-Mid-server-VALID_SANDBOX_KEY');
        config()->set('midtrans.client_key', 'SB-Mid-client-VALID_SANDBOX_KEY');
        config()->set('midtrans.payment_mode', 'live');
        config()->set('midtrans.is_production', false);
        config()->set('midtrans.allow_real_in_tests', true);

        $mock = \Mockery::mock('alias:Midtrans\Snap');
        $mock->shouldReceive('getSnapToken')->once()->andReturn('SNAP-TEST-KILOAN-123');

        $pricing = new \App\Services\BookingPricingService();
        $service = new \App\Services\BookingCreationService($pricing);

        $fishingPaket = PaketWisata::create([
            'nama_paket' => 'Fishing Lake',
            'slug' => 'fishing-lake-kiloan',
            'deskripsi' => 'Fixture kiloan fishing package',
            'jenis_paket' => 'Fishing Lake',
            'harga' => 150000,
            'kuota' => 10,
            'is_active' => true,
        ]);

        Jadwal::create([
            'paket_id' => $fishingPaket->id,
            'tanggal' => '2026-09-20',
            'kuota_tersedia' => 10,
        ]);

        $result = $service->createFishingBooking([
            'nama_lengkap' => 'Kiloan Tester',
            'email' => 'kiloan@example.com',
            'no_hp' => '08123456789',
            'tanggal_kunjungan' => '2026-09-20',
            'jam_kunjungan' => '09:30',
            'jenis_pemancingan' => 'kiloan',
            'jumlah_joran' => 1,
            'perlu_sewa_alat' => true,
            'ukuran_joran' => 'besar',
            'qty_komet' => 2,
            'qty_umpan_jadi' => 1,
            'setuju_aturan' => true,
        ]);

        $this->assertSame('SNAP-TEST-KILOAN-123', $result['snap_token']);
        $this->assertSame(83000.0, $result['estimasi_total']);
        $this->assertStringNotContainsString('SIMULATION-', $result['snap_token']);
    }

    public function test_fishing_lake_email_templates_include_form_details(): void
    {
        $paket = PaketWisata::create([
            'nama_paket' => 'Fishing Lake',
            'slug' => 'fishing-lake-email',
            'deskripsi' => 'Fixture fishing package',
            'jenis_paket' => 'Fishing Lake',
            'harga' => 80000,
            'kuota' => 20,
            'is_active' => true,
        ]);

        $booking = Pemesanan::create([
            'paket_wisata_id' => $paket->id,
            'kode_booking' => 'GOD-EMAIL-DETAILS',
            'nama_lengkap' => 'Email Tester',
            'email' => 'tester@example.com',
            'no_hp' => '08123456789',
            'tanggal_kunjungan' => '2026-09-18',
            'jam_kunjungan' => '19:00',
            'jumlah_orang' => 1,
            'total_harga' => 150000,
            'status' => 'pending',
            'package_specific_data' => [
                'jenis_pemancingan' => 'tarikan',
                'durasi' => '4',
                'tambahan_jam' => 1,
                'jumlah_joran' => 1,
                'perlu_sewa_alat' => true,
                'ukuran_joran' => 'besar',
                'umpan' => [
                    'anak_ikan_komet' => 2,
                    'umpan_jadi_godongijo' => 1,
                ],
            ],
        ]);

        $booking->load('paketWisata');
        $booking->pembayaran()->create([
            'order_id' => 'ORDER-EMAIL-1',
            'gross_amount' => 150000,
            'payment_type' => 'gopay',
            'status' => 'success',
            'snap_token' => 'SIMULATION-TEST',
        ]);

        $confirmationEmail = view('emails.booking-confirmation', ['pemesanan' => $booking, 'paket' => $paket, 'packageDisplayName' => 'Fishing Lake', 'packageType' => 'fishing-lake', 'jadwal' => null])->render();
        $successEmail = view('emails.payment-success', ['pemesanan' => $booking, 'pembayaran' => $booking->pembayaran, 'paket' => $paket, 'packageDisplayName' => 'Fishing Lake', 'packageType' => 'fishing-lake'])->render();

        foreach ([$confirmationEmail, $successEmail] as $emailHtml) {
            $this->assertStringContainsString('Jenis Pemancingan', $emailHtml);
            $this->assertStringContainsString('Tarikan', $emailHtml);
            $this->assertStringContainsString('Durasi', $emailHtml);
            $this->assertStringContainsString('4 jam', $emailHtml);
            $this->assertStringContainsString('Tambahan Jam', $emailHtml);
            $this->assertStringContainsString('Sewa Alat', $emailHtml);
            $this->assertStringContainsString('Ya • Besar', $emailHtml);
            $this->assertStringContainsString('Anak Ikan Komet', $emailHtml);
            $this->assertStringContainsString('Umpan Jadi Godongijo', $emailHtml);
        }
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
