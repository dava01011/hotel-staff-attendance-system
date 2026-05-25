<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Combo Festival Citylink Bandung Absensi</title>
    <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }

        html, body { min-height: 100%; width: 100%; }

        body {
            background: #F8FAFC; /* Light gray background like Talenta */
            overflow-x: hidden;
            overflow-y: auto;
            color: #1E293B;
        }

        .container-wrapper {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            width: 100%;
        }

        .left-panel {
            background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); /* Orange gradient */
            color: white;
        }

        .input-field {
            transition: all 0.2s ease;
            border: 1px solid #E2E8F0;
            background-color: #FFFFFF;
        }

        .input-field:focus {
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
            outline: none;
        }

        .btn-primary {
            background-color: #ea580c; /* Orange */
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #c2410c;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.2);
        }

        .btn-primary:active { transform: translateY(0); }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .brand-text {
            color: #ea580c;
        }

        /* Rate limit countdown */
        #countdown-wrap { display: none; }
        #countdown-wrap.show { display: flex; }
    </style>
</head>

<body>
    <div class="container-wrapper">
        <div class="flex w-full min-h-screen">
            
            {{-- ── Left Side (Desktop Branding) ──────────────────── --}}
            <div class="hidden lg:flex w-1/2 left-panel flex-col justify-center p-12 relative">
                <div class="absolute top-12 left-12">
                    <div class="bg-white rounded-lg inline-block p-2">
                        <img src="{{ asset('img/Logo.png') }}" alt="Logo" class="h-12 object-contain">
                    </div>
                </div>
                
                <div class="space-y-6 max-w-lg mt-12">
                    <h1 class="text-4xl font-bold leading-tight text-white">
                        Combo Festival Citylink Bandung Absensi
                    </h1>
                </div>

                <div class="absolute bottom-12 left-12 text-orange-200 text-sm font-medium">
                    &copy; {{ date('Y') }} Present by Hellokeypi. All rights reserved.
                </div>
            </div>

            {{-- ── Right Side (Login Form) ───────────────────────────── --}}
            <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-white">
                <div class="w-full max-w-md">

                    {{-- Mobile Logo --}}
                    <div class="lg:hidden flex justify-center mb-8">
                        <img src="{{ asset('img/Logo.png') }}" alt="Logo" class="h-16 object-contain">
                    </div>

                    {{-- Header --}}
                    <div class="mb-8">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Sign in</h2>
                    </div>

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 p-4 rounded-lg flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    @endif

                    {{-- Error Messages --}}
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 p-4 rounded-lg flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <ul class="text-sm text-red-800 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Login Form --}}
                    <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                        @csrf

                        {{-- Email --}}
                        <div class="mb-5">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Pribadi / Perusahaan
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input type="email" id="email" name="email"
                                       value="{{ old('email') }}" required
                                       class="input-field w-full pl-10 pr-4 py-3 rounded-lg text-gray-800 text-sm"
                                       placeholder="contoh@email.com"
                                       autocomplete="email">
                            </div>
                            @error('email')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-5">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input type="password" id="password" name="password" required
                                       class="input-field w-full pl-10 pr-10 py-3 rounded-lg text-gray-800 text-sm"
                                       placeholder="Masukkan kata sandi Anda"
                                       autocomplete="current-password">
                                <button type="button" onclick="togglePassword()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Remember Me & Forgot Password --}}
                        <div class="flex items-center justify-between mb-6">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <div class="relative">
                                    <input type="checkbox" id="remember" name="remember"
                                           class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 rounded-full peer
                                                peer-checked:bg-[#ea580c]
                                                transition-colors duration-200"></div>
                                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow
                                                peer-checked:translate-x-4
                                                transition-transform duration-200"></div>
                                </div>
                                <span class="text-sm text-gray-600">Ingat saya</span>
                            </label>
                            
                            <!-- Option for forgot password (can add route later) -->
                            <a href="#" class="text-sm font-medium text-[#ea580c] hover:underline">Lupa kata sandi?</a>
                        </div>

                        {{-- Rate limit info --}}
                        <div id="countdown-wrap" class="mb-5 items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-xs text-amber-800 font-medium">
                                Terlalu banyak percobaan. Coba lagi dalam <strong id="countdown">0</strong> detik.
                            </p>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" id="submitBtn"
                                class="btn-primary w-full text-white py-3 rounded-lg font-semibold text-sm">
                            Masuk
                        </button>
                    </form>

                    {{-- Register Link --}}
                    {{-- 
                    <div class="mt-8 text-center pt-6 border-t border-gray-100">
                        <p class="text-sm text-gray-500">
                            Belum mendaftarkan perusahaan Anda?
                            <a href="{{ route('register') }}" class="font-semibold text-[#ea580c] hover:underline ml-1">
                                Daftar Sekarang
                            </a>
                        </p>
                    </div> 
                    --}}
                    
                    {{-- Mobile Footer --}}
                    <div class="lg:hidden mt-8 text-center text-gray-400 text-xs font-medium">
                        &copy; {{ date('Y') }} Present by Hellokeypi.<br>All rights reserved.
                    </div>

                </div>
            </div>

        </div>
    </div>

    <script>
        /* ── Toggle Password ────────────────────────────────── */
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eye-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                           a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
                           M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29
                           m7.532 7.532l3.29 3.29M3 3l3.59 3.59
                           m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7
                           a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                           -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }

        /* ── Rate limit countdown (jika ada error throttle) ── */
        @if($errors->has('email') && str_contains($errors->first('email'), 'detik'))
            @php
                preg_match('/(\d+) detik/', $errors->first('email'), $m);
                $secs = $m[1] ?? 60;
            @endphp
            (function () {
                let secs = {{ $secs }};
                const wrap   = document.getElementById('countdown-wrap');
                const label  = document.getElementById('countdown');
                const btn    = document.getElementById('submitBtn');

                wrap.classList.add('show');
                btn.disabled = true;
                label.textContent = secs;

                const timer = setInterval(() => {
                    secs--;
                    label.textContent = secs;
                    if (secs <= 0) {
                        clearInterval(timer);
                        wrap.classList.remove('show');
                        btn.disabled = false;
                    }
                }, 1000);
            })();
        @endif

        /* ── Auto-focus on error field ──────────────────────── */
        window.addEventListener('load', function () {
            @if($errors->has('email'))
                document.getElementById('email').focus();
            @elseif($errors->has('password'))
                document.getElementById('password').focus();
            @endif
        });
    </script>
</body>
</html>
