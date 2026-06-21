@extends('layouts.admin')

@section('title', 'Monitoring Bid')
@section('page-title', 'Monitoring Bid')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-[#3553A8] hover:text-[#2B438A] transition-colors">
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

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="rounded-lg bg-[#3553A8] p-5 shadow flex flex-col justify-center">
            <p class="text-xs font-semibold text-indigo-200 uppercase tracking-wider mb-1">Total Bid</p>
            <p class="text-2xl font-bold text-white">{{ $bids->count() }}</p>
        </div>

        <div class="rounded-lg bg-white border-2 border-[#3553A8] p-5 shadow flex flex-col justify-center">
            <p class="text-xs font-semibold text-[#3553A8] uppercase tracking-wider mb-1">Bid Terendah</p>
            <p class="text-2xl font-bold text-[#3553A8]">
                {{ $bids->isNotEmpty() ? 'Rp ' . number_format($bids->first()->bid_amount, 0, ',', '.') : '-' }}
            </p>
        </div>

        <div class="rounded-lg bg-[#3553A8] p-5 shadow flex flex-col justify-center">
            <p class="text-xs font-semibold text-indigo-200 uppercase tracking-wider mb-1">Bid Tertinggi</p>
            <p class="text-2xl font-bold text-white">
                {{ $bids->isNotEmpty() ? 'Rp ' . number_format($bids->last()->bid_amount, 0, ',', '.') : '-' }}
            </p>
        </div>
    </div>

    <div class="rounded-lg bg-[#3553A8] shadow w-full">
        <table class="w-full text-left text-sm text-white">
            <thead>
                <tr class="border-b border-[#4A6BCC] text-xs font-bold uppercase tracking-wider text-indigo-200">
                    <th class="px-6 py-4">Rank</th>
                    <th class="px-6 py-4">Vendor</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4 text-right">Bid Amount</th>
                    <th class="px-6 py-4">Notes</th>
                    <th class="px-6 py-4">Submitted</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#4A6BCC]">
                @forelse ($bids as $i => $bid)
                    <tr class="{{ $bid->id === $lowestBidId ? 'bg-[#2B438A]' : 'hover:bg-[#2B438A]' }} transition-colors">
                        <td class="px-6 py-4">
                            @if ($bid->id === $lowestBidId)
                                <span class="inline-flex items-center gap-1 rounded bg-[#28C5D4] px-2.5 py-1 text-xs font-bold text-white shadow-sm">
                                    ★ Terendah
                                </span>
                            @else
                                <span class="text-indigo-200 font-bold">#{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold">
                            {{ $bid->vendor->company_name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-indigo-100">
                            {{ $bid->vendor->user->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-bold
                                   {{ $bid->id === $lowestBidId ? 'text-[#28C5D4]' : 'text-white' }}">
                            Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-indigo-100 max-w-xs">
                            {{ $bid->notes ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-indigo-100">
                            {{ $bid->submitted_at?->format('d M Y, H:i') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.tenders.bids.histories', [$tender, $bid]) }}"
                               class="inline-block rounded bg-[#4A6BCC] border border-[#5b7edd] px-4 py-1.5 text-xs font-bold text-white
                                      hover:bg-[#5b7edd] shadow-sm transition-colors">
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
