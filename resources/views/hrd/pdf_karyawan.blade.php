<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Karyawan</title>
    <style>
        body { font-family: sans-serif; font-size: 8.5pt; color: #1f2937; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #111827; padding-bottom: 8px; }
        .header h2 { margin: 0 0 4px 0; text-transform: uppercase; font-size: 13pt; }
        .header p { margin: 0; font-size: 8pt; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #9ca3af; padding: 5px 7px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 7.5pt; }
        .text-center { text-align: center; }
        .footer { margin-top: 15px; text-align: right; font-size: 7pt; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Data Karyawan & Kuota Cuti</h2>
        <p>Unit Pabrik: {{ strtoupper($pabrik) }} | Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 12%;">NIP</th>
                <th style="width: 20%;">Nama Lengkap</th>
                <th style="width: 10%;">Pabrik</th>
                <th style="width: 13%;">Divisi</th>
                <th style="width: 13%;">Jabatan</th>
                <th style="width: 9%;" class="text-center">Masuk Kerja</th>
                <th style="width: 9%;" class="text-center">Cuti Lalu</th>
                <th style="width: 9%;" class="text-center">Sisa Cuti</th>
            </tr>
        </thead>
        <tbody>
            @forelse($karyawans as $index => $k)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $k->nip ?? '-' }}</td>
                    <td><strong>{{ $k->name }}</strong></td>
                    <td>{{ strtoupper($k->pabrik ?? '-') }}</td>
                    <td>{{ $k->divisi ?? '-' }}</td>
                    <td>{{ $k->jabatan ?? '-' }}</td>
                    <td class="text-center">{{ $k->tgl_masuk_kerja ? date('d/m/Y', strtotime($k->tgl_masuk_kerja)) : '-' }}</td>
                    <td class="text-center">{{ $k->sisa_cuti_lalu ?? 0 }} hari</td>
                    <td class="text-center"><strong>{{ $k->sisa_cuti }} hari</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data karyawan ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem E-Cuti Perusahaan
    </div>
</body>
</html>