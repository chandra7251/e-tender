@extends('layouts.admin')

@section('title', 'Laporan & Export')
@section('page-title', 'Laporan & Export Data')

@section('content')
<div class="w-full space-y-6">

    <div class="rounded-xl bg-[#3553A8] px-6 py-5 shadow-sm">
        <h2 class="text-lg font-bold text-white">Pusat Laporan</h2>
        <p class="text-sm text-indigo-200 mt-1">Download laporan data tender, vendor, dan audit log dalam format CSV.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Tender Report --}}
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                        <svg class="h-5 w-5 text-[#3553A8]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Laporan Tender</h3>
                        <p class="text-xs text-gray-500">Rekapitulasi semua tender</p>
                    </div>
                </div>
                <form method="GET" action="{{ route('admin.reports.export.tenders') }}" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-[#3553A8]">
                            <option value="">Semua Status</option>
                            @foreach (['draft','open','aanwijzing','bidding','closed','finished'] as $s)
                                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Dari</label>
                            <input type="date" name="date_from" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-[#3553A8]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai</label>
                            <input type="date" name="date_to" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-[#3553A8]">
                        </div>
                    </div>
                    <button type="submit"
                            class="w-full rounded-md bg-[#3553A8] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#2B438A] transition-colors flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Download CSV
                    </button>
                </form>
            </div>
        </div>

        {{-- Vendor Report --}}
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                        <svg class="h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Laporan Vendor</h3>
                        <p class="text-xs text-gray-500">Data vendor dan kinerja</p>
                    </div>
                </div>
                <form method="GET" action="{{ route('admin.reports.export.vendors') }}" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status Verifikasi</label>
                        <select name="status" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-[#3553A8]">
                            <option value="">Semua</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full rounded-md bg-purple-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-purple-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Download CSV
                    </button>
                </form>
            </div>
        </div>

        {{-- Audit Log Report --}}
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100">
                        <svg class="h-5 w-5 text-amber-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 1.5a.75.75 0 01.75.75V4.5a.75.75 0 01-1.5 0V2.25A.75.75 0 0112 1.5zM5.636 4.136a.75.75 0 011.06 0l1.592 1.591a.75.75 0 01-1.061 1.06l-1.591-1.59a.75.75 0 010-1.061z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Audit Log</h3>
                        <p class="text-xs text-gray-500">Log aktivitas sistem</p>
                    </div>
                </div>
                <form method="GET" action="{{ route('admin.reports.export.audit-logs') }}" class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Dari</label>
                            <input type="date" name="date_from" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-[#3553A8]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai</label>
                            <input type="date" name="date_to" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm outline-none focus:border-[#3553A8]">
                        </div>
                    </div>
                    <button type="submit"
                            class="w-full rounded-md bg-amber-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-amber-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Download CSV
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
