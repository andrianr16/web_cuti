<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mengubah ENUM role agar mendukung 'supervisor'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('hrd', 'supervisor', 'karyawan') NOT NULL DEFAULT 'karyawan'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('hrd', 'karyawan') NOT NULL DEFAULT 'karyawan'");
    }
};