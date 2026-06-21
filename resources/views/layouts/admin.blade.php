<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F0F2F5]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — E-Procurement</title>
    <link rel="icon" type="image/png" href="{{ asset('images/auth/favicon.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }

        /* ── Mobile Sidebar Drawer ────────────────────────── */
        #mobile-sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        #mobile-sidebar.open {
            transform: translateX(0);
        }
        #sidebar-backdrop {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        #sidebar-backdrop.open {
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>

<body class="h-full">
<div class="flex h-full min-h-screen bg-[#F0F2F5] text-gray-800">

    <div id="sidebar-backdrop"
         class="fixed inset-0 z-40 bg-black/50 md:hidden"
         onclick="closeMobileSidebar()">
    </div>

    <aside class="hidden md:flex w-64 flex-col bg-[#3553A8] border-r border-[#4A6BCC] flex-shrink-0">

        <div class="flex h-[72px] items-center px-6 border-b border-[#4A6BCC]">
            <img src="{{ asset('images/auth/logo.png') }}" alt="E-Tender Logo" class="h-14 w-auto object-contain">
        </div>

        <nav class="flex-1 space-y-2 px-4 py-6">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.dashboard') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h2.25a3 3 0 013 3v2.25a3 3 0 01-3 3H6a3 3 0 01-3-3V6zm9.75 0a3 3 0 013-3H18a3 3 0 013 3v2.25a3 3 0 01-3 3h-2.25a3 3 0 01-3-3V6zM3 15.75a3 3 0 013-3h2.25a3 3 0 013 3V18a3 3 0 01-3 3H6a3 3 0 01-3-3v-2.25zm9.75 0a3 3 0 013-3H18a3 3 0 013 3V18a3 3 0 01-3 3h-2.25a3 3 0 01-3-3v-2.25z" clip-rule="evenodd" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.vendors.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.vendors*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
                </svg>
                Vendor
            </a>

            <a href="{{ route('admin.submissions.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.submissions*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M6.912 3a3 3 0 00-2.868 2.118l-2.411 7.838a3 3 0 00-.133.882V18a3 3 0 003 3h15a3 3 0 003-3v-4.162c0-.299-.045-.596-.133-.882l-2.412-7.838A3 3 0 0017.088 3H6.912zm13.823 9.75H16.5a3 3 0 00-2.83 2h-3.34a3 3 0 00-2.83-2H3.265l1.943-6.317A1.5 1.5 0 016.912 4.5h10.176a1.5 1.5 0 011.434 1.433l1.943 6.317a.75.75 0 01-.23-.5z" clip-rule="evenodd"/></svg>
                <span class="flex-1">Pengajuan Vendor</span>
                @php
                    $pendingCount = \App\Models\VendorSubmission::where('status','pending')->count();
                @endphp
                @if ($pendingCount > 0)
                    <span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full
                                 bg-[#F09459] px-1.5 text-[10px] font-bold text-white">
                        {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.tenders.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.tenders*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd" />
                    <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                </svg>
                Tender
            </a>
        </nav>

        <div class="border-t border-[#4A6BCC] p-4">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium
                               text-white hover:bg-[#2B438A] transition-colors duration-150">
                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <aside id="mobile-sidebar"
           class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-[#3553A8] border-r border-[#4A6BCC] md:hidden">

        <div class="flex h-[72px] items-center justify-between px-5 border-b border-[#4A6BCC]">
            <img src="{{ asset('images/auth/logo.png') }}" alt="E-Tender Logo" class="h-12 w-auto object-contain">
            <button onclick="closeMobileSidebar()"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-white hover:bg-[#2B438A] transition-colors">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-2 px-4 py-6">
            <a href="{{ route('admin.dashboard') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.dashboard') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h2.25a3 3 0 013 3v2.25a3 3 0 01-3 3H6a3 3 0 01-3-3V6zm9.75 0a3 3 0 013-3H18a3 3 0 013 3v2.25a3 3 0 01-3 3h-2.25a3 3 0 01-3-3V6zM3 15.75a3 3 0 013-3h2.25a3 3 0 013 3V18a3 3 0 01-3 3H6a3 3 0 01-3-3v-2.25zm9.75 0a3 3 0 013-3H18a3 3 0 013 3V18a3 3 0 01-3 3h-2.25a3 3 0 01-3-3v-2.25z" clip-rule="evenodd" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.vendors.index') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.vendors*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
                </svg>
                Vendor
            </a>

            <a href="{{ route('admin.submissions.index') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.submissions*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M6.912 3a3 3 0 00-2.868 2.118l-2.411 7.838a3 3 0 00-.133.882V18a3 3 0 003 3h15a3 3 0 003-3v-4.162c0-.299-.045-.596-.133-.882l-2.412-7.838A3 3 0 0017.088 3H6.912zm13.823 9.75H16.5a3 3 0 00-2.83 2h-3.34a3 3 0 00-2.83-2H3.265l1.943-6.317A1.5 1.5 0 016.912 4.5h10.176a1.5 1.5 0 011.434 1.433l1.943 6.317a.75.75 0 01-.23-.5z" clip-rule="evenodd"/></svg>
                <span class="flex-1">Pengajuan Vendor</span>
                @if ($pendingCount > 0)
                    <span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full
                                 bg-[#F09459] px-1.5 text-[10px] font-bold text-white">
                        {{ $pendingCount > 99 ? '99+' : $pendingCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.tenders.index') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.tenders*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd" />
                    <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                </svg>
                Tender
            </a>
        </nav>

        <div class="border-t border-[#4A6BCC] p-4">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium
                               text-white hover:bg-[#2B438A] transition-colors duration-150">
                    <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex flex-1 flex-col overflow-hidden min-w-0">

        <header class="flex h-[72px] items-center justify-between border-b border-[#4A6BCC] bg-[#3553A8] px-4 md:px-8">

            <div class="flex items-center gap-3">

                <button onclick="openMobileSidebar()"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-white hover:bg-[#2B438A]
                               transition-colors duration-150 md:hidden flex-shrink-0">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
                <h1 class="text-base md:text-xl font-bold text-white tracking-wide truncate">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <div class="flex items-center gap-2 md:gap-8 flex-shrink-0">
                <div class="flex items-center gap-2 md:gap-3">
                    <span class="hidden sm:block text-sm font-medium text-white truncate max-w-[120px] md:max-w-none">
                        {{ auth()->user()->name ?? 'Admin Tender' }}
                    </span>
                    <div class="h-9 w-9 md:h-10 md:w-10 overflow-hidden rounded-full border-2 border-white/20 flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name=Admin+Tender&background=28C5D4&color=fff" alt="Avatar" class="h-full w-full object-cover">
                    </div>
                </div>
            </div>
        </header>

        @if (session('success'))
            <div class="mx-4 md:mx-8 mt-6 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-600">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mx-4 md:mx-8 mt-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            @yield('content')
        </main>
    </div>

</div>

<script>
    function openMobileSidebar() {
        document.getElementById('mobile-sidebar').classList.add('open');
        document.getElementById('sidebar-backdrop').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeMobileSidebar() {
        document.getElementById('mobile-sidebar').classList.remove('open');
        document.getElementById('sidebar-backdrop').classList.remove('open');
        document.body.style.overflow = '';
    }
</script>

</body>
</html>
