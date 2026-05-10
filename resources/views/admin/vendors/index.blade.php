@extends('layouts.admin')

@section('title', 'Vendor Management')
@section('page-title', 'Vendor Management')

@section('content')
<div class="space-y-4">

    {{-- ── Filter & Search Bar ─────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('admin.vendors.index') }}"
          class="flex flex-col gap-3 sm:flex-row sm:items-center">

        {{-- Search --}}
        <div class="relative flex-1">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-500">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama perusahaan atau email..."
                   class="w-full rounded-lg border border-gray-700 bg-gray-800 py-2 pl-9 pr-4 text-sm
                          text-gray-100 placeholder-gray-600 outline-none
                          focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        {{-- Status filter --}}
        <select name="status"
                class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm
                       text-gray-100 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            <option value="">Semua Status</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <button type="submit"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white
                       hover:bg-indigo-500 transition-colors duration-150">
            Filter
        </button>

        @if (request('search') || request('status'))
            <a href="{{ route('admin.vendors.index') }}"
               class="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-400
                      hover:text-white transition-colors duration-150">
                Reset
            </a>
        @endif
    </form>

    {{-- ── Table ──────────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-800 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">
                    <th class="px-5 py-3">Perusahaan</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Phone</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Terdaftar</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($vendors as $vendor)
                    <tr class="hover:bg-gray-800/40 transition-colors duration-100">
                        <td class="px-5 py-3 font-medium text-gray-100">
                            {{ $vendor->company_name }}
                        </td>
                        <td class="px-5 py-3 text-gray-400">
                            {{ $vendor->user->email ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-gray-400">
                            {{ $vendor->phone ?? '-' }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $badge = match($vendor->verification_status) {
                                    'approved' => 'bg-emerald-900/50 text-emerald-400 border-emerald-700',
                                    'rejected' => 'bg-red-900/50 text-red-400 border-red-700',
                                    default    => 'bg-amber-900/50 text-amber-400 border-amber-700',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                                {{ ucfirst($vendor->verification_status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ $vendor->created_at->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.vendors.show', $vendor) }}"
                               class="rounded-md bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-300
                                      hover:bg-indigo-600 hover:text-white transition-colors duration-150">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-600">
                            Tidak ada vendor ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($vendors->hasPages())
        <div class="text-sm text-gray-500">
            {{ $vendors->links() }}
        </div>
    @endif

</div>
@endsection
