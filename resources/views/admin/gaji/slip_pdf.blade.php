<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $gaji->karyawan->user->nama }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #334155;
            margin: 20px;
            background-color: #fff;
        }

        .container {
            border: 1px solid #e2e8f0;
            padding: 25px;
            position: relative;
        }

        .container::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: #334155;
        }

        .header-table { width: 100%; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; }
        .header-table td { vertical-align: middle; }
        .logo img { max-width: 90px; height: auto; }
        .title-area { text-align: center; }
        .title-area h1 { font-size: 18px; margin-bottom: 2px; color: #0f172a; letter-spacing: 1px; }
        .title-area .subtitle { font-size: 12px; color: #64748b; font-weight: bold; }

        .info-grid {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 4px 0;
            border-bottom: 1px dashed #f1f5f9;
        }
        .label { color: #64748b; font-weight: bold; width: 80px; }
        .value { color: #1e293b; font-weight: 500; }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #475569;
        }
        .main-table td {
            border: 1px solid #e2e8f0;
            padding: 10px 8px;
            vertical-align: top;
        }

        .item-row { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .item-name { color: #334155; }
        .item-amount { font-weight: bold; color: #0f172a; }

        .thp-row {
            margin-top: 10px;
            background-color: #0f172a;
            color: #fff;
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 4px;
        }
        .thp-label { font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .thp-value { font-size: 14px; font-weight: 800; }

        .signature-table {
            width: 100%;
            margin-top: 50px;
        }
        .signature-table td {
            text-align: center;
            width: 33%;
            vertical-align: bottom;
        }
        .signature-space { height: 60px; }
        .signature-name { font-weight: bold; border-bottom: 1px solid #334155; display: inline-block; padding: 0 10px; margin-top: 5px; }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="container">
    <table class="header-table">
        <tr>
            <td width="20%" class="logo">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </td>
            <td width="60%" class="title-area">
                <h1>SLIP GAJI KARYAWAN</h1>
                <div class="subtitle">PT. HARRIS HOTEL</div>
                <div style="margin-top: 5px; font-size: 10px; color: #334155;">
                    Periode: {{ \Carbon\Carbon::create($gaji->tahun, $gaji->bulan, 1)->locale('id')->monthName }} {{ $gaji->tahun }}
                </div>
            </td>
            <td width="20%" style="text-align: right; font-size: 8px; color: #94a3b8;">
                ORIGINAL COPY
            </td>
        </tr>
    </table>

    <table class="info-grid">
        <tr>
            <td width="15%" class="label">NIP</td>
            <td width="35%" class="value">: {{ $gaji->karyawan->nip ?? '-' }}</td>
            <td width="15%" class="label">Departemen</td>
            <td width="35%" class="value">: {{ $gaji->karyawan->departemen->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nama</td>
            <td class="value">: {{ $gaji->karyawan->user->nama ?? '-' }}</td>
            <td class="label">Jabatan</td>
            <td class="value">: {{ $gaji->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="50%">PENERIMAAN (EARNINGS)</th>
                <th width="50%">POTONGAN (DEDUCTIONS)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="item-row">
                        <span class="item-name">Gaji Pokok ({{ $gaji->total_hadir }} Hari)</span>
                        <span class="item-amount">Rp {{ number_format($gaji->total_hadir * $gaji->gaji_harian, 0, ',', '.') }}</span>
                    </div>
                    <div style="height: 60px;"></div> 
                </td>
                <td>
                    <div class="item-row">
                        <span class="item-name">Potongan Absensi</span>
                        <span class="item-amount">Rp 0</span>
                    </div>
                    <div style="height: 60px;"></div>
                </td>
            </tr>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td>
                    <div class="item-row">
                        <span>Total Penerimaan</span>
                        <span>Rp {{ number_format($gaji->total_hadir * $gaji->gaji_harian, 0, ',', '.') }}</span>
                    </div>
                </td>
                <td>
                    <div class="item-row">
                        <span>Total Potongan</span>
                        <span>Rp 0</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="thp-row">
        <span class="thp-label">Take Home Pay (Gaji Bersih)</span>
        <span class="thp-value">Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</span>
    </div>

    <table class="signature-table">
        <tr>
            <td>
                <p>Accounting / HRD</p>
                <div class="signature-space"></div>
                <div class="signature-name">Admin Harris Hotel</div>
            </td>
            <td></td>
            <td>
                <p>Penerima,</p>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $gaji->karyawan->user->nama }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem absensi Harris Hotel pada {{ now()->format('d/m/Y H:i:s') }}
    </div>
</div>

</body>
</html>
