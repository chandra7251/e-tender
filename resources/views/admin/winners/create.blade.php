@extends('layouts.admin')

@section('title', 'Pilih Pemenang')
@section('page-title', 'Pilih Pemenang Tender')

@section('content')
<div class="w-full space-y-6">

    <a href="{{ route('admin.tenders.show', $tender) }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors font-medium">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Detail Tender
    </a>

    <form method="POST" action="{{ route('admin.tenders.winner.store', $tender) }}" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        @csrf

        <div class="xl:col-span-2 space-y-6">

            <div class="rounded-xl bg-[#3553A8] px-5 py-4 shadow-sm">
                <p class="text-xs text-indigo-200">Tender</p>
                <p class="mt-0.5 text-base font-bold text-white">{{ $tender->title }}</p>
            </div>

            @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Multi-criteria info banner --}}
            @if ($hasCriteria)
                <div class="rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-700 flex items-start gap-2">
                    <svg class="h-5 w-5 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-bold">Mode: Evaluasi Multi-Kriteria</p>
                        <p class="text-xs mt-0.5">Ranking berdasarkan total skor tertimbang (weighted score) dari {{ $criteria->count() }} kriteria. Vendor dengan skor tertinggi berada di urutan pertama.</p>
                        @if (!$isFullyEvaluated)
                            <p class="text-xs mt-1 text-amber-600 font-bold">⚠ Belum semua bid dievaluasi.
                                <a href="{{ route('admin.tenders.evaluations.create', $tender) }}" class="underline">Evaluasi dulu →</a>
                            </p>
                        @endif
                    </div>
                </div>
            @endif

        <div class="rounded-xl bg-[#3553A8] overflow-hidden shadow-sm">
            <div class="border-b border-[#4A6BCC] px-5 py-4">
                <h2 class="text-sm font-bold text-white">Pilih Bid Pemenang</h2>
                @if ($hasCriteria)
                    <p class="text-xs text-indigo-100 mt-1.5">
                        Urutan prioritas:
                        <span class="text-teal-300 font-medium">① Total Skor Tertimbang Tertinggi</span>
                    </p>
                @else
                    <p class="text-xs text-indigo-100 mt-1.5">
                        Urutan prioritas pemenang:
                        <span class="text-teal-300 font-medium">① Bid terendah</span>
                        &rarr;
                        <span class="text-blue-300 font-medium">② Waktu submit tercepat</span>
                        &rarr;
                        <span class="text-purple-300 font-medium">③ ULID (urutan masuk ke server)</span>
                    </p>
                    <p class="text-xs text-indigo-200/80 mt-1">
                        Jika dua vendor punya bid yang sama, vendor dengan waktu submit lebih awal otomatis di posisi teratas.
                    </p>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-white" style="min-width: 700px;">
                    <thead>
                        <tr class="border-b border-[#4A6BCC] text-left text-xs font-bold uppercase tracking-widest text-indigo-100">
                            <th class="px-5 py-3 w-8"></th>
                            <th class="px-5 py-3">Rank</th>
                            <th class="px-5 py-3">Vendor</th>
                            <th class="px-5 py-3 text-right">Bid Amount</th>
                            @if ($hasCriteria)
                                <th class="px-5 py-3 text-center">Total Skor</th>
                            @endif
                            <th class="px-5 py-3">Waktu Submit</th>
                            <th class="px-5 py-3">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#4A6BCC]">
                        @foreach ($bids as $i => $bid)
                        @php
                            $isTie = !$hasCriteria && $bids->where('bid_amount', $bid->bid_amount)->count() > 1;
                        @endphp
                        <tr class="hover:bg-[#2B438A] transition-colors duration-150 cursor-pointer"
                            onclick="document.getElementById('bid_{{ $bid->id }}').click()">
                            <td class="px-5 py-3">
                                <input type="radio" id="bid_{{ $bid->id }}" name="bid_id"
                                       value="{{ $bid->id }}"
                                       class="h-4 w-4 accent-[#28C5D4] cursor-pointer"
                                       {{ $i === 0 ? 'checked' : '' }}>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @if ($i === 0)
                                    <span class="rounded-full bg-[#28C5D4] px-2.5 py-0.5 text-xs font-bold text-white shadow-sm">
                                        ★ {{ $hasCriteria ? 'Skor Tertinggi' : 'Terendah' }}
                                    </span>
                                    @if ($isTie)
                                        <span class="ml-1 rounded-full bg-blue-500/80 px-2 py-0.5 text-xs font-bold text-white"
                                              title="Tie-breaker: dipilih karena submit lebih awal">
                                            ⚡ Tie-winner
                                        </span>
                                    @endif
                                @else
                                    <span class="text-indigo-200 font-semibold">#{{ $i + 1 }}</span>
                                    @if ($isTie && isset($bids[$i - 1]) && $bids[$i - 1]->bid_amount == $bid->bid_amount)
                                        <span class="ml-1 text-xs text-yellow-300 font-bold" title="Sama dengan bid di atasnya — kalah waktu submit">
                                            ⚠ Tie
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-5 py-3 font-semibold text-white whitespace-nowrap">
                                {{ $bid->vendor->company_name ?? '-' }}
                            </td>
                            <td class="px-5 py-3 text-right font-mono font-bold whitespace-nowrap
                                       {{ $i === 0 ? 'text-[#28C5D4]' : 'text-white' }}">
                                Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                            </td>
                            @if ($hasCriteria)
                            <td class="px-5 py-3 text-center">
                                @if ($bid->total_weighted_score !== null)
                                    <span class="text-lg font-bold {{ $i === 0 ? 'text-[#28C5D4]' : 'text-white' }}">
                                        {{ number_format($bid->total_weighted_score, 2) }}
                                    </span>
                                @else
                                    <span class="text-indigo-300 text-xs">Belum dievaluasi</span>
                                @endif
                            </td>
                            @endif
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="font-mono text-xs {{ $i === 0 ? 'text-indigo-100 font-medium' : 'text-indigo-200' }}">
                                    {{ $bid->submitted_at->format('d/m/Y H:i:s') }}<span class="text-indigo-300/50">.{{ $bid->submitted_at->format('u') }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-indigo-100 max-w-xs truncate">
                                {{ $bid->notes ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </div>

        <div class="xl:col-span-1 space-y-6">

            <div class="rounded-xl bg-[#3553A8] shadow-sm p-6 space-y-4">
            <div>
                <label for="selection_method" class="mb-1.5 block text-sm font-bold text-white">
                    Metode Seleksi <span class="text-red-300">*</span>
                </label>
                <select id="selection_method" name="selection_method"
                        class="w-full rounded-md border-0 bg-white px-4 py-2.5 text-sm font-medium
                               text-gray-700 outline-none focus:ring-2 focus:ring-[#2B438A]
                               @error('selection_method') border-red-500 @enderror">
                    <option value="lowest_price"       {{ old('selection_method','lowest_price') === 'lowest_price'       ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="multi_criteria"      {{ old('selection_method') === 'multi_criteria' ? 'selected' : '' }}>Evaluasi Multi-Kriteria</option>
                    <option value="admin_consideration" {{ old('selection_method') === 'admin_consideration' ? 'selected' : '' }}>Pertimbangan Admin</option>
                </select>
                @error('selection_method')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="notes" class="mb-1.5 block text-sm font-bold text-white">
                    Catatan <span class="text-xs font-normal text-indigo-200">(opsional)</span>
                </label>
                <textarea id="notes" name="notes" rows="3"
                          placeholder="Alasan pemilihan pemenang..."
                          class="w-full rounded-md border-0 bg-white px-4 py-2.5 text-sm font-medium
                                 text-gray-700 placeholder-gray-400 outline-none
                                 focus:ring-2 focus:ring-[#2B438A]">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.tenders.show', $tender) }}"
               class="rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="rounded-md bg-[#28C5D4] px-6 py-2.5 text-sm font-bold text-white
                           hover:bg-teal-400 transition-colors duration-150 shadow-sm">
                ✓ Tetapkan Pemenang
            </button>
        </div>
        </div>
    </form>

</div>
@endsection
