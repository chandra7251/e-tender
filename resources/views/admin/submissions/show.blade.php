@extends('layouts.admin')

@section('title', $submission->nama_barang)
@section('page-title', 'Pengajuan Vendor')

@section('content')
<div class="space-y-6">

    <a href="{{ route('admin.submissions.index') }}"
       class="inline-flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-gray-900 transition-colors">
        <svg class="h-4 w-4 stroke-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali Ke Daftar Pengajuan
    </a>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="lg:col-span-2 space-y-6">

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

            @if ($submission->deskripsi)
                <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-bold text-white">Deskripsi</h2>
                    <p class="text-sm text-indigo-50 leading-relaxed whitespace-pre-line">{{ $submission->deskripsi }}</p>
                </div>
            @endif

            @if ($submission->spesifikasi)
                <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-bold text-white">Spesifikasi</h2>
                    <p class="text-sm text-indigo-50 leading-relaxed whitespace-pre-line">{{ $submission->spesifikasi }}</p>
                </div>
            @endif

            @if ($submission->catatan)
                <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                    <h2 class="mb-4 text-lg font-bold text-white">Catatan dari Vendor</h2>
                    <div class="rounded-lg border border-[#4A6BCC] bg-[#2B438A] p-4">
                        <p class="text-sm text-white leading-relaxed whitespace-pre-line">{{ $submission->catatan }}</p>
                    </div>
                </div>
            @endif

            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        Foto Pengajuan
                        <span class="text-xs font-normal text-indigo-200">({{ $submission->photos->count() }} foto)</span>
                    </h2>

                    @if ($submission->photos->isNotEmpty())
                        <button id="btn-download-all"
                                onclick="downloadAllPhotos()"
                                class="inline-flex items-center gap-2 rounded-md bg-[#28C5D4] px-4 py-2
                                       text-xs font-bold text-white hover:bg-teal-400 transition-colors duration-150 shrink-0">
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                            </svg>
                            Download Semua
                        </button>
                    @endif
                </div>

                @if ($submission->photos->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <svg class="h-12 w-12 text-indigo-400 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 21h18M21 3H3M6.75 6.75h.008v.008H6.75V6.75z"/>
                        </svg>
                        <p class="text-sm text-indigo-300 italic">Tidak ada foto yang dilampirkan.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        @foreach ($submission->photos as $index => $photo)
                            <div class="group flex flex-col overflow-hidden rounded-xl border border-[#4A6BCC]
                                        bg-[#2B438A] hover:border-[#28C5D4] transition-colors duration-150">

                                <div class="relative aspect-square overflow-hidden cursor-pointer"
                                     onclick="openLightbox('{{ $photo->photo_url }}', {{ $index + 1 }}, {{ $submission->photos->count() }})">
                                    <img src="{{ $photo->photo_url }}"
                                         alt="Foto pengajuan {{ $index + 1 }}"
                                         class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-105"
                                         onerror="this.parentElement.innerHTML='<div class=\'flex h-full w-full items-center justify-center bg-[#1E3066]\'><p class=\'text-xs text-indigo-300 text-center px-2\'>Foto tidak tersedia</p></div>'">

                                    <div class="absolute inset-0 flex items-center justify-center opacity-0
                                                group-hover:opacity-100 transition-opacity duration-200 bg-black/40">
                                        <svg class="h-8 w-8 text-white drop-shadow" xmlns="http://www.w3.org/2000/svg"
                                             fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                                        </svg>
                                    </div>

                                    <span class="absolute top-2 left-2 rounded-full bg-black/50 px-2 py-0.5
                                                 text-[10px] font-bold text-white">
                                        {{ $index + 1 }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-2 px-3 py-2 border-t border-[#4A6BCC]">
                                    <span class="text-[11px] text-indigo-300 truncate">
                                        Foto {{ $index + 1 }}
                                    </span>
                                    <a href="{{ $photo->photo_url }}"
                                       download="pengajuan-{{ $submission->id }}-foto-{{ $index + 1 }}.jpg"
                                       class="inline-flex items-center gap-1 rounded-md bg-[#3553A8] border border-[#4A6BCC]
                                              px-2.5 py-1 text-[11px] font-semibold text-white shrink-0
                                              hover:bg-[#28C5D4] hover:border-[#28C5D4] transition-colors duration-150"
                                       title="Download foto {{ $index + 1 }}">
                                        <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                             viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @php
                        $photoData = $submission->photos->map(function($p, $i) use ($submission) {
                            return [
                                'url'      => $p->photo_url,
                                'filename' => 'pengajuan-' . $submission->id . '-foto-' . ($i + 1) . '.jpg',
                            ];
                        })->values();
                    @endphp
                    <script>
                        var submissionPhotos = @json($photoData);

                        function downloadAllPhotos() {
                            var btn = document.getElementById('btn-download-all');
                            btn.disabled = true;
                            btn.textContent = 'Mengunduh...';

                            var delay = 0;
                            submissionPhotos.forEach(function(photo) {
                                setTimeout(function() {
                                    var a = document.createElement('a');
                                    a.href = photo.url;
                                    a.download = photo.filename;
                                    document.body.appendChild(a);
                                    a.click();
                                    document.body.removeChild(a);
                                }, delay);
                                delay += 500; // jeda 500ms antar download agar browser tidak blokir
                            });

                            setTimeout(function() {
                                btn.disabled = false;
                                btn.innerHTML = '<svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg> Download Semua';
                            }, delay + 500);
                        }

                        function openLightbox(url, num, total) {
                            var overlay = document.createElement('div');
                            overlay.id = 'lightbox-overlay';
                            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;cursor:pointer;';
                            overlay.innerHTML = '<div style="position:relative;max-width:90vw;max-height:90vh;">'
                                + '<img src="' + url + '" style="max-width:100%;max-height:85vh;object-fit:contain;border-radius:0.75rem;box-shadow:0 25px 50px rgba(0,0,0,0.5);">'
                                + '<div style="position:absolute;bottom:-2rem;left:50%;transform:translateX(-50%);color:rgba(255,255,255,0.7);font-size:0.75rem;white-space:nowrap;">Foto ' + num + ' dari ' + total + ' — Klik di luar untuk tutup</div>'
                                + '</div>';
                            overlay.onclick = function(e) { if (e.target === overlay || e.target.tagName !== 'IMG') document.body.removeChild(overlay); };
                            document.body.appendChild(overlay);
                        }
                    </script>
                @endif
            </div>

        </div>

        <div class="space-y-6">

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

            @if ($submission->status === 'pending')

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
                                      required minlength="10"
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
