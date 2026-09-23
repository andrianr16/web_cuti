<?php

namespace App\Exports;

use App\Models\CutiRequest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapCutiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $status;
    protected $pabrik;
    protected $tahun;
    protected $bulan;

    public function __construct($status, $pabrik, $tahun, $bulan)
    {
        $this->status = $status;
        $this->pabrik = $pabrik;
        $this->tahun  = $tahun;
        $this->bulan  = $bulan;
    }

    public function collection(): Collection
    {
        $query = CutiRequest::with(['user', 'hrd'])->latest();

        if ($this->status !== 'semua' && !empty($this->status)) {
            $query->where('status', $this->status);
        }

        if ($this->pabrik !== 'semua' && !empty($this->pabrik)) {
            $pabrik = $this->pabrik;
            $query->whereHas('user', function ($q) use ($pabrik) {
                $q->where('pabrik', strtolower($pabrik));
            });
        }

        if ($this->tahun !== 'semua' && !empty($this->tahun)) {
            if ($this->bulan !== 'semua' && !empty($this->bulan)) {
                $periode = sprintf('%s-%02d', $this->tahun, $this->bulan);
                $query->where(function ($q) use ($periode) {
                    $q->where('tanggal_mulai', 'like', $periode . '%')
                      ->orWhere('tanggal_selesai', 'like', $periode . '%');
                });
            } else {
                $query->where(function ($q) {
                    $q->whereYear('tanggal_mulai', $this->tahun)
                      ->orWhereYear('tanggal_selesai', $this->tahun);
                });
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nomor Surat',
            'NIP',
            'Nama Karyawan',
            'Divisi',
            'Pabrik',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Jumlah Hari',
            'Alasan',
            'Kontak Darurat',
            'Status',
            'Catatan HRD',
            'Tanggal Diajukan',
            'Disetujui Oleh',
        ];
    }

    public function map($cuti): array
    {
        return [
            $cuti->nomor_surat ?? '-',
            $cuti->user->nip ?? '-',
            $cuti->user->name ?? '-',
            $cuti->user->divisi ?? '-',
            strtoupper($cuti->user->pabrik ?? '-'),
            $cuti->tanggal_mulai ? date('d-m-Y', strtotime($cuti->tanggal_mulai)) : '-',
            $cuti->tanggal_selesai ? date('d-m-Y', strtotime($cuti->tanggal_selesai)) : '-',
            $cuti->jumlah_hari . ' Hari',
            preg_replace('/\s+/', ' ', $cuti->alasan),
            "'" . ($cuti->kontak_darurat ?? '-'),
            strtoupper($cuti->status),
            $cuti->catatan_hrd ?? '-',
            $cuti->created_at ? $cuti->created_at->format('d-m-Y H:i') : '-',
            $cuti->hrd->name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}