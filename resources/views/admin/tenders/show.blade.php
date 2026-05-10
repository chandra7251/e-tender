@extends('layouts.admin')

@section('title', $tender->title)
@section('page-title', 'Detail Tender')

@section('content')
<div class="space-y-6">

    {{-- Back + action buttons --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('admin.tenders.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-300 transition-colors">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Kembali ke Daftar Tender
        </a>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.tenders.participants.index', $tender) }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-700 px-3 py-2 text-sm
                      text-gray-300 hover:bg-gray-800 hover:text-white transition-colors duration-150">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                </svg>
                Peserta <span class="rounded-full bg-gray-700 px-1.5 py-0.5 text-xs">{{ $tender->participants->count() }}</span>
            </a>
            <a href="{{ route('admin.tenders.bids.index', $tender) }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-700 px-3 py-2 text-sm
                      text-gray-300 hover:bg-gray-800 hover:text-white transition-colors duration-150">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                </svg>
                Bid <span class="rounded-full bg-gray-700 px-1.5 py-0.5 text-xs">{{ $tender->bids->count() }}</span>
            </a>

            @if ($tender->result)
                <a href="{{ route('admin.tenders.result.show', $tender) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-700 px-3 py-2 text-sm
                          text-emerald-400 hover:bg-emerald-900/30 transition-colors duration-150">
                    ★ Hasil
                </a>
            @else
                <a href="{{ route('admin.tenders.winner.create', $tender) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-amber-700 px-3 py-2 text-sm
                          text-amber-400 hover:bg-amber-900/30 transition-colors duration-150">
                    Pilih Winner
                </a>
            @endif

            <a href="{{ route('admin.tenders.histories.index', $tender) }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-700 px-3 py-2 text-sm
                      text-gray-400 hover:bg-gray-800 hover:text-white transition-colors duration-150">
                History
            </a>
            <a href="{{ route('admin.tenders.edit', $tender) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-700 px-3 py-2 text-sm
                      text-gray-300 hover:bg-gray-800 hover:text-white transition-colors duration-150">
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Left: Detail + Timeline + Announcements ─────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Tender Info --}}
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <div class="mb-4 flex items-start justify-between">
                    <h2 class="text-lg font-semibold text-gray-100">{{ $tender->title }}</h2>
                    @php
                        $badge = match($tender->status) {
                            'open'       => 'bg-sky-900/50 text-sky-400 border-sky-700',
                            'aanwijzing' => 'bg-violet-900/50 text-violet-400 border-violet-700',
                            'bidding'    => 'bg-amber-900/50 text-amber-400 border-amber-700',
                            'closed'     => 'bg-gray-700/50 text-gray-400 border-gray-600',
                            'finished'   => 'bg-emerald-900/50 text-emerald-400 border-emerald-700',
                            default      => 'bg-gray-800/50 text-gray-500 border-gray-700',
                        };
                    @endphp
                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-sm font-semibold {{ $badge }}">
                        {{ ucfirst($tender->status) }}
                    </span>
                </div>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-widest text-gray-600 mb-1">Deskripsi</p>
                        <p class="text-gray-300">{{ $tender->description }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-widest text-gray-600 mb-1">Spesifikasi</p>
                        <p class="text-gray-300 whitespace-pre-line">{{ $tender->specification }}</p>
                    </div>
                    <div class="pt-2 border-t border-gray-800">
                        <p class="text-xs text-gray-600">Dibuat oleh: {{ $tender->creator->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h2 class="mb-4 text-sm font-semibold text-gray-300">Timeline</h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    @php
                        $rows = [
                            ['label' => 'Mulai Tender',      'val' => $tender->start_date],
                            ['label' => 'Selesai Tender',    'val' => $tender->end_date],
                            ['label' => 'Tanggal Aanwijzing','val' => $tender->aanwijzing_date],
                            ['label' => 'Bidding Mulai',     'val' => $tender->bidding_start],
                            ['label' => 'Bidding Selesai',   'val' => $tender->bidding_end],
                        ];
                    @endphp
                    @foreach ($rows as $row)
                        <div class="rounded-lg border border-gray-800 bg-gray-800/40 p-3">
                            <p class="text-xs text-gray-500 mb-1">{{ $row['label'] }}</p>
                            <p class="font-medium text-gray-100">
                                {{ $row['val'] ? $row['val']->format('d M Y, H:i') : '—' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Announcements list --}}
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h2 class="mb-4 text-sm font-semibold text-gray-300">
                    Aanwijzing / Pengumuman
                    <span class="ml-2 text-xs font-normal text-gray-600">
                        ({{ $tender->announcements->count() }})
                    </span>
                </h2>

                @if ($tender->announcements->isEmpty())
                    <p class="text-sm text-gray-600">Belum ada pengumuman.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($tender->announcements->sortByDesc('published_at') as $ann)
                            <div class="rounded-lg border border-gray-800 bg-gray-800/40 p-4">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="font-medium text-gray-100">{{ $ann->title }}</p>
                                    <span class="shrink-0 text-xs text-gray-500">
                                        {{ $ann->published_at?->format('d M Y, H:i') ?? '-' }}
                                    </span>
                                </div>
                                <p class="mt-1.5 text-sm text-gray-400">{{ $ann->content }}</p>
                                <p class="mt-2 text-xs text-gray-600">Oleh: {{ $ann->creator->name ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- ── Right: Update Status + Add Announcement ──────────────────────── --}}
        <div class="space-y-6">

            {{-- Update Status --}}
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h2 class="mb-4 text-sm font-semibold text-gray-300">Ubah Status</h2>
                <form method="POST" action="{{ route('admin.tenders.status', $tender) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="status" class="mb-1 block text-xs text-gray-400">Status Baru</label>
                        <select id="status" name="status"
                                class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm
                                       text-gray-100 outline-none
                                       focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                                       @error('status') border-red-600 @enderror">
                            @foreach (['draft','open','aanwijzing','bidding','closed','finished'] as $s)
                                <option value="{{ $s }}" {{ $tender->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="mb-1 block text-xs text-gray-400">
                            Catatan perubahan <span class="text-gray-600">(opsional)</span>
                        </label>
                        <input id="description" type="text" name="description"
                               placeholder="Alasan perubahan status..."
                               class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm
                                      text-gray-100 placeholder-gray-600 outline-none
                                      focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                    <button type="submit"
                            class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white
                                   hover:bg-indigo-500 transition-colors duration-150">
                        Ubah Status
                    </button>
                </form>
            </div>

            {{-- Add Announcement --}}
            <div class="rounded-xl border border-violet-800 bg-violet-900/20 p-6">
                <h2 class="mb-4 text-sm font-semibold text-violet-400">Tambah Aanwijzing</h2>
                <form method="POST" action="{{ route('admin.tenders.announcements.store', $tender) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="ann_title" class="mb-1 block text-xs text-gray-400">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input id="ann_title" type="text" name="title" value="{{ old('title') }}"
                               placeholder="Judul pengumuman..."
                               class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm
                                      text-gray-100 placeholder-gray-600 outline-none
                                      focus:border-violet-500 focus:ring-1 focus:ring-violet-500
                                      @error('title') border-red-600 @enderror">
                        @error('title')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="ann_content" class="mb-1 block text-xs text-gray-400">
                            Isi <span class="text-red-500">*</span>
                        </label>
                        <textarea id="ann_content" name="content" rows="4"
                                  placeholder="Isi pengumuman / aanwijzing..."
                                  class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm
                                         text-gray-100 placeholder-gray-600 outline-none
                                         focus:border-violet-500 focus:ring-1 focus:ring-violet-500
                                         @error('content') border-red-600 @enderror">{{ old('content') }}</textarea>
                        @error('content')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="published_at" class="mb-1 block text-xs text-gray-400">
                            Tanggal Publikasi <span class="text-red-500">*</span>
                        </label>
                        <input id="published_at" type="datetime-local" name="published_at"
                               value="{{ old('published_at') }}"
                               class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm
                                      text-gray-100 outline-none
                                      focus:border-violet-500 focus:ring-1 focus:ring-violet-500
                                      @error('published_at') border-red-600 @enderror">
                        @error('published_at')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit"
                            class="w-full rounded-lg bg-violet-700 px-4 py-2 text-sm font-semibold text-white
                                   hover:bg-violet-600 transition-colors duration-150">
                        + Tambah Aanwijzing
                    </button>
                </form>
            </div>

            {{-- History log --}}
            @if ($tender->histories->count())
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h2 class="mb-3 text-sm font-semibold text-gray-300">Riwayat Aktivitas</h2>
                <ol class="space-y-3">
                    @foreach ($tender->histories->sortByDesc('created_at') as $h)
                        <li class="flex gap-3 text-xs">
                            <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-indigo-500 ring-4 ring-indigo-900/30"></span>
                            <div>
                                <p class="font-medium text-gray-300">{{ str_replace('_', ' ', ucfirst($h->action)) }}</p>
                                @if ($h->old_status && $h->new_status)
                                    <p class="text-gray-500">
                                        {{ ucfirst($h->old_status) }} → {{ ucfirst($h->new_status) }}
                                    </p>
                                @endif
                                @if ($h->description)
                                    <p class="text-gray-600">{{ $h->description }}</p>
                                @endif
                                <p class="mt-0.5 text-gray-700">
                                    {{ $h->actor->name ?? '-' }} &middot;
                                    {{ $h->created_at ? $h->created_at->format('d M Y, H:i') : '-' }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
