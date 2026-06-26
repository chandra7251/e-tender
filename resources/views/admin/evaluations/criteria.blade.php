@extends('layouts.admin')

@section('title', 'Kriteria Evaluasi')
@section('page-title', 'Kriteria Evaluasi Tender')

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
    </div>

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.tenders.evaluation-criteria.store', $tender) }}" id="criteriaForm">
        @csrf

        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 px-5 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Kriteria Evaluasi</h2>
                    <p class="text-xs text-gray-500 mt-1">Tentukan kriteria dan bobot untuk mengevaluasi penawaran vendor. Total bobot harus = <strong>100%</strong>.</p>
                </div>
                <button type="button" onclick="addCriteria()"
                        class="rounded-md bg-[#28C5D4] px-4 py-2 text-xs font-bold text-white hover:bg-teal-400 transition-colors shadow-sm">
                    + Tambah Kriteria
                </button>
            </div>

            <div id="criteria-container" class="divide-y divide-gray-100">
                @forelse ($criteria as $i => $c)
                <div class="criteria-row p-5 bg-white hover:bg-gray-50 transition-colors" data-index="{{ $i }}">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                        <div class="md:col-span-1 flex items-center justify-center pt-6">
                            <span class="criteria-number inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#3553A8] text-xs font-bold text-white">{{ $i + 1 }}</span>
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kriteria *</label>
                            <input type="text" name="criteria[{{ $i }}][name]" value="{{ $c->name }}" required
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none"
                                   placeholder="Contoh: Harga Penawaran">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Bobot (%) *</label>
                            <input type="number" name="criteria[{{ $i }}][weight]" value="{{ $c->weight }}" required
                                   step="0.01" min="0.01" max="100"
                                   class="weight-input w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none"
                                   oninput="updateTotalWeight()">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Skor Maks *</label>
                            <input type="number" name="criteria[{{ $i }}][max_score]" value="{{ $c->max_score }}" required
                                   min="1" max="1000"
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi</label>
                            <input type="text" name="criteria[{{ $i }}][description]" value="{{ $c->description }}"
                                   class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none"
                                   placeholder="Opsional">
                        </div>
                        <div class="md:col-span-1 flex items-center pt-6">
                            <button type="button" onclick="removeCriteria(this)"
                                    class="rounded-md border border-red-200 bg-red-50 p-2 text-red-500 hover:bg-red-100 transition-colors">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                {{-- Default empty rows will be created by JS --}}
                @endforelse
            </div>

            <div class="border-t border-gray-200 px-5 py-4 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-600">Total Bobot:</span>
                    <span id="totalWeight" class="text-lg font-bold text-[#3553A8]">0%</span>
                    <span id="weightStatus" class="text-xs font-medium px-2 py-0.5 rounded-full"></span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.tenders.show', $tender) }}"
                       class="rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="rounded-md bg-[#28C5D4] px-6 py-2.5 text-sm font-bold text-white hover:bg-teal-400 transition-colors shadow-sm">
                        ✓ Simpan Kriteria
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let criteriaIndex = {{ $criteria->count() > 0 ? $criteria->count() : 0 }};

@if ($criteria->isEmpty())
    document.addEventListener('DOMContentLoaded', function() {
        // Add default criteria
        const defaults = [
            { name: 'Harga Penawaran', weight: 40, max_score: 100, description: 'Skor berdasarkan harga terendah' },
            { name: 'Kualitas Teknis', weight: 30, max_score: 100, description: 'Penilaian kualitas teknis penawaran' },
            { name: 'Pengalaman Vendor', weight: 20, max_score: 100, description: 'Track record dan pengalaman vendor' },
            { name: 'Kelengkapan Dokumen', weight: 10, max_score: 100, description: 'Kelengkapan dan validitas dokumen' },
        ];
        defaults.forEach(d => addCriteria(d));
    });
@endif

function addCriteria(defaults = null) {
    const container = document.getElementById('criteria-container');
    const idx = criteriaIndex++;

    const html = `
    <div class="criteria-row p-5 bg-white hover:bg-gray-50 transition-colors" data-index="${idx}">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
            <div class="md:col-span-1 flex items-center justify-center pt-6">
                <span class="criteria-number inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#3553A8] text-xs font-bold text-white">${container.children.length + 1}</span>
            </div>
            <div class="md:col-span-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Kriteria *</label>
                <input type="text" name="criteria[${idx}][name]" value="${defaults?.name || ''}" required
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none"
                       placeholder="Contoh: Harga Penawaran">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Bobot (%) *</label>
                <input type="number" name="criteria[${idx}][weight]" value="${defaults?.weight || ''}" required
                       step="0.01" min="0.01" max="100"
                       class="weight-input w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none"
                       oninput="updateTotalWeight()">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Skor Maks *</label>
                <input type="number" name="criteria[${idx}][max_score]" value="${defaults?.max_score || 100}" required
                       min="1" max="1000"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi</label>
                <input type="text" name="criteria[${idx}][description]" value="${defaults?.description || ''}"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none"
                       placeholder="Opsional">
            </div>
            <div class="md:col-span-1 flex items-center pt-6">
                <button type="button" onclick="removeCriteria(this)"
                        class="rounded-md border border-red-200 bg-red-50 p-2 text-red-500 hover:bg-red-100 transition-colors">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>`;

    container.insertAdjacentHTML('beforeend', html);
    updateNumbers();
    updateTotalWeight();
}

function removeCriteria(btn) {
    const row = btn.closest('.criteria-row');
    row.remove();
    updateNumbers();
    updateTotalWeight();
}

function updateNumbers() {
    document.querySelectorAll('.criteria-number').forEach((el, i) => {
        el.textContent = i + 1;
    });
}

function updateTotalWeight() {
    let total = 0;
    document.querySelectorAll('.weight-input').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    const display = document.getElementById('totalWeight');
    const status = document.getElementById('weightStatus');

    display.textContent = total.toFixed(2) + '%';

    if (Math.abs(total - 100) < 0.01) {
        display.classList.remove('text-red-500');
        display.classList.add('text-[#3553A8]');
        status.textContent = '✓ Valid';
        status.classList.remove('bg-red-100', 'text-red-600');
        status.classList.add('bg-emerald-100', 'text-emerald-600');
    } else {
        display.classList.remove('text-[#3553A8]');
        display.classList.add('text-red-500');
        status.textContent = total < 100 ? `Kurang ${(100 - total).toFixed(2)}%` : `Lebih ${(total - 100).toFixed(2)}%`;
        status.classList.remove('bg-emerald-100', 'text-emerald-600');
        status.classList.add('bg-red-100', 'text-red-600');
    }
}

document.addEventListener('DOMContentLoaded', updateTotalWeight);
</script>
@endsection
