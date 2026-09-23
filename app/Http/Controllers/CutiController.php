<?php

namespace App\Http\Controllers;

use App\Models\CutiRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;

class CutiController extends Controller
{
    // Menampilkan halaman formulir pengajuan & histori cuti karyawan
    public function index()
    {
        $user = auth()->user();
        $riwayatCuti = CutiRequest::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('cuti.index', compact('user', 'riwayatCuti'));
    }

    // Memproses simpan pengajuan cuti
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string|min:5|max:500',
            'kontak_darurat'  => 'required|string|max:50',
        ], [
            'tanggal_mulai.after_or_equal'   => 'Tanggal mulai cuti minimal hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'alasan.min'                     => 'Alasan cuti minimal 5 karakter.',
        ]);

        $tglMulai = $request->tanggal_mulai;
        $tglSelesai = $request->tanggal_selesai;

        // 1. Cek Tanggal Bentrok (Overlapping) dengan pengajuan aktif sebelumnya
        $cekBentrok = CutiRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($tglMulai, $tglSelesai) {
                $query->whereBetween('tanggal_mulai', [$tglMulai, $tglSelesai])
                    ->orWhereBetween('tanggal_selesai', [$tglMulai, $tglSelesai])
                    ->orWhere(function ($sub) use ($tglMulai, $tglSelesai) {
                        $sub->where('tanggal_mulai', '<=', $tglMulai)
                            ->where('tanggal_selesai', '>=', $tglSelesai);
                    });
            })
            ->exists();

        if ($cekBentrok) {
            return back()
                ->withInput()
                ->with('error', 'Tanggal yang Anda pilih bertabrakan dengan pengajuan cuti Anda yang lain (status pending/disetujui).');
        }

        // 2. Hitung Hanya Hari Kerja (Skip Sabtu & Minggu)
        $periode = CarbonPeriod::create($tglMulai, $tglSelesai);
        $jumlahHariKerja = 0;

        foreach ($periode as $date) {
            // Lewati Sabtu (isSaturday) dan Minggu (isSunday)
            if (! $date->isWeekend()) {
                $jumlahHariKerja++;
            }
        }

        // Jika karyawan hanya memilih hari Sabtu/Minggu
        if ($jumlahHariKerja === 0) {
            return back()
                ->withInput()
                ->with('error', 'Rentang tanggal yang Anda pilih hanya terdiri dari akhir pekan (Sabtu/Minggu).');
        }

        // 3. Validasi Kuota Cuti Karyawan
        if ($user->sisa_cuti < $jumlahHariKerja) {
            return back()
                ->withInput()
                ->with('error', "Sisa cuti Anda tidak mencukupi! Pengajuan ini membutuhkan {$jumlahHariKerja} hari kerja, sisa cuti Anda hanya {$user->sisa_cuti} hari.");
        }

        // 4. Simpan Pengajuan Cuti
        CutiRequest::create([
            'user_id'         => $user->id,
            'tanggal_mulai'   => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'jumlah_hari'     => $jumlahHariKerja,
            'alasan'          => $request->alasan,
            'kontak_darurat'  => $request->kontak_darurat,
            'status'          => 'pending',
        ]);

        return redirect()->route('cuti.index')
            ->with('success', "Permohonan cuti sebanyak {$jumlahHariKerja} hari kerja berhasil dikirim ke HRD!");
    }

    // Menampilkan halaman preview cetak di browser
    public function printSurat(CutiRequest $cuti)
    {
        if (auth()->id() !== $cuti->user_id && auth()->user()->role !== 'hrd') {
            abort(403);
        }

        if ($cuti->status !== 'approved') {
            return back()->with('error', 'Surat cuti hanya bisa dicetak jika sudah disetujui HRD.');
        }

        $logoBase64 = null;
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        $isPdf = false;
        return view('cuti.surat', compact('cuti', 'logoBase64', 'isPdf'));
    }

    // Mengunduh file PDF resmi
    public function exportPdf(CutiRequest $cuti)
    {
        if (auth()->id() !== $cuti->user_id && auth()->user()->role !== 'hrd') {
            abort(403);
        }

        if ($cuti->status !== 'approved') {
            return back()->with('error', 'Surat cuti hanya bisa diunduh jika sudah disetujui HRD.');
        }

        $logoBase64 = null;
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        $isPdf = true; // Menandakan sedang dirender ke PDF
        $pdf = Pdf::loadView('cuti.surat', compact('cuti', 'logoBase64', 'isPdf'))
                  ->setPaper('a4', 'portrait');

        $namaFile = 'Surat_Cuti_' . str_replace(' ', '_', $cuti->user->name) . '_' . date('Ymd') . '.pdf';
        return $pdf->download($namaFile);
    }
}