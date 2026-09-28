<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->string('keterangan');
            // 'libur_nasional' = tanggal merah (tidak memotong jatah cuti tahunan)
            // 'cuti_bersama'   = cuti bersama pemerintah (MEMOTONG jatah cuti tahunan)
            $table->enum('jenis', ['libur_nasional', 'cuti_bersama'])->default('libur_nasional');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};