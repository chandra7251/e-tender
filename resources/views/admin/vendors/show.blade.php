@extends('layouts.admin')

@section('title', $vendor->company_name)
@section('page-title', 'Vendor Management')

@section('content')
<div class="space-y-6">

    {{-- Back link --}}
    <a href="{{ route('admin.vendors.index') }}"
       class="inline-flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-gray-900 transition-colors">
        <svg class="h-4 w-4 stroke-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali Ke Daftar Vendor
    </a>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Left: Vendor Info ──────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Company Info --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white underline underline-offset-8 decoration-2">Informasi Perusahaan</h2>
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Nama Perusahaan</dt>
                        <dd class="font-bold text-white text-right">{{ $vendor->company_name }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Phone</dt>
                        <dd class="text-white text-right">{{ $vendor->phone ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Alamat</dt>
                        <dd class="text-white text-right max-w-xs">{{ $vendor->address ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Terdaftar</dt>
                        <dd class="text-white text-right">{{ $vendor->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- User Info --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white">Akun User</h2>
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Nama</dt>
                        <dd class="font-bold text-white text-right">{{ $vendor->user->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Email</dt>
                        <dd class="text-white text-right">{{ $vendor->user->email ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between items-center">
                        <dt class="text-indigo-200">Role</dt>
                        <dd class="text-white text-right">{{ $vendor->user->role ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Documents --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white flex items-center">
                    Dokumen Vendor
                    <span class="ml-2 text-xs font-normal text-indigo-200">({{ $vendor->documents->count() }} file)</span>
                </h2>

                @if ($vendor->documents->isEmpty())
                    <p class="text-sm text-indigo-200">Belum ada dokumen yang diunggah.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($vendor->documents as $doc)
                            <li class="flex items-center justify-between rounded-lg border border-[#4A6BCC] bg-[#2B438A] px-4 py-3">
                                <div class="flex items-center gap-3">
                                    {{-- Icon file berdasarkan tipe MIME --}}
                                    @if (str_contains($doc->mime_type ?? '', 'pdf'))
                                        <svg class="h-5 w-5 shrink-0 text-red-300" xmlns="http://www.w3.org/2000/svg"
                                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 shrink-0 text-indigo-200" xmlns="http://www.w3.org/2000/svg"
                                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 7.5h18M3 12h18M3 16.5h18"/>
                                        </svg>
                                    @endif

                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $doc->file_name }}</p>
                                        <p class="text-xs text-indigo-200">
                                            {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                                            &middot; {{ number_format($doc->file_size / 1024, 0) }} KB
                                            &middot; {{ $doc->uploaded_at ? \Carbon\Carbon::parse($doc->uploaded_at)->format('d M Y') : '-' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Tombol download — hanya admin yang bisa akses karena route ini dilindungi middleware auth + role:admin --}}
                                <a href="{{ route('admin.vendors.documents.download', [$vendor, $doc]) }}"
                                   title="Download {{ $doc->file_name }}"
                                   class="ml-4 inline-flex shrink-0 items-center gap-1.5 rounded-md bg-[#28C5D4] px-3 py-1.5 text-xs font-bold text-white hover:bg-teal-500 transition-colors duration-150">
                                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                    </svg>
                                    Download
                                </a>
                            </li>
                        @endforeach

                    </ul>
                @endif
            </div>

        </div>

        {{-- ── Right: Verification Status & Actions ───────────────────────── --}}
        <div class="space-y-6">

            {{-- Status Card --}}
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white">Status Verifikasi</h2>

                @php
                    $badge = match($vendor->verification_status) {
                        'approved' => 'bg-[#28C5D4]',
                        'rejected' => 'bg-[#788B9A]',
                        default    => 'bg-[#F09459]',
                    };
                @endphp

                <div class="mb-6">
                    <span class="inline-flex items-center rounded-full px-4 py-1.5 text-xs font-bold text-white {{ $badge }}">
                        {{ ucfirst($vendor->verification_status) }}
                    </span>
                </div>

                @if ($vendor->verified_at)
                    <dl class="space-y-4 text-sm">
                        <div class="flex justify-between items-center">
                            <dt class="text-indigo-200">Diverifikasi oleh</dt>
                            <dd class="font-bold text-white text-right">{{ $vendor->verifier->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-indigo-200">Tanggal</dt>
                            <dd class="text-white text-right">{{ $vendor->verified_at->format('d M Y, H:i') }}</dd>
                        </div>
                    </dl>
                @endif

                @if ($vendor->verification_notes)
                    <div class="mt-6 rounded-lg border border-[#4A6BCC] bg-[#2B438A] p-4">
                        <p class="text-xs font-semibold text-indigo-200 mb-2">Catatan</p>
                        <p class="text-sm text-white">{{ $vendor->verification_notes }}</p>
                    </div>
                @endif
            </div>

            {{-- ── Actions (only shown when pending) ─────────────────────── --}}
            @if ($vendor->verification_status === 'pending')

                {{-- Approve Form --}}
                <div class="rounded-xl bg-white border border-[#28C5D4] p-6 shadow-sm">
                    <h3 class="mb-4 text-sm font-bold text-[#28C5D4]">Approve Vendor</h3>
                    <form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label for="approve_notes" class="mb-2 block text-xs font-semibold text-gray-500">
                                Catatan (opsional)
                            </label>
                            <textarea id="approve_notes" name="notes" rows="3"
                                      placeholder="Catatan persetujuan..."
                                      class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm
                                             text-gray-900 placeholder-gray-400 outline-none
                                             focus:border-[#28C5D4] focus:ring-1 focus:ring-[#28C5D4]"></textarea>
                        </div>
                        <button type="submit"
                                class="w-full rounded-md bg-[#28C5D4] px-4 py-2.5 text-sm font-bold text-white
                                       hover:bg-teal-500 transition-colors duration-150">
                            ✓ Approve
                        </button>
                    </form>
                </div>

                {{-- Reject Form --}}
                <div class="rounded-xl bg-white border border-red-200 p-6 shadow-sm">
                    <h3 class="mb-4 text-sm font-bold text-red-500">Reject Vendor</h3>
                    <form method="POST" action="{{ route('admin.vendors.reject', $vendor) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label for="reject_notes" class="mb-2 block text-xs font-semibold text-gray-500">
                                Alasan penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="reject_notes" name="notes" rows="3"
                                      placeholder="Tuliskan alasan penolakan..."
                                      class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm
                                             text-gray-900 placeholder-gray-400 outline-none
                                             focus:border-red-500 focus:ring-1 focus:ring-red-500
                                             @error('notes') border-red-500 @enderror"></textarea>
                            @error('notes')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                class="w-full rounded-md bg-red-500 px-4 py-2.5 text-sm font-bold text-white
                                       hover:bg-red-600 transition-colors duration-150">
                            ✕ Reject
                        </button>
                    </form>
                </div>

            @endif

        </div>
    </div>

</div>
@endsection
