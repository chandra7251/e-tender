@extends('layouts.admin')

@section('title', 'Monitoring Bid')
@section('page-title', 'Monitoring Bid')

@section('content')
<div class="space-y-4">

    {{-- Back + header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-300 transition-colors">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Kembali ke Detail Tender
        </a>
        <span class="text-sm font-medium text-gray-400 truncate">{{ $tender->title }}</span>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-800 bg-gray-900 p-4">
            <p class="text-xs text-gray-500">Total Bid</p>
            <p class="mt-1 text-2xl font-bold text-indigo-400">{{ $bids->count() }}</p>
        </div>
        <div class="rounded-xl border border-teal-800 bg-teal-900/30 p-4">
            <p class="text-xs text-gray-500">Bid Terendah</p>
            <p class="mt-1 text-2xl font-bold text-teal-400">
                {{ $bids->isNotEmpty() ? 'Rp ' . number_format($bids->first()->bid_amount, 0, ',', '.') : '—' }}
            </p>
        </div>
        <div class="rounded-xl border border-gray-800 bg-gray-900 p-4">
            <p class="text-xs text-gray-500">Bid Tertinggi</p>
            <p class="mt-1 text-2xl font-bold text-gray-300">
                {{ $bids->isNotEmpty() ? 'Rp ' . number_format($bids->last()->bid_amount, 0, ',', '.') : '—' }}
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-xl border border-gray-800 bg-gray-900">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-800 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">
                    <th class="px-5 py-3">Rank</th>
                    <th class="px-5 py-3">Vendor</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3 text-right">Bid Amount</th>
                    <th class="px-5 py-3">Notes</th>
                    <th class="px-5 py-3">Submitted</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($bids as $i => $bid)
                    <tr class="{{ $bid->id === $lowestBidId ? 'bg-teal-900/20' : 'hover:bg-gray-800/40' }} transition-colors duration-100">
                        <td class="px-5 py-3">
                            @if ($bid->id === $lowestBidId)
                                <span class="inline-flex items-center gap-1 rounded-full border border-teal-700
                                             bg-teal-900/50 px-2.5 py-0.5 text-xs font-semibold text-teal-400">
                                    ★ Terendah
                                </span>
                            @else
                                <span class="text-gray-600">#{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-100">
                            {{ $bid->vendor->company_name ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-gray-400">
                            {{ $bid->vendor->user->email ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-right font-mono font-semibold
                                   {{ $bid->id === $lowestBidId ? 'text-teal-400' : 'text-gray-100' }}">
                            Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-gray-500 max-w-xs truncate">
                            {{ $bid->notes ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ $bid->submitted_at?->format('d M Y, H:i') ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.tenders.bids.histories', [$tender, $bid]) }}"
                               class="rounded-md bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-300
                                      hover:bg-indigo-600 hover:text-white transition-colors duration-150">
                                Histori
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-600">
                            Belum ada bid masuk pada tender ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
