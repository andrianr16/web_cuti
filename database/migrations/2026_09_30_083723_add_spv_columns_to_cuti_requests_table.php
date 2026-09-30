<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuti_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('cuti_requests', 'spv_id')) {
                $table->foreignId('spv_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('cuti_requests', 'ttd_spv')) {
                $table->longText('ttd_spv')->nullable()->after('ttd_karyawan');
            }
            if (!Schema::hasColumn('cuti_requests', 'catatan_spv')) {
                $table->text('catatan_spv')->nullable()->after('ttd_spv');
            }
            if (!Schema::hasColumn('cuti_requests', 'spv_approved_at')) {
                $table->timestamp('spv_approved_at')->nullable()->after('catatan_spv');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cuti_requests', function (Blueprint $table) {
            $table->dropForeign(['spv_id']);
            $table->dropColumn(['spv_id', 'ttd_spv', 'catatan_spv', 'spv_approved_at']);
        });
    }
};