@extends('admin.layouts.app')

@section('title', 'Data Jabatan')

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

    /* ── Filter Pills ───────────────────────────────────────── */
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

    /* ── Jabatan Cell ───────────────────────────────────────── */
    .jabatan-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .jabatan-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f0fdf4;
        color: #16a34a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        border: 1px solid #bbf7d0;
    }

    .jabatan-name { font-weight: 600; color: #2d3748; font-size: 13px; }

    /* ── Tipe Badge ─────────────────────────────────────────── */
    .tipe-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 11px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .tipe-badge.bulanan  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .tipe-badge.harian   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .tipe-badge.default  { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

    /* ── Salary ─────────────────────────────────────────────── */
    .salary-val {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
    }

    .salary-empty {
        font-size: 12px;
        color: #cbd5e1;
        font-style: italic;
    }

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

    .action-btn.edit         { background: #fff3cd; color: #d97706; }
    .action-btn.edit:hover   { background: #fbbf24; color: white; }

    .action-btn.delete         { background: #fee2e2; color: #dc2626; }
    .action-btn.delete:hover   { background: #ef4444; color: white; }

    /* ── No Results ─────────────────────────────────────────── */
    .no-results      { display: none; text-align: center; padding: 60px 20px; }
    .no-results.show { display: block; }
    .no-results-icon    { font-size: 52px; color: #dee2e6; margin-bottom: 14px; }
    .no-results-text    { color: #6c757d; font-size: 15px; font-weight: 600; margin-bottom: 6px; }
    .no-results-subtext { color: #adb5bd; font-size: 13px; }

    @media (max-width: 992px) {
        .search-container { max-width: 100%; }
    }
</style>
@endpush

@section('content')

    {{-- ── Page Header ──────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">Data Jabatan</h4>
            <small class="text-muted">Manajemen jabatan & struktur gaji</small>
        </div>

        <button class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#tambahJabatan">
            <i class="fas fa-plus"></i> Tambah Data
        </button>
    </div>

    {{-- ── Filter Card ──────────────────────────────────────── --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text"
                           class="form-control search-input"
                           id="searchInput"
                           placeholder="Cari nama jabatan..."
                           autocomplete="off">
                    <button class="clear-search" id="clearSearch">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="ms-auto text-muted">
                    <small>Total: <strong>{{ $jabatan->count() }}</strong> jabatan</small>
                </div>
            </div>

            {{-- Tipe Gaji Filter --}}
            <div class="filter-group mt-3">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-layer-group"></i> Semua
                    <span class="count">{{ $jabatan->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="bulanan">
                    <i class="fas fa-calendar-alt"></i> Bulanan
                    <span class="count">{{ $jabatan->where('tipe_gaji','bulanan')->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="harian">
                    <i class="fas fa-calendar-day"></i> Harian
                    <span class="count">{{ $jabatan->where('tipe_gaji','harian')->count() }}</span>
                </button>
            </div>

            <div class="search-stats" id="searchStats">
                <div>
                    Menampilkan <strong id="resultCount">0</strong>
                    dari <strong>{{ $jabatan->count() }}</strong> jabatan
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
                            <th style="min-width:180px;">Nama Jabatan</th>
                            <th style="width:120px;">Jatah Cuti (Bulanan)</th>
                            <th style="width:120px;">Tipe Gaji</th>
                            <th style="min-width:140px;">Gaji Pokok</th>
                            <th style="min-width:140px;">Gaji Harian</th>
                            <th style="width:90px; text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse ($jabatan as $item)
                            @php
                                $tipe = strtolower($item->tipe_gaji ?? '');
                                $tipeClass = in_array($tipe, ['bulanan','harian']) ? $tipe : 'default';
                            @endphp
                            <tr data-nama="{{ strtolower($item->nama_jabatan) }}"
                                data-tipe="{{ $tipe }}">

                                <td style="padding-left:20px; color:#94a3b8; font-size:12px;">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- Jabatan --}}
                                <td>
                                    <div class="jabatan-cell">
                                        <div class="jabatan-icon">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <span class="jabatan-name">{{ $item->nama_jabatan }}</span>
                                    </div>
                                </td>

                                {{-- Jatah Cuti Bulanan --}}
                                <td>
                                    <span class="badge bg-soft-info text-info fw-bold">
                                        +{{ $item->jatah_cuti_bulanan }} hari / bulan
                                    </span>
                                </td>

                                {{-- Tipe Gaji --}}
                                <td>
                                    <span class="tipe-badge {{ $tipeClass }}">
                                        {{ ucfirst($item->tipe_gaji ?? '-') }}
                                    </span>
                                </td>

                                {{-- Gaji Pokok --}}
                                <td>
                                    @if($item->gaji_pokok)
                                        <span class="salary-val">
                                            Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="salary-empty">—</span>
                                    @endif
                                </td>

                                {{-- Gaji Harian --}}
                                <td>
                                    @if($item->gaji_harian)
                                        <span class="salary-val">
                                            Rp {{ number_format($item->gaji_harian, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="salary-empty">—</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td style="text-align:center;">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button class="action-btn edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#ubahJabatan{{ $item->id }}"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#hapusJabatan{{ $item->id }}"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr id="emptyState">
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-user-tie fa-3x mb-3" style="opacity:.2;"></i>
                                        <p class="mb-0 fw-medium">Belum ada data jabatan</p>
                                        <small>Klik tombol "Tambah Data" untuk menambahkan jabatan</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="no-results" id="noResults">
                <div class="no-results-icon"><i class="fas fa-search"></i></div>
                <div class="no-results-text">Tidak ada jabatan ditemukan</div>
                <div class="no-results-subtext">Coba ubah kata kunci atau filter tipe gaji</div>
            </div>
        </div>

        @foreach ($jabatan as $item)
            @include('admin.jabatan.edit')
            @include('admin.jabatan.delete')
        @endforeach
    </div>

    @include('admin.jabatan.create')

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
    let currentFilter = 'all';

    /* ── Filter pills ───────────────────────────────────────── */
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            currentFilter = this.getAttribute('data-filter');
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            applyFilters();
        });
    });

    /* ── Search ─────────────────────────────────────────────── */
    searchInput.addEventListener('input', function () {
        currentSearch = this.value.toLowerCase().trim();
        clearBtn.classList.toggle('show', currentSearch.length > 0);
        applyFilters();
    });

    /* ── Core filter ────────────────────────────────────────── */
    function applyFilters() {
        let visible = 0;

        allRows.forEach(row => {
            const nama = row.getAttribute('data-nama');
            const tipe = row.getAttribute('data-tipe');

            const searchMatch = !currentSearch || nama.includes(currentSearch);
            const filterMatch = currentFilter === 'all' || tipe === currentFilter;

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

        resultCount.textContent = visible;
        const active = currentSearch || currentFilter !== 'all';
        searchStats.classList.toggle('show', active);
        noResults.classList.toggle('show', visible === 0 && active);
    }

    /* ── Clear & Reset ──────────────────────────────────────── */
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
        currentFilter = 'all';
        clearBtn.classList.remove('show');
        filterBtns.forEach(b => b.classList.toggle('active', b.getAttribute('data-filter') === 'all'));
        applyFilters();
    });

    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') clearBtn.click();
    });

    /* ── Toast ──────────────────────────────────────────────── */
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
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transition = 'opacity .3s';
            setTimeout(() => t.remove(), 300);
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
@keyframes slideInR {
    from { transform: translateX(80px); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}
</style>
@endpush
