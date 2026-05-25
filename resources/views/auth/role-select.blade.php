<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Mode Akses - Harris Absensi</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #F8FAFC;
            min-height: 100vh;
        }

        .role-card {
            transition: all 0.2s ease;
            cursor: pointer;
            border: 2px solid #E2E8F0;
        }

        .role-card:hover {
            transform: translateY(-2px);
            border-color: #CBD5E1;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .role-card.selected {
            border-color: #ea580c;
            background-color: #F0F7FF;
        }

        .role-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 1rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .icon-admin {
            background-color: #E0E7FF;
            color: #4338CA;
        }
        
        .role-card.selected .icon-admin {
            background-color: #4338CA;
            color: #FFFFFF;
        }

        .icon-karyawan {
            background-color: #E1EFFE;
            color: #ea580c;
        }
        
        .role-card.selected .icon-karyawan {
            background-color: #ea580c;
            color: #FFFFFF;
        }

        .btn-submit {
            background-color: #ea580c;
            transition: all 0.2s ease;
        }

        .btn-submit:hover:not(:disabled) {
            background-color: #093E7E;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(12, 82, 166, 0.2);
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-3xl">

            <!-- Header -->
            <div class="text-center mb-10">
                <div class="mb-6">
                    <img src="{{ asset('img/Logo.png') }}" alt="HARRIS Hotel Logo" class="h-16 mx-auto object-contain">
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                    Selamat Datang, {{ $user->nama }}!
                </h1>
                <p class="text-gray-500">
                    Silakan pilih mode akses yang ingin Anda gunakan
                </p>
            </div>

            <!-- Role Selection Cards -->
            <form action="{{ route('role.set') }}" method="POST" id="roleForm">
                @csrf
                <input type="hidden" name="selected_role" id="selected_role">

                <div class="grid md:grid-cols-2 gap-5 mb-8">
                    <!-- Admin Card -->
                    <div class="role-card bg-white rounded-xl shadow-sm p-6"
                         onclick="selectCard('admin', this)">
                        <div class="role-icon icon-admin transition-colors duration-200">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">
                            Mode Admin / HR
                        </h3>
                        <p class="text-gray-500 text-center text-sm mb-5">
                            Kelola sistem, karyawan, dan data absensi
                        </p>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-[#ea580c] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Kelola data karyawan & shift</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-[#ea580c] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Persetujuan cuti & pengajuan</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-[#ea580c] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Laporan & monitoring dashboard</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Karyawan Card -->
                    <div class="role-card bg-white rounded-xl shadow-sm p-6"
                         onclick="selectCard('karyawan', this)">
                        <div class="role-icon icon-karyawan transition-colors duration-200">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">
                            Mode Karyawan
                        </h3>
                        <p class="text-gray-500 text-center text-sm mb-5">
                            Akses absensi harian dan portal personal
                        </p>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-[#ea580c] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Absensi masuk & pulang harian</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-[#ea580c] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Pengajuan cuti & izin</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-[#ea580c] mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Lihat riwayat kehadiran & slip gaji</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" disabled
                        class="btn-submit w-full text-white py-3 rounded-lg font-semibold disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                    Lanjutkan ke Dashboard <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </button>
            </form>

            <!-- Info Box -->
            <div class="bg-orange-50 border border-orange-100 rounded-lg p-4 mt-6">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-orange-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm text-orange-800">
                        Anda dapat beralih mode kapan saja melalui menu profil setelah berhasil login.
                    </p>
                </div>
            </div>

            <!-- Logout Button -->
            <div class="text-center mt-8">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-800 font-medium inline-flex items-center text-sm transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Batalkan dan Keluar
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        function selectCard(role, card) {
            // Remove selected class from all cards
            document.querySelectorAll('.role-card').forEach(c => {
                c.classList.remove('selected');
            });

            // Add selected class to clicked card
            card.classList.add('selected');

            // Set hidden input value
            document.getElementById('selected_role').value = role;

            // Enable submit button
            document.getElementById('submitBtn').disabled = false;
        }

        // Prevent form submission if no role selected
        document.getElementById('roleForm').addEventListener('submit', function(e) {
            const selectedRole = document.getElementById('selected_role').value;
            if (!selectedRole) {
                e.preventDefault();
                alert('Silakan pilih mode akses terlebih dahulu');
            }
        });
    </script>
</body>

</html> 
