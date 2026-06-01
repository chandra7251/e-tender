@extends('layouts.admin')

@section('title', $submission->nama_barang)
@section('page-title', 'Pengajuan Vendor')

@section('content')
<div class="space-y-6">

    {{-- Back link --}}
    <a href="{{ route('admin.submissions.index') }}"
       class="inline-flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-gray-900 transition-colors">
        <svg class="h-4 w-4 stroke-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali Ke Daftar Pengajuan
    </a>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Left: Detail Pengajuan ────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Detail Barang/Jasa --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white underline underline-offset-8 decoration-2">
                    Detail Pengajuan
                </h2>
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between items-start">
                        <dt class="text-indigo-200 shrink-0">Nama Barang/Jasa</dt>
                        <dd class="font-bold text-white text-right ml-4">{{ $submission->nama_barang }}</dd>
                    </div>
                    <div class="flex justify-between items-start">
                        <dt class="text-indigo-200 shrink-0">Kategori</dt>
                        <dd class="text-white text-right ml-4">
                            <span class="inline-flex rounded-full bg-[#2B438A] border border-[#4A6BCC] px-3 py-0.5 text-xs font-medium">
                                {{ $submission->kategori ?? '-' }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between items-start">
                        <dt class="text-indigo-200 shrink-0">Estimasi Harga</dt>
                        <dd class="font-bold text-white text-right ml-4">
                            @if ($submission->estimasi_harga)
                                Rp {{ number_format($submission->estimasi_harga, 0, ',', '.') }}
                            @else
                                <span class="text-indigo-300">Tidak disebutkan</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between items-start">
                        <dt class="text-indigo-200 shrink-0">Tanggal Pengajuan</dt>
                        <dd class="text-white text-right ml-4">{{ $submission->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Deskripsi --}}
            @if ($submission->deskripsi)
                <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-bold text-white">Deskripsi</h2>
                    <p class="text-sm text-indigo-50 leading-relaxed whitespace-pre-line">{{ $submission->deskripsi }}</p>
                </div>
            @endif

            {{-- Spesifikasi --}}
            @if ($submission->spesifikasi)
                <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-bold text-white">Spesifikasi</h2>
                    <p class="text-sm text-indigo-50 leading-relaxed whitespace-pre-line">{{ $submission->spesifikasi }}</p>
                </div>
            @endif

            {{-- Catatan dari Vendor --}}
            @if ($submission->catatan)
                <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-bold text-white">Catatan dari Vendor</h2>
                    <div class="rounded-lg border border-[#4A6BCC] bg-[#2B438A] p-4">
                        <p class="text-sm text-white leading-relaxed whitespace-pre-line">{{ $submission->catatan }}</p>
                    </div>
                </div>
            @endif

            {{-- Foto-Foto Pengajuan --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-white flex items-center gap-2">
                    Foto Pengajuan
                    <span class="text-xs font-normal text-indigo-200">({{ $submission->photos->count() }} foto)</span>
                </h2>

                @if ($submission->photos->isEmpty())
                    <p class="text-sm text-indigo-300 italic">Tidak ada foto yang dilampirkan.</p>
                @else
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($submission->photos as $photo)
                            <a href="{{ $photo->photo_url }}" target="_blank"
                               class="group relative overflow-hidden rounded-lg border border-[#4A6BCC] bg-[#2B438A]
                                      aspect-square block hover:border-[#28C5D4] transition-colors duration-150">
                                <img src="{{ $photo->photo_url }}"
                                     alt="Foto pengajuan"
                                     class="h-full w-full object-cover group-hover:opacity-90 transition-opacity duration-150"
                                     onerror="this.src='https://via.placeholder.com/400x400?text=Foto+Tidak+Tersedia'">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100
                                            transition-opacity duration-150 bg-black/40">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 21h18M21 3H3"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- ── Right: Info Vendor & Actions ─────────────────────────────── --}}
        <div class="space-y-6">

            {{-- Info Vendor --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white">Info Vendor</h2>
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Perusahaan</dt>
                        <dd class="font-bold text-white text-right">{{ $submission->vendor?->company_name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Nama</dt>
                        <dd class="text-white text-right">{{ $submission->vendor?->user?->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Email</dt>
                        <dd class="text-white text-right text-xs break-all">{{ $submission->vendor?->user?->email ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Phone</dt>
                        <dd class="text-white text-right">{{ $submission->vendor?->phone ?? '-' }}</dd>
                    </div>
                    <div class="pt-2 border-t border-[#4A6BCC]">
                        <a href="{{ route('admin.vendors.show', $submission->vendor) }}"
                           class="flex items-center justify-center gap-2 w-full rounded-md border border-[#4A6BCC]
                                  bg-[#2B438A] px-4 py-2 text-xs font-semibold text-white
                                  hover:bg-[#1E3066] transition-colors duration-150">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Lihat Profil Vendor
                        </a>
                    </div>
                </dl>
            </div>

            {{-- Status Pengajuan --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white">Status Pengajuan</h2>
                @php
                    $badge = match($submission->status) {
                        'approved' => 'bg-[#28C5D4]',
                        'rejected' => 'bg-[#788B9A]',
                        default    => 'bg-[#F09459]',
                    };
                    $label = match($submission->status) {
                        'approved' => '✓ Disetujui',
                        'rejected' => '✕ Ditolak',
                        default    => '⏳ Menunggu Review',
                    };
                @endphp

                <div class="mb-4">
                    <span class="inline-flex items-center rounded-full px-4 py-1.5 text-xs font-bold text-white {{ $badge }}">
                        {{ $label }}
                    </span>
                </div>

                @if ($submission->reviewed_at)
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <dt class="text-indigo-200">Direview oleh</dt>
                            <dd class="font-bold text-white text-right">{{ $submission->reviewer?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-indigo-200">Tanggal Review</dt>
                            <dd class="text-white text-right">{{ $submission->reviewed_at->format('d M Y, H:i') }}</dd>
                        </div>
                    </dl>
                @endif

                @if ($submission->catatan_admin)
                    <div class="mt-4 rounded-lg border border-[#4A6BCC] bg-[#2B438A] p-4">
                        <p class="text-xs font-semibold text-indigo-200 mb-2">Catatan Admin</p>
                        <p class="text-sm text-white">{{ $submission->catatan_admin }}</p>
                    </div>
                @endif
            </div>

            {{-- ── Action Buttons (only when pending) ─────────────────── --}}
            @if ($submission->status === 'pending')

                {{-- Approve --}}
                <div class="rounded-xl bg-white border-2 border-[#28C5D4] p-6 shadow-sm">
                    <h3 class="mb-4 text-sm font-bold text-[#28C5D4] flex items-center gap-2">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Setujui Pengajuan
                    </h3>
                    <p class="mb-4 text-xs text-gray-500">
                        Pengajuan ini akan ditandai sebagai <strong>Disetujui</strong>.
                        Vendor akan dapat melihat status perubahan di aplikasi mobile.
                    </p>
                    <form method="POST" action="{{ route('admin.submissions.approve', $submission) }}"
                          onsubmit="return confirm('Yakin ingin menyetujui pengajuan ini?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full rounded-md bg-[#28C5D4] px-4 py-2.5 text-sm font-bold text-white
                                       hover:bg-teal-500 transition-colors duration-150">
                            ✓ Setujui Pengajuan
                        </button>
                    </form>
                </div>

                {{-- Reject --}}
                <div class="rounded-xl bg-white border-2 border-red-300 p-6 shadow-sm">
                    <h3 class="mb-4 text-sm font-bold text-red-500 flex items-center gap-2">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tolak Pengajuan
                    </h3>
                    <form method="POST" action="{{ route('admin.submissions.reject', $submission) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label for="catatan_admin" class="mb-2 block text-xs font-semibold text-gray-500">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="catatan_admin" name="catatan_admin" rows="3"
                                      placeholder="Tuliskan alasan penolakan pengajuan ini (min. 10 karakter)..."
                                      class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm
                                             text-gray-900 placeholder-gray-400 outline-none
                                             focus:border-red-500 focus:ring-1 focus:ring-red-500
                                             @error('catatan_admin') border-red-500 @enderror">{{ old('catatan_admin') }}</textarea>
                            @error('catatan_admin')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                onclick="return confirm('Yakin ingin menolak pengajuan ini?')"
                                class="w-full rounded-md bg-red-500 px-4 py-2.5 text-sm font-bold text-white
                                       hover:bg-red-600 transition-colors duration-150">
                            ✕ Tolak Pengajuan
                        </button>
                    </form>
                </div>

            @endif

        </div>
    </div>

</div>
@endsection
