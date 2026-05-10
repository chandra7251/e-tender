@extends('layouts.admin')

@section('title', 'Purchase Order')
@section('page-title', 'Purchase Order')

@section('content')
<div class="max-w-xl space-y-6">

    <a href="{{ route('admin.tenders.result.show', $tender) }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-300 transition-colors">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Hasil Tender
    </a>

    {{-- PO Card --}}
    <div class="rounded-xl border border-indigo-800 bg-indigo-900/20 p-6 space-y-4">

        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-indigo-500 uppercase tracking-widest font-semibold mb-1">Purchase Order</p>
                <p class="text-2xl font-bold font-mono text-indigo-300">{{ $po->po_number }}</p>
            </div>
            <span class="rounded-full border border-indigo-700 bg-indigo-900/50 px-3 py-1 text-xs font-semibold text-indigo-400">
                Issued
            </span>
        </div>

        <dl class="space-y-3 text-sm border-t border-gray-800 pt-4">
            <div class="flex justify-between">
                <dt class="text-gray-500">Tender</dt>
                <dd class="font-medium text-gray-100 text-right max-w-xs truncate">{{ $tender->title }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Vendor</dt>
                <dd class="text-gray-300">{{ $po->vendor->company_name ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Email Vendor</dt>
                <dd class="text-gray-400">{{ $po->vendor->user->email ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Jumlah</dt>
                <dd class="font-mono font-bold text-teal-400">
                    Rp {{ number_format($po->amount, 0, ',', '.') }}
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Tanggal Terbit</dt>
                <dd class="text-gray-300">{{ $po->issued_date?->format('d M Y') ?? '-' }}</dd>
            </div>
            @if ($po->notes)
                <div class="pt-1">
                    <dt class="text-gray-500 mb-1">Catatan</dt>
                    <dd class="rounded-lg border border-gray-800 bg-gray-800/40 p-3 text-gray-300 text-sm">
                        {{ $po->notes }}
                    </dd>
                </div>
            @endif
            <div class="flex justify-between pt-2 border-t border-gray-800">
                <dt class="text-gray-500">Digenerate oleh</dt>
                <dd class="text-gray-400">{{ $po->generator->name ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Dibuat</dt>
                <dd class="text-gray-400">{{ $po->created_at?->format('d M Y, H:i') ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
            Kembali ke Tender
        </a>
        <a href="{{ route('admin.tenders.histories.index', $tender) }}"
           class="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
            Lihat History
        </a>
    </div>

</div>
@endsection
