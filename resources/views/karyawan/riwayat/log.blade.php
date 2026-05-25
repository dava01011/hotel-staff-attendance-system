@extends('karyawan.layout.master')

@section('title', 'Riwayat')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
    }

    body {
        margin: 0;
        padding: 0;
        background: #ffffff;
        overflow: hidden;
    }

    .fullscreen-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    /* ═══════════════════════════════════════════════════════ */
    /* HEADER BAR */
    /* ═══════════════════════════════════════════════════════ */
    .header-bar {
        margin-top: 70px;
        background: #2d2d2e;
        color: white;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .header-icon {
        font-size: 16px;
    }

    .header-title {
        font-size: 16px;
        font-weight: 500;
    }

    /* ═══════════════════════════════════════════════════════ */
    /* NAVIGATION TABS */
    /* ═══════════════════════════════════════════════════════ */
    .nav-tabs {
        background: white;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        flex-shrink: 0;
    }

    .nav-tab {
        flex: 1;
        padding: 12px 16px;
        text-align: center;
        border: none;
        background: none;
        color: #6c757d;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        position: relative;
        transition: all 0.3s;
    }

    .nav-tab.active {
        color: #2d2d2e;
    }

    .nav-tab.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #2d2d2e;
    }

    .nav-tab i {
        margin-right: 6px;
        font-size: 14px;
    }

    /* ═══════════════════════════════════════════════════════ */
    /* FILTER SECTION */
    /* ═══════════════════════════════════════════════════════ */
    .filter-section {
        background: #f8f9fa;
        padding: 12px 20px;
        border-bottom: 1px solid #e9ecef;
        flex-shrink: 0;
    }

    .month-selector {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        cursor: pointer;
    }

    .month-selector i {
        color: #6c757d;
        font-size: 14px;
    }

    .month-text {
        flex: 1;
        font-size: 13px;
        color: #495057;
        font-weight: 500;
    }

    .month-input {
        display: none;
    }

    /* Filter pills untuk tab cuti */
    .filter-pills {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .filter-pills::-webkit-scrollbar {
        display: none;
    }

    .filter-pill {
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #dee2e6;
        background: white;
        color: #6c757d;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
    }

    .filter-pill.active {
        border-color: #2d2d2e;
        color: #2d2d2e;
        background: #f0f0f0;
    }

    .filter-pill.pending.active {
        border-color: #f59e0b;
        color: #92400e;
        background: #fef3c7;
    }

    .filter-pill.disetujui.active {
        border-color: #10b981;
        color: #065f46;
        background: #d1fae5;
    }

    .filter-pill.ditolak.active {
        border-color: #ef4444;
        color: #991b1b;
        background: #fee2e2;
    }

    /* ═══════════════════════════════════════════════════════ */
    /* CONTENT AREA */
    /* ═══════════════════════════════════════════════════════ */
    .content-wrapper {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        background: #ffffff;
        margin-bottom: 70px;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* ═══════════════════════════════════════════════════════ */
    /* ABSENSI LIST */
    /* ═══════════════════════════════════════════════════════ */
    .list-item {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 16px;
        background: white;
        transition: background 0.2s;
    }

    .list-item:active {
        background: #fafafa;
    }

    .list-item.today {
        background: #f8fbff;
    }

    /* Date Column */
    .date-col {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .date-box {
        width: 48px;
        height: 48px;
        background: #354591;
        color: white;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .date-num {
        font-size: 18px;
        font-weight: 700;
        line-height: 1;
    }

    .date-month {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0.8;
        margin-top: 2px;
    }

    .date-info {
        flex: 1;
    }

    .day-name {
        font-size: 14px;
        font-weight: 600;
        color: #212529;
        margin-bottom: 2px;
    }

    .full-date {
        font-size: 12px;
        color: #6c757d;
    }

    /* Time Column */
    .time-col {
        display: flex;
        gap: 12px;
        align-items: center;
        min-width: 80px;
    }

    .time-item {
        text-align: center;
        min-width: 40px;
    }

    .time-value {
        font-size: 14px;
        font-weight: 600;
        color: #212529;
    }

    .time-value.empty {
        color: #adb5bd;
    }

    /* Status Badge */
    .status-badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .status-badge.hadir {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.terlambat {
        background: #fff3cd;
        color: #856404;
    }

    .status-badge.alpha {
        background: #f8d7da;
        color: #721c24;
    }

    .status-badge.izin {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.disetujui {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.ditolak {
        background: #fee2e2;
        color: #991b1b;
    }

    /* ═══════════════════════════════════════════════════════ */
    /* CUTI CARD */
    /* ═══════════════════════════════════════════════════════ */
    .cuti-card {
        padding: 14px 20px;
        border-bottom: 1px solid #f0f0f0;
        background: white;
        transition: background 0.2s;
    }

    .cuti-card:active {
        background: #fafafa;
    }

    .cuti-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .cuti-title {
        font-size: 14px;
        font-weight: 600;
        color: #212529;
    }

    .cuti-sub {
        font-size: 12px;
        color: #6c757d;
        margin-top: 2px;
    }

    .cuti-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .cuti-info i {
        width: 14px;
        text-align: center;
    }

    .cuti-days {
        display: inline-block;
        padding: 2px 8px;
        background: #f0f0ff;
        color: #667eea;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 700;
    }

    .cuti-rejection {
        margin-top: 10px;
        padding: 10px 12px;
        background: #fff5f5;
        border-radius: 6px;
        border-left: 3px solid #ef4444;
    }

    .cuti-rejection-title {
        font-size: 10px;
        font-weight: 700;
        color: #991b1b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }

    .cuti-rejection-text {
        font-size: 12px;
        color: #991b1b;
    }

    /* ═══════════════════════════════════════════════════════ */
    /* EMPTY STATE */
    /* ═══════════════════════════════════════════════════════ */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 28px;
    }

    .empty-text {
        font-size: 14px;
        color: #495057;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .empty-sub {
        font-size: 12px;
        color: #adb5bd;
    }

    /* ═══════════════════════════════════════════════════════ */
    /* PAGINATION */
    /* ═══════════════════════════════════════════════════════ */
    .pagination-wrapper {
        padding: 16px 20px;
        background: white;
        border-top: 1px solid #e9ecef;
        flex-shrink: 0;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .pagination a,
    .pagination span {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        font-size: 13px;
        color: #495057;
        text-decoration: none;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: #2d2d2e;
        color: white;
    }

    .pagination .active span {
        background: #2d2d2e;
        color: white;
    }

    .pagination .disabled span {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* ═══════════════════════════════════════════════════════ */
    /* RESPONSIVE */
    /* ═══════════════════════════════════════════════════════ */
    @media (max-width: 480px) {
        .header-bar {
            padding: 12px 16px;
        }

        .filter-section {
            padding: 10px 16px;
        }

        .list-item,
        .cuti-card {
            padding: 12px 16px;
            gap: 10px;
        }

        .date-box {
            width: 44px;
            height: 44px;
        }

        .date-num {
            font-size: 16px;
        }

        .day-name {
            font-size: 13px;
        }

        .time-col {
            gap: 8px;
        }

        .time-value {
            font-size: 13px;
        }
    }
</style>

<div class="fullscreen-wrapper">
    <!-- Header -->
    <div class="header-bar">
        <i class="fas fa-history header-icon"></i>
        <div class="header-title">Riwayat</div>
    </div>

    <!-- Navigation Tabs -->
    <div class="nav-tabs">
        <button class="nav-tab active" data-tab="absensi">
            <i class="fas fa-clock"></i> Absensi
        </button>
        <button class="nav-tab" data-tab="cuti">
            <i class="fas fa-calendar-alt"></i> Cuti
        </button>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <label for="bulan-filter" class="month-selector">
            <i class="far fa-calendar-alt"></i>
            <span class="month-text" id="monthText">
                {{ \Carbon\Carbon::parse($bulan ?? date('Y-m'))->translatedFormat('F Y') }}
            </span>
            <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
        </label>
        <form method="GET" id="filterForm" style="display: none;">
            <input type="month" name="bulan" id="bulan-filter" value="{{ $bulan ?? date('Y-m') }}"
                   class="month-input" onchange="updateMonthText(); this.form.submit()">
        </form>

        <!-- Filter pills untuk tab cuti (hidden by default) -->
        <div class="filter-pills" id="cutiFilterPills" style="display: none; margin-top: 10px;">
            <button class="filter-pill active" data-cuti-status="semua">Semua</button>
            <button class="filter-pill pending" data-cuti-status="pending">Menunggu</button>
            <button class="filter-pill disetujui" data-cuti-status="disetujui">Disetujui</button>
            <button class="filter-pill ditolak" data-cuti-status="ditolak">Ditolak</button>
        </div>
    </div>

    <!-- Content -->
    <div class="content-wrapper">
        <!-- TAB: ABSENSI -->
        <div class="tab-content active" id="tab-absensi">
            @forelse($absensi as $row)
                @php
                    $tanggal = \Carbon\Carbon::parse($row->tanggal);
                    $isToday = $tanggal->isToday();
                @endphp

                <div class="list-item {{ $isToday ? 'today' : '' }}">
                    <div class="date-col">
                        <div class="date-box">
                            <div class="date-num">{{ $tanggal->format('d') }}</div>
                            <div class="date-month">{{ $tanggal->format('M') }}</div>
                        </div>
                        <div class="date-info">
                            <div class="day-name">{{ $tanggal->translatedFormat('l') }}</div>
                            <div class="full-date">{{ $tanggal->translatedFormat('d F Y') }}</div>
                        </div>
                    </div>

                    <div class="time-col">
                        <div class="time-item">
                            <div class="time-value {{ $row->jam_masuk ? '' : 'empty' }}">
                                {{ $row->jam_masuk ? date('H:i', strtotime($row->jam_masuk)) : '--:--' }}
                            </div>
                        </div>

                        <div class="time-item">
                            <div class="time-value {{ $row->jam_pulang ? '' : 'empty' }}">
                                {{ $row->jam_pulang ? date('H:i', strtotime($row->jam_pulang)) : '--:--' }}
                            </div>
                        </div>
                    </div>

                    @if ($row->status == 'hadir')
                        <span class="status-badge hadir">Hadir</span>
                    @elseif($row->status == 'terlambat')
                        <span class="status-badge terlambat">Terlambat</span>
                    @elseif($row->status == 'alpha')
                        <span class="status-badge alpha">Alpha</span>
                    @elseif($row->status == 'izin')
                        <span class="status-badge izin">Izin</span>
                    @endif
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="far fa-calendar-times"></i>
                    </div>
                    <div class="empty-text">Tidak ada data</div>
                    <div class="empty-sub">Belum ada riwayat absensi bulan ini</div>
                </div>
            @endforelse

            @if ($absensi->hasPages())
                <div class="pagination-wrapper">
                    {{ $absensi->appends(['bulan' => $bulan ?? date('Y-m')])->links() }}
                </div>
            @endif
        </div>

        <!-- TAB: CUTI -->
        <div class="tab-content" id="tab-cuti">
            @forelse($cuti as $item)
                <div class="cuti-card" data-cuti-status="{{ $item->status }}">
                    <div class="cuti-header">
                        <div>
                            <div class="cuti-title">{{ $item->jenisCuti->nama ?? 'Cuti' }}</div>
                            <div class="cuti-sub">Diajukan {{ $item->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="status-badge {{ $item->status }}">
                            @if($item->status === 'pending') Menunggu
                            @elseif($item->status === 'disetujui') Disetujui
                            @else Ditolak
                            @endif
                        </span>
                    </div>

                    <div class="cuti-info">
                        <i class="far fa-calendar-alt"></i>
                        <span>
                            {{ $item->tanggal_mulai->format('d M Y') }}
                            –
                            {{ $item->tanggal_selesai->format('d M Y') }}
                        </span>
                        <span class="cuti-days">{{ $item->jumlah_hari }} hari</span>
                    </div>

                    @if($item->alasan)
                    <div class="cuti-info">
                        <i class="fas fa-comment-alt"></i>
                        <span>{{ Str::limit($item->alasan, 60) }}</span>
                    </div>
                    @endif

                    @if($item->status === 'ditolak' && $item->catatan_admin)
                    <div class="cuti-rejection">
                        <div class="cuti-rejection-title">
                            <i class="fas fa-times-circle"></i> Alasan Ditolak
                        </div>
                        <div class="cuti-rejection-text">{{ $item->catatan_admin }}</div>
                    </div>
                    @endif
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="far fa-calendar-times"></i>
                    </div>
                    <div class="empty-text">Belum ada riwayat cuti</div>
                    <div class="empty-sub">Pengajuan cuti kamu akan muncul di sini</div>
                </div>
            @endforelse

            @if ($cuti->hasPages())
                <div class="pagination-wrapper">
                    {{ $cuti->appends(['bulan' => $bulan ?? date('Y-m')])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// ═════════════════════════════════════════════════════════
// TAB NAVIGATION
// ═════════════════════════════════════════════════════════
document.querySelectorAll('.nav-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const tabName = this.dataset.tab;

        // Update active tab
        document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        // Update active content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById('tab-' + tabName).classList.add('active');

        // Show/hide filter pills
        const cutiFilterPills = document.getElementById('cutiFilterPills');
        if (tabName === 'cuti') {
            cutiFilterPills.style.display = 'flex';
        } else {
            cutiFilterPills.style.display = 'none';
        }
    });
});

// ═════════════════════════════════════════════════════════
// MONTH PICKER
// ═════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function() {
    const monthSelector = document.querySelector('.month-selector');
    monthSelector.addEventListener('click', function() {
        document.getElementById('bulan-filter').showPicker();
    });

    // Auto scroll to today (absensi)
    const todayItem = document.querySelector('.list-item.today');
    if (todayItem) {
        setTimeout(() => {
            todayItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 300);
    }
});

function updateMonthText() {
    const monthInput = document.getElementById('bulan-filter');
    const monthText = new Date(monthInput.value + '-01').toLocaleDateString('id-ID', {
        month: 'long',
        year: 'numeric'
    });
    document.getElementById('monthText').textContent = monthText;
}

// ═════════════════════════════════════════════════════════
// CUTI FILTER PILLS
// ═════════════════════════════════════════════════════════
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        const status = this.dataset.cutiStatus;

        // Update active pill
        document.querySelectorAll('.filter-pill').forEach(p => {
            p.classList.remove('active');
        });
        this.classList.add('active');

        // Filter cuti cards
        const cards = document.querySelectorAll('.cuti-card');
        const visibleCards = [];

        cards.forEach(card => {
            if (status === 'semua' || card.dataset.cutiStatus === status) {
                card.style.display = '';
                visibleCards.push(card);
            } else {
                card.style.display = 'none';
            }
        });

        // Show empty state if nothing visible
        let emptyEl = document.querySelector('.empty-filter');

        if (visibleCards.length === 0 && status !== 'semua') {
            if (!emptyEl) {
                emptyEl = document.createElement('div');
                emptyEl.className = 'empty-state empty-filter';
                emptyEl.innerHTML = `
                    <div class="empty-icon"><i class="far fa-folder-open"></i></div>
                    <div class="empty-text">Tidak ada data</div>
                    <div class="empty-sub">Belum ada cuti dengan status ini</div>
                `;
                document.querySelector('.tab-content#tab-cuti').appendChild(emptyEl);
            }
            emptyEl.style.display = 'flex';
        } else if (emptyEl) {
            emptyEl.style.display = 'none';
        }
    });
});
</script>

@endsection
