@extends('layouts.admin')

@section('title', 'Monitoring Bid')
@section('page-title', 'Monitoring Bid')

@section('content')
<div class="space-y-6">

    {{-- Back + header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-gray-900 transition-colors">
            <svg class="h-4 w-4 stroke-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Kembali Ke Detail Tender
        </a>
        <div class="text-sm">
            <span class="font-bold text-[#3553A8] truncate max-w-sm inline-block">{{ $tender->title }}</span>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        {{-- Total Bid --}}
        <div class="rounded-md border border-[#3553A8] bg-[#3553A8] p-4 shadow-sm">
            <p class="text-xs font-medium text-white mb-1">Total Bid</p>
            <p class="text-xl font-bold text-white">{{ $bids->count() }}</p>
        </div>
        {{-- Bid Terendah --}}
        <div class="rounded-md border border-[#3553A8] bg-[#F0F2F5] p-4 shadow-sm">
            <p class="text-xs font-medium text-[#3553A8] mb-1">Bid Terendah</p>
            <p class="text-xl font-bold text-[#3553A8]">
                {{ $bids->isNotEmpty() ? 'Rp ' . number_format($bids->first()->bid_amount, 0, ',', '.') : '-' }}
            </p>
        </div>
        {{-- Bid Tertinggi --}}
        <div class="rounded-md border border-[#3553A8] bg-[#3553A8] p-4 shadow-sm">
            <p class="text-xs font-medium text-white mb-1">Bid Tertinggi</p>
            <p class="text-xl font-bold text-white">
                {{ $bids->isNotEmpty() ? 'Rp ' . number_format($bids->last()->bid_amount, 0, ',', '.') : '-' }}
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-md bg-[#3553A8] shadow-sm">
        <table class="w-full text-sm text-white">
            <thead>
                <tr class="border-b border-[#4A6BCC] text-left text-xs font-bold uppercase tracking-wider text-indigo-200">
                    <th class="px-6 py-4">Rank</th>
                    <th class="px-6 py-4">Vendor</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4 text-right">Bid Amount</th>
                    <th class="px-6 py-4">Notes</th>
                    <th class="px-6 py-4">Submitted</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#4A6BCC]">
                @forelse ($bids as $i => $bid)
                    <tr class="{{ $bid->id === $lowestBidId ? 'bg-[#2B438A]' : 'hover:bg-[#2B438A]' }} transition-colors duration-150">
                        <td class="px-6 py-4">
                            @if ($bid->id === $lowestBidId)
                                <span class="inline-flex items-center gap-1 rounded bg-[#28C5D4] px-2 py-1 text-xs font-bold text-white">
                                    ★ Terendah
                                </span>
                            @else
                                <span class="text-indigo-50 font-bold">#{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold tracking-wide">
                            {{ $bid->vendor->company_name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-indigo-50">
                            {{ $bid->vendor->user->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-bold
                                   {{ $bid->id === $lowestBidId ? 'text-[#28C5D4]' : 'text-white' }}">
                            Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-indigo-50 max-w-xs truncate">
                            {{ $bid->notes ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-indigo-50">
                            {{ $bid->submitted_at?->format('d M Y, H:i') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.tenders.bids.histories', [$tender, $bid]) }}"
                               class="rounded bg-[#2B438A] border border-[#4A6BCC] px-4 py-1.5 text-xs font-bold text-white
                                      hover:bg-[#1E3066] transition-colors duration-150">
                                Histori
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-indigo-200">
                            Belum ada bid masuk pada tender ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
