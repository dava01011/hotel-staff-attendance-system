@extends('admin.layouts.app')

@section('title', 'Kalender Shift Pattern')

@push('styles')
<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .month-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        background: white;
        transition: box-shadow 0.2s;
    }
    .month-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }

    .month-header {
        padding: 10px 14px;
        font-weight: 700;
        font-size: 13px;
        color: white;
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .month-body { padding: 10px; }

    .week-row {
        display: grid;
        grid-template-columns: 48px repeat(7, 1fr);
        gap: 3px;
        margin-bottom: 3px;
        align-items: center;
    }

    .week-num {
        font-size: 10px; font-weight: 700; color: #94a3b8;
        text-align: center; padding: 4px 2px;
        border-radius: 6px; background: #f8fafc;
        cursor: pointer; transition: all 0.2s;
        text-decoration: none;
    }
    .week-num:hover { background: #eff6ff; color: #1d4ed8; }

    .day-cell {
        padding: 4px 2px;
        border-radius: 6px;
        text-align: center;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.2;
    }
    .day-cell.kerja        { background: #d1fae5; color: #065f46; }
    .day-cell.libur        { background: #fee2e2; color: #991b1b; }
    .day-cell.kerja.override { background: #a7f3d0; color: #064e3b; outline: 2px solid #10b981; }
    .day-cell.libur.override { background: #fecaca; color: #7f1d1d; outline: 2px solid #ef4444; }
    .day-cell.empty        { background: transparent; }
    .day-cell.today        { outline: 2px solid #f59e0b; outline-offset: 1px; }

    .day-header-row {
        display: grid;
        grid-template-columns: 48px repeat(7, 1fr);
        gap: 3px;
        margin-bottom: 6px;
    }
    .day-header-cell {
        text-align: center; font-size: 10px; font-weight: 700;
        color: #64748b; padding: 3px 2px;
    }

    .legend {
        display: flex; flex-wrap: wrap; gap: 10px; align-items: center;
        font-size: 12px;
    }
    .legend-item { display: flex; align-items: center; gap: 5px; }
    .legend-dot {
        width: 14px; height: 14px; border-radius: 4px; flex-shrink: 0;
    }

    .year-nav {
        display: flex; align-items: center; gap: 10px;
    }
    .year-btn {
        border: 2px solid #e9ecef; background: white; border-radius: 8px;
        width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; color: #6c757d; font-size: 14px; text-decoration: none;
    }
    .year-btn:hover { border-color: #0d6efd; color: #0d6efd; background: #eff6ff; }

    .employee-info { display: flex; align-items: center; gap: 12px; }
    .avatar-initial {
        width: 44px; height: 44px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 18px; color: white;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        flex-shrink: 0;
    }
    .profile-img {
        width: 44px; height: 44px; border-radius: 50%;
        object-fit: cover; border: 2px solid #e9ecef; flex-shrink: 0;
    }

    .stat-card {
        border-radius: 10px; padding: 14px 18px; text-align: center;
    }
    .stat-card .stat-num { font-size: 22px; font-weight: 700; }
    .stat-card .stat-label { font-size: 11px; font-weight: 600; opacity: 0.8; }

    @media (max-width: 576px) {
        .calendar-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.shift-pattern.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-1">Kalender Shift Pattern</h4>
        <small class="text-muted">Visualisasi jadwal kerja sepanjang tahun</small>
    </div>
</div>

{{-- Karyawan + Year Nav --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="employee-info">
                @if(!empty($karyawan->foto_profil))
                    <img src="{{ asset('storage/' . $karyawan->foto_profil) }}"
                         alt="{{ $karyawan->user->nama ?? '' }}" class="profile-img">
                @else
                    <div class="avatar-initial">
                        {{ strtoupper(substr($karyawan->user->nama ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="fw-bold" style="color:#2d3748;font-size:15px;">
                        {{ $karyawan->user->nama ?? '-' }}
                    </div>
                    <div class="text-muted" style="font-size:12px;">NIP: {{ $karyawan->nip ?? '-' }}</div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                {{-- Year Navigator --}}
                <div class="year-nav">
                    <a href="{{ route('admin.shift-pattern.calendar', $karyawan->id) }}?tahun={{ $tahun - 1 }}"
                       class="year-btn">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <span class="fw-bold" style="font-size:18px;min-width:60px;text-align:center;">
                        {{ $tahun }}
                    </span>
                    <a href="{{ route('admin.shift-pattern.calendar', $karyawan->id) }}?tahun={{ $tahun + 1 }}"
                       class="year-btn">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>

                {{-- Actions --}}
                <a href="{{ route('admin.shift-pattern.default.edit', $karyawan->id) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-sliders-h me-1"></i>Edit Default
                </a>
                <a href="{{ route('admin.shift-pattern.weekly.edit', $karyawan->id) }}"
                   class="btn btn-sm btn-outline-warning text-dark">
                    <i class="fas fa-calendar-week me-1"></i>Override Minggu
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
@php
    // Hitung stats dari default pattern
    $defaultPatternMap = [];
    if ($defaultPattern) {
        foreach ($defaultPattern as $dp) {
            $defaultPatternMap[$dp->hari] = $dp->tipe;
        }
    }
    $defaultKerjaCount = collect($defaultPatternMap)->filter(fn($t) => $t === 'kerja')->count();
    $defaultLiburCount = collect($defaultPatternMap)->filter(fn($t) => $t === 'libur')->count();
    $overrideMingguCount = $weeklyOverrides->keys()->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#eff6ff;color:#1d4ed8;">
            <div class="stat-num">{{ $defaultKerjaCount }}</div>
            <div class="stat-label">Hari Kerja / Minggu (Default)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#fef2f2;color:#dc2626;">
            <div class="stat-num">{{ $defaultLiburCount }}</div>
            <div class="stat-label">Hari Libur / Minggu (Default)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#fffbeb;color:#d97706;">
            <div class="stat-num">{{ $overrideMingguCount }}</div>
            <div class="stat-label">Minggu Override di {{ $tahun }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:#f0fdf4;color:#16a34a;">
            <div class="stat-num">{{ 52 - $overrideMingguCount }}</div>
            <div class="stat-label">Minggu Pakai Default</div>
        </div>
    </div>
</div>

{{-- Legend --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <div class="legend">
            <span class="fw-semibold" style="font-size:12px;color:#64748b;">Keterangan:</span>
            <span class="legend-item">
                <span class="legend-dot" style="background:#d1fae5;border:1px solid #6ee7b7;"></span>
                Kerja (default)
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="background:#fee2e2;border:1px solid #fca5a5;"></span>
                Libur (default)
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="background:#a7f3d0;outline:2px solid #10b981;outline-offset:1px;"></span>
                Kerja (override)
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="background:#fecaca;outline:2px solid #ef4444;outline-offset:1px;"></span>
                Libur (override)
            </span>
            <span class="legend-item">
                <span class="legend-dot" style="background:#fef9c3;outline:2px solid #f59e0b;outline-offset:1px;"></span>
                Hari ini
            </span>
            <span class="legend-item ms-2" style="font-size:11px;color:#94a3b8;">
                Klik nomor minggu untuk edit override
            </span>
        </div>
    </div>
</div>

{{-- Calendar --}}
@php
    $hariOrder = ['sen','sel','rab','kam','jum','sab','min'];
    $hariLabel = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
    $hariMap   = ['sen'=>'senin','sel'=>'selasa','rab'=>'rabu','kam'=>'kamis','jum'=>'jumat','sab'=>'sabtu','min'=>'minggu'];
    $today     = \Carbon\Carbon::today();
    $todayWeek = $today->weekOfYear;
    $todayYear = $today->year;

    $monthNames = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
        9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
    ];

    // Build override map: minggu_ke => hari => tipe
    $overrideMap = [];
    foreach ($weeklyOverrides as $mingguKe => $patterns) {
        foreach ($patterns as $p) {
            $overrideMap[$mingguKe][$p->hari] = $p->tipe;
        }
    }
@endphp

<div class="calendar-grid">
    @for($month = 1; $month <= 12; $month++)
        @php
            $firstDay  = \Carbon\Carbon::create($tahun, $month, 1);
            $lastDay   = $firstDay->copy()->endOfMonth();
            $weeksInMonth = [];

            // Kumpulkan semua minggu yang ada di bulan ini
            $current = $firstDay->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
            while ($current->lte($lastDay)) {
                $weekNum = $current->weekOfYear;
                $weekYear = $current->year;
                // Minggu harus overlap dengan bulan ini
                $weekEnd = $current->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                if ($current->month == $month || $weekEnd->month == $month) {
                    $weeksInMonth[$weekNum] = [
                        'start' => $current->copy(),
                        'end'   => $weekEnd->copy(),
                        'year'  => $weekYear,
                    ];
                }
                $current->addWeek();
            }
        @endphp

        <div class="month-card">
            <div class="month-header">
                <span>{{ $monthNames[$month] }} {{ $tahun }}</span>
                <span style="font-size:11px;opacity:0.8;">{{ count($weeksInMonth) }} minggu</span>
            </div>
            <div class="month-body">
                {{-- Day headers --}}
                <div class="day-header-row">
                    <div class="day-header-cell" style="color:#94a3b8;font-size:9px;">Mgg</div>
                    @foreach($hariLabel as $lbl)
                        <div class="day-header-cell">{{ $lbl }}</div>
                    @endforeach
                </div>

                @foreach($weeksInMonth as $weekNum => $weekInfo)
                    @php
                        $hasOverride = isset($overrideMap[$weekNum]);
                        $isThisWeek  = ($weekNum === $todayWeek && $tahun === $todayYear);
                    @endphp
                    <div class="week-row">
                        {{-- Week number (clickable) --}}
                        <a href="{{ route('admin.shift-pattern.weekly.edit', $karyawan->id) }}?minggu_ke={{ $weekNum }}&tahun={{ $weekInfo['year'] }}"
                           class="week-num {{ $hasOverride ? 'text-warning fw-bold' : '' }}">
                            W{{ $weekNum }}
                        </a>

                        @foreach($hariOrder as $idx => $hariShort)
                            @php
                                $hariFullKey = $hariMap[$hariShort];
                                $isOverride  = $hasOverride && isset($overrideMap[$weekNum][$hariFullKey]);
                                $tipe        = $isOverride
                                    ? $overrideMap[$weekNum][$hariFullKey]
                                    : ($defaultPatternMap[$hariFullKey] ?? null);

                                // startOfWeek = Senin (+0), Selasa (+1), ..., Minggu (+6)
                                // $hariOrder = ['sen','sel','rab','kam','jum','sab','min']
                                $cellDate = $weekInfo['start']->copy()->addDays($idx);

                                $inMonth = ($cellDate->month == $month);
                                $isToday = $cellDate->toDateString() === $today->toDateString();
                            @endphp
                            @if(!$inMonth)
                                <div class="day-cell empty"></div>
                            @elseif($tipe)
                                <div class="day-cell {{ $tipe }}{{ $isOverride ? ' override' : '' }}{{ $isToday ? ' today' : '' }}"
                                     title="{{ $cellDate->format('d M Y') }} - {{ ucfirst($tipe) }}{{ $isOverride ? ' (override)' : '' }}">
                                    {{ $cellDate->format('d') }}
                                </div>
                            @else
                                <div class="day-cell empty{{ $isToday ? ' today' : '' }}"
                                     style="color:#cbd5e1;font-size:10px;">
                                    {{ $cellDate->format('d') }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endfor
</div>

<div class="mt-4 text-center text-muted" style="font-size:12px;">
    Klik nomor minggu (W##) untuk langsung mengedit override minggu tersebut.
</div>

@endsection

@push('scripts')
<script>
    // Tooltip untuk day cells
    document.querySelectorAll('.day-cell[title]').forEach(cell => {
        cell.style.cursor = 'default';
    });
</script>
@endpush
