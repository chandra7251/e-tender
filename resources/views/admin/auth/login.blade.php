<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login — E-Tender</title>
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
            background-color: #3553A8; /* Adjusted blue to better match the image */
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
    </style>
</head>
<body>

    <div class="login-container">
        
        <!-- Left Section (Blue) -->
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
                            <button type="button" class="absolute right-4 text-gray-300 hover:text-gray-500 focus:outline-none transition-colors">
                                <!-- Eye Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                    <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clip-rule="evenodd" />
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

                    <!-- Forgot Password -->
                    <div class="text-center mt-5">
                        <a href="#" class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                            Lupa kata sandi?
                        </a>
                    </div>
                </form>

            </div>
        </div>
        
    </div>

</body>
</html>
