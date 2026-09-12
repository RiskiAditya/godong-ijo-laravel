<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $cards = [
            [
                'name' => 'Meeting Package',
                'description' => 'Paket meeting untuk acara perusahaan, rapat, dan kegiatan profesional dengan pilihan half day, full day, serta VIP sesuai kebutuhan acara.',
                'foto' => 'images/Private Images/BCA-Gathering-2048x1137.webp',
                'options' => [
                    ['key' => 'meeting_half_day', 'label' => 'Paket Half Day Meeting', 'price' => 250000, 'minimum' => 10, 'type' => 'per_person', 'event' => 'meeting', 'duration' => 'half_day', 'description' => 'IDR 250.000++ / Pax | Minimum 10 Pax'],
                    ['key' => 'meeting_full_day', 'label' => 'Paket Full Day Meeting', 'price' => 400000, 'minimum' => 10, 'type' => 'per_person', 'event' => 'meeting', 'duration' => 'full_day', 'description' => 'IDR 400.000++ / Pax | Minimum 10 Pax'],
                    ['key' => 'meeting_vip', 'label' => 'Paket VIP Meeting', 'price' => 600000, 'minimum' => 15, 'type' => 'per_person', 'event' => 'meeting', 'duration' => 'vip', 'description' => 'IDR 600.000++ / Pax | Minimum 15 Pax'],
                ],
            ],
            [
                'name' => 'Gathering Package',
                'description' => 'Paket gathering dengan buffet menu untuk acara perusahaan, komunitas, dan keluarga dalam pilihan half day atau full day.',
                'foto' => 'images/Private Images/BCA-Gathering-2048x1137.webp',
                'options' => [
                    ['key' => 'gathering_half_day', 'label' => 'Paket Half Day Gathering', 'price' => 210000, 'minimum' => 50, 'type' => 'per_person', 'event' => 'gathering', 'duration' => 'half_day', 'description' => 'Buffet Menu | IDR 210.000++ / Pax | Minimum 50 Pax'],
                    ['key' => 'gathering_full_day', 'label' => 'Paket Full Day Gathering', 'price' => 250000, 'minimum' => 50, 'type' => 'per_person', 'event' => 'gathering', 'duration' => 'full_day', 'description' => 'Buffet Menu | IDR 250.000++ / Pax | Minimum 50 Pax'],
                ],
            ],
            [
                'name' => 'Wedding Package',
                'description' => 'Paket intimate untuk wedding dan engagement dengan suasana private room, dekorasi elegan, dan kapasitas acara yang disesuaikan.',
                'foto' => 'images/Private Images/Dekorasi-Wedding-dan-Lamaran-2048x1137.webp',
                'options' => [
                    ['key' => 'wedding_intimate', 'label' => 'Wedding Intimate Package', 'price' => 35000000, 'minimum' => 100, 'type' => 'package', 'event' => 'wedding', 'duration' => 'full_day', 'description' => 'IDR 35 JUTA Nett | Kapasitas 100 Pax'],
                    ['key' => 'engagement_intimate', 'label' => 'Engagement Intimate Package', 'price' => 15000000, 'minimum' => 50, 'type' => 'package', 'event' => 'engagement', 'duration' => 'full_day', 'description' => 'IDR 15 JUTA Nett | Kapasitas 50 Pax'],
                ],
            ],
        ];

        $genericTarget = DB::table('paket_wisata')
            ->where('jenis_paket', 'Private Room')
            ->whereNotIn('nama_paket', array_column($cards, 'name'))
            ->orderBy('id')
            ->first();

        foreach ($cards as $card) {
            $attributes = [
                'jenis_paket' => 'Private Room',
                'deskripsi' => $card['description'],
                'foto' => $card['foto'],
                'harga' => 0,
                'kuota' => 100,
                'is_active' => true,
                'booking_config' => json_encode([
                    'price_type' => 'package',
                    'minimum_pax' => 1,
                    'event_type' => $card['options'][0]['event'],
                    'duration' => 'package',
                    'private_room_options' => $card['options'],
                ]),
                'updated_at' => now(),
            ];

            $existing = DB::table('paket_wisata')->where('nama_paket', $card['name'])->first();
            if ($existing) {
                DB::table('paket_wisata')->where('id', $existing->id)->update($attributes);
                continue;
            }

            if ($card['name'] === 'Meeting Package' && $genericTarget) {
                DB::table('paket_wisata')->where('id', $genericTarget->id)->update(array_merge($attributes, [
                    'nama_paket' => $card['name'],
                    'slug' => Str::slug($card['name']),
                ]));
                $genericTarget = null;
                continue;
            }

            DB::table('paket_wisata')->insert(array_merge($attributes, [
                'nama_paket' => $card['name'],
                'slug' => Str::slug($card['name']),
                'created_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        DB::table('paket_wisata')
            ->whereIn('nama_paket', ['Meeting Package', 'Gathering Package', 'Wedding Package'])
            ->where('jenis_paket', 'Private Room')
            ->delete();
    }
};
