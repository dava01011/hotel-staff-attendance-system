{{-- resources/views/karyawan/partials/header.blade.php --}}

<header class="kv-header">

    @if(request()->routeIs('karyawan.dashboard'))

        {{-- ── Dashboard Header ──────────────────────────── --}}
        <div class="kv-h-logo">
            <img src="{{ asset('img/Logo.png') }}" alt="Logo">
        </div>

<div class="kv-h-actions">
    @php
        $unreadCount = auth()->user()->notifikasi()->forMode()->where('is_read', false)->count();
        $karyawan = auth()->user()->karyawan;
        $fotoProfil = $karyawan && $karyawan->foto_profil ? asset('storage/' . $karyawan->foto_profil) : null;
    @endphp

    <a href="{{ route('notifikasi.index') }}" class="kv-h-btn {{ request()->routeIs('notifikasi.index') ? 'is-active' : '' }}" aria-label="Notifikasi">
        <i class="fas fa-bell"></i>
        @if($unreadCount > 0)
            <span class="kv-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </a>

    <button class="kv-h-btn profile-avatar-btn" onclick="openProfileModal()" aria-label="Profil">
        @if($fotoProfil)
            <img src="{{ $fotoProfil }}" alt="Foto {{ auth()->user()->nama }}" class="profile-avatar-img">
        @else
            <i class="fas fa-user-circle"></i>
        @endif
    </button>
</div>

    @else

        {{-- ── Inner Page Header ─────────────────────────── --}}
        <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('karyawan.dashboard') }}"
        class="kv-back-btn"
        aria-label="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>

        <h2 class="kv-page-title">@yield('title', 'Halaman')</h2>

        {{-- spacer to keep title centered --}}
        <div style="width:40px;"></div>

    @endif

</header>

@include('karyawan.partials.profile-modal')

<style>
/* Profile avatar button */
.profile-avatar-btn {
    padding: 0 !important;
    overflow: hidden;
}

.profile-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* ── Base ───────────────────────────────────────────────────── */
:root {
    --kv-blue:   #ea580c;
    --kv-blue2:  #c2410c;
    --kv-orange: #ea580c; /* Overriding orange with blue for consistency */
    --kv-orange2:#093E7E;
    --kv-white:  #ffffff;
    --kv-text:   #1e293b;
    --kv-sub:    #64748b;
    --kv-border: #e2e8f0;
    --kv-h:      60px;
}

/* ── Header Shell ───────────────────────────────────────────── */
.kv-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 900;
    height: var(--kv-h);
    background: var(--kv-white);
    border-bottom: 2px solid var(--kv-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    gap: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
}

/* ── Logo ───────────────────────────────────────────────────── */
.kv-h-logo img {
    height: 38px;
    width: auto;
    display: block;
}

/* ── Action Buttons (bell + profile) ───────────────────────── */
.kv-h-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.kv-h-btn {
    position: relative;
    width: 40px;
    height: 40px;
    border-radius: 25px;
    border: 1.5px solid var(--kv-border);
    background: #f8fafc;
    color: var(--kv-sub);
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.kv-h-btn:hover {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: var(--kv-blue);
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(29,78,216,.12);
}

.kv-h-btn.is-active {
    background: #EFF6FF;
    border-color: #BFDBFE;
    color: var(--kv-blue);
}

/* ── Notification Badge ─────────────────────────────────────── */
.kv-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--kv-orange);
    color: white;
    font-size: 9px;
    font-weight: 800;
    min-width: 17px;
    height: 17px;
    padding: 0 4px;
    border-radius: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(12, 82, 166, 0.45);
    animation: badge-pop 2.5s ease-in-out infinite;
}

@keyframes badge-pop {
    0%, 100% { transform: scale(1); }
    50%       { transform: scale(1.18); }
}

/* ── Back Button ────────────────────────────────────────────── */
.kv-back-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    border: 1.5px solid var(--kv-border);
    background: #f8fafc;
    color: var(--kv-sub);
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s;
    flex-shrink: 0;
}

.kv-back-btn:hover {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: var(--kv-blue);
    transform: translateX(-2px);
}

/* ── Page Title (inner pages) ───────────────────────────────── */
.kv-page-title {
    flex: 1;
    text-align: center;
    font-size: 16px;
    font-weight: 800;
    color: var(--kv-text);
    margin: 0;
    /* blue accent */
    color: var(--kv-blue);
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 480px) {
    .kv-h-logo img { height: 32px; }
    .kv-h-btn,
    .kv-back-btn   { width: 36px; height: 36px; font-size: 16px; border-radius: 10px; }
    .kv-page-title { font-size: 15px; }
}
</style>

<script>
function openProfileModal() {
    const modal   = document.getElementById('profileModal');
    const overlay = document.getElementById('profileOverlay');
    if (modal && overlay) {
        overlay.classList.add('active');
        setTimeout(() => modal.classList.add('active'), 10);
        document.body.style.overflow = 'hidden';
    }
}

function closeProfileModal() {
    const modal   = document.getElementById('profileModal');
    const overlay = document.getElementById('profileOverlay');
    if (modal && overlay) {
        modal.classList.remove('active');
        setTimeout(() => overlay.classList.remove('active'), 300);
        document.body.style.overflow = '';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('profileOverlay');
    if (overlay) {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) closeProfileModal();
        });
    }

    const modal = document.getElementById('profileModal');
    if (modal) {
        let startY = 0;
        modal.addEventListener('touchstart', e => { startY = e.touches[0].clientY; });
        modal.addEventListener('touchend',   e => {
            if (e.changedTouches[0].clientY - startY > 100) closeProfileModal();
        });
    }
});
</script>
