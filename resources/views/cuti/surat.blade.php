<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Cuti - {{ $cuti->user->name }}</title>
    <style>
        /* Pengaturan Kertas A4 & Margin DomPDF */
        @page {
            size: A4 portrait;
            margin: 8mm 12mm 8mm 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.25;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            box-sizing: border-box;
            border: 1.5px solid #000;
            padding: 8px 12px;
        }

        /* Tabel Pengaturan Header Simetris 3 Kolom */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #000;
            margin-bottom: 6px;
            padding-bottom: 4px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .section-title {
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 2px 0;
            margin: 8px 0 6px 0;
            text-transform: uppercase;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.data-table td {
            padding: 2px 3px;
            vertical-align: top;
        }

        .line-bottom {
            border-bottom: 1px solid #000;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            text-align: center;
        }
        .ttd-table td {
            width: 50%;
            padding: 1px 0;
            vertical-align: top;
        }
        .ttd-space {
            height: 38px;
        }

        /* Mode Cetak & Tombol */
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .container { border: 1.5px solid #000; }
        }
        .btn-panel {
            margin-bottom: 12px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 6px 14px;
            color: #fff;
            border-radius: 4px;
            font-size: 8.5pt;
            font-family: Arial, sans-serif;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

    {{-- Tombol Aksi Layar --}}
    @if(!$isPdf)
    <div class="btn-panel no-print">
        <button onclick="window.print()" class="btn" style="background-color: #2563eb;">
            🖨️ Cetak Formulir (Print)
        </button>
        <a href="{{ route('cuti.pdf', $cuti->id) }}" class="btn" style="background-color: #059669;">
            📥 Unduh PDF
        </a>
    </div>
    @endif

    <div class="container">
        <!-- HEADER 3 KOLOM: KIRI LOGO (20%), TENGAH JUDUL CENTER (60%), KANAN PENYEIMBANG (20%) -->
        <table class="header-table">
            <tr>
                <td style="width: 20%; text-align: left;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" style="width: 105px; height: auto;" alt="Logo">
                    @else
                        <span style="font-weight: bold; font-size: 11pt; color: #15803d;">LESTARI GROUP</span>
                    @endif
                </td>
                <td style="width: 60%; text-align: center;">
                    <h2 style="margin: 0; font-size: 13.5pt; font-weight: bold; letter-spacing: 1px;">PERMOHONAN CUTI</h2>
                </td>
                <td style="width: 20%;"></td>
            </tr>
        </table>

        <!-- BAGIAN 1: PERMOHONAN CUTI (KARYAWAN) -->
        <p style="margin: 3px 0 4px 0;">Yang bertanda tangan di bawah ini, saya :</p>

        <table class="data-table">
            <tr>
                <td style="width: 24%;">Nama</td>
                <td style="width: 2%;">:</td>
                <td class="line-bottom" style="width: 74%;"><strong>{{ $cuti->user->name }}</strong></td>
            </tr>
            <tr>
                <td>Bagian / Divisi</td>
                <td>:</td>
                <td class="line-bottom">{{ $cuti->user->divisi ?? '-' }}</td>
            </tr>
            <tr>
                <td>Dengan ini mengajukan untuk</td>
                <td>:</td>
                <td class="line-bottom"><strong>{{ $cuti->jenis_permohonan ?? 'Cuti Tahunan' }}</strong></td>
            </tr>
            <tr>
                <td>Selama</td>
                <td>:</td>
                <td class="line-bottom">{{ $cuti->jumlah_hari }} hari kerja</td>
            </tr>
            <tr>
                <td>Terhitung mulai hari</td>
                <td>:</td>
                <td class="line-bottom">
                    {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->locale('id')->isoFormat('dddd') }}, 
                    Tanggal: {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d-m-Y') }}
                </td>
            </tr>
            <tr>
                <td>Sampai dengan hari</td>
                <td>:</td>
                <td class="line-bottom">
                    {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->locale('id')->isoFormat('dddd') }}, 
                    Tanggal: {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d-m-Y') }}
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top: 4px;">Alamat yang dapat dihubungi selama saya menjalankan cuti :</td>
            </tr>
            <tr>
                <td style="padding-left: 12px;">- Alamat</td>
                <td>:</td>
                <td class="line-bottom">{{ $cuti->alamat_cuti ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding-left: 12px;">- Telepon</td>
                <td>:</td>
                <td class="line-bottom">{{ $cuti->kontak_darurat }}</td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top: 4px;">Permohonan untuk izin, harus disebutkan keperluan :</td>
            </tr>
            <tr>
                <td colspan="3" class="line-bottom" style="padding: 2px 0;">
                    <em>{{ $cuti->alasan }}</em>
                </td>
            </tr>
        </table>

        <!-- Tanda Tangan Karyawan & Atasan -->
        <table class="ttd-table">
            <tr>
                <td></td>
                <td>Bogor, {{ $cuti->created_at->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>Disetujui oleh,</td>
                <td>Pemohon,</td>
            </tr>
            <tr>
                <td class="ttd-space"></td>
                <td class="ttd-space"></td>
            </tr>
            <tr>
                <td><u>( Kepala Bagian / Supervisor )</u></td>
                <td><u><strong>{{ $cuti->user->name }}</strong></u></td>
            </tr>
        </table>

        <div style="font-size: 7.5pt; margin-top: 2px;">*) Coret yang tidak perlu</div>

        <!-- BAGIAN 2: DIISI OLEH BAGIAN PERSONALIA (HRD) -->
        <div class="section-title">DIISI OLEH BAGIAN PERSONALIA</div>

        <table class="data-table">
            <tr>
                <td style="width: 32%;">* Mulai Masuk Kerja</td>
                <td style="width: 2%;">:</td>
                <td class="line-bottom" colspan="2">
                    {{ $cuti->user->tgl_masuk_kerja ? \Carbon\Carbon::parse($cuti->user->tgl_masuk_kerja)->format('d-m-Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td>* Hak cuti timbul</td>
                <td>:</td>
                <td class="line-bottom" colspan="2">12 hari kerja</td>
            </tr>
            <tr>
                <td>* Sisa hak cuti tahun sebelumnya</td>
                <td>:</td>
                <td class="line-bottom" style="width: 18%;">0</td>
                <td style="width: 48%;">hari kerja</td>
            </tr>
            <tr>
                <td><strong>Jumlah Hak Cuti</strong></td>
                <td>:</td>
                <td class="line-bottom"><strong>{{ $cuti->user->sisa_cuti + ($cuti->status === 'approved' ? $cuti->jumlah_hari : 0) }}</strong></td>
                <td><strong>hari kerja</strong></td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top: 4px;"><strong>* Perhitungan hak cuti</strong></td>
            </tr>
            <tr>
                <td style="padding-left: 15px;">- Hak cuti yang masih ada</td>
                <td>:</td>
                <td class="line-bottom">{{ $cuti->user->sisa_cuti + ($cuti->status === 'approved' ? $cuti->jumlah_hari : 0) }}</td>
                <td>hari kerja</td>
            </tr>
            <tr>
                <td style="padding-left: 15px;">- Jumlah cuti yang dimohon</td>
                <td>:</td>
                <td class="line-bottom">{{ $cuti->jumlah_hari }}</td>
                <td>hari kerja (Tgl {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m') }} s.d {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }})</td>
            </tr>
            <tr style="font-weight: bold;">
                <td style="text-align: right; padding-right: 12px;">SISA CUTI</td>
                <td>:</td>
                <td class="line-bottom" style="border-top: 1px solid #000; border-bottom: 2px solid #000;">
                    {{ $cuti->status === 'approved' ? $cuti->user->sisa_cuti : ($cuti->user->sisa_cuti - $cuti->jumlah_hari) }}
                </td>
                <td style="border-top: 1px solid #000; border-bottom: 2px solid #000;">hari kerja</td>
            </tr>
        </table>

        <!-- Tanda Tangan Personalia -->
        <table class="ttd-table" style="margin-top: 8px;">
            <tr>
                <td></td>
                <td>Bogor, {{ $cuti->approved_at ? \Carbon\Carbon::parse($cuti->approved_at)->format('d-m-Y') : date('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>Disetujui oleh,</td>
                <td>Personalia / HRD,</td>
            </tr>
            <tr>
                <td class="ttd-space">
                    @if($cuti->status === 'approved')
                        <div style="color: green; font-size: 7.5pt; font-weight: bold; border: 1px dashed green; display: inline-block; padding: 2px 6px;">DISETUJUI</div>
                    @endif
                </td>
                <td class="ttd-space">
                    @if($cuti->status === 'approved')
                        <div style="color: green; font-size: 7.5pt; font-weight: bold; border: 1px dashed green; display: inline-block; padding: 2px 6px;">VERIFIED HRD</div>
                    @endif
                </td>
            </tr>
            <tr>
                <td><u>( Kepala Bagian / Supervisor )</u></td>
                <td><u><strong>{{ $cuti->hrd->name ?? 'Bagian Personalia' }}</strong></u></td>
            </tr>
        </table>

        <!-- Catatan HRD -->
        <div style="margin-top: 6px;">
            <strong>Catatan :</strong>
            <div class="line-bottom" style="min-height: 14px; font-style: italic; padding: 1px 0;">
                {{ $cuti->catatan_hrd ?? '-' }}
            </div>
        </div>
    </div>

</body>
</html>