<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Karyawan Baru') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('hrd.karyawan.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- NIP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip') }}" required placeholder="Contoh: KRY005"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @error('nip') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap karyawan"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email Login -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email (Untuk Login)</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@perusahaan.com"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Password Awal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password Awal</label>
                            <input type="password" name="password" required placeholder="Minimal 6 karakter"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Lokasi Pabrik -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Penempatan Pabrik</label>
                            <select name="pabrik" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="supra">Pabrik Supra</option>
                                <option value="joya">Pabrik Joya</option>
                                <option value="minyak">Pabrik Minyak</option>
                                <option value="seg">Pabrik SEG</option>
                            </select>
                        </div>

                        <!-- Kategori Karyawan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kategori Pekerja</label>
                            <select name="kategori" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="staff">Staff Kantor</option>
                                <option value="tkl">TKL (Tenaga Kerja Langsung)</option>
                            </select>
                        </div>

                        <!-- Divisi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Divisi / Bagian</label>
                            <input type="text" name="divisi" value="{{ old('divisi') }}" required placeholder="Contoh: Produksi, IT, Teknisi, OB"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>

                        <!-- Jabatan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                            <input type="text" name="jabatan" value="{{ old('jabatan') }}" required placeholder="Contoh: Operator Mesin, Staff IT"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>

                        <!-- Tanggal Masuk Kerja -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Masuk Kerja</label>
                            <input type="date" name="tgl_masuk_kerja" value="{{ old('tgl_masuk_kerja') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>

                        <!-- Jatah Kuota Cuti Awal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jatah Hak Cuti Awal</label>
                            <input type="number" name="sisa_cuti" value="{{ old('sisa_cuti', 12) }}" min="0" max="30" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 pt-4 border-t">
                        <a href="{{ route('hrd.karyawan.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md shadow">
                            Simpan Karyawan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>