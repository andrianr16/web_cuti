<?php

namespace App\Http\Controllers;

use App\Models\CutiRequest;
use App\Models\User;
use App\Imports\KaryawanImport;
use App\Exports\RekapCutiExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;


class HrdCutiController extends Controller
{
    // Menampilkan daftar pengajuan cuti yang masuk
    public function index()
    {
        $pengajuanCuti = CutiRequest::with('user')
            ->latest()
            ->paginate(10);

        return view('hrd.cuti.index', compact('pengajuanCuti'));
    }

    // Memproses ACC / Persetujuan Cuti
    public function approve(Request $request, CutiRequest $cuti)
    {
        if ($cuti->status !== 'pending') {
            return back()->with('error', 'Pengajuan cuti ini sudah pernah diproses sebelumnya.');
        }

        $karyawan = $cuti->user;

        // Validasi ketersediaan saldo cuti
        if ($karyawan->sisa_cuti < $cuti->jumlah_hari) {
            return back()->with('error', 'Sisa kuota cuti karyawan tidak mencukupi untuk disetujui.');
        }

        DB::transaction(function () use ($cuti, $karyawan, $request) {
            // 1. Kurangi sisa kuota cuti karyawan
            $karyawan->decrement('sisa_cuti', $cuti->jumlah_hari);

            // 2. Format nomor surat: CUTI/YYYYMM/000X
            $nomorSurat = 'CUTI/' . date('Ym') . '/' . str_pad($cuti->id, 4, '0', STR_PAD_LEFT);

            // 3. Update status cuti
            $cuti->update([
                'status'       => 'approved',
                'nomor_surat'  => $nomorSurat,
                'hrd_id'       => auth()->id(),
                'catatan_hrd'  => $request->input('catatan_hrd'),
                'approved_at'  => now(),
            ]);
        });

        return back()->with('success', 'Pengajuan cuti berhasil disetujui (ACC). Saldo cuti karyawan telah terpotong.');
    }

    // Memproses Penolakan Cuti
    public function reject(Request $request, CutiRequest $cuti)
    {
        $request->validate([
            'catatan_hrd' => 'required|string|max:500',
        ], [
            'catatan_hrd.required' => 'Wajib memberikan alasan atau catatan penolakan.',
        ]);

        if ($cuti->status !== 'pending') {
            return back()->with('error', 'Pengajuan cuti ini sudah pernah diproses sebelumnya.');
        }

        $cuti->update([
            'status'      => 'rejected',
            'hrd_id'      => auth()->id(),
            'catatan_hrd' => $request->catatan_hrd,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan cuti telah ditolak.');
    }

    public function dashboardKaryawan(Request $request)
    {
        // Ambil parameter filter & pencarian
        $pabrik   = $request->get('pabrik', 'semua');
        $divisi   = $request->get('divisi', 'semua');
        $kategori = $request->get('kategori', 'semua');
        $jabatan  = $request->get('jabatan', 'semua');
        $cariNama = $request->get('nama');
        $cariNip  = $request->get('nip');

        $query = User::where('role', 'karyawan');

        // 1. Filter Pabrik
        if (!empty($pabrik) && $pabrik !== 'semua') {
            $query->where('pabrik', strtolower($pabrik));
        }

        // 2. Filter Divisi
        if (!empty($divisi) && $divisi !== 'semua') {
            $query->where('divisi', $divisi);
        }

        // 3. Filter Kategori (Staff / TKL)
        if (!empty($kategori) && $kategori !== 'semua') {
            $query->where('kategori', strtolower($kategori));
        }

        // 4. Filter Jabatan
        if (!empty($jabatan) && $jabatan !== 'semua') {
            $query->where('jabatan', $jabatan);
        }

        // 5. Search by Nama
        if (!empty($cariNama)) {
            $query->where('name', 'like', '%' . trim($cariNama) . '%');
        }

        // 6. Search by NIP
        if (!empty($cariNip)) {
            $query->where('nip', 'like', '%' . trim($cariNip) . '%');
        }

        // Ambil data hasil filter
        $karyawans = $query->orderBy('pabrik')->orderBy('divisi')->orderBy('name')->get();

        // Dapatkan list unik untuk opsi pilihan dropdown
        $listDivisi  = User::where('role', 'karyawan')->whereNotNull('divisi')->where('divisi', '!=', '')->distinct()->pluck('divisi')->sort();
        $listJabatan = User::where('role', 'karyawan')->whereNotNull('jabatan')->where('jabatan', '!=', '')->distinct()->pluck('jabatan')->sort();

        // UBAH BAGIAN INI: Kelompokkan per PABRIK
        $karyawanPerPabrik = $karyawans->groupBy(function($item) {
            return strtolower($item->pabrik ?: 'belum_ditentukan');
        });

        return view('hrd.dashboard_karyawan', compact(
            'karyawanPerPabrik', // Kirim variabel baru ini ke view
            'listDivisi', 
            'listJabatan', 
            'pabrik', 
            'divisi', 
            'kategori', 
            'jabatan', 
            'cariNama', 
            'cariNip',
            'karyawans'
        ));
    }

    // Method untuk HRD melakukan Reset Cuti Manual per pabrik / semua pabrik
    public function resetCutiMassal(Request $request)
    {
        $pabrik = $request->input('pabrik');

        $query = \App\Models\User::where('role', 'karyawan');
        if ($pabrik && $pabrik !== 'semua') {
            $query->where('pabrik', $pabrik);
        }

        $query->update([
            'sisa_cuti_lalu' => \DB::raw('sisa_cuti'),
            'sisa_cuti' => 12,
        ]);

        return back()->with('success', 'Hak cuti tahunan berhasil direset menjadi 12 hari!');
    }

    public function createKaryawan()
    {
        return view('hrd.karyawan.create');
    }

    // Menyimpan data karyawan baru ke database
    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'nip'             => 'required|string|unique:users,nip|max:30',
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:users,email|max:100',
            'password'        => 'required|string|min:6',
            'pabrik'          => 'required|in:supra,joya,minyak,seg',
            'kategori'        => 'required|in:staff,tkl',
            'divisi'          => 'required|string|max:50',
            'jabatan'         => 'required|string|max:50',
            'tgl_masuk_kerja' => 'required|date',
            'sisa_cuti'       => 'required|integer|min:0',
        ], [
            'nip.unique'   => 'NIP sudah terdaftar untuk karyawan lain.',
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'nip'             => $request->nip,
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'role'            => 'karyawan',
            'pabrik'          => $request->pabrik,
            'kategori'        => $request->kategori,
            'divisi'          => $request->divisi,
            'jabatan'         => $request->jabatan,
            'tgl_masuk_kerja' => $request->tgl_masuk_kerja,
            'sisa_cuti'       => $request->sisa_cuti,
            'sisa_cuti_lalu'  => 0,
        ]);

        return redirect()->route('hrd.karyawan.index', ['pabrik' => $request->pabrik])
            ->with('success', 'Karyawan baru berhasil didaftarkan ke sistem!');
    }

    // Menampilkan form edit karyawan
    public function editKaryawan(User $karyawan)
    {
        return view('hrd.karyawan.edit', compact('karyawan'));
    }

    // Menyimpan pembaruan data karyawan
    public function updateKaryawan(Request $request, User $karyawan)
    {
        $request->validate([
            'nip'             => 'required|string|max:30|unique:users,nip,' . $karyawan->id,
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|max:100|unique:users,email,' . $karyawan->id,
            'pabrik'          => 'required|in:supra,joya,minyak,seg',
            'kategori'        => 'required|in:staff,tkl',
            'divisi'          => 'required|string|max:50',
            'jabatan'         => 'required|string|max:50',
            'tgl_masuk_kerja' => 'required|date',
            'sisa_cuti'       => 'required|integer|min:0',
        ]);

        $data = [
            'nip'             => $request->nip,
            'name'            => $request->name,
            'email'           => $request->email,
            'pabrik'          => $request->pabrik,
            'kategori'        => $request->kategori,
            'divisi'          => $request->divisi,
            'jabatan'         => $request->jabatan,
            'tgl_masuk_kerja' => $request->tgl_masuk_kerja,
            'sisa_cuti'       => $request->sisa_cuti,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = bcrypt($request->password);
        }

        $karyawan->update($data);

        return redirect()->route('hrd.karyawan.index', ['pabrik' => $karyawan->pabrik])
            ->with('success', "Data karyawan {$karyawan->name} berhasil diperbarui!");
    }

    public function destroy(User $karyawan)
    {
        // Cegah HRD menghapus akunnya sendiri
        if ($karyawan->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Pastikan hanya akun role karyawan yang dihapus lewat menu ini
        if ($karyawan->role !== 'karyawan') {
            return back()->with('error', 'Hanya akun karyawan yang dapat dihapus.');
        }

        $nama = $karyawan->name;
        $karyawan->delete();

        return redirect()->route('hrd.karyawan.index')
            ->with('success', "Akun karyawan {$nama} berhasil dihapus dari sistem.");
    }

    public function importKaryawan(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|max:5120',
        ]);

        $file = $request->file('file_excel');
        $path = $file->getRealPath();

        // 1. Baca seluruh baris file CSV
        $rows = array_map(function($line) {
            // Hapus BOM UTF-8 jika file disimpan dari Excel Windows
            $bom = pack('H*', 'EFBBBF');
            $line = preg_replace("/^$bom/", '', $line);
            return $line;
        }, file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

        if (count($rows) <= 1) {
            return back()->with('error', 'File CSV kosong atau hanya berisi baris header.');
        }

        // 2. Deteksi pemisah (koma atau titik koma)
        $delimiter = str_contains($rows[0], ';') ? ';' : ',';

        // 3. Ambil baris pertama sebagai header dan bersihkan nama kolom
        $headerRaw = str_getcsv(array_shift($rows), $delimiter);
        $headers = array_map(function($col) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', $col)));
        }, $headerRaw);

        $berhasil = 0;
        $barisError = [];

        foreach ($rows as $index => $rowString) {
            $row = str_getcsv($rowString, $delimiter);
            
            // Lewati jika baris kosong
            if (empty(array_filter($row))) {
                continue;
            }

            // Samakan jumlah kolom jika ada kolom kosong di ujung baris
            if (count($row) < count($headers)) {
                $row = array_pad($row, count($headers), '');
            }

            $data = array_combine($headers, array_slice($row, 0, count($headers)));

            $nip   = trim($data['nip'] ?? '');
            $email = trim($data['email'] ?? '');

            // NIP dan Email wajib ada
            if (empty($nip) || empty($email)) {
                $barisError[] = 'Baris ke-' . ($index + 2) . ' (NIP atau Email kosong)';
                continue;
            }

            // Format tanggal masuk kerja
            $tglMasuk = now()->toDateString();
            if (!empty($data['tgl_masuk_kerja'])) {
                try {
                    $tglMasuk = Carbon::parse(trim($data['tgl_masuk_kerja']))->format('Y-m-d');
                } catch (\Exception $e) {
                    $tglMasuk = now()->toDateString();
                }
            }

            User::updateOrCreate(
                ['nip' => $nip],
                [
                    'name'            => trim($data['nama'] ?? $data['name'] ?? 'Karyawan'),
                    'email'           => $email,
                    'password'        => !empty($data['password']) ? Hash::make(trim($data['password'])) : Hash::make('password123'),
                    'role'            => 'karyawan',
                    'pabrik'          => strtolower(trim($data['pabrik'] ?? 'supra')),
                    'kategori'        => strtolower(trim($data['kategori'] ?? 'staff')),
                    'divisi'          => trim($data['divisi'] ?? 'Umum'),
                    'jabatan'         => trim($data['jabatan'] ?? 'Staff'),
                    'tgl_masuk_kerja' => $tglMasuk,
                    'sisa_cuti'       => isset($data['sisa_cuti']) && is_numeric($data['sisa_cuti']) ? (int)$data['sisa_cuti'] : 12,
                    'sisa_cuti_lalu'  => isset($data['sisa_cuti_lalu']) && is_numeric($data['sisa_cuti_lalu']) ? (int)$data['sisa_cuti_lalu'] : 0,
                ]
            );

            $berhasil++;
        }

        if ($berhasil === 0) {
            return back()->with('error', 'Tidak ada data yang berhasil diimpor. Penyebab: ' . implode(', ', $barisError));
        }

        $pesan = "Berhasil mengimpor/memperbarui {$berhasil} data karyawan!";
        if (!empty($barisError)) {
            $pesan .= " Namun baris berikut dilewati: " . implode(', ', $barisError);
        }

        return back()->with('success', $pesan);
    }

    // Download template Excel kosong berformat CSV
    public function downloadTemplateExcel()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_karyawan.csv"',
        ];

        $columns = ['nip', 'nama', 'email', 'password', 'pabrik', 'kategori', 'divisi', 'jabatan', 'tgl_masuk_kerja', 'sisa_cuti'];
        $sample = ['KRY0099', 'Ahmad Subarjo', 'ahmad@perusahaan.com', 'password123', 'supra', 'staff', 'Logistik', 'Staff Gudang', '2024-01-15', '12'];

        $callback = function () use ($columns, $sample) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $sample);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportKaryawanCsv(Request $request)
    {
        $pabrik = $request->get('pabrik', 'semua');
        $query = User::where('role', 'karyawan');

        if ($pabrik !== 'semua') {
            $query->where('pabrik', $pabrik);
        }

        $karyawans = $query->orderBy('pabrik')->orderBy('divisi')->orderBy('name')->get();
        $fileName = 'Data_Karyawan_' . ($pabrik === 'semua' ? 'SEMUA_PABRIK' : strtoupper($pabrik)) . '_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($karyawans) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // BOM UTF-8

            fputcsv($handle, ['NIP', 'Nama Karyawan', 'Email', 'Pabrik', 'Kategori', 'Divisi', 'Jabatan', 'Tgl Masuk Kerja', 'Sisa Cuti Lalu', 'Sisa Hak Cuti']);

            foreach ($karyawans as $k) {
                fputcsv($handle, [
                    $k->nip ?? '-',
                    $k->name,
                    $k->email,
                    strtoupper($k->pabrik ?? '-'),
                    strtoupper($k->kategori ?? '-'),
                    $k->divisi ?? '-',
                    $k->jabatan ?? '-',
                    $k->tgl_masuk_kerja ? date('d/m/Y', strtotime($k->tgl_masuk_kerja)) : '-',
                    $k->sisa_cuti_lalu ?? 0,
                    $k->sisa_cuti,
                ]);
            }
            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    // Ekspor PDF
    public function exportKaryawanPdf(Request $request)
    {
        $pabrik = $request->get('pabrik', 'semua');
        $query = User::where('role', 'karyawan');

        if ($pabrik !== 'semua') {
            $query->where('pabrik', $pabrik);
        }

        $karyawans = $query->orderBy('pabrik')->orderBy('divisi')->orderBy('name')->get();

        $pdf = Pdf::loadView('hrd.pdf_karyawan', compact('karyawans', 'pabrik'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Data_Karyawan_' . ($pabrik === 'semua' ? 'SEMUA_PABRIK' : strtoupper($pabrik)) . '_' . date('Ymd') . '.pdf');
    }

    // 3. Ekspor Laporan Rekap Permohonan Cuti ke CSV/Excel
    public function exportCutiCsv(Request $request)
    {
        $status = $request->get('status', 'semua');
        $pabrik = $request->get('pabrik', 'semua');
        $tahun  = $request->get('tahun', date('Y'));
        $bulan  = $request->get('bulan', 'semua');

        $namaPabrik = ($pabrik !== 'semua') ? strtoupper($pabrik) . '_' : '';
        $namaPeriode = ($bulan !== 'semua') ? $tahun . sprintf('%02d', $bulan) : $tahun;
        $fileName = 'Rekap_Permohonan_Cuti_' . $namaPabrik . $namaPeriode . '_' . date('His') . '.xlsx';

        return Excel::download(new RekapCutiExport($status, $pabrik, $tahun, $bulan), $fileName);
    }
}