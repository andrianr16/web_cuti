<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Persetujuan Cuti Tim (Supervisor)') }} - Divisi: {{ auth()->user()->divisi ?? 'Semua' }}
            </h2>
            @if(empty(auth()->user()->signature_pad))
                <a href="{{ route('profile.edit') }}" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded animate-pulse">
                    ⚠️ Atur TTD Digital di Profil
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Daftar Pengajuan Masuk (Menunggu SPV) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between border-b pb-4 mb-4">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        📋 Permohonan Masuk Menunggu ACC Anda
                        <span class="bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 rounded-full font-semibold">
                            {{ $pengajuanCuti->count() }} Permohonan
                        </span>
                    </h3>
                </div>

                <div class="overflow-x-visible">
                    <table class="w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-600 uppercase font-semibold text-[11px]">
                            <tr>
                                <th class="py-2.5 px-3 text-left">Pegawai</th>
                                <th class="py-2.5 px-3 text-left">Periode Cuti</th>
                                <th class="py-2.5 px-2 text-center">Durasi</th>
                                <th class="py-2.5 px-3 text-left">Alasan</th>
                                <th class="py-2.5 px-2 text-center">TTD</th>
                                <th class="py-2.5 px-3 text-center w-48">Aksi Keputusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pengajuanCuti as $cuti)
                                <tr class="hover:bg-gray-50">
                                    <!-- Pegawai & Jabatan -->
                                    <td class="py-2 px-3">
                                        <div class="font-bold text-gray-900">{{ $cuti->user->name ?? '-' }}</div>
                                        <div class="text-[11px] text-gray-500 leading-tight">
                                            {{ $cuti->user->divisi ?? '-' }} / {{ $cuti->user->jabatan ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Tanggal Cuti -->
                                    <td class="py-2 px-3 whitespace-nowrap text-gray-700">
                                        {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }} 
                                        <span class="text-gray-400">s.d</span> 
                                        {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}
                                    </td>

                                    <!-- Durasi -->
                                    <td class="py-2 px-2 text-center whitespace-nowrap">
                                        <span class="font-bold text-blue-600">{{ $cuti->jumlah_hari }} Hari</span>
                                    </td>

                                    <!-- Alasan -->
                                    <td class="py-2 px-3 text-gray-700 max-w-[180px] truncate" title="{{ $cuti->alasan }}">
                                        {{ $cuti->alasan }}
                                    </td>

                                    <!-- TTD Karyawan (Dibuat Kecil & Ramping) -->
                                    <td class="py-2 px-2 text-center">
                                        @if(!empty($cuti->ttd_karyawan))
                                            <img src="{{ $cuti->ttd_karyawan }}" alt="TTD" class="h-7 mx-auto border border-gray-200 rounded bg-white px-1 py-0.5 object-contain">
                                        @else
                                            <span class="text-gray-400 italic text-[10px]">-</span>
                                        @endif
                                    </td>

                                    <!-- Tombol Aksi -->
                                    <td class="py-2 px-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5" id="action-btn-{{ $cuti->id }}">
                                            <!-- Tombol ACC -->
                                            <form action="{{ route('spv.cuti.approve', $cuti->id) }}" method="POST" onsubmit="return confirm('Setujui dan teruskan ke HRD?');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded text-xs shadow-sm">
                                                    ✓ ACC
                                                </button>
                                            </form>

                                            <!-- Tombol Buka Input Tolak -->
                                            <button type="button" onclick="openRejectRow('{{ $cuti->id }}')" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded text-xs shadow-sm">
                                                ✕ Tolak
                                            </button>
                                        </div>

                                        <!-- Input Alasan Tolak (Muncul Vertikal/Ramping di Bawahnya) -->
                                        <div id="reject-box-{{ $cuti->id }}" class="hidden mt-2 p-2 bg-red-50 border border-red-200 rounded text-left">
                                            <form action="{{ route('spv.cuti.reject', $cuti->id) }}" method="POST">
                                                @csrf
                                                <input type="text" name="catatan_spv" required placeholder="Alasan penolakan..." 
                                                    class="w-full text-xs rounded border-gray-300 py-1 px-2 mb-1.5 focus:border-red-500 focus:ring-red-500">
                                                <div class="flex justify-end gap-1">
                                                    <button type="button" onclick="closeRejectRow('{{ $cuti->id }}')" class="px-2 py-0.5 text-[11px] bg-gray-200 hover:bg-gray-300 text-gray-700 rounded">
                                                        Batal
                                                    </button>
                                                    <button type="submit" class="px-2 py-0.5 text-[11px] bg-red-600 hover:bg-red-700 text-white font-semibold rounded">
                                                        Konfirmasi
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-gray-400 text-xs">
                                        Tidak ada permohonan masuk yang menunggu persetujuan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Riwayat Keputusan Supervisor -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-bold text-gray-800 mb-3">Riwayat Keputusan Anda</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-3 py-2 text-left">Nama</th>
                                <th class="px-3 py-2 text-left">Durasi</th>
                                <th class="px-3 py-2 text-left">Tanggal Diproses</th>
                                <th class="px-3 py-2 text-left">Status Terkini</th>
                                <th class="px-3 py-2 text-left">Catatan Anda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($riwayatSpv as $histori)
                                <tr>
                                    <td class="px-3 py-2 font-bold">{{ $histori->user->name }}</td>
                                    <td class="px-3 py-2">{{ $histori->jumlah_hari }} Hari</td>
                                    <td class="px-3 py-2 text-gray-500">{{ $histori->spv_approved_at ?? $histori->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-3 py-2">
                                        @if($histori->status === 'pending_hrd')
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 font-semibold rounded">Diteruskan ke HRD</span>
                                        @elseif($histori->status === 'approved')
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-semibold rounded">Selesai (Di-ACC HRD)</span>
                                        @elseif($histori->status === 'rejected_spv')
                                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 font-semibold rounded">Ditolak SPV</span>
                                        @elseif($histori->status === 'rejected')
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 font-semibold rounded">Ditolak HRD</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-gray-600 italic">{{ $histori->catatan_spv ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-gray-400">Belum ada riwayat persetujuan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        function openRejectRow(id) {
            document.getElementById('action-btn-' + id).classList.add('hidden');
            document.getElementById('reject-box-' + id).classList.remove('hidden');
        }

        function closeRejectRow(id) {
            document.getElementById('reject-box-' + id).classList.add('hidden');
            document.getElementById('action-btn-' + id).classList.remove('hidden');
        }
    </script>
</x-app-layout>