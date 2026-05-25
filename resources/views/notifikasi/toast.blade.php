{{-- resources/views/components/alert-toast.blade.php --}}

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* ALERT MODAL */
    .alert-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .alert-modal.show {
        display: block;
    }

    /* Animasi overlay */
    .alert-overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        animation: fadeIn 0.3s ease forwards;
    }

    .alert-modal.hide .alert-overlay {
        animation: fadeOut 0.3s ease forwards;
    }

    /* Animasi panel */
    .alert-panel {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        width: 400px;
        max-width: 90%;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        animation: slideInUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .alert-modal.hide .alert-panel {
        animation: slideOutDown 0.3s ease forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translate(-50%, -30%) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    }

    @keyframes slideOutDown {
        from {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        to {
            opacity: 0;
            transform: translate(-50%, 10%) scale(0.9);
        }
    }

    /* Header dengan warna */
    .alert-header {
        padding: 30px 30px 15px;
        text-align: center;
    }

    .alert-header.danger { background: linear-gradient(135deg, #fee2e2, #ffd5d5); }
    .alert-header.warning { background: linear-gradient(135deg, #fef3c7, #fde68a); }
    .alert-header.success { background: linear-gradient(135deg, #d1fae5, #a7f3d0); }
    .alert-header.info { background: linear-gradient(135deg, #dbeafe, #bfdbfe); }
    .alert-header.error { background: linear-gradient(135deg, #fecaca, #fca5a5); }

    /* Icon */
    .alert-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 28px;
        color: white;
        animation: pulse 2s ease-in-out infinite;
    }

    .alert-icon.danger { background: #dc2626; }
    .alert-icon.warning { background: #f59e0b; }
    .alert-icon.success { background: #10b981; }
    .alert-icon.info { background: #3b82f6; }
    .alert-icon.error { background: #6b7280; }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .alert-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .alert-message {
        color: #475569;
        font-size: 1rem;
        line-height: 1.6;
        padding: 0 20px 10px;
    }

    /* Footer */
    .alert-footer {
        padding: 20px 30px 30px;
        display: flex;
        gap: 12px;
        justify-content: center;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .alert-footer.success-layout {
        padding: 20px 30px 30px;
    }

    .alert-footer.success-layout .alert-btn {
        width: 100%;
        max-width: 200px;
    }

    .alert-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 40px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        min-width: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .alert-btn.cancel {
        background: #e2e8f0;
        color: #475569;
    }

    .alert-btn.cancel:hover {
        background: #cbd5e1;
        color: #1e293b;
    }

    .alert-btn.confirm {
        color: white;
    }

    .alert-btn.confirm.danger { background: #dc2626; }
    .alert-btn.confirm.danger:hover { background: #b91c1c; transform: translateY(-2px); }

    .alert-btn.confirm.warning { background: #f59e0b; }
    .alert-btn.confirm.warning:hover { background: #d97706; transform: translateY(-2px); }

    .alert-btn.confirm.success { background: #10b981; }
    .alert-btn.confirm.success:hover { background: #059669; transform: translateY(-2px); }

    .alert-btn.confirm.info { background: #3b82f6; }
    .alert-btn.confirm.info:hover { background: #2563eb; transform: translateY(-2px); }

    .alert-btn.confirm.error { background: #6b7280; }
    .alert-btn.confirm.error:hover { background: #4b5563; transform: translateY(-2px); }

    .alert-btn.confirm:active { transform: translateY(0); }

    .alert-btn.success-single {
        background: #10b981;
        color: white;
        width: 100%;
        max-width: 200px;
        margin: 0 auto;
    }

    .alert-btn.success-single:hover {
        background: #059669;
        transform: translateY(-2px);
    }

    /* Loading state */
    .alert-btn.confirm.loading {
        pointer-events: none;
        position: relative;
        color: transparent;
    }

    .alert-btn.confirm.loading::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        border: 2px solid white;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Error message di dalam alert */
    .alert-error-message {
        background: #fee2e2;
        border: 1px solid #fecaca;
        border-radius: 12px;
        padding: 12px 16px;
        margin: 0 30px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #991b1b;
        font-size: 0.9rem;
        animation: shake 0.5s ease;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .alert-error-message i {
        font-size: 1.2rem;
    }

    /* Responsive Mobile */
    @media (max-width: 640px) {
        .alert-panel {
            width: 90%;
        }

        .alert-header {
            padding: 20px 20px 10px;
        }

        .alert-icon {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }

        .alert-title {
            font-size: 1.3rem;
        }

        .alert-message {
            font-size: 0.9rem;
            padding: 0 15px 10px;
        }

        .alert-footer {
            padding: 15px 20px 20px;
            flex-direction: column;
        }

        .alert-footer.success-layout .alert-btn {
            max-width: 100%;
        }

        .alert-btn {
            width: 100%;
        }

        .alert-error-message {
            margin: 0 20px 15px;
            font-size: 0.85rem;
        }
    }

    /* Toast Style */
    .toast-container {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 10000;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 350px;
    }

    .toast {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        animation: slideInRight 0.3s ease forwards;
        border-left: 4px solid;
        margin-bottom: 10px;
    }

    .toast.hide {
        animation: slideOutRight 0.3s ease forwards;
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    .toast.success { border-left-color: #10b981; }
    .toast.error { border-left-color: #dc2626; }
    .toast.warning { border-left-color: #f59e0b; }
    .toast.info { border-left-color: #3b82f6; }

    .toast-content {
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toast-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        flex-shrink: 0;
    }

    .toast-icon.success { background: #10b981; }
    .toast-icon.error { background: #dc2626; }
    .toast-icon.warning { background: #f59e0b; }
    .toast-icon.info { background: #3b82f6; }

    .toast-message {
        flex: 1;
        color: #1e293b;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .toast-close {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 18px;
        padding: 0 5px;
    }

    .toast-close:hover {
        color: #475569;
    }

    @media (max-width: 640px) {
        .toast-container {
            left: 20px;
            right: 20px;
            max-width: none;
        }

        .toast {
            width: 100%;
        }
    }
</style>

{{-- ALERT MODAL --}}
<div id="alertModal" class="alert-modal">
    <div class="alert-overlay" onclick="closeAlert()"></div>
    <div class="alert-panel">
        <div class="alert-header" id="alertHeader">
            <div class="alert-icon" id="alertIcon">
                <span id="alertIconSymbol">⚠️</span>
            </div>
            <h3 class="alert-title" id="alertTitle">Konfirmasi</h3>
            <p class="alert-message" id="alertMessage">Pesan alert</p>
        </div>

        <!-- Error message container (hidden by default) -->
        <div id="errorContainer" class="alert-error-message" style="display: none;">
            <span>⚠️</span>
            <span id="errorText">Gagal memproses permintaan</span>
        </div>

        <div class="alert-footer" id="alertFooter">
            <!-- Tombol akan diisi oleh JavaScript -->
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div id="toastContainer" class="toast-container"></div>

<script>
(function() {
    // ========== ALERT MODAL ==========
    const modal = document.getElementById('alertModal');
    const toastContainer = document.getElementById('toastContainer');
    let currentType = 'danger';
    let closeTimer = null;
    let confirmAction = null;

    // Ikon untuk setiap tipe
    const icons = {
        danger: { icon: '⚠️', confirm: '✓', text: 'Hapus' },
        warning: { icon: '⚠️', confirm: '→', text: 'Lanjutkan' },
        success: { icon: '✓', confirm: '✓', text: 'OK' },
        info: { icon: 'ℹ️', confirm: '✓', text: 'Mengerti' },
        error: { icon: '✕', confirm: '✕', text: 'Tutup' }
    };

    // Fungsi untuk menampilkan alert
    window.showAlert = function(type, title, message, onConfirm = null) {
        // Hapus class hide jika ada
        modal.classList.remove('hide');

        // Bersihkan timer sebelumnya
        if (closeTimer) {
            clearTimeout(closeTimer);
            closeTimer = null;
        }

        currentType = type;
        confirmAction = onConfirm;

        // Update UI
        const header = document.getElementById('alertHeader');
        const icon = document.getElementById('alertIcon');
        const iconSymbol = document.getElementById('alertIconSymbol');
        const alertTitle = document.getElementById('alertTitle');
        const alertMessage = document.getElementById('alertMessage');
        const footer = document.getElementById('alertFooter');
        const errorContainer = document.getElementById('errorContainer');

        // Sembunyikan error message
        errorContainer.style.display = 'none';

        // Reset classes
        header.className = 'alert-header';
        icon.className = 'alert-icon';

        // Add type classes
        header.classList.add(type);
        icon.classList.add(type);

        // Set content
        iconSymbol.textContent = icons[type].icon;
        alertTitle.textContent = title;
        // alertMessage.textContent = message;
        alertMessage.innerHTML = message;


        // Atur footer berdasarkan tipe
        if (type === 'success' || type === 'error') {
            // Success/error hanya 1 button
            footer.innerHTML = `
                <button class="alert-btn success-single ${type}" onclick="closeAlert()">
                    <span>${icons[type].confirm}</span> ${icons[type].text}
                </button>
            `;
            footer.className = 'alert-footer success-layout';
        } else {
            // Danger, Warning, Info punya 2 button (Cancel & Confirm)
            footer.innerHTML = `
                <button class="alert-btn cancel" onclick="closeAlert()">
                    <span>✕</span> Batal
                </button>
                <button class="alert-btn confirm ${type}" id="confirmBtn" onclick="handleConfirm()">
                    <span>${icons[type].confirm}</span>
                    <span>${icons[type].text}</span>
                </button>
            `;
            footer.className = 'alert-footer';
        }

        // Tampilkan modal dengan animasi masuk
        modal.classList.add('show');
    };

    // Fungsi untuk menutup alert dengan animasi keluar
    window.closeAlert = function() {
        // Cek loading state jika ada tombol confirm
        const confirmBtn = document.getElementById('confirmBtn');
        if (confirmBtn && confirmBtn.classList.contains('loading')) {
            window.showToast('Tunggu proses selesai...', 'warning');
            return;
        }

        // Tambahkan class hide untuk animasi keluar
        modal.classList.add('hide');

        // Hapus class show setelah animasi selesai
        closeTimer = setTimeout(() => {
            modal.classList.remove('show');
            modal.classList.remove('hide');
            confirmAction = null;
            closeTimer = null;
        }, 300);
    };

    // Fungsi untuk menampilkan error di dalam alert
    window.showAlertError = function(message) {
        const errorContainer = document.getElementById('errorContainer');
        const errorText = document.getElementById('errorText');

        errorText.textContent = message || 'Terjadi kesalahan. Silakan coba lagi.';
        errorContainer.style.display = 'flex';

        // Scroll error ke dalam view
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    // Fungsi untuk handle confirm (untuk danger/warning/info)
    window.handleConfirm = function() {
        const confirmBtn = document.getElementById('confirmBtn');
        if (!confirmBtn || confirmBtn.classList.contains('loading')) return;

        if (confirmAction && typeof confirmAction === 'function') {
            // Loading state
            confirmBtn.classList.add('loading');
            confirmBtn.innerHTML = ''; // Clear untuk spinner

            // Execute confirm action
            const result = confirmAction();

            // Handle jika result adalah Promise
            if (result && typeof result.then === 'function') {
                result
                    .then(() => {
                        // Success akan di-handle oleh controller, tapi kita tutup alert
                        setTimeout(() => {
                            confirmBtn.classList.remove('loading');
                            confirmBtn.innerHTML = `
                                <span>${icons[currentType].confirm}</span>
                                <span>${icons[currentType].text}</span>
                            `;
                            window.closeAlert();
                        }, 500);
                    })
                    .catch((error) => {
                        // Tampilkan error
                        confirmBtn.classList.remove('loading');
                        confirmBtn.innerHTML = `
                            <span>${icons[currentType].confirm}</span>
                            <span>${icons[currentType].text}</span>
                        `;
                        window.showAlertError(error.message || 'Gagal memproses permintaan');
                    });
            } else {
                // Synchronous action
                setTimeout(() => {
                    confirmBtn.classList.remove('loading');
                    confirmBtn.innerHTML = `
                        <span>${icons[currentType].confirm}</span>
                        <span>${icons[currentType].text}</span>
                    `;
                    window.closeAlert();
                }, 500);
            }
        } else {
            // Jika tidak ada callback, langsung close
            window.closeAlert();
        }
    };

    // ========== TOAST ==========
    window.showToast = function(message, type = 'success', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const iconMap = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };

        const icon = iconMap[type] || '✓';

        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-icon ${type}">${icon}</div>
                <div class="toast-message">${message}</div>
                <button class="toast-close" onclick="this.closest('.toast').classList.add('hide'); setTimeout(() => this.closest('.toast').remove(), 300);">✕</button>
            </div>
        `;

        toastContainer.appendChild(toast);

        // Auto hide setelah duration
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.add('hide');
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);

        return toast;
    };

    // Helper functions untuk toast
    window.showSuccess = function(message) {
        return window.showToast(message, 'success');
    };

    window.showError = function(message) {
        return window.showToast(message, 'error', 5000);
    };

    window.showWarning = function(message) {
        return window.showToast(message, 'warning', 4000);
    };

    window.showInfo = function(message) {
        return window.showToast(message, 'info', 3000);
    };

    // Tutup dengan tombol ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show') && !modal.classList.contains('hide')) {
            window.closeAlert();
        }
    });

    // Auto show dari session
    @if(session('success'))
        setTimeout(() => {
            window.showSuccess('{{ session('success') }}');
        }, 100);
    @endif

    @if(session('error'))
        setTimeout(() => {
            window.showError('{{ session('error') }}');
        }, 100);
    @endif

    @if(session('warning'))
        setTimeout(() => {
            window.showWarning('{{ session('warning') }}');
        }, 100);
    @endif

    @if(session('info'))
        setTimeout(() => {
            window.showInfo('{{ session('info') }}');
        }, 100);
    @endif

    @if(session('alert'))
        setTimeout(() => {
            window.showAlert(
                '{{ session('alert.type') }}',
                '{{ session('alert.title') }}',
                '{{ session('alert.message') }}',
                function() {
                    @if(session('alert.confirm_url'))
                        window.location.href = '{{ session('alert.confirm_url') }}';
                    @endif
                }
            );
        }, 100);
    @endif
})();
</script>
