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
        'alamat_cuti',
        'kontak_darurat',
        'status',
        'hrd_id',
        'catatan_hrd',
        'approved_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hrd(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hrd_id');
    }
}