@extends('layouts.admin')
@section('title', 'Ranking Gabungan (2 Amplop) — ' . $tender->title)

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.tenders.show', $tender) }}" class="text-indigo-600 hover:underline text-sm">← Kembali ke Tender</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">🏆 Ranking Gabungan (Evaluasi 2 Amplop)</h1>
        <p class="text-gray-500">{{ $tender->title }}</p>
    </div>

    {{-- Weight Info --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $techWeight }}%</div>
            <div class="text-sm text-blue-500">Bobot Teknis</div>
            <div class="w-full bg-blue-200 rounded-full h-2 mt-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $techWeight }}%"></div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $priceWeight }}%</div>
            <div class="text-sm text-green-500">Bobot Harga</div>
            <div class="w-full bg-green-200 rounded-full h-2 mt-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $priceWeight }}%"></div>
            </div>
        </div>
    </div>

    {{-- Formula --}}
    <div class="bg-gray-50 border rounded-xl p-4 mb-6">
        <p class="text-sm text-gray-600">
            <strong>Formula:</strong> Skor Gabungan = (Skor Teknis × {{ $techWeight }}%) + (Skor Harga × {{ $priceWeight }}%)
        </p>
        <p class="text-xs text-gray-500 mt-1">
            Skor Harga = (Harga Terendah / Harga Vendor) × 100
        </p>
    </div>

    @if($rankedBids->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
        <p class="text-gray-400 text-lg">Tidak ada vendor yang lulus evaluasi teknis.</p>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Rank</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Skor Teknis</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Penawaran</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Skor Harga</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Skor Gabungan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($rankedBids as $i => $bid)
                <tr class="{{ $i === 0 ? 'bg-yellow-50/60 ring-2 ring-yellow-300 ring-inset' : '' }}">
                    <td class="px-4 py-3 text-center">
                        @if($i === 0)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-400 text-white font-bold text-sm">🥇</span>
                        @elseif($i === 1)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-white font-bold text-sm">🥈</span>
                        @elseif($i === 2)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-400 text-white font-bold text-sm">🥉</span>
                        @else
                            <span class="text-gray-500 font-medium">{{ $i + 1 }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $bid->vendor->company_name ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $bid->vendor->user->email ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-semibold text-blue-700">{{ number_format($bid->technical_score ?? 0, 1) }}</span>
                        <div class="text-xs text-gray-400">× {{ $techWeight }}%</div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-medium text-gray-800">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-semibold text-green-700">{{ number_format($bid->price_score_calculated, 1) }}</span>
                        <div class="text-xs text-gray-400">× {{ $priceWeight }}%</div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="text-xl font-bold {{ $i === 0 ? 'text-yellow-600' : 'text-indigo-600' }}">
                            {{ number_format($bid->combined_score, 2) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Winner Suggestion --}}
    @php $topBid = $rankedBids->first(); @endphp
    @if($topBid && !$tender->hasWinner())
    <div class="bg-green-50 border border-green-200 rounded-xl p-5 mt-6">
        <h3 class="font-bold text-green-800 mb-2">🏆 Rekomendasi Pemenang</h3>
        <p class="text-green-700">
            <strong>{{ $topBid->vendor->company_name }}</strong> dengan skor gabungan tertinggi
            <strong>{{ number_format($topBid->combined_score, 2) }}</strong>
            (Teknis: {{ number_format($topBid->technical_score, 1) }}, Harga: Rp {{ number_format($topBid->bid_amount, 0, ',', '.') }}).
        </p>
        <a href="{{ route('admin.tenders.winner.create', $tender) }}"
            class="inline-block mt-3 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
            Pilih Pemenang →
        </a>
    </div>
    @endif
    @endif

    <div class="flex justify-between mt-6">
        <a href="{{ route('admin.tenders.envelope.price', $tender) }}"
            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">← Evaluasi Harga</a>
        <a href="{{ route('admin.tenders.show', $tender) }}"
            class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200">Detail Tender</a>
    </div>
</div>
@endsection
