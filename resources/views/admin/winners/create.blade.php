@extends('layouts.admin')

@section('title', 'Pilih Pemenang')
@section('page-title', 'Pilih Pemenang Tender')

@section('content')
<div class="max-w-3xl space-y-6">

    <a href="{{ route('admin.tenders.show', $tender) }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-300 transition-colors">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Detail Tender
    </a>

    {{-- Tender title --}}
    <div class="rounded-xl border border-gray-800 bg-gray-900 px-5 py-4">
        <p class="text-xs text-gray-500">Tender</p>
        <p class="mt-0.5 text-base font-semibold text-gray-100">{{ $tender->title }}</p>
    </div>

    <form method="POST" action="{{ route('admin.tenders.winner.store', $tender) }}" class="space-y-6">
        @csrf

        @if ($errors->any())
            <div class="rounded-lg border border-red-700 bg-red-900/40 px-4 py-3 text-sm text-red-300">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Bid selection table --}}
        <div class="rounded-xl border border-gray-800 bg-gray-900 overflow-hidden">
            <div class="border-b border-gray-800 px-5 py-3">
                <h2 class="text-sm font-semibold text-gray-300">Pilih Bid Pemenang</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Urutan prioritas pemenang:
                    <span class="text-teal-400 font-medium">① Bid terendah</span>
                    &rarr;
                    <span class="text-blue-400 font-medium">② Waktu submit tercepat</span>
                    &rarr;
                    <span class="text-purple-400 font-medium">③ ULID (urutan masuk ke server)</span>
                </p>
                <p class="text-xs text-gray-600 mt-0.5">
                    Jika dua vendor punya bid yang sama, vendor dengan waktu submit lebih awal otomatis di posisi teratas.
                </p>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">
                        <th class="px-5 py-3 w-8"></th>
                        <th class="px-5 py-3">Rank</th>
                        <th class="px-5 py-3">Vendor</th>
                        <th class="px-5 py-3 text-right">Bid Amount</th>
                        <th class="px-5 py-3">Waktu Submit</th>
                        <th class="px-5 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @foreach ($bids as $i => $bid)
                    @php
                        // Deteksi tie: cek apakah ada bid lain dengan amount yang sama
                        $isTie = $bids->where('bid_amount', $bid->bid_amount)->count() > 1;
                    @endphp
                    <tr class="hover:bg-gray-800/60 transition-colors duration-100 cursor-pointer"
                        onclick="document.getElementById('bid_{{ $bid->id }}').click()">
                        <td class="px-5 py-3">
                            <input type="radio" id="bid_{{ $bid->id }}" name="bid_id"
                                   value="{{ $bid->id }}"
                                   class="h-4 w-4 accent-indigo-500"
                                   {{ $i === 0 ? 'checked' : '' }}>
                        </td>
                        <td class="px-5 py-3">
                            @if ($i === 0)
                                <span class="rounded-full border border-teal-700 bg-teal-900/50
                                             px-2.5 py-0.5 text-xs font-semibold text-teal-400">
                                    ★ Terendah
                                </span>
                                @if ($isTie)
                                    {{-- Ada bid lain dengan amount sama — tie-breaker aktif --}}
                                    <span class="ml-1 rounded-full border border-blue-700 bg-blue-900/40
                                                 px-2 py-0.5 text-xs font-semibold text-blue-400"
                                          title="Tie-breaker: dipilih karena submit lebih awal">
                                        ⚡ Tie-winner
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-600">#{{ $i + 1 }}</span>
                                @if ($isTie && $bids[$i - 1]->bid_amount == $bid->bid_amount)
                                    <span class="ml-1 text-xs text-yellow-600" title="Sama dengan bid di atasnya — kalah waktu submit">
                                        ⚠ Tie
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-100">
                            {{ $bid->vendor->company_name ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-right font-mono font-semibold
                                   {{ $i === 0 ? 'text-teal-400' : 'text-gray-100' }}">
                            Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3">
                            {{-- Waktu submit dengan presisi microsecond --}}
                            <span class="font-mono text-xs {{ $i === 0 ? 'text-blue-400' : 'text-gray-500' }}">
                                {{ $bid->submitted_at->format('d/m/Y H:i:s') }}<span class="text-gray-600">.{{ $bid->submitted_at->format('u') }}</span>
                            </span>
                            @if ($isTie)
                                <br>
                                <span class="text-xs text-gray-700" title="ULID tie-breaker: {{ $bid->ulid }}">
                                    ID: {{ substr($bid->ulid ?? '', 0, 10) }}…
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500 max-w-xs truncate">
                            {{ $bid->notes ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Selection method & notes --}}
        <div class="rounded-xl border border-gray-800 bg-gray-900 p-6 space-y-4">
            <div>
                <label for="selection_method" class="mb-1.5 block text-sm font-medium text-gray-300">
                    Metode Seleksi <span class="text-red-500">*</span>
                </label>
                <select id="selection_method" name="selection_method"
                        class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3.5 py-2.5 text-sm
                               text-gray-100 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                               @error('selection_method') border-red-600 @enderror">
                    <option value="lowest_price"       {{ old('selection_method','lowest_price') === 'lowest_price'       ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="admin_consideration" {{ old('selection_method') === 'admin_consideration' ? 'selected' : '' }}>Pertimbangan Admin</option>
                </select>
                @error('selection_method')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="notes" class="mb-1.5 block text-sm font-medium text-gray-300">
                    Catatan <span class="text-xs text-gray-500">(opsional)</span>
                </label>
                <textarea id="notes" name="notes" rows="3"
                          placeholder="Alasan pemilihan pemenang..."
                          class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3.5 py-2.5 text-sm
                                 text-gray-100 placeholder-gray-600 outline-none
                                 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.tenders.show', $tender) }}"
               class="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white
                           hover:bg-indigo-500 transition-colors duration-150">
                ✓ Tetapkan Pemenang
            </button>
        </div>
    </form>

</div>
@endsection
