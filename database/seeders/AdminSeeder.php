<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@godongijo.com',
            'password' => bcrypt('admin123'), // Ganti password ini setelah login pertama kali
        ]);
    }
}
