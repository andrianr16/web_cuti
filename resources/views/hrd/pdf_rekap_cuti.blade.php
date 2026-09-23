<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Cuti</title>
    <style>
        body { font-family: sans-serif; font-size: 8.5pt; color: #1f2937; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #111827; padding-bottom: 8px; }
        .header h2 { margin: 0 0 4px 0; text-transform: uppercase; font-size: 13pt; }
        .header p { margin: 0; font-size: 8pt; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #9ca3af; padding: 5px 6px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; text-transform: uppercase; font-size: 7.5pt; }
        .text-center { text-align: center; }
        .badge { font-weight: bold; font-size: 7.5pt; text-transform: uppercase; }
        .footer { margin-top: 15px; text-align: right; font-size: 7pt; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Rekapitulasi Permohonan Cuti Karyawan</h2>
        <p>Status: {{ strtoupper($status) }} | Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 14%;">No. Surat</th>
                <th style="width: 16%;">Nama Karyawan</th>
                <th style="width: 10%;">Divisi</th>
                <th style="width: 14%;" class="text-center">Periode Cuti</th>
                <th style="width: 6%;" class="text-center">Durasi</th>
                <th style="width: 18%;">Alasan</th>
                <th style="width: 8%;" class="text-center">Status</th>
                <th style="width: 10%;">Verifikator</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cutiList as $index => $c)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $c->nomor_surat ?? '-' }}</td>
                    <td><strong>{{ $c->user->name ?? '-' }}</strong></td>
                    <td>{{ $c->user->divisi ?? '-' }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($c->tanggal_mulai)) }} - {{ date('d/m/Y', strtotime($c->tanggal_selesai)) }}</td>
                    <td class="text-center">{{ $c->jumlah_hari }} hr</td>
                    <td>{{ $c->alasan }}</td>
                    <td class="text-center badge">{{ strtoupper($c->status) }}</td>
                    <td>{{ $c->hrd->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada permohonan cuti ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem E-Cuti Perusahaan
    </div>
</body>
</html>