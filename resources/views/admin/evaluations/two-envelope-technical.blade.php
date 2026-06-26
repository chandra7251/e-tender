@extends('layouts.admin')
@section('title', 'Evaluasi Teknis (Amplop 1) — ' . $tender->title)

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('admin.tenders.show', $tender) }}" class="text-indigo-600 hover:underline text-sm">← Kembali ke Tender</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">📦 Amplop 1: Evaluasi Teknis</h1>
        <p class="text-gray-500">{{ $tender->title }}</p>
        <div class="flex gap-4 mt-2 text-sm">
            <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full">Passing Grade: <strong>{{ $tender->passing_grade ?? 70 }}</strong></span>
            <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full">Bobot Teknis: <strong>{{ $tender->technical_weight ?? 60 }}%</strong></span>
        </div>
    </div>

    @if($bids->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
        <p class="text-gray-400 text-lg">Belum ada bid yang masuk.</p>
    </div>
    @else
    <form action="{{ route('admin.tenders.envelope.technical.store', $tender) }}" method="POST">
        @csrf

        @if($technicalCriteria->isNotEmpty())
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <h3 class="font-semibold text-blue-800 mb-2">Kriteria Teknis yang Ditetapkan:</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach($technicalCriteria as $c)
                <div class="bg-white rounded-lg p-2 text-center border">
                    <div class="text-sm font-medium text-gray-700">{{ $c->name }}</div>
                    <div class="text-xs text-gray-500">Bobot: {{ $c->weight }}% | Max: {{ $c->max_score }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="space-y-4">
            @foreach($bids as $bid)
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $bid->vendor->company_name ?? '-' }}</h3>
                        <p class="text-sm text-gray-500">{{ $bid->vendor->user->email ?? '-' }} • Bid: {{ $bid->submitted_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($bid->technical_status !== 'pending')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $bid->technical_status === 'passed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $bid->technical_status === 'passed' ? '✅ Lulus' : '❌ Gugur' }}
                    </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Skor Teknis (0-100)</label>
                        <input type="number" name="evaluations[{{ $bid->id }}][technical_score]"
                            value="{{ old("evaluations.{$bid->id}.technical_score", $bid->technical_score) }}"
                            min="0" max="100" step="0.01" required
                            class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-400 mt-1">Min. {{ $tender->passing_grade ?? 70 }} untuk lulus</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="evaluations[{{ $bid->id }}][technical_status]"
                            class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="passed" {{ old("evaluations.{$bid->id}.technical_status", $bid->technical_status) === 'passed' ? 'selected' : '' }}>✅ Lulus</option>
                            <option value="failed" {{ old("evaluations.{$bid->id}.technical_status", $bid->technical_status) === 'failed' ? 'selected' : '' }}>❌ Gugur</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <input type="text" name="evaluations[{{ $bid->id }}][technical_notes]"
                            value="{{ old("evaluations.{$bid->id}.technical_notes", $bid->technical_notes) }}"
                            maxlength="500" placeholder="Catatan evaluasi..."
                            class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.tenders.show', $tender) }}"
                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</a>
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                Simpan Evaluasi Teknis
            </button>
        </div>
    </form>
    @endif
</div>
@endsection
