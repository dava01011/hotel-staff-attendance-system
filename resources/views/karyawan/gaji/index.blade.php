@extends('karyawan.layout.master')

@section('title', 'Slip Gaji')

@section('content')
<style>
    * { margin: 0; padding: 0; box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

    body { background: #f5f5f5; overflow: hidden; }

    .page-wrap {
        position: fixed;
        top: 70px; left: 0; right: 0; bottom: 0;
        display: flex;
        flex-direction: column;
        background: #f5f5f5;
        overflow: hidden;
    }

    /* ── Filter ───────────────────────────────────────────── */
    .filter-bar {
        background: white;
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        gap: 10px;
        flex-shrink: 0;
        z-index: 10;
    }

    .filter-select {
        flex: 1;
        padding: 9px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        font-size: 13px;
        color: #374151;
        background: white;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 30px;
        cursor: pointer;
        transition: border-color .2s;
    }

    .filter-select:focus {
        outline: none;
        border-color: #354591;
    }

    /* ── Scrollable list ──────────────────────────────────── */
    .gaji-list {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 12px 16px 90px;
    }

    /* ── Summary banner ───────────────────────────────────── */
    .summary-banner {
        background: linear-gradient(135deg, #354591 0%, #5568d3 100%);
        border-radius: 14px;
        padding: 16px 18px;
        color: white;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .summary-banner .sb-label { font-size: 11px; opacity: .8; margin-bottom: 4px; text-transform: uppercase; font-weight: 600; }
    .summary-banner .sb-amount { font-size: 22px; font-weight: 700; }
    .summary-banner .sb-right  { text-align: right; }
    .summary-banner .sb-days   { font-size: 13px; opacity: .85; }

    /* ── Gaji Card ────────────────────────────────────────── */
    .gaji-card {
        background: white;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        border: 1px solid #f1f1f1;
    }

    .card-top {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }

    .month-badge {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .month-badge .mb-mon { font-size: 12px; font-weight: 700; text-transform: uppercase; line-height: 1; }
    .month-badge .mb-yr  { font-size: 11px; font-weight: 500; opacity: .85; margin-top: 2px; }

    .card-info { flex: 1; }
    .card-info .ci-title  { font-size: 15px; font-weight: 700; color: #111827; }
    .card-info .ci-sub    { font-size: 12px; color: #9ca3af; margin-top: 1px; }

    .card-total { text-align: right; }
    .card-total .ct-label  { font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; margin-bottom: 2px; }
    .card-total .ct-amount { font-size: 17px; font-weight: 700; color: #16a34a; }

    /* ── Detail Row ───────────────────────────────────────── */
    .card-details {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        padding: 12px 0;
        border-top: 1px solid #f3f4f6;
        border-bottom: 1px solid #f3f4f6;
        margin-bottom: 14px;
    }

    .det-item { text-align: center; }
    .det-item .di-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; margin-bottom: 3px; }
    .det-item .di-val   { font-size: 14px; font-weight: 700; color: #1f2937; }

    /* ── Action Buttons ───────────────────────────────────── */
    .card-actions {
        display: flex;
        gap: 8px;
        width: 100%;
    }

    .ca-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all .15s;
        -webkit-tap-highlight-color: transparent;
        user-select: none;
    }

    .ca-btn:active { transform: scale(.97); }

    .ca-btn.detail { background: #eff6ff; color: #1d4ed8; }
    .ca-btn.detail:hover { background: #dbeafe; }

    .ca-btn.pdf { background: #f0fdf4; color: #16a34a; }
    .ca-btn.pdf:hover { background: #dcfce7; }

    /* ── Empty ────────────────────────────────────────────── */
    .empty-state {
        text-align: center;
        padding: 70px 20px;
        color: #9ca3af;
    }

    .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }
    .empty-state h3 { font-size: 16px; font-weight: 600; color: #6b7280; margin-bottom: 6px; }
    .empty-state p  { font-size: 13px; }

    /* ── PDF Modal ────────────────────────────────────────── */
    .pdf-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        flex-direction: column;
        background: #1f2937;
    }

    .pdf-overlay.show { display: flex; }

    .pdf-toolbar {
        background: #111827;
        color: white;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    .pdf-toolbar .pt-title { flex: 1; font-size: 14px; font-weight: 600; }

    .pt-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all .15s;
    }

    .pt-btn.download { background: #16a34a; color: white; }
    .pt-btn.download:hover { background: #15803d; }
    .pt-btn.close-btn { background: #374151; color: white; }
    .pt-btn.close-btn:hover { background: #4b5563; }

    .pdf-frame-wrap { flex: 1; overflow: hidden; }
    .pdf-frame-wrap iframe { width: 100%; height: 100%; border: none; }

    .pdf-loading {
        display: none;
        position: absolute;
        inset: 0;
        background: rgba(17,24,39,.8);
        color: white;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 12px;
        font-size: 14px;
        z-index: 10;
    }

    .pdf-loading.show { display: flex; }
    .pdf-loading i { font-size: 32px; animation: spin 1s linear infinite; }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Responsive ───────────────────────────────────────── */
    @media (max-width: 480px) {
        .gaji-list { padding: 10px 12px 90px; }
        .summary-banner { flex-direction: column; align-items: flex-start; gap: 10px; }
        .summary-banner .sb-right { text-align: left; }
        .card-details { gap: 4px; }
        .det-item .di-label { font-size: 9px; }
        .det-item .di-val { font-size: 13px; }
        
        .card-top { 
            flex-direction: column; 
            align-items: stretch; 
            gap: 12px; 
            position: relative;
        }
        
        .month-badge {
            position: absolute;
            top: 0;
            right: 0;
            width: 44px;
            height: 44px;
        }

        .card-info { margin-right: 50px; }
        
        .card-total { 
            text-align: left; 
            border-top: 1px dashed #f1f1f1; 
            padding-top: 12px; 
            margin-top: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    }
</style>

<div class="page-wrap">

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" id="filterForm" style="display:contents;">
            <select name="tahun" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Tahun</option>
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ ($tahun ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <select name="bulan" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ ($bulan ?? '') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->locale('id')->monthName }}
                    </option>
                @endfor
            </select>
        </form>
    </div>

    <div class="gaji-list">

        {{-- Summary Banner --}}
        @if($gaji->count() > 0)
        @php $totalGaji = $gaji->sum('total_gaji'); $totalHadir = $gaji->sum('total_hadir'); @endphp
        <div class="summary-banner">
            <div>
                <div class="sb-label">Total Gaji</div>
                <div class="sb-amount">Rp {{ number_format($totalGaji, 0, ',', '.') }}</div>
            </div>
            <div class="sb-right">
                <div class="sb-label">Total Hadir</div>
                <div class="sb-days">{{ $totalHadir }} hari</div>
            </div>
        </div>
        @endif

        {{-- List --}}
        @forelse($gaji as $g)
            @php
                $bulanNama  = \Carbon\Carbon::create()->month($g->bulan)->locale('id')->monthName;
                $bulanShort = strtoupper(substr($bulanNama, 0, 3));
            @endphp

            <div class="gaji-card">
                <div class="card-top">
                    <div class="month-badge">
                        <div class="mb-mon">{{ $bulanShort }}</div>
                        <div class="mb-yr">{{ $g->tahun }}</div>
                    </div>
                    <div class="card-info">
                        <div class="ci-title">{{ $bulanNama }} {{ $g->tahun }}</div>
                        <div class="ci-sub">Periode Gaji</div>
                    </div>
                    <div class="card-total">
                        <div class="ct-label">Total</div>
                        <div class="ct-amount">
                            Rp {{ number_format($g->total_gaji, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="card-details">
                    <div class="det-item">
                        <div class="di-label">Hadir</div>
                        <div class="di-val">{{ $g->total_hadir }} hr</div>
                    </div>
                    <div class="det-item">
                        <div class="di-label">Gaji Pokok</div>
                        <div class="di-val">{{ number_format($g->gaji_pokok / 1000, 0) }}K</div>
                    </div>
                    <div class="det-item">
                        <div class="di-label">Tunjangan</div>
                        <div class="di-val">{{ number_format($g->total_tunjangan / 1000, 0) }}K</div>
                    </div>
                </div>

                <div class="card-actions">
                    <!--<a href="{{ route('karyawan.gaji.slip', $g->id) }}"-->
                    <!--   class="ca-btn detail">-->
                    <!--    <i class="fas fa-file-alt"></i> Detail-->
                    <!--</a>-->
                    <button type="button"
                            class="ca-btn pdf"
                            onclick="openPdf({{ $g->id }}, '{{ $bulanNama }} {{ $g->tahun }}')">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-wallet"></i>
                <h3>Belum ada data</h3>
                <p>Slip gaji belum tersedia untuk periode ini</p>
            </div>
        @endforelse

    </div>
</div>

{{-- PDF Overlay --}}
<div class="pdf-overlay" id="pdfOverlay">
    <div class="pdf-toolbar">
        <span class="pt-title" id="pdfTitle">Slip Gaji</span>
        <button class="pt-btn download" id="pdfDownloadBtn">
            <i class="fas fa-download"></i> Download
        </button>
        <button class="pt-btn close-btn" onclick="closePdf()">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    <div class="pdf-frame-wrap" style="position:relative;">
        <div class="pdf-loading" id="pdfLoading">
            <i class="fas fa-spinner"></i>
            <span>Memuat PDF...</span>
        </div>
        <iframe id="pdfFrame" src="" title="Slip Gaji PDF"></iframe>
    </div>
</div>

<script>
    let currentId = null;

    function openPdf(id, title) {
        currentId = id;
        document.getElementById('pdfTitle').textContent = 'Slip Gaji — ' + title;

        const frame    = document.getElementById('pdfFrame');
        const loading  = document.getElementById('pdfLoading');
        const overlay  = document.getElementById('pdfOverlay');
        const dlBtn    = document.getElementById('pdfDownloadBtn');

        // Show overlay + loading
        overlay.classList.add('show');
        loading.classList.add('show');
        frame.src = '';

        // Set download action
        dlBtn.onclick = function () {
            window.location.href = '/karyawan/gaji/' + id + '/download';
        };

        // Load PDF
        frame.onload = function () {
            loading.classList.remove('show');
        };

        frame.onerror = function () {
            loading.classList.remove('show');
            alert('Gagal memuat PDF. Coba download langsung.');
        };

        // Slight delay so overlay animates first
        setTimeout(() => {
            frame.src = '/karyawan/gaji/' + id + '/preview';
        }, 100);
    }

    function closePdf() {
        const overlay = document.getElementById('pdfOverlay');
        const frame   = document.getElementById('pdfFrame');
        const loading = document.getElementById('pdfLoading');

        overlay.classList.remove('show');
        loading.classList.remove('show');
        frame.src = '';
        currentId = null;
    }

    // Tutup dengan tombol back/escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closePdf();
    });
</script>
@endsection