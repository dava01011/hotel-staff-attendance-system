@extends('admin.layouts.app')

@section('title', 'Riwayat Gaji')

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

    /* ── Stats Cards ────────────────────────────────────────── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        border: 1px solid #eaecf0;
        border-radius: 12px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: box-shadow 0.2s;
    }

    .stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); }

    .stat-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .stat-icon-wrap.blue   { background: #eff6ff; color: #3b82f6; }
    .stat-icon-wrap.green  { background: #f0fdf4; color: #16a34a; }
    .stat-icon-wrap.violet { background: #f5f3ff; color: #7c3aed; }
    .stat-icon-wrap.amber  { background: #fffbeb; color: #d97706; }

    .stat-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; margin-bottom: 2px; }
    .stat-value { font-size: 20px; font-weight: 800; color: #1e293b; line-height: 1.1; }
    .stat-value.small { font-size: 15px; }

    /* ── Table ──────────────────────────────────────────────── */
    .table-compact th {
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 11px 12px;
        color: #495057;
    }

    .table-compact td { padding: 10px 12px; vertical-align: middle; font-size: 13px; }

    .table tbody tr { transition: background-color 0.15s; }
    .table tbody tr.highlight {
        background-color: #fff3cd !important;
        animation: highlight-fade 1.5s ease-out;
    }

    @keyframes highlight-fade {
        from { background-color: #fff3cd; }
        to   { background-color: transparent; }
    }

    /* ── Employee Cell ──────────────────────────────────────── */
    .employee-info { display: flex; align-items: center; gap: 11px; }

    .avatar-initial {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        color: white;
        flex-shrink: 0;
    }

    .employee-name { font-weight: 600; color: #2d3748; line-height: 1.2; }
    .employee-sub  { font-size: 11px; color: #94a3b8; margin-top: 1px; }

    /* ── Period Cell ────────────────────────────────────────── */
    .period-cell {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 11px;
        background: #f1f5f9;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }

    /* ── Hadir Badge ────────────────────────────────────────── */
    .hadir-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: #d1fae5;
        color: #065f46;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    /* ── Money ──────────────────────────────────────────────── */
    .money-harian { font-size: 13px; color: #64748b; font-weight: 500; }
    .money-total  { font-size: 14px; color: #0d6efd; font-weight: 700; }

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

    .reset-filter       { font-size: 13px; color: #dc3545; text-decoration: none; font-weight: 600; }
    .reset-filter:hover { text-decoration: underline; }

    /* ── Modal PDF Preview ──────────────────────────────────── */
    .modal-pdf .modal-dialog {
        max-width: 95vw;
        height: 95vh;
        margin: 1.5rem auto;
    }

    .modal-pdf .modal-content {
        height: 100%;
        border-radius: 16px;
        overflow: hidden;
    }

    .modal-pdf .modal-body {
        padding: 0;
        height: calc(100% - 120px);
    }

    .modal-pdf .pdf-frame {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* ── Responsive ─────────────────────────────────────────── */
    @media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 992px)  { .search-container { max-width: 100%; margin-bottom: 12px; } }
    @media (max-width: 576px)  { .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; } }
</style>
@endpush

@section('content')

    {{-- ── Page Header ──────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Riwayat Gaji</h4>
            <small class="text-muted">Rekap dan perhitungan gaji karyawan</small>
        </div>
        <a href="{{ route('admin.gaji.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
            <i class="fas fa-calculator"></i> Hitung Gaji
        </a>
    </div>

    {{-- ── Stats Cards ──────────────────────────────────────── --}}
    @php
        $totalGaji    = $gaji->sum('total_gaji');
        $totalKaryawan = $gaji->pluck('karyawan_id')->unique()->count();
        $rataHadir    = $gaji->avg('total_hadir') ?? 0;
        $bulanIni     = $gaji->where('bulan', now()->month)->where('tahun', now()->year)->count();
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap blue"><i class="fas fa-file-invoice-dollar"></i></div>
            <div>
                <div class="stat-label">Total Record</div>
                <div class="stat-value">{{ $gaji->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap green"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <div class="stat-label">Total Gaji Tersalurkan</div>
                <div class="stat-value small">Rp {{ number_format($totalGaji, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap violet"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-label">Karyawan Digaji</div>
                <div class="stat-value">{{ $totalKaryawan }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrap amber"><i class="fas fa-calendar-check"></i></div>
            <div>
                <div class="stat-label">Rata-rata Hadir</div>
                <div class="stat-value">{{ number_format($rataHadir, 1) }} <span style="font-size:13px; color:#94a3b8;">hari</span></div>
            </div>
        </div>
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
                           placeholder="Cari nama karyawan..."
                           autocomplete="off">
                    <button class="clear-search" id="clearSearch" title="Hapus">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="ms-auto text-muted">
                    <small>Total: <strong>{{ $gaji->count() }}</strong> record</small>
                </div>
            </div>

            {{-- Filter by tahun --}}
            @php
                $tahunList = $gaji->pluck('tahun')->unique()->sortDesc()->values();
            @endphp

            <div class="filter-group mt-3">
                <button class="filter-btn active" data-filter="all">
                    <i class="fas fa-layer-group"></i> Semua Tahun
                    <span class="count">{{ $gaji->count() }}</span>
                </button>
                @foreach($tahunList as $thn)
                    <button class="filter-btn" data-filter="{{ $thn }}">
                        <i class="fas fa-calendar"></i> {{ $thn }}
                        <span class="count">{{ $gaji->where('tahun', $thn)->count() }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Search Stats --}}
            <div class="search-stats" id="searchStats">
                <div>
                    Menampilkan <strong id="resultCount">0</strong>
                    dari <strong>{{ $gaji->count() }}</strong> record
                </div>
                <a href="#" class="reset-filter" id="resetFilter">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            </div>

        </div>
    </div>

    {{-- ── Table ────────────────────────────────────────────── --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-compact align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:44px; padding-left:16px;">#</th>
                            <th style="min-width:190px;">Karyawan</th>
                            <th style="width:160px;">Periode</th>
                            <th style="width:110px; text-align:center;">Hadir</th>
                            <th style="width:150px;">Gaji Harian</th>
                            <th style="width:160px;">Total Gaji</th>
                            <th style="width:80px; text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="gajiTableBody">
                        @forelse($gaji as $g)
                            @php
                                $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899'];
                                $ci     = ord(strtolower(substr($g->karyawan->user->nama ?? 'k', 0, 1))) % count($colors);
                                $bulanNama = \Carbon\Carbon::create()->month($g->bulan)->locale('id')->monthName;
                            @endphp
                            <tr data-nama="{{ strtolower($g->karyawan->user->nama ?? '') }}"
                                data-tahun="{{ $g->tahun }}">

                                <td style="padding-left:16px; color:#94a3b8; font-size:12px;">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- Karyawan --}}
                                <td>
                                    <div class="employee-info">
                                        <div class="avatar-initial" style="background:{{ $colors[$ci] }};">
                                            {{ strtoupper(substr($g->karyawan->user->nama ?? 'K', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="employee-name">{{ $g->karyawan->user->nama ?? '-' }}</div>
                                            <div class="employee-sub">{{ $g->karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Periode --}}
                                <td>
                                    <span class="period-cell">
                                        <i class="fas fa-calendar-alt text-muted"></i>
                                        {{ ucfirst($bulanNama) }} {{ $g->tahun }}
                                    </span>
                                </td>

                                {{-- Hadir --}}
                                <td style="text-align:center;">
                                    <span class="hadir-badge">
                                        <i class="fas fa-check-circle"></i>
                                        {{ $g->total_hadir }} hari
                                    </span>
                                </td>

                                {{-- Gaji Harian --}}
                                <td>
                                    <span class="money-harian">
                                        Rp {{ number_format($g->gaji_harian, 0, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Total Gaji --}}
                                <td>
                                    <span class="money-total">
                                        Rp {{ number_format($g->total_gaji, 0, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td style="text-align:center;">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" 
                                           class="btn btn-sm btn-outline-info"
                                           onclick="previewSlip('{{ route('admin.gaji.slip', $g) }}', '{{ route('admin.gaji.slip.pdf', $g) }}', '{{ $g->karyawan->user->nama }}')"
                                           title="Preview Slip">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.gaji.slip.pdf', $g) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form action="{{ route('admin.gaji.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Hapus data gaji ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Gaji">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr id="emptyState">
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-file-invoice-dollar fa-3x mb-3" style="opacity:.25;"></i>
                                        <p class="mb-0 fw-medium">Belum ada data gaji</p>
                                        <small>Klik "Hitung Gaji" untuk memulai</small>
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

    </div>

    {{-- ── Modal Preview PDF ─────────────────────────────────── --}}
    <div class="modal fade modal-pdf" id="previewPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-file-pdf text-danger me-2"></i>Preview Slip Gaji
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfPreviewFrame" class="pdf-frame" src=""></iframe>
                </div>
                <div class="modal-footer bg-light">
                    <div class="d-flex gap-2 w-100 justify-content-between align-items-center">
                        <small class="text-muted" id="pdfFileName"></small>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-success" id="btnDownloadPdf" target="_blank">
                                <i class="fas fa-download me-1"></i>Download PDF
                            </a>
                            <button type="button" class="btn btn-primary" id="btnPrintPdf">
                                <i class="fas fa-print me-1"></i>Cetak
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const tableBody   = document.getElementById('gajiTableBody');
    const noResults   = document.getElementById('noResults');
    const searchStats = document.getElementById('searchStats');
    const resultCount = document.getElementById('resultCount');
    const resetFilter = document.getElementById('resetFilter');
    const filterBtns  = document.querySelectorAll('.filter-btn');
    const allRows     = tableBody.querySelectorAll('tr:not(#emptyState)');

    let currentSearch = '';
    let currentFilter = 'all';

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            currentFilter = this.getAttribute('data-filter');
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
            const nama  = row.getAttribute('data-nama');
            const tahun = row.getAttribute('data-tahun');

            const searchMatch = !currentSearch || nama.includes(currentSearch);
            const filterMatch = currentFilter === 'all' || tahun === currentFilter;

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

    clearBtn.addEventListener('click', function () {
        searchInput.value = ''; currentSearch = '';
        clearBtn.classList.remove('show');
        applyFilters(); searchInput.focus();
    });

    resetFilter.addEventListener('click', function (e) {
        e.preventDefault();
        searchInput.value = ''; currentSearch = '';
        clearBtn.classList.remove('show');
        currentFilter = 'all';
        filterBtns.forEach(b => b.classList.toggle('active', b.getAttribute('data-filter') === 'all'));
        applyFilters();
    });

    searchInput.addEventListener('keydown', e => { if (e.key === 'Escape') clearBtn.click(); });

    // ── PDF Preview Modal Logic ──────────────────────────────
    const previewPdfModal = new bootstrap.Modal(document.getElementById('previewPdfModal'));
    const pdfFrame = document.getElementById('pdfPreviewFrame');
    const btnDownloadPdf = document.getElementById('btnDownloadPdf');
    const btnPrintPdf = document.getElementById('btnPrintPdf');
    const pdfFileName = document.getElementById('pdfFileName');

    window.previewSlip = function(previewUrl, downloadUrl, nama) {
        pdfFrame.src = previewUrl;
        btnDownloadPdf.href = downloadUrl;
        pdfFileName.textContent = 'Slip Gaji: ' + nama;
        previewPdfModal.show();
    }

    btnPrintPdf.addEventListener('click', function() {
        if (pdfFrame.contentWindow) {
            pdfFrame.contentWindow.focus();
            pdfFrame.contentWindow.print();
        } else {
            window.open(pdfFrame.src, '_blank')?.focus();
        }
    });

    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); searchInput.focus(); }
    });

    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.style.cssText = `
            position:fixed; top:20px; right:20px;
            padding:14px 22px;
            background:${type === 'success' ? '#16a34a' : '#dc2626'};
            color:white; border-radius:8px; font-weight:600; font-size:13px;
            z-index:9999; box-shadow:0 4px 14px rgba(0,0,0,.15);
            animation:slideInRight .3s ease-out;
            display:flex; align-items:center; gap:10px;
        `;
        t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${msg}</span>`;
        document.body.appendChild(t);
        setTimeout(() => {
            t.style.animation = 'slideInRight .3s ease-out reverse';
            setTimeout(() => t.remove(), 300);
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