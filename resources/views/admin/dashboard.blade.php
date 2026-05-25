@extends('admin.layouts.app')
@php
    use App\Helpers\RoleHelper;
@endphp
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang di Admin Panel')

@push('styles')
<style>
/* ── BASE ───────────────────────────────────────────────────── */
.db-wrap { display: flex; flex-direction: column; gap: 20px; }

/* ── STAT CARDS ──────────────────────────────────────────────── */
.db-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
.stat-card {
    background: #fff;
    border: 1px solid #eaecf0;
    border-radius: 12px;
    padding: 20px;
    display: flex; flex-direction: column; gap: 14px;
    transition: box-shadow .2s, transform .2s;
}
.stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.06); transform: translateY(-1px); }
.stat-card-top { display: flex; justify-content: space-between; align-items: flex-start; }
.stat-label {
    font-size: 11.5px; font-weight: 600;
    color: #8a94a6; text-transform: uppercase; letter-spacing: .5px;
}
.stat-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.icon-blue    { background: #eff6ff; color: #3b82f6; }
.icon-emerald { background: #ecfdf5; color: #10b981; }
.icon-amber   { background: #fffbeb; color: #f59e0b; }
.icon-violet  { background: #f5f3ff; color: #7c3aed; }
.stat-value {
    font-size: 30px; font-weight: 800;
    color: #0f172a; letter-spacing: -1px; line-height: 1;
}
.stat-footer { font-size: 12px; color: #8a94a6; display: flex; align-items: center; gap: 4px; }

/* ── ATTENDANCE METER ────────────────────────────────────────── */
.db-attendance {
    background: #fff; border: 1px solid #eaecf0; border-radius: 12px;
    padding: 20px 24px; display: flex; align-items: center; gap: 28px;
}
.att-percent {
    font-size: 42px; font-weight: 800;
    color: #0f172a; letter-spacing: -2px; white-space: nowrap;
}
.att-percent sub { font-size: 18px; font-weight: 500; color: #94a3b8; vertical-align: baseline; letter-spacing: 0; }
.att-right { flex: 1; display: flex; flex-direction: column; gap: 10px; }
.att-track { height: 8px; border-radius: 99px; background: #f1f5f9; overflow: hidden; display: flex; gap: 1px; }
.att-fill { height: 100%; border-radius: 99px; transition: width .9s cubic-bezier(.4,0,.2,1); }
.fill-hadir     { background: #10b981; }
.fill-terlambat { background: #f59e0b; }
.fill-cuti      { background: #3b82f6; }
.att-legend { display: flex; gap: 20px; flex-wrap: wrap; }
.leg-item { display: flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; color: #374151; }
.leg-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* ── 2-COL ROW ────────────────────────────────────────────────── */
.db-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

/* ── PANEL ───────────────────────────────────────────────────── */
.db-panel { background: #fff; border: 1px solid #eaecf0; border-radius: 12px; overflow: hidden; }
.panel-header {
    padding: 16px 20px; border-bottom: 1px solid #f1f5f9;
    display: flex; justify-content: space-between; align-items: center;
}
.panel-title { font-size: 13.5px; font-weight: 700; color: #0f172a; }
.panel-sub   { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }
.panel-badge {
    font-size: 11px; font-weight: 600; padding: 2px 9px;
    border-radius: 99px; background: #f1f5f9; color: #475569;
}

/* ── MINI CHART ─────────────────────────────────────────────── */
.mini-chart {
    padding: 16px 20px 8px;
    display: flex; align-items: flex-end; gap: 8px; height: 130px;
}
.mc-col { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; gap: 5px; }
.mc-bars { display: flex; align-items: flex-end; gap: 2px; width: 100%; justify-content: center; flex: 1; }
.mc-bar { width: 9px; border-radius: 3px 3px 0 0; min-height: 3px; transition: height .6s cubic-bezier(.4,0,.2,1); }
.mc-bar.b-hadir     { background: #10b981; }
.mc-bar.b-terlambat { background: #fbbf24; }
.mc-label { font-size: 10px; color: #94a3b8; font-weight: 500; }
.chart-legend { padding: 0 20px 14px; display: flex; gap: 16px; }

/* ── QUICK ACTIONS ───────────────────────────────────────────── */
.quick-grid { padding: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.quick-btn {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 14px; border-radius: 10px;
    background: #f8fafc; border: 1px solid #eaecf0;
    text-decoration: none;
    transition: background .15s, border-color .15s, transform .15s;
}
.quick-btn:hover { background: #f1f5f9; border-color: #cbd5e1; transform: translateY(-1px); text-decoration: none; }
.quick-icon { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
.quick-text strong { display: block; font-size: 12.5px; font-weight: 700; color: #0f172a; }
.quick-text span   { font-size: 11px; color: #94a3b8; }

/* ── TABLE ───────────────────────────────────────────────────── */
.db-table-wrap { background: #fff; border: 1px solid #eaecf0; border-radius: 12px; overflow: hidden; }
.db-table { width: 100%; border-collapse: collapse; }
.db-table thead tr   { background: #f8fafc; }
.db-table thead th   { padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #eaecf0; }
.db-table tbody tr   { border-bottom: 1px solid #f1f5f9; transition: background .15s; }
.db-table tbody tr:last-child { border-bottom: none; }
.db-table tbody tr:hover { background: #f8fafc; }
.db-table tbody td   { padding: 12px 16px; font-size: 13px; color: #374151; }

.av { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0; }
.av-blue    { background: linear-gradient(135deg,#3b82f6,#6366f1); }
.av-emerald { background: linear-gradient(135deg,#10b981,#059669); }
.av-amber   { background: linear-gradient(135deg,#f59e0b,#d97706); }
.av-rose    { background: linear-gradient(135deg,#f43f5e,#e11d48); }

.name-cell    { display: flex; align-items: center; gap: 10px; }
.name-primary { font-weight: 600; color: #0f172a; font-size: 13px; }
.name-sub     { font-size: 11.5px; color: #94a3b8; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 99px; font-size: 11px; font-weight: 600; }
.badge-hadir     { background: #ecfdf5; color: #059669; }
.badge-terlambat { background: #fffbeb; color: #d97706; }
.badge-cuti      { background: #eff6ff; color: #2563eb; }
.badge-alpa      { background: #fff1f2; color: #e11d48; }
.badge-sakit     { background: #f5f3ff; color: #7c3aed; }

.empty-state { padding: 48px 24px; text-align: center; }
.empty-state i { font-size: 32px; color: #cbd5e1; margin-bottom: 10px; display: block; }
.empty-state p { font-size: 13px; color: #94a3b8; }

/* ── RESPONSIVE ─────────────────────────────────────────────── */
@media (max-width: 1100px) {
    .db-stats  { grid-template-columns: repeat(2, 1fr); }
    .db-row-2  { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .db-stats        { grid-template-columns: 1fr 1fr; }
    .db-attendance   { flex-direction: column; align-items: flex-start; gap: 14px; }
    .att-percent     { font-size: 34px; }
}
</style>
@endpush

@section('content')
<div class="db-wrap">

    {{-- ── STAT CARDS ─────────────────────────────────────────── --}}
<div class="db-stats">

    {{-- Total Karyawan --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-label">Total Karyawan</div>
            <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
        </div>
        <div class="stat-value">{{ $totalKaryawan }}</div>
        <div class="stat-footer">
            <i class="fas fa-circle" style="font-size:6px;color:#10b981;"></i>
            Karyawan aktif
        </div>
    </div>

    {{-- Hadir Hari Ini --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-label">Hadir Hari Ini</div>
            <div class="stat-icon icon-emerald"><i class="fas fa-user-check"></i></div>
        </div>
        <div class="stat-value">{{ $hadirHariIni }}</div>
        <div class="stat-footer">dari {{ $totalKaryawan }} karyawan</div>
    </div>

    {{-- Terlambat Hari Ini --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-label">Terlambat</div>
            <div class="stat-icon icon-amber"><i class="fas fa-clock"></i></div>
        </div>
        <div class="stat-value">{{ $terlambatHariIni }}</div>
        <div class="stat-footer">hari ini</div>
    </div>

    {{-- Card ke-4: Cuti Pending (untuk yang bisa approve) atau Cuti Hari Ini (untuk lainnya) --}}
    <div class="stat-card">
        <div class="stat-card-top">
            @if($canApprove)
                <div class="stat-label">Cuti Pending</div>
                <div class="stat-icon icon-violet"><i class="fas fa-file-alt"></i></div>
            @else
                <div class="stat-label">Cuti Hari Ini</div>
                <div class="stat-icon icon-violet"><i class="fas fa-calendar-check"></i></div>
            @endif
        </div>
        <div class="stat-value">
            @if($canApprove)
                {{ $cutiPending }}
            @else
                {{ $cutiHariIni }}
            @endif
        </div>
        <div class="stat-footer">
            @if($canApprove)
                menunggu persetujuan
            @else
                karyawan sedang cuti
            @endif
        </div>
    </div>

</div>

    {{-- ── ATTENDANCE METER ────────────────────────────────────── --}}
    @php
        $pHadir     = $totalKaryawan > 0 ? round((max(0,$hadirHariIni - $terlambatHariIni) / $totalKaryawan) * 100) : 0;
        $pTerlambat = $totalKaryawan > 0 ? round(($terlambatHariIni / $totalKaryawan) * 100) : 0;
        $pCuti      = $totalKaryawan > 0 ? round(($cutiHariIni / $totalKaryawan) * 100) : 0;
        $pAlpa      = max(0, 100 - $pHadir - $pTerlambat - $pCuti);
    @endphp

    <div class="db-attendance">
        <div class="att-percent">{{ $persentaseHadir }}<sub>%</sub></div>
        <div class="att-right">
            <div style="font-size:12px;color:#94a3b8;font-weight:500;">
                Tingkat kehadiran hari ini
            </div>
            <div class="att-track">
                <div class="att-fill fill-hadir"     style="width:{{ $pHadir }}%"></div>
                <div class="att-fill fill-terlambat" style="width:{{ $pTerlambat }}%"></div>
                <div class="att-fill fill-cuti"      style="width:{{ $pCuti }}%"></div>
                <div class="att-fill"                style="width:{{ $pAlpa }}%;background:#e2e8f0;"></div>
            </div>
            <div class="att-legend">
                <div class="leg-item"><div class="leg-dot" style="background:#10b981;"></div> Hadir ({{ max(0,$hadirHariIni - $terlambatHariIni) }})</div>
                <div class="leg-item"><div class="leg-dot" style="background:#f59e0b;"></div> Terlambat ({{ $terlambatHariIni }})</div>
                <div class="leg-item"><div class="leg-dot" style="background:#3b82f6;"></div> Cuti/Izin ({{ $cutiHariIni }})</div>
                <div class="leg-item"><div class="leg-dot" style="background:#e2e8f0;border:1px solid #cbd5e1;"></div> Belum absen ({{ $alpaHariIni }})</div>
            </div>
        </div>
    </div>

    {{-- ── CHART + QUICK ACTIONS ───────────────────────────────── --}}
    <div class="db-row-2">

        {{-- Tren 7 hari --}}
        @php $maxHadir = $trend7Hari->max('hadir') ?: 1; @endphp
        <div class="db-panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Tren Kehadiran</div>
                    <div class="panel-sub">7 hari terakhir</div>
                </div>
                <div class="panel-badge">{{ $totalAbsensiBulanIni }} bulan ini</div>
            </div>
            <div class="mini-chart">
                @foreach($trend7Hari as $day)
                <div class="mc-col">
                    <div class="mc-bars">
                        <div class="mc-bar b-hadir"
                             style="height:{{ round(($day['hadir'] / $maxHadir) * 90) }}px"
                             title="Hadir: {{ $day['hadir'] }}"></div>
                        <div class="mc-bar b-terlambat"
                             style="height:{{ round(($day['terlambat'] / $maxHadir) * 90) }}px"
                             title="Terlambat: {{ $day['terlambat'] }}"></div>
                    </div>
                    <div class="mc-label">{{ $day['label'] }}</div>
                </div>
                @endforeach
            </div>
            <div class="chart-legend">
                <div class="leg-item"><div class="leg-dot" style="background:#10b981;"></div> Hadir</div>
                <div class="leg-item"><div class="leg-dot" style="background:#fbbf24;"></div> Terlambat</div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="db-panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Aksi Cepat</div>
                    <div class="panel-sub">Navigasi langsung</div>
                </div>
            </div>

{{-- Quick Actions --}}
<div class="quick-grid">
    {{-- Ganti Tambah Karyawan dengan Kelola User --}}
    @if(RoleHelper::isSuperAdmin())
        <a href="{{ route('admin.user.index') }}" class="quick-btn">
            <div class="quick-icon icon-blue"><i class="fas fa-users-cog"></i></div>
            <div class="quick-text">
                <strong>Kelola User</strong>
                <span>Manajemen akun</span>
            </div>
        </a>
    @endif

    {{-- Data Karyawan (semua role) --}}
    <a href="{{ route('admin.karyawan.index') }}" class="quick-btn">
        <div class="quick-icon icon-emerald"><i class="fas fa-users"></i></div>
        <div class="quick-text">
            <strong>Data Karyawan</strong>
            <span>Lihat semua karyawan</span>
        </div>
    </a>

    {{-- Jabatan (Super Admin) --}}
    @if(RoleHelper::isSuperAdmin())
        <a href="{{ route('admin.jabatan.index') }}" class="quick-btn">
            <div class="quick-icon icon-amber"><i class="fas fa-briefcase"></i></div>
            <div class="quick-text">
                <strong>Jabatan</strong>
                <span>Kelola jabatan</span>
            </div>
        </a>
    @endif

    {{-- Approval Cuti (yang punya akses) --}}
    @if($canApprove)
        <a href="{{ route('admin.approval') }}" class="quick-btn">
            <div class="quick-icon icon-violet"><i class="fas fa-check-circle"></i></div>
            <div class="quick-text">
                <strong>Approval Cuti</strong>
                <span>{{ $cutiPending }} pending</span>
            </div>
        </a>
    @endif
</div>
        </div>

    </div>

    {{-- ── RECENT ABSENSI TABLE ────────────────────────────────── --}}
    <div class="db-table-wrap">
        <div class="panel-header">
            <div>
                <div class="panel-title">Absensi Terbaru</div>
                <div class="panel-sub">Hari ini · {{ $today->format('d M Y') }}</div>
            </div>
            <a href="{{ route('admin.absensi.index') }}"
               style="font-size:12px;color:#3b82f6;font-weight:600;text-decoration:none;">
                Lihat semua <i class="fas fa-arrow-right" style="font-size:10px;"></i>
            </a>
        </div>

        @if($recentAbsensi->count())
        <table class="db-table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Jabatan</th>
                    <th>Jam Masuk</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php $avColors = ['av-blue','av-emerald','av-amber','av-rose']; @endphp
                @foreach($recentAbsensi as $i => $absensi)
                <tr>
                    <td>
                        <div class="name-cell">
                            <div class="av {{ $avColors[$i % 4] }}">
                                {{ strtoupper(substr($absensi->karyawan->user->nama ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <div class="name-primary">{{ $absensi->karyawan->user->nama ?? '-' }}</div>
                                <div class="name-sub">NIP {{ $absensi->karyawan->nip ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:#64748b;font-size:12.5px;">
                        {{ $absensi->karyawan->jabatan->nama_jabatan ?? '-' }}
                    </td>
                    <td style="font-weight:600;color:#0f172a;font-size:13px;font-variant-numeric:tabular-nums;">
                        {{ $absensi->jam_masuk
                            ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i')
                            : '—' }}
                    </td>
                    <td>
                        @php
                            $s   = $absensi->status;
                            $map = [
                                'hadir'     => ['badge-hadir',     'Hadir'],
                                'terlambat' => ['badge-terlambat', 'Terlambat'],
                                'cuti'      => ['badge-cuti',      'Cuti'],
                                'alpa'      => ['badge-alpa',      'Tidak Hadir'],
                                'sakit'     => ['badge-sakit',     'Sakit'],
                            ];
                            [$cls, $lbl] = $map[$s] ?? ['badge-alpa', ucfirst($s)];
                        @endphp
                        <span class="badge {{ $cls }}">{{ $lbl }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>Belum ada absensi hari ini.</p>
        </div>
        @endif
    </div>

</div>
@endsection