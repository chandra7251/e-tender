@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ── Vendor Stats ──────────────────────────────────────────────────── --}}
    <div>
        <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-500">Vendor</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">

            @php
                $vendorCards = [
                    ['label' => 'Total Vendor', 'value' => $stats['vendor_total'],    'color' => 'text-indigo-400',  'bg' => 'bg-indigo-900/30',  'border' => 'border-indigo-800'],
                    ['label' => 'Pending',      'value' => $stats['vendor_pending'],  'color' => 'text-amber-400',   'bg' => 'bg-amber-900/30',   'border' => 'border-amber-800'],
                    ['label' => 'Approved',     'value' => $stats['vendor_approved'], 'color' => 'text-emerald-400', 'bg' => 'bg-emerald-900/30', 'border' => 'border-emerald-800'],
                    ['label' => 'Rejected',     'value' => $stats['vendor_rejected'], 'color' => 'text-red-400',     'bg' => 'bg-red-900/30',     'border' => 'border-red-800'],
                ];
            @endphp

            @foreach ($vendorCards as $card)
                <div class="rounded-xl border {{ $card['border'] }} {{ $card['bg'] }} p-5">
                    <p class="text-xs font-medium text-gray-500">{{ $card['label'] }}</p>
                    <p class="mt-2 text-3xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Tender Stats ───────────────────────────────────────────────────── --}}
    <div>
        <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-500">Tender</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">

            @php
                $tenderCards = [
                    ['label' => 'Total Tender',   'value' => $stats['tender_total'],    'color' => 'text-violet-400', 'bg' => 'bg-violet-900/30', 'border' => 'border-violet-800'],
                    ['label' => 'Tender Aktif',   'value' => $stats['tender_active'],   'color' => 'text-sky-400',    'bg' => 'bg-sky-900/30',    'border' => 'border-sky-800'],
                    ['label' => 'Tender Selesai', 'value' => $stats['tender_finished'], 'color' => 'text-gray-400',   'bg' => 'bg-gray-800/50',   'border' => 'border-gray-700'],
                ];
            @endphp

            @foreach ($tenderCards as $card)
                <div class="rounded-xl border {{ $card['border'] }} {{ $card['bg'] }} p-5">
                    <p class="text-xs font-medium text-gray-500">{{ $card['label'] }}</p>
                    <p class="mt-2 text-3xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Bidding Stats ──────────────────────────────────────────────────── --}}
    <div>
        <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-gray-500">Bidding & Peserta</h3>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-teal-800 bg-teal-900/30 p-5">
                <p class="text-xs font-medium text-gray-500">Total Bid</p>
                <p class="mt-2 text-3xl font-bold text-teal-400">{{ $stats['bid_total'] }}</p>
            </div>
            <div class="rounded-xl border border-cyan-800 bg-cyan-900/30 p-5">
                <p class="text-xs font-medium text-gray-500">Total Peserta</p>
                <p class="mt-2 text-3xl font-bold text-cyan-400">{{ $stats['participant_total'] }}</p>
            </div>
            <div class="rounded-xl border border-amber-800 bg-amber-900/30 p-5">
                <p class="text-xs font-medium text-gray-500">Tender Bidding</p>
                <p class="mt-2 text-3xl font-bold text-amber-400">{{ $stats['active_bidding_tenders'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-700 bg-gray-800/50 p-5">
                <p class="text-xs font-medium text-gray-500">Bid Terendah</p>
                <p class="mt-2 text-lg font-bold text-gray-200">
                    {{ $stats['lowest_bid'] ? 'Rp ' . number_format($stats['lowest_bid'], 0, ',', '.') : '—' }}
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

