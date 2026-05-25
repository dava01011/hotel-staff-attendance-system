{{-- resources/views/partials/role-switcher.blade.php --}}
@php
    $user = Auth::user();
    $hasAdminRole = in_array($user->role, ['admin', 'super_admin']);
    $hasKaryawanData = $user->karyawan && $user->karyawan->status === 'aktif';
    $hasDualAccess = $hasAdminRole && $hasKaryawanData;
    $activeRole = Session::get('active_role');
@endphp

@if($hasDualAccess)
<div class="role-switcher">
    <form action="{{ route('role.switch') }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="switch_to" value="{{ $activeRole === 'admin' ? 'karyawan' : 'admin' }}">
        <button type="submit" class="switch-role-btn">
            <i class="fas fa-exchange-alt mr-2"></i>
            Beralih ke Mode {{ $activeRole === 'admin' ? 'Karyawan' : 'Admin' }}
        </button>
    </form>
</div>

<style>
.switch-role-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.switch-role-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

.switch-role-btn:active {
    transform: translateY(0);
}
</style>
@endif
