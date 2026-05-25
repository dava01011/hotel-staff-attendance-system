{{-- resources/views/admin/approval/index.blade.php --}}
@extends(is_admin_mode() ? 'admin.layouts.app' : 'karyawan.layout.master')

@section('title', 'Approval Management')

@push('styles')
<style>
    /* ===== WRAPPER ===== */
    .approval-wrapper {
        display: flex;
        flex-direction: column;
        background: transparent;
    }

    @media (max-width: 768px) {
        body { overflow: hidden; }

        .approval-wrapper {
            position: fixed;
            top: 70px; left: 0; right: 0; bottom: 70px;
            background: #ffffff;
        }
    }

    /* ===== FILTER TABS ===== */
    .filter-tabs {
        display: flex;
        gap: 0;
        background: white;
        padding: 0;
        border-bottom: 1px solid #e9ecef;
        flex-shrink: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 12px 12px 0 0;
        scrollbar-width: none;
    }

    .filter-tabs::-webkit-scrollbar { display: none; }

    @media (max-width: 768px) { .filter-tabs { border-radius: 0; } }

    .tab-btn {
        flex: 0 0 auto;
        min-width: max-content;
        padding: 14px 20px;
        background: transparent;
        border: none;
        color: #6c757d;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        white-space: nowrap;
    }

    .tab-btn.active { color: #667eea; font-weight: 600; }

    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 2px;
        background: #667eea;
    }

    .tab-count {
        background: #dc3545;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        margin-left: 6px;
        display: inline-block;
    }

    /* ===== APPROVAL LIST ===== */
    .approval-list {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        background: #f8f9fa;
    }

    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* ===== DESKTOP: GRID ===== */
    .approval-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        padding: 20px;
    }

    @media (max-width: 1100px) { .approval-grid { grid-template-columns: repeat(2, 1fr); } }

    @media (max-width: 768px) {
        .approval-grid {
            display: flex;
            flex-direction: column;
            gap: 0;
            padding: 0;
            background: white;
        }
    }

    /* ===== APPROVAL CARD ===== */
    .approval-item {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: box-shadow 0.2s, transform 0.2s;
    }

    .approval-item:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .approval-item {
            border-radius: 0;
            box-shadow: none;
            border-bottom: 1px solid #f0f0f0;
        }
        .approval-item:hover { transform: none; box-shadow: none; }
    }

    .approval-card {
        padding: 16px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        background: white;
        transition: background 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        flex: 1;
    }

    .approval-card:hover  { background: #fafafa; }
    .approval-card:active { background: #f5f5f5; }

    .approval-card.your-turn {
        background: #fffbf0;
        border-left: 4px solid #ffc107;
    }

    .approval-icon { flex-shrink: 0; }

    .avatar-circle {
        width: 44px; height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 18px; font-weight: 700;
    }

    .avatar-circle.shift { background: linear-gradient(135deg, #354591 0%, #4a5db8 100%); }
    .avatar-circle.wajah { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); }

    .approval-content { flex: 1; min-width: 0; }

    .approval-header {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
        align-items: flex-start;
    }

    .approval-title {
        font-size: 15px; font-weight: 600; color: #212529;
        margin: 0 0 2px 0;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .approval-subtitle { font-size: 12px; color: #6c757d; margin: 0; }

    .cuti-badge {
        background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
        color: white; padding: 3px 8px; border-radius: 10px;
        font-size: 10px; font-weight: 700; white-space: nowrap; flex-shrink: 0;
    }

    .shift-badge {
        background: linear-gradient(135deg, #354591 0%, #4a5db8 100%);
        color: white; padding: 3px 8px; border-radius: 10px;
        font-size: 10px; font-weight: 700; white-space: nowrap; flex-shrink: 0;
    }

    .wajah-badge {
        background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
        color: white; padding: 3px 8px; border-radius: 10px;
        font-size: 10px; font-weight: 700; white-space: nowrap; flex-shrink: 0;
    }

    .approval-dates {
        display: flex; align-items: center; gap: 8px;
        margin-bottom: 8px; font-size: 12px; color: #495057;
    }

    .approval-dates i { color: #667eea; font-size: 12px; flex-shrink: 0; }

    .date-text { display: flex; flex-direction: column; gap: 1px; }
    .date-text small { font-size: 10px; color: #6c757d; }

    .shift-info {
        display: flex; align-items: center; gap: 8px;
        margin-bottom: 8px; flex-wrap: wrap;
    }

    .shift-box { padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; }
    .shift-old { background: #e2e8f0; color: #4a5568; }
    .shift-new { background: #d1fae5; color: #065f46; }
    .shift-arrow { color: #354591; font-weight: 700; }

    .approval-reason { margin-bottom: 10px; }

    .reason-label {
        font-size: 10px; color: #6c757d; font-weight: 700;
        text-transform: uppercase; display: block; margin-bottom: 3px;
    }

    .reason-text {
        font-size: 12px; color: #495057; margin: 0; line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .approval-footer {
        display: flex; align-items: center; justify-content: space-between;
        gap: 8px; margin-top: auto;
    }

    .approval-progress {
        display: flex; align-items: center; gap: 8px;
        font-size: 11px; color: #6c757d; flex: 1;
    }

    .progress-bar {
        flex: 1; height: 3px; background: #e9ecef;
        border-radius: 2px; overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transition: width 0.3s ease;
    }

    .progress-text { font-size: 11px; color: #6c757d; white-space: nowrap; }

    .approval-status { flex-shrink: 0; }

    .badge-turn {
        display: inline-flex; align-items: center; gap: 5px;
        background: #fff3cd; color: #856404;
        padding: 5px 8px; border-radius: 10px;
        font-size: 11px; font-weight: 700;
    }

    .badge-turn i { font-size: 9px; animation: pulse 1.5s infinite; }

    .badge-waiting {
        display: inline-flex; align-items: center; gap: 4px;
        background: #e2e8f0; color: #4a5568;
        padding: 4px 7px; border-radius: 6px;
        font-size: 10px; font-weight: 700;
    }

    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

    /* ===== ACTION BUTTONS ===== */
    .approval-actions {
        padding: 10px 16px;
        border-top: 1px solid #f0f0f0;
        display: flex; gap: 8px;
        background: #fafafa;
    }

    .btn-action {
        flex: 1; padding: 7px 10px; border: none; border-radius: 6px;
        font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 5px;
    }

    .btn-approve { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
    .btn-approve:hover { box-shadow: 0 4px 12px rgba(16,185,129,0.3); }

    .btn-reject { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; }
    .btn-reject:hover { box-shadow: 0 4px 12px rgba(239,68,68,0.3); }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center; padding: 80px 20px;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        grid-column: 1 / -1;
    }

    .empty-icon {
        width: 80px; height: 80px; margin: 0 auto 20px;
        background: #f8f9fa; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #adb5bd; font-size: 36px;
    }

    .empty-state h3 { font-size: 16px; color: #495057; margin-bottom: 6px; font-weight: 600; }
    .empty-state p  { font-size: 13px; color: #adb5bd; }

    /* ===== PAGINATION ===== */
    .pagination-wrapper {
        background: white; padding: 16px 20px;
        border-top: 1px solid #e9ecef;
        grid-column: 1 / -1;
    }

    .pagination {
        display: flex; justify-content: center; gap: 6px;
        margin: 0; padding: 0; list-style: none; flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        width: 36px; height: 36px; border-radius: 6px;
        font-size: 13px; color: #495057; text-decoration: none;
        background: #f8f9fa; display: flex;
        align-items: center; justify-content: center;
        font-weight: 500; transition: all 0.2s;
    }

    .pagination a:hover          { background: #667eea; color: white; }
    .pagination .active span     { background: #667eea; color: white; }

    /* ===== REJECT MODAL (shared) ===== */
    .reject-modal-overlay {
        display: none; position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center; justify-content: center;
    }

    .reject-modal-overlay.show { display: flex; animation: overlayFadeIn .3s ease forwards; }
    .reject-modal-overlay.hide { display: flex; animation: overlayFadeOut .3s ease forwards; }

    @keyframes overlayFadeIn  { from{opacity:0} to{opacity:1} }
    @keyframes overlayFadeOut { from{opacity:1} to{opacity:0} }

    .reject-modal-content {
        background: white; width: 420px; max-width: 90%;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        overflow: hidden; margin: 0 auto;
        transform: scale(0.9) translateY(20px); opacity: 0;
    }

    .reject-modal-overlay.show .reject-modal-content {
        animation: contentPopIn .4s cubic-bezier(.34,1.56,.64,1) forwards;
    }

    .reject-modal-overlay.hide .reject-modal-content {
        animation: contentPopOut .3s ease forwards;
    }

    @keyframes contentPopIn  { 0%{opacity:0;transform:scale(.8) translateY(20px)} 100%{opacity:1;transform:scale(1) translateY(0)} }
    @keyframes contentPopOut { 0%{opacity:1;transform:scale(1) translateY(0)} 100%{opacity:0;transform:scale(.8) translateY(20px)} }

    .reject-modal-header {
        padding: 24px 24px 16px; text-align: center;
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        position: relative; overflow: hidden;
    }

    .reject-modal-header::before {
        content: ''; position: absolute;
        top: -30px; right: -30px; width: 150px; height: 150px;
        background: linear-gradient(135deg, #fecaca, #fca5a5);
        border-radius: 50%; opacity: .4; z-index: 0;
    }

    .reject-modal-icon {
        width: 60px; height: 60px; margin: 0 auto 12px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; background: #dc2626; color: white;
        font-size: 28px; position: relative; z-index: 2;
        animation: pulse 2s ease-in-out infinite;
    }

    .reject-modal-header h3 {
        font-size: 1.5rem; font-weight: 700; color: #b91c1c;
        margin-bottom: 4px; position: relative; z-index: 2;
    }

    .reject-modal-body { padding: 20px 24px; }
    .reject-modal-body .form-group { margin-bottom: 16px; }

    .reject-modal-body label {
        display: block; font-size: 14px; font-weight: 600;
        color: #1e293b; margin-bottom: 6px;
    }

    .reject-modal-body label i { color: #dc2626; margin-right: 4px; }

    .reject-modal-body textarea {
        width: 100%; padding: 12px;
        border: 2px solid #fee2e2; border-radius: 12px;
        font-family: inherit; font-size: 13px;
        resize: vertical; transition: all .2s; background: #fef2f2;
    }

    .reject-modal-body textarea:focus {
        outline: none; border-color: #dc2626; background: white;
        box-shadow: 0 0 0 3px rgba(220,38,38,.1);
    }

    .reject-modal-body small { display: block; font-size: 11px; color: #64748b; margin-top: 4px; }

    .reject-modal-footer {
        padding: 16px 24px 24px; display: flex; gap: 12px;
        justify-content: center; background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .reject-modal-btn {
        padding: 10px 24px; border: none; border-radius: 40px;
        font-size: .9rem; font-weight: 600; cursor: pointer; transition: all .2s;
        min-width: 110px; display: flex; align-items: center; justify-content: center; gap: 6px;
    }

    .reject-modal-btn.cancel { background: #e2e8f0; color: #475569; }
    .reject-modal-btn.cancel:hover { background: #cbd5e1; color: #1e293b; }
    .reject-modal-btn.submit { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
    .reject-modal-btn.submit:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(220,38,38,.3); }

    @media (max-width: 480px) {
        .tab-btn { padding: 10px 12px; font-size: 13px; }
        .approval-card { padding: 12px; gap: 10px; }
        .avatar-circle { width: 38px; height: 38px; font-size: 15px; }
        .approval-title { font-size: 14px; }
        .reject-modal-content { width: 90%; margin: 0 20px; }
        .reject-modal-footer { flex-direction: column; }
        .reject-modal-btn { width: 100%; }
        .reject-modal-header { padding: 20px 20px 12px; }
        .reject-modal-icon { width: 50px; height: 50px; font-size: 24px; }
        .reject-modal-header h3 { font-size: 1.3rem; }
        .reject-modal-body { padding: 16px 20px; }
    }
</style>
@endpush

@section('content')
<div class="approval-wrapper">

    {{-- ── Filter Tabs ── --}}
    <div class="filter-tabs">
        <button class="tab-btn active" data-tab="cuti">
            <i class="fas fa-calendar-alt"></i> Cuti
            @if($cutiCount['pending'] > 0)
            <span class="tab-count">{{ $cutiCount['pending'] }}</span>
            @endif
        </button>

        @if(in_array(Auth::user()->role, ['super_admin', 'gm']))
        <button class="tab-btn" data-tab="wajah">
            <i class="fas fa-user-circle"></i> Ganti Wajah
            @if($wajahCount['pending'] > 0)
            <span class="tab-count">{{ $wajahCount['pending'] }}</span>
            @endif
        </button>
        @endif

        @if(in_array(Auth::user()->role, ['super_admin', 'gm', 'admin']))
        <button class="tab-btn" data-tab="shift">
            <i class="fas fa-sync-alt"></i> Shift
            @if($shiftCount['pending'] > 0)
            <span class="tab-count">{{ $shiftCount['pending'] }}</span>
            @endif
        </button>
        @endif
        
        {{-- Tab Libur Pengganti --}}
        <button class="tab-btn" data-tab="libur">
            <i class="fas fa-umbrella-beach"></i> Libur Pengganti
            @if($liburCount['pending'] > 0)
            <span class="tab-count">{{ $liburCount['pending'] }}</span>
            @endif
        </button>
    </div>

    {{-- ── Approval List ── --}}
    <div class="approval-list">

        {{-- ══ TAB: Cuti ══ --}}
        <div class="tab-content active" id="tab-cuti">
            <div class="approval-grid">
                @forelse($cuti as $item)
                    @php
                        $isYourTurn      = $item->current_step === Auth::user()->role;
                        $totalApprovals  = $item->approvals->count();
                        $doneApprovals   = $item->approvals->where('status', '!=', 'pending')->count();
                        $progressPercent = $totalApprovals > 0 ? ($doneApprovals / $totalApprovals) * 100 : 0;
                    @endphp
                    <div class="approval-item">
                        <a href="{{ route('admin.approval.cuti.detail', $item->id) }}"
                           class="approval-card {{ $isYourTurn ? 'your-turn' : '' }}">
                            <div class="approval-icon">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr($item->karyawan->user->nama, 0, 1)) }}
                                </div>
                            </div>
                            <div class="approval-content">
                                <div class="approval-header">
                                    <div style="min-width:0;flex:1;">
                                        <h3 class="approval-title">{{ $item->karyawan->user->nama }}</h3>
                                        <p class="approval-subtitle">{{ $item->karyawan->departemen->nama }}</p>
                                    </div>
                                    <span class="cuti-badge">{{ $item->jenisCuti->nama }}</span>
                                </div>
                                <div class="approval-dates">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span class="date-text">
                                        {{ $item->tanggal_mulai->format('d M') }} – {{ $item->tanggal_selesai->format('d M Y') }}
                                        <small>{{ $item->jumlahHari }} hari</small>
                                    </span>
                                </div>
                                <div class="approval-reason">
                                    <span class="reason-label">Alasan:</span>
                                    <p class="reason-text">{{ $item->alasan }}</p>
                                </div>
                                <div class="approval-footer">
                                    <div class="approval-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width:{{ $progressPercent }}%"></div>
                                        </div>
                                        <span class="progress-text">{{ round($progressPercent) }}%</span>
                                    </div>
                                    <div class="approval-status">
                                        @if($isYourTurn)
                                            @php
                                                $myRole = Auth::user()->role;
                                                $myLabel = [
                                                    'admin' => 'ADM',
                                                    'gm'    => 'GM',
                                                    'super_admin' => 'SA'
                                                ][$myRole] ?? strtoupper($myRole);
                                            @endphp
                                            <span class="badge-turn"><i class="fas fa-bell"></i> Giliran {{ $myLabel }}</span>
                                        @else
                                            @php
                                                $stepLabelMap = [
                                                    'admin' => 'ADM',
                                                    'gm'    => 'GM',
                                                    'super_admin' => 'SA'
                                                ];
                                                $stepLabel = $stepLabelMap[$item->current_step] ?? strtoupper(substr($item->current_step, 0, 3));
                                            @endphp
                                            <span class="badge-waiting">
                                                <i class="fas fa-hourglass-half"></i>
                                                {{ $stepLabel }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                        @if($isYourTurn)
                        <div class="approval-actions">
                            <button class="btn-action btn-approve"
                                    onclick="showConfirmCutiApprove({{ $item->id }}, '{{ addslashes($item->karyawan->user->nama) }}', '{{ addslashes($item->jenisCuti->nama) }}')">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                            <button class="btn-action btn-reject"
                                    onclick="showRejectModal('cuti', {{ $item->id }})">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                        <h3>Tidak ada approval cuti</h3>
                        <p>Semua pengajuan cuti sudah diproses</p>
                    </div>
                @endforelse

@if(method_exists($cuti, 'hasPages') && $cuti->hasPages())
<div class="pagination-wrapper">{{ $cuti->links() }}</div>
@endif
            </div>
        </div>
        
        {{-- ══ TAB: Libur Pengganti ══ --}}
        <div class="tab-content" id="tab-libur">
            <div class="approval-grid">
                @forelse($liburPengganti as $item)
                    @php
                        $isYourTurn      = $item->current_step === Auth::user()->role;
                        $totalApprovals  = $item->approvals->count();
                        $doneApprovals   = $item->approvals->where('status', '!=', 'pending')->count();
                        $progressPercent = $totalApprovals > 0 ? ($doneApprovals / $totalApprovals) * 100 : 0;
                    @endphp
                    <div class="approval-item">
                        <a href="{{ route('admin.libur-pengganti.detail', $item->id) }}"
                           class="approval-card {{ $isYourTurn ? 'your-turn' : '' }}">
                            <div class="approval-icon">
                                <div class="avatar-circle" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                    {{ strtoupper(substr($item->karyawan->user->nama, 0, 1)) }}
                                </div>
                            </div>
                            <div class="approval-content">
                                <div class="approval-header">
                                    <div style="min-width:0;flex:1;">
                                        <h3 class="approval-title">{{ $item->karyawan->user->nama }}</h3>
                                        <p class="approval-subtitle">{{ $item->karyawan->departemen->nama }}</p>
                                    </div>
                                    <span class="badge" style="background: #11998e; color: white; padding: 3px 8px; border-radius: 10px; font-size: 10px; font-weight: 700;">
                                        Libur Pengganti
                                    </span>
                                </div>
                                <div class="approval-dates">
                                    <i class="fas fa-calendar-alt" style="color: #11998e;"></i>
                                    <span class="date-text">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                    </span>
                                </div>
                                <div class="approval-reason">
                                    <span class="reason-label">Alasan:</span>
                                    <p class="reason-text">{{ $item->alasan }}</p>
                                </div>
                                <div class="approval-footer">
                                    <div class="approval-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width:{{ $progressPercent }}%; background: linear-gradient(90deg, #11998e, #38ef7d);"></div>
                                        </div>
                                        <span class="progress-text">{{ round($progressPercent) }}%</span>
                                    </div>
                                    <div class="approval-status">
                                        @if($isYourTurn)
                                            @php
                                                $myRole = Auth::user()->role;
                                                $myLabel = [
                                                    'admin' => 'ADM',
                                                    'gm'    => 'GM',
                                                    'super_admin' => 'SA'
                                                ][$myRole] ?? strtoupper($myRole);
                                            @endphp
                                            <span class="badge-turn"><i class="fas fa-bell"></i> Giliran {{ $myLabel }}</span>
                                        @else
                                            @php
                                                $stepLabelMap = [
                                                    'admin' => 'ADM',
                                                    'gm'    => 'GM',
                                                    'super_admin' => 'SA'
                                                ];
                                                $stepLabel = $stepLabelMap[$item->current_step] ?? strtoupper(substr($item->current_step, 0, 3));
                                            @endphp
                                            <span class="badge-waiting">
                                                <i class="fas fa-hourglass-half"></i>
                                                {{ $stepLabel }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                        @if($isYourTurn)
                        <div class="approval-actions">
                            <button class="btn-action btn-approve"
                                    onclick="showConfirmLiburApprove({{ $item->id }}, '{{ addslashes($item->karyawan->user->nama) }}')">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                            <button class="btn-action btn-reject"
                                    onclick="showRejectModal('libur', {{ $item->id }})">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-umbrella-beach"></i></div>
                        <h3>Tidak ada approval libur pengganti</h3>
                        <p>Semua pengajuan libur pengganti sudah diproses</p>
                    </div>
                @endforelse
        
@if(method_exists($liburPengganti, 'hasPages') && $liburPengganti->hasPages())
    <div class="pagination-wrapper">{{ $liburPengganti->links() }}</div>
@endif
            </div>
        </div>

        {{-- ══ TAB: Ganti Wajah ══ --}}
        @if(in_array(Auth::user()->role, ['super_admin', 'gm']))
        <div class="tab-content" id="tab-wajah">
            <div class="approval-grid">
                @forelse($wajahRequests as $item)
                    <div class="approval-item">
                        <div class="approval-card your-turn" style="cursor:default;">
                            <div class="approval-icon">
                                <div class="avatar-circle wajah">
                                    {{ strtoupper(substr($item->karyawan->user->nama, 0, 1)) }}
                                </div>
                            </div>
                            <div class="approval-content">
                                <div class="approval-header">
                                    <div style="min-width:0;flex:1;">
                                        <h3 class="approval-title">{{ $item->karyawan->user->nama }}</h3>
                                        <p class="approval-subtitle">{{ $item->karyawan->departemen->nama ?? '-' }}</p>
                                    </div>
                                    <span class="wajah-badge">Ganti Wajah</span>
                                </div>
                                <div class="approval-dates">
                                    <i class="fas fa-clock"></i>
                                    <span class="date-text">
                                        {{ $item->created_at->format('d M Y, H:i') }}
                                        <small>{{ $item->created_at->diffForHumans() }}</small>
                                    </span>
                                </div>
                                <div class="approval-reason">
                                    <span class="reason-label">Alasan:</span>
                                    <p class="reason-text">{{ $item->alasan }}</p>
                                </div>
                                <div class="approval-footer">
                                    <div class="approval-status">
                                        <span class="badge-turn">
                                            @php
                                                $myRole = Auth::user()->role;
                                                $myLabel = [
                                                    'admin' => 'ADM',
                                                    'gm'    => 'GM',
                                                    'super_admin' => 'SA'
                                                ][$myRole] ?? strtoupper($myRole);
                                            @endphp
                                            <i class="fas fa-bell"></i> Giliran {{ $myLabel }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="approval-actions">
                            <button class="btn-action btn-approve"
                                    onclick="showConfirmWajahApprove({{ $item->id }}, '{{ addslashes($item->karyawan->user->nama) }}')">
                                <i class="fas fa-check"></i> Setujui
                            </button>
                            <button class="btn-action btn-reject"
                                    onclick="showRejectModal('wajah', {{ $item->id }})">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-user-circle"></i></div>
                        <h3>Tidak ada permohonan ganti wajah</h3>
                        <p>Semua permohonan sudah diproses</p>
                    </div>
                @endforelse

@if(method_exists($wajahRequests, 'hasPages') && $wajahRequests->hasPages())
    <div class="pagination-wrapper">{{ $wajahRequests->links() }}</div>
@endif
            </div>
        </div>
        @endif

{{-- ══ TAB: Shift ══ --}}
@if(in_array(Auth::user()->role, ['super_admin', 'admin']))
<div class="tab-content" id="tab-shift">
    <div class="approval-grid">
        @forelse($shift as $item)
            @php
                $isYourTurn      = $item->current_step === Auth::user()->role;
                $totalApprovals  = $item->approvals->count();
                $doneApprovals   = $item->approvals->where('status', '!=', 'pending')->count();
                $progressPercent = $totalApprovals > 0 ? ($doneApprovals / $totalApprovals) * 100 : 0;
            @endphp
            <div class="approval-item">
                <a href="{{ route('admin.approval.shift.detail', $item->id) }}"
                   class="approval-card {{ $isYourTurn ? 'your-turn' : '' }}">
                    <div class="approval-icon">
                        <div class="avatar-circle shift">
                            {{ strtoupper(substr($item->pemohon->nama, 0, 1)) }}
                        </div>
                    </div>
                    <div class="approval-content">
                        <div class="approval-header">
                            <div style="min-width:0;flex:1;">
                                <h3 class="approval-title">{{ $item->pemohon->nama }}</h3>
                                <p class="approval-subtitle">{{ $item->departemen->nama }}</p>
                            </div>
                            <span class="shift-badge">{{ ucfirst($item->jenis) }}</span>
                        </div>
                        <div class="shift-info">
                            <span class="shift-box shift-old">{{ $item->shiftLama->jenis }}</span>
                            <span class="shift-arrow"><i class="fas fa-arrow-right"></i></span>
                            <span class="shift-box shift-new">{{ $item->shiftBaru->jenis }}</span>
                        </div>
                        <div class="approval-dates">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="date-text">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                @if($item->tanggal_selesai)
                                    – {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                @else
                                    – Permanen
                                @endif
                            </span>
                        </div>
                        <div class="approval-reason">
                            <span class="reason-label">Alasan:</span>
                            <p class="reason-text">{{ $item->alasan }}</p>
                        </div>
                        <div class="approval-footer">
                            <div class="approval-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width:{{ $progressPercent }}%"></div>
                                </div>
                                <span class="progress-text">{{ round($progressPercent) }}%</span>
                            </div>
                            <div class="approval-status">
                                @if($isYourTurn)
                                    @php
                                        $myRole = Auth::user()->role;
                                        $myLabel = [
                                            'admin' => 'ADM',
                                            'hrd'   => 'HRD',
                                            'gm'    => 'GM',
                                            'super_admin' => 'SA'
                                        ][$myRole] ?? strtoupper($myRole);
                                    @endphp
                                    <span class="badge-turn"><i class="fas fa-bell"></i> Giliran {{ $myLabel }}</span>
                                @else
                                    @php
                                        $stepLabelMap = [
                                            'admin' => 'ADM',
                                            'hrd'   => 'HRD',
                                            'gm'    => 'GM',
                                            'super_admin' => 'SA'
                                        ];
                                        $stepLabel = $stepLabelMap[$item->current_step] ?? strtoupper(substr($item->current_step, 0, 3));
                                    @endphp
                                    <span class="badge-waiting">
                                        <i class="fas fa-hourglass-half"></i>
                                        {{ $stepLabel }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                @if($isYourTurn)
                <div class="approval-actions">
                    <button class="btn-action btn-approve"
                            onclick="showConfirmShiftApprove({{ $item->id }}, '{{ addslashes($item->departemen->nama) }}')">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                    <button class="btn-action btn-reject"
                            onclick="showRejectModal('shift', {{ $item->id }})">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                </div>
                @endif
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                <h3>Tidak ada approval shift</h3>
                <p>Semua pengajuan shift sudah diproses</p>
            </div>
        @endforelse

@if(method_exists($shift, 'hasPages') && $shift->hasPages())
<div class="pagination-wrapper">{{ $shift->links() }}</div>
@endif
    </div>
</div>
@endif

{{-- ── Single Reject Modal (shared) ── --}}
<div id="rejectModal" class="reject-modal-overlay" onclick="closeRejectModal(event)">
    <div class="reject-modal-content" onclick="event.stopPropagation()">
        <div class="reject-modal-header">
            <div class="reject-modal-icon"><i class="fas fa-times"></i></div>
            <h3 id="rejectModalTitle">Tolak Pengajuan</h3>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="reject-modal-body">
                <div class="form-group">
                    <label><i class="fas fa-pencil-alt"></i> Alasan Penolakan <span style="color:#ef4444">*</span></label>
                    <textarea name="catatan_admin"
                              placeholder="Jelaskan alasan penolakan secara detail..."
                              required minlength="10" rows="4"></textarea>
                    <small><i class="fas fa-info-circle"></i> Minimum 10 karakter</small>
                </div>
            </div>
            <div class="reject-modal-footer">
                <button type="button" class="reject-modal-btn cancel" onclick="closeRejectModal()">
                    <i class="fas fa-arrow-left"></i> Batal
                </button>
                <button type="submit" class="reject-modal-btn submit">
                    <i class="fas fa-times"></i> Ya, Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Tab switching ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            this.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        });
    });
});

// ── Shared reject modal ──────────────────────────────────────
const rejectRoutes = {
    cuti:  `{{ url('/admin/cuti') }}`,
    shift: `{{ url('/admin/shift') }}`,
    wajah: `{{ url('/admin/wajah/requests') }}`,
    libur: `{{ url('/admin/libur-pengganti') }}`,   // <-- tambahkan
};

const rejectTitles = {
    cuti:  'Tolak Pengajuan Cuti',
    shift: 'Tolak Pengajuan Shift',
    wajah: 'Tolak Permohonan Ganti Wajah',
    libur: 'Tolak Pengajuan Libur Pengganti',       // <-- tambahkan
};

function showRejectModal(type, id) {
    const modal = document.getElementById('rejectModal');
    document.getElementById('rejectModalTitle').textContent = rejectTitles[type];
    document.getElementById('rejectForm').action = `${rejectRoutes[type]}/${id}/reject`;
    modal.classList.remove('hide');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    setTimeout(() => modal.querySelector('textarea').focus(), 300);
}

function closeRejectModal(event) {
    if (!event || event.target === event.currentTarget) {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hide');
        setTimeout(() => {
            modal.classList.remove('show', 'hide');
            document.body.style.overflow = '';
            document.getElementById('rejectForm').reset();
        }, 300);
    }
}

// ── Confirm helpers ──────────────────────────────────────────
function showConfirmCutiApprove(id, karyawan, jenis) {
    showAlert('warning', 'Setujui Pengajuan Cuti?',
        `Anda akan menyetujui cuti <strong>${karyawan}</strong> untuk ${jenis}. Lanjutkan?`,
        function () {
            submitForm(`{{ url('/admin/cuti') }}/${id}/approve`);
        }
    );
}

function showConfirmShiftApprove(id, departemen) {
    showAlert('warning', 'Setujui Pengajuan Shift?',
        `Anda akan menyetujui perubahan shift untuk departemen <strong>${departemen}</strong>. Lanjutkan?`,
        function () {
            submitForm(`{{ url('/admin/shift') }}/${id}/approve`);
        }
    );
}

function showConfirmLiburApprove(id, karyawan) {
    showAlert('warning', 'Setujui Pengajuan Libur Pengganti?',
        `Anda akan menyetujui libur pengganti dari <strong>${karyawan}</strong>. Lanjutkan?`,
        function () {
            submitForm(`{{ url('/admin/libur-pengganti') }}/${id}/approve`);
        }
    );
}

function showConfirmWajahApprove(id, nama) {
    showAlert('warning', 'Setujui Permohonan Ganti Wajah?',
        `Anda akan menyetujui permohonan ganti template wajah dari <strong>${nama}</strong>.<br>Karyawan dapat langsung capture wajah baru setelah ini.`,
        function () {
            submitForm(`{{ url('/admin/wajah/requests') }}/${id}/approve`);
        }
    );
}

function submitForm(action) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;
    form.innerHTML = `@csrf`;
    document.body.appendChild(form);
    form.submit();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRejectModal(); });

@if(session('alert'))
    const alertData = {!! json_encode(session('alert')) !!};
    setTimeout(() => showAlert(alertData.type, alertData.title, alertData.message), 500);
@endif
</script>
@endpush
