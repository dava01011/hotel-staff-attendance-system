{{-- resources/views/notifikasi/index.blade.php --}}
@extends(is_admin_mode() ? 'admin.layouts.app' : 'karyawan.layout.fullscreen')

@section('title', 'Notifikasi')

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

    .notifikasi-wrapper {
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        bottom: 70px;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 0;
        background: white;
        padding: 0;
        border-bottom: 1px solid #e9ecef;
        flex-shrink: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

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

    .tab-btn.active {
        color: #667eea;
        font-weight: 600;
    }

    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: #667eea;
    }

    /* Action Bar */
    .action-bar {
        background: white;
        padding: 10px 16px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .info-text {
        font-size: 13px;
        color: #6c757d;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap; /* allow wrapping on tiny screens */
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #e9ecef;
        background: white;
        color: #495057;
        transition: all 0.2s;
    }

    .btn-action:hover {
        background: #f8f9fa;
    }

    .btn-action.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .btn-delete {
        color: #dc2626;
        border-color: #fee2e2;
    }

    .btn-delete:hover {
        background: #fee2e2;
    }

    .select-mode-actions {
        display: none;
        gap: 8px;
    }

    .select-mode-actions.show {
        display: flex;
    }

    /* Notifications List */
    .notifications-list {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        background: #ffffff;
    }

    .notification-item {
        border-bottom: 1px solid #f0f0f0;
        display: flex;
    }

    .notification-item.unread {
        background: #e0f2fe;
    }

    .notification-item.select-mode {
        display: flex;
    }

    .notification-checkbox-wrapper {
        display: none;
        align-items: center;
        padding: 16px 0 16px 12px;
    }

    .select-mode .notification-checkbox-wrapper {
        display: flex;
    }

    .checkbox-custom {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #667eea;
    }

    .notification-card {
        flex: 1;
        padding: 14px 16px;
        display: flex;
        gap: 14px;
        align-items: flex-start;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        transition: background 0.2s;
    }

    .notification-card:hover {
        background: #fafafa;
    }

    /* Icon */
    .notification-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }

    /* Content */
    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 6px;
        align-items: flex-start;
    }

    .notification-title {
        font-size: 15px;
        font-weight: 600;
        color: #212529;
        flex: 1;
        word-break: break-word;
    }

    .notification-time {
        font-size: 12px;
        color: #adb5bd;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .notification-message {
        font-size: 14px;
        color: #6c757d;
        line-height: 1.5;
        word-break: break-word;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 36px;
    }

    .empty-state h3 {
        font-size: 16px;
        color: #495057;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .empty-state p {
        font-size: 13px;
        color: #adb5bd;
    }

    /* ===== PAGINATION - COMPACT ===== */
    .pagination-wrapper {
        background: white;
        padding: 6px 10px;
        border-top: 1px solid #e9ecef;
        flex-shrink: 0;
    }

    .pagination-info {
        text-align: center;
        font-size: 10px;
        color: #9ca3af;
        margin-bottom: 4px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 2px;
        margin: 0;
        padding: 0;
        list-style: none;
        flex-wrap: wrap;
        align-items: center;
    }

    .pagination li {
        margin: 0;
    }

    .pagination a,
    .pagination span {
        min-width: 26px;
        height: 26px;
        padding: 0 4px;
        border-radius: 5px;
        font-size: 10px;
        color: #495057;
        text-decoration: none;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: #667eea;
        color: white;
    }

    .pagination .active span {
        background: #667eea;
        color: white;
    }

    .pagination .disabled span {
        opacity: 0.3;
        cursor: not-allowed;
    }

    /* Sembunyikan nomor halaman, hanya tampilkan prev/next */
    .pagination .page-item:not(.prev):not(.next) {
        display: none;
    }

    .pagination .prev,
    .pagination .next {
        display: flex;
    }

    /* Delete Options Modal */
    .modal-overlay {
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

    .modal-overlay.show {
        display: flex;
    }

    .modal-content {
        background: white;
        width: 100%;
        max-width: 500px;
        border-radius: 20px 20px 0 0;
        padding: 0;
        animation: slideUp 0.3s ease-out;
        max-height: 80vh;
        overflow-y: auto;
    }

    @keyframes slideUp {
        from { transform: translateY(100%); }
        to { transform: translateY(0); }
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #212529;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #6c757d;
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .delete-options {
        padding: 8px;
    }

    .delete-option {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: pointer;
        border-radius: 12px;
        transition: all 0.2s;
        border: none;
        width: 100%;
        background: none;
        text-align: left;
    }

    .delete-option:hover {
        background: #f8f9fa;
    }

    .delete-option-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .delete-option-content {
        flex: 1;
    }

    .delete-option-title {
        font-size: 15px;
        font-weight: 600;
        color: #212529;
        margin-bottom: 4px;
    }

    .delete-option-desc {
        font-size: 13px;
        color: #6c757d;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .tab-btn {
            padding: 12px 16px;
            font-size: 14px;
        }

        .action-bar {
            padding: 8px 12px;
        }
    }

    @media (max-width: 480px) {
        .tab-btn {
            padding: 10px 12px;
            font-size: 13px;
        }

        .action-bar {
            padding: 8px 10px;
        }

        .notification-card {
            padding: 12px;
            gap: 12px;
        }

        .notification-checkbox-wrapper {
            padding: 12px 0 12px 10px;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .notification-title {
            font-size: 14px;
        }

        .notification-message {
            font-size: 13px;
        }

        .pagination a,
        .pagination span {
            min-width: 24px;
            height: 24px;
            font-size: 9px;
        }

        .pagination-info {
            font-size: 9px;
            margin-bottom: 3px;
        }
    }
</style>
@endpush

@section('content')
<div class="notifikasi-wrapper">
    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="tab-btn active" data-filter="all">Semua</button>
        <button class="tab-btn" data-filter="absensi">Absensi</button>
        <button class="tab-btn" data-filter="gaji">Gaji</button>
        <button class="tab-btn" data-filter="cuti">Cuti</button>
        <button class="tab-btn" data-filter="sistem">Sistem</button>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="info-text" id="infoText">
            {{ $notifikasi->total() }} notifikasi
        </div>

        <div class="action-buttons">
            <button class="btn-action" id="btnSelectMode" onclick="toggleSelectMode()">
                <i class="fas fa-check-square"></i>
                <span>Pilih</span>
            </button>

            <button class="btn-action" onclick="showDeleteOptions()">
                <i class="fas fa-ellipsis-v"></i>
            </button>

            <div class="select-mode-actions" id="selectActions">
                <button class="btn-action" onclick="markSelectedAsRead()">
                    <i class="fas fa-check"></i>
                    <span>Dibaca</span>
                </button>
                <button class="btn-action btn-delete" onclick="deleteSelected()">
                    <i class="fas fa-trash"></i>
                    <span id="deleteCount">Hapus</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list">
        @forelse($notifikasi as $n)
        @php
            $iconClass = 'fa-bell';
            $iconBg = '#6c757d';

            switch($n->type) {
                case 'cuti':
                    $iconClass = 'fa-calendar-alt';
                    $iconBg = '#667eea';
                    break;
                case 'libur_pengganti':
                    $iconClass = 'fa-leaf';
                    $iconBg = '#11998e';
                    break;
                case 'absensi':
                    $iconClass = 'fa-clipboard-check';
                    $iconBg = '#06b6d4';
                    break;
                case 'gaji':
                    $iconClass = 'fa-money-bill-wave';
                    $iconBg = '#10b981';
                    break;
                case 'sistem':
                    $iconClass = 'fa-cog';
                    $iconBg = '#8b5cf6';
                    break;
                case 'shift':
                    $iconClass = 'fa-clock';
                    $iconBg = '#f59e0b';
                    break;
            }
        @endphp

        <div class="notification-item {{ !$n->is_read ? 'unread' : '' }}" data-id="{{ $n->id }}" data-type="{{ $n->type }}">
            <div class="notification-checkbox-wrapper">
                <input type="checkbox" class="checkbox-custom notif-checkbox" value="{{ $n->id }}" onchange="updateSelection()">
            </div>

            <a href="#" class="notification-card" onclick="markAsRead(event, {{ $n->id }})">
                <div class="notification-icon" style="background: {{ $iconBg }};">
                    <i class="fas {{ $iconClass }}"></i>
                </div>

                <div class="notification-content">
                    <div class="notification-header">
                        <h3 class="notification-title">{{ $n->judul }}</h3>
                        <span class="notification-time">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="notification-message">{{ $n->pesan }}</p>
                </div>
            </a>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h3>Tidak ada notifikasi</h3>
            <p>Semua notifikasi akan muncul di sini</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination - COMPACT -->
    @if($notifikasi->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            Hlm {{ $notifikasi->currentPage() }} / {{ $notifikasi->lastPage() }} ({{ $notifikasi->total() }} total)
        </div>
        {!! $notifikasi->links('pagination::bootstrap-4') !!}
    </div>
    @endif
</div>

<!-- Delete Options Modal -->
<div class="modal-overlay" id="deleteModal" onclick="closeModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3>Opsi Hapus Notifikasi</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="delete-options">
            <button class="delete-option" onclick="deleteByTimeRange('today')">
                <div class="delete-option-icon" style="background: #fef3c7; color: #f59e0b;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="delete-option-content">
                    <div class="delete-option-title">Hapus Hari Ini</div>
                    <div class="delete-option-desc">Notifikasi masuk hari ini akan dihapus</div>
                </div>
            </button>

            <button class="delete-option" onclick="deleteByTimeRange('week')">
                <div class="delete-option-icon" style="background: #dbeafe; color: #3b82f6;">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="delete-option-content">
                    <div class="delete-option-title">Hapus Minggu Ini</div>
                    <div class="delete-option-desc">Notifikasi 7 hari terakhir akan dihapus</div>
                </div>
            </button>

            <button class="delete-option" onclick="deleteByTimeRange('month')">
                <div class="delete-option-icon" style="background: #e0e7ff; color: #6366f1;">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="delete-option-content">
                    <div class="delete-option-title">Hapus Bulan Ini</div>
                    <div class="delete-option-desc">Notifikasi 30 hari terakhir akan dihapus</div>
                </div>
            </button>

            <button class="delete-option" onclick="deleteRead()">
                <div class="delete-option-icon" style="background: #d1fae5; color: #10b981;">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="delete-option-content">
                    <div class="delete-option-title">Hapus yang Sudah Dibaca</div>
                    <div class="delete-option-desc">Semua notifikasi yang sudah dibaca akan dihapus</div>
                </div>
            </button>

            <button class="delete-option" onclick="deleteAll()">
                <div class="delete-option-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <div class="delete-option-content">
                    <div class="delete-option-title">Hapus Semua</div>
                    <div class="delete-option-desc">Seluruh notifikasi akan dihapus (tidak dapat dibatalkan)</div>
                </div>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectModeActive = false;

// Tab filtering
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        const notifications = document.querySelectorAll('.notification-item');

        notifications.forEach(notif => {
            if (filter === 'all' || notif.dataset.type === filter) {
                notif.style.display = 'flex';
            } else {
                notif.style.display = 'none';
            }
        });
    });
});

// Toggle Select Mode
function toggleSelectMode() {
    selectModeActive = !selectModeActive;
    const notifications = document.querySelectorAll('.notification-item');
    const infoText = document.getElementById('infoText');
    const selectActions = document.getElementById('selectActions');
    const btnSelectMode = document.getElementById('btnSelectMode');

    if (selectModeActive) {
        notifications.forEach(n => n.classList.add('select-mode'));
        infoText.style.display = 'none';
        selectActions.classList.add('show');
        btnSelectMode.classList.add('active');
    } else {
        notifications.forEach(n => n.classList.remove('select-mode'));
        document.querySelectorAll('.notif-checkbox').forEach(cb => cb.checked = false);
        infoText.style.display = 'block';
        selectActions.classList.remove('show');
        btnSelectMode.classList.remove('active');
        updateSelection();
    }
}

// Update Selection
function updateSelection() {
    const checked = document.querySelectorAll('.notif-checkbox:checked');
    const count = checked.length;
    const deleteCount = document.getElementById('deleteCount');

    if (count > 0) {
        deleteCount.textContent = `Hapus (${count})`;
    } else {
        deleteCount.textContent = 'Hapus';
    }
}

// Mark as Read
function markAsRead(event, id) {
    event.preventDefault();

    if (selectModeActive) return;

    fetch(`/notifikasi/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`.notification-item[data-id="${id}"]`);
            if (item) item.classList.remove('unread');

            const badge = document.querySelector('.notification-badge');
            if (badge) {
                let currentCount = parseInt(badge.textContent);
                if (currentCount > 1) {
                    badge.textContent = currentCount - 1;
                } else {
                    badge.remove();
                }
            }
        }
    });
}

// Mark Selected as Read
function markSelectedAsRead() {
    const checked = Array.from(document.querySelectorAll('.notif-checkbox:checked')).map(cb => cb.value);

    if (checked.length === 0) {
        showToast('Pilih notifikasi terlebih dahulu', 'warning');
        return;
    }

    fetch('/notifikasi/mark-read-bulk', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ ids: checked })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`${checked.length} notifikasi ditandai sudah dibaca`, 'success');
            checked.forEach(id => {
                const item = document.querySelector(`.notification-item[data-id="${id}"]`);
                if (item) item.classList.remove('unread');
            });
            document.querySelectorAll('.notif-checkbox').forEach(cb => cb.checked = false);
            updateSelection();
            toggleSelectMode();
            setTimeout(() => location.reload(), 1000);
        }
    });
}

// Delete Selected
function deleteSelected() {
    const checked = Array.from(document.querySelectorAll('.notif-checkbox:checked')).map(cb => cb.value);

    if (checked.length === 0) {
        showToast('Pilih notifikasi terlebih dahulu', 'warning');
        return;
    }

    showAlert(
        'danger',
        'Hapus Notifikasi Terpilih',
        `${checked.length} notifikasi yang dipilih akan dihapus permanen`,
        function() {
            const confirmBtn = document.getElementById('confirmBtn');
            confirmBtn.classList.add('loading');

            fetch('/notifikasi/delete-bulk', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: checked })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(`${checked.length} notifikasi berhasil dihapus`, 'success');
                    closeAlert();
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    );
}

// Show/Close Modal
function showDeleteOptions() {
    document.getElementById('deleteModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal(event) {
    if (!event || event.target === event.currentTarget) {
        document.getElementById('deleteModal').classList.remove('show');
        document.body.style.overflow = '';
    }
}

// Delete by Time Range
function deleteByTimeRange(range) {
    const titles = {
        'today': 'Hapus Hari Ini',
        'week': 'Hapus Minggu Ini',
        'month': 'Hapus Bulan Ini'
    };

    const messages = {
        'today': 'Notifikasi hari ini akan dihapus permanen',
        'week': 'Notifikasi 7 hari terakhir akan dihapus permanen',
        'month': 'Notifikasi 30 hari terakhir akan dihapus permanen'
    };

    showAlert(
        'danger',
        titles[range],
        messages[range],
        function() {
            fetch('/notifikasi/delete-by-time', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ range })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Notifikasi berhasil dihapus', 'success');
                    closeAlert();
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    );
}

// Delete Read
function deleteRead() {
    showAlert(
        'danger',
        'Hapus Notifikasi',
        'Semua notifikasi yang sudah dibaca akan dihapus permanen',
        function() {
            fetch('/notifikasi/delete-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Notifikasi berhasil dihapus', 'success');
                    closeAlert();
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    );
}

// Delete All
function deleteAll() {
    showAlert(
        'danger',
        'Hapus Semua Notifikasi',
        'SEMUA notifikasi akan dihapus permanen. Tindakan ini tidak dapat dibatalkan!',
        function() {
            fetch('/notifikasi/delete-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Semua notifikasi berhasil dihapus', 'success');
                    closeAlert();
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    );
}
</script>
@endpush