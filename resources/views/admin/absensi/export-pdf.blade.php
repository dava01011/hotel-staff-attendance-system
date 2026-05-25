<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; margin: 20px; color: #1e293b; }

        /* Header */
        .header-table { width: 100%; margin-bottom: 12px; border-bottom: 2px solid #334155; padding-bottom: 10px; }
        .header-table td { vertical-align: middle; }
        .logo img { max-width: 80px; height: auto; }
        .title-area { text-align: center; }
        .title-area h1 { font-size: 14px; margin-bottom: 2px; color: #0f172a; }
        .title-area .subtitle { font-size: 10px; color: #475569; margin-bottom: 4px; }
        .title-area .periode { font-size: 9px; font-weight: bold; color: #334155; }

        /* Data Table */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .data-table th {
            background-color: #1e40af;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #1e3a8a;
        }
        .data-table td {
            padding: 4px;
            border: 1px solid #cbd5e1;
            font-size: 8px;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) { background-color: #f1f5f9; }
        .data-table tr:nth-child(odd) { background-color: #ffffff; }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-bold { font-weight: bold; }

        /* Status colors */
        .status-hadir { color: #065f46; font-weight: bold; }
        .status-izin { color: #92400e; font-weight: bold; }
        .status-sakit { color: #9a3412; font-weight: bold; }
        .status-cuti { color: #1e40af; font-weight: bold; }
        .status-alpa { color: #991b1b; font-weight: bold; }
        .status-terlambat { color: #b45309; font-weight: bold; }
        .status-libur { color: #6b7280; font-weight: bold; }

        /* Footer */
        .footer { margin-top: 15px; font-size: 8px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 8px; }

        /* Summary */
        .summary-table { width: auto; border-collapse: collapse; margin-top: 10px; margin-bottom: 10px; }
        .summary-table td { padding: 3px 10px; font-size: 8px; border: 1px solid #cbd5e1; }
        .summary-table .label { background: #f1f5f9; font-weight: bold; }
    </style>
</head>
<body>

@php
    $filters = $filters ?? [];
    $hasData = isset($absensi) && $absensi->isNotEmpty();

    // Periode text
    $periodeText = 'Semua Data';
    if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
        $from = $filters['date_from'] ?? '-';
        $to = $filters['date_to'] ?? '-';
        $periodeText = "$from s/d $to";
    }

    // Status text
    $statusText = 'Semua Status';
    if (!empty($filters['status']) && $filters['status'] !== 'all') {
        $statusText = ucfirst($filters['status']);
    }

    // Hitung summary
    if ($hasData) {
        $totalHadir = $absensi->where('status', 'hadir')->count();
        $totalIzin = $absensi->where('status', 'izin')->count();
        $totalSakit = $absensi->where('status', 'sakit')->count();
        $totalCuti = $absensi->where('status', 'cuti')->count();
        $totalAlpa = $absensi->where('status', 'alpa')->count();
        $totalTerlambat = $absensi->where('status', 'terlambat')->count();
        $totalLibur = $absensi->where('status', 'libur')->count();
    }
@endphp

{{-- ═══════ HEADER ═══════ --}}
<table class="header-table" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width: 100px;">
            @if(isset($logoBase64) && $logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo" style="max-width:80px; height:auto;">
            @endif
        </td>
        <td class="title-area">
            <h1>LAPORAN ABSENSI KARYAWAN</h1>
            <div class="subtitle">PT. HARRIS HOTEL</div>
            <div class="periode">Periode: {{ $periodeText }} &nbsp;|&nbsp; Status: {{ $statusText }}</div>
        </td>
        <td style="width: 90px;"></td>
    </tr>
</table>

@if($hasData)

{{-- ═══════ SUMMARY ═══════ --}}
<table class="summary-table">
    <tr>
        <td class="label">Total Data</td>
        <td>{{ $absensi->count() }}</td>
        <td class="label">Hadir</td>
        <td>{{ $totalHadir }}</td>
        <td class="label">Izin</td>
        <td>{{ $totalIzin }}</td>
        <td class="label">Sakit</td>
        <td>{{ $totalSakit }}</td>
        <td class="label">Cuti</td>
        <td>{{ $totalCuti }}</td>
        <td class="label">Tidak Hadir</td>
        <td>{{ $totalAlpa }}</td>
    </tr>
</table>

{{-- ═══════ DATA TABLE ═══════ --}}
<table class="data-table">
    <thead>
        <tr>
            <th style="width:25px;">#</th>
            <th style="width:120px;">Nama Karyawan</th>
            <th style="width:70px;">Tanggal</th>
            <th style="width:35px;">Hari</th>
            <th style="width:45px;">Masuk</th>
            <th style="width:45px;">Pulang</th>
            <th style="width:80px;">Departemen</th>
            <th style="width:80px;">Jabatan</th>
            <th style="width:50px;">Wajah</th>
            <th style="width:60px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($absensi as $i => $item)
            @php
                $tanggalStr = ($item->tanggal instanceof \Carbon\Carbon) ? $item->tanggal->format('Y-m-d') : $item->tanggal;
                $tanggalCarbon = \Carbon\Carbon::parse($tanggalStr);
                $st = strtolower($item->status ?? '');
                $statusLabel = match($st) {
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'cuti' => 'Cuti',
                    'alpa' => 'Tidak Hadir',
                    'terlambat' => 'Terlambat',
                    'libur' => 'Libur',
                    default => ucfirst($st),
                };
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-left text-bold">{{ $item->karyawan->user->nama ?? '-' }}</td>
                <td class="text-center">{{ $tanggalCarbon->format('d/m/Y') }}</td>
                <td class="text-center">{{ $tanggalCarbon->translatedFormat('D') }}</td>
                <td class="text-center">{{ $item->jam_masuk ? substr($item->jam_masuk, 0, 5) : '-' }}</td>
                <td class="text-center">{{ $item->jam_pulang ? substr($item->jam_pulang, 0, 5) : '-' }}</td>
                <td class="text-left">{{ $item->karyawan->departemen->nama ?? '-' }}</td>
                <td class="text-left">{{ $item->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                <td class="text-center">
                    @if($item->face_valid == 1)
                        ✓ Valid
                    @elseif($item->face_valid === 0)
                        ✗ Invalid
                    @else
                        -
                    @endif
                </td>
                <td class="text-center status-{{ $st }}">{{ $statusLabel }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@else
<div style="text-align:center; padding:40px; border:1px solid #e2e8f0; border-radius:4px; margin-top:20px;">
    <p style="font-size:11px; color:#64748b; font-weight:bold;">Tidak ada data absensi untuk periode yang dipilih.</p>
</div>
@endif

{{-- ═══════ FOOTER ═══════ --}}
<div class="footer">
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }} oleh {{ Auth::user()->nama ?? 'System' }}</p>
    <p>Total: {{ $hasData ? $absensi->count() : 0 }} record</p>
</div>

</body>
</html>