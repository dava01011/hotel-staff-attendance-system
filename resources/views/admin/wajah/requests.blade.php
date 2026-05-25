{{-- resources/views/admin/wajah/requests.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Permohonan Ganti Wajah')
@section('page-title', 'Permohonan Ganti Wajah')
@section('page-subtitle', 'Review permohonan update template wajah karyawan')

@push('styles')
<style>
    .page-wrapper {
        padding: 20px;
        max-width: 900px;
        margin: 0 auto;
    }

    /* ── Stats row ── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 16px 18px;
        border: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: white; flex-shrink: 0;
    }

    .stat-icon.pending   { background: linear-gradient(135deg,#f59e0b,#d97706); }
    .stat-icon.approved  { background: linear-gradient(135deg,#10b981,#059669); }
    .stat-icon.rejected  { background: linear-gradient(135deg,#ef4444,#dc2626); }

    .stat-num  { font-size: 22px; font-weight: 700; color: #1e293b; }
    .stat-lbl  { font-size: 12px; color: #64748b; font-weight: 500; }

    /* ── Filter pills ── */
    .filter-bar {
        display: flex; gap: 8px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .filter-pill {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px; font-weight: 600;
        border: 2px solid #e2e8f0;
        background: white; color: #718096;
        cursor: pointer; transition: all .2s;
    }

    .filter-pill.active,
    .filter-pill:hover { border-color: #1d4ed8; color: #1d4ed8; background: #eff6ff; }
    .filter-pill.pending.active   { border-color:#f59e0b;color:#92400e;background:#fef3c7; }
    .filter-pill.disetujui.active { border-color:#10b981;color:#065f46;background:#d1fae5; }
    .filter-pill.ditolak.active   { border-color:#ef4444;color:#991b1b;background:#fee2e2; }

    /* ── Request card ── */
    .request-card {
        background: white;
        border-radius: 14px;
        border: 1px solid #e9ecef;
        margin-bottom: 12px;
        overflow: hidden;
        transition: box-shadow .2s;
    }

    .request-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }

    .request-card.pending   { border-left: 4px solid #f59e0b; }
    .request-card.disetujui { border-left: 4px solid #10b981; }
    .request-card.ditolak   { border-left: 4px solid #ef4444; }

    .card-body {
        padding: 16px 18px;
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .avatar {
        width: 44px; height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg,#1d4ed8,#4a5db8);
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 700;
        flex-shrink: 0;
    }

    .card-info { flex: 1; min-width: 0; }

    .card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 8px;
    }

    .karyawan-name {
        font-size: 15px; font-weight: 700; color: #1e293b;
        margin-bottom: 2px;
    }

    .karyawan-dept {
        font-size: 12px; color: #64748b;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .4px;
        flex-shrink: 0;
    }

    .status-badge.pending   { background: #fef3c7; color: #92400e; }
    .status-badge.disetujui { background: #d1fae5; color: #065f46; }
    .status-badge.ditolak   { background: #fee2e2; color: #991b1b; }

    .alasan-text {
        font-size: 13px; color: #475569;
        background: #f8fafc;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 8px;
        line-height: 1.5;
    }

    .meta-row {
        display: flex; align-items: center; gap: 12px;
        font-size: 11px; color: #94a3b8; flex-wrap: wrap;
    }

    .meta-row i { width: 12px; }

    /* ── Capture pending notice ── */
    .capture-notice {
        background: #eff6ff;
        border-top: 1px solid #bfdbfe;
        padding: 10px 18px;
        font-size: 12px; color: #1d4ed8;
        display: flex; align-items: center; gap: 8px;
    }

    /* ── Rejection note ── */
    .rejection-note {
        background: #fef2f2;
        border-top: 1px solid #fecaca;
        padding: 10px 18px;
        font-size: 12px; color: #991b1b;
        display: flex; align-items: flex-start; gap: 8px;
    }

    /* ── Actions ── */
    .card-actions {
        border-top: 1px solid #f0f0f0;
        background: #fafafa;
        padding: 10px 18px;
        display: flex; gap: 8px;
    }

    .btn-action {
        flex: 1; padding: 8px 10px;
        border: none; border-radius: 8px;
        font-size: 12px; font-weight: 600;
        cursor: pointer; transition: all .2s;
        display: flex; align-items: center; justify-content: center; gap: 5px;
    }

    .btn-approve {
        background: linear-gradient(135deg,#10b981,#059669);
        color: white;
    }
    .btn-approve:hover { box-shadow: 0 4px 10px rgba(16,185,129,.3); }

    .btn-reject {
        background: linear-gradient(135deg,#ef4444,#dc2626);
        color: white;
    }
    .btn-reject:hover { box-shadow: 0 4px 10px rgba(239,68,68,.3); }

    /* ── Empty state ── */
    .empty-state {
        text-align: center; padding: 60px 20px;
        color: #94a3b8;
    }

    .empty-state i { font-size: 48px; margin-bottom: 12px; display: block; }
    .empty-state p { font-size: 14px; font-weight: 500; }

    /* ── Reject modal ── */
    .modal-overlay {
        display: none;
        position: fixed; top:0; left:0; right:0; bottom:0;
        background: rgba(0,0,0,.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center; justify-content: center;
    }

    .modal-overlay.show { display: flex; animation: fadeIn .25s forwards; }
    .modal-overlay.hide { display: flex; animation: fadeOut .25s forwards; }

    @keyframes fadeIn  { from{opacity:0} to{opacity:1} }
    @keyframes fadeOut { from{opacity:1} to{opacity:0} }

    .modal-box {
        background: white; width: 420px; max-width: 92%;
        border-radius: 20px; overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,.2);
        opacity: 0; transform: scale(.9) translateY(16px);
    }

    .modal-overlay.show .modal-box {
        animation: popIn .35s cubic-bezier(.34,1.56,.64,1) forwards;
    }

    .modal-overlay.hide .modal-box {
        animation: popOut .25s ease forwards;
    }

    @keyframes popIn  { 0%{opacity:0;transform:scale(.85) translateY(20px)} 100%{opacity:1;transform:scale(1) translateY(0)} }
    @keyframes popOut { 0%{opacity:1;transform:scale(1) translateY(0)} 100%{opacity:0;transform:scale(.85) translateY(20px)} }

    .modal-head {
        background: linear-gradient(135deg,#fee2e2,#fecaca);
        padding: 20px 20px 14px;
        text-align: center;
        position: relative; overflow: hidden;
    }

    .modal-head::before {
        content:''; position:absolute; top:-20px; right:-20px;
        width:100px; height:100px; background:#fca5a5; border-radius:50%; opacity:.3;
    }

    .modal-head-icon {
        width:50px; height:50px; border-radius:50%; background:#dc2626; color:white;
        display:flex; align-items:center; justify-content:center; font-size:22px;
        margin: 0 auto 10px; position:relative; z-index:1;
    }

    .modal-head h3 { font-size:16px; font-weight:700; color:#b91c1c; margin:0; position:relative; z-index:1; }

    .modal-body { padding: 16px 20px; }
    .modal-body label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }

    .modal-body textarea {
        width:100%; padding:10px 12px; border:2px solid #fee2e2;
        border-radius:10px; font-family:inherit; font-size:13px;
        resize:vertical; background:#fef2f2; box-sizing:border-box; transition: border-color .2s;
    }

    .modal-body textarea:focus { outline:none; border-color:#dc2626; background:white; box-shadow:0 0 0 3px rgba(220,38,38,.1); }
    .modal-body small { display:block; font-size:11px; color:#9ca3af; margin-top:4px; }

    .modal-foot {
        padding: 12px 20px 18px;
        display: flex; gap: 10px;
        background: #f8fafc; border-top: 1px solid #f0f0f0;
    }

    .modal-btn {
        flex: 1; padding: 10px; border: none; border-radius: 30px;
        font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s;
        display: flex; align-items: center; justify-content: center; gap: 6px;
    }

    .modal-btn.cancel { background:#e5e7eb; color:#374151; }
    .modal-btn.cancel:hover { background:#d1d5db; }
    .modal-btn.submit { background:linear-gradient(135deg,#ef4444,#dc2626); color:white; }
    .modal-btn.submit:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(220,38,38,.3); }

    @media (max-width: 640px) {
        .stats-row { grid-template-columns: 1fr 1fr; }
        .page-wrapper { padding: 14px; }
    }
</style>
@endpush

@section('content')
<div class="page-wrapper">

    {{-- ── Stats ── --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon pending"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <div class="stat-num">{{ $requests->where('status','pending')->count() }}</div>
                <div class="stat-lbl">Menunggu</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon approved"><i class="fas fa-check"></i></div>
            <div>
                <div class="stat-num">{{ $requests->where('status','disetujui')->count() }}</div>
                <div class="stat-lbl">Disetujui</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rejected"><i class="fas fa-times"></i></div>
            <div>
                <div class="stat-num">{{ $requests->where('status','ditolak')->count() }}</div>
                <div class="stat-lbl">Ditolak</div>
            </div>
        </div>
    </div>

    {{-- ── Filter pills ── --}}
    <div class="filter-bar">
        <button class="filter-pill active" data-status="semua">Semua</button>
        <button class="filter-pill pending"   data-status="pending">Menunggu</button>
        <button class="filter-pill disetujui" data-status="disetujui">Disetujui</button>
        <button class="filter-pill ditolak"   data-status="ditolak">Ditolak</button>
    </div>

    {{-- ── Cards ── --}}
    @forelse($requests as $item)
    <div class="request-card {{ $item->status }}" data-status="{{ $item->status }}">
        <div class="card-body">
            <div class="avatar">{{ strtoupper(substr($item->karyawan->user->nama, 0, 1)) }}</div>
            <div class="card-info">
                <div class="card-top">
                    <div>
                        <div class="karyawan-name">{{ $item->karyawan->user->nama }}</div>
                        <div class="karyawan-dept">
                            {{ $item->karyawan->departemen->nama ?? '-' }}
                            &nbsp;·&nbsp;
                            {{ $item->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                    <span class="status-badge {{ $item->status }}">
                        @if($item->status === 'pending') Menunggu
                        @elseif($item->status === 'disetujui') Disetujui
                        @else Ditolak @endif
                    </span>
                </div>

                <div class="alasan-text">
                    <strong style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:3px;">Alasan:</strong>
                    {{ $item->alasan }}
                </div>

                <div class="meta-row">
                    <span><i class="far fa-clock"></i> {{ $item->created_at->diffForHumans() }}</span>
                    @if($item->reviewer)
                    <span><i class="fas fa-user-check"></i> {{ $item->reviewer->nama }}</span>
                    @endif
                    @if($item->status === 'disetujui' && $item->captured_at)
                    <span><i class="fas fa-camera"></i> Captured {{ $item->captured_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Capture pending notice --}}
        @if($item->status === 'disetujui' && !$item->captured_at)
        <div class="capture-notice">
            <i class="fas fa-info-circle"></i>
            Menunggu karyawan melakukan capture wajah baru di halaman Settings mereka.
        </div>
        @endif

        {{-- Rejection note --}}
        @if($item->status === 'ditolak' && $item->catatan_admin)
        <div class="rejection-note">
            <i class="fas fa-times-circle" style="margin-top:1px;flex-shrink:0;"></i>
            <span><strong>Alasan penolakan:</strong> {{ $item->catatan_admin }}</span>
        </div>
        @endif

        {{-- Action buttons (pending only) --}}
        @if($item->status === 'pending')
        <div class="card-actions">
            <button class="btn-action btn-approve"
                    onclick="confirmApprove({{ $item->id }}, '{{ addslashes($item->karyawan->user->nama) }}')">
                <i class="fas fa-check"></i> Setujui
            </button>
            <button class="btn-action btn-reject"
                    onclick="showRejectModal({{ $item->id }})">
                <i class="fas fa-times"></i> Tolak
            </button>
        </div>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="far fa-user-circle"></i>
        <p>Belum ada permohonan ganti wajah</p>
    </div>
    @endforelse

    @if($requests->hasPages())
    <div style="margin-top:16px;">{{ $requests->links() }}</div>
    @endif

</div>

{{-- ── Reject Modal ── --}}
<div id="rejectModal" class="modal-overlay" onclick="if(event.target===this) closeRejectModal()">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-head">
            <div class="modal-head-icon"><i class="fas fa-times"></i></div>
            <h3>Tolak Permohonan Wajah</h3>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="modal-body">
                <label>Alasan Penolakan <span style="color:#ef4444;">*</span></label>
                <textarea name="catatan_admin"
                          placeholder="Jelaskan alasan penolakan..."
                          required minlength="5" rows="4"></textarea>
                <small><i class="fas fa-info-circle"></i> Minimal 5 karakter</small>
            </div>
            <div class="modal-foot">
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
// Filter pills
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');

        const status = this.dataset.status;
        document.querySelectorAll('.request-card').forEach(card => {
            card.style.display = (status === 'semua' || card.dataset.status === status) ? '' : 'none';
        });
    });
});

// Approve
function confirmApprove(id, nama) {
    showAlert('warning', 'Setujui Permohonan?',
        `Setujui permohonan ganti wajah dari <strong>${nama}</strong>? Karyawan akan bisa capture wajah baru setelah ini.`,
        function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/wajah/requests/${id}/approve`;
            form.innerHTML = `@csrf`;
            document.body.appendChild(form);
            form.submit();
        }
    );
}

// Reject modal
function showRejectModal(id) {
    const modal = document.getElementById('rejectModal');
    const form  = document.getElementById('rejectForm');
    form.action = `/admin/wajah/requests/${id}/reject`;
    modal.classList.remove('hide');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    setTimeout(() => form.querySelector('textarea').focus(), 300);
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

document.addEventListener('keydown', e => { if(e.key==='Escape') closeRejectModal(); });

@if(session('alert'))
    const a = {!! json_encode(session('alert')) !!};
    setTimeout(() => showAlert(a.type, a.title, a.message), 400);
@elseif(session('success'))
    setTimeout(() => showAlert('success', 'Berhasil', '{{ session("success") }}'), 400);
@elseif(session('error'))
    setTimeout(() => showAlert('error', 'Gagal', '{{ session("error") }}'), 400);
@endif
</script>
@endpush
