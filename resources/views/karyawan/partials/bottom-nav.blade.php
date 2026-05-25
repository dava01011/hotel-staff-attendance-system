{{-- resources/views/karyawan/partials/bottom-nav.blade.php --}}

@php
    $userRole   = Auth::user()->role ?? 'karyawan';
    $canApprove = in_array($userRole, ['manager','gm','super_admin','admin']);
@endphp

<nav class="kv-nav" role="navigation" aria-label="Navigasi utama">

    {{-- Beranda --}}
    <a href="{{ route('karyawan.dashboard') }}"
       class="kv-nav-item {{ request()->routeIs('karyawan.dashboard') ? 'is-active' : '' }}">
        <span class="kv-nav-icon"><i class="fas fa-home"></i></span>
        <span class="kv-nav-label">Beranda</span>
        <span class="kv-nav-pip"></span>
    </a>

    {{-- Riwayat --}}
    <a href="{{ route('karyawan.absensi.log') }}"
       class="kv-nav-item {{ request()->routeIs('karyawan.absensi.log') ? 'is-active' : '' }}">
        <span class="kv-nav-icon"><i class="fas fa-history"></i></span>
        <span class="kv-nav-label">Riwayat</span>
        <span class="kv-nav-pip"></span>
    </a>

    {{-- Pengajuan --}}
    <a href="{{ route('karyawan.pengajuan.index') }}"
       class="kv-nav-item {{ request()->routeIs('karyawan.pengajuan.index') ? 'is-active' : '' }}">
        <span class="kv-nav-icon"><i class="fas fa-calendar-alt"></i></span>
        <span class="kv-nav-label">Pengajuan</span>
        <span class="kv-nav-pip"></span>
    </a>

    {{-- Approval / Slip Gaji --}}
    @if($canApprove)
        <a href="{{ route('admin.approval') }}"
           class="kv-nav-item {{ request()->routeIs('admin.approval') ? 'is-active' : '' }}">
            <span class="kv-nav-icon"><i class="fas fa-check-circle"></i></span>
            <span class="kv-nav-label">Approval</span>
            <span class="kv-nav-pip"></span>
        </a>
    @else
        <a href="{{ route('karyawan.gaji.index') }}"
           class="kv-nav-item {{ request()->routeIs('karyawan.gaji.index') ? 'is-active' : '' }}">
            <span class="kv-nav-icon"><i class="fas fa-file-invoice-dollar"></i></span>
            <span class="kv-nav-label">Slip Gaji</span>
            <span class="kv-nav-pip"></span>
        </a>
    @endif

    {{-- Pengaturan --}}
    <a href="{{ route('settings.index') }}"
       class="kv-nav-item {{ request()->routeIs('settings.index') ? 'is-active' : '' }}">
        <span class="kv-nav-icon"><i class="fas fa-cog"></i></span>
        <span class="kv-nav-label">Pengaturan</span>
        <span class="kv-nav-pip"></span>
    </a>

</nav>

<style>
/* ── Bottom Nav Shell ───────────────────────────────────────── */
.kv-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 900;
    height: 64px;
    background: #ffffff;
    border-top: 1.5px solid #e2e8f0;
    display: flex;
    align-items: stretch;
    box-shadow: 0 -4px 20px rgba(0,0,0,.07);

    /* safe area for notched phones */
    padding-bottom: env(safe-area-inset-bottom, 0);
}

/* ── Nav Item ───────────────────────────────────────────────── */
.kv-nav-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    text-decoration: none;
    color: #94a3b8;
    position: relative;
    transition: color 0.2s;
    -webkit-tap-highlight-color: transparent;
    padding-top: 6px;
}

.kv-nav-item:hover {
    color: #1d4ed8;
}

/* ── Icon wrapper ───────────────────────────────────────────── */
.kv-nav-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: all 0.25s cubic-bezier(.34,1.56,.64,1);
    background: transparent;
}

/* ── Label ──────────────────────────────────────────────────── */
.kv-nav-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .2px;
    line-height: 1;
    transition: color 0.2s;
}

/* ── Pip (top indicator bar) ────────────────────────────────── */
.kv-nav-pip {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%) scaleX(0);
    width: 28px;
    height: 3px;
    border-radius: 0 0 3px 3px;
    background: #ea580c;
    transform-origin: center;
    transition: transform 0.25s cubic-bezier(.34,1.56,.64,1);
}

/* ── Active State ───────────────────────────────────────────── */
.kv-nav-item.is-active {
    color: #ea580c;
}

.kv-nav-item.is-active .kv-nav-icon {
    background: #EFF6FF;
    color: #ea580c;
    transform: translateY(-2px) scale(1.08);
    box-shadow: 0 4px 12px rgba(12, 82, 166, 0.2);
}

.kv-nav-item.is-active .kv-nav-label {
    color: #ea580c;
}

.kv-nav-item.is-active .kv-nav-pip {
    transform: translateX(-50%) scaleX(1);
}

/* ── Tap animation ──────────────────────────────────────────── */
.kv-nav-item:active .kv-nav-icon {
    transform: scale(0.9);
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 400px) {
    .kv-nav { height: 60px; }
    .kv-nav-icon { width: 32px; height: 32px; font-size: 16px; }
    .kv-nav-label { font-size: 9px; }
}
</style>
