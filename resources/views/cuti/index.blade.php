<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Formulir Pengajuan Cuti Karyawan') }}
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

            <!-- Kartu Info Kuota & Form Pengajuan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between border-b pb-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Buat Permohonan Cuti Baru</h3>
                        <p class="text-sm text-gray-500">Silakan lengkapi tanggal dan alasan cuti di bawah ini.</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs uppercase tracking-wider text-gray-500 font-semibold block">Sisa Kuota Cuti</span>
                        <span class="text-3xl font-extrabold text-blue-600">{{ $user->sisa_cuti }} <span class="text-sm font-normal text-gray-600">Hari</span></span>
                    </div>
                </div>

                <form action="{{ route('cuti.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Info Pegawai (Read-Only) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase">NIP</label>
                            <div class="font-medium text-gray-800">{{ $user->nip ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase">Nama Lengkap</label>
                            <div class="font-medium text-gray-800">{{ $user->name }}</div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase">Divisi / Jabatan</label>
                            <div class="font-medium text-gray-800">{{ $user->divisi ?? '-' }} / {{ $user->jabatan ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Input Tanggal Mulai & Tanggal Selesai -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai Cuti</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('tanggal_mulai')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai Cuti</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('tanggal_selesai')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Keterangan Estimasi Hari Kerja -->
                        <div id="estimasiHariBox" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800">
                            Estimasi cuti yang terhitung: <strong id="totalHariText">0</strong> hari kerja (tidak termasuk Sabtu & Minggu).
                        </div>
                    </div>

                    <!-- Alamat Selama Menjalankan Cuti -->
                    <div>
                        <label for="alamat_cuti" class="block text-sm font-medium text-gray-700">Alamat Selama Menjalankan Cuti</label>
                        <textarea name="alamat_cuti" id="alamat_cuti" rows="2" required placeholder="Contoh: Jl. Mawar No. 12, Bandung, Jawa Barat"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('alamat_cuti') }}</textarea>
                        @error('alamat_cuti')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kontak Darurat -->
                    <div>
                        <label for="kontak_darurat" class="block text-sm font-medium text-gray-700">Nomor HP / Kontak Darurat</label>
                        <input type="text" name="kontak_darurat" id="kontak_darurat" value="{{ old('kontak_darurat') }}" placeholder="Contoh: 081234567890" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('kontak_darurat')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alasan Cuti (Mandatory) -->
                    <div>
                        <label for="alasan" class="block text-sm font-semibold text-gray-700">
                            Alasan / Keterangan Cuti <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alasan" id="alasan" rows="3" required
                            placeholder="Tuliskan alasan permohonan cuti secara jelas (wajib diisi)..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('alasan') }}</textarea>
                        @error('alasan')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Input Canvas Tanda Tangan Karyawan -->
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Tanda Tangan Pemohon (Coret di bawah ini) <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-2 bg-gray-50 flex flex-col items-center">
                            <canvas id="signature-pad" class="border border-gray-300 rounded bg-white w-full max-w-md h-40"></canvas>
                            <div class="w-full max-w-md flex justify-between items-center mt-2">
                                <span class="text-xs text-gray-500">Goreskan tanda tangan Anda di dalam kotak.</span>
                                <button type="button" id="clear-signature" class="px-2.5 py-1 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 rounded font-medium">
                                    Hapus / Ulangi
                                </button>
                            </div>
                        </div>
                        <!-- Input tersembunyi untuk menyimpan gambar tanda tangan Base64 -->
                        <input type="hidden" name="ttd_karyawan" id="ttd_karyawan_input" required>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow transition">
                            Kirim Pengajuan ke HRD
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Riwayat Pengajuan Cuti Karyawan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Riwayat Pengajuan Cuti Anda</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal Pengajuan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Durasi Cuti</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Alasan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status HRD</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi / Dokumen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($riwayatCuti as $cuti)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                        {{ $cuti->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $cuti->jumlah_hari }} Hari Kerja</div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 max-w-xs">
                                        {{ $cuti->alasan }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($cuti->status === 'pending_spv')
                                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full border border-amber-200">
                                                Menunggu ACC SPV
                                            </span>
                                        @elseif($cuti->status === 'pending_hrd')
                                            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full border border-blue-200">
                                                Menunggu ACC HRD
                                            </span>
                                        @elseif($cuti->status === 'approved')
                                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full border border-emerald-200">
                                                Disetujui (ACC)
                                            </span>
                                        @elseif($cuti->status === 'rejected_spv')
                                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 text-xs font-semibold rounded-full border border-rose-200">
                                                Ditolak SPV
                                            </span>
                                            @if($cuti->catatan_spv)
                                                <p class="text-[11px] text-rose-600 mt-0.5">Alasan: {{ $cuti->catatan_spv }}</p>
                                            @endif
                                        @elseif($cuti->status === 'rejected')
                                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 text-xs font-semibold rounded-full border border-rose-200">
                                                Ditolak HRD
                                            </span>
                                            @if($cuti->catatan_hrd)
                                                <p class="text-[11px] text-rose-600 mt-0.5">Alasan: {{ $cuti->catatan_hrd }}</p>
                                            @endif
                                        @else
                                            <span class="px-2.5 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                                                {{ ucfirst($cuti->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap space-x-2">
                                        @if($cuti->status === 'approved')
                                            <a href="{{ route('cuti.print', $cuti->id) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded shadow">
                                                Cetak / PDF
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada riwayat pengajuan cuti.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        const inputMulai = document.getElementById('tanggal_mulai');
        const inputSelesai = document.getElementById('tanggal_selesai');
        const estimasiBox = document.getElementById('estimasiHariBox');
        const totalHariText = document.getElementById('totalHariText');

        function hitungHariKerja() {
            const mulaiVal = inputMulai.value;
            const selesaiVal = inputSelesai.value;

            if (!mulaiVal || !selesaiVal) {
                estimasiBox.classList.add('hidden');
                return;
            }

            const start = new Date(mulaiVal);
            const end = new Date(selesaiVal);

            if (start > end) {
                estimasiBox.classList.add('hidden');
                return;
            }

            let hariKerja = 0;
            let cur = new Date(start);

            while (cur <= end) {
                const dayOfWeek = cur.getDay();
                // 0 = Minggu, 6 = Sabtu
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    hariKerja++;
                }
                cur.setDate(cur.getDate() + 1);
            }

            totalHariText.innerText = hariKerja;
            estimasiBox.classList.remove('hidden');
        }

        inputMulai.addEventListener('change', hitungHariKerja);
        inputSelesai.addEventListener('change', hitungHariKerja);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('signature-pad');
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgb(0, 0, 0)'
            });

            // Sesuaikan resolusi canvas
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear();
            }
            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();

            document.getElementById('clear-signature').addEventListener('click', function () {
                signaturePad.clear();
            });

            // Simpan tanda tangan saat form dikirim
            const form = canvas.closest('form');
            form.addEventListener('submit', function (e) {
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    alert('Silakan bubuhkan tanda tangan pemohon terlebih dahulu!');
                    return false;
                }
                document.getElementById('ttd_karyawan_input').value = signaturePad.toDataURL('image/png');
            });
        });
    </script>
</x-app-layout>

