@extends('layouts.admin')

@section('title', 'Histori Bid')
@section('page-title', 'Histori Bid')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-[#3553A8] font-semibold">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="hover:text-[#2B438A] transition-colors">{{ Str::limit($tender->title, 40) }}</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('admin.tenders.bids.index', $tender) }}"
           class="hover:text-[#2B438A] transition-colors">Bid Monitoring</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-700">Histori</span>
    </div>

    {{-- Bid summary card --}}
    <div class="rounded-lg bg-[#3553A8] p-6 shadow-md text-white">
        <p class="text-xs font-semibold text-indigo-200 uppercase tracking-wider mb-1">Vendor</p>
        <p class="text-xl font-bold mb-6">{{ $bid->vendor->company_name ?? '-' }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-indigo-200 uppercase tracking-wider mb-1 font-semibold">Bid Saat Ini</p>
                <p class="font-mono text-xl font-bold text-[#28C5D4]">
                    Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-xs text-indigo-200 uppercase tracking-wider mb-1 font-semibold">Submitted</p>
                <p class="text-sm text-white font-medium">
                    {{ $bid->submitted_at?->format('d M Y, H:i') ?? '-' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-indigo-200 uppercase tracking-wider mb-1 font-semibold">Terakhir diubah</p>
                <p class="text-sm text-white font-medium">
                    {{ $bid->updated_at?->format('d M Y, H:i') ?? '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- History timeline --}}
    <div class="rounded-lg bg-white border border-gray-200 shadow-md p-6 sm:p-8">
        <h2 class="mb-6 text-lg font-bold text-[#3553A8] border-b border-gray-200 pb-4">
            Riwayat Perubahan
            <span class="ml-2 inline-flex items-center justify-center rounded-full bg-[#3553A8] px-2.5 py-0.5 text-xs font-bold text-white">
                {{ $histories->count() }} entri
            </span>
        </h2>

        @if ($histories->isEmpty())
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-500">Belum ada riwayat perubahan bid ini.</h3>
            </div>
        @else
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach ($histories as $h)
                        <li>
                            <div class="relative pb-8">
                                @if (!$loop->last)
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-300" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-4">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-[#3553A8] flex items-center justify-center ring-4 ring-white">
                                            <div class="h-2.5 w-2.5 rounded-full bg-white"></div>
                                        </span>
                                    </div>
                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                        <div>
                                            <div class="flex items-center gap-3 flex-wrap mb-1">
                                                @if ($h->old_bid_amount !== null)
                                                    <span class="font-mono text-sm font-medium text-gray-400 line-through">
                                                        Rp {{ number_format($h->old_bid_amount, 0, ',', '.') }}
                                                    </span>
                                                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                                <span class="font-mono text-base font-bold text-[#3553A8]">
                                                    Rp {{ number_format($h->new_bid_amount, 0, ',', '.') }}
                                                </span>
                                            </div>

                                            @if ($h->notes)
                                                <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3 border border-gray-100 mt-2">{{ $h->notes }}</p>
                                            @endif
                                        </div>
                                        <div class="whitespace-nowrap text-right text-xs text-gray-500 font-medium">
                                            <time datetime="{{ $h->changed_at?->toIso8601String() }}">
                                                {{ $h->changed_at?->format('d M Y, H:i') ?? ($h->created_at?->format('d M Y, H:i') ?? '-') }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

</div>
@endsection
