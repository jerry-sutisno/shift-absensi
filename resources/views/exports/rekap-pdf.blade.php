<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Absensi Bulanan - {{ $periodeLabel }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0F4C81;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #0F4C81;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0 0;
            font-size: 10px;
            color: #666666;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-info td {
            font-size: 10px;
            padding: 2px 4px;
        }
        .kpi-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .kpi-table td {
            border: 1px solid #d1d5db;
            background-color: #f8fafc;
            padding: 8px;
            text-align: center;
        }
        .kpi-title {
            font-size: 9px;
            text-transform: uppercase;
            color: #64748b;
        }
        .kpi-value {
            font-size: 14px;
            font-weight: bold;
            color: #0F4C81;
            margin-top: 4px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        .data-table th {
            background-color: #0F4C81;
            color: #ffffff;
            font-size: 10px;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer-sign {
            width: 100%;
            margin-top: 30px;
        }
        .footer-sign td {
            width: 50%;
            text-align: center;
            font-size: 10px;
        }
        .sign-space {
            height: 50px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SIM-Shift DTY Indorama - Laporan Rekapitulasi Absensi Bulanan</h1>
        <p>Divisi DTY PT Indo-Rama Synthetics Tbk &bull; Sistem Informasi Manajemen Shift & Presensi</p>
    </div>

    <table class="meta-info">
        <tr>
            <td width="15%"><strong>Periode:</strong></td>
            <td width="35%">{{ $periodeLabel }}</td>
            <td width="15%"><strong>Dicetak Oleh:</strong></td>
            <td width="35%">{{ $dicetakOleh }}</td>
        </tr>
        <tr>
            <td><strong>Departemen:</strong></td>
            <td>{{ $departemenLabel }}</td>
            <td><strong>Tanggal Cetak:</strong></td>
            <td>{{ $tanggalCetak }}</td>
        </tr>
    </table>

    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-title">Total Hadir</div>
                <div class="kpi-value">{{ $kpiHadir }}</div>
            </td>
            <td>
                <div class="kpi-title">Keterlambatan</div>
                <div class="kpi-value" style="color: #d97706;">{{ $kpiTerlambat }}</div>
            </td>
            <td>
                <div class="kpi-title">Alpa / Tanpa Keterangan</div>
                <div class="kpi-value" style="color: #dc2626;">{{ $kpiAlpa }}</div>
            </td>
            <td>
                <div class="kpi-title">Izin / Sakit / Cuti</div>
                <div class="kpi-value" style="color: #2563eb;">{{ $kpiIzinCuti }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="12%">ID Karyawan</th>
                <th width="24%">Nama Karyawan</th>
                <th width="15%">Departemen</th>
                <th width="11%">Regu</th>
                <th width="7%">Hadir</th>
                <th width="8%">Terlambat</th>
                <th width="6%">Alpa</th>
                <th width="7%">Izin/Cuti</th>
                <th width="6%">Lembur</th>
            </tr>
        </thead>
        <tbody>
            @forelse($karyawans as $idx => $emp)
                @php
                    $s = $statsByEmp[$emp->id_karyawan] ?? [
                        'hadir' => 0, 'terlambat' => 0, 'menit_terlambat' => 0, 'alpa' => 0, 'izin_sakit_cuti' => 0, 'total_lembur_menit' => 0
                    ];
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $emp->id_karyawan }}</td>
                    <td><strong>{{ $emp->nama_karyawan }}</strong><br><small style="color: #64748b;">{{ $emp->jabatan?->nama_jabatan }}</small></td>
                    <td>{{ $emp->departemen?->nama_departemen }}</td>
                    <td class="text-center">{{ $emp->grup?->nama_grup ?? 'General Shift' }}</td>
                    <td class="text-center" style="font-weight: bold; color: #16a34a;">{{ $s['hadir'] }}</td>
                    <td class="text-center" style="color: #d97706;">{{ $s['terlambat'] }} <small>({{ $s['menit_terlambat'] }}m)</small></td>
                    <td class="text-center" style="color: #dc2626;">{{ $s['alpa'] }}</td>
                    <td class="text-center" style="color: #2563eb;">{{ $s['izin_sakit_cuti'] }}</td>
                    <td class="text-center">{{ round($s['total_lembur_menit'] / 60, 1) }} jam</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data rekapitulasi untuk filter yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sign">
        <tr>
            <td>
                Mengetahui,<br><strong>Manager Departemen</strong>
                <div class="sign-space"></div>
                ( .................................................. )
            </td>
            <td>
                Diverifikasi Oleh,<br><strong>Personalia / Admin HR</strong>
                <div class="sign-space"></div>
                ( .................................................. )
            </td>
        </tr>
    </table>
</body>
</html>
