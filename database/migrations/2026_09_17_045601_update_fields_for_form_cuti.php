<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah tanggal masuk kerja di users jika belum ada
        Schema::table('users', function (Blueprint $table) {
            $table->date('tgl_masuk_kerja')->nullable()->after('divisi');
        });

        // Tambah detail alamat & tipe permohonan di cuti_requests
        Schema::table('cuti_requests', function (Blueprint $table) {
            $table->string('jenis_permohonan')->default('Cuti Tahunan')->after('user_id');
            $table->text('alamat_cuti')->nullable()->after('kontak_darurat');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tgl_masuk_kerja');
        });
        Schema::table('cuti_requests', function (Blueprint $table) {
            $table->dropColumn(['jenis_permohonan', 'alamat_cuti']);
        });
    }
};