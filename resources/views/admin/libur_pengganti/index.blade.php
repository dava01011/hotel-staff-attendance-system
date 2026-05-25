@extends('admin.layouts.app')

@section('title', 'Saldo Libur Pengganti')

@push('styles')
<style>
    /* ── Search Box ─────────────────────────────────────────── */
    .search-container {
        position: relative;
        flex: 1;
        max-width: 400px;
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

    /* ── Table ──────────────────────────────────────────────── */
    .table-compact th {
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 11px 14px;
        color: #495057;
    }

    .table-compact td {
        padding: 10px 14px;
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

    /* ── Status Badge (Saldo) ───────────────────────────────── */
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

    .status-badge.banyak  { background: #d1fae5; color: #065f46; }
    .status-badge.sedikit { background: #fef3c7; color: #92400e; }
    .status-badge.kosong  { background: #fee2e2; color: #991b1b; }

    /* ── Action Buttons ─────────────────────────────────────── */
    .action-btn {
        width: auto;
        padding: 0 14px;
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
        background: #dbeafe;
        color: #1d4ed8;
        font-weight: 600;
        gap: 5px;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,.12);
        background: #bfdbfe;
        color: #1e40af;
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
    }

    .reset-filter:hover { text-decoration: underline; }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 992px) {
        .search-container { max-width: 100%; margin-bottom: 12px; }
    }

    @media (max-width: 768px) {
        .employee-compact { gap: 8px; }
        .avatar-small { width: 30px; height: 30px; font-size: 11px; }
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Saldo Libur Pengganti</h4>
        <small class="text-muted">Kelola saldo hari libur pengganti karyawan</small>
    </div>
    <button class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#resetAllModal">
        <i class="fas fa-redo"></i> Reset Semua Saldo
    </button>
</div>

{{-- Search Card --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text"
                       class="form-control search-input"
                       id="searchInput"
                       placeholder="Cari nama atau NIP karyawan..."
                       autocomplete="off">
                <button class="clear-search" id="clearSearch" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="ms-auto text-muted">
                <small>Total: <strong>{{ $liburPengganti->total() }}</strong> karyawan</small>
            </div>
        </div>

        {{-- Search Stats --}}
        <div class="search-stats" id="searchStats">
            <div>
                Menampilkan <strong id="resultCount">0</strong>
                dari <strong>{{ $liburPengganti->total() }}</strong> karyawan
            </div>
            <a href="#" class="reset-filter" id="resetFilter">
                <i class="fas fa-redo"></i> Reset
            </a>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-compact align-middle mb-0" id="saldoTable">
                <thead class="table-light">
                    <tr>
                        <th style="width:44px; padding-left:20px;">#</th>
                        <th style="min-width:200px;">Karyawan</th>
                        <th style="width:150px;">Saldo</th>
                        <th style="min-width:160px;">Terakhir Diupdate</th>
                        <th style="width:120px; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="saldoTableBody">
                    @php
                        $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#06b6d4'];
                    @endphp
                    @forelse($liburPengganti as $item)
                        @php
                            $nama = $item->karyawan->user->nama ?? '';
                            $ci   = ord(strtolower(substr($nama, 0, 1))) % count($colors);
                        @endphp
                        <tr data-nama="{{ strtolower($nama) }}"
                            data-nip="{{ strtolower($item->karyawan->nip ?? '') }}">

                            <td style="padding-left:20px; color:#94a3b8; font-size:12px;">
                                {{ $loop->iteration + ($liburPengganti->currentPage() - 1) * $liburPengganti->perPage() }}
                            </td>

                            <td>
                                <div class="employee-compact">
                                    <div class="avatar-small" style="background:{{ $colors[$ci] }};">
                                        {{ strtoupper(substr($nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="employee-name">{{ $nama ?: '-' }}</div>
                                        <div class="employee-nip">NIP: {{ $item->karyawan->nip ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @php
                                    $saldo = $item->saldo;
                                    $cls   = $saldo >= 3 ? 'banyak' : ($saldo > 0 ? 'sedikit' : 'kosong');
                                @endphp
                                <span class="status-badge {{ $cls }}">
                                    <i class="fas fa-umbrella-beach"></i>
                                    {{ $saldo }} hari
                                </span>
                            </td>

                            <td>
                                <span class="text-muted" style="font-size:13px;">
                                    @if($item->terakhir_diupdate)
                                        <i class="fas fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->terakhir_diupdate)->diffForHumans() }}
                                        <br>
                                        <small style="font-size:11px;">
                                            {{ \Carbon\Carbon::parse($item->terakhir_diupdate)->format('d M Y H:i') }}
                                        </small>
                                    @else
                                        —
                                    @endif
                                </span>
                            </td>

                            <td style="text-align:center;">
                                <button class="action-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#adjustModal{{ $item->karyawan_id }}"
                                        title="Adjust Saldo">
                                    <i class="fas fa-edit"></i> Adjust
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyState">
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-umbrella-beach fa-3x mb-3" style="opacity:.2;"></i>
                                    <p class="mb-0 fw-medium">Belum ada data libur pengganti</p>
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
            <div class="no-results-text">Karyawan tidak ditemukan</div>
            <div class="no-results-subtext">Coba ubah kata kunci pencarian</div>
        </div>

        @if($liburPengganti->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $liburPengganti->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Adjust Modals --}}
@foreach($liburPengganti as $item)
<div class="modal fade" id="adjustModal{{ $item->karyawan_id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0" style="background:#eff6ff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-edit" style="color:#1d4ed8;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Adjust Saldo</h6>
                        <small class="text-muted" style="font-size:11px;">
                            {{ $item->karyawan->user->nama ?? '-' }}
                        </small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.libur-pengganti.adjust', $item->karyawan_id) }}" method="POST">
                @csrf
                <div class="modal-body pt-3">
                    <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded-3"
                         style="background:#f8fafc;">
                        <span style="font-size:13px;color:#64748b;">Saldo sekarang</span>
                        <span class="fw-bold" style="font-size:18px;color:#1d4ed8;">
                            {{ $item->saldo }} hari
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Saldo Baru <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="saldo_baru"
                               class="form-control" style="font-size:13px;"
                               min="0" value="{{ $item->saldo }}" required>
                        <div class="form-text" style="font-size:11px;">
                            Masukkan angka 0 atau lebih.
                        </div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="font-size:13px;">
                            Keterangan (opsional)
                        </label>
                        <input type="text" name="keterangan"
                               class="form-control" style="font-size:13px;"
                               placeholder="contoh: Bonus libur Q1">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Reset All Modal --}}
<div class="modal fade" id="resetAllModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header border-0 pb-0" style="background:#fef2f2;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fee2e2;
                                display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-redo" style="color:#dc2626;font-size:14px;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Reset Semua Saldo</h6>
                        <small class="text-muted" style="font-size:11px;">Tindakan tidak bisa dibatalkan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.libur-pengganti.reset-all') }}" method="POST">
                @csrf
                <div class="modal-body text-center py-4">
                    <p style="font-size:13px;" class="mb-2">
                        Semua saldo libur pengganti akan di-reset ke <strong>0</strong>.
                    </p>
                    <p class="text-muted" style="font-size:12px;">
                        Biasanya dilakukan di awal tahun baru.
                    </p>
                    <div class="form-check d-flex align-items-center justify-content-center gap-2 mt-3">
                        <input class="form-check-input" type="checkbox" name="confirm"
                               value="1" id="confirmReset" required>
                        <label class="form-check-label fw-semibold" for="confirmReset"
                               style="font-size:13px;">
                            Ya, saya yakin reset semua saldo
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-semibold">
                        <i class="fas fa-redo me-1"></i> Reset Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const resetFilter = document.getElementById('resetFilter');
    const tableBody   = document.getElementById('saldoTableBody');
    const noResults   = document.getElementById('noResults');
    const searchStats = document.getElementById('searchStats');
    const resultCount = document.getElementById('resultCount');
    const allRows     = tableBody.querySelectorAll('tr:not(#emptyState)');

    let currentSearch = '';

    function applySearch() {
        const q = currentSearch;
        let visible = 0;

        allRows.forEach(row => {
            const nama = row.dataset.nama || '';
            const nip  = row.dataset.nip  || '';
            const match = !q || nama.includes(q) || nip.includes(q);

            if (match) {
                row.style.display = '';
                visible++;
                if (q) {
                    row.classList.add('highlight');
                    setTimeout(() => row.classList.remove('highlight'), 1500);
                }
            } else {
                row.style.display = 'none';
            }
        });

        resultCount.textContent = visible;
        const active = q.length > 0;
        searchStats.classList.toggle('show', active);
        clearBtn.classList.toggle('show', active);
        noResults.classList.toggle('show', visible === 0 && active);
    }

    searchInput.addEventListener('input', function () {
        currentSearch = this.value.toLowerCase().trim();
        applySearch();
    });

    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        currentSearch = '';
        applySearch();
        searchInput.focus();
    });

    resetFilter.addEventListener('click', function (e) {
        e.preventDefault();
        clearBtn.click();
    });

    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') clearBtn.click();
    });

    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.style.cssText = `
            position:fixed; top:20px; right:20px;
            padding:14px 22px;
            background:${type === 'success' ? '#16a34a' : '#dc2626'};
            color:white; border-radius:8px; font-weight:600; font-size:13px;
            z-index:9999; box-shadow:0 4px 14px rgba(0,0,0,.15);
            animation:slideInR .3s ease-out;
            display:flex; align-items:center; gap:10px;
        `;
        t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${msg}</span>`;
        document.body.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }, 3000);
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