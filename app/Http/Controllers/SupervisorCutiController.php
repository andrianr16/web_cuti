<?php

namespace App\Http\Controllers;

use App\Models\CutiRequest;
use Illuminate\Http\Request;

class SupervisorCutiController extends Controller
{
    // Menampilkan daftar permohonan yang menunggu persetujuan SPV
    public function index()
    {
        $user = auth()->user();

        // SPV melihat pengajuan dari divisi yang sama dan berstatus 'pending_spv'
        $pengajuanCuti = CutiRequest::where('status', 'pending_spv')->with('user')->latest()->get();

        // Riwayat permohonan yang sudah pernah diproses oleh SPV ini
        $riwayatSpv = CutiRequest::where('spv_id', $user->id)
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        return view('supervisor.index', compact('pengajuanCuti', 'riwayatSpv'));
    }

    // Aksi ACC oleh Supervisor
    public function approve(Request $request, CutiRequest $cuti)
    {
        if ($cuti->status !== 'pending_spv') {
            return back()->with('error', 'Permohonan sudah tidak dalam status menunggu Supervisor.');
        }

        $spv = auth()->user();

        // Validasi tanda tangan profil SPV
        if (empty($spv->signature_pad)) {
            return back()->with('error', 'Anda belum mengatur tanda tangan digital di menu Profil! Silakan isi terlebih dahulu.');
        }

        // Teruskan ke HRD
        $cuti->update([
            'status'          => 'pending_hrd',
            'spv_id'          => $spv->id,
            'ttd_spv'         => $spv->signature_pad,
            'spv_approved_at' => now(),
            'catatan_spv'     => $request->input('catatan_spv', 'Disetujui oleh Supervisor'),
        ]);

        return back()->with('success', 'Permohonan cuti disetujui dan diteruskan ke HRD!');
    }

    // Aksi Tolak oleh Supervisor
    public function reject(Request $request, CutiRequest $cuti)
    {
        if ($cuti->status !== 'pending_spv') {
            return back()->with('error', 'Permohonan sudah diproses.');
        }

        $request->validate([
            'catatan_spv' => 'required|string|min:3',
        ], [
            'catatan_spv.required' => 'Wajib memberikan alasan penolakan untuk karyawan.',
        ]);

        // Berhenti di sini, tidak masuk ke HRD
        $cuti->update([
            'status'      => 'rejected_spv',
            'spv_id'      => auth()->id(),
            'catatan_spv' => $request->catatan_spv,
        ]);

        return back()->with('success', 'Permohonan cuti telah ditolak.');
    }
}