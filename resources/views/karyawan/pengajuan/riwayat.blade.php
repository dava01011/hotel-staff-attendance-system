@extends('karyawan.layout.master')

@section('title', 'Riwayat Cuti')

@push('styles')
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
        background: #f8f9fa;
        overflow: hidden;
    }

    .fullscreen-wrapper {
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        bottom: 70px;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
    }

    /* ── Filter bar ───────────────────────────── */
    .filter-bar {
        background: white;
        padding: 12px 16px;
        display: flex;
        gap: 8px;
        overflow-x: auto;
        flex-shrink: 0;
        border-bottom: 1px solid #f0f0f0;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .filter-bar::-webkit-scrollbar { display: none; }

    .filter-pill {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
        background: white;
        color: #718096;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
    }

    .filter-pill.active,
    .filter-pill:hover { border-color: #667eea; color: #667eea; background: #f0f0ff; }

    .filter-pill.pending.active   { border-color: #f59e0b; color: #92400e; background: #fef3c7; }
    .filter-pill.disetujui.active { border-color: #10b981; color: #065f46; background: #d1fae5; }
    .filter-pill.ditolak.active   { border-color: #ef4444; color: #991b1b; background: #fee2e2; }

    /* ── Scrollable list ──────────────────────── */
    .riwayat-content {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 16px;
    }

    /* ── Cuti card ────────────────────────────── */
    .cuti-card {
        background: white;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 12px;
        border-left: 4px solid #667eea;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
        transition: box-shadow .25s;
    }

    .cuti-card:hover { box-shadow: 0 4px 14px rgba(102,126,234,.15); }

    .cuti-card.pending   { border-left-color: #f59e0b; }
    .cuti-card.disetujui { border-left-color: #10b981; }
    .cuti-card.ditolak   { border-left-color: #ef4444; }

    /* Card header */
    .card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .jenis-label {
        font-size: 15px;
        font-weight: 700;
        color: #2d3748;
    }

    .jenis-sub {
        font-size: 11px;
        color: #a0aec0;
        margin-top: 2px;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        flex-shrink: 0;
    }

    .status-badge.pending   { background: #fef3c7; color: #92400e; }
    .status-badge.disetujui { background: #d1fae5; color: #065f46; }
    .status-badge.ditolak   { background: #fee2e2; color: #991b1b; }

    /* Info row */
    .info-row {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #718096;
        margin-bottom: 6px;
    }

    .info-row i { width: 14px; text-align: center; }

    .durasi-badge {
        display: inline-block;
        padding: 2px 8px;
        background: #f0f0ff;
        color: #667eea;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        margin-left: 4px;
    }

    /* Rejection note */
    .rejection-note {
        margin-top: 10px;
        padding: 10px 12px;
        background: #fff5f5;
        border-radius: 8px;
        border-left: 3px solid #ef4444;
    }

    .rejection-title {
        font-size: 10px;
        font-weight: 700;
        color: #991b1b;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 3px;
    }

    .rejection-text {
        font-size: 12px;
        color: #991b1b;
    }

    /* ── Empty state ──────────────────────────── */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 80px 20px;
        text-align: center;
    }

    .empty-icon {
        font-size: 56px;
        color: #e2e8f0;
        margin-bottom: 14px;
    }

    .empty-text {
        font-size: 14px;
        font-weight: 600;
        color: #9ca3af;
        margin-bottom: 4px;
    }

    .empty-sub {
        font-size: 12px;
        color: #cbd5e0;
    }

    /* ── Pagination ───────────────────────────── */
    .pagination-wrapper {
        padding: 4px 0 16px;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        gap: 6px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .pagination a,
    .pagination span {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        font-size: 13px;
        color: #495057;
        text-decoration: none;
        background: white;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        transition: all .2s;
    }

    .pagination a:hover          { background: #667eea; color: white; border-color: #667eea; }
    .pagination .active span     { background: #667eea; color: white; border-color: #667eea; }
    .pagination .disabled span   { opacity: .4; cursor: not-allowed; }

    @media (max-width: 768px) {
        .fullscreen-wrapper { top: 60px; bottom: 60px; }
        .riwayat-content    { padding: 12px; }
    }
</style>
@endpush

@section('content')
<div class="fullscreen-wrapper">

    {{-- ── Filter pills ── --}}
    <div class="filter-bar">
        <button class="filter-pill active" data-status="semua">Semua</button>
        <button class="filter-pill pending"   data-status="pending">Menunggu</button>
        <button class="filter-pill disetujui" data-status="disetujui">Disetujui</button>
        <button class="filter-pill ditolak"   data-status="ditolak">Ditolak</button>
    </div>

    {{-- ── Card list ── --}}
    <div class="riwayat-content">

        @forelse($cuti as $item)
        <div class="cuti-card {{ $item->status }}" data-status="{{ $item->status }}">

            {{-- Top row: jenis + badge --}}
            <div class="card-top">
                <div>
                    <div class="jenis-label">{{ $item->jenisCuti->nama ?? 'Cuti' }}</div>
                    <div class="jenis-sub">Diajukan {{ $item->created_at->diffForHumans() }}</div>
                </div>
                <div class="status-badge {{ $item->status }}">
                    @if($item->status === 'pending') Menunggu
                    @elseif($item->status === 'disetujui') Disetujui
                    @else Ditolak
                    @endif
                </div>
            </div>

            {{-- Tanggal & durasi --}}
            <div class="info-row">
                <i class="far fa-calendar-alt"></i>
                <span>
                    {{ $item->tanggal_mulai->format('d M Y') }}
                    –
                    {{ $item->tanggal_selesai->format('d M Y') }}
                </span>
                <span class="durasi-badge">{{ $item->jumlah_hari }} hari</span>
            </div>

            {{-- Alasan pengajuan --}}
            @if($item->alasan)
            <div class="info-row">
                <i class="fas fa-comment-alt"></i>
                <span>{{ Str::limit($item->alasan, 60) }}</span>
            </div>
            @endif

            {{-- Alasan penolakan --}}
            @if($item->status === 'ditolak' && $item->catatan_admin)
            <div class="rejection-note">
                <div class="rejection-title"><i class="fas fa-times-circle me-1"></i> Alasan Ditolak</div>
                <div class="rejection-text">{{ $item->catatan_admin }}</div>
            </div>
            @endif

        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon"><i class="far fa-calendar-times"></i></div>
            <div class="empty-text">Belum ada riwayat cuti</div>
            <div class="empty-sub">Pengajuan cuti kamu akan muncul di sini</div>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($cuti->hasPages())
        <div class="pagination-wrapper">
            {{ $cuti->links() }}
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function () {
        // Update active pill
        document.querySelectorAll('.filter-pill').forEach(p => {
            p.classList.remove('active');
        });
        this.classList.add('active');

        const status = this.dataset.status;
        const cards  = document.querySelectorAll('.cuti-card');

        cards.forEach(card => {
            if (status === 'semua' || card.dataset.status === status) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // Show empty state if nothing visible
        const visible = [...cards].filter(c => c.style.display !== 'none');
        let emptyEl   = document.querySelector('.empty-filter');

        if (visible.length === 0 && status !== 'semua') {
            if (!emptyEl) {
                emptyEl = document.createElement('div');
                emptyEl.className = 'empty-state empty-filter';
                emptyEl.innerHTML = `
                    <div class="empty-icon"><i class="far fa-folder-open"></i></div>
                    <div class="empty-text">Tidak ada data untuk filter ini</div>
                `;
                document.querySelector('.riwayat-content').appendChild(emptyEl);
            }
            emptyEl.style.display = 'flex';
        } else if (emptyEl) {
            emptyEl.style.display = 'none';
        }
    });
});
</script>
@endpush
