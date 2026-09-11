<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivateRoomBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        config(['midtrans.payment_mode' => 'simulation']);

        \App\Models\PaketWisata::create([
            'jenis_paket' => 'Private Room',
            'nama_paket' => 'Paket Private Event',
            'deskripsi' => 'Private room booking',
            'harga' => 250000,
            'kuota' => 200,
            'is_active' => true,
            'booking_config' => ['minimum_pax' => 100],
        ]);
    }

    public function test_private_room_booking_uses_selected_option_minimum_instead_of_global_default(): void
    {
        $package = \App\Models\PaketWisata::first();

        $response = $this->postJson('/api/booking/store', [
            'paket_wisata_id' => $package->id,
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'no_hp' => '081234567890',
            'tanggal_kunjungan' => now()->addDay()->format('Y-m-d'),
            'jumlah_orang' => 50,
            'package_specific_data' => [
                'private_room_option' => 'gathering_full_day',
                'event_type' => 'gathering',
                'expected_attendees' => 50,
                'event_duration' => 'full_day',
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pemesanan', [
            'email' => 'budi@example.com',
            'status' => 'pending',
        ]);
    }
}
