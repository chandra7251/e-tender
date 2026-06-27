<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F0F2F5]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — E-Procurement</title>
    <link rel="icon" type="image/png" href="{{ asset('images/auth/favicon.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body class="h-full overflow-hidden">
<div class="flex h-full bg-[#F0F2F5] text-gray-800">

    <div id="sidebar-backdrop"
         class="fixed inset-0 z-40 bg-black/50 md:hidden"
         onclick="closeMobileSidebar()">
    </div>

    <aside class="hidden md:flex w-64 flex-col bg-[#3553A8] border-r border-[#4A6BCC] flex-shrink-0">

        <div class="flex h-[72px] items-center px-6 border-b border-[#4A6BCC]">
            <img src="{{ asset('images/auth/logo.png') }}" alt="E-Tender Logo" class="h-14 w-auto object-contain">
        </div>

        <nav class="flex-1 space-y-2 px-4 py-6 overflow-y-auto">
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

            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
            <a href="{{ route('admin.vendors.blacklist.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.vendors.blacklist*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M6.72 5.66l11.62 11.62A8.25 8.25 0 006.72 5.66zm12.68 10.56L7.78 4.6A8.25 8.25 0 0119.4 16.22zM12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25z" clip-rule="evenodd" />
                </svg>
                Blacklist
            </a>
            @endif

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

            <a href="{{ route('admin.audit-logs.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.audit-logs*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 1.5a.75.75 0 01.75.75V4.5a.75.75 0 01-1.5 0V2.25A.75.75 0 0112 1.5zM5.636 4.136a.75.75 0 011.06 0l1.592 1.591a.75.75 0 01-1.061 1.06l-1.591-1.59a.75.75 0 010-1.061zm12.728 0a.75.75 0 010 1.06l-1.591 1.592a.75.75 0 01-1.06-1.061l1.59-1.591a.75.75 0 011.061 0zm-6.816 4.496a.75.75 0 01.82.311l5.228 7.917a.75.75 0 01-.777 1.148l-2.097-.43 1.045 3.9a.75.75 0 01-1.45.388l-1.044-3.899-1.601 1.42a.75.75 0 01-1.247-.606l.569-9.47a.75.75 0 01.554-.679z" clip-rule="evenodd" />
                </svg>
                Audit Log
            </a>


            <a href="{{ route('admin.complaints.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                       {{ request()->routeIs('admin.complaints*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                </svg>
                <span class="flex-1">Sanggahan</span>
                @php $pendingComplaints = \App\Models\TenderComplaint::where('status','pending')->count(); @endphp
                @if($pendingComplaints > 0)
                    <span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-400 px-1.5 text-[10px] font-bold text-white">{{ $pendingComplaints }}</span>
                @endif
            </a>

            <a href="{{ route('admin.contracts.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                       {{ request()->routeIs('admin.contracts*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0118 9.375v9.375a3 3 0 003-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 00-.673-.05A3 3 0 0015 1.5h-1.5a3 3 0 00-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6zM13.5 3A1.5 1.5 0 0012 4.5h4.5A1.5 1.5 0 0015 3h-1.5z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 20.625V9.375zm9.586 4.594a.75.75 0 00-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 00-1.06 1.06l1.5 1.5a.75.75 0 001.116-.062l3-3.75z" clip-rule="evenodd" />
                </svg>
                Kontrak
            </a>

            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                       {{ request()->routeIs('admin.settings*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 00-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 00-2.282.819l-.922 1.597a1.875 1.875 0 00.432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 000 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 00-.432 2.385l.922 1.597a1.875 1.875 0 002.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 002.28-.819l.923-1.597a1.875 1.875 0 00-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 000-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 00-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 00-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 00-1.85-1.567h-1.843zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" clip-rule="evenodd" />
                </svg>
                Pengaturan
            </a>

            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.reports*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.25 13.5a8.25 8.25 0 018.25-8.25.75.75 0 01.75.75v6.75H18a.75.75 0 01.75.75 8.25 8.25 0 01-16.5 0z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M12.75 3a.75.75 0 01.75-.75 8.25 8.25 0 018.25 8.25.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z" clip-rule="evenodd" />
                </svg>
                Laporan
            </a>
            <a href="{{ route('admin.catalogue.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                       {{ request()->routeIs('admin.catalogue*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z"/>
                    <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z"/>
                </svg>
                E-Catalogue
            </a>
            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                       {{ request()->routeIs('admin.users*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z" clip-rule="evenodd"/>
                    <path d="M5.082 14.254a8.287 8.287 0 00-1.308 5.135 9.687 9.687 0 01-1.764-.44l-.115-.04a.563.563 0 01-.373-.487l-.01-.121a3.75 3.75 0 013.57-4.047zM20.226 19.389a8.287 8.287 0 00-1.308-5.135 3.75 3.75 0 013.57 4.047l-.01.121a.563.563 0 01-.373.487l-.115.04c-.567.2-1.156.349-1.764.441z"/>
                </svg>
                User Admin
            </a>
            @endif
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

            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
            <a href="{{ route('admin.vendors.blacklist.index') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.vendors.blacklist*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M6.72 5.66l11.62 11.62A8.25 8.25 0 006.72 5.66zm12.68 10.56L7.78 4.6A8.25 8.25 0 0119.4 16.22zM12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25z" clip-rule="evenodd" />
                </svg>
                Blacklist
            </a>
            @endif

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

            <a href="{{ route('admin.audit-logs.index') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.audit-logs*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 1.5a.75.75 0 01.75.75V4.5a.75.75 0 01-1.5 0V2.25A.75.75 0 0112 1.5zM5.636 4.136a.75.75 0 011.06 0l1.592 1.591a.75.75 0 01-1.061 1.06l-1.591-1.59a.75.75 0 010-1.061zm12.728 0a.75.75 0 010 1.06l-1.591 1.592a.75.75 0 01-1.06-1.061l1.59-1.591a.75.75 0 011.061 0zm-6.816 4.496a.75.75 0 01.82.311l5.228 7.917a.75.75 0 01-.777 1.148l-2.097-.43 1.045 3.9a.75.75 0 01-1.45.388l-1.044-3.899-1.601 1.42a.75.75 0 01-1.247-.606l.569-9.47a.75.75 0 01.554-.679z" clip-rule="evenodd" />
                </svg>
                Audit Log
            </a>


            <a href="{{ route('admin.complaints.index') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                       request()->routeIs('admin.complaints*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' 
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                </svg>
                <span class="flex-1">Sanggahan</span>
                @php $pendingComplaints = \App\Models\TenderComplaint::where("status","pending")->count(); @endphp
                @if($pendingComplaints > 0)
                    <span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-400 px-1.5 text-[10px] font-bold text-white"> $pendingComplaints </span>
                @endif
            </a>

            <a href="{{ route('admin.contracts.index') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                       request()->routeIs('admin.contracts*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' 
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0118 9.375v9.375a3 3 0 003-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 00-.673-.05A3 3 0 0015 1.5h-1.5a3 3 0 00-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6zM13.5 3A1.5 1.5 0 0012 4.5h4.5A1.5 1.5 0 0015 3h-1.5z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 20.625V9.375zm9.586 4.594a.75.75 0 00-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 00-1.06 1.06l1.5 1.5a.75.75 0 001.116-.062l3-3.75z" clip-rule="evenodd" />
                </svg>
                Kontrak
            </a>

            <a href="{{ route('admin.settings.index') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                       request()->routeIs('admin.settings*') ? 'bg-white/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' 
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 00-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 00-2.282.819l-.922 1.597a1.875 1.875 0 00.432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 000 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 00-.432 2.385l.922 1.597a1.875 1.875 0 002.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 002.28-.819l.923-1.597a1.875 1.875 0 00-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 000-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 00-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 00-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 00-1.85-1.567h-1.843zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" clip-rule="evenodd" />
                </svg>
                Pengaturan
            </a>

            <a href="{{ route('admin.reports.index') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                      {{ request()->routeIs('admin.reports*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.25 13.5a8.25 8.25 0 018.25-8.25.75.75 0 01.75.75v6.75H18a.75.75 0 01.75.75 8.25 8.25 0 01-16.5 0z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M12.75 3a.75.75 0 01.75-.75 8.25 8.25 0 018.25 8.25.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z" clip-rule="evenodd" />
                </svg>
                Laporan
            </a>

            @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
            <a href="{{ route('admin.users.index') }}"
               onclick="closeMobileSidebar()"
               class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold
                       {{ request()->routeIs('admin.users*') ? 'bg-white text-[#3553A8]' : 'text-white hover:bg-[#2B438A]' }}
                      transition-colors duration-150">
                <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z" clip-rule="evenodd"/>
                    <path d="M5.082 14.254a8.287 8.287 0 00-1.308 5.135 9.687 9.687 0 01-1.764-.44l-.115-.04a.563.563 0 01-.373-.487l-.01-.121a3.75 3.75 0 013.57-4.047zM20.226 19.389a8.287 8.287 0 00-1.308-5.135 3.75 3.75 0 013.57 4.047l-.01.121a.563.563 0 01-.373.487l-.115.04c-.567.2-1.156.349-1.764.441z"/>
                </svg>
                User Admin
            </a>
            @endif
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

                    {{-- Language Switcher --}}
                    <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1 text-xs font-semibold">
                        <a href="?lang=id" class="px-2 py-1 rounded {{ app()->getLocale() === 'id' ? 'bg-white shadow text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">ID</a>
                        <a href="?lang=en" class="px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-white shadow text-blue-700' : 'text-gray-500 hover:text-gray-700' }}">EN</a>
                    </div>

                    <div class="h-9 w-9 md:h-10 md:w-10 overflow-hidden rounded-full border-2 border-white/20 flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=28C5D4&color=fff" alt="Avatar" class="h-full w-full object-cover">
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

    @stack('scripts')
</body>
</html>
