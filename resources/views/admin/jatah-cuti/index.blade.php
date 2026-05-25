@extends('admin.layouts.app')

@section('title', 'Data Jatah Cuti')

@push('styles')
<style>
    /* ── Search ─────────────────────────────────────────────── */
    .search-container {
        position: relative;
        flex: 1;
        max-width: 360px;
    }

    .search-input {
        padding-left: 42px;
        border-radius: 25px;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
        font-size: 13px;
    }

    .search-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13,110,253,.1);
    }

    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        pointer-events: none;
        font-size: 13px;
    }

    .clear-search {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        padding: 4px;
        display: none;
        transition: color 0.2s;
        font-size: 12px;
    }

    .clear-search:hover { color: #dc3545; }
    .clear-search.show  { display: block; }

    /* ── Year Filter Pills ──────────────────────────────────── */
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
        color: #495057;
    }

    .filter-btn:hover         { border-color: #0d6efd; background: #f8f9fa; }
    .filter-btn.active        { background: #0d6efd; color: white; border-color: #0d6efd; }
    .filter-btn .count        { background: rgba(0,0,0,.1); padding: 2px 8px; border-radius: 10px; font-size: 11px; }
    .filter-btn.active .count { background: rgba(255,255,255,.2); }

    /* ── Search Stats ───────────────────────────────────────── */
    .search-stats {
        font-size: 13px;
        color: #6c757d;
        display: none;
        padding: 12px 0 0;
        border-top: 1px solid #e9ecef;
        margin-top: 14px;
    }

    .search-stats.show   { display: flex; justify-content: space-between; align-items: center; }
    .search-stats strong { color: #0d6efd; }

    .reset-filter       { font-size: 13px; color: #dc3545; text-decoration: none; font-weight: 600; }
    .reset-filter:hover { text-decoration: underline; }

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

    /* ── Employee Cell ──────────────────────────────────────── */
    .employee-compact { display: flex; align-items: center; gap: 10px; }

    .avatar-small {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        color: white;
        flex-shrink: 0;
    }

    .employee-name { font-weight: 600; color: #2d3748; line-height: 1.2; }
    .employee-dept { font-size: 11px; color: #94a3b8; margin-top: 1px; }

    /* ── Year Badge ─────────────────────────────────────────── */
    .year-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        border: 1px solid #bfdbfe;
    }

    /* ── Quota Bar ──────────────────────────────────────────── */
    .quota-wrap { min-width: 140px; }

    .quota-num {
        font-size: 15px;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 4px;
    }

    .quota-bar-bg {
        height: 5px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }

    .quota-bar-fill {
        height: 100%;
        border-radius: 3px;
        background: linear-gradient(90deg, #3b82f6, #0d6efd);
        transition: width 0.4s ease;
    }

    .quota-bar-fill.high   { background: linear-gradient(90deg, #10b981, #059669); }
    .quota-bar-fill.medium { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .quota-bar-fill.low    { background: linear-gradient(90deg, #ef4444, #dc2626); }

    .quota-label { font-size: 10px; color: #94a3b8; font-weight: 600; margin-top: 2px; }

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

    /* ── No Results ─────────────────────────────────────────── */
    .no-results      { display: none; text-align: center; padding: 60px 20px; }
    .no-results.show { display: block; }
    .no-results-icon    { font-size: 52px; color: #dee2e6; margin-bottom: 14px; }
    .no-results-text    { color: #6c757d; font-size: 15px; font-weight: 600; margin-bottom: 6px; }
    .no-results-subtext { color: #adb5bd; font-size: 13px; }

    /* ── Info Alert ─────────────────────────────────────────── */
    .alert-soft-info {
        background: #e6f2ff;
        border-left: 4px solid #0d6efd;
        color: #084298;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 13px;
    }
</style>
@endpush

@section('content')

    {{-- ── Page Header ──────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Data Jatah Cuti</h4>
            <small class="text-muted">
                Manajemen jatah cuti karyawan
                @if(!$canCRUD && in_array(Auth::user()->role, ['admin', 'manager', 'gm']))
                    - Departemen Anda
                @endif
            </small>
        </div>

        @if($canCRUD)
            <button class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#tambahJatah">
                <i class="fas fa-plus"></i> Tambah Data
            </button>
        @endif
    </div>

    {{-- Info untuk role non-CRUD --}}
    @if(!$canCRUD && in_array(Auth::user()->role, ['admin', 'manager', 'gm']))
        <div class="alert-soft-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            Anda hanya dapat melihat data jatah cuti karyawan di departemen Anda.
            Untuk mengubah data, silakan hubungi HRD atau Super Admin.
        </div>
    @endif

    {{-- ── Filter Card ──────────────────────────────────────── --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">

            {{-- Search + Total --}}
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text"
                           class="form-control search-input"
                           id="searchInput"
                           placeholder="Cari nama karyawan..."
                           autocomplete="off">
                    <button class="clear-search" id="clearSearch">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="ms-auto text-muted">
                    <small>Total: <strong>{{ $jatahCuti->count() }}</strong> data</small>
                </div>
            </div>

            {{-- Year Filter Pills --}}
            @php
                $years = $jatahCuti->pluck('tahun')->unique()->sortDesc()->values();
            @endphp
            @if($years->count() > 1)
            <div class="filter-group mt-3">
                <button class="filter-btn active" data-year="all">
                    <i class="fas fa-layer-group"></i> Semua Tahun
                    <span class="count">{{ $jatahCuti->count() }}</span>
                </button>
                @foreach($years as $year)
                <button class="filter-btn" data-year="{{ $year }}">
                    <i class="fas fa-calendar"></i> {{ $year }}
                    <span class="count">{{ $jatahCuti->where('tahun', $year)->count() }}</span>
                </button>
                @endforeach
            </div>
            @endif

            {{-- Search Stats --}}
            <div class="search-stats" id="searchStats">
                <div>
                    Menampilkan <strong id="resultCount">0</strong>
                    dari <strong>{{ $jatahCuti->count() }}</strong> data
                </div>
                <a href="#" class="reset-filter" id="resetFilter">
                    <i class="fas fa-redo"></i> Reset
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
                            <th style="min-width:200px;">Karyawan</th>
                            <th style="width:110px;">Tahun</th>
                            <th style="min-width:160px;">Jatah Cuti</th>
                            @if($canCRUD)
                                <th style="width:100px; text-align:center;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @php
                            $colors  = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4'];
                            $maxJatah = $jatahCuti->max('jatah') ?: 1;
                        @endphp

                        @forelse ($jatahCuti as $item)
                            @php
                                $nama = $item->karyawan->user->nama ?? '-';
                                $dept = $item->karyawan->departemen->nama ?? null;
                                $ci   = ord(strtolower(substr($nama, 0, 1))) % count($colors);
                                $pct  = min(100, round(($item->jatah / $maxJatah) * 100));
                                $barClass = $pct >= 70 ? 'high' : ($pct >= 40 ? 'medium' : 'low');
                            @endphp
                            <tr data-nama="{{ strtolower($nama) }}"
                                data-year="{{ $item->tahun }}">

                                <td style="padding-left:20px; color:#94a3b8; font-size:12px;">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- Karyawan --}}
                                <td>
                                    <div class="employee-compact">
                                        <div class="avatar-small" style="background:{{ $colors[$ci] }};">
                                            {{ strtoupper(substr($nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="employee-name">{{ $nama }}</div>
                                            @if($dept)
                                                <div class="employee-dept">{{ $dept }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Tahun --}}
                                <td>
                                    <span class="year-badge">
                                        <i class="fas fa-calendar-alt" style="font-size:11px;"></i>
                                        {{ $item->tahun }}
                                    </span>
                                </td>

                                {{-- Jatah --}}
                                <td>
                                    <div class="quota-wrap">
                                        <div class="quota-num">{{ $item->jatah }} <span style="font-size:11px;font-weight:500;color:#94a3b8;">hari</span></div>
                                        <div class="quota-bar-bg">
                                            <div class="quota-bar-fill {{ $barClass }}" style="width:{{ $pct }}%;"></div>
                                        </div>
                                        <div class="quota-label">{{ $pct }}% dari maks</div>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                @if($canCRUD)
                                    <td style="text-align:center;">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="action-btn edit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#ubahJatah{{ $item->id }}"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#hapusJatah{{ $item->id }}"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif

                            </tr>
                        @empty
                            <tr id="emptyState">
                                <td @if($canCRUD) colspan="5" @else colspan="4" @endif class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-umbrella-beach fa-3x mb-3" style="opacity:.2;"></i>
                                        <p class="mb-0 fw-medium">Belum ada data jatah cuti</p>
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
                <div class="no-results-text">Tidak ada data ditemukan</div>
                <div class="no-results-subtext">Coba ubah kata kunci atau filter tahun</div>
            </div>

        </div>
    </div>

    {{-- ── Modals ────────────────────────────────────────────── --}}
    @if($canCRUD)
        @foreach ($jatahCuti as $item)
            @include('admin.jatah-cuti.edit')
            @include('admin.jatah-cuti.delete')
        @endforeach

        @include('admin.jatah-cuti.create')
    @endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const filterBtns  = document.querySelectorAll('.filter-btn');
    const tableBody   = document.getElementById('tableBody');
    const noResults   = document.getElementById('noResults');
    const searchStats = document.getElementById('searchStats');
    const resultCount = document.getElementById('resultCount');
    const resetFilter = document.getElementById('resetFilter');
    const allRows     = tableBody.querySelectorAll('tr:not(#emptyState)');

    let currentSearch = '';
    let currentYear   = 'all';

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            currentYear = this.getAttribute('data-year');
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            applyFilters();
        });
    });

    searchInput.addEventListener('input', function () {
        currentSearch = this.value.toLowerCase().trim();
        clearBtn.classList.toggle('show', currentSearch.length > 0);
        applyFilters();
    });

    function applyFilters() {
        let visible = 0;

        allRows.forEach(row => {
            const nama = row.getAttribute('data-nama');
            const year = row.getAttribute('data-year');

            const searchMatch = !currentSearch || nama.includes(currentSearch);
            const yearMatch   = currentYear === 'all' || year === currentYear;

            if (searchMatch && yearMatch) {
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

        resultCount.textContent = visible;
        const active = currentSearch || currentYear !== 'all';
        searchStats.classList.toggle('show', active);
        noResults.classList.toggle('show', visible === 0 && active);
    }

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
        currentYear   = 'all';
        clearBtn.classList.remove('show');
        filterBtns.forEach(b => b.classList.toggle('active', b.getAttribute('data-year') === 'all'));
        applyFilters();
    });

    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') clearBtn.click();
    });

    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.style.cssText = `
            position:fixed; top:20px; right:20px;
            padding:13px 20px;
            background:${type === 'success' ? '#16a34a' : '#dc2626'};
            color:white; border-radius:10px; font-weight:600; font-size:13px;
            z-index:9999; box-shadow:0 4px 14px rgba(0,0,0,.15);
            display:flex; align-items:center; gap:10px;
            animation: slideInR .3s ease-out;
        `;
        t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${msg}</span>`;
        document.body.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }, 3000);
    }

    @if(session('success')) showToast('{{ session('success') }}', 'success'); @endif
    @if(session('error'))   showToast('{{ session('error') }}', 'error');   @endif
});
</script>

<style>
@keyframes slideInR {
    from { transform: translateX(80px); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}
</style>
@endpush