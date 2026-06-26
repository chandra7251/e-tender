@extends('layouts.admin')

@section('title', 'History Tender')
@section('page-title', 'History Tender')

@section('content')
<div class="max-w-3xl space-y-4">

    <div class="flex items-center gap-2 text-sm mb-4">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="font-medium text-gray-500 hover:text-gray-700 transition-colors">{{ Str::limit($tender->title, 50) }}</a>
        <span class="text-gray-500">/</span>
        <span class="font-bold text-gray-800">History</span>
    </div>

    <div class="rounded-md bg-[#3553A8] px-6 py-4 shadow-sm">
        <p class="text-xs text-indigo-200">Tender</p>
        <p class="text-lg font-bold text-white mt-0.5">{{ $tender->title }}</p>
        <p class="text-xs text-indigo-200 mt-1">{{ $histories->count() }} aktivitas tercatat</p>
    </div>

    <div class="space-y-4">
        @if ($histories->isEmpty())
            <div class="rounded-md bg-[#3553A8] px-6 py-8 shadow-sm text-center">
                <p class="text-sm text-indigo-200">Belum ada aktivitas tercatat.</p>
            </div>
        @else
            @foreach ($histories as $h)
                <div class="rounded-md bg-[#3553A8] px-6 py-5 shadow-sm">
                    <p class="text-base font-bold text-white">
                        {{ str_replace('_', ' ', ucfirst($h->action)) }}
                    </p>

                    @if ($h->old_status && $h->new_status && $h->old_status !== $h->new_status)
                        <p class="text-sm text-indigo-200 mt-1">
                            <span class="line-through opacity-80">{{ ucfirst($h->old_status) }}</span>
                            <span class="mx-1">→</span>
                            <span class="font-bold text-[#28C5D4]">{{ ucfirst($h->new_status) }}</span>
                        </p>
                    @endif

                    @if ($h->description)
                        <p class="text-sm text-indigo-200 mt-1">{{ $h->description }}</p>
                    @endif

                    <p class="text-xs text-indigo-200 opacity-80 mt-2">
                        {{ $h->actor->name ?? 'System' }} - {{ $h->created_at?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </div>
            @endforeach
        @endif
    </div>

</div>
@endsection
