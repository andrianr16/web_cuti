<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Persetujuan Cuti Karyawan (HRD)') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Notifikasi -->
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-5 bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Ekspor Rekapitulasi Data Cuti</div>
                <form action="{{ route('hrd.cuti.export.csv') }}" method="GET" class="flex flex-wrap items-end gap-3">
                                
                    <!-- Filter Tahun -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tahun</label>
                        <select name="tahun" class="rounded-lg border-gray-300 text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                            <option value="semua">Semua Tahun</option>
                            @for ($y = date('Y'); $y >= 2023; $y--)
                                <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Filter Bulan -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Bulan</label>
                        <select name="bulan" class="rounded-lg border-gray-300 text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                            <option value="semua">Semua Bulan (1 Tahun Penuh)</option>
                            <option value="1" {{ request('bulan') == '1' ? 'selected' : '' }}>Januari</option>
                            <option value="2" {{ request('bulan') == '2' ? 'selected' : '' }}>Februari</option>
                            <option value="3" {{ request('bulan') == '3' ? 'selected' : '' }}>Maret</option>
                            <option value="4" {{ request('bulan') == '4' ? 'selected' : '' }}>April</option>
                            <option value="5" {{ request('bulan') == '5' ? 'selected' : '' }}>Mei</option>
                            <option value="6" {{ request('bulan') == '6' ? 'selected' : '' }}>Juni</option>
                            <option value="7" {{ request('bulan') == '7' ? 'selected' : '' }}>Juli</option>
                            <option value="8" {{ request('bulan') == '8' ? 'selected' : '' }}>Agustus</option>
                            <option value="9" {{ request('bulan') == '9' ? 'selected' : '' }}>September</option>
                            <option value="10" {{ request('bulan') == '10' ? 'selected' : '' }}>Oktober</option>
                            <option value="11" {{ request('bulan') == '11' ? 'selected' : '' }}>November</option>
                            <option value="12" {{ request('bulan') == '12' ? 'selected' : '' }}>Desember</option>
                        </select>
                    </div>

                    <!-- Filter Pabrik -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Pabrik</label>
                        <select name="pabrik" class="rounded-lg border-gray-300 text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                            <option value="semua">Semua Pabrik</option>
                            <option value="supra" {{ request('pabrik') == 'supra' ? 'selected' : '' }}>Supra</option>
                            <option value="joya" {{ request('pabrik') == 'joya' ? 'selected' : '' }}>Joya</option>
                            <option value="minyak" {{ request('pabrik') == 'minyak' ? 'selected' : '' }}>Minyak</option>
                            <option value="seg" {{ request('pabrik') == 'seg' ? 'selected' : '' }}>SEG</option>
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="rounded-lg border-gray-300 text-xs shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50">
                            <option value="semua">Semua Status</option>
                            <option value="approved">Approved (ACC)</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                        <!-- Tombol Download -->
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Unduh Laporan
                    </button>
                </form>
            </div>

            <!-- 1. TABEL PERMOHONAN MASUK MENUNGGU HRD -->
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        📋 Permohonan Masuk Menunggu Persetujuan HRD
                        <span class="bg-blue-100 text-blue-800 text-[11px] px-2 py-0.5 rounded-full font-bold">
                            {{ $pengajuanCuti->count() }} Permohonan
                        </span>
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-600 uppercase font-semibold text-[11px]">
                            <tr>
                                <th class="py-2.5 px-3 text-left">Pegawai</th>
                                <th class="py-2.5 px-3 text-center">Sisa Cuti</th>
                                <th class="py-2.5 px-3 text-left">Periode Cuti</th>
                                <th class="py-2.5 px-3 text-left">Alasan</th>
                                <th class="py-2.5 px-3 text-center">TTD Pegawai</th>
                                <th class="py-2.5 px-3 text-center">Persetujuan SPV</th>
                                <th class="py-2.5 px-3 text-center w-48">Aksi HRD</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pengajuanCuti as $cuti)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2.5 px-3">
                                        <div class="font-bold text-gray-900">{{ $cuti->user->name ?? '-' }}</div>
                                        <div class="text-[11px] text-gray-500">NIP: {{ $cuti->user->nip ?? '-' }} | {{ $cuti->user->divisi ?? '-' }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="font-bold text-blue-600">{{ $cuti->user->sisa_cuti }} Hari</span>
                                    </td>
                                    <td class="py-2.5 px-3 whitespace-nowrap">
                                        <div>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</div>
                                        <div class="text-[11px] font-semibold text-gray-700">{{ $cuti->jumlah_hari }} Hari Kerja</div>
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-700 max-w-[180px] truncate" title="{{ $cuti->alasan }}">
                                        {{ $cuti->alasan }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        @if(!empty($cuti->ttd_karyawan))
                                            <img src="{{ $cuti->ttd_karyawan }}" alt="TTD" class="h-7 mx-auto border border-gray-200 rounded bg-white px-1 py-0.5 object-contain">
                                        @else
                                            <span class="text-gray-400 italic text-[10px]">-</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold rounded">
                                            ✓ ACC SPV
                                        </span>
                                        <div class="text-[10px] text-gray-500 mt-0.5">{{ $cuti->spv->name ?? 'Supervisor' }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5" id="btn-group-{{ $cuti->id }}">
                                            <!-- Tombol ACC -->
                                            <form action="{{ route('hrd.cuti.approve', $cuti->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menyetujui pengajuan cuti ini? Kuota cuti akan dipotong.');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded text-xs shadow-sm">
                                                    ✓ ACC
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak -->
                                            <button type="button" onclick="openHrdReject('{{ $cuti->id }}')" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded text-xs shadow-sm">
                                                ✕ Tolak
                                            </button>
                                        </div>

                                        <!-- Form Input Alasan Tolak (Ramping & Dropdown) -->
                                        <div id="hrd-reject-box-{{ $cuti->id }}" class="hidden mt-2 p-2 bg-red-50 border border-red-200 rounded text-left">
                                            <form action="{{ route('hrd.cuti.reject', $cuti->id) }}" method="POST">
                                                @csrf
                                                <input type="text" name="catatan_hrd" required placeholder="Alasan penolakan HRD..." 
                                                    class="w-full text-xs rounded border-gray-300 py-1 px-2 mb-1.5 focus:border-red-500 focus:ring-red-500">
                                                <div class="flex justify-end gap-1">
                                                    <button type="button" onclick="closeHrdReject('{{ $cuti->id }}')" class="px-2 py-0.5 text-[11px] bg-gray-200 hover:bg-gray-300 text-gray-700 rounded">
                                                        Batal
                                                    </button>
                                                    <button type="submit" class="px-2 py-0.5 text-[11px] bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded">
                                                        Konfirmasi Tolak
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-400 text-xs">
                                        Tidak ada pengajuan cuti yang menunggu peninjauan HRD.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            <form action="{{ route('hrd.cuti.index') }}" method="GET" class="flex items-center gap-2 mb-4">
                <div class="relative w-full max-w-xs">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari nama karyawan / NIP..." 
                        class="w-full text-xs rounded-lg border-gray-300 pl-8 pr-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('hrd.cuti.index') }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-xs font-semibold">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Modal Card Detail Pengajuan Cuti -->
    <div id="detailModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs hidden flex items-center justify-center z-50 p-4 transition-all">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-gray-100 transform transition-all">
            
            <!-- Header Card -->
            <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">📄</span>
                    <div>
                        <h3 class="text-sm font-bold tracking-wide uppercase">Detail Permohonan Cuti</h3>
                        <p class="text-[11px] text-slate-300 font-mono" id="modalNomorSurat">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-white text-lg font-bold p-1">&times;</button>
            </div>

            <!-- Konten Card -->
            <div class="p-6 space-y-4 text-xs text-gray-700">
                
                <!-- Ringkasan Pemohon -->
                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200 grid grid-cols-2 gap-3">
                    <div>
                        <span class="text-gray-400 block uppercase text-[10px] font-bold">Nama Karyawan</span>
                        <strong class="text-gray-900 text-sm" id="modalNama">-</strong>
                        <div class="text-gray-500 text-[11px]" id="modalNipDivisi">-</div>
                    </div>
                    <div>
                        <span class="text-gray-400 block uppercase text-[10px] font-bold">Durasi Cuti</span>
                        <strong class="text-blue-600 text-sm" id="modalDurasi">-</strong>
                        <div class="text-gray-500 text-[11px]" id="modalPeriode">-</div>
                    </div>
                </div>

                <!-- Alasan Cuti Lengkap -->
                <div>
                    <span class="text-gray-500 block uppercase text-[10px] font-bold mb-1.5">Alasan / Keperluan Cuti Lengkap:</span>
                    <div class="p-3.5 bg-blue-50/50 border border-blue-100 rounded-xl text-gray-800 leading-relaxed max-h-48 overflow-y-auto whitespace-pre-line text-[13px]" id="modalAlasan">
                        -
                    </div>
                </div>

                <!-- Kontak Darurat -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100 text-[11px]">
                    <span class="text-gray-500 font-medium">Kontak Darurat Selama Cuti:</span>
                    <strong class="text-gray-800 font-mono" id="modalKontak">-</strong>
                </div>

            </div>

            <!-- Footer Card -->
            <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- Modal Penolakan Cuti -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tolak Pengajuan Cuti</h3>
            <p class="text-sm text-gray-600 mb-4" id="rejectModalDesc">Masukkan alasan penolakan permohonan cuti ini.</p>
            
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan / Alasan Penolakan</label>
                    <textarea name="catatan_hrd" rows="3" required placeholder="Contoh: Pekerjaan mendesak / tanggal bentrok dengan tim lain"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 text-white text-sm font-semibold rounded-md hover:bg-rose-700">
                        Konfirmasi Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. TABEL RIWAYAT PERMOHONAN CUTI (ACC / TOLAK) -->
    <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-5">
        <div class="border-b pb-3 mb-4">
            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                📑 Riwayat Keputusan Cuti (Disetujui & Ditolak)
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-600 uppercase font-semibold text-[11px]">
                    <tr>
                        <th class="py-2.5 px-3 text-left">Pegawai</th>
                        <th class="py-2.5 px-3 text-left">Periode Cuti</th>
                        <th class="py-2.5 px-2 text-center">Durasi</th>
                        <th class="py-2.5 px-3 text-left">Alasan Pengajuan</th>
                        <th class="py-2.5 px-3 text-center">Status Keputusan</th>
                        <th class="py-2.5 px-3 text-left">Catatan / Keterangan</th>
                        <th class="py-2.5 px-3 text-center">No. Surat / Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayatCuti as $riwayat)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-gray-900">{{ $riwayat->user->name ?? '-' }}</div>
                                <div class="text-[11px] text-gray-500">NIP: {{ $riwayat->user->nip ?? '-' }}</div>
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap text-gray-700">
                                {{ \Carbon\Carbon::parse($riwayat->tanggal_mulai)->format('d/m/Y') }} s.d {{ \Carbon\Carbon::parse($riwayat->tanggal_selesai)->format('d/m/Y') }}
                            </td>
                            <td class="py-2.5 px-2 text-center font-semibold text-blue-600">
                                {{ $riwayat->jumlah_hari }} Hari
                            </td>
                            <td class="py-2.5 px-3 text-gray-600 max-w-[150px] truncate" title="{{ $riwayat->alasan }}">
                                {{ $riwayat->alasan }}
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                @if($riwayat->status === 'approved')
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold rounded-full text-[10px]">
                                        ✓ Disetujui HRD
                                    </span>
                                @elseif($riwayat->status === 'rejected')
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-bold rounded-full text-[10px]">
                                        ✕ Ditolak HRD
                                    </span>
                                @elseif($riwayat->status === 'rejected_spv')
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 font-bold rounded-full text-[10px]">
                                        ✕ Ditolak SPV
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-gray-600 max-w-[180px]">
                                @if($riwayat->status === 'rejected')
                                    <span class="text-rose-600 text-[11px]">{{ $riwayat->catatan_hrd ?? '-' }}</span>
                                @elseif($riwayat->status === 'rejected_spv')
                                    <span class="text-amber-700 text-[11px]">{{ $riwayat->catatan_spv ?? '-' }}</span>
                                @else
                                    <span class="text-emerald-700 text-[11px]">{{ $riwayat->catatan_hrd ?? 'Disetujui' }}</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                @if($riwayat->status === 'approved')
                                    <span class="font-mono text-[11px] font-semibold text-gray-700 block mb-1">
                                        {{ $riwayat->nomor_surat ?? '-' }}
                                    </span>
                                    <a href="{{ route('cuti.pdf', $riwayat->id) }}" target="_blank" class="inline-flex items-center px-2 py-0.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded text-[10px] font-bold">
                                        📄 Cetak PDF
                                    </a>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-400 text-xs">
                                Belum ada riwayat keputusan cuti.
                            </td>
                        </tr>
                     @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $riwayatCuti->links() }}
        </div>
    </div>

    <script>
        function showRejectModal(id, nama) {
            const form = document.getElementById('rejectForm');
            form.action = `/hrd/cuti/${id}/reject`;
            document.getElementById('rejectModalDesc').innerText = `Penolakan cuti untuk: ${nama}`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        function showDetailModal(cuti, user) {
            document.getElementById('modalNama').innerText = user.name;
            document.getElementById('modalNipDivisi').innerText = `NIP: ${user.nip ?? '-'} | Divisi: ${user.divisi ?? '-'}`;
            document.getElementById('modalNomorSurat').innerText = cuti.nomor_surat ? `No: ${cuti.nomor_surat}` : 'Status: Menunggu Persetujuan';
            
            // Format tanggal
            document.getElementById('modalPeriode').innerText = `${cuti.tanggal_mulai} s/d ${cuti.tanggal_selesai}`;
            document.getElementById('modalDurasi').innerText = `${cuti.jumlah_hari} Hari Kerja`;
            
            // Alasan penuh & kontak
            document.getElementById('modalAlasan').innerText = cuti.alasan;
            document.getElementById('modalKontak').innerText = cuti.kontak_darurat;

            // Tampilkan modal card
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</x-app-layout>