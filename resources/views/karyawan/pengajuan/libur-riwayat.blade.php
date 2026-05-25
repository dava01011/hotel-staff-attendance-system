@extends('karyawan.layout.fullscreen')

@section('title', 'Riwayat Libur Pengganti')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
    }

    body {
        background: #f8f9fa;
        overflow: hidden;
    }

    .fullscreen-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
    }

    /* Header */
    .header-bar {
        background: white;
        padding: 16px 20px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
    }

    .header-bar h1 {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    .back-btn {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #f0f0f0;
        color: #212529;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        text-decoration: none;
        transition: background 0.2s;
        flex-shrink: 0;
    }

    .back-btn:hover { background: #e0e0e0; color: #212529; }

    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 0;
        background: white;
        padding: 0 16px;
        border-bottom: 1px solid #e9ecef;
        flex-shrink: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .filter-tabs::-webkit-scrollbar { display: none; }

    .tab-btn {
        flex: 0 0 auto;
        min-width: max-content;
        padding: 14px 16px;
        background: transparent;
        border: none;
        color: #6c757d;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        white-space: nowrap;
        text-decoration: none;
    }

    .tab-btn.active {
        color: #11998e;
    }

    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: #11998e;
    }

    /* List */
    .riwayat-list {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 20px;
        padding-bottom: 30px;
    }

    /* Item Card */
    .libur-item {
        background: white;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #11998e;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        transition: all 0.3s;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .libur-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .item-type {
        font-size: 16px;
        font-weight: 700;
        color: #2d3748;
    }

    .item-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .item-status.pending { background: #fef3c7; color: #92400e; }
    .item-status.disetujui { background: #d1fae5; color: #065f46; }
    .item-status.ditolak { background: #fee2e2; color: #991b1b; }

    .item-date {
        font-size: 13px;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }

    .item-date i { color: #11998e; }

    /* Simple Progress */
    .simple-progress {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: space-around;
        background: #f1f5f9;
        border-radius: 12px;
        padding: 12px 8px;
    }

    .simple-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        flex: 1;
    }

    .simple-badge {
        font-size: 12px;
        font-weight: 700;
        padding: 6px 0;
        width: 100%;
        text-align: center;
        border-radius: 20px;
        background: white;
        color: #64748b;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .simple-badge.completed { background: #10b981; color: white; }
    .simple-badge.current { background: #f59e0b; color: white; }
    .simple-badge.pending { background: #e2e8f0; color: #64748b; }
    .simple-badge.rejected { background: #ef4444; color: white; }

    .simple-label {
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
    }

    .empty-icon {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 15px;
    }

    .empty-text {
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
    }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination a,
    .pagination span {
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border-radius: 8px;
        font-size: 13px;
        color: #495057;
        text-decoration: none;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }

    .pagination a:hover { background: #11998e; color: white; }
    .pagination .active span { background: #11998e; color: white; }
</style>
@endpush

@section('content')
<div class="fullscreen-wrapper">
    <div class="header-bar">
        <a href="{{ route('karyawan.pengajuan.index') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Riwayat Libur Pengganti</h1>
    </div>

    <div class="filter-tabs">
        <a href="{{ route('karyawan.libur-pengganti.riwayat') }}" class="tab-btn {{ $currentStatus == 'all' ? 'active' : '' }}">Semua</a>
        <a href="{{ route('karyawan.libur-pengganti.riwayat', ['status' => 'pending']) }}" class="tab-btn {{ $currentStatus == 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('karyawan.libur-pengganti.riwayat', ['status' => 'disetujui']) }}" class="tab-btn {{ $currentStatus == 'disetujui' ? 'active' : '' }}">Disetujui</a>
        <a href="{{ route('karyawan.libur-pengganti.riwayat', ['status' => 'ditolak']) }}" class="tab-btn {{ $currentStatus == 'ditolak' ? 'active' : '' }}">Ditolak</a>
    </div>

    <div class="riwayat-list">
        @forelse($liburPengganti as $item)
            <a href="{{ route('karyawan.libur-pengganti.show', $item->id) }}" class="libur-item">
                <div class="item-header">
                    <div class="item-type">Libur Pengganti</div>
                    <div class="item-status {{ $item->status }}">
                        {{ ucfirst($item->status) }}
                    </div>
                </div>
                <div class="item-date">
                    <i class="far fa-calendar"></i>
                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                </div>

                @if($item->approvals && count($item->approvals) > 0)
                <div class="simple-progress">
                    @foreach($item->approvals as $approval)
                        @php
                            $badgeClass = 'pending';
                            if ($approval->status === 'disetujui') $badgeClass = 'completed';
                            elseif ($approval->status === 'ditolak') $badgeClass = 'rejected';
                            elseif ($approval->step === $item->current_step) $badgeClass = 'current';
                        @endphp
                        <div class="simple-step">
                            <div class="simple-badge {{ $badgeClass }}">
                                @if($approval->status === 'disetujui')
                                    <i class="fas fa-check"></i>
                                @elseif($approval->status === 'ditolak')
                                    <i class="fas fa-times"></i>
                                @elseif($approval->step === $item->current_step)
                                    <i class="fas fa-hourglass-half"></i>
                                @else
                                    <i class="fas fa-minus"></i>
                                @endif
                            </div>
                            <div class="simple-label">{{ $approval->role_label }}</div>
                        </div>
                    @endforeach
                </div>
                @endif
            </a>
        @empty
            <div class="empty-state">
                <div class="empty-icon"><i class="far fa-calendar-times"></i></div>
                <div class="empty-text">Tidak ada pengajuan libur pengganti</div>
            </div>
        @endforelse

        @if($liburPengganti->hasPages())
            <div class="pagination-wrapper">
                {{ $liburPengganti->links() }}
            </div>
        @endif
    </div>
</div>
@endsection