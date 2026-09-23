<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Facades\Hash;

class KaryawanImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    private int $rows = 0;

    /**
     * Menyesuaikan delimiter CSV (otomatis dukung koma maupun titik koma)
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => $this->detectDelimiter(),
        ];
    }

    private function detectDelimiter(): string
    {
        // Default pemisah koma, jika file pakai titik koma akan terdeteksi
        return request()->hasFile('file_excel') && 
               str_contains(file_get_contents(request()->file('file_excel')->getRealPath()), ';') 
               ? ';' : ',';
    }

    public function model(array $row)
    {
        // Normalisasi keys (buang spasi atau karakter tersembunyi)
        $cleanRow = [];
        foreach ($row as $key => $val) {
            $cleanKey = strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $key)));
            $cleanRow[$cleanKey] = is_string($val) ? trim($val) : $val;
        }

        // Cek nip dan email
        $nip = $cleanRow['nip'] ?? null;
        $email = $cleanRow['email'] ?? null;

        if (empty($nip) || empty($email)) {
            return null;
        }

        $this->rows++;

        return User::updateOrCreate(
            ['nip' => $nip],
            [
                'name'            => $cleanRow['nama'] ?? $cleanRow['name'] ?? 'Karyawan',
                'email'           => $email,
                'password'        => !empty($cleanRow['password']) ? Hash::make($cleanRow['password']) : Hash::make('password123'),
                'role'            => 'karyawan',
                'pabrik'          => strtolower($cleanRow['pabrik'] ?? 'supra'),
                'kategori'        => strtolower($cleanRow['kategori'] ?? 'staff'),
                'divisi'          => $cleanRow['divisi'] ?? '-',
                'jabatan'         => $cleanRow['jabatan'] ?? '-',
                'tgl_masuk_kerja' => !empty($cleanRow['tgl_masuk_kerja']) ? date('Y-m-d', strtotime($cleanRow['tgl_masuk_kerja'])) : now()->toDateString(),
                'sisa_cuti'       => isset($cleanRow['sisa_cuti']) ? (int)$cleanRow['sisa_cuti'] : 12,
                'sisa_cuti_lalu'  => isset($cleanRow['sisa_cuti_lalu']) ? (int)$cleanRow['sisa_cuti_lalu'] : 0,
            ]
        );
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}