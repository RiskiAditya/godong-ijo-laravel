<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FishingBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
        // Create Fishing Lake package required for fishing bookings
        \App\Models\PaketWisata::firstOrCreate(
            ['jenis_paket' => 'Fishing Lake'],
            [
                'nama_paket' => 'Paket Sport Fishing',
                'deskripsi' => 'Paket pemancingan di Monster Fish Fishing Lake',
                'harga' => 0,
                'kuota' => 999, // Fishing lake has unlimited capacity
                'is_active' => true,
            ]
        );
    }

    public function test_fishing_booking_validation_rejects_invalid_time_and_missing_agreement(): void
    {
        $response = $this->postJson('/booking/fishing', [
            'nama_lengkap' => 'Budi',
            'email' => 'budi@example.com',
            'no_hp' => '081234567890',
            'tanggal_kunjungan' => now()->addDay()->format('Y-m-d'),
            'jam_kunjungan' => '08:00',
            'jenis_pemancingan' => 'tarikan',
            'jumlah_joran' => 1,
            'durasi' => '2',
            'tambahan_jam' => 0,
            'setuju_aturan' => false,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['jam_kunjungan', 'setuju_aturan']);
    }

    public function test_fishing_booking_can_be_saved_for_kiloan_with_null_estimate(): void
    {
        $response = $this->postJson('/booking/fishing', [
            'nama_lengkap' => 'Sinta',
            'email' => 'sinta@example.com',
            'no_hp' => '081234567891',
            'tanggal_kunjungan' => now()->addDay()->format('Y-m-d'),
            'jam_kunjungan' => '10:30',
            'jenis_pemancingan' => 'kiloan',
            'jumlah_joran' => 2,
            'setuju_aturan' => true,
            'qty_komet' => 1,
            'qty_umpan_jadi' => 0,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pemesanan', [
            'nama_lengkap' => 'Sinta',
            'no_hp' => '6281234567891',
            'status' => 'pending',
        ]);

        $booking = \App\Models\Pemesanan::where('nama_lengkap', 'Sinta')->first();
        $this->assertNull($booking->total_harga);
        $this->assertSame('kiloan', $booking->package_specific_data['jenis_pemancingan'] ?? null);
    }
}
