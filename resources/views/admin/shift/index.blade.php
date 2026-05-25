@extends('admin.layouts.app')

@section('title', 'Data Shift')

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

    /* ── Table ──────────────────────────────────────────────── */
    .table-compact th {
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 11px 14px;
        color: #495057;
    }

    .table-compact td { padding: 10px 14px; vertical-align: middle; font-size: 13px; }

    .table tbody tr { transition: background-color 0.15s; }

    .table tbody tr.highlight {
        background-color: #fff3cd !important;
        animation: highlight-fade 1.5s ease-out forwards;
    }

    @keyframes highlight-fade {
        from { background-color: #fff3cd; }
        to   { background-color: transparent; }
    }

    /* ── Shift Info Cell ────────────────────────────────────── */
    .shift-compact { display: flex; align-items: center; gap: 10px; }

    .shift-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        color: white;
        flex-shrink: 0;
    }

    .shift-name { font-weight: 600; color: #2d3748; line-height: 1.2; }
    .shift-sub  { font-size: 11px; color: #94a3b8; margin-top: 1px; }

    /* ── Time Badge ─────────────────────────────────────────── */
    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 11px;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 700;
        background: #f1f5f9;
        color: #334155;
    }

    .time-badge i { font-size: 9px; }

    /* ── Status Badge (untuk Lintas Hari & Toleransi) ───────── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 11px;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-badge i { font-size: 9px; }

    .status-badge.lintas { background: #fef3c7; color: #92400e; }
    .status-badge.normal { background: #dbeafe; color: #1e40af; }

    .status-badge.toleransi { background: #d1fae5; color: #065f46; }
    .status-badge.no-toleransi { background: #fee2e2; color: #991b1b; }

    /* ── Action Buttons ─────────────────────────────────────── */
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .action-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,.12); }

    .action-btn.edit   { background: #fff3cd; color: #d97706; }
    .action-btn.edit:hover   { background: #fbbf24; color: white; }

    .action-btn.delete { background: #fee2e2; color: #dc2626; }
    .action-btn.delete:hover { background: #ef4444; color: white; }

    /* ── No Results / Search Stats ──────────────────────────── */
    .no-results      { display: none; text-align: center; padding: 60px 20px; }
    .no-results.show { display: block; }
    .no-results-icon    { font-size: 56px; color: #dee2e6; margin-bottom: 16px; }
    .no-results-text    { color: #6c757d; font-size: 15px; font-weight: 600; margin-bottom: 6px; }
    .no-results-subtext { color: #adb5bd; font-size: 13px; }

    .search-stats {
        font-size: 14px;
        color: #6c757d;
        display: none;
        padding: 12px 0;
        border-top: 1px solid #e9ecef;
        margin-top: 14px;
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
        .shift-compact { gap: 8px; }
        .shift-icon { width: 30px; height: 30px; font-size: 11px; }
    }
</style>
@endpush

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Data Shift</h4>
            <small class="text-muted">Manajemen shift kerja</small>
        </div>
        <button class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#tambahShift">
            <i class="fas fa-plus"></i> Tambah Data
        </button>
    </div>

    {{-- ── Search & Filter Card ─────────────────────────────── --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text"
                           class="form-control search-input"
                           id="searchInput"
                           placeholder="Cari kode shift atau jam kerja..."
                           autocomplete="off">
                    <button class="clear-search" id="clearSearch" title="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="ms-auto text-muted">
                    <small>Total: <strong>{{ $shift->count() }}</strong> shift</small>
                </div>
            </div>

            {{-- Filter --}}
            <div class="filter-group mt-3">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-layer-group"></i>
                    Semua
                    <span class="count" id="countAll">{{ $shift->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="lintas">
                    <i class="fas fa-moon"></i>
                    Lintas Hari
                    <span class="count" id="countLintas">{{ $shift->where('lintas_hari', 1)->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="normal">
                    <i class="fas fa-sun"></i>
                    Normal
                    <span class="count" id="countNormal">{{ $shift->where('lintas_hari', 0)->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="toleransi">
                    <i class="fas fa-stopwatch"></i>
                    Ada Toleransi
                    <span class="count" id="countToleransi">{{ $shift->where('toleransi_menit', '>', 0)->count() }}</span>
                </button>
            </div>

            {{-- Search Stats --}}
            <div class="search-stats" id="searchStats">
                <div>
                    Menampilkan <strong id="resultCount">0</strong>
                    dari <strong>{{ $shift->count() }}</strong> shift
                </div>
                <a href="#" class="reset-filter" id="resetFilter">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            </div>

        </div>
    </div>

    {{-- ── Table Card ───────────────────────────────────────── --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-compact align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:44px; padding-left:20px;">#</th>
                            <th style="min-width:100px;">Shift</th>
                            <th style="width:130px;">Jam Masuk</th>
                            <th style="width:130px;">Jam Pulang</th>
                            <th style="width:130px;">Toleransi</th>
                            <th style="width:130px;">Lintas Hari</th>
                            <th style="width:100px; text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="shiftTableBody">
                        @php
                            $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4'];
                        @endphp
                        @forelse ($shift as $item)
                            @php
                                $ci = ord(strtolower(substr($item->kode, 0, 1))) % count($colors);
                            @endphp
                            <tr
                                data-kode="{{ strtolower($item->kode) }}"
                                data-jam="{{ substr($item->jam_masuk,0,5) . ' ' . substr($item->jam_pulang,0,5) }}"
                                data-lintas="{{ $item->lintas_hari ? 'lintas' : 'normal' }}"
                                data-toleransi="{{ $item->toleransi_menit > 0 ? 'ada' : 'tidak' }}">

                                <td style="padding-left:20px; color:#94a3b8; font-size:12px;">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- Shift Info --}}
                                <td>
                                    <div class="shift-compact">
                                        <div class="shift-icon" style="background:{{ $colors[$ci] }};">
                                            {{ strtoupper(substr($item->kode, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="shift-name">{{ $item->kode }}</div>
                                            <div class="shift-sub">
                                                {{ substr($item->jam_masuk,0,5) }} – {{ substr($item->jam_pulang,0,5) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Jam Masuk --}}
                                <td>
                                    <span class="time-badge">
                                        <i class="fas fa-sign-in-alt text-success"></i>
                                        {{ substr($item->jam_masuk, 0, 5) }}
                                    </span>
                                </td>

                                {{-- Jam Pulang --}}
                                <td>
                                    <span class="time-badge">
                                        <i class="fas fa-sign-out-alt text-danger"></i>
                                        {{ substr($item->jam_pulang, 0, 5) }}
                                    </span>
                                </td>

                                {{-- Toleransi --}}
                                <td>
                                    @if($item->toleransi_menit > 0)
                                        <span class="status-badge toleransi">
                                            <i class="fas fa-stopwatch"></i>
                                            {{ $item->toleransi_menit }} menit
                                        </span>
                                    @else
                                        <span class="status-badge no-toleransi">
                                            <i class="fas fa-ban"></i>
                                            Tidak Ada
                                        </span>
                                    @endif
                                </td>

                                {{-- Lintas Hari --}}
                                <td>
                                    @if($item->lintas_hari)
                                        <span class="status-badge lintas">
                                            <i class="fas fa-moon"></i> Ya
                                        </span>
                                    @else
                                        <span class="status-badge normal">
                                            <i class="fas fa-sun"></i> Tidak
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td style="text-align:center;">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button class="action-btn edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#ubahShift{{ $item->id }}"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#hapusShift{{ $item->id }}"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyState">
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-layer-group fa-3x mb-3" style="opacity:.2;"></i>
                                        <p class="mb-0 fw-medium">Belum ada data shift</p>
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
                <div class="no-results-text">Tidak ada shift ditemukan</div>
                <div class="no-results-subtext">Coba ubah kata kunci pencarian atau filter</div>
            </div>
        </div>

        @foreach ($shift as $item)
            @include('admin.shift.edit')
            @include('admin.shift.delete')
        @endforeach
    </div>

    @include('admin.shift.create')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const tableBody   = document.getElementById('shiftTableBody');
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
            const kode      = row.getAttribute('data-kode');
            const jam       = row.getAttribute('data-jam');
            const lintas    = row.getAttribute('data-lintas');
            const toleransi = row.getAttribute('data-toleransi');

            const searchMatch = !currentSearch ||
                kode.includes(currentSearch) ||
                jam.includes(currentSearch);

            let filterMatch = true;
            if (currentFilter === 'lintas')    filterMatch = lintas    === 'lintas';
            if (currentFilter === 'normal')    filterMatch = lintas    === 'normal';
            if (currentFilter === 'toleransi') filterMatch = toleransi === 'ada';

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
            padding:14px 22px;
            background:${type === 'success' ? '#16a34a' : '#dc2626'};
            color:white; border-radius:8px; font-weight:600; font-size:13px;
            z-index:9999; box-shadow:0 4px 14px rgba(0,0,0,.15);
            animation:slideInRight .3s ease-out;
            display:flex; align-items:center; gap:10px;
        `;
        toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideInRight .3s ease-out reverse';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    @if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
    @if(session('error'))   showToast('{{ session('error') }}', 'error');   @endif
});
</script>

<style>
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
</style>
@endpush