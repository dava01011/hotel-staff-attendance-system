@extends(is_admin_mode() ? 'admin.layouts.app' : 'karyawan.layout.fullscreen')

@section('title', 'Detail Approval Libur Pengganti')
@section('page-title', 'Detail Approval Libur Pengganti')
@section('page-subtitle', 'Review pengajuan libur pengganti')

@push('styles')
<style>
    /* Copy semua style dari detail cuti, ganti warna aksen dengan #11998e dan #38ef7d */
    /* ===== WRAPPER ===== */
    .approval-detail-wrapper {
        max-width: 760px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    @media (max-width: 768px) {
        body { overflow: hidden; }
        .approval-detail-wrapper {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            bottom: 0;
            max-width: 100%;
            margin: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    /* ===== HEADER ===== */
    .detail-header {
        display: none;
    }

    @media (max-width: 768px) {
        .detail-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            background: white;
            border-bottom: 1px solid #e9ecef;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .detail-header h1 {
            font-size: 17px;
            font-weight: 700;
            color: #212529;
            margin: 0;
        }
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

    /* ===== CONTENT ===== */
    .detail-content {
        flex: 1;
        background: #f8f9fa;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    @media (max-width: 768px) {
        .detail-content {
            padding: 12px;
            padding-bottom: 100px;
        }
    }

    /* ===== CARDS ===== */
    .detail-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        overflow: hidden;
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

    /* ===== DETAIL ROWS ===== */
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

    /* ===== REASON TEXT ===== */
    .reason-text {
        padding: 14px 18px;
        font-size: 14px;
        color: #495057;
        line-height: 1.7;
        word-break: break-word;
    }

    /* ===== BADGE ===== */
    .badge-green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
    }

    /* ===== FILE LINK ===== */
    .file-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #11998e;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 14px;
        background: #e6f7f5;
        border-radius: 8px;
        border: 1px solid #a7e0db;
        transition: all 0.2s;
        margin: 14px 18px;
    }

    .file-link:hover {
        background: #cceae7;
        color: #0b6e66;
    }

    /* ===== TIMELINE ===== */
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
    }

    .timeline-item:not(:last-child) {
        padding-bottom: 20px;
    }

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

    .timeline-dot.pending {
        background: #fff3cd;
        color: #856404;
        border: 2px solid #ffc107;
    }

    .timeline-dot.disetujui { background: #11998e; }
    .timeline-dot.ditolak { background: #ef4444; }

    .timeline-info {
        padding-top: 4px;
        flex: 1;
    }

    .timeline-step {
        font-size: 13px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 2px;
    }

    .timeline-time {
        font-size: 12px;
        color: #6c757d;
    }

    .timeline-note {
        font-size: 12px;
        color: #ef4444;
        margin-top: 3px;
        font-style: italic;
    }

    /* ===== ACTION BUTTONS ===== */
    .detail-actions {
        display: flex;
        gap: 10px;
        padding: 16px 20px;
        background: white;
        border-top: 1px solid #e9ecef;
    }

    @media (min-width: 769px) {
        .detail-actions {
            border-radius: 0 0 12px 12px;
            border: 1px solid #e9ecef;
            border-top: 1px solid #e9ecef;
            max-width: 760px;
            margin: 0 auto;
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .detail-actions {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 12px 16px;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.08);
            z-index: 100;
        }
    }

    .btn {
        flex: 1;
        padding: 11px 16px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        text-decoration: none;
        color: inherit;
    }

    .btn-secondary { background: #e9ecef; color: #374151; }
    .btn-secondary:hover { background: #dee2e6; color: #212529; }

    .btn-approve {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    .btn-approve:hover { box-shadow: 0 4px 12px rgba(17,153,142,0.3); }

    .btn-reject {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    .btn-reject:hover { box-shadow: 0 4px 12px rgba(239,68,68,0.3); }

    .btn-disabled {
        background: #e9ecef;
        color: #9ca3af;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* ===== REJECT MODAL ===== */
    .reject-modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .reject-modal-overlay.show {
        display: flex;
        animation: overlayIn 0.25s ease forwards;
    }

    .reject-modal-overlay.hide {
        display: flex;
        animation: overlayOut 0.25s ease forwards;
    }

    @keyframes overlayIn { from { opacity:0; } to { opacity:1; } }
    @keyframes overlayOut { from { opacity:1; } to { opacity:0; } }

    .reject-modal-box {
        background: white;
        width: 440px;
        max-width: 92%;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        opacity: 0;
        transform: scale(0.9) translateY(16px);
    }

    .reject-modal-overlay.show .reject-modal-box {
        animation: boxIn 0.35s cubic-bezier(0.34,1.56,0.64,1) forwards;
    }

    .reject-modal-overlay.hide .reject-modal-box {
        animation: boxOut 0.25s ease forwards;
    }

    @keyframes boxIn {
        0% { opacity:0; transform: scale(0.85) translateY(20px); }
        100% { opacity:1; transform: scale(1) translateY(0); }
    }

    @keyframes boxOut {
        0% { opacity:1; transform: scale(1) translateY(0); }
        100% { opacity:0; transform: scale(0.85) translateY(20px); }
    }

    .reject-modal-head {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        padding: 22px 20px 16px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .reject-modal-head::before {
        content: '';
        position: absolute;
        top: -24px; right: -24px;
        width: 120px; height: 120px;
        background: #fca5a5;
        border-radius: 50%;
        opacity: 0.35;
    }

    .reject-modal-icon {
        width: 54px; height: 54px;
        border-radius: 50%;
        background: #dc2626;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin: 0 auto 10px;
        position: relative;
        z-index: 1;
    }

    .reject-modal-head h3 {
        font-size: 17px;
        font-weight: 700;
        color: #b91c1c;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .reject-modal-body {
        padding: 18px 20px;
    }

    .reject-modal-body label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .reject-modal-body textarea {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #fee2e2;
        border-radius: 10px;
        font-family: inherit;
        font-size: 13px;
        resize: vertical;
        background: #fef2f2;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .reject-modal-body textarea:focus {
        outline: none;
        border-color: #dc2626;
        background: white;
        box-shadow: 0 0 0 3px rgba(220,38,38,0.1);
    }

    .reject-modal-body small {
        display: block;
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }

    .reject-modal-foot {
        padding: 14px 20px 20px;
        display: flex;
        gap: 10px;
        background: #f8fafc;
        border-top: 1px solid #f0f0f0;
    }

    .modal-btn {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .modal-btn.cancel { background: #e5e7eb; color: #374151; }
    .modal-btn.cancel:hover { background: #d1d5db; }
    .modal-btn.submit {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    .modal-btn.submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220,38,38,0.3);
    }

    /* ===== MOBILE TWEAKS ===== */
    @media (max-width: 480px) {
        .detail-header { padding: 12px 14px; }
        .detail-header h1 { font-size: 16px; }
        .card-header { padding: 12px 14px; }
        .detail-row { padding: 10px 14px; }
        .reason-text { padding: 12px 14px; }
        .timeline { padding: 12px 14px; }
        .file-link { margin: 12px 14px; }
        .reject-modal-foot { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="approval-detail-wrapper">

    <!-- Header -->
    <div class="detail-header">
        <a href="{{ route('admin.approval') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Detail Approval Libur Pengganti</h1>
    </div>

    <!-- Content -->
    <div class="detail-content">

        <!-- Informasi Karyawan -->
        <div class="detail-card">
            <div class="card-header">
                <i class="fas fa-user"></i>
                <span>Informasi Karyawan</span>
            </div>
            <div class="card-body">
                <div class="detail-row">
                    <span class="detail-label">Nama</span>
                    <span class="detail-value">{{ $pengajuan->karyawan->user->nama }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Departemen</span>
                    <span class="detail-value">{{ $pengajuan->karyawan->departemen->nama }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Jabatan</span>
                    <span class="detail-value">{{ $pengajuan->karyawan->jabatan->nama ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Informasi Libur Pengganti -->
        <div class="detail-card">
            <div class="card-header">
                <i class="fas fa-umbrella-beach"></i>
                <span>Informasi Libur Pengganti</span>
            </div>
            <div class="card-body">
                <div class="detail-row">
                    <span class="detail-label">Tanggal Libur</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($pengajuan->tanggal)->format('d F Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Diajukan</span>
                    <span class="detail-value">{{ $pengajuan->created_at->format('d F Y, H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Alasan -->
        <div class="detail-card">
            <div class="card-header">
                <i class="fas fa-comment-alt"></i>
                <span>Alasan Pengajuan</span>
            </div>
            <p class="reason-text">{{ $pengajuan->alasan }}</p>
        </div>

        <!-- File Pendukung -->
        @if($pengajuan->file_pendukung)
        <div class="detail-card">
            <div class="card-header">
                <i class="fas fa-file"></i>
                <span>File Pendukung</span>
            </div>
            <a href="{{ Storage::url($pengajuan->file_pendukung) }}" target="_blank" class="file-link">
                <i class="fas fa-download"></i> Unduh File
            </a>
        </div>
        @endif

        <!-- Progress Timeline -->
        <div class="detail-card">
            <div class="card-header">
                <i class="fas fa-tasks"></i>
                <span>Progress Persetujuan</span>
            </div>
            <div class="timeline">
                @foreach($pengajuan->approvals as $approval)
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
                            <div class="timeline-step">{{ $approval->role_label }}</div>
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
                @endforeach
            </div>
        </div>

    </div>

    <!-- Action Buttons -->
    @php $isYourTurn = $pengajuan->current_step === Auth::user()->role; @endphp
    <div class="detail-actions">
        <a href="{{ route('admin.approval') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>

        @if($isYourTurn)
            <button type="button" class="btn btn-approve" onclick="confirmApprove()">
                <i class="fas fa-check"></i> Setujui
            </button>
            <button type="button" class="btn btn-reject" onclick="showRejectModal()">
                <i class="fas fa-times"></i> Tolak
            </button>
        @elseif($pengajuan->status === 'pending')
            @php
                $currentLabel = [
                    'admin' => 'Admin Departemen',
                    'gm'    => 'General Manager',
                    'super_admin' => 'Super Admin'
                ][$pengajuan->current_step] ?? strtoupper($pengajuan->current_step);
            @endphp
            <button type="button" class="btn btn-disabled" disabled>
                <i class="fas fa-lock"></i> Menunggu {{ $currentLabel }}
            </button>
        @else
            <button type="button" class="btn btn-disabled" disabled>
                <i class="fas fa-lock"></i> Sudah {{ ucfirst($pengajuan->status) }}
            </button>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="reject-modal-overlay" onclick="handleOverlayClick(event)">
    <div class="reject-modal-box" onclick="event.stopPropagation()">
        <div class="reject-modal-head">
            <div class="reject-modal-icon"><i class="fas fa-times"></i></div>
            <h3>Tolak Pengajuan Libur Pengganti</h3>
        </div>
        <form id="rejectForm" method="POST" action="{{ route('admin.libur-pengganti.reject', $pengajuan->id) }}">
            @csrf
            <div class="reject-modal-body">
                <label>Alasan Penolakan <span style="color:#ef4444">*</span></label>
                <textarea name="catatan_admin" placeholder="Jelaskan alasan penolakan secara detail..." required minlength="10" rows="4"></textarea>
                <small><i class="fas fa-info-circle"></i> Minimal 10 karakter</small>
            </div>
            <div class="reject-modal-foot">
                <button type="button" class="modal-btn cancel" onclick="closeRejectModal()">
                    <i class="fas fa-arrow-left"></i> Batal
                </button>
                <button type="submit" class="modal-btn submit">
                    <i class="fas fa-times"></i> Ya, Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.remove('hide');
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        setTimeout(() => modal.querySelector('textarea').focus(), 300);
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hide');
        setTimeout(() => {
            modal.classList.remove('show', 'hide');
            document.body.style.overflow = '';
            document.getElementById('rejectForm').reset();
        }, 250);
    }

    function handleOverlayClick(event) {
        if (event.target === event.currentTarget) closeRejectModal();
    }

    function confirmApprove() {
        showAlert('warning', 'Setujui Pengajuan Libur Pengganti?', 'Anda yakin ingin menyetujui pengajuan ini?', function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.libur-pengganti.approve', $pengajuan->id) }}`;
            form.innerHTML = `@csrf<input type="hidden" name="catatan_admin" value="">`;
            document.body.appendChild(form);
            form.submit();
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeRejectModal();
    });

    @if(session('alert'))
        const alertData = {!! json_encode(session('alert')) !!};
        setTimeout(() => showAlert(alertData.type, alertData.title, alertData.message), 500);
    @endif
</script>
@endpush