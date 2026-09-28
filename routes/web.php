<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\HrdCutiController;
use App\Http\Controllers\SupervisorCutiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard tunggal dengan pengalihan role yang rapi
Route::get('/dashboard', function () {
    $role = auth()->user()->role;
    if ($role === 'hrd') {
        return redirect()->route('hrd.cuti.index');
    } elseif ($role === 'supervisor') {
        return redirect()->route('spv.cuti.index');
    }
    return redirect()->route('cuti.index');
})->middleware(['auth'])->name('dashboard');

// Profil Karyawan & Dokumen Cuti Cetak/PDF
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/cuti/{cuti}/print', [CutiController::class, 'printSurat'])->name('cuti.print');
    Route::get('/cuti/{cuti}/pdf', [CutiController::class, 'exportPdf'])->name('cuti.pdf');
    Route::get('/cuti/{cuti}/surat-pdf', [CutiController::class, 'exportPdf'])->name('cuti.surat.pdf');
});

// Area Karyawan
Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
    Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');
});

// Area HRD (Disatukan dalam 1 grup ber-prefix 'hrd')
Route::middleware(['auth', 'role:hrd'])->prefix('hrd')->name('hrd.')->group(function () {
    // Persetujuan & Riwayat Cuti
    Route::get('/cuti', [HrdCutiController::class, 'index'])->name('cuti.index');
    Route::post('/cuti/{cuti}/approve', [HrdCutiController::class, 'approve'])->name('cuti.approve');
    Route::post('/cuti/{cuti}/reject', [HrdCutiController::class, 'reject'])->name('cuti.reject');

    // Monitoring & CRUD Data Karyawan
    Route::get('/karyawan', [HrdCutiController::class, 'dashboardKaryawan'])->name('karyawan.index');
    Route::get('/karyawan/tambah', [HrdCutiController::class, 'createKaryawan'])->name('karyawan.create');
    Route::post('/karyawan/simpan', [HrdCutiController::class, 'storeKaryawan'])->name('karyawan.store');
    Route::get('/karyawan/{karyawan}/edit', [HrdCutiController::class, 'editKaryawan'])->name('karyawan.edit');
    Route::put('/karyawan/{karyawan}', [HrdCutiController::class, 'updateKaryawan'])->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}', [HrdCutiController::class, 'destroy'])->name('karyawan.destroy');
    Route::post('/reset-cuti', [HrdCutiController::class, 'resetCutiMassal'])->name('reset.cuti');

    // Endpoint Riwayat Popup Modal Karyawan
    Route::get('/karyawan/{user}/riwayat', [HrdCutiController::class, 'riwayatKaryawan'])->name('karyawan.riwayat');

    // Import & Export Karyawan
    Route::post('/karyawan/import', [HrdCutiController::class, 'importKaryawan'])->name('karyawan.import');
    Route::get('/karyawan/template', [HrdCutiController::class, 'downloadTemplateExcel'])->name('karyawan.template');
    Route::get('/karyawan-export-csv', [HrdCutiController::class, 'exportKaryawanCsv'])->name('karyawan.export.csv');
    Route::get('/karyawan-export-pdf', [HrdCutiController::class, 'exportKaryawanPdf'])->name('karyawan.export.pdf');

    // Export Cuti
    Route::get('/cuti-export-csv', [HrdCutiController::class, 'exportCutiCsv'])->name('cuti.export.csv');
    Route::get('/cuti-export-pdf', [HrdCutiController::class, 'exportCutiPdf'])->name('cuti.export.pdf');
    Route::get('/cuti/export-excel', [HrdCutiController::class, 'exportExcel'])->name('cuti.export');
});

// Area Supervisor
Route::middleware(['auth', 'role:supervisor'])->prefix('spv')->group(function () {
    Route::get('/cuti', [SupervisorCutiController::class, 'index'])->name('spv.cuti.index');
    Route::post('/cuti/{cuti}/approve', [SupervisorCutiController::class, 'approve'])->name('spv.cuti.approve');
    Route::post('/cuti/{cuti}/reject', [SupervisorCutiController::class, 'reject'])->name('spv.cuti.reject');
});

require __DIR__.'/auth.php';