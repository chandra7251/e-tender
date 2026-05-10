@extends('layouts.admin')

@section('title', 'Generate Purchase Order')
@section('page-title', 'Generate Purchase Order')

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

    {{-- Winner summary --}}
    <div class="rounded-xl border border-gray-800 bg-gray-900 px-5 py-4">
        <p class="text-xs text-gray-500 mb-1">Vendor Pemenang</p>
        <p class="text-base font-semibold text-emerald-400">{{ $result->winner->company_name ?? '-' }}</p>
        <p class="text-sm text-gray-500 mt-0.5">Tender: {{ $tender->title }}</p>
    </div>

    {{-- PO Form --}}
    <form method="POST" action="{{ route('admin.tenders.purchase-order.store', $tender) }}" class="space-y-5">
        @csrf

        @if ($errors->any())
            <div class="rounded-lg border border-red-700 bg-red-900/40 px-4 py-3 text-sm text-red-300">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-gray-800 bg-gray-900 p-6 space-y-4">

            <div>
                <label for="po_number" class="mb-1.5 block text-sm font-medium text-gray-300">
                    Nomor PO <span class="text-red-500">*</span>
                </label>
                <input id="po_number" type="text" name="po_number"
                       value="{{ old('po_number', $suggestedPoNumber) }}"
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3.5 py-2.5 text-sm
                              text-gray-100 outline-none font-mono
                              focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                              @error('po_number') border-red-600 @enderror">
                @error('po_number')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="amount" class="mb-1.5 block text-sm font-medium text-gray-300">
                    Jumlah (Rp) <span class="text-red-500">*</span>
                </label>
                <input id="amount" type="number" name="amount" min="0" step="1"
                       value="{{ old('amount', $result->winning_bid_amount) }}"
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3.5 py-2.5 text-sm
                              text-gray-100 outline-none
                              focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                              @error('amount') border-red-600 @enderror">
                @error('amount')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="issued_date" class="mb-1.5 block text-sm font-medium text-gray-300">
                    Tanggal Terbit <span class="text-red-500">*</span>
                </label>
                <input id="issued_date" type="date" name="issued_date"
                       value="{{ old('issued_date', now()->format('Y-m-d')) }}"
                       class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3.5 py-2.5 text-sm
                              text-gray-100 outline-none
                              focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                              @error('issued_date') border-red-600 @enderror">
                @error('issued_date')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="notes" class="mb-1.5 block text-sm font-medium text-gray-300">
                    Catatan <span class="text-xs text-gray-500">(opsional)</span>
                </label>
                <textarea id="notes" name="notes" rows="3"
                          placeholder="Instruksi tambahan, syarat, dll..."
                          class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3.5 py-2.5 text-sm
                                 text-gray-100 placeholder-gray-600 outline-none
                                 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('notes') }}</textarea>
            </div>

        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.tenders.result.show', $tender) }}"
               class="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white
                           hover:bg-indigo-500 transition-colors duration-150">
                Generate PO
            </button>
        </div>
    </form>

</div>
@endsection
