@extends('layouts.admin')

@section('title', $vendor->company_name)
@section('page-title', 'Detail Vendor')

@section('content')
<div class="space-y-6">

    {{-- Back link --}}
    <a href="{{ route('admin.vendors.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-300 transition-colors">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Daftar Vendor
    </a>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ── Left: Vendor Info ──────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Company Info --}}
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h2 class="mb-4 text-base font-semibold text-gray-100">Informasi Perusahaan</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nama Perusahaan</dt>
                        <dd class="font-medium text-gray-100">{{ $vendor->company_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="text-gray-300">{{ $vendor->phone ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Alamat</dt>
                        <dd class="text-gray-300 text-right max-w-xs">{{ $vendor->address ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Terdaftar</dt>
                        <dd class="text-gray-300">{{ $vendor->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- User Info --}}
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h2 class="mb-4 text-base font-semibold text-gray-100">Akun User</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nama</dt>
                        <dd class="font-medium text-gray-100">{{ $vendor->user->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Email</dt>
                        <dd class="text-gray-300">{{ $vendor->user->email ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Role</dt>
                        <dd class="text-gray-300">{{ $vendor->user->role ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Documents --}}
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h2 class="mb-4 text-base font-semibold text-gray-100">
                    Dokumen Vendor
                    <span class="ml-2 text-xs font-normal text-gray-500">({{ $vendor->documents->count() }} file)</span>
                </h2>

                @if ($vendor->documents->isEmpty())
                    <p class="text-sm text-gray-600">Belum ada dokumen yang diunggah.</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($vendor->documents as $doc)
                            <li class="flex items-center justify-between rounded-lg border border-gray-800 bg-gray-800/50 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <svg class="h-5 w-5 shrink-0 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-200">{{ $doc->file_name }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                                            &middot; {{ number_format($doc->file_size / 1024, 0) }} KB</p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600">
                                    {{ $doc->uploaded_at ? \Carbon\Carbon::parse($doc->uploaded_at)->format('d M Y') : '-' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>

        {{-- ── Right: Verification Status & Actions ───────────────────────── --}}
        <div class="space-y-6">

            {{-- Status Card --}}
            <div class="rounded-xl border border-gray-800 bg-gray-900 p-6">
                <h2 class="mb-4 text-base font-semibold text-gray-100">Status Verifikasi</h2>

                @php
                    $badge = match($vendor->verification_status) {
                        'approved' => 'bg-emerald-900/50 text-emerald-400 border-emerald-700',
                        'rejected' => 'bg-red-900/50 text-red-400 border-red-700',
                        default    => 'bg-amber-900/50 text-amber-400 border-amber-700',
                    };
                @endphp

                <span class="inline-flex items-center rounded-full border px-3 py-1 text-sm font-semibold {{ $badge }}">
                    {{ ucfirst($vendor->verification_status) }}
                </span>

                @if ($vendor->verified_at)
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Diverifikasi oleh</dt>
                            <dd class="text-gray-300">{{ $vendor->verifier->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Tanggal</dt>
                            <dd class="text-gray-300">{{ $vendor->verified_at->format('d M Y, H:i') }}</dd>
                        </div>
                    </dl>
                @endif

                @if ($vendor->verification_notes)
                    <div class="mt-4 rounded-lg border border-gray-700 bg-gray-800/50 p-3">
                        <p class="text-xs font-medium text-gray-500 mb-1">Catatan</p>
                        <p class="text-sm text-gray-300">{{ $vendor->verification_notes }}</p>
                    </div>
                @endif
            </div>

            {{-- ── Actions (only shown when pending) ─────────────────────── --}}
            @if ($vendor->verification_status === 'pending')

                {{-- Approve Form --}}
                <div class="rounded-xl border border-emerald-800 bg-emerald-900/20 p-6">
                    <h3 class="mb-3 text-sm font-semibold text-emerald-400">Approve Vendor</h3>
                    <form method="POST" action="{{ route('admin.vendors.approve', $vendor) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="approve_notes" class="mb-1 block text-xs text-gray-400">
                                Catatan (opsional)
                            </label>
                            <textarea id="approve_notes" name="notes" rows="3"
                                      placeholder="Catatan persetujuan..."
                                      class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm
                                             text-gray-100 placeholder-gray-600 outline-none
                                             focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">{{ old('notes') }}</textarea>
                        </div>
                        <button type="submit"
                                class="w-full rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white
                                       hover:bg-emerald-600 transition-colors duration-150">
                            ✓ Approve
                        </button>
                    </form>
                </div>

                {{-- Reject Form --}}
                <div class="rounded-xl border border-red-800 bg-red-900/20 p-6">
                    <h3 class="mb-3 text-sm font-semibold text-red-400">Reject Vendor</h3>
                    <form method="POST" action="{{ route('admin.vendors.reject', $vendor) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="reject_notes" class="mb-1 block text-xs text-gray-400">
                                Alasan penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="reject_notes" name="notes" rows="3"
                                      placeholder="Tuliskan alasan penolakan..."
                                      class="w-full rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm
                                             text-gray-100 placeholder-gray-600 outline-none
                                             focus:border-red-500 focus:ring-1 focus:ring-red-500
                                             @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                class="w-full rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white
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
