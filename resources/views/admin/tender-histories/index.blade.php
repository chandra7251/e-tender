@extends('layouts.admin')

@section('title', 'History Tender')
@section('page-title', 'History Tender')

@section('content')
<div class="max-w-2xl space-y-4">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="hover:text-gray-300 transition-colors">{{ Str::limit($tender->title, 50) }}</a>
        <span>/</span>
        <span class="text-gray-400">History</span>
    </div>

    {{-- Tender info --}}
    <div class="rounded-xl border border-gray-800 bg-gray-900 px-5 py-3">
        <p class="text-xs text-gray-500">Tender</p>
        <p class="font-semibold text-gray-100">{{ $tender->title }}</p>
        <p class="text-xs text-gray-600 mt-0.5">{{ $histories->count() }} aktivitas tercatat</p>
    </div>

    {{-- Timeline --}}
    <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
        @if ($histories->isEmpty())
            <p class="text-sm text-gray-600 text-center py-4">Belum ada aktivitas tercatat.</p>
        @else
            <ol class="space-y-0">
                @foreach ($histories as $h)
                    <li class="flex gap-4">
                        <div class="flex flex-col items-center">
                            @php
                                $dotColor = match(true) {
                                    $h->action === 'winner_selected' => 'bg-emerald-500 ring-emerald-900/40',
                                    $h->action === 'po_generated'    => 'bg-indigo-500 ring-indigo-900/40',
                                    $h->action === 'tender_created'  => 'bg-sky-500 ring-sky-900/40',
                                    str_contains($h->action, 'status') => 'bg-violet-500 ring-violet-900/40',
                                    default => 'bg-gray-500 ring-gray-900/40',
                                };
                            @endphp
                            <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full ring-4 {{ $dotColor }}"></span>
                            @if (!$loop->last)
                                <span class="w-px flex-1 bg-gray-800 mt-1"></span>
                            @endif
                        </div>
                        <div class="pb-6 flex-1">
                            <p class="text-sm font-medium text-gray-200">
                                {{ str_replace('_', ' ', ucfirst($h->action)) }}
                            </p>
                            @if ($h->old_status && $h->new_status && $h->old_status !== $h->new_status)
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <span class="line-through">{{ ucfirst($h->old_status) }}</span>
                                    <span class="mx-1 text-gray-600">→</span>
                                    <span class="text-violet-400 font-medium">{{ ucfirst($h->new_status) }}</span>
                                </p>
                            @endif
                            @if ($h->description)
                                <p class="text-xs text-gray-500 mt-1">{{ $h->description }}</p>
                            @endif
                            <p class="text-xs text-gray-700 mt-1">
                                {{ $h->actor->name ?? 'System' }}
                                &middot;
                                {{ $h->created_at?->format('d M Y, H:i') ?? '-' }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

</div>
@endsection
