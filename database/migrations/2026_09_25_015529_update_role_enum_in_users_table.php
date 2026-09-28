<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum kolom role agar menerima 'supervisor'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('karyawan', 'supervisor', 'hrd') DEFAULT 'karyawan'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('karyawan', 'hrd') DEFAULT 'karyawan'");
    }
};