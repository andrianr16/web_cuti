<?php

namespace App\Http\Controllers;

use App\Models\CutiRequest;
use App\Models\Holiday;
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

    public function store(Request $request)
    {
        $user = auth()->user();

        // 1. Validasi Input: Alasan dibuat MANDATORY (Wajib diisi & minimal 5 karakter)
        $request->validate([
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan'          => 'required|string|min:5|max:500',
            'kontak_darurat'  => 'required|string|max:50',
            'ttd_karyawan'    => 'required|string',
        ], [
            'alasan.required' => 'Alasan cuti wajib diisi (tidak boleh kosong).',
            'alasan.min'      => 'Alasan cuti terlalu pendek, minimal 5 karakter.',
            'ttd_karyawan.required' => 'Tanda tangan digital wajib dicantumkan.',
        ]);

        $mulai = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);

        // Ambil daftar hari libur resmi pada rentang tanggal yang diajukan
        $daftarLibur = Holiday::whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->get()
            ->keyBy('tanggal');

        $jumlahHari = 0;
        $period = CarbonPeriod::create($mulai, $selesai);

        foreach ($period as $date) {
            $tglStr = $date->toDateString();

            // Abaikan akhir pekan (Sabtu & Minggu)
            if ($date->isWeekend()) {
                continue;
            }

            // Cek apakah tanggal ini terdaftar di kalender libur
            if ($daftarLibur->has($tglStr)) {
                $libur = $daftarLibur->get($tglStr);

                // Tanggal merah / Libur nasional: Tidak memotong jatah cuti tahunan
                if ($libur->jenis === 'libur_nasional') {
                    continue;
                }

                // Cuti bersama: Tetap dihitung memotong kuota cuti tahunan
                if ($libur->jenis === 'cuti_bersama') {
                    $jumlahHari++;
                    continue;
                }
            }

            // Hari kerja biasa
            $jumlahHari++;
        }

        if ($jumlahHari === 0) {
            return back()
                ->withInput()
                ->with('error', 'Rentang tanggal yang Anda pilih seluruhnya adalah hari libur atau akhir pekan. Tidak ada hari kerja yang dipotong.');
        }

        // Validasi kuota sisa cuti karyawan
        if ($user->sisa_cuti < $jumlahHari) {
            return back()
                ->withInput()
                ->with('error', "Sisa cuti Anda tidak mencukupi! Anda mengajukan {$jumlahHari} hari kerja (termasuk cuti bersama), sisa cuti Anda {$user->sisa_cuti} hari.");
        }

        // Simpan pengajuan (masuk antrean Supervisor terlebih dahulu)
        CutiRequest::create([
            'user_id'           => $user->id,
            'tanggal_mulai'     => $request->tanggal_mulai,
            'tanggal_selesai'   => $request->tanggal_selesai,
            'jumlah_hari'       => $jumlahHari,
            'alasan'            => $request->alasan,
            'alamat_cuti'       => $request->alamat_cuti,
            'kontak_darurat'    => $request->kontak_darurat,
            'ttd_karyawan'      => $request->ttd_karyawan,
            'status'            => 'pending_spv',
        ]);

        return redirect()->route('cuti.index')->with('success', "Permohonan cuti sebanyak {$jumlahHari} hari kerja berhasil dikirim ke Supervisor!");
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