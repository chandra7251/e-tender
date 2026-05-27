@extends('layouts.admin')

@section('title', 'Tender Management')
@section('page-title', 'Tender Management')

@section('content')
<div class="space-y-4">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm font-bold text-gray-600">{{ $tenders->total() }} Tender ditemukan</p>
        <a href="{{ route('admin.tenders.create') }}"
           class="inline-flex items-center gap-2 rounded-md bg-[#28C5D4] px-4 py-2.5 text-sm font-bold
                  text-white hover:bg-teal-400 transition-colors duration-150">
            <svg class="h-4 w-4 stroke-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Buat Tender
        </a>
    </div>

    {{-- ── Main Blue Card ─────────────────────────────────────────────────── --}}
    <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
        
        {{-- Filter & Search --}}
        <form method="GET" action="{{ route('admin.tenders.index') }}"
              class="flex flex-col gap-3 sm:flex-row sm:items-center mb-6">

            {{-- Status filter --}}
            <select name="status"
                    class="rounded-md border-0 bg-white px-4 py-2.5 text-sm font-medium
                           text-gray-700 outline-none focus:ring-2 focus:ring-[#2B438A]">
                <option value="">Semua Status</option>
                @foreach (['draft','open','aanwijzing','bidding','closed','finished'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="rounded-md bg-[#2B438A] px-6 py-2.5 text-sm font-semibold text-white
                           hover:bg-[#1E3066] transition-colors duration-150">
                Filter
            </button>

            @if (request('search') || request('status'))
                <a href="{{ route('admin.tenders.index') }}"
                   class="rounded-md border border-[#4A6BCC] px-4 py-2.5 text-sm text-indigo-200
                          hover:text-white transition-colors duration-150">
                    Reset
                </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#4A6BCC] text-left text-xs font-bold uppercase tracking-wider text-indigo-100">
                        <th class="px-2 py-4">Perusahaan</th>
                        <th class="px-2 py-4 text-center">Status</th>
                        <th class="px-2 py-4">Mulai</th>
                        <th class="px-2 py-4">Selesai</th>
                        <th class="px-2 py-4">Bidding Start</th>
                        <th class="px-2 py-4">Bidding End</th>
                        <th class="px-2 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#4A6BCC]">
                    @forelse ($tenders as $tender)
                        <tr class="hover:bg-[#2B438A] transition-colors duration-150">
                            <td class="px-2 py-4 font-semibold tracking-wide">
                                {{ $tender->title }}
                            </td>
                            <td class="px-2 py-4 text-center">
                                @php
                                    $badge = match($tender->status) {
                                        'open'       => 'bg-[#28C5D4] text-white',
                                        'aanwijzing' => 'bg-violet-500 text-white',
                                        'bidding'    => 'bg-[#F09459] text-white',
                                        'closed'     => 'bg-gray-500 text-white',
                                        'finished'   => 'bg-[#34D399] text-white',
                                        default      => 'bg-[#788B9A] text-white', // draft
                                    };
                                @endphp
                                <span class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-bold w-20 {{ $badge }}">
                                    {{ ucfirst($tender->status) }}
                                </span>
                            </td>
                            <td class="px-2 py-4 text-indigo-50 whitespace-nowrap">
                                {{ $tender->start_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-2 py-4 text-indigo-50 whitespace-nowrap">
                                {{ $tender->end_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-2 py-4 text-indigo-50 whitespace-nowrap">
                                {{ $tender->bidding_start?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-2 py-4 text-indigo-50 whitespace-nowrap">
                                {{ $tender->bidding_end?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-2 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.tenders.show', $tender) }}"
                                       class="rounded bg-[#2B438A] border border-[#4A6BCC] px-3 py-1.5 text-[11px] font-semibold text-white
                                              hover:bg-[#1E3066] transition-colors duration-150">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.tenders.edit', $tender) }}"
                                       class="rounded bg-[#2B438A] border border-[#4A6BCC] px-3 py-1.5 text-[11px] font-semibold text-white
                                              hover:bg-[#1E3066] transition-colors duration-150">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-2 py-10 text-center text-sm text-indigo-200">
                                Tidak ada tender ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($tenders->hasPages())
        <div class="mt-4">
            {{ $tenders->links() }}
        </div>
    @endif

</div>
@endsection
