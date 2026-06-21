<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login — E-Tender</title>
    <link rel="icon" type="image/png" href="{{ asset('images/auth/favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .login-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
            background-color: #ffffff;
        }
        .left-section {
            width: 45%;
            background-color: #3553A8;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .right-section {
            width: 55%;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            position: relative;
        }

        /* ── Mobile Responsive ─────────────────────────────── */
        @media (max-width: 767px) {
            .login-container {
                flex-direction: column;
                background-color: #ffffff;
            }
            .left-section {
                display: none;
            }
            .right-section {
                width: 100%;
                min-height: 100vh;
                padding: 0;
                justify-content: flex-start;
            }
            .right-section > div.w-full {
                max-width: 100%;
                padding: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .mobile-logo {
                display: flex !important;
                width: 100%;
                background-color: #3553A8;
                padding: 3.5rem 1.5rem 2.5rem;
                justify-content: center;
                margin-bottom: 2rem;
            }
            .mobile-form-content {
                width: 100%;
                max-width: 400px;
                padding: 0 1.5rem 2rem;
            }
        }
        .wave-divider {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 200px;
            transform: translateX(1px); /* Prevent sub-pixel gap */
            color: #ffffff;
            z-index: 10;
        }
        
        /* Illustration CSS */
        .illustration-wrapper {
            position: relative;
            width: 300px;
            height: 250px;
            margin-top: 20px;
            z-index: 5;
            margin-right: 40px;
        }
        
        .card-bg {
            position: absolute;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        /* Form Styles */
        .form-input {
            width: 100%;
            border-radius: 9999px; /* full rounded */
            border: 1px solid #D1D5DB;
            padding: 14px 24px;
            font-size: 0.875rem;
            color: #374151;
            outline: none;
            transition: all 0.2s;
        }
        .form-input:focus {
            border-color: #28C5D4;
            box-shadow: 0 0 0 2px rgba(40, 197, 212, 0.2);
        }
        .form-input::placeholder {
            color: #9CA3AF;
        }
        .submit-btn {
            width: 100%;
            background-color: #28C5D4;
            color: white;
            border-radius: 9999px;
            padding: 14px 24px;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #22B2C0;
        }
        
        /* Hide native password toggle in browsers like Edge/Chrome */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear,
        input[type="password"]::-webkit-contacts-auto-fill-button,
        input[type="password"]::-webkit-credentials-auto-fill-button {
            display: none !important;
        }
    </style>
</head>
<body>

    <div class="login-container">
        
        <!-- Left Section (Blue) — hidden on mobile via CSS -->
        <div class="left-section">
            <!-- Wavy background divider (S-Curve matching Gambar 1) -->
            <svg class="wave-divider" preserveAspectRatio="none" viewBox="0 0 100 100" fill="currentColor">
                <path d="M100,0 L100,100 L60,100 C 150,70 -50,30 100,0 Z" />
            </svg>

            <!-- Logo -->
            <div class="absolute top-12 left-12 z-20">
                <img src="{{ asset('images/auth/logo.png') }}" alt="E-Tender Logo" class="h-20 w-auto">
            </div>

            <!-- Dashboard Illustration -->
            <div class="relative z-10 w-full max-w-[55%] flex justify-start items-center self-start ml-12 mt-32">
                <img src="{{ asset('images/auth/illustration.png') }}" alt="Dashboard Illustration" class="w-full h-auto object-contain rounded-xl">
            </div>
        </div>

        <!-- Right Section (White Form) -->
        <div class="right-section z-20">
            <div class="w-full max-w-md px-6">

                <!-- Mobile Logo — only shown on small screens -->
                <div class="mobile-logo hidden justify-center mb-8">
                    <img src="{{ asset('images/auth/logo.png') }}" alt="E-Tender Logo" class="h-20 w-auto drop-shadow-md">
                </div>
                
                <div class="mobile-form-content w-full">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-3 tracking-wide">Admin Login</h1>
                        <p class="text-gray-400 text-[15px] leading-relaxed">
                            Selamat datang di Sistem Admin. Masuk<br>untuk mengelola pelelangan.
                        </p>
                    </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl bg-red-50 border border-red-100 px-4 py-3 text-sm text-red-500 text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.post') }}" novalidate class="space-y-5">
                    @csrf
                    
                    <!-- Email Input -->
                    <div>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            placeholder="Masukkan Email Admin" 
                            class="form-input @error('email') border-red-400 @enderror"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-500 px-4">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="relative flex items-center">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                placeholder="••••••••••••••" 
                                class="form-input pr-12 @error('password') border-red-400 @enderror"
                            >
                            <button type="button" onclick="togglePassword()" class="absolute right-4 text-gray-300 hover:text-gray-500 focus:outline-none transition-colors">
                                <!-- Eye Icon (Open) -->
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                    <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clip-rule="evenodd" />
                                </svg>
                                <!-- Eye Icon (Closed) -->
                                <svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 hidden">
                                    <path d="M3.53 2.47a.75.75 0 00-1.06 1.06l18 18a.75.75 0 101.06-1.06l-18-18zM22.676 12.553a11.249 11.249 0 01-2.631 4.31l-3.099-3.099a5.25 5.25 0 00-6.71-6.71L7.759 4.577a11.217 11.217 0 014.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113z" />
                                    <path d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0115.75 12zM12.53 15.713l-4.243-4.244a3.75 3.75 0 004.243 4.243z" />
                                    <path d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 00-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 016.75 12z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-500 px-4">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="submit-btn">
                            Masuk
                        </button>
                    </div>

                </form>
                </div>

            </div>
        </div>
        
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
