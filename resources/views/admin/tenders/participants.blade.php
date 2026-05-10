@extends('layouts.admin')

@section('title', 'Peserta Tender')
@section('page-title', 'Peserta Tender')

@section('content')
<div class="space-y-4">

    {{-- Back + header --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-300 transition-colors">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Kembali ke Detail Tender
        </a>
        <div class="text-sm text-gray-500">
            <span class="font-medium text-gray-300 truncate max-w-xs inline-block">{{ $tender->title }}</span>
        </div>
    </div>

    {{-- Stat --}}
    <div class="rounded-xl border border-gray-800 bg-gray-900 px-5 py-4">
        <p class="text-sm text-gray-500">
            Total Peserta:
            <span class="ml-1 text-2xl font-bold text-indigo-400">{{ $participants->count() }}</span>
        </p>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-800 text-left text-xs font-semibold uppercase tracking-widest text-gray-500">
                    <th class="px-5 py-3">#</th>
                    <th class="px-5 py-3">Perusahaan</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Status Vendor</th>
                    <th class="px-5 py-3">Bergabung</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse ($participants as $i => $participant)
                    <tr class="hover:bg-gray-800/40 transition-colors duration-100">
                        <td class="px-5 py-3 text-gray-600">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 font-medium text-gray-100">
                            {{ $participant->vendor->company_name ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-gray-400">
                            {{ $participant->vendor->user->email ?? '-' }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $status = $participant->vendor->verification_status ?? 'unknown';
                                $badge = match($status) {
                                    'approved' => 'bg-emerald-900/50 text-emerald-400 border-emerald-700',
                                    'rejected' => 'bg-red-900/50 text-red-400 border-red-700',
                                    default    => 'bg-amber-900/50 text-amber-400 border-amber-700',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $badge }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500">
                            {{ $participant->joined_at?->format('d M Y, H:i') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-600">
                            Belum ada vendor yang bergabung pada tender ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
