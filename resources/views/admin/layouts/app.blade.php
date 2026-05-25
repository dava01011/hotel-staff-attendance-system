<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Harris Absensi</title>
<link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
    {{-- <title>@yield('title', 'Admin') - Harris Absensi</title> --}}

    <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
        }

        .gradient-blue {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        }

        .sidebar-link {
            transition: all 0.3s ease;
        }

        .sidebar-link:hover {
            background: rgba(12, 82, 166, 0.05);
            padding-left: 1.5rem;
        }

        .sidebar-link.active {
            background: rgba(12, 82, 166, 0.1);
            padding-left: 1.5rem;
            font-weight: 600;
        }

        #sidebar {
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            #sidebar.closed {
                transform: translateX(-100%);
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('admin.components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            @include('admin.components.header')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-6">
                <!-- Breadcrumb (Optional) -->
                @if(isset($breadcrumbs))
                <nav class="mb-4 text-sm">
                    <ol class="flex items-center space-x-2 text-gray-600">
                        @foreach($breadcrumbs as $label => $url)
                            @if($loop->last)
                                <li class="text-[#ea580c] font-semibold">{{ $label }}</li>
                            @else
                                <li>
                                    <a href="{{ $url }}" class="hover:text-[#ea580c]">{{ $label }}</a>
                                    <span class="mx-2">/</span>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
                @endif

                <!-- Flash Messages -->
                {{-- @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
                @endif --}}
        @include('notifikasi.toast')

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    {{-- <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden" onclick="toggleSidebar()"></div> --}}

    <script>
        // Toggle Sidebar untuk Mobile
        // function toggleSidebar() {
        //     const sidebar = document.getElementById('sidebar');
        //     const overlay = document.getElementById('sidebar-overlay');

        //     sidebar.classList.toggle('closed');
        //     overlay.classList.toggle('hidden');
        // }

        // Auto close pada resize
        // window.addEventListener('resize', function() {
        //     if (window.innerWidth >= 768) {
        //         document.getElementById('sidebar').classList.remove('closed');
        //         document.getElementById('sidebar-overlay').classList.add('hidden');
        //     }
        // });

        function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.classList.toggle('hidden');
}

// tutup dropdown saat klik di luar
document.addEventListener('click', function (e) {
    const menu = document.getElementById('userMenu');
    const button = e.target.closest('[onclick="toggleUserMenu()"]');

    if (!menu.contains(e.target) && !button) {
        menu.classList.add('hidden');
    }
});


</script>

    @stack('scripts')
</body>
</html>
