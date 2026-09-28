<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            // Tahun Baru 2026
            ['tanggal' => '2026-01-01', 'keterangan' => 'Tahun Baru 2026 Masehi', 'jenis' => 'libur_nasional'],

            // Rangkaian Idul Fitri 1447 H / 2026 M
            ['tanggal' => '2026-03-20', 'keterangan' => 'Cuti Bersama Hari Raya Idul Fitri 1447 H', 'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-21', 'keterangan' => 'Hari Raya Idul Fitri 1447 H (Hari ke-1)', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-03-22', 'keterangan' => 'Hari Raya Idul Fitri 1447 H (Hari ke-2)', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-03-23', 'keterangan' => 'Cuti Bersama Hari Raya Idul Fitri 1447 H', 'jenis' => 'cuti_bersama'],
            ['tanggal' => '2026-03-24', 'keterangan' => 'Cuti Bersama Hari Raya Idul Fitri 1447 H', 'jenis' => 'cuti_bersama'],

            // Hari Buruh & Kemerdekaan
            ['tanggal' => '2026-05-01', 'keterangan' => 'Hari Buruh Internasional', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-08-17', 'keterangan' => 'Hari Kemerdekaan RI Ke-81', 'jenis' => 'libur_nasional'],
            ['tanggal' => '2026-12-25', 'keterangan' => 'Hari Raya Natal', 'jenis' => 'libur_nasional'],
        ];

        foreach ($holidays as $item) {
            Holiday::updateOrCreate(
                ['tanggal' => $item['tanggal']],
                ['keterangan' => $item['keterangan'], 'jenis' => $item['jenis']]
            );
        }
    }
}