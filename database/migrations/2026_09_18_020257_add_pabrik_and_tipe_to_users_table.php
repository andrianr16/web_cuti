<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('pabrik', ['supra', 'joya', 'minyak', 'seg'])->default('joya')->after('role');
            $table->enum('kategori', ['staff', 'tkl'])->default('staff')->after('pabrik');
            $table->integer('sisa_cuti_lalu')->default(0)->after('sisa_cuti'); // untuk sisa cuti tahun lalu
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pabrik', 'kategori', 'sisa_cuti_lalu']);
        });
    }
};
