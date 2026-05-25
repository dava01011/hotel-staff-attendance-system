<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Harris Absensi</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

        * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }

        html, body { height: 100%; width: 100%; }

        body {
            background: linear-gradient(135deg, #ffffff 0%, #fff5f0 100%);
            overflow-x: hidden;
            overflow-y: auto;
        }

        .container-wrapper {
            min-height: 100vh;
            min-height: 100dvh;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #f0f0f0;
        }

        .input-field:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 4px rgba(12, 82, 166, 0.1);
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(12, 82, 166, 0.3);
        }

        .btn-primary:active { transform: translateY(0); }

        .gradient-text {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .floating-shape {
            position: fixed;
            border-radius: 50%;
            opacity: 0.08;
            animation: float 20s infinite ease-in-out;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33%       { transform: translate(30px, -30px) rotate(120deg); }
            66%       { transform: translate(-30px, 30px) rotate(240deg); }
        }

        .shape-1 { width: 300px; height: 300px; background: #ea580c; top: -100px; right: -100px; }
        .shape-2 { width: 200px; height: 200px; background: #c2410c; bottom: -50px; left: -50px; animation-delay: -5s; }

        .content-container { position: relative; z-index: 10; width: 100%; max-width: 1280px; }

        .password-strength { height: 4px; border-radius: 2px; transition: all 0.3s ease; }

        @media (max-width: 640px) {
            .container-wrapper { padding: 0.5rem; }
        }
    </style>
</head>

<body>
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>

    <div class="container-wrapper">
        <div class="content-container">
            <div class="grid lg:grid-cols-2 gap-4 lg:gap-8 items-center">

                {{-- ── Left Side (Desktop) ──────────────────── --}}
                <div class="hidden lg:flex flex-col justify-center space-y-8 p-8">
                    <div class="flex justify-center">
                        <img src="{{ asset('img/Logo.png') }}" alt="Logo" class="h-32 object-contain">
                    </div>
                    <div class="space-y-4">
                        <h2 class="text-4xl font-bold text-gray-800">
                            Bergabung Sebagai<br>
                            <span class="gradient-text">Karyawan</span>
                        </h2>
                        <p class="text-lg text-gray-500">
                            Daftarkan diri Anda untuk menggunakan sistem absensi digital.
                        </p>
                    </div>
                </div>

                {{-- ── Register Card ────────────────────────── --}}
                <div class="w-full">
                    <div class="bg-white rounded-2xl lg:rounded-3xl shadow-2xl p-6 sm:p-10">

                        {{-- Mobile Logo --}}
                        <div class="lg:hidden flex justify-center mb-6">
                            <img src="{{ asset('img/Logo.png') }}" alt="Logo" class="h-24 object-contain">
                        </div>

                        {{-- Header --}}
                        <div class="text-center mb-5">
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-1">Daftar Karyawan</h2>
                        </div>

                        {{-- Info Box --}}
                        <div class="mb-5 bg-orange-50 border-l-4 border-orange-500 p-3 rounded-lg flex items-start gap-2">
                            <svg class="w-4 h-4 text-orange-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-xs font-semibold text-orange-800 mb-0.5">Perhatian:</p>
                                <p class="text-xs text-orange-700">Akun Anda akan aktif setelah disetujui oleh admin.</p>
                            </div>
                        </div>

                        {{-- Error Messages --}}
                        @if($errors->any())
                            <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-3 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <ul class="text-xs text-red-700 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        {{-- Register Form --}}
                        <form action="{{ route('register.post') }}" method="POST" id="registerForm">
                            @csrf

                            {{-- Nama --}}
                            <div class="mb-4">
                                <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <input type="text" id="nama" name="nama"
                                           value="{{ old('nama') }}" required
                                           class="input-field w-full pl-12 pr-4 py-3 rounded-xl text-gray-800 text-sm"
                                           placeholder="Nama lengkap Anda"
                                           autocomplete="name">
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                        </svg>
                                    </div>
                                    <input type="email" id="email" name="email"
                                           value="{{ old('email') }}" required
                                           class="input-field w-full pl-12 pr-4 py-3 rounded-xl text-gray-800 text-sm"
                                           placeholder="nama@email.com"
                                           autocomplete="email">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Gunakan email aktif untuk notifikasi</p>
                            </div>

                            {{-- Password --}}
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input type="password" id="password" name="password"
                                           required minlength="6"
                                           class="input-field w-full pl-12 pr-12 py-3 rounded-xl text-gray-800 text-sm"
                                           placeholder="Minimal 6 karakter"
                                           autocomplete="new-password"
                                           oninput="checkPasswordStrength()">
                                    <button type="button" onclick="togglePassword('password')"
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg id="eye-password" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                {{-- Strength bar --}}
                                <div class="mt-2">
                                    <div id="strength-bar" class="password-strength bg-gray-200 w-0"></div>
                                    <p id="strength-text" class="text-xs text-gray-400 mt-1">Minimal 6 karakter</p>
                                </div>
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="mb-6">
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           required minlength="6"
                                           class="input-field w-full pl-12 pr-12 py-3 rounded-xl text-gray-800 text-sm"
                                           placeholder="Ketik ulang password"
                                           autocomplete="new-password"
                                           oninput="checkPasswordMatch()">
                                    <button type="button" onclick="togglePassword('password_confirmation')"
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg id="eye-confirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <p id="match-msg" class="text-xs mt-1 hidden"></p>
                            </div>

                            {{-- Submit --}}
                            <button type="submit" id="submitBtn"
                                    class="btn-primary w-full text-white py-3.5 rounded-xl font-semibold text-base shadow-lg">
                                Daftar Sekarang
                            </button>
                        </form>

                        {{-- Login Link --}}
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-500">
                                Sudah punya akun?
                                <a href="{{ route('login') }}" class="font-semibold text-orange-600 hover:text-orange-700">
                                    Login di sini
                                </a>
                            </p>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-400">
                                Dengan mendaftar, Anda menyetujui syarat dan ketentuan yang berlaku.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        /* ── Toggle Password ──────────────────────────────── */
        function togglePassword(fieldId) {
            const field   = document.getElementById(fieldId);
            const iconId  = fieldId === 'password' ? 'eye-password' : 'eye-confirmation';
            const icon    = document.getElementById(iconId);

            const eyeOpen = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                       -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            const eyeSlash = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                       a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
                       M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29
                       m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                       m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7
                       a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;

            if (field.type === 'password') {
                field.type = 'text';
                icon.innerHTML = eyeSlash;
            } else {
                field.type = 'password';
                icon.innerHTML = eyeOpen;
            }
        }

        /* ── Password Strength ────────────────────────────── */
        function checkPasswordStrength() {
            const val  = document.getElementById('password').value;
            const bar  = document.getElementById('strength-bar');
            const text = document.getElementById('strength-text');

            let score = 0;
            if (val.length >= 6)                              score++;
            if (val.length >= 8)                              score++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val))      score++;
            if (/\d/.test(val))                               score++;
            if (/[^a-zA-Z\d]/.test(val))                     score++;

            const map = [
                { label: 'Sangat Lemah', color: 'bg-red-400',    cls: 'text-red-500' },
                { label: 'Lemah',        color: 'bg-red-500',    cls: 'text-red-600' },
                { label: 'Sedang',       color: 'bg-yellow-400', cls: 'text-yellow-600' },
                { label: 'Kuat',         color: 'bg-green-400',  cls: 'text-green-600' },
                { label: 'Sangat Kuat',  color: 'bg-green-500',  cls: 'text-green-700' },
            ];

            const level = map[Math.max(0, score - 1)] || map[0];

            bar.className    = `password-strength ${val ? level.color : 'bg-gray-200'}`;
            bar.style.width  = val ? (score * 20) + '%' : '0';
            text.textContent = val ? `Kekuatan: ${level.label}` : 'Minimal 6 karakter';
            text.className   = `text-xs mt-1 ${val ? level.cls : 'text-gray-400'}`;
        }

        /* ── Password Match ───────────────────────────────── */
        function checkPasswordMatch() {
            const pw   = document.getElementById('password').value;
            const conf = document.getElementById('password_confirmation').value;
            const msg  = document.getElementById('match-msg');

            if (!conf) { msg.classList.add('hidden'); return; }
            msg.classList.remove('hidden');

            if (pw === conf) {
                msg.textContent = '✓ Password cocok';
                msg.className   = 'text-xs mt-1 text-green-600';
            } else {
                msg.textContent = '✗ Password tidak cocok';
                msg.className   = 'text-xs mt-1 text-red-600';
            }
        }

        /* ── Client-side validation ───────────────────────── */
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const pw   = document.getElementById('password').value;
            const conf = document.getElementById('password_confirmation').value;

            if (pw !== conf) {
                e.preventDefault();
                document.getElementById('password_confirmation').focus();
                const msg = document.getElementById('match-msg');
                msg.classList.remove('hidden');
                msg.textContent = '✗ Password tidak cocok';
                msg.className   = 'text-xs mt-1 text-red-600';
                return false;
            }

            if (pw.length < 6) {
                e.preventDefault();
                document.getElementById('password').focus();
                return false;
            }
        });

        /* ── Auto-focus ───────────────────────────────────── */
        window.addEventListener('load', function () {
            @if($errors->has('nama'))
                document.getElementById('nama').focus();
            @elseif($errors->has('email'))
                document.getElementById('email').focus();
            @elseif($errors->has('password'))
                document.getElementById('password').focus();
            @endif
        });
    </script>
</body>
</html>
