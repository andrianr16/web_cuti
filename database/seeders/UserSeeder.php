<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun HRD / Admin
        User::create([
            'nip' => 'HRD001',
            'name' => 'HRD Manager',
            'email' => 'hrd@perusahaan.com',
            'password' => Hash::make('password123'),
            'role' => 'hrd',
            'divisi' => 'Human Resources',
            'jabatan' => 'HR Manager',
            'sisa_cuti' => 12,
        ]);

        // 2. Akun Karyawan 1 (Budi)
        User::create([
            'nip' => 'KRY001',
            'name' => 'Budi Santoso',
            'email' => 'budi@perusahaan.com',
            'password' => Hash::make('password123'),
            'role' => 'karyawan',
            'divisi' => 'Teknologi Informasi',
            'jabatan' => 'Web Developer',
            'sisa_cuti' => 12,
            'tgl_masuk_kerja' => '2023-01-15',
        ]);

        // 3. Akun Karyawan 2 (Siti)
        User::create([
            'nip' => 'KRY002',
            'name' => 'Siti Aminah',
            'email' => 'siti@perusahaan.com',
            'password' => Hash::make('password123'),
            'role' => 'karyawan',
            'divisi' => 'Keuangan',
            'jabatan' => 'Staff Accounting',
            'sisa_cuti' => 10,
            'tgl_masuk_kerja' => '2022-06-01',
        ]);
    }
}