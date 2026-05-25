@extends('karyawan.layout.master')

@section('title', 'Riwayat Pengajuan Shift')

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
        background: #ffffff;
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

    .riwayat-content {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 20px;
    }

    .shift-req-item {
        background: white;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 15px;
        border-left: 4px solid #354591;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .shift-req-item:hover {
        box-shadow: 0 4px 12px rgba(53, 69, 145, 0.15);
    }

    .shift-req-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .shift-req-type {
        font-size: 16px;
        font-weight: 700;
        color: #2d3748;
    }

    .shift-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .shift-status.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .shift-status.disetujui {
        background: #d1fae5;
        color: #065f46;
    }

    .shift-status.ditolak {
        background: #fee2e2;
        color: #991b1b;
    }

    .shift-changes {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        font-size: 14px;
        color: #4a5568;
    }

    .shift-old {
        padding: 6px 12px;
        background: #e2e8f0;
        border-radius: 6px;
        font-weight: 600;
    }

    .shift-arrow {
        color: #354591;
        font-weight: 700;
    }

    .shift-new {
        padding: 6px 12px;
        background: #d1fae5;
        border-radius: 6px;
        font-weight: 700;
        color: #065f46;
    }

    .shift-period {
        font-size: 13px;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .shift-period i {
        color: #354591;
    }

    .shift-submitted {
        font-size: 12px;
        color: #9ca3af;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
    }

    .empty-icon {
        font-size: 64px;
        color: #e2e8f0;
        margin-bottom: 15px;
    }

    .empty-text {
        color: #9ca3af;
        font-size: 14px;
        font-weight: 500;
    }

    .pagination-wrapper {
        padding: 20px;
        background: white;
        border-radius: 12px;
        margin-top: 15px;
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
        background: #354591;
        color: white;
    }

    .pagination .active span {
        background: #354591;
        color: white;
    }

    .pagination .disabled span {
        opacity: 0.4;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .fullscreen-wrapper {
            top: 60px;
            bottom: 60px;
        }

        .riwayat-content {
            padding: 15px;
        }
    }
</style>
@endpush

@section('content')
<div class="fullscreen-wrapper">
    <div class="riwayat-content">
        @forelse($pengajuan as $item)
        <div class="shift-req-item">
            <div class="shift-req-header">
                <div class="shift-req-type">
                    <i class="fas fa-sync-alt"></i>
                    Pergantian {{ ucfirst($item->jenis) }}
                </div>
                <div class="shift-status {{ $item->status }}">
                    {{ ucfirst($item->status) }}
                </div>
            </div>

            <div class="shift-changes">
                <span class="shift-old">{{ $item->shiftLama->jenis ?? '-' }}</span>
                <span class="shift-arrow"><i class="fas fa-arrow-right"></i></span>
                <span class="shift-new">{{ $item->shiftBaru->jenis ?? '-' }}</span>
            </div>

            <div class="shift-period">
                <i class="far fa-calendar"></i>
                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                @if($item->tanggal_selesai)
                    - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                @else
                    - Seterusnya
                @endif
            </div>

            <div class="shift-submitted">
                <i class="far fa-clock"></i>
                Diajukan {{ $item->created_at->diffForHumans() }}
            </div>

            @if($item->status === 'ditolak' && $item->catatan_admin)
            <div style="margin-top: 12px; padding: 10px; background: #fee2e2; border-radius: 8px; border-left: 3px solid #ef4444;">
                <div style="font-size: 11px; font-weight: 600; color: #991b1b; margin-bottom: 3px;">ALASAN DITOLAK:</div>
                <div style="font-size: 12px; color: #991b1b;">{{ $item->catatan_admin }}</div>
            </div>
            @endif

            @if($item->alasan)
            <div style="margin-top: 12px; padding: 10px; background: #f7fafc; border-radius: 8px; border-left: 3px solid #354591;">
                <div style="font-size: 11px; font-weight: 600; color: #4a5568; margin-bottom: 3px;">ALASAN:</div>
                <div style="font-size: 12px; color: #2d3748;">{{ $item->alasan }}</div>
            </div>
            @endif
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="far fa-clock"></i>
            </div>
            <div class="empty-text">Belum ada riwayat pengajuan shift</div>
        </div>
        @endforelse

        @if($pengajuan->hasPages())
        <div class="pagination-wrapper">
            {{ $pengajuan->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
