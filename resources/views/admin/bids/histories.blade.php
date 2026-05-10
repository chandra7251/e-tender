@extends('layouts.admin')

@section('title', 'Histori Bid')
@section('page-title', 'Histori Bid')

@section('content')
<div class="max-w-2xl space-y-4">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="hover:text-gray-300 transition-colors">{{ Str::limit($tender->title, 40) }}</a>
        <span>/</span>
        <a href="{{ route('admin.tenders.bids.index', $tender) }}"
           class="hover:text-gray-300 transition-colors">Bid Monitoring</a>
        <span>/</span>
        <span class="text-gray-400">Histori</span>
    </div>

    {{-- Bid summary card --}}
    <div class="rounded-xl border border-gray-800 bg-gray-900 p-5">
        <p class="text-xs text-gray-500 mb-1">Vendor</p>
        <p class="text-base font-semibold text-gray-100">{{ $bid->vendor->company_name ?? '-' }}</p>
        <div class="mt-3 flex items-center gap-6 text-sm">
            <div>
                <p class="text-xs text-gray-600">Bid Saat Ini</p>
                <p class="font-mono font-bold text-teal-400">
                    Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-600">Submitted</p>
                <p class="text-gray-300">{{ $bid->submitted_at?->format('d M Y, H:i') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600">Terakhir diubah</p>
                <p class="text-gray-300">{{ $bid->updated_at?->format('d M Y, H:i') ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- History timeline --}}
    <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
        <h2 class="mb-4 text-sm font-semibold text-gray-300">
            Riwayat Perubahan
            <span class="ml-2 text-xs font-normal text-gray-600">({{ $histories->count() }} entri)</span>
        </h2>

        @if ($histories->isEmpty())
            <p class="text-sm text-gray-600">Belum ada riwayat perubahan bid ini.</p>
        @else
            <ol class="space-y-4">
                @foreach ($histories as $h)
                    <li class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-indigo-500
                                         ring-4 ring-indigo-900/30"></span>
                            @if (!$loop->last)
                                <span class="mt-1 w-px flex-1 bg-gray-800"></span>
                            @endif
                        </div>
                        <div class="pb-4">
                            <div class="flex items-center gap-3 flex-wrap">
                                @if ($h->old_bid_amount !== null)
                                    <span class="font-mono text-sm text-gray-500 line-through">
                                        Rp {{ number_format($h->old_bid_amount, 0, ',', '.') }}
                                    </span>
                                    <svg class="h-3 w-3 text-gray-600" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                              d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                @endif
                                <span class="font-mono text-sm font-semibold text-teal-400">
                                    Rp {{ number_format($h->new_bid_amount, 0, ',', '.') }}
                                </span>
                            </div>

                            @if ($h->notes)
                                <p class="mt-1 text-xs text-gray-500">{{ $h->notes }}</p>
                            @endif

                            <p class="mt-1 text-xs text-gray-700">
                                {{ $h->changed_at?->format('d M Y, H:i') ?? ($h->created_at?->format('d M Y, H:i') ?? '-') }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

</div>
@endsection
