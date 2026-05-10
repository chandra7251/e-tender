@extends('layouts.admin')

@section('title', 'Hasil Tender')
@section('page-title', 'Hasil Tender')

@section('content')
<div class="max-w-2xl space-y-6">

    <a href="{{ route('admin.tenders.show', $tender) }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-300 transition-colors">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Detail Tender
    </a>

    {{-- Winner card --}}
    <div class="rounded-xl border border-emerald-800 bg-emerald-900/20 p-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-800 text-emerald-300 text-xl">
                ★
            </span>
            <div>
                <p class="text-xs text-emerald-600 uppercase tracking-widest font-semibold">Pemenang</p>
                <p class="text-xl font-bold text-emerald-300">{{ $result->winner->company_name ?? '-' }}</p>
            </div>
        </div>

        <dl class="space-y-3 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500">Email Vendor</dt>
                <dd class="text-gray-300">{{ $result->winner->user->email ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Tender</dt>
                <dd class="font-medium text-gray-100">{{ $tender->title }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Winning Bid</dt>
                <dd class="font-mono font-bold text-teal-400">
                    Rp {{ number_format($result->winning_bid_amount, 0, ',', '.') }}
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Metode Seleksi</dt>
                <dd class="text-gray-300">
                    {{ $result->selection_method === 'lowest_price' ? 'Harga Terendah' : 'Pertimbangan Admin' }}
                </dd>
            </div>
            @if ($result->notes)
                <div>
                    <dt class="text-gray-500 mb-1">Catatan</dt>
                    <dd class="rounded-lg border border-gray-800 bg-gray-800/40 p-3 text-gray-300">
                        {{ $result->notes }}
                    </dd>
                </div>
            @endif
            <div class="flex justify-between pt-2 border-t border-gray-800">
                <dt class="text-gray-500">Diputuskan oleh</dt>
                <dd class="text-gray-300">{{ $result->decider->name ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Tanggal</dt>
                <dd class="text-gray-300">{{ $result->decided_at?->format('d M Y, H:i') ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap items-center gap-3">
        @if ($result->purchaseOrder)
            <a href="{{ route('admin.tenders.purchase-order.show', $tender) }}"
               class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors">
                Lihat Purchase Order
            </a>
        @else
            <a href="{{ route('admin.tenders.purchase-order.create', $tender) }}"
               class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 transition-colors">
                + Generate PO
            </a>
        @endif

        @if ($tender->status !== 'finished')
            <form method="POST" action="{{ route('admin.tenders.finish', $tender) }}">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="rounded-lg border border-emerald-700 px-4 py-2 text-sm font-semibold text-emerald-400
                               hover:bg-emerald-900/40 transition-colors"
                        onclick="return confirm('Tandai tender sebagai Finished?')">
                    ✓ Mark as Finished
                </button>
            </form>
        @else
            <span class="rounded-full border border-emerald-700 bg-emerald-900/50 px-3 py-1 text-xs font-semibold text-emerald-400">
                Tender Finished
            </span>
        @endif

        <a href="{{ route('admin.tenders.histories.index', $tender) }}"
           class="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
            Lihat History
        </a>
    </div>

</div>
@endsection
