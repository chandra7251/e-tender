@extends('layouts.admin')

@section('title', 'Generate Purchase Order')
@section('page-title', 'Generate Purchase Order')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.tenders.result.show', $tender) }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#3553A8] hover:text-[#2B438A] transition-colors">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Kembali ke Hasil Tender
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-red-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-sm font-bold text-red-800">Terdapat kesalahan pada isian form:</h3>
                    <ul class="mt-1 list-disc list-inside text-sm text-red-700 space-y-0.5">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4 border-b border-gray-100 pb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#28C5D4]/10 text-[#28C5D4]">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">Vendor Pemenang</h2>
                        <p class="text-xs text-gray-500">Penerima Purchase Order</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nama Perusahaan</p>
                        <p class="text-base font-bold text-gray-900">{{ $result->winner->company_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Nilai Pemenang (Bid)</p>
                        <p class="text-lg font-bold text-[#3553A8]">Rp {{ number_format($result->winning_bid_amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tender Terkait</p>
                        <p class="text-sm font-medium text-gray-700 leading-snug">{{ $tender->title }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-[#3553A8]/20 bg-[#3553A8]/5 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-[#3553A8] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-[#3553A8]">Informasi Penting</p>
                        <p class="text-xs text-[#3553A8] mt-1.5 leading-relaxed opacity-90">Pastikan nilai nominal dan deskripsi telah sesuai dengan persetujuan final sebelum menerbitkan dokumen PO ini. Dokumen yang telah diterbitkan akan bersifat mengikat dan menjadi dasar penagihan resmi (Invoice).</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.tenders.purchase-order.store', $tender) }}" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden h-full flex flex-col">
                @csrf
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-5">
                    <h2 class="text-lg font-bold text-gray-800">Detail Purchase Order</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Lengkapi form di bawah ini untuk menerbitkan surat pemesanan</p>
                </div>

                <div class="p-6 space-y-6 flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label for="po_number" class="mb-1.5 block text-sm font-semibold text-gray-700">
                                Nomor PO <span class="text-red-500">*</span>
                            </label>
                            <input id="po_number" type="text" name="po_number"
                                   value="{{ old('po_number', $suggestedPoNumber) }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 
                                          shadow-sm font-mono outline-none focus:border-[#3553A8] focus:ring-2 focus:ring-[#3553A8]/20 transition-all
                                          @error('po_number') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        </div>

                        <div>
                            <label for="issued_date" class="mb-1.5 block text-sm font-semibold text-gray-700">
                                Tanggal Terbit <span class="text-red-500">*</span>
                            </label>
                            <input id="issued_date" type="date" name="issued_date"
                                   value="{{ old('issued_date', now()->format('Y-m-d')) }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 
                                          shadow-sm outline-none focus:border-[#3553A8] focus:ring-2 focus:ring-[#3553A8]/20 transition-all
                                          @error('issued_date') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        </div>
                    </div>

                    <div>
                        <label for="amount" class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Total Nilai PO (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-gray-500 font-bold sm:text-sm">Rp</span>
                            </div>
                            <input id="amount" type="number" name="amount" min="0" step="1"
                                   value="{{ old('amount', $result->winning_bid_amount) }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white pl-12 pr-4 py-2.5 text-base font-bold text-[#3553A8] 
                                          shadow-sm outline-none focus:border-[#3553A8] focus:ring-2 focus:ring-[#3553A8]/20 transition-all
                                          @error('amount') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500">Otomatis diisi dengan nilai bid pemenang. Dapat disesuaikan jika ada negosiasi final.</p>
                    </div>

                    <div>
                        <label for="notes" class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Catatan Tambahan <span class="text-xs text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <textarea id="notes" name="notes" rows="4"
                                  placeholder="Termin pembayaran, garansi, atau instruksi pengiriman..."
                                  class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 
                                         shadow-sm outline-none focus:border-[#3553A8] focus:ring-2 focus:ring-[#3553A8]/20 transition-all">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="border-t border-gray-100 bg-gray-50/80 px-6 py-4 flex items-center justify-end gap-3 mt-auto">
                    <a href="{{ route('admin.tenders.result.show', $tender) }}"
                       class="rounded-lg px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-[#3553A8] px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-[#2B438A] transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-[#3553A8]">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" />
                        </svg>
                        Terbitkan Purchase Order
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
