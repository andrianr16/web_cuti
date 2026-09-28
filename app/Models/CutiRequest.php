<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiRequest extends Model
{
    protected $fillable = [
        'user_id',
        'nomor_surat',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'alasan',
        'kontak_darurat',
        'status',
        'hrd_id',
        'catatan_hrd',
        'approved_at',
        'ttd_karyawan',
        'spv_id',
        'ttd_spv',
        'spv_approved_at',
        'catatan_spv',
        'ttd_hrd',
    ];

    // Relasi ke Pemohon (Karyawan)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Supervisor
    public function spv(): BelongsTo
    {
        return $this->belongsTo(User::class, 'spv_id');
    }

    // Relasi ke HRD (Cukup tulis SATU kali saja di sini)
    public function hrd(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hrd_id');
    }
}