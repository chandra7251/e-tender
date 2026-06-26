@extends('layouts.admin')
@section('title', 'Detail Kontrak')
@section('page-title', 'Detail Kontrak')

@section('content')
<div class="max-w-3xl space-y-6">

    @if(session('success'))
    <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="font-bold text-gray-800 mb-5 pb-3 border-b">Informasi Kontrak</h3>
        @php
            $statusColor = match($contract->status) {
                'draft'          => 'bg-gray-100 text-gray-600',
                'sent_to_vendor' => 'bg-blue-100 text-blue-700',
                'signed_vendor'  => 'bg-yellow-100 text-yellow-700',
                'signed_admin'   => 'bg-indigo-100 text-indigo-700',
                'active'         => 'bg-green-100 text-green-700',
                'completed'      => 'bg-emerald-100 text-emerald-700',
                'terminated'     => 'bg-red-100 text-red-700',
                default          => 'bg-gray-100 text-gray-600',
            };
        @endphp
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-xs text-gray-500 mb-1">No. Kontrak</dt>
                <dd class="font-mono font-bold text-gray-800">{{ $contract->contract_number }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">Status</dt>
                <dd><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusColor }}">{{ strtoupper($contract->status) }}</span></dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">Tender</dt>
                <dd class="text-gray-800">{{ $contract->tender->title ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">Vendor</dt>
                <dd class="text-gray-800">{{ optional($contract->vendor->user)->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">Nilai Kontrak</dt>
                <dd class="font-bold text-gray-800">Rp {{ number_format($contract->contract_value, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">Periode</dt>
                <dd class="text-gray-700">{{ optional($contract->start_date)->format('d/m/Y') }} &ndash; {{ optional($contract->end_date)->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">TTD Vendor</dt>
                <dd class="text-gray-700">{{ $contract->signed_by_vendor_at ? $contract->signed_by_vendor_at->format('d/m/Y H:i') : '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">TTD Admin</dt>
                <dd class="text-gray-700">{{ $contract->signed_by_admin_at ? $contract->signed_by_admin_at->format('d/m/Y H:i') : '-' }}</dd>
            </div>
        </dl>

        @if($contract->terms)
        <div class="mt-5 pt-4 border-t">
            <dt class="text-xs text-gray-500 mb-2">Syarat & Ketentuan Kontrak</dt>
            <dd class="text-sm text-gray-700 whitespace-pre-line bg-gray-50 rounded-lg p-4 max-h-60 overflow-y-auto">{{ $contract->terms }}</dd>
        </div>
        @endif

        <div class="flex flex-wrap gap-3 mt-6 pt-4 border-t">
            @if($contract->status === 'draft')
            <form method="POST" action="{{ route('admin.contracts.send', $contract->id) }}">
                @csrf @method('PATCH')
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">Kirim ke Vendor</button>
            </form>
            @endif
            @if($contract->status === 'signed_vendor')
            <form method="POST" action="{{ route('admin.contracts.sign', $contract->id) }}">
                @csrf @method('PATCH')
                <button class="rounded-lg bg-green-600 px-4 py-2 text-sm font-bold text-white hover:bg-green-700">Tanda Tangani (Admin)</button>
            </form>
            @endif
            @if($contract->status === 'active')
            <form method="POST" action="{{ route('admin.contracts.complete', $contract->id) }}"
                  onsubmit="return confirm('Tandai kontrak ini sebagai SELESAI?')">
                @csrf @method('PATCH')
                <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">Selesaikan Kontrak</button>
            </form>
            @endif
            <a href="{{ route('admin.contracts.export.pdf', $contract->id) }}"
               target="_blank"
               class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700">
                Export PDF
            </a>
            <a href="{{ route('admin.contracts.index') }}"
               class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                &larr; Kembali
            </a>
        </div>
    </div>

    @if($contract->deliveries->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="font-bold text-gray-800 mb-4 pb-3 border-b">Jadwal Penyerahan / Milestone</h3>
        <div class="space-y-3">
        @foreach($contract->deliveries as $d)
        @php
            $mStatus = match($d->status) {
                'scheduled'   => ['color'=>'bg-gray-100 text-gray-600',  'label'=>'Terjadwal'],
                'in_progress' => ['color'=>'bg-blue-100 text-blue-700',  'label'=>'Dalam Proses'],
                'delivered'   => ['color'=>'bg-yellow-100 text-yellow-700','label'=>'Diserahkan'],
                'verified'    => ['color'=>'bg-emerald-100 text-emerald-700','label'=>'Diverifikasi'],
                'overdue'     => ['color'=>'bg-red-100 text-red-700',    'label'=>'Terlambat'],
                default       => ['color'=>'bg-gray-100 text-gray-600',  'label'=>ucfirst($d->status)],
            };
        @endphp
        <div class="flex items-start justify-between rounded-lg border border-gray-100 bg-gray-50 px-4 py-3">
            <div class="flex-1">
                <p class="font-semibold text-sm text-gray-800">{{ $d->title }}</p>
                @if($d->description)
                <p class="text-xs text-gray-500 mt-0.5">{{ $d->description }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-1">Due: {{ $d->due_date ? $d->due_date->format('d/m/Y') : '-' }}</p>
            </div>
            <span class="ml-4 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $mStatus['color'] }}">{{ $mStatus['label'] }}</span>
        </div>
        @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
