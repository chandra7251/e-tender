@extends('layouts.admin')

@section('title', 'Detail Purchase Order')
@section('page-title', 'Detail Purchase Order')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.tenders.result.show', $tender) }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#3553A8] hover:text-[#2B438A] transition-colors">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Kembali ke Hasil Tender
        </a>
        
        <div class="flex gap-3">
            <a href="{{ route('admin.tenders.histories.index', $tender) }}"
               class="rounded-lg bg-white border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors flex items-center gap-2">
                <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Lihat History
            </a>
            <a href="{{ route('admin.tenders.purchase-order.pdf', $tender) }}"
               class="rounded-lg bg-[#3553A8] px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-[#2B438A] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3553A8] transition-colors flex items-center gap-2">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Unduh PDF
            </a>
        </div>
    </div>

    {{-- Kertas A4 Document Style --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden relative">
        
        {{-- Decorative Top Line --}}
        <div class="absolute top-0 left-0 right-0 h-2 bg-[#3553A8]"></div>

        <div class="p-6 sm:p-10 md:p-12">
            {{-- Header (Kop Surat) --}}
            <div class="text-center border-b-[2px] border-gray-700 pb-3 mb-6">
                <h1 class="text-xl font-bold text-gray-800 uppercase tracking-widest">{{ config('app.name', 'ZETA') }}</h1>
                <p class="text-[13px] text-gray-500 mt-1">Dokumen Surat Pemesanan / Purchase Order (PO) Resmi</p>
            </div>

            <div class="text-right mb-6">
                <h2 class="text-2xl font-bold text-[#2980b9] uppercase mb-1">PURCHASE ORDER</h2>
                <p class="text-[13px] font-bold text-gray-800">Nomor PO: {{ $po->po_number }}</p>
                <p class="text-[13px] font-bold text-gray-800">Tanggal: {{ $po->issued_date ? $po->issued_date->format('d M Y') : date('d M Y') }}</p>
            </div>

            {{-- Info Vendor & Tender (2 Kolom) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="border border-gray-200 bg-gray-50/50 p-4 rounded-sm">
                    <p class="text-[12px] font-bold text-[#2c3e50] uppercase mb-3 border-b border-gray-200 pb-2">KEPADA VENDOR:</p>
                    <p class="text-[13px] font-bold text-gray-800">{{ $po->vendor->company_name ?? '-' }}</p>
                    <p class="text-[13px] text-gray-600 mt-1">Alamat: {{ $po->vendor->address ?? '-' }}</p>
                    <p class="text-[13px] text-gray-600">Email: {{ $po->vendor->user->email ?? '-' }}</p>
                    <p class="text-[13px] text-gray-600">Telp: {{ $po->vendor->phone ?? '-' }}</p>
                </div>

                <div class="border border-gray-200 bg-gray-50/50 p-4 rounded-sm">
                    <p class="text-[12px] font-bold text-[#2c3e50] uppercase mb-3 border-b border-gray-200 pb-2">DETAIL TENDER:</p>
                    <p class="text-[13px] font-bold text-gray-800">{{ $tender->title }}</p>
                    <p class="text-[13px] text-gray-600 mt-1">Status Tender: Selesai</p>
                    <p class="text-[13px] text-gray-600">Diterbitkan oleh: {{ $po->generator->name ?? '-' }}</p>
                </div>
            </div>

            {{-- Table --}}
            <div class="mb-6 overflow-hidden border border-gray-200 rounded-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#2c3e50]">
                            <th class="py-2.5 px-3 text-[13px] font-bold text-white text-center w-[5%] border-r border-[#34495e]">No</th>
                            <th class="py-2.5 px-3 text-[13px] font-bold text-white border-r border-[#34495e]">Nama / Deskripsi Tender</th>
                            <th class="py-2.5 px-3 text-[13px] font-bold text-white text-right w-[25%]">Total Penawaran (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="py-3 px-3 text-[13px] text-gray-800 text-center border-r border-gray-200">1</td>
                            <td class="py-3 px-3 text-[13px] text-gray-800 border-r border-gray-200">
                                {{ $tender->title }}
                            </td>
                            <td class="py-3 px-3 text-[13px] font-bold text-gray-800 text-right">
                                {{ number_format($po->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t border-gray-200">
                            <td colspan="2" class="py-3 px-3 text-[13px] font-bold text-gray-800 text-right border-r border-gray-200">GRAND TOTAL</td>
                            <td class="py-3 px-3 text-[14px] font-bold text-[#2980b9] text-right">
                                Rp {{ number_format($po->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Notes --}}
            @if ($po->notes)
            <div class="mb-4 bg-gray-50/50 p-3 rounded-sm">
                <p class="text-[13px] font-bold text-gray-800 mb-1">Catatan Tambahan:</p>
                <p class="text-[13px] text-gray-700">{!! nl2br(e($po->notes)) !!}</p>
            </div>
            @endif

            <div class="mb-8 border-l-4 border-[#e67e22] pl-4 py-1 bg-white">
                <p class="text-[13px] font-bold text-gray-800 mb-1.5">Ketentuan Sistem Tender & Bidding:</p>
                <ol class="list-decimal pl-5 space-y-0.5 text-[13px] text-gray-700">
                    <li>Dokumen Purchase Order (PO) ini dihasilkan secara otomatis oleh sistem sebagai tahap akhir (Result Output) dari proses Tender.</li>
                    <li>Nilai Grand Total di atas merupakan hasil mutlak dari sistem <span class="italic">Bidding</span> berbasis waktu (Time-Based) yang diajukan oleh Vendor.</li>
                    <li>Vendor pemenang telah melewati proses Registrasi, Verifikasi Dokumen (Approved), serta proses Aanwijzing sesuai aturan sistem.</li>
                </ol>
            </div>

            {{-- Signatures --}}
            <div class="grid grid-cols-2 gap-8 mt-12 mb-8">
                <div class="text-center">
                    <p class="text-[12px] text-gray-800 mb-16">Disetujui Oleh,</p>
                    <p class="text-[13px] font-bold text-gray-900 border-b border-gray-400 inline-block px-4 pb-1">{{ $po->generator->name ?? 'Admin Procurement' }}</p>
                    <p class="text-[11px] text-gray-600 mt-1">Pejabat Pembuat Komitmen (PPK)</p>
                </div>
                <div class="text-center">
                    <p class="text-[12px] text-gray-800 mb-16">Menerima & Menyetujui,</p>
                    <p class="text-[13px] font-bold text-gray-900 border-b border-gray-400 inline-block px-4 pb-1">Direktur/Penanggung Jawab</p>
                    <p class="text-[11px] text-gray-600 mt-1">{{ $po->vendor->company_name ?? 'Vendor' }}</p>
                </div>
            </div>

            <div class="mt-8 pt-4 border-t border-gray-200 text-center text-[10px] text-gray-400 leading-relaxed">
                Dokumen ini digenerate secara otomatis oleh Sistem E-Procurement (Tender & Bidding System) pada {{ now()->format('d M Y, H:i:s') }}.<br>
                Sistem ini dibangun untuk memenuhi requirement Final Project pengadaan end-to-end.
            </div>
        </div>
    </div>

</div>
@endsection
