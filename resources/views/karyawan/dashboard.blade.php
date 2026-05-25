@extends('karyawan.layout.master')

@section('title', 'Dashboard Absensi')

@push('styles')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f0f4ff;
        overflow: hidden;
    }

    :root {
        --kv-blue:   #ea580c;
        --kv-blue2:  #c2410c;
        --kv-orange: #ea580c; /* Override orange to blue */
        --kv-orange2:#093E7E;
        --kv-white:  #ffffff;
        --kv-bg:     #f8fafc;
        --kv-text:   #1e293b;
        --kv-sub:    #64748b;
        --kv-border: #e2e8f0;
    }

    /* ── Wrapper ─────────────────────────────────────────────── */
    .fw {
        position: fixed;
        inset: 0;
        display: flex;
        flex-direction: column;
        background: var(--kv-bg);
    }

    .scroll-body {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
        padding-bottom: 90px;
    }

    /* ── Alerts ──────────────────────────────────────────────── */
    .alerts {
        position: fixed;
        top: 68px;
        left: 50%;
        transform: translateX(-50%);
        width: calc(100% - 32px);
        max-width: 480px;
        z-index: 999;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 16px rgba(0,0,0,.1);
        animation: slideDown .3s ease-out;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
    .alert-danger  { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }
    .alert-info    { background: #dbeafe; color: #1e40af; border-left: 4px solid var(--kv-blue); }

    /* ── Header ──────────────────────────────────────────────── */
    .top-header {
        margin-top: 60px;
        background: var(--kv-white);
        border-bottom: 1px solid var(--kv-border);
        padding: 20px 20px 50px;
        position: relative;
        overflow: hidden;
    }

    /* orange left accent bar */
    .top-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, var(--kv-blue), var(--kv-orange));
    }

    /* subtle grid background */
    .top-header::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(29,78,216,.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(29,78,216,.04) 1px, transparent 1px);
        background-size: 24px 24px;
        pointer-events: none;
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
        z-index: 1;
    }

    .greeting-sub {
        font-size: 11px;
        color: var(--kv-orange);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 4px;
    }

    .greeting-name {
        font-size: 22px;
        font-weight: 800;
        color: var(--kv-text);
        line-height: 1.2;
    }

    .header-date-pill {
        background: var(--kv-blue);
        padding: 10px 16px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 700;
        color: white;
        text-align: center;
        line-height: 1.4;
        box-shadow: 0 4px 12px rgba(12, 82, 166, 0.25);
    }

    /* ── Main Card ───────────────────────────────────────────── */
    .main-card {
        background: var(--kv-white);
        border-radius: 24px 24px 0 0;
        margin-top: -28px;
        padding: 24px 20px 20px;
        position: relative;
        z-index: 2;
        box-shadow: 0 -2px 16px rgba(0,0,0,.05);
    }

    /* ── Shift Banner ────────────────────────────────────────── */
    .shift-banner {
        background: #EFF6FF;
        border: 1.5px solid #BFDBFE;
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
    }

    .shift-icon-wrap {
        width: 44px;
        height: 44px;
        background: var(--kv-orange);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(12, 82, 166, 0.3);
    }

    .shift-text-wrap { flex: 1; }

    .shift-name {
        font-size: 15px;
        font-weight: 800;
        color: var(--kv-text);
        margin-bottom: 2px;
    }

    .shift-time-row {
        font-size: 13px;
        color: var(--kv-orange);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .shift-badge {
        background: var(--kv-orange);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .5px;
        box-shadow: 0 2px 8px rgba(12, 82, 166, 0.3);
    }

    /* ── Clock + Check Button ────────────────────────────────── */
    .clock-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
    }

    .clock-block { flex: 1; }

    .live-clock {
        font-size: 40px;
        font-weight: 800;
        color: var(--kv-text);
        letter-spacing: -1px;
        font-variant-numeric: tabular-nums;
        line-height: 1;
        margin-bottom: 4px;
    }

    .live-date {
        font-size: 11px;
        color: var(--kv-sub);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .check-btn {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        border: none;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(.34,1.56,.64,1);
        position: relative;
        text-decoration: none;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .check-btn i { font-size: 22px; margin-bottom: 5px; display: block; }

    /* Check In → blue */
    .check-btn.checkin {
        background: var(--kv-blue);
        color: white;
        box-shadow: 0 8px 24px rgba(12, 82, 166, 0.35);
    }
    .check-btn.checkin:hover { transform: scale(1.08); box-shadow: 0 12px 30px rgba(12, 82, 166, 0.45); }

    /* pulse ring */
    .check-btn.checkin::after {
        content: '';
        position: absolute;
        inset: -7px;
        border-radius: 50%;
        border: 2.5px solid rgba(12, 82, 166, 0.25);
        animation: pulse-ring 2s ease-out infinite;
    }

    /* Check Out → blue (since orange is overridden) */
    .check-btn.checkout {
        background: var(--kv-orange);
        color: white;
        box-shadow: 0 8px 24px rgba(12, 82, 166, 0.35);
    }
    .check-btn.checkout:hover { transform: scale(1.08); box-shadow: 0 12px 30px rgba(12, 82, 166, 0.45); }

    .check-btn.checkout::after {
        content: '';
        position: absolute;
        inset: -7px;
        border-radius: 50%;
        border: 2.5px solid rgba(12, 82, 166, 0.25);
        animation: pulse-ring 2s ease-out infinite;
    }

    /* Done → neutral */
    .check-btn.done {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: default;
        box-shadow: none;
    }

    @keyframes pulse-ring {
        0%    { transform: scale(1);    opacity: 1; }
        100%  { transform: scale(1.2);  opacity: 0; }
    }

    /* ── Time Stats ──────────────────────────────────────────── */
    .time-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 0;
    }

    .ts-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 10px;
        text-align: center;
    }

    .ts-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    .ts-icon.in     { background: #eff6ff; color: var(--kv-blue); }
    .ts-icon.out    { background: #EFF6FF; color: var(--kv-orange); }
    .ts-icon.dur    { background: #f0fdf4; color: #16a34a; }

    .ts-val {
        font-size: 17px;
        font-weight: 800;
        color: #0f172a;
        font-variant-numeric: tabular-nums;
        margin-bottom: 3px;
    }

    .ts-label {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    /* ── Section divider ─────────────────────────────────────── */
    .section-wrap {
        background: var(--kv-white);
        margin-top: 10px;
        padding: 20px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--kv-text);
    }

    .see-all-btn {
        background: #EFF6FF;
        color: var(--kv-orange);
        border: 1.5px solid #BFDBFE;
        padding: 7px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .see-all-btn:hover {
        background: var(--kv-orange);
        color: white;
        border-color: var(--kv-orange);
    }

    /* ── Monthly Stats Cards ─────────────────────────────────── */
    .monthly-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 22px;
    }

    .ms-card {
        border-radius: 14px;
        padding: 16px 12px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .ms-card::after {
        content: '';
        position: absolute;
        bottom: -10px;
        right: -10px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        opacity: .08;
    }

    .ms-card.present { background: #f0fdf4; }
    .ms-card.present::after { background: #16a34a; }

    .ms-card.absent  { background: #fff1f2; }
    .ms-card.absent::after  { background: #e11d48; }

    .ms-card.late    { background: #fffbeb; }
    .ms-card.late::after    { background: #d97706; }

    .ms-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 8px;
    }

    .ms-card.present .ms-label { color: #16a34a; }
    .ms-card.absent  .ms-label { color: #e11d48; }
    .ms-card.late    .ms-label { color: #d97706; }

    .ms-val {
        font-size: 36px;
        font-weight: 900;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }

    .ms-card.present .ms-val { color: #16a34a; }
    .ms-card.absent  .ms-val { color: #e11d48; }
    .ms-card.late    .ms-val { color: #d97706; }

    .ms-sub {
        font-size: 11px;
        margin-top: 4px;
        font-weight: 600;
        opacity: .6;
    }

    .ms-card.present .ms-sub { color: #16a34a; }
    .ms-card.absent  .ms-sub { color: #e11d48; }
    .ms-card.late    .ms-sub { color: #d97706; }

    /* ── Mini Calendar ───────────────────────────────────────── */
    .cal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }

    .cal-month-label {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .cal-nav { display: flex; gap: 6px; }

    .cal-nav-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1.5px solid #BFDBFE;
        background: #EFF6FF;
        color: var(--kv-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .cal-nav-btn:hover { background: var(--kv-orange); color: white; border-color: var(--kv-orange); }

    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-bottom: 16px;
    }

    .cal-day-hdr {
        text-align: center;
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        padding: 6px 2px;
        text-transform: uppercase;
    }

    .cal-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        color: #334155;
        transition: all 0.15s;
        cursor: default;
        position: relative;
    }

    .cal-day.today {
        background: var(--kv-orange);
        color: white;
        font-weight: 800;
        box-shadow: 0 4px 10px rgba(12, 82, 166, 0.3);
    }

    .cal-day.hadir      { background: #dcfce7; color: #16a34a; }
    .cal-day.terlambat  { background: #fef9c3; color: #b45309; }
    .cal-day.alpa       { background: #fee2e2; color: #dc2626; }
    .cal-day.cuti       { background: #ede9fe; color: #7c3aed; }
    .cal-day.sakit,
    .cal-day.izin       { background: #fff7ed; color: #ea580c; }
    .cal-day.libur      { background: #f8fafc; color: #cbd5e1; }

    /* dot indicator */
    .cal-day.hadir::after,
    .cal-day.terlambat::after,
    .cal-day.alpa::after,
    .cal-day.cuti::after,
    .cal-day.sakit::after,
    .cal-day.izin::after {
        content: '';
        position: absolute;
        bottom: 3px;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: currentColor;
        opacity: .6;
    }

    /* ── Calendar Legend ─────────────────────────────────────── */
    .cal-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 16px;
        margin-bottom: 20px;
    }

    .leg-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
    }

    .leg-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex-shrink: 0;
    }

    .leg-dot.hadir     { background: #dcfce7; border: 1.5px solid #86efac; }
    .leg-dot.terlambat { background: #fef9c3; border: 1.5px solid #fde047; }
    .leg-dot.alpa      { background: #fee2e2; border: 1.5px solid #fca5a5; }
    .leg-dot.cuti      { background: #ede9fe; border: 1.5px solid #c4b5fd; }
    .leg-dot.sakit     { background: #fff7ed; border: 1.5px solid #fdba74; }
    .leg-dot.libur     { background: #f8fafc; border: 1.5px solid #e2e8f0; }

    /* ── Request Button ──────────────────────────────────────── */
    .request-btn {
        width: 100%;
        background: var(--kv-orange);
        color: white;
        border: none;
        padding: 16px;
        border-radius: 16px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s;
        box-shadow: 0 4px 14px rgba(12, 82, 166, 0.3);
        text-decoration: none;
        letter-spacing: .3px;
    }

    .request-btn:hover {
        background: var(--kv-orange2);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(12, 82, 166, 0.4);
    }

    /* ── Announcements ───────────────────────────────────────── */
    .announcement-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .announcement-item {
        background: #fff;
        border: 1px solid var(--kv-border);
        border-radius: 12px;
        padding: 16px;
        display: flex;
        gap: 14px;
        align-items: flex-start;
        box-shadow: 0 2px 8px rgba(0,0,0,.02);
    }

    .announcement-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 18px;
    }

    .announcement-icon.global { background: #d1fae5; color: #059669; }
    .announcement-icon.departemen { background: #dbeafe; color: #2563eb; }

    .announcement-content { flex: 1; }
    
    .announcement-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--kv-text);
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .announcement-text {
        font-size: 13px;
        color: var(--kv-sub);
        line-height: 1.5;
        margin-bottom: 8px;
    }

    .announcement-meta {
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* ── Responsive ──────────────────────────────────────────── */
    @media (max-width: 640px) {
        .clock-row  { flex-direction: column; align-items: center; text-align: center; }
        .clock-block { text-align: center; }
        .check-btn  { width: 100px; height: 100px; }
        .live-clock { font-size: 36px; }
    }

    @media (max-width: 400px) {
        .ms-val { font-size: 28px; }
        .live-clock { font-size: 30px; }
        .cal-day { font-size: 11px; }
        .shift-banner { flex-direction: column; text-align: center; }
        .shift-time-row { justify-content: center; }
        .time-stats { gap: 6px; }
        .ts-item { padding: 10px 6px; }
        .ms-card { padding: 12px 8px; }
        .ms-label { font-size: 9px; }
    }
</style>
@endpush

@section('content')
<div class="fw">

    {{-- ── Alerts ─────────────────────────────────────────────── --}}
    {{-- @if(session('success') || session('error') || session('info'))
    <div class="alerts" id="alertsWrap">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif
        @if(session('info'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
        @endif
    </div>
    @endif --}}

    <div class="scroll-body" id="scrollBody">

        {{-- ── Header ──────────────────────────────────────────── --}}
        <div class="top-header">
            <div class="header-row">
                <div>
                    <div class="greeting-sub">Selamat datang</div>
                    <div class="greeting-name">{{ auth()->user()->nama }}</div>
                    <div style="font-size: 13px; color: var(--kv-sub); font-weight: 600; margin-top: 4px;">
                        {{ auth()->user()->karyawan->jabatan->nama_jabatan ?? '-' }} • {{ auth()->user()->karyawan->departemen->nama ?? '-' }}
                    </div>
                </div>
                <div class="header-date-pill">
                    <div style="font-size:18px; font-weight:800; line-height:1;">
                        {{ \Carbon\Carbon::now()->format('d') }}
                    </div>
                    <div style="font-size:10px; opacity:.8;">
                        {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('M Y')) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Main Card ────────────────────────────────────────── --}}
        <div class="main-card">

            {{-- Shift Banner --}}
            <div class="shift-banner">
                <div class="shift-icon-wrap">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="shift-text-wrap">
                    <div class="shift-name">{{ $shift ?? 'Belum Ada Shift' }}</div>
                    <div class="shift-time-row">
                        <i class="fas fa-clock"></i>
                        {{ $jamMasuk ?? '--:--' }} – {{ $jamPulang ?? '--:--' }}
                    </div>
                </div>
                @if($shift)
                    <span class="shift-badge">AKTIF</span>
                @endif
            </div>

            {{-- Clock + Check Button --}}
            <div class="clock-row">
                <div class="clock-block">
                    <div class="live-clock" id="liveClock">00:00:00</div>
                    <div class="live-date">
                        {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('l, d F Y')) }}
                    </div>
                </div>

                @if($absensiToday && $absensiToday->jam_masuk && !$absensiToday->jam_pulang)
                    <a href="{{ route('karyawan.absensi.pulang.form') }}" class="check-btn checkout">
                        <i class="fas fa-sign-out-alt"></i>
                        Check Out
                    </a>
                @elseif($absensiToday && $absensiToday->jam_pulang)
                    <div class="check-btn done">
                        <i class="fas fa-check-double"></i>
                        Done
                    </div>
                @else
                    <a href="{{ route('karyawan.absensi.masuk.form') }}" class="check-btn checkin">
                        <i class="fas fa-sign-in-alt"></i>
                        Check In
                    </a>
                @endif
            </div>

            {{-- Time Stats --}}
            <div class="time-stats">
                <div class="ts-item">
                    <div class="ts-icon in"><i class="fas fa-sign-in-alt"></i></div>
                    <div class="ts-val">
                        @if($absensiToday && $absensiToday->jam_masuk)
                            {{ substr($absensiToday->jam_masuk, 0, 5) }}
                        @else
                            --:--
                        @endif
                    </div>
                    <div class="ts-label">Masuk</div>
                </div>

                <div class="ts-item">
                    <div class="ts-icon out"><i class="fas fa-sign-out-alt"></i></div>
                    <div class="ts-val">
                        @if($absensiToday && $absensiToday->jam_pulang)
                            {{ substr($absensiToday->jam_pulang, 0, 5) }}
                        @else
                            --:--
                        @endif
                    </div>
                    <div class="ts-label">Pulang</div>
                </div>

                <div class="ts-item">
                    <div class="ts-icon dur"><i class="fas fa-hourglass-half"></i></div>
                    <div class="ts-val">
                        @if($absensiToday && $absensiToday->jam_masuk && $absensiToday->jam_pulang)
                            @php
                                $diff  = \Carbon\Carbon::parse($absensiToday->jam_masuk)
                                             ->diff(\Carbon\Carbon::parse($absensiToday->jam_pulang));
                                $hours = $diff->h + ($diff->days * 24);
                            @endphp
                            {{ sprintf('%02d:%02d', $hours, $diff->i) }}
                        @else
                            00:00
                        @endif
                    </div>
                    <div class="ts-label">Durasi</div>
                </div>
            </div>

        </div>

        {{-- ── Pengumuman ───────────────────────────────────────── --}}
        @if(isset($pengumuman) && $pengumuman->count() > 0)
        <div class="section-wrap" style="margin-top: 0; padding-top: 5px; background: transparent;">
            <div class="section-header mb-3">
                <div class="section-title">Pengumuman Terbaru</div>
            </div>
            <div class="announcement-list">
                @foreach($pengumuman as $p)
                <div class="announcement-item">
                    <div class="announcement-icon {{ $p->tipe }}">
                        <i class="fas {{ $p->tipe === 'global' ? 'fa-bullhorn' : 'fa-building' }}"></i>
                    </div>
                    <div class="announcement-content">
                        <div class="announcement-title">{{ $p->judul }}</div>
                        <div class="announcement-text">{{ $p->konten }}</div>
                        <div class="announcement-meta">
                            <span><i class="fas fa-clock"></i> {{ $p->created_at->diffForHumans() }}</span>
                            <span><i class="fas fa-user"></i> {{ $p->pembuat->nama ?? 'Admin' }}</span>
                        </div>
                        <div style="font-size: 10px; color: #94a3b8; font-weight: 600; margin-top: 4px;">
                            {{ $p->pembuat->karyawan->jabatan->nama_jabatan ?? '-' }} • {{ $p->pembuat->karyawan->departemen->nama ?? '-' }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Monthly Stats + Calendar ─────────────────────────── --}}
        <div class="section-wrap">

            <div class="section-header">
                <div class="section-title">Kehadiran Bulan Ini</div>
                <a href="{{ route('karyawan.absensi.log') }}" class="see-all-btn">
                    {{ strtoupper(\Carbon\Carbon::now()->translatedFormat('F')) }}
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            {{-- Monthly Stat Cards --}}
            <div class="monthly-stats">
                <div class="ms-card present">
                    <div class="ms-label">Hadir</div>
                    <div class="ms-val">{{ $stats['hadir'] ?? 0 }}</div>
                    <div class="ms-sub">hari</div>
                </div>
                <div class="ms-card absent">
                    <div class="ms-label">Tidak Hadir</div>
                    <div class="ms-val">{{ str_pad($stats['alpa'] ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="ms-sub">hari</div>
                </div>
                <div class="ms-card late">
                    <div class="ms-label">Terlambat</div>
                    <div class="ms-val">{{ str_pad($stats['terlambat'] ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="ms-sub">hari</div>
                </div>
            </div>

            {{-- Mini Calendar --}}
            <div class="cal-header">
                <div class="cal-month-label" id="calLabel">
                    {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </div>
                <div class="cal-nav">
                    <button class="cal-nav-btn" onclick="changeMonth(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="cal-nav-btn" onclick="changeMonth(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div class="cal-grid" id="calGrid"></div>

            <div class="cal-legend">
                <div class="leg-item"><div class="leg-dot hadir"></div> Hadir</div>
                <div class="leg-item"><div class="leg-dot terlambat"></div> Terlambat</div>
                <div class="leg-item"><div class="leg-dot alpa"></div> Tidak Hadir</div>
                <div class="leg-item"><div class="leg-dot cuti"></div> Cuti</div>
                <div class="leg-item"><div class="leg-dot sakit"></div> Sakit / Izin</div>
                <div class="leg-item"><div class="leg-dot libur"></div> Libur</div>
            </div>

            {{-- Request Button --}}
            <a href="{{ route('karyawan.pengajuan.index') }}" class="request-btn">
                <i class="fas fa-plus-circle"></i>
                Ajukan Permohonan
            </a>

        </div>
    </div>{{-- /scroll-body --}}
</div>{{-- /fw --}}
@endsection

@push('scripts')
<script>
/* ── Live Clock ─────────────────────────────────────────────── */
function updateClock() {
    const n = new Date();
    const h = String(n.getHours()).padStart(2, '0');
    const m = String(n.getMinutes()).padStart(2, '0');
    const s = String(n.getSeconds()).padStart(2, '0');
    const el = document.getElementById('liveClock');
    if (el) el.textContent = `${h}:${m}:${s}`;
}
setInterval(updateClock, 1000);
updateClock();

/* ── Mini Calendar ──────────────────────────────────────────── */
const attendanceData = @json($calendarData ?? []);

let curMonth = new Date().getMonth();
let curYear  = new Date().getFullYear();

const ID_MONTHS = [
    'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];

function buildCalendar(month, year) {
    const grid  = document.getElementById('calGrid');
    const label = document.getElementById('calLabel');

    label.textContent = `${ID_MONTHS[month]} ${year}`;
    grid.innerHTML = '';

    // Day headers
    ['Min','Sen','Sel','Rab','Kam','Jum','Sab'].forEach(d => {
        const el = document.createElement('div');
        el.className = 'cal-day-hdr';
        el.textContent = d;
        grid.appendChild(el);
    });

    const today     = new Date();
    const firstDay  = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();

    // Empty leading cells
    for (let i = 0; i < firstDay; i++) {
        const empty = document.createElement('div');
        empty.className = 'cal-day';
        grid.appendChild(empty);
    }

    // Day cells
    for (let d = 1; d <= totalDays; d++) {
        const cell    = document.createElement('div');
        const dateStr = `${year}-${String(month + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear();

        cell.className = 'cal-day';
        cell.textContent = d;

        if (isToday) {
            cell.classList.add('today');
        } else if (attendanceData[dateStr]) {
            cell.classList.add(attendanceData[dateStr].toLowerCase());
        }

        grid.appendChild(cell);
    }
}

function changeMonth(dir) {
    curMonth += dir;
    if (curMonth > 11) { curMonth = 0; curYear++; }
    if (curMonth < 0)  { curMonth = 11; curYear--; }
    buildCalendar(curMonth, curYear);
}

/* ── Auto-hide alerts ───────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    buildCalendar(curMonth, curYear);

    const alerts = document.getElementById('alertsWrap');
    if (alerts) {
        setTimeout(() => {
            alerts.style.transition = 'opacity .4s';
            alerts.style.opacity    = '0';
            setTimeout(() => alerts.remove(), 400);
        }, 4000);
    }
});
</script>
@endpush
