<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun HRD
        User::firstOrCreate(
            ['email' => 'hrd@perusahaan.com'],
            [
                'nip' => 'HRD001',
                'name' => 'Tim HRD Personalia',
                'password' => Hash::make('password123'),
                'role' => 'hrd',
                'pabrik' => 'joya',
                'kategori' => 'staff',
                'divisi' => 'HRD',
                'jabatan' => 'Manager HRD',
                'sisa_cuti' => 12,
            ]
        );

        // 2. Akun Karyawan 1 (Budi - Pabrik Joya)
        User::firstOrCreate(
            ['email' => 'budi@perusahaan.com'],
            [
                'nip' => 'KRY001',
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'pabrik' => 'joya',
                'kategori' => 'staff',
                'divisi' => 'IT',
                'jabatan' => 'Staff IT',
                'sisa_cuti' => 12,
                'tgl_masuk_kerja' => '2023-01-15',
            ]
        );

        // 3. Akun Karyawan 2 (Siti - Pabrik Supra)
        User::firstOrCreate(
            ['email' => 'siti@perusahaan.com'],
            [
                'nip' => 'KRY002',
                'name' => 'Siti Rahma',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'pabrik' => 'supra',
                'kategori' => 'tkl',
                'divisi' => 'Produksi',
                'jabatan' => 'Operator Produksi',
                'sisa_cuti' => 12,
                'tgl_masuk_kerja' => '2023-05-10',
            ]
        );
    }
}