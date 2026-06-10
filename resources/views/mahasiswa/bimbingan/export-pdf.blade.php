<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Bimbingan Akademik</title>
    <style>
        @page { margin: 1.5cm 1.5cm 2cm 1.5cm; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2F2F2F; line-height: 1.4; font-size: 11px; margin: 0; padding: 0; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 3px solid #1F4C7A; }
        .header-title { color: #1B3F66; font-size: 18px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .header-subtitle { color: #2FA7A0; font-size: 11px; font-weight: bold; margin: 3px 0 0 0; letter-spacing: 0.5px; }
        .header-meta { color: #7A7A7A; font-size: 9px; margin-top: 5px; }
        .student-box { width: 100%; border-collapse: collapse; margin-bottom: 25px; background-color: #F5F7FA; border: 1px solid #E2E8F0; }
        .student-box td { padding: 8px 12px; vertical-align: top; }
        .student-box td.label { font-weight: bold; color: #1B3F66; width: 18%; }
        .student-box td.colon { width: 2%; text-align: center; }
        .student-box td.value { color: #2F2F2F; width: 30%; }
        .doc-title { text-align: center; font-size: 14px; font-weight: bold; color: #1B3F66; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 0.5px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .data-table th { background-color: #1F4C7A; color: #FFFFFF; font-weight: bold; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; border: 1px solid #1F4C7A; }
        .data-table td { padding: 8px 10px; border: 1px solid #E2E8F0; vertical-align: top; }
        .data-table tr:nth-child(even) { background-color: #F5F7FA; }
        .badge { display: inline-block; padding: 3px 8px; font-size: 8px; font-weight: bold; text-transform: uppercase; border-radius: 10px; text-align: center; }
        .badge-pending { background-color: #FEF3C7; color: #D97706; }
        .badge-approved { background-color: #D1FAE5; color: #059669; }
        .badge-completed { background-color: #DBEAFE; color: #2563EB; }
        .badge-rejected { background-color: #FEE2E2; color: #DC2626; }
        .badge-canceled { background-color: #F3F4F6; color: #4B5563; }
        .tipe-online { color: #2563EB; font-weight: bold; }
        .tipe-offline { color: #4B5563; font-weight: bold; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 40px; page-break-inside: avoid; }
        .signature-table td { width: 50%; vertical-align: top; }
        .signature-title { font-size: 10px; color: #7A7A7A; margin-bottom: 50px; }
        .signature-name { font-weight: bold; color: #1B3F66; text-decoration: underline; }
        .signature-nip { color: #7A7A7A; font-size: 9px; margin-top: 2px; }
        .footer { position: fixed; bottom: -1cm; left: 0; right: 0; height: 1cm; text-align: center; font-size: 8px; color: #7A7A7A; border-top: 1px solid #E2E8F0; padding-top: 5px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 70%; vertical-align: middle;">
                <h1 class="header-title">SIBIMA</h1>
                <p class="header-subtitle">Sistem Bimbingan Mahasiswa - Portal Akademik</p>
            </td>
            <td style="width: 30%; text-align: right; vertical-align: middle;">
                <div class="header-meta">
                    Tanggal Ekspor: {{ $exportAt }}<br>
                    Format: Dokumen Digital Resmi
                </div>
            </td>
        </tr>
    </table>
    <div class="doc-title">Laporan Hasil Bimbingan Akademik</div>
    <table class="student-box">
        <tr>
            <td class="label">Nama Lengkap</td><td class="colon">:</td><td class="value"><strong></strong></td>
            <td class="label">Program Studi</td><td class="colon">:</td><td class="value"></td>
        </tr>
        <tr>
            <td class="label">NIM</td><td class="colon">:</td><td class="value"></td>
            <td class="label">Fakultas</td><td class="colon">:</td><td class="value"></td>
        </tr>
        <tr>
            <td class="label">Angkatan / Sem</td><td class="colon">:</td><td class="value"></td>
            <td class="label">Status Akademik</td><td class="colon">:</td><td class="value"></td>
        </tr>
    </table>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:5%; text-align:center;">No</th>
                <th style="width:25%;">Dosen Pembimbing</th>
                <th style="width:20%;">Tanggal &amp; Waktu</th>
                <th style="width:35%;">Topik &amp; Judul TA</th>
                <th style="width:15%; text-align:center;">Tanda Tangan Dosen</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= 8; $i++)
                <tr>
                    <td style="text-align:center; height: 50px;">{{ $i }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-title">Mahasiswa bersangkutan,</div>
                <div style="height:50px;"></div>
                <div class="signature-name">............................................</div>
                <div class="signature-nip">NIM. ........................................</div>
            </td>
            <td style="text-align:right;">
                <div class="signature-title">Mengetahui,<br>Dosen Pembimbing Utama</div>
                <div style="height:50px;"></div>
                <div class="signature-name">............................................</div>
                <div class="signature-nip">
                    NIDN. ........................................
                </div>
            </td>
        </tr>
    </table>
    <div class="footer">Dokumen ini dihasilkan secara otomatis oleh Sistem Bimbingan Mahasiswa (SIBIMA). Halaman 1 dari 1</div>
</body>
</html>
