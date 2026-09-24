<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\HrdCutiController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'hrd') {
        return redirect()->route('hrd.cuti.index');
    }
    return redirect()->route('cuti.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/cuti/{cuti}/print', [CutiController::class, 'printSurat'])->name('cuti.print');
    Route::get('/cuti/{cuti}/pdf', [CutiController::class, 'exportPdf'])->name('cuti.pdf');
});

Route::middleware(['auth', 'role:karyawan'])->group(function () {
    Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
    Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');
});

Route::middleware(['auth', 'role:hrd'])->group(function () {
    Route::get('/hrd/cuti', [HrdCutiController::class, 'index'])->name('hrd.cuti.index');
    Route::post('/hrd/cuti/{cuti}/approve', [HrdCutiController::class, 'approve'])->name('hrd.cuti.approve');
    Route::post('/hrd/cuti/{cuti}/reject', [HrdCutiController::class, 'reject'])->name('hrd.cuti.reject');
    Route::get('/hrd/karyawan', [HrdCutiController::class, 'dashboardKaryawan'])->name('hrd.karyawan.index');
    Route::post('/hrd/karyawan/reset-cuti', [HrdCutiController::class, 'resetCutiMassal'])->name('hrd.reset.cuti');
    Route::get('/hrd/karyawan/tambah', [HrdCutiController::class, 'createKaryawan'])->name('hrd.karyawan.create');
    Route::post('/hrd/karyawan/simpan', [HrdCutiController::class, 'storeKaryawan'])->name('hrd.karyawan.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/cuti/{cuti}/print', [CutiController::class, 'printSurat'])->name('cuti.print');
    Route::get('/cuti/{cuti}/pdf', [CutiController::class, 'exportPdf'])->name('cuti.pdf');
});

Route::middleware(['auth', 'role:hrd'])->prefix('hrd')->name('hrd.')->group(function () {
    // Rute Cuti & Dashboard Karyawan
    Route::get('/karyawan', [HrdCutiController::class, 'dashboardKaryawan'])->name('karyawan.index');
    Route::get('/karyawan/tambah', [HrdCutiController::class, 'createKaryawan'])->name('karyawan.create');
    Route::post('/karyawan/simpan', [HrdCutiController::class, 'storeKaryawan'])->name('karyawan.store');
    Route::delete('/karyawan/{karyawan}', [HrdCutiController::class, 'destroy'])->name('karyawan.destroy');

    // Rute Edit Manual
    Route::get('/karyawan/{karyawan}/edit', [HrdCutiController::class, 'editKaryawan'])->name('karyawan.edit');
    Route::put('/karyawan/{karyawan}', [HrdCutiController::class, 'updateKaryawan'])->name('karyawan.update');
    Route::post('/reset-cuti', [HrdCutiController::class, 'resetCutiMassal'])->name('reset.cuti');

    // Rute Import Excel & Download Template
    Route::post('/karyawan/import', [HrdCutiController::class, 'importKaryawan'])->name('karyawan.import');
    Route::get('/karyawan/template', [HrdCutiController::class, 'downloadTemplateExcel'])->name('karyawan.template');

    Route::get('/karyawan-export-csv', [HrdCutiController::class, 'exportKaryawanCsv'])->name('karyawan.export.csv');
    Route::get('/karyawan-export-pdf', [HrdCutiController::class, 'exportKaryawanPdf'])->name('karyawan.export.pdf');

    // Ekspor Cuti
    Route::get('/cuti-export-csv', [HrdCutiController::class, 'exportCutiCsv'])->name('cuti.export.csv');
    Route::get('/cuti-export-pdf', [HrdCutiController::class, 'exportCutiPdf'])->name('cuti.export.pdf');

    Route::get('/cuti/export-excel', [HrdCutiController::class, 'exportExcel'])->name('cuti.export');
});



require __DIR__.'/auth.php';
