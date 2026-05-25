@extends('admin.layout.master')

@section('title', 'Approval Cuti')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .approval-container {
        padding: 25px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .page-subtitle {
        font-size: 14px;
        color: #718096;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        border-left: 5px solid #4285f4;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-card.pending {
        border-left-color: #ffc107;
    }

    .stat-card.approved {
        border-left-color: #28a745;
    }

    .stat-card.rejected {
        border-left-color: #dc3545;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .stat-card.pending .stat-icon {
        background: #fff3cd;
        color: #856404;
    }

    .stat-card.approved .stat-icon {
        background: #d4edda;
        color: #155724;
    }

    .stat-card.rejected .stat-icon {
        background: #f8d7da;
        color: #721c24;
    }

    .stat-label {
        font-size: 13px;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #2d3748;
    }

    /* Cuti Card */
    .cuti-list {
        display: grid;
        gap: 20px;
    }

    .cuti-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        border-left: 5px solid #FF6B35;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .cuti-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .cuti-info {
        flex: 1;
    }

    .employee-name {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .employee-role {
        font-size: 13px;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
    }

    .cuti-type {
        background: linear-gradient(135deg, #4285f4 0%, #5a98f7 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        display: inline-block;
    }

    .cuti-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .detail-icon {
        width: 40px;
        height: 40px;
        background: #f7fafc;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FF6B35;
        font-size: 18px;
        flex-shrink: 0;
    }

    .detail-content {
        flex: 1;
    }

    .detail-label {
        font-size: 12px;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 3px;
    }

    .detail-value {
        font-size: 15px;
        color: #2d3748;
        font-weight: 600;
    }

    .reason-box {
        background: #f7fafc;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .reason-box strong {
        font-size: 13px;
        color: #4a5568;
        display: block;
        margin-bottom: 8px;
    }

    .reason-box p {
        font-size: 14px;
        color: #2d3748;
        line-height: 1.6;
        margin: 0;
    }

    /* Approval Progress */
    .approval-progress {
        background: #f7fafc;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .progress-title {
        font-size: 14px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 15px;
    }

    .progress-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
    }

    .progress-line {
        position: absolute;
        top: 20px;
        left: 40px;
        right: 40px;
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
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #a0aec0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 18px;
        font-weight: 700;
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
        font-size: 12px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 3px;
    }

    .step-date {
        font-size: 11px;
        color: #718096;
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
        }
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-approve {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    }

    .btn-reject {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    }

    .btn-disabled {
        background: #e2e8f0;
        color: #a0aec0;
        cursor: not-allowed;
        box-shadow: none;
    }

    .btn-disabled:hover {
        transform: none;
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
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 15px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        font-size: 20px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
    }

    .modal-body textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        min-height: 120px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
    }

    .modal-body textarea:focus {
        outline: none;
        border-color: #4285f4;
    }

    .modal-footer {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #a0aec0;
    }

    .empty-state i {
        font-size: 80px;
        margin-bottom: 20px;
    }

    .empty-state p {
        font-size: 18px;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .approval-container {
            padding: 15px;
        }

        .cuti-header {
            flex-direction: column;
        }

        .progress-steps {
            flex-direction: column;
            align-items: flex-start;
        }

        .progress-line {
            left: 20px;
            right: auto;
            top: 40px;
            bottom: 40px;
            width: 3px;
            height: auto;
        }

        .step {
            display: flex;
            align-items: center;
            text-align: left;
            margin-bottom: 20px;
        }

        .step-circle {
            margin: 0 15px 0 0;
        }
    }
</style>
@endpush

@section('content')
<div class="approval-container">
    {{-- Page Header --}}
    <div class="page-header">
        <h1 class="page-title">Approval Cuti - {{ strtoupper(Auth::user()->role) }}</h1>
        <p class="page-subtitle">Kelola pengajuan cuti yang menunggu persetujuan Anda</p>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-label">Menunggu Approval</div>
            <div class="stat-value">{{ $cutiCount['pending'] ?? 0 }}</div>
        </div>
    </div>

    {{-- Cuti List --}}
    <div class="cuti-list">
        @forelse($cuti as $item)
            <div class="cuti-card">
                <div class="cuti-header">
                    <div class="cuti-info">
                        <div class="employee-name">{{ $item->karyawan->user->nama }}</div>
                        <div class="employee-role">{{ strtoupper($item->karyawan->user->role) }} - {{ $item->karyawan->departemen->nama }}</div>
                    </div>
                    <div class="cuti-type">
                        {{ $item->jenisCuti->nama }}
                    </div>
                </div>

                <div class="cuti-details">
                    <div class="detail-item">
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

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Durasi</div>
                            <div class="detail-value">{{ $item->jumlahHari }} Hari</div>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="detail-content">
                            <div class="detail-label">Diajukan</div>
                            <div class="detail-value">{{ $item->created_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="reason-box">
                    <strong>Alasan Cuti:</strong>
                    <p>{{ $item->alasan }}</p>
                </div>

                @if($item->file_pendukung)
                <div style="margin-bottom: 20px;">
                    <a href="{{ Storage::url($item->file_pendukung) }}" target="_blank" class="btn" style="background: #4285f4; color: white;">
                        <i class="fas fa-file-download"></i>
                        Lihat File Pendukung
                    </a>
                </div>
                @endif

                {{-- Approval Progress --}}
                <div class="approval-progress">
                    <div class="progress-title">Progress Persetujuan</div>
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
                            <div class="step-label">{{ $approval->stepName }}</div>
                            @if($approval->approved_at)
                                <div class="step-date">
                                    {{ $approval->approved_at->format('d/m H:i') }}
                                    @if($approval->approver)
                                        <br>{{ $approval->approver->nama }}
                                    @endif
                                </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="action-buttons">
                    @if($item->current_step === Auth::user()->role)
                        <button class="btn btn-approve" onclick="showApproveModal({{ $item->id }})">
                            <i class="fas fa-check"></i>
                            Setujui
                        </button>
                        <button class="btn btn-reject" onclick="showRejectModal({{ $item->id }})">
                            <i class="fas fa-times"></i>
                            Tolak
                        </button>
                    @else
                        <button class="btn btn-disabled" disabled>
                            <i class="fas fa-info-circle"></i>
                            Menunggu {{ strtoupper($item->current_step) }}
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>Tidak ada pengajuan cuti yang menunggu approval</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Approve Modal --}}
<div id="approveModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Setujui Pengajuan Cuti</div>
        <form id="approveForm" method="POST">
            @csrf
            <div class="modal-body">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2d3748;">Catatan (Opsional)</label>
                <textarea name="catatan_admin" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" style="background: #e2e8f0; color: #2d3748;" onclick="closeModal('approveModal')">
                    Batal
                </button>
                <button type="submit" class="btn btn-approve">
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
        <div class="modal-header">Tolak Pengajuan Cuti</div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="modal-body">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2d3748;">
                    Alasan Penolakan <span style="color: #dc3545;">*</span>
                </label>
                <textarea name="catatan_admin" placeholder="Jelaskan alasan penolakan..." required minlength="10"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" style="background: #e2e8f0; color: #2d3748;" onclick="closeModal('rejectModal')">
                    Batal
                </button>
                <button type="submit" class="btn btn-reject">
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
    form.action = `/admin/cuti/${id}/approve`;
    document.getElementById('approveModal').classList.add('show');
}

function showRejectModal(id) {
    const form = document.getElementById('rejectForm');
    form.action = `/admin/cuti/${id}/reject`;
    document.getElementById('rejectModal').classList.add('show');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

// Close modal when clicking outside
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id);
        }
    });
});
</script>
@endpush
