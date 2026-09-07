<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('paket_wisata', 'booking_config')) {
            Schema::table('paket_wisata', function (Blueprint $table) {
                $table->json('booking_config')->nullable()->after('diskon_persen');
            });
        }

        $packages = [
            ['name' => 'Wedding Intimate Package', 'price' => 35000000, 'image' => 'images/Private Images/Paket-Wedding-dan-Engagement-1536x701.webp', 'type' => 'package', 'minimum' => 100, 'event' => 'wedding'],
            ['name' => 'Engagement Intimate Package', 'price' => 15000000, 'image' => 'images/Private Images/Paket-Wedding-dan-Engagement-1536x701.webp', 'type' => 'package', 'minimum' => 50, 'event' => 'engagement'],
            ['name' => 'Paket Half Day Gathering', 'price' => 210000, 'image' => 'images/Private Images/Paket-Gathering-2048x934.webp', 'type' => 'per_person', 'minimum' => 50, 'event' => 'gathering', 'duration' => 'half_day'],
            ['name' => 'Paket Full Day Gathering', 'price' => 250000, 'image' => 'images/Private Images/Paket-Gathering-2048x934.webp', 'type' => 'per_person', 'minimum' => 50, 'event' => 'gathering', 'duration' => 'full_day'],
            ['name' => 'Paket Half Day Meeting', 'price' => 250000, 'image' => 'images/Private Images/Paket-Meeting-1536x701.webp', 'type' => 'per_person', 'minimum' => 10, 'event' => 'meeting', 'duration' => 'half_day'],
            ['name' => 'Paket Full Day Meeting', 'price' => 400000, 'image' => 'images/Private Images/Paket-Meeting-1536x701.webp', 'type' => 'per_person', 'minimum' => 10, 'event' => 'meeting', 'duration' => 'full_day'],
            ['name' => 'Paket VIP Meeting', 'price' => 600000, 'image' => 'images/Private Images/Paket-Meeting-1536x701.webp', 'type' => 'per_person', 'minimum' => 15, 'event' => 'meeting', 'duration' => 'vip'],
        ];

        $cardImage = DB::table('paket_wisata')
            ->where('jenis_paket', 'Private Room')
            ->where('nama_paket', 'Paket Private Event')
            ->value('foto') ?: 'images/Private Images/BCA-Gathering-2048x1137.webp';

        foreach ($packages as $package) {
            $config = [
                'price_type' => $package['type'],
                'minimum_pax' => $package['minimum'],
                'event_type' => $package['event'],
                'duration' => $package['duration'] ?? null,
                'price_reference_image' => $package['image'],
                'tax_note' => $package['type'] === 'per_person' ? 'Harga belum termasuk pajak & service' : null,
            ];

            $existing = DB::table('paket_wisata')
                ->where('jenis_paket', 'Private Room')
                ->where('nama_paket', $package['name'])
                ->first();

            $values = [
                'nama_paket' => $package['name'],
                'slug' => Str::slug($package['name']),
                'deskripsi' => 'Private Room untuk ' . $package['event'] . '.',
                'foto' => $cardImage,
                'harga' => $package['price'],
                'kuota' => 100,
                'is_active' => true,
                'booking_config' => json_encode($config),
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('paket_wisata')->where('id', $existing->id)->update($values);
            } else {
                DB::table('paket_wisata')->insert($values + [
                    'jenis_paket' => 'Private Room',
                    'created_at' => now(),
                ]);
            }
        }

        DB::table('paket_wisata')
            ->where('jenis_paket', 'Private Room')
            ->where('nama_paket', 'Paket Private Event')
            ->whereNull('booking_config')
            ->update(['is_active' => false, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('paket_wisata')
            ->where('jenis_paket', 'Private Room')
            ->whereIn('nama_paket', [
                'Wedding Intimate Package',
                'Engagement Intimate Package',
                'Paket Half Day Gathering',
                'Paket Full Day Gathering',
                'Paket Half Day Meeting',
                'Paket Full Day Meeting',
                'Paket VIP Meeting',
            ])
            ->delete();

        Schema::table('paket_wisata', function (Blueprint $table) {
            $table->dropColumn('booking_config');
        });
    }
};
