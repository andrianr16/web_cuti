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

        // Pastikan SPV hanya menarik permohonan dari karyawan yang divisinya sama persis
        $pengajuanCuti = CutiRequest::where('status', 'pending_spv')
            ->whereHas('user', function ($query) use ($user) {
                $query->where('divisi', $user->divisi);
            })
            ->with('user')
            ->latest()
            ->get();

        // Riwayat keputusan yang pernah diproses oleh SPV ini
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
        $spv = auth()->user();

        // Validasi: Cegah SPV memproses permohonan dari divisi lain
        if ($cuti->user->divisi !== $spv->divisi) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk memproses permohonan dari divisi lain.');
        }

        if ($cuti->status !== 'pending_spv') {
            return back()->with('error', 'Permohonan sudah tidak dalam status menunggu Supervisor.');
        }

        if (empty($spv->signature_pad)) {
            return back()->with('error', 'Anda belum mengatur tanda tangan digital di menu Profil! Silakan isi terlebih dahulu.');
        }

        $cuti->update([
            'status'          => 'pending_hrd',
            'spv_id'          => $spv->id,
            'ttd_spv'         => $spv->signature_pad,
            'spv_approved_at' => now(),
            'catatan_spv'     => $request->input('catatan_spv', 'Disetujui oleh Supervisor'),
        ]);

        return back()->with('success', 'Permohonan cuti disetujui dan diteruskan ke HRD!');
    }

    public function reject(Request $request, CutiRequest $cuti)
    {
        $spv = auth()->user();

        // Validasi: Cegah SPV menolak permohonan dari divisi lain
        if ($cuti->user->divisi !== $spv->divisi) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk memproses permohonan dari divisi lain.');
        }

        if ($cuti->status !== 'pending_spv') {
            return back()->with('error', 'Permohonan sudah diproses.');
        }

        $request->validate([
            'catatan_spv' => 'required|string|min:3',
        ], [
            'catatan_spv.required' => 'Wajib memberikan alasan penolakan untuk karyawan.',
        ]);

        $cuti->update([
            'status'      => 'rejected_spv',
            'spv_id'      => $spv->id,
            'catatan_spv' => $request->catatan_spv,
        ]);

        return back()->with('success', 'Permohonan cuti telah ditolak.');
    }
}