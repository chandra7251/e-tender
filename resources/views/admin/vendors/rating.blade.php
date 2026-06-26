@extends('layouts.admin')
@section('title', 'Rating Vendor — ' . $vendor->company_name)

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('admin.tenders.show', $tender) }}" class="text-indigo-600 hover:underline text-sm">← Kembali ke Tender</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Rating Vendor</h1>
        <p class="text-gray-500 mt-1">Tender: <strong>{{ $tender->title }}</strong></p>
        <p class="text-gray-500">Vendor: <strong>{{ $vendor->company_name }}</strong></p>
    </div>

    {{-- Existing Rating Notice --}}
    @if($existingRating)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="text-blue-700 font-medium">⚡ Vendor ini sudah memiliki rating. Form di bawah akan mengupdate rating yang ada.</p>
        <p class="text-blue-600 text-sm mt-1">Rating saat ini: <strong>{{ $existingRating->overall_score }}/5</strong></p>
    </div>
    @endif

    <form action="{{ route('admin.tenders.vendors.rating.store', [$tender, $vendor]) }}" method="POST" class="bg-white rounded-xl shadow-sm border p-6 space-y-6">
        @csrf

        {{-- Star Rating Inputs --}}
        @php
            $dimensions = [
                'quality_score' => ['label' => 'Kualitas Pekerjaan', 'desc' => 'Kualitas barang/jasa yang diberikan vendor', 'icon' => '⭐'],
                'delivery_score' => ['label' => 'Ketepatan Waktu', 'desc' => 'Ketepatan dalam memenuhi jadwal/deadline', 'icon' => '⏱️'],
                'communication_score' => ['label' => 'Komunikasi', 'desc' => 'Responsivitas dan kejelasan komunikasi vendor', 'icon' => '💬'],
                'compliance_score' => ['label' => 'Kepatuhan Dokumen', 'desc' => 'Kelengkapan dan kesesuaian dokumen', 'icon' => '📋'],
            ];
        @endphp

        @foreach($dimensions as $field => $info)
        <div>
            <label class="block font-semibold text-gray-700 mb-1">
                {{ $info['icon'] }} {{ $info['label'] }}
            </label>
            <p class="text-sm text-gray-500 mb-2">{{ $info['desc'] }}</p>
            <div class="flex gap-2" x-data="{ score: {{ old($field, $existingRating?->$field ?? 0) }} }">
                @for($i = 1; $i <= 5; $i++)
                <button type="button"
                    @click="score = {{ $i }}"
                    :class="score >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                    class="text-3xl hover:text-yellow-400 transition-colors focus:outline-none">
                    ★
                </button>
                @endfor
                <input type="hidden" name="{{ $field }}" :value="score">
                <span class="ml-2 text-gray-600 font-semibold self-center" x-text="score + '/5'"></span>
            </div>
            @error($field)
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        @endforeach

        {{-- Review Text --}}
        <div>
            <label for="review" class="block font-semibold text-gray-700 mb-1">📝 Catatan/Review</label>
            <textarea name="review" id="review" rows="4" maxlength="1000"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Berikan catatan tentang kinerja vendor di tender ini...">{{ old('review', $existingRating?->review) }}</textarea>
            @error('review')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('admin.tenders.show', $tender) }}"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                {{ $existingRating ? 'Update Rating' : 'Simpan Rating' }}
            </button>
        </div>
    </form>
</div>
@endsection
