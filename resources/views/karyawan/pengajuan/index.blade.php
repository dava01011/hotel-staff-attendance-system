@extends('karyawan.layout.master')

@section('title', 'Pengajuan')

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
        overflow-y: auto;
    }

    /* Ruang untuk header */
    .fullscreen-wrapper {
        position: relative;
        min-height: 100vh;
        width: 100%;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
        margin: 0;
        padding-top: 56px; /* Sesuaikan dengan tinggi header */
    }

    /* Tab Navigation */
    .tab-navigation {
        background: white;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        flex-shrink: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: sticky;
        top: 56px;
        z-index: 10;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .tab-nav-item {
        flex: 1 0 auto;
        min-width: 100px;
        padding: 16px 12px;
        text-align: center;
        cursor: pointer;
        border: none;
        background: none;
        color: #6c757d;
        font-weight: 600;
        font-size: 14px;
        position: relative;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .tab-nav-item.active {
        color: #354591;
        font-weight: 700;
    }

    .tab-nav-item.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 3px;
        background: #354591;
    }

    .tab-nav-item i {
        margin-right: 6px;
        font-size: 14px;
    }

    /* Konten scrollable + ruang bottom nav */
    .pengajuan-content {
        flex: 1;
        width: 100%;
        padding: 12px 0 70px 0; /* Atas sedikit ruang, bawah untuk bottom nav */
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* ===== SALDO CARDS ===== */
    .saldo-cards-container {
        display: flex;
        gap: 12px;
        margin: 0 12px 16px 12px;
        padding: 0;
    }

    .saldo-card {
        flex: 1;
        background: white;
        padding: 16px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        border: 1px solid #f0f0f0;
    }

    .saldo-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
        flex-shrink: 0;
    }

    .saldo-card-cuti .saldo-card-icon {
        background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    }

    .saldo-card-libur .saldo-card-icon {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .saldo-card-content {
        flex: 1;
    }

    .saldo-card-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 4px;
    }

    .saldo-card-value {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }

    .saldo-card-value span {
        font-size: 13px;
        font-weight: 500;
        color: #94a3b8;
        margin-left: 4px;
    }

    /* ===== SECTION CARD ===== */
    .section-card {
        background: white;
        margin: 0 12px 16px 12px;
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        border: 1px solid #f0f0f0;
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title i {
        width: 24px;
        text-align: center;
    }

    .section-title.cuti i { color: #FF6B35; }
    .section-title.shift i { color: #354591; }
    .section-title.libur i { color: #11998e; }

    .view-all {
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
        color: #64748b;
        padding: 6px 8px;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .view-all.cuti:hover { background: #fff5f0; }
    .view-all.shift:hover { background: #f0f4ff; }
    .view-all.libur:hover { background: #e6f7f5; }

    /* ===== ITEM CARDS ===== */
    .request-item {
        background: #fafbfc;
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 12px;
        border-left: 4px solid;
        transition: all 0.2s;
        overflow: visible;
    }

    .request-item:last-child {
        margin-bottom: 0;
    }

    .request-item.cuti { border-left-color: #FF6B35; }
    .request-item.libur { border-left-color: #11998e; }
    .request-item.shift { border-left-color: #354591; }

    .request-item:hover {
        background: #f1f5f9;
    }

    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 8px;
    }

    .item-title {
        flex: 1;
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }

    .item-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .item-status.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .item-status.disetujui {
        background: #d1fae5;
        color: #065f46;
    }

    .item-status.ditolak {
        background: #fee2e2;
        color: #991b1b;
    }

    .item-meta {
        font-size: 13px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }

    /* ===== PROGRESS APPROVAL ===== */
    .approval-section {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px dashed #cbd5e1;
    }

    .approval-title {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .approval-steps {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 4px;
        overflow-x: auto;
        padding-bottom: 8px; /* space for scrollbar */
        scrollbar-width: thin;
    }

    .approval-step {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        position: relative;
    }

    .step-indicator {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        background: white;
        border: 2px solid #cbd5e1;
        color: #64748b;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .step-indicator.pending {
        background: white;
        border-color: #cbd5e1;
        color: #64748b;
    }

    .step-indicator.current {
        background: #f59e0b;
        border-color: #f59e0b;
        color: white;
        animation: pulse-current 2s infinite;
    }

    .step-indicator.completed {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }

    .step-indicator.rejected {
        background: #ef4444;
        border-color: #ef4444;
        color: white;
    }

    @keyframes pulse-current {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .step-label {
        font-size: 10px;
        font-weight: 600;
        text-align: center;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .step-connector {
        position: absolute;
        top: 18px;
        left: 50%;
        right: -50%;
        height: 2px;
        background: #cbd5e1;
        z-index: 0;
    }

    .step-connector.completed {
        background: #10b981;
    }

    .approval-step:last-child .step-connector {
        display: none;
    }

    /* ===== ACTION BUTTONS ===== */
    .item-actions {
        display: flex;
        gap: 12px;
        margin-top: 16px;
    }

    .btn-action {
        flex: 1;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
    }

    .btn-view {
        background: #354591;
        color: white;
    }

    .btn-view:hover {
        background: #2a3774;
    }

    .btn-cancel {
        background: #f1f5f9;
        color: #dc3545;
    }

    .btn-cancel:hover {
        background: #fee2e2;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }

    .empty-icon {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 12px;
    }

    .empty-text {
        color: #94a3b8;
        font-size: 14px;
        font-weight: 500;
    }

    /* ===== FAB ===== */
    .fab-container {
        position: fixed;
        bottom: 80px;
        right: 16px;
        z-index: 999;
    }

    .fab-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 998;
    }

    .fab-overlay.show {
        display: block;
    }

    .fab-menu {
        position: absolute;
        bottom: 70px;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        padding: 8px;
        min-width: 200px;
        opacity: 0;
        transform: translateY(10px) scale(0.8);
        transform-origin: bottom right;
        transition: all 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: none;
    }

    .fab-menu.show {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: all;
    }

    .fab-menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-size: 14px;
    }

    .fab-menu-item:hover {
        background: #f1f5f9;
    }

    .fab-menu-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
        flex-shrink: 0;
    }

    .fab-menu-icon.cuti { background: #FF6B35; }
    .fab-menu-icon.libur { background: #11998e; }
    .fab-menu-icon.shift { background: #354591; }

    .fab-menu-content {
        flex: 1;
    }

    .fab-menu-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .fab-menu-desc {
        font-size: 12px;
        color: #64748b;
    }

    .fab-button {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #354591;
        color: white;
        border: none;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fab-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .fab-button.active {
        transform: rotate(45deg);
        background: #2a3774;
    }

    /* Responsive */
    @media (max-width: 480px) {
        .saldo-cards-container {
            margin: 0 8px 12px 8px;
            gap: 8px;
            flex-direction: column;
        }

        .saldo-card {
            padding: 12px 10px;
        }

        .saldo-card-icon {
            width: 44px;
            height: 44px;
            font-size: 20px;
        }

        .saldo-card-value {
            font-size: 22px;
        }

        .section-card {
            margin: 0 8px 12px 8px;
            padding: 14px;
        }

        .tab-nav-item {
            min-width: 90px;
            font-size: 13px;
            padding: 14px 8px;
        }

        .step-indicator {
            width: 32px;
            height: 32px;
            font-size: 13px;
        }

        .step-label {
            font-size: 9px;
            white-space: nowrap; /* Prevent wrapping on tiny screens */
        }
        
        .approval-step {
            min-width: 65px; /* Give steps some breathing room */
        }
    }
</style>
@endpush

@section('content')
<div class="fullscreen-wrapper">
    {{-- Tab Navigation --}}
    <div class="tab-navigation">
        <button class="tab-nav-item active" data-tab="cuti">
            <i class="fas fa-calendar-check"></i> Cuti
        </button>
        <button class="tab-nav-item" data-tab="libur">
            <i class="fas fa-umbrella-beach"></i> Libur
        </button>
        @if(in_array(auth()->user()->role, ['admin', 'manager', 'gm']))
        <button class="tab-nav-item" data-tab="shift">
            <i class="fas fa-sync"></i> Shift
        </button>
        @endif
    </div>

    {{-- Scrollable Content --}}
    <div class="pengajuan-content">
        {{-- Saldo Cards --}}
        <div class="saldo-cards-container">
            <div class="saldo-card saldo-card-cuti">
                <div class="saldo-card-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="saldo-card-content">
                    <div class="saldo-card-label">Jatah Cuti</div>
                    <div class="saldo-card-value">{{ $jatahCuti->jatah ?? 0 }} <span>hari</span></div>
                </div>
            </div>
            <div class="saldo-card saldo-card-libur">
                <div class="saldo-card-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="saldo-card-content">
                    <div class="saldo-card-label">Libur Pengganti</div>
                    <div class="saldo-card-value">{{ $saldoLibur->saldo ?? 0 }} <span>hari</span></div>
                </div>
            </div>
        </div>

        {{-- TAB: Pengajuan Cuti --}}
        <div class="tab-content active" id="tab-cuti">
            {{-- Cuti Aktif --}}
            @if($cutiAktif && count($cutiAktif) > 0)
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title cuti">
                        <i class="fas fa-hourglass-start"></i> Pengajuan Aktif
                    </div>
                </div>

                @foreach($cutiAktif as $item)
                <div class="request-item cuti">
                    <div class="item-header">
                        <div class="item-title">{{ $item->jenisCuti->nama ?? 'Cuti' }}</div>
                        <div class="item-status {{ $item->status }}">{{ ucfirst($item->status) }}</div>
                    </div>

                    <div class="item-meta">
                        <i class="far fa-calendar"></i>
                        <span>{{ $item->tanggal_mulai->format('d M Y') }} - {{ $item->tanggal_selesai->format('d M Y') }}</span>
                        <span style="font-weight: 700;">{{ $item->jumlah_hari }} hari</span>
                    </div>

                    {{-- Progress Approval --}}
                    @if($item->approvals && count($item->approvals) > 0)
                    <div class="approval-section">
                        <div class="approval-title">
                            <i class="fas fa-tasks"></i> Persetujuan
                        </div>
                        <div class="approval-steps">
                            @foreach($item->approvals as $index => $approval)
                                <div class="approval-step">
                                    @if($index < count($item->approvals) - 1)
                                        <div class="step-connector {{ $approval->status === 'disetujui' ? 'completed' : '' }}"></div>
                                    @endif
                                    <div class="step-indicator {{ $approval->status === 'disetujui' ? 'completed' : ($approval->step === $item->current_step ? 'current' : ($approval->status === 'ditolak' ? 'rejected' : 'pending')) }}">
                                        @if($approval->status === 'disetujui')
                                            <i class="fas fa-check"></i>
                                        @elseif($approval->status === 'ditolak')
                                            <i class="fas fa-times"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <div class="step-label">{{ $approval->role_label }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="item-actions">
                        <form action="{{ route('karyawan.pengajuan.cuti.cancel', $item->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            <button type="submit" class="btn-action btn-cancel" onclick="return confirm('Batalkan pengajuan?')">
                                <i class="fas fa-trash-alt"></i> Batalkan
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Cuti Selesai --}}
            @if($cutiSelesai && count($cutiSelesai) > 0)
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title cuti">
                        <i class="fas fa-history"></i> Riwayat
                    </div>
                    <a href="{{ route('karyawan.pengajuan.riwayat') }}" class="view-all cuti">
                        Lihat Semua <i class="fas fa-chevron-right"></i>
                    </a>
                </div>

                @foreach($cutiSelesai->take(3) as $item)
                <div class="request-item cuti">
                    <div class="item-header">
                        <div class="item-title">{{ $item->jenisCuti->nama ?? 'Cuti' }}</div>
                        <div class="item-status {{ $item->status }}">{{ ucfirst($item->status) }}</div>
                    </div>
                    <div class="item-meta">
                        <i class="far fa-calendar"></i>
                        <span>{{ $item->tanggal_mulai->format('d M Y') }} - {{ $item->tanggal_selesai->format('d M Y') }}</span>
                        <span style="font-weight: 700;">{{ $item->jumlah_hari }} hari</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Empty State --}}
            @if((!$cutiAktif || count($cutiAktif) === 0) && (!$cutiSelesai || count($cutiSelesai) === 0))
            <div class="section-card">
                <div class="empty-state">
                    <div class="empty-icon"><i class="far fa-calendar-times"></i></div>
                    <div class="empty-text">Belum ada pengajuan cuti</div>
                </div>
            </div>
            @endif
        </div>

        {{-- TAB: Libur Pengganti --}}
        <div class="tab-content" id="tab-libur">
            {{-- Libur Aktif --}}
            @if($liburAktif && $liburAktif->count() > 0)
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title libur">
                        <i class="fas fa-hourglass-start"></i> Pengajuan Aktif
                    </div>
                </div>

                @foreach($liburAktif as $item)
                <div class="request-item libur">
                    <div class="item-header">
                        <div class="item-title">Libur Pengganti #{{ $item->id }}</div>
                        <div class="item-status {{ $item->status }}">{{ ucfirst($item->status) }}</div>
                    </div>

                    <div class="item-meta">
                        <i class="far fa-calendar"></i>
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }} • 1 hari
                    </div>

                    {{-- Progress Approval --}}
                    @if($item->approvals && $item->approvals->count() > 0)
                    <div class="approval-section">
                        <div class="approval-title">
                            <i class="fas fa-tasks"></i> Persetujuan
                        </div>
                        <div class="approval-steps">
                            @foreach($item->approvals as $index => $approval)
                                @php
                                    $statusClass = 'pending';
                                    if ($approval->status === 'disetujui') $statusClass = 'completed';
                                    elseif ($approval->status === 'ditolak') $statusClass = 'rejected';
                                    elseif ($approval->step === $item->current_step) $statusClass = 'current';
                                @endphp
                                <div class="approval-step">
                                    @if($index < $item->approvals->count() - 1)
                                        <div class="step-connector {{ $approval->status === 'disetujui' ? 'completed' : '' }}"></div>
                                    @endif
                                    <div class="step-indicator {{ $statusClass }}">
                                        @if($approval->status === 'disetujui')
                                            <i class="fas fa-check"></i>
                                        @elseif($approval->status === 'ditolak')
                                            <i class="fas fa-times"></i>
                                        @elseif($approval->step === $item->current_step)
                                            <i class="fas fa-hourglass-half"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <div class="step-label">{{ $approval->role_label }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="approval-section">
                        <div class="approval-title">
                            <i class="fas fa-exclamation-triangle"></i> Data approval tidak lengkap
                        </div>
                    </div>
                    @endif

                    <div class="item-actions">
                        <a href="{{ route('karyawan.libur-pengganti.show', $item->id) }}" class="btn-action btn-view">
                            <i class="fas fa-eye"></i> Detail
                        </a>
                        <form action="{{ route('karyawan.libur-pengganti.cancel', $item->id) }}" method="POST" style="flex: 1; display: inline-block;">
                            @csrf
                            <button type="submit" class="btn-action btn-cancel" onclick="return confirm('Batalkan pengajuan?')">
                                <i class="fas fa-trash"></i> Batalkan
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Libur Selesai --}}
            @if($liburSelesai && $liburSelesai->count() > 0)
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title libur">
                        <i class="fas fa-history"></i> Riwayat
                    </div>
                    <a href="{{ route('karyawan.libur-pengganti.riwayat') }}" class="view-all libur">
                        Lihat Semua <i class="fas fa-chevron-right"></i>
                    </a>
                </div>

                @foreach($liburSelesai->take(3) as $item)
                <div class="request-item libur">
                    <div class="item-header">
                        <div class="item-title">Libur Pengganti #{{ $item->id }}</div>
                        <div class="item-status {{ $item->status }}">{{ ucfirst($item->status) }}</div>
                    </div>
                    <div class="item-meta">
                        <i class="far fa-calendar"></i>
                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Empty State --}}
            @if((!$liburAktif || $liburAktif->count() === 0) && (!$liburSelesai || $liburSelesai->count() === 0))
            <div class="section-card">
                <div class="empty-state">
                    <div class="empty-icon"><i class="far fa-calendar-times"></i></div>
                    <div class="empty-text">Belum ada pengajuan libur pengganti</div>
                </div>
            </div>
            @endif
        </div>

{{-- TAB: Pengajuan Shift --}}
@if(in_array(auth()->user()->role, ['admin', 'manager', 'gm']))
<div class="tab-content" id="tab-shift">
    {{-- Shift Aktif --}}
    @if(isset($shiftAktif) && $shiftAktif->count() > 0)
    <div class="section-card">
        <div class="section-header">
            <div class="section-title shift">
                <i class="fas fa-hourglass-start"></i> Pengajuan Aktif
            </div>
        </div>

        @foreach($shiftAktif as $item)
        <div class="request-item shift">
            <div class="item-header">
                <div class="item-title">Ganti Shift {{ ucfirst($item->kode) }}</div>
                <div class="item-status {{ $item->status }}">{{ ucfirst($item->status) }}</div>
            </div>

            <div class="item-meta">
                <i class="far fa-calendar"></i>
                <span>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</span>
                @if($item->tanggal_selesai)
                    <span>s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</span>
                @else
                    <span>- Seterusnya</span>
                @endif
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                <span style="padding: 4px 10px; background: #f0f0f0; border-radius: 6px; font-weight: 600; font-size: 12px;">
                    {{ $item->shiftLama->kode ?? '-' }}
                </span>
                <span style="color: #354591; font-weight: 700; font-size: 12px;"><i class="fas fa-arrow-right"></i></span>
                <span style="padding: 4px 10px; background: #d1fae5; border-radius: 6px; font-weight: 700; color: #065f46; font-size: 12px;">
                    {{ $item->shiftBaru->kode ?? '-' }}
                </span>
            </div>

            {{-- Progress Approval (sama seperti cuti & libur) --}}
            @if($item->approvals && $item->approvals->count() > 0)
            <div class="approval-section">
                <div class="approval-title">
                    <i class="fas fa-tasks"></i> Persetujuan
                </div>
                <div class="approval-steps">
                    @foreach($item->approvals as $index => $approval)
                        @php
                            $statusClass = 'pending';
                            if ($approval->status === 'disetujui') $statusClass = 'completed';
                            elseif ($approval->status === 'ditolak') $statusClass = 'rejected';
                            elseif ($approval->step === $item->current_step) $statusClass = 'current';
                        @endphp
                        <div class="approval-step">
                            @if($index < $item->approvals->count() - 1)
                                <div class="step-connector {{ $approval->status === 'disetujui' ? 'completed' : '' }}"></div>
                            @endif
                            <div class="step-indicator {{ $statusClass }}">
                                @if($approval->status === 'disetujui')
                                    <i class="fas fa-check"></i>
                                @elseif($approval->status === 'ditolak')
                                    <i class="fas fa-times"></i>
                                @elseif($approval->step === $item->current_step)
                                    <i class="fas fa-hourglass-half"></i>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            <div class="step-label">{{ $approval->role_label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="approval-section">
                <div class="approval-title">
                    <i class="fas fa-exclamation-triangle"></i> Data approval tidak lengkap
                </div>
            </div>
            @endif

            <div class="item-actions">
                <a href="{{ route('karyawan.ajukan-shift.show', $item->id) }}" class="btn-action btn-view">
                    <i class="fas fa-eye"></i> Detail
                </a>
                <form action="{{ route('karyawan.ajukan-shift.cancel', $item->id) }}" method="POST" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn-action btn-cancel" onclick="return confirm('Batalkan pengajuan shift?')">
                        <i class="fas fa-trash-alt"></i> Batalkan
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Shift Selesai / Riwayat --}}
    @if(isset($shiftSelesai) && $shiftSelesai->count() > 0)
    <div class="section-card">
        <div class="section-header">
            <div class="section-title shift">
                <i class="fas fa-history"></i> Riwayat
            </div>
            <a href="{{ route('karyawan.ajukan-shift.riwayat') }}" class="view-all shift">
                Lihat Semua <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        @foreach($shiftSelesai->take(3) as $item)
        <div class="request-item shift">
            <div class="item-header">
                <div class="item-title">Ganti Shift {{ ucfirst($item->kode) }}</div>
                <div class="item-status {{ $item->status }}">{{ ucfirst($item->status) }}</div>
            </div>
            <div class="item-meta">
                <i class="far fa-calendar"></i>
                <span>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</span>
                @if($item->tanggal_selesai)
                    <span>s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</span>
                @else
                    <span>- Seterusnya</span>
                @endif
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="padding: 4px 10px; background: #f0f0f0; border-radius: 6px; font-size: 12px;">
                    {{ $item->shiftLama->kode ?? '-' }}
                </span>
                <span style="color: #354591;"><i class="fas fa-arrow-right"></i></span>
                <span style="padding: 4px 10px; background: #d1fae5; border-radius: 6px; font-weight: 600; font-size: 12px;">
                    {{ $item->shiftBaru->kode ?? '-' }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Empty State --}}
    @if((!isset($shiftAktif) || $shiftAktif->count() === 0) && (!isset($shiftSelesai) || $shiftSelesai->count() === 0))
    <div class="section-card">
        <div class="empty-state">
            <div class="empty-icon"><i class="far fa-clock"></i></div>
            <div class="empty-text">Belum ada pengajuan shift</div>
        </div>
    </div>
    @endif
</div>
@endif

    {{-- FAB --}}
    <div class="fab-overlay" id="fabOverlay" onclick="toggleFabMenu()"></div>
    <div class="fab-container">
        <div class="fab-menu" id="fabMenu">
            <a href="{{ route('karyawan.pengajuan.create') }}" class="fab-menu-item">
                <div class="fab-menu-icon cuti">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="fab-menu-content">
                    <div class="fab-menu-title">Ajukan Cuti</div>
                    <div class="fab-menu-desc">Buat pengajuan baru</div>
                </div>
            </a>

            <a href="{{ route('karyawan.libur-pengganti.create') }}" class="fab-menu-item">
                <div class="fab-menu-icon libur">
                    <i class="fas fa-umbrella-beach"></i>
                </div>
                <div class="fab-menu-content">
                    <div class="fab-menu-title">Libur Pengganti</div>
                    <div class="fab-menu-desc">Gunakan saldo libur</div>
                </div>
            </a>

            @if(in_array(auth()->user()->role, ['admin', 'manager', 'gm']))
            <a href="{{ route('karyawan.ajukan-shift.create') }}" class="fab-menu-item">
                <div class="fab-menu-icon shift">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="fab-menu-content">
                    <div class="fab-menu-title">Ganti Shift</div>
                    <div class="fab-menu-desc">Ajukan pergantian shift</div>
                </div>
            </a>
            @endif
        </div>

        <button class="fab-button" id="fabButton" onclick="toggleFabMenu()">
            <i class="fas fa-plus"></i>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Tab Switching
document.querySelectorAll('.tab-nav-item').forEach(tab => {
    tab.addEventListener('click', function() {
        const targetTab = this.dataset.tab;
        
        // Remove active dari semua tab dan content
        document.querySelectorAll('.tab-nav-item').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Active tab ini dan content-nya
        this.classList.add('active');
        document.getElementById('tab-' + targetTab).classList.add('active');
    });
});

// FAB Menu Toggle
function toggleFabMenu() {
    const fabMenu = document.getElementById('fabMenu');
    const fabButton = document.getElementById('fabButton');
    const fabOverlay = document.getElementById('fabOverlay');
    
    const isActive = fabMenu.classList.contains('show');
    
    if (isActive) {
        fabMenu.classList.remove('show');
        fabButton.classList.remove('active');
        fabOverlay.classList.remove('show');
    } else {
        fabMenu.classList.add('show');
        fabButton.classList.add('active');
        fabOverlay.classList.add('show');
    }
}
</script>
@endpush