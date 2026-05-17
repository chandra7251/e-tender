@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Top Cards Grid --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

        {{-- 1. TOTAL VENDOR CARD --}}
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 tracking-wide">TOTAL VENDOR</h3>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#E8EFFF] text-[#3553A8]">
                    {{-- Users Icon --}}
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
                    </svg>
                </div>
            </div>
            
            <div class="mb-4">
                <span class="text-5xl font-extrabold text-gray-900">{{ $stats['vendor_total'] ?? 7 }}</span>
            </div>

            {{-- Progress Bar --}}
            @php
                $totalVendor = $stats['vendor_total'] ?? 7;
                $approved = $stats['vendor_approved'] ?? 4;
                $pending = $stats['vendor_pending'] ?? 2;
                $rejected = $stats['vendor_rejected'] ?? 1;

                $pctApp = $totalVendor > 0 ? ($approved / $totalVendor) * 100 : 0;
                $pctPen = $totalVendor > 0 ? ($pending / $totalVendor) * 100 : 0;
                $pctRej = $totalVendor > 0 ? ($rejected / $totalVendor) * 100 : 0;
            @endphp
            <div class="mb-4 flex h-3 w-full overflow-hidden rounded-full bg-gray-100">
                <div style="width: {{ $pctRej }}%" class="bg-[#788B9A]"></div>
                <div style="width: {{ $pctPen }}%" class="bg-[#F09459]"></div>
                <div style="width: {{ $pctApp }}%" class="bg-[#28C5D4]"></div>
            </div>

            {{-- Legend --}}
            <div class="space-y-1.5 text-xs font-semibold text-gray-800">
                <div class="flex items-center gap-2">
                    <div class="h-2.5 w-2.5 rounded-full bg-[#28C5D4]"></div>
                    <span>Approved : {{ $approved }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-2.5 w-2.5 rounded-full bg-[#F09459]"></div>
                    <span>Pending &nbsp;&nbsp;&nbsp;: {{ $pending }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-2.5 w-2.5 rounded-full bg-[#788B9A]"></div>
                    <span>Rejected &nbsp;&nbsp;: {{ $rejected }}</span>
                </div>
            </div>
        </div>

        {{-- 2. TOTAL TENDER CARD --}}
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 tracking-wide">TOTAL TENDER</h3>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#E8EFFF] text-[#3553A8]">
                    {{-- Document Icon --}}
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            
            <div class="mb-4">
                <span class="text-5xl font-extrabold text-gray-900">{{ $stats['tender_total'] ?? 5 }}</span>
            </div>

            {{-- Progress Bar --}}
            @php
                $totalTender = $stats['tender_total'] ?? 5;
                $active = $stats['tender_active'] ?? 2;
                $finished = $stats['tender_finished'] ?? 3;

                $pctAct = $totalTender > 0 ? ($active / $totalTender) * 100 : 0;
                $pctFin = $totalTender > 0 ? ($finished / $totalTender) * 100 : 0;
            @endphp
            <div class="mb-4 flex h-3 w-full overflow-hidden rounded-full bg-gray-100">
                <div style="width: {{ $pctAct }}%" class="bg-[#C892FF]"></div>
                <div style="width: {{ $pctFin }}%" class="bg-[#8BE8F6]"></div>
            </div>

            {{-- Legend --}}
            <div class="space-y-1.5 text-xs font-semibold text-gray-800">
                <div class="flex items-center gap-2">
                    <div class="h-2.5 w-2.5 rounded-full bg-[#C892FF]"></div>
                    <span>Tender Aktif &nbsp;&nbsp;&nbsp;: {{ $active }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="h-2.5 w-2.5 rounded-full bg-[#8BE8F6]"></div>
                    <span>Tender Selesai : {{ $finished }}</span>
                </div>
            </div>
        </div>

        {{-- 3. BIDDING & PESERTA CARD --}}
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-bold text-gray-900 tracking-wide">BIDDING & PESERTA</h3>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#E8EFFF] text-[#3553A8]">
                    {{-- Gavel Icon --}}
                    <svg class="h-6 w-6 transform -rotate-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 15.75L18.75 11.25a2.121 2.121 0 00-3-3l-4.5 4.5m-5.25 3L8.25 10.5M3.75 14.25L7.5 18m2.25-3l1.5-1.5m6-1.5l1.5-1.5m-1.5-6l4.5 4.5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h4.5v-1.5H3V21z" />
                    </svg>
                </div>
            </div>
            
            <div class="flex justify-between mt-2 mb-6">
                <div class="text-center">
                    <p class="text-4xl font-extrabold text-gray-900">{{ $stats['bid_total'] ?? 1 }}</p>
                    <p class="text-[10px] font-bold text-gray-700 uppercase mt-1">Total Bid</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-extrabold text-gray-900">{{ $stats['participant_total'] ?? 1 }}</p>
                    <p class="text-[10px] font-bold text-gray-700 uppercase mt-1">Total Peserta</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl font-extrabold text-gray-900">{{ $stats['active_bidding_tenders'] ?? 0 }}</p>
                    <p class="text-[10px] font-bold text-gray-700 uppercase mt-1">Tender Bidding</p>
                </div>
            </div>

            <div class="mt-auto">
                <p class="text-[10px] font-bold text-[#3553A8] uppercase mb-1">Bid Terendah :</p>
                <p class="text-2xl font-extrabold text-gray-900">
                    {{ isset($stats['lowest_bid']) && $stats['lowest_bid'] ? 'Rp ' . number_format($stats['lowest_bid'], 0, ',', '.') : 'Rp 92.500.000' }}
                </p>
            </div>
        </div>

    </div>

    {{-- Chart Card --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        <h3 class="mb-6 text-lg font-bold text-gray-800">Recent Tender & Bidding Activity</h3>
        
        {{-- Chart Mockup with SVG --}}
        <div class="relative w-full h-[300px] border-b border-l border-gray-200">
            <!-- Grid lines -->
            <div class="absolute inset-0 flex flex-col justify-between z-0">
                <div class="w-full border-b border-gray-100 h-0"></div>
                <div class="w-full border-b border-gray-100 h-0"></div>
                <div class="w-full border-b border-gray-100 h-0"></div>
                <div class="w-full border-b border-gray-100 h-0"></div>
            </div>
            
            <!-- Y-axis labels -->
            <div class="absolute left-[-20px] top-0 bottom-0 flex flex-col justify-between text-xs text-gray-400 font-medium">
                <span>4</span>
                <span>3</span>
                <span>2</span>
                <span>1</span>
                <span>0</span>
            </div>

            <!-- SVG Curves -->
            <svg class="absolute inset-0 h-full w-full z-10" viewBox="0 0 1000 300" preserveAspectRatio="none">
                <!-- Blue Line Gradient Fill -->
                <defs>
                    <linearGradient id="blueGradient" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#3553A8" stop-opacity="0.3" />
                        <stop offset="100%" stop-color="#3553A8" stop-opacity="0" />
                    </linearGradient>
                    <linearGradient id="tealGradient" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" stop-color="#28C5D4" stop-opacity="0.3" />
                        <stop offset="100%" stop-color="#28C5D4" stop-opacity="0" />
                    </linearGradient>
                </defs>

                <!-- Dark Blue Line & Fill -->
                <path d="M0,300 C150,150 250,150 300,200 C350,250 450,250 500,100 C550,-50 650,200 700,300 L850,300 L1000,50 L1000,300 Z" fill="url(#blueGradient)"/>
                <path d="M0,300 C150,150 250,150 300,200 C350,250 450,250 500,100 C550,-50 650,200 700,300 L850,300 L1000,50" fill="none" stroke="#3553A8" stroke-width="3" stroke-linecap="round"/>

                <!-- Teal Line & Fill -->
                <path d="M0,300 C150,200 250,200 300,250 C350,300 450,300 500,150 C550,50 650,250 700,300 L850,300 L1000,150 L1000,300 Z" fill="url(#tealGradient)"/>
                <path d="M0,300 C150,200 250,200 300,250 C350,300 450,300 500,150 C550,50 650,250 700,300 L850,300 L1000,150" fill="none" stroke="#28C5D4" stroke-width="3" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

</div>
@endsection
