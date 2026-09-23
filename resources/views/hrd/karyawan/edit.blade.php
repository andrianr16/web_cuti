<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Data Karyawan: {{ $karyawan->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <form action="{{ route('hrd.karyawan.update', $karyawan->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip', $karyawan->nip) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $karyawan->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', $karyawan->email) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ganti Password (Kosongkan bila tidak diubah)</label>
                            <input type="password" name="password" placeholder="Minimal 6 karakter"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pabrik</label>
                            <select name="pabrik" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="supra" {{ old('pabrik', $karyawan->pabrik) == 'supra' ? 'selected' : '' }}>Pabrik Supra</option>
                                <option value="joya" {{ old('pabrik', $karyawan->pabrik) == 'joya' ? 'selected' : '' }}>Pabrik Joya</option>
                                <option value="minyak" {{ old('pabrik', $karyawan->pabrik) == 'minyak' ? 'selected' : '' }}>Pabrik Minyak</option>
                                <option value="seg" {{ old('pabrik', $karyawan->pabrik) == 'seg' ? 'selected' : '' }}>Pabrik SEG</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kategori Karyawan</label>
                            <select name="kategori" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="staff" {{ old('kategori', $karyawan->kategori) == 'staff' ? 'selected' : '' }}>Staff Kantor</option>
                                <option value="tkl" {{ old('kategori', $karyawan->kategori) == 'tkl' ? 'selected' : '' }}>TKL (Tenaga Kerja Langsung)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Divisi / Departemen</label>
                            <input type="text" name="divisi" value="{{ old('divisi', $karyawan->divisi) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                            <input type="text" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Masuk Kerja</label>
                            <input type="date" name="tgl_masuk_kerja" value="{{ old('tgl_masuk_kerja', $karyawan->tgl_masuk_kerja) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sisa Kuota Cuti (Hari)</label>
                            <input type="number" name="sisa_cuti" value="{{ old('sisa_cuti', $karyawan->sisa_cuti) }}" min="0" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('hrd.karyawan.index', ['pabrik' => $karyawan->pabrik]) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>