@extends('admin.layouts.app')

@section('title', 'Data Karyawan')

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
    .filter-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

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

    .filter-btn:hover {
        border-color: #0d6efd;
        background: #f8f9fa;
    }

    .filter-btn.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    .filter-btn .count {
        background: rgba(0, 0, 0, 0.1);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
    }

    .filter-btn.active .count {
        background: rgba(255, 255, 255, 0.2);
    }

    /* ── Table ──────────────────────────────────────────────── */
    .table-compact th {
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 11px 8px;
        color: #495057;
    }

    .table-compact td {
        padding: 10px 8px;
        vertical-align: middle;
        font-size: 13px;
    }

    .table tbody tr {
        transition: background-color 0.15s;
    }

    .table tbody tr.highlight {
        background-color: #fff3cd !important;
        animation: highlight-fade 1.5s ease-out forwards;
    }

    @keyframes highlight-fade {
        from { background-color: #fff3cd; }
        to   { background-color: transparent; }
    }

    /* ── Employee Cell ──────────────────────────────────────── */
    .employee-compact {
        display: flex;
        align-items: center;
        gap: 10px;
    }

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

    .employee-name {
        font-weight: 600;
        color: #2d3748;
        line-height: 1.2;
    }

    .employee-nip {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 1px;
    }

    /* ── Contact Info ───────────────────────────────────────── */
    .contact-info {
        font-size: 12px;
        color: #475569;
        line-height: 1.4;
    }

    .contact-info i {
        width: 14px;
        color: #94a3b8;
        margin-right: 4px;
    }

    .address-text {
        max-width: 180px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 12px;
        color: #475569;
    }

    /* ── Status Badge ───────────────────────────────────────── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 11px;
        border-radius: 14px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-badge i {
        font-size: 9px;
    }

    .status-badge.aktif {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.nonaktif {
        background: #fee2e2;
        color: #991b1b;
    }

    /* ── Face Badge ─────────────────────────────────────────── */
    .face-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }

    .face-badge.registered {
        background: #d1fae5;
        color: #065f46;
    }

    .face-badge.not-registered {
        background: #fee2e2;
        color: #991b1b;
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

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,.12);
    }

    .action-btn.edit {
        background: #fff3cd;
        color: #d97706;
    }

    .action-btn.edit:hover {
        background: #fbbf24;
        color: white;
    }

    .action-btn.delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .action-btn.delete:hover {
        background: #ef4444;
        color: white;
    }

    .action-btn.info {
        background: #e0f2fe;
        color: #0284c7;
    }

    .action-btn.info:hover {
        background: #0ea5e9;
        color: white;
    }

    /* ── No Results / Search Stats ──────────────────────────── */
    .no-results {
        display: none;
        text-align: center;
        padding: 60px 20px;
    }

    .no-results.show { display: block; }

    .no-results-icon {
        font-size: 56px;
        color: #dee2e6;
        margin-bottom: 16px;
    }

    .no-results-text {
        color: #6c757d;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .no-results-subtext {
        color: #adb5bd;
        font-size: 13px;
    }

    .search-stats {
        font-size: 14px;
        color: #6c757d;
        display: none;
        padding: 12px 0;
        border-top: 1px solid #e9ecef;
        margin-top: 14px;
    }

    .search-stats.show {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-stats strong { color: #0d6efd; }

    .reset-filter {
        font-size: 13px;
        color: #dc3545;
        text-decoration: none;
        font-weight: 600;
        display: none;
        cursor: pointer;
    }

    .reset-filter:hover { text-decoration: underline; }
    .reset-filter.show  { display: inline-block; }

    /* ── Restricted Notice ──────────────────────────────────── */
    .restricted-notice {
        display: none;
        background: #cfe2ff;
        border: 1px solid #b6d4fe;
        border-radius: 6px;
        padding: 12px 16px;
        margin-bottom: 15px;
        color: #084298;
        font-size: 13px;
        font-weight: 500;
    }

    .restricted-notice.show { display: block; }
    .restricted-notice i { margin-right: 8px; }

    /* ── No Permission Badge ────────────────────────────────── */
    .no-permission-badge {
        display: inline-block;
        padding: 4px 10px;
        background: #f8d7da;
        color: #721c24;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 992px) {
        .search-container {
            max-width: 100%;
            margin-bottom: 15px;
        }
        .filter-group { margin-top: 15px; }
        .search-stats {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
    }

    @media (max-width: 768px) {
        .employee-compact { gap: 8px; }
        .avatar-small { width: 30px; height: 30px; font-size: 11px; }
        .address-text { max-width: 120px; }
    }
</style>
@endpush

@section('content')

    {{-- Restricted View Notice --}}
    @if(!$canCRUD && in_array(Auth::user()->role, ['admin', 'manager', 'gm']))
        <div class="restricted-notice show">
            <i class="fas fa-info-circle"></i>
            Anda hanya dapat melihat data karyawan dari departemen Anda
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Data Karyawan</h4>
            <small class="text-muted">Manajemen data karyawan</small>
        </div>

        @if($canCRUD)
            <button class="btn btn-primary btn-sm d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#tambahKaryawan">
                <i class="fas fa-plus"></i> Tambah Data
            </button>
        @endif
    </div>

    {{-- Search & Filter Card --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control search-input" id="searchInput"
                           placeholder="Cari NIP, nama, departemen, atau jabatan..." autocomplete="off">
                    <button class="clear-search" id="clearSearch" title="Clear search">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="ms-auto text-muted">
                    <small>Total: <strong id="totalCount">{{ $karyawan->count() }}</strong> karyawan</small>
                </div>
            </div>

            {{-- Filter Buttons --}}
            <div class="filter-group mt-3">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-users"></i> Semua
                    <span class="count">{{ $karyawan->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="aktif">
                    <i class="fas fa-user-check"></i> Aktif
                    <span class="count">{{ $karyawan->where('status', 'aktif')->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="nonaktif">
                    <i class="fas fa-user-times"></i> Non-Aktif
                    <span class="count">{{ $karyawan->where('status', 'nonaktif')->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="wajah-terdaftar">
                    <i class="fas fa-user-shield"></i> Wajah Terdaftar
                    <span class="count">{{ $karyawan->where('wajah_terdaftar', 1)->count() }}</span>
                </button>
                <button class="filter-btn" data-filter="wajah-belum">
                    <i class="fas fa-user-slash"></i> Belum Daftar Wajah
                    <span class="count">{{ $karyawan->where('wajah_terdaftar', 0)->count() }}</span>
                </button>
            </div>

            {{-- Search Stats --}}
            <div class="search-stats" id="searchStats">
                <div>
                    Menampilkan <strong id="resultCount">0</strong> dari
                    <strong>{{ $karyawan->count() }}</strong> karyawan
                </div>
                <a href="#" class="reset-filter" id="resetFilter">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-compact align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:44px; padding-left:20px;">#</th>
                            <th style="min-width:200px;">Karyawan</th>
                            <th style="min-width:90px;">Departemen</th>
                            <th style="min-width:120px;">Jabatan</th>
                            <th style="width:120px;">No. Telepon</th>
                            <th style="min-width:150px;">Alamat</th>
                            <th style="width:100px;">Status</th>
                            <th style="width:120px;">Wajah</th>
                            <th style="width:100px; text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="karyawanTableBody">
                        @php
                            $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4'];
                        @endphp
                        @forelse ($karyawan as $item)
                            @php
                                $nama  = $item->user->nama ?? '';
                                $ci    = ord(strtolower(substr($nama, 0, 1))) % count($colors);
                            @endphp
                            <tr data-nip="{{ strtolower($item->nip) }}"
                                data-nama="{{ strtolower($nama) }}"
                                data-departemen="{{ strtolower($item->departemen->nama ?? '') }}"
                                data-jabatan="{{ strtolower($item->jabatan->nama_jabatan ?? '') }}"
                                data-telepon="{{ strtolower($item->no_telepon ?? '') }}"
                                data-alamat="{{ strtolower($item->alamat ?? '') }}"
                                data-status="{{ strtolower($item->status) }}"
                                data-wajah="{{ $item->wajah_terdaftar ? '1' : '0' }}">

                                <td style="padding-left:20px; color:#94a3b8; font-size:12px;">
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <div class="employee-compact">
                                        <div class="avatar-small" style="background:{{ $colors[$ci] }};">
                                            {{ strtoupper(substr($nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="employee-name">{{ $nama ?: '-' }}</div>
                                            <div class="employee-nip">NIP: {{ $item->nip }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="text-muted">
                                        <i class="fas fa-building me-1"></i>
                                        {{ $item->departemen->nama ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border" style="font-weight:500;">
                                        {{ $item->jabatan->nama_jabatan ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    @if($item->no_telepon)
                                        <span class="contact-info">
                                            <i class="fas fa-phone-alt"></i> {{ $item->no_telepon }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($item->alamat)
                                        <span class="address-text" title="{{ $item->alamat }}">
                                            <i class="fas fa-map-marker-alt me-1" style="color:#94a3b8;"></i>
                                            {{ $item->alamat }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="status-badge {{ $item->status == 'aktif' ? 'aktif' : 'nonaktif' }}">
                                        <i class="fas fa-circle"></i>
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>

                                <td>
                                    @if($item->wajah_terdaftar)
                                        <span class="face-badge registered">
                                            <i class="fas fa-check-circle"></i> Terdaftar
                                        </span>
                                    @else
                                        <span class="face-badge not-registered">
                                            <i class="fas fa-times-circle"></i> Belum
                                        </span>
                                    @endif
                                </td>

                                @if($canCRUD)
                                    <td style="text-align:center;">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('admin.karyawan.show', $item->id) }}" class="action-btn info" title="Detail Info">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            <button class="action-btn edit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#ubahKaryawan{{ $item->id }}"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#hapusKaryawan{{ $item->id }}"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                @else
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center align-items-center">
                                            <a href="{{ route('admin.karyawan.show', $item->id) }}" class="action-btn info" title="Detail Info">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            <span class="no-permission-badge m-0">
                                                <i class="fas fa-lock"></i> No Access
                                            </span>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr id="emptyState">
                                <td @if($canCRUD) colspan="9" @else colspan="8" @endif class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3" style="opacity:.2;"></i>
                                        <p class="mb-0 fw-medium">Belum ada data karyawan</p>
                                        <small>Klik tombol "Tambah Data" untuk menambahkan karyawan</small>
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
                <div class="no-results-text">Tidak ada karyawan ditemukan</div>
                <div class="no-results-subtext">Coba ubah kata kunci pencarian atau filter</div>
            </div>
        </div>

        {{-- Modals hanya tampil jika canCRUD --}}
        @if($canCRUD)
            @foreach ($karyawan as $item)
                @include('admin.karyawan.edit')
                @include('admin.karyawan.delete')
            @endforeach
        @endif
    </div>

    {{-- Create modal --}}
    @if($canCRUD)
        @include('admin.karyawan.create')
    @endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const tableBody = document.getElementById('karyawanTableBody');
    const noResults = document.getElementById('noResults');
    const searchStats = document.getElementById('searchStats');
    const resultCount = document.getElementById('resultCount');
    const resetFilter = document.getElementById('resetFilter');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const allRows = tableBody.querySelectorAll('tr:not(#emptyState)');
    const canCRUD = {{ $canCRUD ? 'true' : 'false' }};

    let currentFilter = 'all';
    let currentSearch = '';

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currentFilter = this.getAttribute('data-filter');
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            applyFilters();
        });
    });

    searchInput.addEventListener('input', function() {
        currentSearch = this.value.toLowerCase().trim();
        clearSearchBtn.classList.toggle('show', currentSearch.length > 0);
        applyFilters();
    });

    function applyFilters() {
        let visibleCount = 0;

        allRows.forEach(row => {
            const nip = row.getAttribute('data-nip');
            const nama = row.getAttribute('data-nama');
            const departemen = row.getAttribute('data-departemen');
            const jabatan = row.getAttribute('data-jabatan');
            const telepon = row.getAttribute('data-telepon');
            const alamat = row.getAttribute('data-alamat');
            const status = row.getAttribute('data-status');
            const wajah = row.getAttribute('data-wajah');

            const searchMatch = !currentSearch ||
                nip.includes(currentSearch) ||
                nama.includes(currentSearch) ||
                departemen.includes(currentSearch) ||
                jabatan.includes(currentSearch) ||
                telepon.includes(currentSearch) ||
                alamat.includes(currentSearch);

            let filterMatch = true;
            if (currentFilter === 'aktif') filterMatch = status === 'aktif';
            else if (currentFilter === 'nonaktif') filterMatch = status === 'nonaktif';
            else if (currentFilter === 'wajah-terdaftar') filterMatch = wajah === '1';
            else if (currentFilter === 'wajah-belum') filterMatch = wajah === '0';

            if (searchMatch && filterMatch) {
                row.style.display = '';
                visibleCount++;
                if (currentSearch) {
                    row.classList.add('highlight');
                    setTimeout(() => row.classList.remove('highlight'), 1500);
                }
            } else {
                row.style.display = 'none';
            }
        });

        updateUI(visibleCount);
    }

    function updateUI(visibleCount) {
        resultCount.textContent = visibleCount;
        const active = currentSearch || currentFilter !== 'all';
        searchStats.classList.toggle('show', active);
        resetFilter.classList.toggle('show', active);
        noResults.classList.toggle('show', visibleCount === 0 && active);
    }

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        currentSearch = '';
        clearSearchBtn.classList.remove('show');
        applyFilters();
        searchInput.focus();
    });

    resetFilter.addEventListener('click', function(e) {
        e.preventDefault();
        searchInput.value = '';
        currentSearch = '';
        clearSearchBtn.classList.remove('show');
        currentFilter = 'all';
        filterBtns.forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-filter') === 'all') btn.classList.add('active');
        });
        applyFilters();
        searchInput.focus();
    });

    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') clearSearchBtn.click();
    });

    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
    });

    if (!canCRUD) {
        document.addEventListener('click', function(e) {
            if (e.target.closest('[data-bs-toggle="modal"]')) {
                e.preventDefault();
                showToast('Anda tidak memiliki izin untuk melakukan aksi ini', 'error');
            }
        });
    }

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
    to   { transform: translateX(0); opacity: 1; }
}
</style>
@endpush