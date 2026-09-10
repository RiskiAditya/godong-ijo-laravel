<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaketWisataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pakets = [
            [
                'nama_paket' => 'Paket Kuliner Keluarga',
                'jenis_paket' => 'The Waterfall Resto',
                'deskripsi' => 'Nikmati pengalaman kuliner ekologis di The Waterfall Resto dengan menu Eropa dan Nusantara di tengah suasana air terjun mini yang asri.

Fasilitas:
• Menu pilihan Eropa & Nusantara
• Suasana Dine in Nature
• Area bermain anak & Mini Zoo
• WiFi gratis hingga 100 Mbps
• Area parkir luas
• Musholla tersedia',
                'harga' => 75000,
                'kuota' => 100,
                'is_active' => true,
                'foto' => 'images/placeholders/asset 3.webp',
            ],
            [
                'nama_paket' => 'Paket Sport Fishing',
                'jenis_paket' => 'Fishing Lake',
                'deskripsi' => 'Tantangan memancing ikan raksasa di Monster Fish Fishing Lake. Pengalaman sport fishing yang tak terlupakan untuk pecinta mancing.

Fasilitas:
• Kolam pemancingan eksklusif
• Ikan berukuran raksasa
• Peralatan tersedia (optional)
• Spot foto menarik
• Fasilitas lengkap
• Cocok untuk pemula & profesional',
                'harga' => 0, // Custom price - "Hubungi Kami"
                'kuota' => 50,
                'is_active' => true,
                'foto' => 'images/placeholders/Redtail-Catfish-Ikan-Predator-Amerika-Selatan-1536x853.webp',
            ],
            [
                'nama_paket' => 'Meeting Package',
                'jenis_paket' => 'Private Room',
                'deskripsi' => 'Paket meeting untuk acara perusahaan, rapat, dan kegiatan profesional dengan pilihan half day, full day, serta VIP sesuai kebutuhan acara.',
                'harga' => 250000,
                'kuota' => 100,
                'is_active' => true,
                'foto' => 'images/Private Images/BCA-Gathering-2048x1137.webp',
            ],
            [
                'nama_paket' => 'Gathering Package',
                'jenis_paket' => 'Private Room',
                'deskripsi' => 'Paket gathering dengan buffet menu untuk acara perusahaan, komunitas, dan keluarga dalam pilihan half day atau full day.',
                'harga' => 210000,
                'kuota' => 100,
                'is_active' => true,
                'foto' => 'images/Private Images/BCA-Gathering-2048x1137.webp',
            ],
            [
                'nama_paket' => 'Wedding Package',
                'jenis_paket' => 'Private Room',
                'deskripsi' => 'Paket intimate untuk wedding dan engagement dengan suasana private room, dekorasi elegan, dan kapasitas acara yang disesuaikan.',
                'harga' => 15000000,
                'kuota' => 100,
                'is_active' => true,
                'foto' => 'images/Private Images/Dekorasi-Wedding-dan-Lamaran-2048x1137.webp',
            ],
            [
                'nama_paket' => 'Paket Rekreasi Keluarga',
                'jenis_paket' => 'The Waterfall Resto',
                'deskripsi' => 'Paket lengkap untuk liburan keluarga: makan di resto, bermain di Mini Zoo, dan fishing. Satu destinasi untuk semua kebutuhan rekreasi.

Fasilitas:
• Makan di The Waterfall Resto
• Akses Mini Zoo & Kids Area
• Fishing experience (1 jam)
• Foto bersama satwa
• Area bermain anak aman
• Parkir gratis',
                'harga' => 150000,
                'kuota' => 80,
                'is_active' => true,
                'foto' => 'images/placeholders/hewan.jpg',
            ],
        ];

        foreach ($pakets as $paket) {
            \App\Models\PaketWisata::updateOrCreate(
                ['nama_paket' => $paket['nama_paket']],
                $paket,
            );
        }

        \App\Models\PaketWisata::where('jenis_paket', 'Private Room')
            ->whereNotIn('nama_paket', ['Meeting Package', 'Gathering Package', 'Wedding Package'])
            ->whereDoesntHave('pemesanan')
            ->whereDoesntHave('jadwal')
            ->delete();
    }
}
