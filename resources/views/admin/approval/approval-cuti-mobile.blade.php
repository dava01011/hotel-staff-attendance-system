@extends('karyawan.layout.master')

@section('title', 'Approval Cuti')

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
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
    }

    /* Alert Messages */
    .alert-container {
        padding: 15px 20px;
        background: white;
        flex-shrink: 0;
    }

    .alert {
        padding: 12px 15px;
        border-radius: 8px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .alert:last-child {
        margin-bottom: 0;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }

    /* Header */
    .approval-header {
        background: linear-gradient(135deg, #4285f4 0%, #5a98f7 100%);
        color: white;
        padding: 20px;
        flex-shrink: 0;
    }

    .header-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .header-subtitle {
        font-size: 13px;
        opacity: 0.9;
    }

    /* Stats Banner */
    .stats-banner {
        background: white;
        padding: 20px;
        margin: 0 20px;
        margin-top: -30px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 10;
        flex-shrink: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #4285f4 0%, #5a98f7 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        margin: 0 auto 10px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 3px;
    }

    .stat-label {
        font-size: 11px;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
    }

    /* Content Area */
    .approval-content {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 20px;
        padding-bottom: 100px;
    }

    /* Cuti Card */
    .cuti-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 15px;
        border-left: 5px solid #4285f4;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .cuti-header {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e2e8f0;
    }

    .employee-info {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .employee-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #4285f4 0%, #5a98f7 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .employee-details {
        flex: 1;
    }

    .employee-name {
        font-size: 16px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 3px;
    }

    .employee-role {
        font-size: 12px;
        color: #718096;
        font-weight: 600;
    }

    .cuti-type-badge {
        background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
    }

    /* Cuti Details */
    .cuti-details {
        margin-bottom: 15px;
    }

    .detail-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .detail-row:last-child {
        margin-bottom: 0;
    }

    .detail-icon {
        width: 35px;
        height: 35px;
        background: #f7fafc;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4285f4;
        font-size: 16px;
        flex-shrink: 0;
    }

    .detail-content {
        flex: 1;
    }

    .detail-label {
        font-size: 11px;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .detail-value {
        font-size: 14px;
        color: #2d3748;
        font-weight: 600;
    }

    /* Reason Box */
    .reason-box {
        background: #f7fafc;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .reason-box strong {
        font-size: 12px;
        color: #4a5568;
        display: block;
        margin-bottom: 5px;
    }

    .reason-box p {
        font-size: 13px;
        color: #2d3748;
        line-height: 1.5;
        margin: 0;
    }

    /* Progress Section */
    .progress-section {
        background: #f7fafc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .progress-title {
        font-size: 12px;
        font-weight: 700;
        color: #4a5568;
        margin-bottom: 12px;
        text-transform: uppercase;
    }

    .progress-steps {
        display: flex;
        align-items: center;
        position: relative;
    }

    .progress-line {
        position: absolute;
        top: 17px;
        left: 25px;
        right: 25px;
        height: 3px;
        background: #e2e8f0;
        z-index: 0;
    }

    .progress-line-fill {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
        transition: width 0.5s ease;
    }

    .step {
        position: relative;
        z-index: 1;
        text-align: center;
        flex: 1;
    }

    .step-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #a0aec0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 16px;
        transition: all 0.3s;
    }

    .step.pending .step-circle {
        background: #fff3cd;
        color: #856404;
        border: 3px solid #ffc107;
        animation: pulse 2s infinite;
    }

    .step.disetujui .step-circle {
        background: #28a745;
        color: white;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }

    .step.ditolak .step-circle {
        background: #dc3545;
        color: white;
    }

    .step-label {
        font-size: 11px;
        font-weight: 700;
        color: #2d3748;
    }

    .step-time {
        font-size: 9px;
        color: #a0aec0;
        margin-top: 2px;
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(255, 193, 7, 0);
        }
    }

    /* Turn Badge */
    .turn-badge {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #fff;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .turn-badge i {
        font-size: 14px;
    }

    /* Action Buttons */
    .action-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .btn-action {
        padding: 13px 20px;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-approve {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .btn-approve:active {
        transform: scale(0.98);
    }

    .btn-reject {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .btn-reject:active {
        transform: scale(0.98);
    }

    .btn-disabled {
        background: #e2e8f0;
        color: #a0aec0;
        cursor: not-allowed;
        grid-column: 1 / -1;
    }

    .file-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        background: #e8f4fd;
        border-radius: 10px;
        color: #4285f4;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 2000;
        align-items: flex-end;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        padding: 25px 20px;
        border-radius: 20px 20px 0 0;
        width: 100%;
        max-width: 600px;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(100%);
        }
        to {
            transform: translateY(0);
        }
    }

    .modal-header {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 15px;
    }

    .modal-body textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        min-height: 100px;
        font-size: 15px;
        font-family: inherit;
        resize: vertical;
    }

    .modal-body textarea:focus {
        outline: none;
        border-color: #4285f4;
    }

    .modal-footer {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 15px;
    }

    .btn-cancel {
        background: #e2e8f0;
        color: #2d3748;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #a0aec0;
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 15px;
    }

    .empty-state p {
        font-size: 16px;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 480px) {
        .stats-grid {
            gap: 10px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }

        .stat-value {
            font-size: 20px;
        }

        .employee-avatar {
            width: 45px;
            height: 45px;
            font-size: 18px;
        }

        .progress-steps {
            gap: 5px;
        }

        .step-circle {
            width: 30px;
            height: 30px;
            font-size: 14px;
        }

        .step-label {
            font-size: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="fullscreen-wrapper">
    {{-- Alert Messages --}}
    @if(session('success') || session('error'))
    <div class="alert-container">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif
    </div>
    @endif

    {{-- Header --}}
    <div class="approval-header">
        <div class="header-title">Approval Cuti</div>
        <div class="header-subtitle">{{ strtoupper(Auth::user()->role) }}</div>
    </div>

    {{-- Stats Banner --}}
    <div class="stats-banner">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">{{ $cutiCount['pending'] ?? 0 }}</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <i class="fas fa-check"></i>
                </div>
                <div class="stat-value">{{ $approvedToday ?? 0 }}</div>
                <div class="stat-label">Hari Ini</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon" style="background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-value">{{ $totalMonth ?? 0 }}</div>
                <div class="stat-label">Bulan Ini</div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="approval-content">
        @forelse($cuti as $item)
            <div class="cuti-card">
                {{-- Header --}}
                <div class="cuti-header">
                    <div class="employee-info">
                        <div class="employee-avatar">
                            {{ strtoupper(substr($item->karyawan->user->nama, 0, 1)) }}
                        </div>
                        <div class="employee-details">
                            <div class="employee-name">{{ $item->karyawan->user->nama }}</div>
                            <div class="employee-role">{{ strtoupper($item->karyawan->user->role) }} • {{ $item->karyawan->departemen->nama }}</div>
                        </div>
                    </div>
                    <span class="cuti-type-badge">{{ $item->jenisCuti->nama }}</span>
                </div>

                {{-- Your Turn Badge --}}
                @if($item->current_step === Auth::user()->role)
                <div class="turn-badge">
                    <i class="fas fa-bell"></i>
                    <span>Giliran Anda untuk Approve!</span>
                </div>
                @endif

                {{-- Details --}}
                <div class="cuti-details">
                    <div class="detail-row">
                        <div class="detail-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Tanggal Cuti</div>
                            <div class="detail-value">
                                {{ $item->tanggal_mulai->format('d M Y') }} - {{ $item->tanggal_selesai->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Durasi</div>
                            <div class="detail-value">{{ $item->jumlahHari }} Hari</div>
                        </div>
                    </div>

                    <div class="detail-row">
                        <div class="detail-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Diajukan</div>
                            <div class="detail-value">{{ $item->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Reason --}}
                <div class="reason-box">
                    <strong>Alasan:</strong>
                    <p>{{ $item->alasan }}</p>
                </div>

                {{-- File Pendukung --}}
                @if($item->file_pendukung)
                <a href="{{ Storage::url($item->file_pendukung) }}" target="_blank" class="file-link">
                    <i class="fas fa-file-download"></i>
                    <span>Lihat File Pendukung</span>
                </a>
                @endif

                {{-- Progress --}}
                <div class="progress-section">
                    <div class="progress-title">Progress Approval</div>
                    <div class="progress-steps">
                        <div class="progress-line">
                            <div class="progress-line-fill" style="width: {{ $item->progressPercentage }}%;"></div>
                        </div>

                        @foreach($item->approvals as $approval)
                        <div class="step {{ $approval->status }}">
                            <div class="step-circle">
                                @if($approval->status == 'disetujui')
                                    <i class="fas fa-check"></i>
                                @elseif($approval->status == 'ditolak')
                                    <i class="fas fa-times"></i>
                                @else
                                    <i class="fas fa-clock"></i>
                                @endif
                            </div>
                            <div class="step-label">{{ strtoupper($approval->step) }}</div>
                            @if($approval->approved_at)
                                <div class="step-time">{{ $approval->approved_at->format('d/m H:i') }}</div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="action-buttons">
                    @if($item->current_step === Auth::user()->role)
                        <button class="btn-action btn-approve" onclick="showApproveModal({{ $item->id }})">
                            <i class="fas fa-check"></i>
                            Setujui
                        </button>
                        <button class="btn-action btn-reject" onclick="showRejectModal({{ $item->id }})">
                            <i class="fas fa-times"></i>
                            Tolak
                        </button>
                    @else
                        <button class="btn-action btn-disabled" disabled>
                            <i class="fas fa-info-circle"></i>
                            Menunggu {{ strtoupper($item->current_step) }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>Tidak ada cuti yang menunggu approval</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Approve Modal --}}
<div id="approveModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Setujui Cuti</div>
        <form id="approveForm" method="POST">
            @csrf
            <div class="modal-body">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Catatan (Opsional)</label>
                <textarea name="catatan_admin" placeholder="Tambahkan catatan..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action btn-cancel" onclick="closeModal('approveModal')">
                    Batal
                </button>
                <button type="submit" class="btn-action btn-approve">
                    <i class="fas fa-check"></i>
                    Setujui
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Tolak Cuti</div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="modal-body">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">
                    Alasan Penolakan <span style="color: #dc3545;">*</span>
                </label>
                <textarea name="catatan_admin" placeholder="Jelaskan alasan..." required minlength="10"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action btn-cancel" onclick="closeModal('rejectModal')">
                    Batal
                </button>
                <button type="submit" class="btn-action btn-reject">
                    <i class="fas fa-times"></i>
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showApproveModal(id) {
    const form = document.getElementById('approveForm');
    form.action = `{{ url('/') }}/${getRolePrefix()}/cuti/${id}/approve`;
    document.getElementById('approveModal').classList.add('show');
}

function showRejectModal(id) {
    const form = document.getElementById('rejectForm');
    form.action = `{{ url('/') }}/${getRolePrefix()}/cuti/${id}/reject`;
    document.getElementById('rejectModal').classList.add('show');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

function getRolePrefix() {
    const role = '{{ Auth::user()->role }}';
    if (role === 'super_admin') return 'admin';
    return role;
}

// Close modal when clicking outside
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});

// Auto hide alerts
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const alerts = document.querySelector('.alert-container');
        if (alerts) {
            alerts.style.transition = 'opacity 0.3s';
            alerts.style.opacity = '0';
            setTimeout(() => alerts.remove(), 300);
        }
    }, 4000);

    // Touch feedback
    document.querySelectorAll('.btn-action:not(.btn-disabled)').forEach(btn => {
        btn.addEventListener('touchstart', () => {
            btn.style.transform = 'scale(0.98)';
        });
        btn.addEventListener('touchend', () => {
            btn.style.transform = '';
        });
    });
});
</script>
@endpush
