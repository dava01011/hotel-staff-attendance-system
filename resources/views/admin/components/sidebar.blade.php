@php
    use App\Helpers\RoleHelper;
@endphp

<aside id="sidebar"
    class="w-64 bg-white border-r border-gray-200 fixed inset-y-0 left-0 z-40 overflow-y-auto
           transform transition-transform duration-300 ease-in-out md:translate-x-0 -translate-x-full
           md:static">

    <!-- Logo -->
    <div class="bg-[#ea580c] p-4 md:p-6 text-white sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="logo-container flex justify-center bg-white rounded-lg p-2">
                    <img src="{{ asset('img/Logo.png') }}" alt="HARRIS Hotel Logo" class="h-12 md:h-14 object-contain">
                </div>
            </div>
            <button onclick="toggleSidebar()"
                class="md:hidden text-white hover:bg-white/10 rounded-lg p-2 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-3 md:p-4 space-y-1 md:space-y-2 pb-20 md:pb-4">

        <!-- ── UTAMA ── -->
        <a href="{{ route('admin.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}
                   flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                   hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
            <i class="fas fa-home w-5 text-center group-hover:scale-110 transition-transform"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        <!-- ── MASTER DATA (Super Admin only) ── -->
        @if (RoleHelper::isSuperAdmin())
            <div class="pt-2">
                <p class="px-3 md:px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-3 mb-2">
                    Master Data
                </p>

                @if (RoleHelper::canManageUsers())
                    <a href="{{ route('admin.user.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.user.index') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-user w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">User</span>
                    </a>
                @endif

                <a href="{{ route('admin.departemen.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.departemen.index') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                    <i class="fas fa-building w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Departemen</span>
                </a>

                <a href="{{ route('admin.jabatan.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.jabatan.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                    <i class="fas fa-briefcase w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Jabatan</span>
                </a>
                
                <a href="{{ route('admin.lokasi-kantor.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.lokasi-kantor.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                    <i class="fas fa-map-marker-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Lokasi</span>
                </a>
            </div>
        @endif

        <!-- ── KARYAWAN (View: semua role) ── -->
        @if (RoleHelper::canViewKaryawan())
            <div class="pt-2">
                <p class="px-3 md:px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-3 mb-2">
                    Karyawan
                </p>

                <a href="{{ route('admin.karyawan.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                    <i class="fas fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Karyawan</span>
                </a>

                <a href="{{ route('admin.wajah.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.wajah.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                    <i class="fas fa-face-smile w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Wajah</span>
                </a>
            </div>
        @endif

        <!-- ── ABSENSI & JADWAL ── -->
        @if (RoleHelper::canViewAbsensi())
            <div class="pt-2">
                <p class="px-3 md:px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-3 mb-2">
                    Absensi & Jadwal
                </p>

                <a href="{{ route('admin.absensi.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                    <i class="fas fa-clock w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Absensi</span>
                </a>

                {{-- Schedule (Super Admin & Admin) --}}
                @if (RoleHelper::canManageShift())
                    <a href="{{ route('admin.shift-pattern.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.shift-pattern.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-table-columns w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Schedule</span>
                    </a>
                @endif

                {{-- Shift, Hari Libur, Libur Pengganti (Super Admin only) --}}
                @if (RoleHelper::isSuperAdmin())

                    <a href="{{ route('admin.shift.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.shift.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-user-clock w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Shift</span>
                    </a>

                    <a href="{{ route('admin.hari-libur-nasional.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.hari-libur-nasional.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-calendar-times w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Hari Libur Nasional</span>
                    </a>

                    <a href="{{ route('admin.libur-pengganti.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.libur-pengganti.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-umbrella-beach w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Libur Pengganti</span>
                    </a>
                @endif


            </div>
        @endif

        <!-- ── CUTI & GAJI ── -->
        @if (RoleHelper::canViewCuti() || RoleHelper::canManageSalary())
            <div class="pt-2">
                <p class="px-3 md:px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-3 mb-2">
                    Cuti & Gaji
                </p>

                {{-- Cuti Menu (View: semua role, tapi CRUD dibatasi di controller) --}}
                @if (RoleHelper::canViewCuti())
                    <a href="{{ route('admin.cuti') }}"
                        class="sidebar-link {{ request()->routeIs('admin.cuti.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-calendar-times w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Cuti</span>
                    </a>

                    <a href="{{ route('admin.jenis-cuti.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.jenis-cuti.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-tags w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Jenis Cuti</span>
                    </a>

                    <a href="{{ route('admin.jatah-cuti.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.jatah-cuti.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-gift w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Jatah Cuti</span>
                    </a>
                @endif

                {{-- Penggajian (Super Admin only) --}}
                @if (RoleHelper::isSuperAdmin())
                    <a href="{{ route('admin.gaji.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.gaji.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-money-bill-wave w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Penggajian</span>
                    </a>
                @endif
            </div>
        @endif

        <!-- ── SISTEM ── -->
        @if (RoleHelper::canViewActivityLog() || RoleHelper::canAccessApproval())
            <div class="pt-2">
                <p class="px-3 md:px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-3 mb-2">
                    Sistem
                </p>

                {{-- Activity Log (Super Admin only) --}}
                @if (RoleHelper::isSuperAdmin())
                    <a href="{{ route('admin.activity-log.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-history w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Log Aktivitas</span>
                    </a>
                @endif

                {{-- Approval (Super Admin, Admin) --}}
                @if (RoleHelper::canAccessApproval())
                    <a href="{{ route('admin.approval') }}"
                        class="sidebar-link {{ request()->routeIs('admin.approval') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-check-double w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Approval</span>

                        @php
                            $user = Auth::user();
                            $userRole = $user->role;
                            $deptId = $user->karyawan->departemen_id ?? null;

                            $cutiCount = \App\Models\Cuti::where('status', 'pending')
                                ->where(function($q) use ($userRole, $deptId) {
                                    if ($userRole === 'admin') {
                                        $q->where('current_step', 'admin')
                                          ->whereHas('karyawan', fn($k) => $k->where('departemen_id', $deptId));
                                    } else {
                                        $q->where('current_step', $userRole);
                                    }
                                })->count();

                            $shiftCount = \App\Models\AjukanShift::where('status', 'pending')
                                ->where(function($q) use ($userRole, $deptId) {
                                    if ($userRole === 'admin') {
                                        $q->where('current_step', 'admin')
                                          ->where('departemen_id', $deptId);
                                    } else {
                                        $q->where('current_step', $userRole);
                                    }
                                })->count();

                            $pendingCount = $cutiCount + $shiftCount;
                        @endphp

                        @if ($pendingCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5 font-semibold">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                @endif
                
                {{-- Pengumuman (Super Admin, Admin, HRD, GM) --}}
                @if (RoleHelper::hasMenuAccess(['super_admin', 'admin', 'gm']))
                    <a href="{{ route('admin.pengumuman.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}
                       flex items-center space-x-3 px-3 md:px-4 py-2.5 md:py-3 text-gray-700 rounded-lg
                       hover:bg-orange-50 hover:text-[#ea580c] transition-all duration-200 group">
                        <i class="fas fa-bullhorn w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Pengumuman</span>
                    </a>
                @endif
            </div>
        @endif

    </nav>

    <!-- Footer -->
    {{-- <div class="hidden md:block sticky bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-gray-50/80 backdrop-blur-sm">
        <div class="text-center">
            <p class="text-xs text-gray-500 font-medium">© 2025 Harris Hotel</p>
            <p class="text-xs text-gray-400">Sistem Absensi v1.0</p>
        </div>
    </div> --}}
</aside>

<style>
    .sidebar-link.active {
        background-color: #F0F7FF;
        color: #ea580c;
        box-shadow: none;
    }

    .sidebar-link.active i {
        color: #ea580c;
    }

    .sidebar-link {
        text-decoration: none !important;
        position: relative;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        #sidebar {
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        }
    }

    #sidebar::-webkit-scrollbar {
        width: 6px;
    }

    #sidebar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #sidebar::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 3px;
    }

    #sidebar::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    @keyframes pulse-badge {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .sidebar-link span.bg-red-500 {
        animation: pulse-badge 2s infinite;
    }
</style>