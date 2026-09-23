<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel HRD - Peninjauan Pengajuan Cuti') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Permohonan Cuti Karyawan</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Karyawan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Sisa Cuti</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Periode Cuti</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Alasan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Tindakan HRD</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pengajuanCuti as $cuti)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $cuti->user->name }}</div>
                                        <div class="text-xs text-gray-500">NIP: {{ $cuti->user->nip ?? '-' }} | {{ $cuti->user->divisi ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="font-semibold text-blue-600">{{ $cuti->user->sisa_cuti }} Hari</span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</div>
                                        <div class="text-xs font-semibold text-gray-700">{{ $cuti->jumlah_hari }} Hari Kerja</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 max-w-xs">
                                        <div onclick="showDetailModal({{ json_encode($cuti) }}, {{ json_encode($cuti->user) }})" 
                                            class="cursor-pointer group">
                                            <p class="truncate text-gray-900 font-medium group-hover:text-blue-600 transition" title="Klik untuk lihat detail">
                                                {{ $cuti->alasan }}
                                            </p>
                                            <span class="inline-flex items-center gap-1 text-[11px] text-blue-600 font-semibold group-hover:underline mt-0.5">
                                                Lihat detail alasan &rarr;
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-gray-500 mt-1">Kontak: {{ $cuti->kontak_darurat }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($cuti->status === 'pending')
                                            <span class="px-2.5 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Menunggu ACC</span>
                                        @elseif($cuti->status === 'approved')
                                            <span class="px-2.5 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Disetujui</span>
                                            <div class="text-[10px] text-gray-500 mt-1 font-mono">{{ $cuti->nomor_surat }}</div>
                                        @else
                                            <span class="px-2.5 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        @if($cuti->status === 'pending')
                                            <div class="flex items-center justify-center space-x-2">
                                                <!-- Form ACC -->
                                                <form action="{{ route('hrd.cuti.approve', $cuti->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menyetujui pengajuan cuti ini?');">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded shadow">
                                                        ACC
                                                    </button>
                                                </form>

                                                <!-- Form Tolak Modal/Prompt -->
                                                <button type="button" onclick="showRejectModal('{{ $cuti->id }}', '{{ $cuti->user->name }}')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded shadow">
                                                    Tolak
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Sudah diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada pengajuan cuti yang masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $pengajuanCuti->links() }}
                </div>
            </div>

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