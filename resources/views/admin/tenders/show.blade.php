@extends('layouts.admin')

@section('title', $tender->title)
@section('page-title', 'Detail Tender')

@section('content')
<div class="space-y-6">

    {{-- Back + action buttons --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('admin.tenders.index') }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-gray-900 transition-colors">
            <svg class="h-4 w-4 stroke-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Kembali Ke Daftar Tender
        </a>
        <div class="flex flex-wrap items-center gap-3">
            {{-- Peserta Button --}}
            <a href="{{ route('admin.tenders.participants.index', $tender) }}"
               class="inline-flex items-center gap-2 rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-2 text-sm font-bold text-white
                      hover:bg-[#1E3066] transition-colors duration-150">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                </svg>
                Peserta <span class="rounded bg-[#5578D0] px-2 py-0.5 text-xs">{{ $tender->participants->count() }}</span>
            </a>
            {{-- Bid Button --}}
            <a href="{{ route('admin.tenders.bids.index', $tender) }}"
               class="inline-flex items-center gap-2 rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-2 text-sm font-bold text-white
                      hover:bg-[#1E3066] transition-colors duration-150">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                </svg>
                Bid <span class="rounded bg-[#5578D0] px-2 py-0.5 text-xs">{{ $tender->bids->count() }}</span>
            </a>

            @if ($tender->result)
                <a href="{{ route('admin.tenders.result.show', $tender) }}"
                   class="inline-flex items-center gap-2 rounded-md bg-white border border-[#3553A8] px-4 py-2 text-sm font-bold text-[#3553A8]
                          hover:bg-indigo-50 transition-colors duration-150">
                    ★ Hasil
                </a>
            @else
                <a href="{{ route('admin.tenders.winner.create', $tender) }}"
                   class="inline-flex items-center gap-2 rounded-md bg-white border border-[#3553A8] px-4 py-2 text-sm font-bold text-[#3553A8]
                          hover:bg-indigo-50 transition-colors duration-150">
                    Pilih Winner
                </a>
            @endif

            <a href="{{ route('admin.tenders.histories.index', $tender) }}"
               class="inline-flex items-center gap-2 rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-2 text-sm font-bold text-white
                      hover:bg-[#1E3066] transition-colors duration-150">
                History
            </a>
            <a href="{{ route('admin.tenders.edit', $tender) }}"
               class="inline-flex items-center gap-2 rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-2 text-sm font-bold text-white
                      hover:bg-[#1E3066] transition-colors duration-150">
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Left: Detail + Timeline + Announcements ─────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Tender Info --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <div class="mb-6 flex items-start justify-between">
                    <h2 class="text-xl font-bold text-white">{{ $tender->title }}</h2>
                    @php
                        $badge = match($tender->status) {
                            'open'       => 'bg-[#28C5D4] text-white',
                            'aanwijzing' => 'bg-violet-500 text-white',
                            'bidding'    => 'bg-[#F09459] text-white',
                            'closed'     => 'bg-gray-500 text-white',
                            'finished'   => 'bg-[#34D399] text-white',
                            default      => 'bg-[#788B9A] text-white', // draft
                        };
                    @endphp
                    <span class="inline-flex items-center justify-center rounded-full px-4 py-1.5 text-xs font-bold {{ $badge }}">
                        {{ ucfirst($tender->status) }}
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-200 mb-1">Deskripsi</p>
                        <p class="text-sm text-white">{{ $tender->description }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-200 mb-1">Spesifikasi</p>
                        <p class="text-sm text-white whitespace-pre-line">{{ $tender->specification }}</p>
                    </div>
                    <div class="pt-4 border-t border-[#4A6BCC] mt-6">
                        <p class="text-xs font-medium text-indigo-200">Dibuat oleh: {{ $tender->creator->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white">Timeline</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
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
                        <div class="rounded-lg border border-[#4A6BCC] px-4 py-3">
                            <p class="text-xs text-indigo-200 mb-1">{{ $row['label'] }}</p>
                            <p class="font-bold text-white">
                                {{ $row['val'] ? $row['val']->format('d M Y, H:i') : '—' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Announcements list --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white flex items-center">
                    Aanwijzing / Pengumuman
                    <span class="ml-2 text-xs font-normal text-indigo-200">
                        ({{ $tender->announcements->count() }})
                    </span>
                </h2>

                @if ($tender->announcements->isEmpty())
                    <p class="text-sm text-indigo-200">Belum ada pengumuman.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($tender->announcements->sortByDesc('published_at') as $ann)
                            <div class="rounded-lg border border-[#4A6BCC] px-5 py-4">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <p class="font-bold text-white text-base">{{ $ann->title }}</p>
                                    <span class="shrink-0 text-xs text-indigo-200">
                                        {{ $ann->published_at?->format('d M Y, H:i') ?? '-' }}
                                    </span>
                                </div>
                                <p class="text-sm text-white mb-3">{{ $ann->content }}</p>
                                <p class="text-xs text-indigo-200">Oleh: {{ $ann->creator->name ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- ── Right: Update Status + Add Announcement ──────────────────────── --}}
        <div class="space-y-6">

            {{-- Update Status --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-4 text-base font-bold text-white">Ubah Status</h2>
                <form method="POST" action="{{ route('admin.tenders.status', $tender) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="status" class="mb-2 block text-sm font-medium text-indigo-200">Status Baru</label>
                        <select id="status" name="status"
                                class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                                       text-white outline-none focus:border-white focus:ring-1 focus:ring-white
                                       @error('status') border-red-400 @enderror">
                            @foreach (['draft','open','aanwijzing','bidding','closed','finished'] as $s)
                                <option value="{{ $s }}" {{ $tender->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-6">
                        <label for="description" class="mb-2 block text-sm font-medium text-indigo-200">
                            Catatan perubahan (opsional)
                        </label>
                        <input id="description" type="text" name="description"
                               placeholder="Alasan perubahan status..."
                               class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                                      text-white placeholder-indigo-200 outline-none
                                      focus:border-white focus:ring-1 focus:ring-white">
                    </div>
                    <button type="submit"
                            class="w-full rounded-md bg-[#28C5D4] px-4 py-2.5 text-sm font-bold text-white
                                   hover:bg-teal-400 transition-colors duration-150">
                        Ubah Status
                    </button>
                </form>
            </div>

            {{-- Add Announcement --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-4 text-base font-bold text-white">Tambah Aanwijzing</h2>
                <form method="POST" action="{{ route('admin.tenders.announcements.store', $tender) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="ann_title" class="mb-2 block text-sm font-bold text-white">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input id="ann_title" type="text" name="title" value="{{ old('title') }}"
                               placeholder="Judul Pengumuman..."
                               class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                                      text-white placeholder-indigo-200 outline-none
                                      focus:border-white focus:ring-1 focus:ring-white
                                      @error('title') border-red-400 @enderror">
                        @error('title')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="ann_content" class="mb-2 block text-sm font-bold text-white">
                            Isi <span class="text-red-500">*</span>
                        </label>
                        <textarea id="ann_content" name="content" rows="4"
                                  placeholder="Isi Pengumuman / aanwijzing..."
                                  class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                                         text-white placeholder-indigo-200 outline-none
                                         focus:border-white focus:ring-1 focus:ring-white
                                         @error('content') border-red-400 @enderror">{{ old('content') }}</textarea>
                        @error('content')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-6">
                        <label for="published_at" class="mb-2 block text-sm font-bold text-white">
                            Tanggal Publikasi <span class="text-red-500">*</span>
                        </label>
                        <input id="published_at" type="datetime-local" name="published_at"
                               value="{{ old('published_at') }}"
                               class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                                      text-white outline-none [color-scheme:dark]
                                      focus:border-white focus:ring-1 focus:ring-white
                                      @error('published_at') border-red-400 @enderror">
                        @error('published_at')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit"
                            class="w-full rounded-md bg-[#28C5D4] px-4 py-2.5 text-sm font-bold text-white
                                   hover:bg-teal-400 transition-colors duration-150">
                        + Tambah aanwijzing
                    </button>
                </form>
            </div>

            {{-- History log --}}
            @if ($tender->histories->count())
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-base font-bold text-white">Riwayat Aktivitas</h2>
                <div class="space-y-4">
                    @foreach ($tender->histories->sortByDesc('created_at') as $h)
                        <div>
                            <p class="font-bold text-white text-sm mb-1">{{ str_replace('_', ' ', ucfirst($h->action)) }}</p>
                            @if ($h->old_status && $h->new_status)
                                <p class="text-xs text-indigo-200 mb-0.5">
                                    {{ ucfirst($h->old_status) }} → {{ ucfirst($h->new_status) }}
                                </p>
                            @endif
                            @if ($h->description)
                                <p class="text-xs text-indigo-200 mb-0.5">{{ $h->description }}</p>
                            @endif
                            <p class="text-[11px] text-indigo-200 opacity-80 mt-1">
                                {{ $h->actor->name ?? '-' }} -
                                {{ $h->created_at ? $h->created_at->format('d M Y, H:i') : '-' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
