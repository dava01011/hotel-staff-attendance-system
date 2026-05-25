<!-- Modal Overlay -->
<div class="profile-overlay" id="profileOverlay"></div>

<!-- Bottom Sheet Modal -->
<div class="profile-modal" id="profileModal">
    <!-- Drag Handle -->
    <div class="modal-handle"></div>

    <!-- User Info Section -->
    <div class="user-info-section">
        <div class="user-avatar">
            @php
                $karyawan = auth()->user()->karyawan;
                $fotoProfil = $karyawan && $karyawan->foto_profil ? asset('storage/' . $karyawan->foto_profil) : null;
                $inisial = strtoupper(substr(auth()->user()->nama, 0, 1));
            @endphp
            @if($fotoProfil)
                <img src="{{ $fotoProfil }}" alt="Profile">
            @else
                <span style="font-size: 24px; font-weight: bold;">{{ $inisial }}</span>
            @endif
        </div>
        <div class="user-details">
            <h3 class="user-name">{{ auth()->user()->nama ?? 'Karyawan' }}</h3>
            <p class="user-email">{{ auth()->user()->email ?? 'email@example.com' }}</p>
            @if($karyawan && $karyawan->nip)
                <p class="user-nip">NIP: {{ $karyawan->nip }}</p>
            @endif
        </div>
    </div>

    <!-- Divider -->
    <div class="modal-divider"></div>

    <!-- Menu Items -->
    <div class="menu-section">
        <!-- Menu Pengaturan (Settings) -->
        <a href="{{ route('settings.index') }}" class="menu-item">
            <div class="menu-icon">
                <i class="fas fa-cog"></i>
            </div>
            <span class="menu-text">Pengaturan</span>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </a>

        <div class="modal-divider"></div>

        <!-- Tombol Logout dengan Konfirmasi -->
        <button type="button" class="menu-item logout-item" onclick="confirmLogout()">
            <div class="menu-icon logout-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <span class="menu-text logout-text">Logout</span>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </button>

        <!-- Form logout (tersembunyi) -->
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <!-- Safe Area Bottom Padding -->
    <div class="safe-area-bottom"></div>
</div>

<style>
/* Profile Modal Styles */
.profile-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.profile-overlay.active {
    opacity: 1;
    visibility: visible;
}

.profile-modal {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    border-radius: 24px 24px 0 0;
    z-index: 1000;
    max-height: 90vh;
    transform: translateY(100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.15);
}

.profile-modal.active {
    transform: translateY(0);
}

/* Drag Handle */
.modal-handle {
    width: 40px;
    height: 4px;
    background: #e0e0e0;
    border-radius: 2px;
    margin: 12px auto 8px;
}

/* User Info Section */
.user-info-section {
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: orange;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    overflow: hidden;
    flex-shrink: 0;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-details {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-size: 18px;
    font-weight: 600;
    color: #212529;
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-email {
    font-size: 14px;
    color: #6c757d;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-nip {
    font-size: 12px;
    color: #6c757d;
    margin-top: 4px;
}

/* Divider */
.modal-divider {
    height: 1px;
    background: #f0f0f0;
    margin: 0 24px;
}

/* Menu Section */
.menu-section {
    padding: 8px 0;
}

.menu-item {
    display: flex;
    align-items: center;
    padding: 16px 24px;
    text-decoration: none;
    color: #212529;
    transition: background 0.2s;
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
}

.menu-item:hover {
    background: #f8f9fa;
}

.menu-item:active {
    background: #e9ecef;
}

.menu-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #495057;
    font-size: 18px;
    margin-right: 16px;
}

.menu-text {
    flex: 1;
    font-size: 16px;
    font-weight: 500;
}

.menu-arrow {
    color: #adb5bd;
    font-size: 14px;
}

/* Logout Item Special Style */
.logout-item {
    color: #dc3545;
}

.logout-icon {
    background: #fee;
    color: #dc3545;
}

.logout-text {
    color: #dc3545;
}

.logout-item:hover {
    background: #fff5f5;
}

/* Safe Area for iPhone Bottom */
.safe-area-bottom {
    height: env(safe-area-inset-bottom, 0px);
    min-height: 16px;
}

/* Responsive */
@media (max-width: 640px) {
    .user-info-section {
        padding: 16px 20px;
    }

    .user-avatar {
        width: 56px;
        height: 56px;
        font-size: 22px;
    }

    .user-name {
        font-size: 17px;
    }

    .user-email,
    .user-nip {
        font-size: 13px;
    }

    .menu-item {
        padding: 14px 20px;
    }

    .menu-icon {
        width: 38px;
        height: 38px;
        font-size: 16px;
    }

    .menu-text {
        font-size: 15px;
    }
}
</style>

<script>
function confirmLogout() {
    // Cek apakah fungsi showAlert dari komponen alert-toast tersedia
    if (typeof window.showAlert === 'function') {
        window.showAlert(
            'warning',
            'Konfirmasi Logout',
            'Apakah Anda yakin ingin keluar dari akun ini?',
            function() {
                document.getElementById('logoutForm').submit();
            }
        );
    } else {
        // Fallback ke confirm bawaan browser
        if (confirm('Apakah Anda yakin ingin logout?')) {
            document.getElementById('logoutForm').submit();
        }
    }
}
</script>