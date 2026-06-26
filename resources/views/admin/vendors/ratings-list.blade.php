@extends('layouts.admin')
@section('title', 'Rating — ' . $vendor->company_name)

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.vendors.show', $vendor) }}" class="text-indigo-600 hover:underline text-sm">← Kembali ke Detail Vendor</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">{{ $vendor->company_name }}</h1>
            <p class="text-gray-500">Riwayat Rating dari Semua Tender</p>
        </div>
        @if($avgRating)
        <div class="text-center bg-indigo-50 rounded-xl px-6 py-4 border border-indigo-100">
            <div class="text-3xl font-bold text-indigo-600">{{ number_format($avgRating, 1) }}</div>
            <div class="text-sm text-indigo-500">Rata-rata / 5</div>
            <div class="text-yellow-400 text-lg mt-1">
                @for($i = 1; $i <= 5; $i++)
                    {{ $i <= round($avgRating) ? '★' : '☆' }}
                @endfor
            </div>
        </div>
        @endif
    </div>

    @if($ratings->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
        <p class="text-gray-400 text-lg">Belum ada rating untuk vendor ini.</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($ratings as $rating)
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $rating->tender->title ?? 'Tender Dihapus' }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Dinilai oleh {{ $rating->rater->name ?? '-' }} •
                        {{ $rating->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-indigo-600">{{ number_format($rating->overall_score, 1) }}/5</div>
                    <div class="text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            {{ $i <= round($rating->overall_score) ? '★' : '☆' }}
                        @endfor
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-gray-500 mb-1">Kualitas</div>
                    <div class="font-bold text-gray-700">{{ $rating->quality_score }}/5</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-gray-500 mb-1">Ketepatan Waktu</div>
                    <div class="font-bold text-gray-700">{{ $rating->delivery_score }}/5</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-gray-500 mb-1">Komunikasi</div>
                    <div class="font-bold text-gray-700">{{ $rating->communication_score }}/5</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <div class="text-xs text-gray-500 mb-1">Kepatuhan</div>
                    <div class="font-bold text-gray-700">{{ $rating->compliance_score }}/5</div>
                </div>
            </div>

            @if($rating->review)
            <div class="mt-3 bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-600 italic">"{{ $rating->review }}"</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $ratings->links() }}</div>
    @endif
</div>
@endsection
