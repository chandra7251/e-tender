@extends('layouts.admin')
@section('title', 'Evaluasi Harga (Amplop 2) — ' . $tender->title)

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.tenders.show', $tender) }}" class="text-indigo-600 hover:underline text-sm">← Kembali ke Tender</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">💰 Amplop 2: Evaluasi Harga</h1>
        <p class="text-gray-500">{{ $tender->title }}</p>
        <div class="flex gap-4 mt-2 text-sm">
            <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full">Bobot Harga: <strong>{{ $tender->price_weight ?? 40 }}%</strong></span>
            @if($tender->open_bidding_price)
            <span class="bg-orange-50 text-orange-700 px-3 py-1 rounded-full">HPS: <strong>Rp {{ number_format($tender->open_bidding_price, 0, ',', '.') }}</strong></span>
            @endif
        </div>
    </div>

    {{-- Info Banner --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
        <p class="text-yellow-800 font-medium">⚠️ Hanya vendor yang <strong>lulus evaluasi teknis</strong> (Amplop 1) yang ditampilkan harganya.</p>
        <p class="text-yellow-700 text-sm mt-1">Vendor yang gugur secara teknis tidak boleh dibuka amplop harganya sesuai Perpres 16/2018.</p>
    </div>

    {{-- Passed Vendors - Price Visible --}}
    <h2 class="text-lg font-bold text-green-700 mb-3">✅ Vendor Lulus Teknis ({{ $passedBids->count() }})</h2>

    @if($passedBids->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border p-8 text-center mb-6">
        <p class="text-gray-400">Tidak ada vendor yang lulus evaluasi teknis.</p>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Skor Teknis</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Penawaran Harga</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">vs HPS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($passedBids as $i => $bid)
                <tr class="{{ $i === 0 ? 'bg-green-50/50' : '' }}">
                    <td class="px-4 py-3 text-sm">{{ $i + 1 }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $bid->vendor->company_name ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $bid->submitted_at->format('d M Y H:i') }}</div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-semibold text-green-700">{{ number_format($bid->technical_score, 1) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-bold text-gray-800">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-4 py-3 text-right text-sm">
                        @if($tender->open_bidding_price && $tender->open_bidding_price > 0)
                            @php
                                $diff = $tender->open_bidding_price - $bid->bid_amount;
                                $pct = ($diff / $tender->open_bidding_price) * 100;
                            @endphp
                            <span class="{{ $diff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $diff >= 0 ? '↓' : '↑' }} {{ number_format(abs($pct), 1) }}%
                            </span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Failed Vendors - Price Hidden --}}
    @if($failedBids->isNotEmpty())
    <h2 class="text-lg font-bold text-red-700 mb-3">❌ Vendor Gugur Teknis ({{ $failedBids->count() }})</h2>
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Skor Teknis</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Harga</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($failedBids as $bid)
                <tr class="bg-red-50/30">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-600">{{ $bid->vendor->company_name ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-semibold text-red-600">{{ number_format($bid->technical_score ?? 0, 1) }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $bid->technical_notes ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs text-gray-400 italic">🔒 Tidak dibuka</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Action --}}
    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route('admin.tenders.envelope.technical', $tender) }}"
            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">← Kembali ke Evaluasi Teknis</a>
        @if($passedBids->count() > 0)
        <a href="{{ route('admin.tenders.envelope.ranking', $tender) }}"
            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
            Lihat Ranking Gabungan →
        </a>
        @endif
    </div>
</div>
@endsection
