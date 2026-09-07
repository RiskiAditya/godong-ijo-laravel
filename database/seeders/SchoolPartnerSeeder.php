<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks untuk truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('school_partners')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $schools = [
            // Real school partners with logos
            [
                'name' => 'BPK Penabur',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/Sekolah-BPK-Penabur-150x110.png',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Sekolah Bukit Sion',
                'level' => 'SMP',
                'logo_path' => 'images/sekolahan/Blue-Campus-Sekolah-Bukit-Sion.png',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'SD Islam Al-Ikhlas',
                'level' => 'SD',
                'logo_path' => 'images/sekolahan/SD-Islam-Al-Ikhlas-150x150.png',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'SMP Islam Al-Ikhlas',
                'level' => 'SMP',
                'logo_path' => 'images/sekolahan/SMP-Islam-Al-Ikhlas-150x150.png',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'Sekolah Ananda',
                'level' => 'SD',
                'logo_path' => 'images/sekolahan/Sekolah-Ananda.png',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'name' => 'Sekolah Athalia',
                'level' => 'SMP',
                'logo_path' => 'images/sekolahan/Sekolah-Athalia.png',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'name' => 'Bakti Mulya 400',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/Sekolah-Bakti-Mulya-400.png',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'name' => 'Sekolah Bukit Mulia',
                'level' => 'SD',
                'logo_path' => 'images/sekolahan/Sekolah-Bukit-Mulia-150x150.png',
                'is_active' => true,
                'order' => 8,
            ],
            [
                'name' => 'Sekolah Citra Berkat',
                'level' => 'SD',
                'logo_path' => 'images/sekolahan/Sekolah-Citra-Berkat.png',
                'is_active' => true,
                'order' => 9,
            ],
            [
                'name' => 'Dian Harapan',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/Sekolah-Dian-Harapan-150x150.png',
                'is_active' => true,
                'order' => 10,
            ],
            [
                'name' => 'SD Islam PB Soedirman',
                'level' => 'SD',
                'logo_path' => 'images/sekolahan/Sekolah-Islam-PB-Soedirman-150x150.png',
                'is_active' => true,
                'order' => 11,
            ],
            [
                'name' => 'Kalam Kudus',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/Sekolah-Kalam-Kudus-300x217.png',
                'is_active' => true,
                'order' => 12,
            ],
            [
                'name' => 'Kanaan Global School',
                'level' => 'SMP',
                'logo_path' => 'images/sekolahan/Sekolah-Kanaan-Global-150x150.png',
                'is_active' => true,
                'order' => 13,
            ],
            [
                'name' => 'Sekolah Kasih Kemuliaan',
                'level' => 'SD',
                'logo_path' => 'images/sekolahan/Sekolah-Kasih-Kemuliaan.png',
                'is_active' => true,
                'order' => 14,
            ],
            [
                'name' => 'Sekolah Lemuel',
                'level' => 'SD',
                'logo_path' => 'images/sekolahan/Sekolah-Lemuel.png',
                'is_active' => true,
                'order' => 15,
            ],
            [
                'name' => 'MAN Insan Cendekia',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/Sekolah-MAN-Insan-Cendekia-Upload-150x110.png',
                'is_active' => true,
                'order' => 16,
            ],
            [
                'name' => 'Marsudirini Bogor',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/Sekolah-Marsudirini-Bogor-150x110.png',
                'is_active' => true,
                'order' => 17,
            ],
            [
                'name' => 'Sekolah Menara Kasih',
                'level' => 'SD',
                'logo_path' => 'images/sekolahan/Sekolah-Menara-Kasih.png',
                'is_active' => true,
                'order' => 18,
            ],
            [
                'name' => 'Pancaran Berkat',
                'level' => 'SMP',
                'logo_path' => 'images/sekolahan/Sekolah-Pancaran-Berkat-150x110.png',
                'is_active' => true,
                'order' => 19,
            ],
            [
                'name' => 'Perkumpulan Mandiri',
                'level' => 'SMP',
                'logo_path' => 'images/sekolahan/Sekolah-Perkumpulan-Mandiri-150x110.png',
                'is_active' => true,
                'order' => 20,
            ],
            [
                'name' => 'Regina Pacis',
                'level' => 'SMP',
                'logo_path' => 'images/sekolahan/Sekolah-Regina-Pacis.png',
                'is_active' => true,
                'order' => 21,
            ],
            [
                'name' => 'Santa Angela',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/Sekolah-Santa-Angela-150x150.png',
                'is_active' => true,
                'order' => 22,
            ],
            [
                'name' => 'Santa Theresia',
                'level' => 'SMP',
                'logo_path' => 'images/sekolahan/Sekolah-Santa-Theresia.png',
                'is_active' => true,
                'order' => 23,
            ],
            [
                'name' => 'Santa Ursula BSD',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/Sekolah-Santa-Ursula-BSD-150x150.png',
                'is_active' => true,
                'order' => 24,
            ],
            [
                'name' => 'Tarsisius',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/Sekolah-Tarsisius-150x150.png',
                'is_active' => true,
                'order' => 25,
            ],
            [
                'name' => 'SMAN 2 Tangerang Selatan',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/SMAN-2-Tangerang-Selatan-Upload-150x110.png',
                'is_active' => true,
                'order' => 26,
            ],
            [
                'name' => 'Springfield School',
                'level' => 'SMP',
                'logo_path' => 'images/sekolahan/Spring-Field-School.png',
                'is_active' => true,
                'order' => 27,
            ],
            [
                'name' => 'Tarakanita',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/Yayasan-Tarakanita.png',
                'is_active' => true,
                'order' => 28,
            ],
            [
                'name' => 'School International School',
                'level' => 'SMA',
                'logo_path' => 'images/sekolahan/School-Internationa-School-300x217.png',
                'is_active' => true,
                'order' => 29,
            ],
        ];

        foreach ($schools as $school) {
            DB::table('school_partners')->insert(array_merge($school, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Successfully seeded ' . count($schools) . ' school partners.');
    }
}
