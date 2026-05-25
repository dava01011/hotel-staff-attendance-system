@extends('karyawan.layout.fullscreen')

@section('title', 'Detail Libur Pengganti')

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

    .detail-wrapper {
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
    .detail-header {
        background: white;
        padding: 16px 20px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
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

    .detail-header h1 {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    /* Content */
    .detail-content {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        padding-bottom: 100px;
    }

    /* Card */
    .detail-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        border-bottom: 1px solid #f0f0f0;
        background: #fafafa;
    }

    .card-header i {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: white;
        flex-shrink: 0;
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .card-header span {
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-body {
        padding: 4px 0;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 11px 18px;
        border-bottom: 1px solid #f8f9fa;
        gap: 12px;
    }

    .detail-row:last-child { border-bottom: none; }

    .detail-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        flex-shrink: 0;
    }

    .detail-value {
        font-size: 14px;
        color: #212529;
        font-weight: 600;
        text-align: right;
    }

    .badge-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-status.pending { background: #fef3c7; color: #92400e; }
    .badge-status.disetujui { background: #d1fae5; color: #065f46; }
    .badge-status.ditolak { background: #fee2e2; color: #991b1b; }

    .reason-text {
        padding: 14px 18px;
        font-size: 14px;
        color: #495057;
        line-height: 1.7;
        word-break: break-word;
    }

    /* Timeline Sederhana */
    .timeline {
        padding: 14px 18px;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .timeline-item {
        display: flex;
        gap: 14px;
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-item:last-child { padding-bottom: 0; }

    .timeline-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 15px;
        top: 32px;
        width: 2px;
        height: calc(100% - 12px);
        background: #e9ecef;
    }

    .timeline-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 13px;
        color: white;
        position: relative;
        z-index: 1;
    }

    .timeline-dot.pending { background: #fff3cd; color: #856404; border: 2px solid #ffc107; }
    .timeline-dot.disetujui { background: #11998e; }
    .timeline-dot.ditolak { background: #ef4444; }

    .timeline-info { padding-top: 4px; flex: 1; }
    .timeline-step { font-size: 13px; font-weight: 700; color: #212529; margin-bottom: 2px; }
    .timeline-time { font-size: 12px; color: #6c757d; }
    .timeline-note { font-size: 12px; color: #ef4444; margin-top: 3px; font-style: italic; }

    /* Tombol Batal */
    .btn-danger {
        display: block;
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="detail-wrapper">
    <div class="detail-header">
        <a href="{{ route('karyawan.pengajuan.index') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Detail Libur Pengganti</h1>
    </div>

    <div class="detail-content">
        {{-- Informasi --}}
        <div class="detail-card">
            <div class="card-header">
                <i class="fas fa-umbrella-beach"></i>
                <span>Informasi Pengajuan</span>
            </div>
            <div class="card-body">
                <div class="detail-row">
                    <span class="detail-label">Tanggal</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($pengajuan->tanggal)->format('d F Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="badge-status {{ $pengajuan->status }}">{{ ucfirst($pengajuan->status) }}</span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Diajukan</span>
                    <span class="detail-value">{{ $pengajuan->created_at->format('d F Y, H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Alasan --}}
        <div class="detail-card">
            <div class="card-header">
                <i class="fas fa-comment-alt"></i>
                <span>Alasan</span>
            </div>
            <p class="reason-text">{{ $pengajuan->alasan }}</p>
        </div>

        {{-- Progress --}}
        <div class="detail-card">
            <div class="card-header">
                <i class="fas fa-tasks"></i>
                <span>Progress Persetujuan</span>
            </div>
            <div class="timeline">
                @forelse($pengajuan->approvals as $approval)
                    <div class="timeline-item">
                        <div class="timeline-dot {{ $approval->status }}">
                            @if($approval->status === 'disetujui')
                                <i class="fas fa-check"></i>
                            @elseif($approval->status === 'ditolak')
                                <i class="fas fa-times"></i>
                            @else
                                <i class="fas fa-hourglass-half"></i>
                            @endif
                        </div>
                        <div class="timeline-info">
                            <div class="timeline-step">{{ strtoupper($approval->step) }}</div>
                            @if($approval->approved_at)
                                <div class="timeline-time">{{ $approval->approved_at->format('d F Y, H:i') }}</div>
                            @else
                                <div class="timeline-time">Menunggu...</div>
                            @endif
                            @if($approval->catatan)
                                <div class="timeline-note">"{{ $approval->catatan }}"</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="reason-text" style="text-align: center;">Tidak ada data persetujuan.</p>
                @endforelse
            </div>
        </div>

        {{-- Catatan Penolakan --}}
        @if($pengajuan->status === 'ditolak' && $pengajuan->catatan_admin)
        <div class="detail-card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i>
                <span>Catatan Penolakan</span>
            </div>
            <p class="reason-text">{{ $pengajuan->catatan_admin }}</p>
        </div>
        @endif

        {{-- Tombol Batal --}}
        @if($pengajuan->status === 'pending')
        <form action="{{ route('karyawan.libur-pengganti.cancel', $pengajuan->id) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan libur ini?')">
            @csrf
            <button type="submit" class="btn-danger">
                <i class="fas fa-times"></i> Batalkan Pengajuan
            </button>
        </form>
        @endif
    </div>
</div>
@endsection