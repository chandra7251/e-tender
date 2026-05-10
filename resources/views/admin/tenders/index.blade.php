@extends('layouts.admin')

@section('title', 'Tender Management')
@section('page-title', 'Tender Management')

@section('content')
<div class="space-y-4">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">{{ $tenders->total() }} tender ditemukan</p>
        <a href="{{ route('admin.tenders.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium
                  text-white hover:bg-indigo-500 transition-colors duration-150">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Buat Tender
        </a>
    </div>

    {{-- ── Filter & Search ─────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('admin.tenders.index') }}"
          class="flex flex-col gap-3 sm:flex-row sm:items-center">

        <div class="relative flex-1">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-500">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
            </span>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari judul tender..."
                   class="w-full rounded-lg border border-gray-700 bg-gray-800 py-2 pl-9 pr-4 text-sm
                          text-gray-100 placeholder-gray-600 outline-none
                          focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <select name="status"
                class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm
                       text-gray-100 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            <option value="">Semua Status</option>
            @foreach (['draft','open','aanwijzing','bidding','closed','finished'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>

        <button type="submit"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white
                       hover:bg-indigo-500 transition-colors duration-150">
            Filter
        </button>

        @if (request('search') || request('status'))
            <a href="{{ route('admin.tenders.index') }}"
               class="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-400
                      hover:text-white transition-colors duration-150">
                Reset
            </a>
        @endif
    </form>

    {{-- ── Table ──────────────────────────────────────────────────────────── --}}
    <div class="overflow-x-auto rounded-xl border border-gray-800 bg-gray-900">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-800 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">
                    <th class="px-5 py-3">Judul</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Mulai</th>
                    <th class="px-5 py-3">Selesai</th>
                    <th class="px-5 py-3">Bidding Start</th>
                    <th class="px-5 py-3">Bidding End</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($tenders as $tender)
                    <tr class="hover:bg-gray-800/40 transition-colors duration-100">
                        <td class="px-5 py-3 font-medium text-gray-100 max-w-xs truncate">
                            {{ $tender->title }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $badge = match($tender->status) {
                                    'open'       => 'bg-sky-900/50 text-sky-400 border-sky-700',
                                    'aanwijzing' => 'bg-violet-900/50 text-violet-400 border-violet-700',
                                    'bidding'    => 'bg-amber-900/50 text-amber-400 border-amber-700',
                                    'closed'     => 'bg-gray-700/50 text-gray-400 border-gray-600',
                                    'finished'   => 'bg-emerald-900/50 text-emerald-400 border-emerald-700',
                                    default      => 'bg-gray-800/50 text-gray-500 border-gray-700',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                                {{ ucfirst($tender->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-400">
                            {{ $tender->start_date?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-gray-400">
                            {{ $tender->end_date?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-gray-400">
                            {{ $tender->bidding_start?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-gray-400">
                            {{ $tender->bidding_end?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.tenders.show', $tender) }}"
                                   class="rounded-md bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-300
                                          hover:bg-indigo-600 hover:text-white transition-colors duration-150">
                                    Detail
                                </a>
                                <a href="{{ route('admin.tenders.edit', $tender) }}"
                                   class="rounded-md bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-300
                                          hover:bg-gray-700 hover:text-white transition-colors duration-150">
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-600">
                            Tidak ada tender ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($tenders->hasPages())
        <div class="text-sm text-gray-500">{{ $tenders->links() }}</div>
    @endif

</div>
@endsection
