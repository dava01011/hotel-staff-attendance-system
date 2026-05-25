<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Slip Gaji</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        /* Employee Info Box */
        .info-box {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 12px;
            background: #fafafa;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
        }

        .info-label {
            width: 120px;
            font-weight: bold;
            color: #555;
        }

        .info-separator {
            margin: 0 5px;
            color: #999;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        /* Salary Details Table */
        .salary-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .salary-table th {
            background: #2d3748;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #2d3748;
        }

        .salary-table td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        .salary-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .table-section-header {
            background: #e8eef5;
            font-weight: bold;
            color: #2d3748;
        }

        .amount-cell {
            text-align: right;
            font-weight: 600;
        }

        .total-row {
            background: #2d3748;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }

        .total-row td {
            border-color: #2d3748;
            padding: 12px 10px;
        }

        /* Summary */
        .summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }

        .summary-box {
            border: 1px solid #ddd;
            padding: 12px;
            background: #f9f9f9;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .signature-box {
            text-align: center;
            padding-top: 20px;
        }

        .signature-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 40px;
            color: #555;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 10px;
            padding-top: 5px;
            font-size: 10px;
            color: #666;
        }

        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }

            .container {
                padding: 0;
                margin: 0;
                max-width: 100%;
            }

            .header {
                page-break-after: avoid;
            }

            .info-box {
                page-break-after: avoid;
            }

            .salary-table {
                page-break-inside: avoid;
            }

            .signature-section {
                page-break-before: avoid;
            }
        }

        /* Utility */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mt-3 {
            margin-top: 15px;
        }

        .mb-3 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">

        {{-- Header --}}
        <div class="header">
            <div class="company-name">Harris Hotel & Pop! Festival Citylink Bandung</div>
            <h1>SLIP GAJI KARYAWAN</h1>
            <p>Periode: {{ \Carbon\Carbon::create()->month($gaji->bulan)->locale('id')->monthName }} {{ $gaji->tahun }}</p>
        </div>

        {{-- Employee Info --}}
        <div class="info-box">
            <div class="info-grid">
                <div>
                    <div class="info-row">
                        <span class="info-label">Nama</span>
                        <span class="info-separator">:</span>
                        <span class="info-value">{{ $gaji->karyawan->user->nama }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Jabatan</span>
                        <span class="info-separator">:</span>
                        <span class="info-value">{{ $gaji->karyawan->jabatan->nama_jabatan ?? 'N/A' }}</span>
                    </div>
                </div>
                <div>
                    <div class="info-row">
                        <span class="info-label">Departemen</span>
                        <span class="info-separator">:</span>
                        <span class="info-value">{{ $gaji->karyawan->departemen->nama ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nomor Induk</span>
                        <span class="info-separator">:</span>
                        <span class="info-value">{{ $gaji->karyawan->nomor_induk ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Salary Details Table --}}
        <table class="salary-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Komponen Gaji</th>
                    <th style="width: 40%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                {{-- Attendance --}}
                <tr>
                    <td colspan="2" class="table-section-header">KEHADIRAN</td>
                </tr>
                <tr>
                    <td>Jumlah Hari Kerja</td>
                    <td class="amount-cell">{{ $gaji->total_hadir }} hari</td>
                </tr>
                <tr>
                    <td>Gaji Harian</td>
                    <td class="amount-cell">Rp {{ number_format($gaji->gaji_harian, 0, ',', '.') }}</td>
                </tr>

                {{-- Salary Components --}}
                <tr>
                    <td colspan="2" class="table-section-header">KOMPONEN GAJI</td>
                </tr>
                <tr>
                    <td>Gaji Pokok</td>
                    <td class="amount-cell">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
                </tr>

                {{-- Allowances --}}
                @if($gaji->total_tunjangan > 0)
                <tr>
                    <td colspan="2" class="table-section-header">TUNJANGAN</td>
                </tr>
                <tr>
                    <td>Total Tunjangan</td>
                    <td class="amount-cell">Rp {{ number_format($gaji->total_tunjangan, 0, ',', '.') }}</td>
                </tr>
                @endif

                {{-- Deductions --}}
                @if($gaji->total_potongan > 0 || $gaji->total_potongan_lain > 0)
                <tr>
                    <td colspan="2" class="table-section-header">POTONGAN</td>
                </tr>
                @if($gaji->total_potongan > 0)
                <tr>
                    <td>BPJS / Asuransi</td>
                    <td class="amount-cell">Rp {{ number_format($gaji->total_potongan, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($gaji->total_potongan_lain > 0)
                <tr>
                    <td>Potongan Lain</td>
                    <td class="amount-cell">Rp {{ number_format($gaji->total_potongan_lain, 0, ',', '.') }}</td>
                </tr>
                @endif
                @endif

                {{-- Total --}}
                <tr class="total-row">
                    <td><strong>TOTAL GAJI BERSIH</strong></td>
                    <td class="amount-cell"><strong>Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- Summary --}}
        <div class="summary">
            <div class="summary-box">
                <div class="summary-label">Gaji Diterima</div>
                <div class="summary-value">Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <div class="summary-label">Hari Kerja</div>
                <div class="summary-value">{{ $gaji->total_hadir }} Hari</div>
            </div>
        </div>

        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Karyawan</div>
                <div style="height: 60px;"></div>
                <div class="signature-line">
                    {{ $gaji->karyawan->user->nama }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Admin / HRD</div>
                <div style="height: 60px;"></div>
                <div class="signature-line">
                    _________________________
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-3" style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 10px; color: #999;">
            <p>Dokumen ini dibuat oleh Sistem HRIS Harris Hotel & Pop! Festival Citylink Bandung</p>
            <p>Tanggal Cetak: {{ now()->locale('id')->format('d F Y H:i') }}</p>
        </div>

    </div>
</body>
</html>