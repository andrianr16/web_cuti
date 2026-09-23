<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ResetCutiTahunan extends Command
{
    protected $signature = 'cuti:reset-tahunan';
    protected $description = 'Mereset kuota cuti tahunan seluruh karyawan pabrik';

    public function handle()
    {
        // Pindahkan sisa cuti sekarang ke sisa_cuti_lalu, lalu isi sisa_cuti baru = 12
        User::where('role', 'karyawan')->update([
            'sisa_cuti_lalu' => \DB::raw('sisa_cuti'), // simpan sisa lama jika ada kebijakan carry forward
            'sisa_cuti'      => 12,                     // kuota standar baru
        ]);

        $this->info('Reset kuota cuti tahunan berhasil dijalankan!');
    }
}
