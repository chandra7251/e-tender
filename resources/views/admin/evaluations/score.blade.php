@extends('layouts.admin')

@section('title', 'Evaluasi Bid')
@section('page-title', 'Evaluasi Bid Vendor')

@section('content')
<div class="w-full space-y-6">

    <a href="{{ route('admin.tenders.show', $tender) }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors font-medium">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Detail Tender
    </a>

    <div class="rounded-xl bg-[#3553A8] px-5 py-4 shadow-sm">
        <p class="text-xs text-indigo-200">Tender</p>
        <p class="mt-0.5 text-base font-bold text-white">{{ $tender->title }}</p>
        <div class="mt-2 flex flex-wrap gap-2">
            @foreach ($criteria as $c)
                <span class="rounded-full bg-white/20 px-3 py-0.5 text-xs font-semibold text-white">
                    {{ $c->name }} ({{ number_format($c->weight, 0) }}%)
                </span>
            @endforeach
        </div>
    </div>

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.tenders.evaluations.store', $tender) }}">
        @csrf

        <div class="space-y-4">
            @foreach ($bids as $bid)
            @php
                $existingEvaluations = $bid->evaluations->keyBy('criteria_id');
            @endphp
            <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-5 py-4 flex items-center justify-between bg-gray-50">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">{{ $bid->vendor->company_name ?? 'Vendor' }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Bid: <span class="font-mono font-bold text-[#3553A8]">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</span>
                            · Submit: {{ $bid->submitted_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <span class="rounded-full bg-[#3553A8]/10 px-3 py-1 text-xs font-bold text-[#3553A8]">
                        Bid #{{ $bid->id }}
                    </span>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-{{ min(count($criteria), 4) }} gap-4">
                        @foreach ($criteria as $c)
                        @php
                            $existing = $existingEvaluations->get($c->id);
                        @endphp
                        <div class="rounded-lg border border-gray-200 p-4">
                            <label class="block text-xs font-bold text-gray-700 mb-1">
                                {{ $c->name }}
                                <span class="font-normal text-gray-400">(maks: {{ $c->max_score }})</span>
                            </label>
                            <p class="text-[10px] text-gray-400 mb-2">Bobot: {{ number_format($c->weight, 0) }}%</p>
                            <input type="number"
                                   name="scores[{{ $bid->id }}][{{ $c->id }}][score]"
                                   value="{{ old("scores.{$bid->id}.{$c->id}.score", $existing?->score ?? '') }}"
                                   min="0" max="{{ $c->max_score }}" step="0.01" required
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm font-mono focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none"
                                   placeholder="0 - {{ $c->max_score }}">
                            <input type="text"
                                   name="scores[{{ $bid->id }}][{{ $c->id }}][notes]"
                                   value="{{ old("scores.{$bid->id}.{$c->id}.notes", $existing?->notes ?? '') }}"
                                   class="mt-2 w-full rounded-md border border-gray-200 px-3 py-1.5 text-xs focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none"
                                   placeholder="Catatan (opsional)">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('admin.tenders.show', $tender) }}"
               class="rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="rounded-md bg-[#28C5D4] px-6 py-2.5 text-sm font-bold text-white hover:bg-teal-400 transition-colors shadow-sm">
                ✓ Simpan Evaluasi
            </button>
        </div>
    </form>
</div>
@endsection
