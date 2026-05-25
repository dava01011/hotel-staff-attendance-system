<header class="bg-white shadow-sm border-b border-gray-200 z-20 sticky top-0">
    <div class="flex items-center justify-between px-4 md:px-6 py-3 md:py-4">
        <!-- Mobile Menu Button & Logo -->
        <div class="flex items-center space-x-3 md:space-x-4">
            <button onclick="toggleSidebar()" class="md:hidden text-gray-600 hover:text-[#ea580c] transition-colors">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <!-- Page Title - Hidden on mobile, show on desktop -->
            <div class="hidden md:block">
                <h1 class="text-xl font-bold text-gray-800">@yield('page-title', 'Web Attendance')</h1>
                <p class="text-sm text-gray-500">@yield('page-subtitle', 'Admin Panel')</p>
            </div>

            <!-- Mobile Logo/Title -->
            <div class="md:hidden flex items-center">
                <img src="{{ asset('img/Logo.png') }}" alt="Logo" class="h-8 w-auto">
            </div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center space-x-2 md:space-x-4">
            <!-- Notifications -->
            @php
                $unreadCount = auth()->user()->notifikasi()->forMode()->where('is_read', false)->count();
            @endphp

            <div class="relative">
                <a href="{{ route('notifikasi.index') }}"
                    class="relative text-gray-600 hover:text-[#ea580c] transition-colors p-2 rounded-lg hover:bg-orange-50 {{ request()->routeIs('notifikasi.index') ? 'text-[#ea580c] bg-orange-50' : '' }}">
                    <i class="fas fa-bell text-lg md:text-xl"></i>
                    @if ($unreadCount > 0)
                        <span
                            class="absolute top-0 right-0 bg-red-500 text-white text-[10px] md:text-xs
                                     rounded-full min-w-[16px] md:min-w-[18px] h-[16px] md:h-[18px]
                                     flex items-center justify-center px-1 font-bold animate-pulse">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- User Dropdown -->
            <div class="relative">
                <button onclick="toggleUserMenu()"
                    class="flex items-center space-x-2 md:space-x-3 hover:bg-gray-100 rounded-lg px-2 md:px-3 py-2 transition-colors">
                    @php
                        $karyawan = auth()->user()->karyawan;
                        $fotoProfil =
                            $karyawan && $karyawan->foto_profil ? asset('storage/' . $karyawan->foto_profil) : null;
                    @endphp

                    <!-- Foto Profil atau Initial -->
                    @if ($fotoProfil)
                        <img src="{{ $fotoProfil }}" alt="{{ auth()->user()->nama }}"
                            class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover border-2 border-orange-200">
                    @else
                        <div
                            class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-[#ea580c] flex items-center justify-center text-white font-semibold text-sm md:text-base">
                            {{ substr(auth()->user()->nama, 0, 1) }}
                        </div>
                    @endif

                    <!-- User Info - Hidden on mobile -->
                    <div class="hidden lg:block text-left">
                        <p class="text-sm font-semibold text-gray-800 max-w-[150px] truncate">
                            {{ auth()->user()->nama }}
                        </p>
                        <p class="text-xs text-gray-500 capitalize truncate max-w-[200px]">
                            {{ $karyawan->jabatan->nama_jabatan ?? str_replace('_', ' ', auth()->user()->role) }} • {{ $karyawan->departemen->nama ?? '-' }}
                        </p>
                    </div>

                    <!-- Chevron - Hidden on small mobile -->
                    <i class="fas fa-chevron-down text-xs text-gray-600 hidden sm:block"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="userMenu"
                    class="absolute right-0 mt-2 w-56 md:w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 hidden z-50">

                    <!-- User Info Card - Show in dropdown on mobile -->
                    <div class="px-4 py-3 border-b border-gray-200 lg:hidden">
                        <div class="flex items-center space-x-3">
                            @if ($fotoProfil)
                                <img src="{{ $fotoProfil }}" alt="{{ auth()->user()->nama }}"
                                    class="w-12 h-12 rounded-full object-cover border-2 border-orange-200">
                            @else
                                <div
                                    class="w-12 h-12 rounded-full bg-[#ea580c] flex items-center justify-center text-white font-semibold">
                                    {{ substr(auth()->user()->nama, 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">
                                    {{ auth()->user()->nama }}
                                </p>
                                <p class="text-xs text-gray-500 capitalize">
                                    {{ $karyawan->jabatan->nama_jabatan ?? str_replace('_', ' ', auth()->user()->role) }} • {{ $karyawan->departemen->nama ?? '-' }}
                                </p>
                                @if ($karyawan)
                                    <p class="text-xs text-gray-400">
                                        NIP: {{ $karyawan->nip }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Menu Items -->
                    <div class="py-1">
                        @if ($karyawan)
                            <div class="px-4 py-2 hidden lg:block">
                                <p class="text-xs text-gray-400">NIP: {{ $karyawan->nip }}</p>
                                <p class="text-xs text-gray-400">{{ $karyawan->departemen->nama ?? '-' }}</p>
                            </div>
                            <hr class="my-1 hidden lg:block">
                        @endif

                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors lg:hidden">
                            <i class="fas fa-home w-5 mr-3 text-gray-500"></i>
                            Dashboard
                        </a>

                        <a href="{{ route('notifikasi.index') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors lg:hidden">
                            <i class="fas fa-bell w-5 mr-3 text-gray-500"></i>
                            Notifikasi
                            @if ($unreadCount > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Pengaturan Button -->
                        <a href="{{ route('settings.index') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-cog w-5 mr-3 text-gray-600"></i>
                            Pengaturan
                        </a>

                        <!-- Ganti Mode Button -->
                        <a href="{{ route('role.select') }}"
                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-exchange-alt w-5 mr-3 text-gray-600"></i>
                            Ganti Mode
                        </a>

                        <!-- Divider -->
                        <hr class="my-1">

                        <!-- Logout Button -->
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="block">
                            @csrf
                            <button type="button" onclick="confirmLogout(event)"
                                class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt w-5 mr-3"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Page Title - Show below header on mobile -->
    <div class="md:hidden px-4 pb-3 border-t border-gray-100">
        <h1 class="text-lg font-bold text-gray-800">@yield('page-title', 'Web Attendance')</h1>
        <p class="text-xs text-gray-500">@yield('page-subtitle', 'Admin Panel')</p>
    </div>
</header>

<!-- Mobile Overlay -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden hidden" onclick="toggleSidebar()">
</div>

<style>
    /* Animasi untuk dropdown */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #userMenu:not(.hidden) {
        animation: slideDown 0.2s ease-out;
    }

    /* Sticky header shadow on scroll */
    header.shadow-scroll {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Modal Styles */
    .logout-modal {
        animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .logout-modal-content {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script>
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('hidden');
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Close user menu when clicking outside
    document.addEventListener('click', function(event) {
        const userMenu = document.getElementById('userMenu');
        const isClickInside = event.target.closest('button[onclick="toggleUserMenu()"]') ||
            event.target.closest('#userMenu');

        if (!isClickInside && !userMenu.classList.contains('hidden')) {
            userMenu.classList.add('hidden');
        }
    });

    // Add shadow to header on scroll
    window.addEventListener('scroll', function() {
        const header = document.querySelector('header');
        if (window.scrollY > 10) {
            header.classList.add('shadow-scroll');
        } else {
            header.classList.remove('shadow-scroll');
        }
    });

    // Close sidebar on route change (for mobile)
    document.addEventListener('turbolinks:load', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (window.innerWidth < 768) {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    });

    // Logout Confirmation
    function confirmLogout(event) {
        event.preventDefault();

        // Create modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 logout-modal';
        modal.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full logout-modal-content">
                <!-- Header -->
                <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-red-900">Konfirmasi Logout</h3>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-4">
                    <p class="text-gray-700 text-sm">
                        Apakah Anda yakin ingin logout dari aplikasi?
                    </p>
                    <p class="text-gray-500 text-xs mt-2">
                        Anda akan diminta untuk login kembali ketika mengakses aplikasi.
                    </p>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="this.closest('.logout-modal').remove()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="button" onclick="document.getElementById('logoutForm').submit()"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Close modal on overlay click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.parentNode) {
                modal.remove();
            }
        });
    }
</script>
