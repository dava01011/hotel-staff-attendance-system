<table>
    {{-- Baris 1: Nama Perusahaan --}}
    <tr>
        <td colspan="10" style="text-align:center; font-size:16pt; font-weight:bold;">
            PT. HARRIS HOTEL
        </td>
    </tr>

    {{-- Baris 2: Subjudul --}}
    <tr>
        <td colspan="10" style="text-align:center; font-size:11pt;">
            LAPORAN DATA ABSENSI KARYAWAN
        </td>
    </tr>

    {{-- Baris 3: Periode --}}
    <tr>
        <td colspan="10" style="text-align:center; font-size:10pt;">
            @if(!empty($filters['date_from']) || !empty($filters['date_to']))
                Periode: {{ $filters['date_from'] ?? '-' }} s/d {{ $filters['date_to'] ?? '-' }}
            @else
                Periode: Semua Data
            @endif
        </td>
    </tr>

    {{-- Baris 4: Spasi --}}
    <tr><td colspan="10"></td></tr>

    {{-- Baris 5: Info filter --}}
    <tr>
        <td colspan="10" style="text-align:center; font-style:italic; font-size:10pt; color:#6B7280;">
            Filter Status:
            @if(!empty($filters['status']) && $filters['status'] !== 'all')
                {{ ucfirst($filters['status']) }}
            @else
                Semua Status
            @endif
            &nbsp;|&nbsp; Total Data: {{ $absensi->count() }} record
        </td>
    </tr>

    {{-- Baris 6: Header Tabel --}}
    <tr>
        <th style="text-align:center; font-weight:bold;">#</th>
        <th style="font-weight:bold;">Nama Karyawan</th>
        <th style="text-align:center; font-weight:bold;">Tanggal</th>
        <th style="text-align:center; font-weight:bold;">Jam Masuk</th>
        <th style="text-align:center; font-weight:bold;">Jam Pulang</th>
        <th style="font-weight:bold;">Departemen</th>
        <th style="font-weight:bold;">Jabatan</th>
        <th style="text-align:center; font-weight:bold;">Terlambat</th>
        <th style="text-align:center; font-weight:bold;">Wajah Valid</th>
        <th style="text-align:center; font-weight:bold;">Status</th>
    </tr>

    {{-- Data Rows --}}
    @forelse($absensi as $i => $item)
        @php
            $tanggalStr = $item->tanggal instanceof \Carbon\Carbon ? $item->tanggal->format('Y-m-d') : $item->tanggal;
            $jamMasuk  = $item->jam_masuk  ? \Carbon\Carbon::parse($item->jam_masuk)  : null;
            $jamPulang = $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang) : null;

            // Hitung keterlambatan jika ada shift
            $terlambat = '-';
            $shift = \App\Services\AbsensiDetectionService::getShiftForDate($item->karyawan, \Carbon\Carbon::parse($tanggalStr));
            
            if ($jamMasuk && $shift) {
                $jadwalMasuk = \Carbon\Carbon::parse($tanggalStr . ' ' . $shift->jam_masuk);
                $toleransi = $shift->toleransi_menit ?? 0;
                $diffMenit = $jamMasuk->diffInMinutes($jadwalMasuk, false);
                if ($diffMenit < -$toleransi) {
                    $terlambat = abs($diffMenit) . ' menit';
                } else {
                    $terlambat = 'Tepat Waktu';
                }
            }
        @endphp
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td>{{ $item->karyawan->user->nama ?? '-' }}</td>
            <td style="text-align:center;">
                {{ \Carbon\Carbon::parse($tanggalStr)->format('d/m/Y') }}
            </td>
            <td style="text-align:center;">
                {{ $jamMasuk ? $jamMasuk->format('H:i') : '-' }}
            </td>
            <td style="text-align:center;">
                {{ $jamPulang ? $jamPulang->format('H:i') : '-' }}
            </td>
            <td>{{ $item->karyawan->departemen->nama ?? '-' }}</td>
            <td>{{ $item->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
            <td style="text-align:center;">{{ $terlambat }}</td>
            <td style="text-align:center;">
                @if($item->face_valid == 1)
                    Valid
                @elseif($item->face_valid == 0)
                    Invalid
                @else
                    -
                @endif
            </td>
            <td style="text-align:center;">{{ $item->status === 'alpa' ? 'Tidak Hadir' : ucfirst($item->status) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="10" style="text-align:center;">Tidak ada data</td>
        </tr>
    @endforelse
</table>