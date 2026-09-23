<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                    {{ __('Data Karyawan & Kuota Cuti') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Monitoring kuota cuti dan manajemen data karyawan seluruh pabrik</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('hrd.karyawan.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Karyawan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Alert Notifikasi --}}
            @if(session('success'))
                <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- 1. Panel Import & Export --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Import CSV -->
                    <div class="border-b lg:border-b-0 lg:border-r border-gray-200 pb-5 lg:pb-0 lg:pr-6">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">Import Massal</span>
                        <h4 class="text-sm font-bold text-gray-800">Unggah File CSV Karyawan</h4>
                        <p class="text-xs text-gray-500 mb-3">Unggah data untuk menambahkan atau memperbarui data karyawan.</p>
                        <div class="flex flex-wrap items-center gap-2">
                            <form action="{{ route('hrd.karyawan.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 bg-gray-50 p-1.5 border border-gray-200 rounded-lg">
                                @csrf
                                <input type="file" name="file_excel" required class="text-xs text-gray-600 file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded shadow-sm">
                                    Unggah
                                </button>
                            </form>
                            <a href="{{ route('hrd.karyawan.template') }}" class="px-2.5 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-medium rounded-lg border border-gray-300">
                                Unduh Template
                            </a>
                        </div>
                    </div>

                    <!-- Ekspor Laporan Sesuai Pilihan Filter -->
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">Pusat Ekspor Laporan</span>
                        <h4 class="text-sm font-bold text-gray-800">Unduh Data Karyawan</h4>
                        <p class="text-xs text-gray-500 mb-3">Pilih cakupan pabrik lalu unduh dalam format Excel/CSV atau PDF.</p>
                        
                        <div class="flex flex-wrap items-center gap-2.5">
                            <select id="select_export_pabrik" class="text-xs font-semibold rounded-lg border-gray-300 py-2 pl-3 pr-8 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                                <option value="semua">🌐 Semua Pabrik</option>
                                <option value="supra">🏭 Pabrik Supra</option>
                                <option value="joya">🏭 Pabrik Joya</option>
                                <option value="minyak">🏭 Pabrik Minyak</option>
                                <option value="seg">🏭 Pabrik SEG</option>
                            </select>

                            <button type="button" onclick="triggerExport('csv')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Export CSV
                            </button>

                            <button type="button" onclick="triggerExport('pdf')" class="inline-flex items-center gap-1.5 px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel Filter & Pencarian Otomatis dengan Tombol Cari --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm mb-6">
                <form action="{{ route('hrd.karyawan.index') }}" method="GET" class="space-y-4">
                    
                    <div class="flex items-center justify-between border-b pb-2.5">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Filter & Pencarian Data Karyawan</h3>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 font-medium">Ditemukan: <strong>{{ $karyawans->count() }}</strong> Orang</span>
                            <a href="{{ route('hrd.karyawan.index') }}" class="px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                Reset Filter
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                        
                        <!-- 1. Search by NIP (Dengan Tombol Cari Terintegrasi) -->
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Cari NIP</label>
                            <div class="relative flex items-center">
                                <input type="text" name="nip" value="{{ request('nip') }}" placeholder="Ketik NIP..."
                                    class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-2 pr-14">
                                <button type="submit" 
                                    class="absolute right-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-semibold rounded shadow-xs transition">
                                    Cari
                                </button>
                            </div>
                        </div>

                        <!-- 2. Search by Nama (Dengan Tombol Cari Terintegrasi) -->
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Cari Nama</label>
                            <div class="relative flex items-center">
                                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Ketik nama..."
                                    class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-2 pr-14">
                                <button type="submit" 
                                    class="absolute right-1 px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-semibold rounded shadow-xs transition">
                                    Cari
                                </button>
                            </div>
                        </div>

                        <!-- 3. Filter Pabrik (Otomatis Submit saat Dipilih) -->
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Pabrik</label>
                            <select name="pabrik" onchange="this.form.submit()" 
                                class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-2 cursor-pointer bg-white">
                                <option value="semua">Semua Pabrik</option>
                                <option value="supra" {{ $pabrik === 'supra' ? 'selected' : '' }}>Pabrik Supra</option>
                                <option value="joya" {{ $pabrik === 'joya' ? 'selected' : '' }}>Pabrik Joya</option>
                                <option value="minyak" {{ $pabrik === 'minyak' ? 'selected' : '' }}>Pabrik Minyak</option>
                                <option value="seg" {{ $pabrik === 'seg' ? 'selected' : '' }}>Pabrik SEG</option>
                            </select>
                        </div>

                        <!-- 4. Filter Divisi (Otomatis Submit saat Dipilih) -->
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Divisi</label>
                            <select name="divisi" onchange="this.form.submit()" 
                                class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-2 cursor-pointer bg-white">
                                <option value="semua">Semua Divisi</option>
                                @foreach($listDivisi as $div)
                                    <option value="{{ $div }}" {{ $divisi === $div ? 'selected' : '' }}>{{ $div }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 5. Filter Kategori (Otomatis Submit saat Dipilih) -->
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Kategori</label>
                            <select name="kategori" onchange="this.form.submit()" 
                                class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-2 cursor-pointer bg-white">
                                <option value="semua">Semua Kategori</option>
                                <option value="staff" {{ $kategori === 'staff' ? 'selected' : '' }}>Staff Kantor</option>
                                <option value="tkl" {{ $kategori === 'tkl' ? 'selected' : '' }}>TKL (Pabrik)</option>
                            </select>
                        </div>

                        <!-- 6. Filter Jabatan (Otomatis Submit saat Dipilih) -->
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Jabatan</label>
                            <select name="jabatan" onchange="this.form.submit()" 
                                class="w-full text-xs rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-2 cursor-pointer bg-white">
                                <option value="semua">Semua Jabatan</option>
                                @foreach($listJabatan as $jab)
                                    <option value="{{ $jab }}" {{ $jabatan === $jab ? 'selected' : '' }}>{{ $jab }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </form>
            </div>

            {{-- 3. Tabel Data Karyawan (Dikelompokkan Berdasarkan Pabrik) --}}
            @php
                $daftarNamaPabrik = [
                    'supra' => 'Pabrik Supra',
                    'joya' => 'Pabrik Joya',
                    'minyak' => 'Pabrik Minyak',
                    'seg' => 'Pabrik SEG',
                    'belum_ditentukan' => 'Belum Ditentukan Pabrik',
                ];
            @endphp

            @forelse($karyawanPerPabrik as $pabrikKey => $listKaryawan)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">
                    <!-- Header Pemisah Berdasarkan Pabrik -->
                    <div class="px-5 py-3.5 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="text-lg">🏭</span>
                            <div>
                                <h3 class="text-sm font-bold text-gray-800 tracking-wide uppercase">
                                    {{ $daftarNamaPabrik[$pabrikKey] ?? strtoupper($pabrikKey) }}
                                </h3>
                            </div>
                        </div>
                        <span class="text-xs bg-gray-200 text-gray-700 font-semibold px-2.5 py-0.5 rounded-full">
                            {{ count($listKaryawan) }} Karyawan
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">NIP</th>
                                    <th class="px-4 py-2.5 text-left">Nama Karyawan</th>
                                    <th class="px-4 py-2.5 text-left">Divisi</th>
                                    <th class="px-4 py-2.5 text-left">Jabatan</th>
                                    <th class="px-4 py-2.5 text-center">Kategori</th>
                                    <th class="px-4 py-2.5 text-center">Masuk Kerja</th>
                                    <th class="px-4 py-2.5 text-center">Cuti Lalu</th>
                                    <th class="px-4 py-2.5 text-center font-bold text-blue-700">Sisa Hak Cuti</th>
                                    <th class="px-4 py-2.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @foreach($listKaryawan as $karyawan)
                                    <tr class="hover:bg-blue-50/30 transition">
                                        <td class="px-4 py-3 font-mono text-gray-500 font-semibold">{{ $karyawan->nip ?? '-' }}</td>
                                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $karyawan->name }}</td>
                                        <td class="px-4 py-3 text-gray-700 font-medium">
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-[11px] rounded border border-gray-200">
                                                {{ $karyawan->divisi ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">{{ $karyawan->jabatan ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($karyawan->kategori === 'staff')
                                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold rounded">STAFF</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold rounded">TKL</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-500">
                                            {{ $karyawan->tgl_masuk_kerja ? \Carbon\Carbon::parse($karyawan->tgl_masuk_kerja)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-500">
                                            {{ $karyawan->sisa_cuti_lalu ?? 0 }} hari
                                        </td>
                                        <td class="px-4 py-3 text-center font-extrabold text-blue-600">
                                            {{ $karyawan->sisa_cuti }} hari
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('hrd.karyawan.edit', $karyawan->id) }}" class="inline-flex items-center px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-300 font-semibold rounded-md shadow-2xs transition">
                                                Edit
                                            </a>

                                            <form action="{{ route('hrd.karyawan.destroy', $karyawan->id) }}" method="POST" class="inline" 
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan {{ $karyawan->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-semibold rounded-md text-[11px] shadow-2xs transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 p-10 rounded-xl text-center text-gray-400 shadow-sm">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Data karyawan tidak ditemukan untuk kriteria filter atau kata kunci yang dimasukkan.
                </div>
            @endforelse

        </div>
    </div>

    {{-- Script Ekspor --}}
    <script>
        function triggerExport(type) {
            const pabrik = document.getElementById('select_export_pabrik').value;
            let url = '';

            if (type === 'csv') {
                url = "{{ route('hrd.karyawan.export.csv') }}?pabrik=" + encodeURIComponent(pabrik);
            } else if (type === 'pdf') {
                url = "{{ route('hrd.karyawan.export.pdf') }}?pabrik=" + encodeURIComponent(pabrik);
            }

            if (url) {
                window.location.href = url;
            }
        }
    </script>
</x-app-layout>