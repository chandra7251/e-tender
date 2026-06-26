@extends('layouts.admin')

@section('title', 'Pengajuan Vendor')
@section('page-title', 'Pengajuan Vendor')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        @php
            $stats = [
                ['label' => 'Total',    'value' => $counts['total'],    'color' => 'bg-[#3553A8]',  'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['label' => 'Pending',  'value' => $counts['pending'],  'color' => 'bg-[#F09459]',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Disetujui','value' => $counts['approved'], 'color' => 'bg-[#28C5D4]',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Ditolak', 'value' => $counts['rejected'], 'color' => 'bg-[#788B9A]',  'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp

        @foreach ($stats as $stat)
            <div class="rounded-xl {{ $stat['color'] }} p-5 shadow-sm flex items-center gap-4">
                <div class="flex-shrink-0 rounded-full bg-white/20 p-2.5">
                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-white/70 uppercase tracking-wide">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold text-white">{{ $stat['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.submissions.index') }}"
              class="flex flex-col gap-3 sm:flex-row sm:items-center mb-6">

            <div class="relative flex-1">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-indigo-200">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama barang, kategori, atau perusahaan..."
                       class="w-full rounded-md border-0 bg-[#2B438A] py-2.5 pl-10 pr-4 text-sm
                              text-white placeholder-indigo-200 outline-none focus:ring-2 focus:ring-white">
            </div>

            <select name="status"
                    class="rounded-md border-0 bg-white px-4 py-2.5 text-sm font-medium
                           text-gray-700 outline-none focus:ring-2 focus:ring-[#2B438A]">
                <option value="">Semua Status</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <button type="submit"
                    class="rounded-md bg-[#2B438A] px-6 py-2.5 text-sm font-semibold text-white
                           hover:bg-[#1E3066] transition-colors duration-150">
                Filter
            </button>

            @if (request('search') || request('status'))
                <a href="{{ route('admin.submissions.index') }}"
                   class="rounded-md border border-[#4A6BCC] px-4 py-2.5 text-sm text-indigo-200
                          hover:text-white transition-colors duration-150">
                    Reset
                </a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-white" style="min-width: 700px;">
                <thead>
                    <tr class="border-b border-[#4A6BCC] text-left text-xs font-bold uppercase tracking-wider text-indigo-100">
                        <th class="px-2 py-4">Barang / Jasa</th>
                        <th class="px-2 py-4">Kategori</th>
                        <th class="px-2 py-4">Perusahaan</th>
                        <th class="px-2 py-4">Est. Harga</th>
                        <th class="px-2 py-4">Status</th>
                        <th class="px-2 py-4">Tanggal</th>
                        <th class="px-2 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#4A6BCC]">
                    @forelse ($submissions as $submission)
                        <tr class="hover:bg-[#2B438A] transition-colors duration-150">
                            <td class="px-2 py-4">
                                <div>
                                    <p class="font-semibold tracking-wide">{{ $submission->nama_barang }}</p>
                                    @if ($submission->photos->count() > 0)
                                        <p class="text-xs text-indigo-300 mt-0.5">
                                            📷 {{ $submission->photos->count() }} foto
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-2 py-4 text-indigo-100">
                                <span class="inline-flex rounded-full bg-[#2B438A] border border-[#4A6BCC] px-2.5 py-0.5 text-xs font-medium">
                                    {{ $submission->kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="px-2 py-4 text-indigo-50">
                                <div>
                                    <p class="font-medium">{{ $submission->vendor?->company_name ?? '-' }}</p>
                                    <p class="text-xs text-indigo-300">{{ $submission->vendor?->user?->email ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="px-2 py-4 text-indigo-50">
                                @if ($submission->estimasi_harga)
                                    Rp {{ number_format($submission->estimasi_harga, 0, ',', '.') }}
                                @else
                                    <span class="text-indigo-300">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-4">
                                @php
                                    $badge = match($submission->status) {
                                        'approved' => 'bg-[#28C5D4] text-white',
                                        'rejected' => 'bg-[#788B9A] text-white',
                                        default    => 'bg-[#F09459] text-white',
                                    };
                                    $label = match($submission->status) {
                                        'approved' => '✓ Disetujui',
                                        'rejected' => '✕ Ditolak',
                                        default    => '⏳ Pending',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-2 py-4 text-indigo-50 text-xs">
                                {{ $submission->created_at->format('d M Y') }}<br>
                                <span class="text-indigo-300">{{ $submission->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-2 py-4 text-right">
                                <a href="{{ route('admin.submissions.show', $submission) }}"
                                   class="rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-1.5 text-xs font-semibold text-white
                                          hover:bg-[#1E3066] transition-colors duration-150">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-2 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="h-10 w-10 text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm text-indigo-200">Tidak ada pengajuan ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($submissions->hasPages())
        <div class="mt-4">
            {{ $submissions->links() }}
        </div>
    @endif

</div>
@endsection
