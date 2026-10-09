<?php

namespace Tests\Feature;

use App\Models\PaketWisata;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Services\BookingEmailNotificationService;
use App\Services\EmailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class BookingEmailRetryCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_retry_command_sends_and_tracks_an_unsent_booking_confirmation(): void
    {
        $booking = $this->createBooking();
        $emailService = Mockery::mock(EmailService::class);
        $emailService->shouldReceive('sendBookingConfirmation')->once()->with(
            Mockery::on(fn (Pemesanan $sentBooking): bool => $sentBooking->is($booking)),
        )->andReturnTrue();
        $this->app->instance(BookingEmailNotificationService::class, new BookingEmailNotificationService($emailService));

        $this->artisan('email:retry-booking-notifications')->assertExitCode(0);

        $this->assertNotNull($booking->fresh()->booking_confirmation_email_sent_at);
        $this->assertSame(1, $booking->fresh()->booking_confirmation_email_attempts);
    }

    public function test_retry_command_sends_payment_success_email_only_for_paid_bookings(): void
    {
        $booking = $this->createBooking(['status' => 'paid']);
        $booking->forceFill(['booking_confirmation_email_sent_at' => now()])->save();
        Pembayaran::create([
            'pemesanan_id' => $booking->id,
            'order_id' => 'ORDER-EMAIL-RETRY',
            'gross_amount' => 200000,
            'status' => 'success',
        ]);

        $emailService = Mockery::mock(EmailService::class);
        $emailService->shouldReceive('sendPaymentSuccess')->once()->with(
            Mockery::on(fn (Pemesanan $sentBooking): bool => $sentBooking->is($booking)),
        )->andReturnTrue();
        $this->app->instance(BookingEmailNotificationService::class, new BookingEmailNotificationService($emailService));

        $this->artisan('email:retry-booking-notifications')->assertExitCode(0);

        $this->assertNotNull($booking->fresh()->payment_success_email_sent_at);
        $this->assertSame(1, $booking->fresh()->payment_success_email_attempts);
    }

    public function test_retry_command_applies_a_cooldown_after_a_failed_attempt(): void
    {
        $booking = $this->createBooking();
        $emailService = Mockery::mock(EmailService::class);
        $emailService->shouldReceive('sendBookingConfirmation')->once()->andReturnFalse();
        $this->app->instance(BookingEmailNotificationService::class, new BookingEmailNotificationService($emailService));

        $this->artisan('email:retry-booking-notifications')->assertExitCode(1);
        $this->artisan('email:retry-booking-notifications')->assertExitCode(0);

        $this->assertSame(1, $booking->fresh()->booking_confirmation_email_attempts);
        $this->assertNull($booking->fresh()->booking_confirmation_email_sent_at);
    }

    public function test_retry_command_does_not_resend_legacy_bookings(): void
    {
        $booking = $this->createBooking();
        $booking->forceFill([
            'booking_confirmation_email_tracking_enabled' => false,
            'payment_success_email_tracking_enabled' => false,
        ])->save();

        $emailService = Mockery::mock(EmailService::class);
        $emailService->shouldNotReceive('sendBookingConfirmation');
        $emailService->shouldNotReceive('sendPaymentSuccess');
        $this->app->instance(BookingEmailNotificationService::class, new BookingEmailNotificationService($emailService));

        $this->artisan('email:retry-booking-notifications')->assertExitCode(0);

        $this->assertNull($booking->fresh()->booking_confirmation_email_sent_at);
        $this->assertSame(0, $booking->fresh()->booking_confirmation_email_attempts);
    }

    private function createBooking(array $attributes = []): Pemesanan
    {
        $package = PaketWisata::create([
            'nama_paket' => 'Paket Email Retry',
            'slug' => 'paket-email-retry-'.uniqid(),
            'deskripsi' => 'Fixture package for email retry tests',
            'jenis_paket' => 'The Waterfall Resto',
            'harga' => 200000,
            'kuota' => 20,
            'is_active' => true,
        ]);

        return Pemesanan::create(array_merge([
            'paket_wisata_id' => $package->id,
            'kode_booking' => 'BK-EMAIL-'.strtoupper(bin2hex(random_bytes(4))),
            'nama_lengkap' => 'Test Customer',
            'email' => 'customer@example.com',
            'no_hp' => '08123456789',
            'jumlah_orang' => 2,
            'total_harga' => 200000,
            'status' => 'pending',
            'tanggal_kunjungan' => now()->addDays(2)->toDateString(),
        ], $attributes));
    }
}
