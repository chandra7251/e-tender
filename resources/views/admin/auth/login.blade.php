<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login — E-Procurement</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex h-full min-h-screen items-center justify-center bg-gray-950">

    <div class="w-full max-w-sm px-4">

        {{-- Logo / Title --}}
        <div class="mb-8 text-center">
            <span class="text-2xl font-bold tracking-tight text-indigo-400">E-Procurement</span>
            <p class="mt-1 text-sm text-gray-500">Admin Panel</p>
        </div>

        {{-- Card --}}
        <div class="rounded-xl bg-gray-900 border border-gray-800 p-8 shadow-2xl">

            <h2 class="mb-6 text-lg font-semibold text-gray-100">Masuk ke Admin</h2>

            {{-- Global error (role mismatch, etc.) --}}
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-900/50 border border-red-700 px-4 py-3 text-sm text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" novalidate>
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-300">
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="admin@example.com"
                        class="w-full rounded-lg bg-gray-800 border border-gray-700 px-3.5 py-2.5 text-sm
                               text-gray-100 placeholder-gray-600 outline-none
                               focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                               @error('email') border-red-600 @enderror
                               transition-colors duration-150"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-300">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full rounded-lg bg-gray-800 border border-gray-700 px-3.5 py-2.5 text-sm
                               text-gray-100 placeholder-gray-600 outline-none
                               focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                               @error('password') border-red-600 @enderror
                               transition-colors duration-150"
                    >
                    @error('password')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white
                           hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none
                           focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900
                           transition-colors duration-150">
                    Masuk
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-gray-600">
            E-Procurement System &copy; {{ date('Y') }}
        </p>
    </div>

</body>
</html>
