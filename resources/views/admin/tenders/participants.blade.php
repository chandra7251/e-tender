@extends('layouts.admin')

@section('title', 'Peserta Tender')
@section('page-title', 'Peserta Tender')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-gray-900 transition-colors">
            <svg class="h-4 w-4 stroke-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Kembali Ke Detail Tender
        </a>
        <div class="text-sm">
            <span class="font-bold text-[#3553A8] truncate max-w-sm inline-block">{{ $tender->title }}</span>
        </div>
    </div>

    <div class="rounded-md bg-[#3553A8] px-6 py-4 shadow-sm">
        <p class="text-sm font-bold text-white flex items-center">
            Total Peserta:
            <span class="ml-2 text-xl font-bold">{{ $participants->count() }}</span>
        </p>
    </div>

    <div class="overflow-x-auto rounded-md bg-[#3553A8] shadow-sm">
        <table class="w-full text-sm text-white">
            <thead>
                <tr class="border-b border-[#4A6BCC] text-left text-xs font-bold uppercase tracking-wider text-indigo-200">
                    <th class="px-6 py-4 w-16">#</th>
                    <th class="px-6 py-4">Perusahaan</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Status Vendor</th>
                    <th class="px-6 py-4">Bergabung</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#4A6BCC]">
                @forelse ($participants as $i => $participant)
                    <tr class="hover:bg-[#2B438A] transition-colors duration-150">
                        <td class="px-6 py-4 text-indigo-50 font-bold">{{ $i + 1 }}</td>
                        <td class="px-6 py-4 font-bold">
                            {{ $participant->vendor->company_name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-indigo-50">
                            {{ $participant->vendor->user->email ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $status = $participant->vendor->verification_status ?? 'unknown';
                                $badge = match($status) {
                                    'approved' => 'bg-[#28C5D4] text-white',
                                    'rejected' => 'bg-[#788B9A] text-white',
                                    default    => 'bg-[#F09459] text-white',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-indigo-50">
                            {{ $participant->joined_at?->format('d M Y, H:i') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-indigo-200">
                            Belum ada vendor yang bergabung pada tender ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
