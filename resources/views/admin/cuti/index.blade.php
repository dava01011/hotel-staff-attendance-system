@extends('admin.layouts.app')

@section('title', 'Riwayat Cuti')

@push('styles')
<style>
    /* ── Search Box ─────────────────────────────────────────── */
    .search-container {
        position: relative;
        flex: 1;
        max-width: 500px;
    }

    .search-input {
        padding-left: 45px;
        border-radius: 25px;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
    }

    .search-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        pointer-events: none;
    }

    .clear-search {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 5px;
        display: none;
        transition: color 0.2s;
    }

    .clear-search:hover { color: #dc3545; }
    .clear-search.show  { display: block; }

    /* ── Filter Buttons ─────────────────────────────────────── */
    .filter-group { display: flex; gap: 8px; flex-wrap: wrap; }

    .filter-btn {
        padding: 6px 16px;
        border: 2px solid #e9ecef;
        background: white;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filter-btn:hover         { border-color: #0d6efd; background: #f8f9fa; }
    .filter-btn.active        { background: #0d6efd; color: white; border-color: #0d6efd; }
    .filter-btn .count        { background: rgba(0,0,0,.1); padding: 2px 8px; border-radius: 10px; font-size: 11px; }
    .filter-btn.active .count { background: rgba(255,255,255,.2); }

    /* ── Table Highlight ────────────────────────────────────── */
    .table tbody tr { transition: background-color 0.2s; }

    .table tbody tr.highlight {
        background-color: #fff3cd !important;
        animation: highlight-fade 1.5s ease-out;
    }

    @keyframes highlight-fade {
        from { background-color: #fff3cd; }
        to   { background-color: transparent; }
    }

    /* ── Employee Info ──────────────────────────────────────── */
    .employee-info    { display: flex; align-items: center; gap: 12px; }
    .employee-details { display: flex; flex-direction: column; }
    .employee-name    { font-weight: 600; color: #2d3748; margin-bottom: 2px; }
    .employee-sub     { font-size: 12px; color: #718096; }

    .avatar-initial {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        color: white;
        flex-shrink: 0;
    }

    /* ── Date Range ─────────────────────────────────────────── */
    .date-range {
        display: inline-flex;
        flex-direction: column;
        gap: 2px;
    }

    .date-main { font-weight: 600; font-size: 13px; color: #334155; }
    .date-day  { font-size: 11px; color: #94a3b8; }

    /* ── Status Badges ──────────────────────────────────────── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.disetujui { background: #d1fae5; color: #065f46; }
    .status-badge.ditolak   { background: #fee2e2; color: #991b1b; }
    .status-badge.pending   { background: #fef9c3; color: #854d0e; }
    .status-badge.default   { background: #f1f5f9; color: #475569; }

    /* ── Alasan / Catatan ───────────────────────────────────── */
    .alasan-text {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 13px;
        color: #475569;
        cursor: default;
    }

    .catatan-text {
        max-width: 160px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 12px;
        color: #64748b;
        font-style: italic;
    }

    /* ── Duration Badge ─────────────────────────────────────── */
    .duration-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: #eff6ff;
        color: #2563eb;
        margin-top: 3px;
    }

    /* ── No Results ─────────────────────────────────────────── */
    .no-results      { display: none; text-align: center; padding: 60px 20px; }
    .no-results.show { display: block; }
    .no-results-icon    { font-size: 64px; color: #dee2e6; margin-bottom: 20px; }
    .no-results-text    { color: #6c757d; font-size: 16px; margin-bottom: 10px; font-weight: 600; }
    .no-results-subtext { color: #adb5bd; font-size: 14px; }

    /* ── Search Stats ───────────────────────────────────────── */
    .search-stats {
        font-size: 14px;
        color: #6c757d;
        display: none;
        padding: 12px 0;
        border-top: 1px solid #e9ecef;
        margin-top: 15px;
    }

    .search-stats.show   { display: flex; justify-content: space-between; align-items: center; }
    .search-stats strong { color: #0d6efd; }

    .reset-filter       { font-size: 13px; color: #dc3545; text-decoration: none; font-weight: 600; display: none; }
    .reset-filter:hover { text-decoration: underline; }
    .reset-filter.show  { display: inline-block; }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 992px) {
        .search-container { max-width: 100%; margin-bottom: 15px; }
        .filter-group     { margin-top: 15px; }
        .search-stats     { flex-direction: column; gap: 10px; align-items: flex-start; }
    }

    @media (max-width: 768px) {
        .alasan-text  { max-width: 120px; }
        .catatan-text { max-width: 100px; }
    }
</style>
@endpush

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Riwayat Cuti</h4>
            <small class="text-muted">
                @if(in_array($userRole, ['super_admin', 'manager', 'gm']))
                    Data cuti seluruh karyawan (Semua Departemen)
                @else
                    Data cuti karyawan (Departemen Anda)
                @endif
            </small>
        </div>
    </div>

    {{-- ── Search & Filter ──────────────────────────────────── --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text"
                           class="form-control search-input"
                           id="searchInput"
                           placeholder="Cari nama karyawan atau alasan cuti..."
                           autocomplete="off">
                    <button class="clear-search" id="clearSearch" title="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="ms-auto text-muted">
                    <small>Total: <strong>{{ $cuti->count() }}</strong> pengajuan</small>
                </div>
            </div>

            {{-- Filter --}}
            <div class="filter-group mt-3">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-list"></i>
                    Semua
                    <span class="count">{{ $cuti->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="pending">
                    <i class="fas fa-hourglass-half"></i>
                    Pending
                    <span class="count">{{ $cuti->where('status', 'pending')->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="disetujui">
                    <i class="fas fa-check-circle"></i>
                    Disetujui
                    <span class="count">{{ $cuti->where('status', 'disetujui')->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="ditolak">
                    <i class="fas fa-times-circle"></i>
                    Ditolak
                    <span class="count">{{ $cuti->where('status', 'ditolak')->count() }}</span>
                </button>
            </div>

            {{-- Search Stats --}}
            <div class="search-stats" id="searchStats">
                <div>
                    Menampilkan <strong id="resultCount">0</strong>
                    dari <strong>{{ $cuti->count() }}</strong> pengajuan
                </div>
                <a href="#" class="reset-filter" id="resetFilter">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            </div>

        </div>
    </div>

    {{-- ── Table ────────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="cutiTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Karyawan</th>
                            <th>Jenis Cuti</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Catatan Admin</th>
                        </tr>
                    </thead>
                    <tbody id="cutiTableBody">
                        @forelse ($cuti as $item)
                            @php
                                $nama   = strtolower($item->karyawan->user->nama ?? '');
                                $alasan = strtolower($item->alasan ?? '');
                                $status = strtolower($item->status ?? '');

                                $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899'];
                                $colorIdx = ord(substr($nama, 0, 1)) % count($colors);
                                $avatarColor = $colors[$colorIdx];

                                $mulai   = \Carbon\Carbon::parse($item->tanggal_mulai);
                                $selesai = \Carbon\Carbon::parse($item->tanggal_selesai);
                                $durasi  = $mulai->diffInDays($selesai) + 1;
                            @endphp
                            <tr
                                data-nama="{{ $nama }}"
                                data-alasan="{{ $alasan }}"
                                data-status="{{ $status }}"
                                data-id="{{ $item->id }}">

                                <td>{{ $loop->iteration }}</td>

                                {{-- Karyawan --}}
                                <td>
                                    <div class="employee-info">
                                        <div class="avatar-initial" style="background: {{ $avatarColor }};">
                                            {{ strtoupper(substr($item->karyawan->user->nama ?? 'K', 0, 1)) }}
                                        </div>
                                        <div class="employee-details">
                                            <div class="employee-name">{{ $item->karyawan->user->nama ?? '-' }}</div>
                                            <div class="employee-sub">
                                                <i class="fas fa-id-badge me-1"></i>{{ $item->karyawan->nip ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Jenis Cuti --}}
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $item->jenisCuti->nama ?? 'Cuti Tahunan' }}
                                    </span>
                                </td>

                                {{-- Tanggal Mulai --}}
                                <td>
                                    <div class="date-range">
                                        <span class="date-main">{{ $mulai->format('d M Y') }}</span>
                                        <span class="date-day">{{ $mulai->translatedFormat('l') }}</span>
                                    </div>
                                </td>

                                {{-- Tanggal Selesai --}}
                                <td>
                                    <div class="date-range">
                                        <span class="date-main">{{ $selesai->format('d M Y') }}</span>
                                        <span class="date-day">{{ $selesai->translatedFormat('l') }}</span>
                                        <span class="duration-pill">
                                            <i class="fas fa-calendar-day"></i> {{ $durasi }} hari
                                        </span>
                                    </div>
                                </td>

                                {{-- Alasan --}}
                                <td>
                                    <span class="alasan-text" title="{{ $item->alasan ?? '-' }}">
                                        {{ $item->alasan ?? '-' }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @php
                                        $statusClass = match($status) {
                                            'disetujui' => 'disetujui',
                                            'ditolak'   => 'ditolak',
                                            'pending'   => 'pending',
                                            default     => 'default',
                                        };
                                        $statusIcon = match($status) {
                                            'disetujui' => 'check-circle',
                                            'ditolak'   => 'times-circle',
                                            'pending'   => 'hourglass-half',
                                            default     => 'circle',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        <i class="fas fa-{{ $statusIcon }}" style="font-size:10px;"></i>
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>

                                {{-- Catatan Admin --}}
                                <td>
                                    @if($item->catatan_admin)
                                        <span class="catatan-text" title="{{ $item->catatan_admin }}">
                                            <i class="fas fa-comment-alt me-1 text-muted"></i>
                                            {{ $item->catatan_admin }}
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size:13px;">—</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr id="emptyState">
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-umbrella-beach fa-3x mb-3" style="opacity:.3;"></i>
                                        <p class="mb-0 fw-medium">Belum ada data cuti</p>
                                        <small>Riwayat pengajuan cuti akan muncul di sini</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- No Results --}}
            <div class="no-results" id="noResults">
                <div class="no-results-icon"><i class="fas fa-search"></i></div>
                <div class="no-results-text">Tidak ada data cuti ditemukan</div>
                <div class="no-results-subtext">Coba ubah kata kunci pencarian atau filter</div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════════
// TABLE FILTERING & SEARCH
// ═══════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const tableBody   = document.getElementById('cutiTableBody');
    const noResults   = document.getElementById('noResults');
    const searchStats = document.getElementById('searchStats');
    const resultCount = document.getElementById('resultCount');
    const resetFilter = document.getElementById('resetFilter');
    const filterBtns  = document.querySelectorAll('.filter-btn');
    const allRows     = tableBody.querySelectorAll('tr:not(#emptyState)');

    let currentFilter = 'all';
    let currentSearch = '';

    /* ── Filter buttons ─────────────────────────── */
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            currentFilter = this.getAttribute('data-filter');
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            applyFilters();
        });
    });

    /* ── Search ─────────────────────────────────── */
    searchInput.addEventListener('input', function () {
        currentSearch = this.value.toLowerCase().trim();
        clearBtn.classList.toggle('show', currentSearch.length > 0);
        applyFilters();
    });

    /* ── Core logic ─────────────────────────────── */
    function applyFilters() {
        let visible = 0;

        allRows.forEach(row => {
            const nama   = row.getAttribute('data-nama');
            const alasan = row.getAttribute('data-alasan');
            const status = row.getAttribute('data-status');

            const searchMatch = !currentSearch ||
                nama.includes(currentSearch)   ||
                alasan.includes(currentSearch);

            let filterMatch = true;
            if (currentFilter !== 'all') filterMatch = status === currentFilter;

            if (searchMatch && filterMatch) {
                row.style.display = '';
                visible++;
                if (currentSearch) {
                    row.classList.add('highlight');
                    setTimeout(() => row.classList.remove('highlight'), 1500);
                }
            } else {
                row.style.display = 'none';
            }
        });

        updateUI(visible);
    }

    function updateUI(visible) {
        resultCount.textContent = visible;
        const active = currentSearch || currentFilter !== 'all';
        searchStats.classList.toggle('show', active);
        resetFilter.classList.toggle('show', active);
        noResults.classList.toggle('show', visible === 0 && active);
    }

    /* ── Clear / Reset ──────────────────────────── */
    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        currentSearch = '';
        clearBtn.classList.remove('show');
        applyFilters();
        searchInput.focus();
    });

    resetFilter.addEventListener('click', function (e) {
        e.preventDefault();
        searchInput.value = '';
        currentSearch = '';
        clearBtn.classList.remove('show');
        currentFilter = 'all';
        filterBtns.forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-filter') === 'all');
        });
        applyFilters();
        searchInput.focus();
    });

    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') clearBtn.click();
    });

    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
    });

    /* ── Toast ──────────────────────────────────── */
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.style.cssText = `
            position:fixed; top:20px; right:20px;
            padding:15px 25px;
            background:${type === 'success' ? '#28a745' : '#dc3545'};
            color:white; border-radius:8px; font-weight:600; font-size:14px;
            z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,.15);
            animation:slideInRight .3s ease-out;
            display:flex; align-items:center; gap:10px;
        `;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideInRight .3s ease-out reverse';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
});
</script>

<style>
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
</style>
@endpush