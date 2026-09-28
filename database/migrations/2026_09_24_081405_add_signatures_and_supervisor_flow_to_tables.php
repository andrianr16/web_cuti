<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah tanda tangan tetap di tabel users (untuk SPV & HRD)
        Schema::table('users', function (Blueprint $table) {
            $table->longText('signature_pad')->nullable()->after('sisa_cuti');
        });

        // 2. Tambah tanda tangan karyawan & alur supervisor di cuti_requests
        Schema::table('cuti_requests', function (Blueprint $table) {
            $table->longText('ttd_karyawan')->nullable()->after('alasan');
            $table->foreignId('spv_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->longText('ttd_spv')->nullable()->after('spv_id');
            $table->dateTime('spv_approved_at')->nullable()->after('ttd_spv');
            $table->text('catatan_spv')->nullable()->after('spv_approved_at');
            $table->longText('ttd_hrd')->nullable()->after('hrd_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('signature_pad');
        });

        Schema::table('cuti_requests', function (Blueprint $table) {
            $table->dropForeign(['spv_id']);
            $table->dropColumn([
                'ttd_karyawan',
                'spv_id',
                'ttd_spv',
                'spv_approved_at',
                'catatan_spv',
                'ttd_hrd',
            ]);
        });
    }
};