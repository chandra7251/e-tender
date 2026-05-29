@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php
    // Fetch Tender Stats directly in view (tanpa mengubah backend/controller)
    $tenderQuery = \App\Models\Tender::query();
    if (request()->has('tender_id')) {
        $selectedTender = \App\Models\Tender::find(request('tender_id'));
    } else {
        $selectedTender = \App\Models\Tender::whereIn('status', ['open', 'aanwijzing', 'bidding'])->latest()->first() 
                          ?? \App\Models\Tender::latest()->first();
    }

    $tenderStats = [];
    if ($selectedTender) {
        $tenderBids = $selectedTender->bids;
        $tenderStats = [
            'id' => $selectedTender->id,
            'title' => $selectedTender->title,
            'total_bid' => $tenderBids->count(),
            'total_peserta' => $selectedTender->participants()->count(),
            'bid_terendah' => $tenderBids->min('bid_amount'),
            'bid_tertinggi' => $tenderBids->max('bid_amount'),
            'end_date' => $selectedTender->end_date ? $selectedTender->end_date->format('d M Y, H:i:s') : null,
        ];
    }

    // List of active tenders for modal
    $activeTenders = \App\Models\Tender::whereNotIn('status', ['draft'])->latest()->get(['id', 'title']);
@endphp

<div class="space-y-6">

    {{-- Top Cards Grid --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

        {{-- 1. TOTAL VENDOR CARD --}}
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 tracking-wide">TOTAL VENDOR</h3>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#77ACEC] bg-opacity-15 text-[#3553A8]">
                    {{-- Users Icon --}}
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.233.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" />
                    </svg>
                </div>
            </div>
            
            <div class="mb-4">
                @php $vendorTotal = \App\Models\Vendor::count(); @endphp
                <span class="text-5xl font-extrabold text-gray-900">{{ $vendorTotal }}</span>
            </div>

            {{-- Progress Bar --}}
            @php
                $approved = \App\Models\Vendor::where('verification_status', 'approved')->count();
                $pending = \App\Models\Vendor::where('verification_status', 'pending')->count();
                $rejected = \App\Models\Vendor::where('verification_status', 'rejected')->count();

                $pctApp = $vendorTotal > 0 ? ($approved / $vendorTotal) * 100 : 0;
                $pctPen = $vendorTotal > 0 ? ($pending / $vendorTotal) * 100 : 0;
                $pctRej = $vendorTotal > 0 ? ($rejected / $vendorTotal) * 100 : 0;
            @endphp
            <div class="mb-4 flex h-3 w-full overflow-hidden rounded-full bg-gray-100">
                @if($pctRej > 0)<div style="width: {{ $pctRej }}%" class="bg-[#788B9A]"></div>@endif
                @if($pctPen > 0)<div style="width: {{ $pctPen }}%" class="bg-[#F09459]"></div>@endif
                @if($pctApp > 0)<div style="width: {{ $pctApp }}%" class="bg-[#28C5D4]"></div>@endif
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
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#77ACEC] bg-opacity-15 text-[#3553A8]">
                    {{-- Document Icon --}}
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            
            <div class="mb-4">
                @php $tenderTotal = \App\Models\Tender::count(); @endphp
                <span class="text-5xl font-extrabold text-gray-900">{{ $tenderTotal }}</span>
            </div>

            {{-- Progress Bar --}}
            @php
                $tenderOpen = \App\Models\Tender::where('status', 'open')->count();
                $tenderBidding = \App\Models\Tender::where('status', 'bidding')->count();
                $tenderAanwijzing = \App\Models\Tender::where('status', 'aanwijzing')->count();
                $tenderFinished = \App\Models\Tender::where('status', 'finished')->count();
                $tenderClosed = \App\Models\Tender::where('status', 'closed')->count();
                $tenderDraft = \App\Models\Tender::where('status', 'draft')->count();

                $pctOpen = $tenderTotal > 0 ? ($tenderOpen / $tenderTotal) * 100 : 0;
                $pctBidding = $tenderTotal > 0 ? ($tenderBidding / $tenderTotal) * 100 : 0;
                $pctAanwijzing = $tenderTotal > 0 ? ($tenderAanwijzing / $tenderTotal) * 100 : 0;
                $pctFinished = $tenderTotal > 0 ? ($tenderFinished / $tenderTotal) * 100 : 0;
                $pctClosed = $tenderTotal > 0 ? ($tenderClosed / $tenderTotal) * 100 : 0;
                $pctDraft = $tenderTotal > 0 ? ($tenderDraft / $tenderTotal) * 100 : 0;
            @endphp
            <div class="mb-4 flex h-3 w-full overflow-hidden rounded-full bg-gray-100">
                @if($pctOpen > 0)<div style="width: {{ $pctOpen }}%" class="bg-[#2AC6D6]"></div>@endif
                @if($pctBidding > 0)<div style="width: {{ $pctBidding }}%" class="bg-[#FCE300]"></div>@endif
                @if($pctAanwijzing > 0)<div style="width: {{ $pctAanwijzing }}%" class="bg-[#E06BE8]"></div>@endif
                @if($pctFinished > 0)<div style="width: {{ $pctFinished }}%; background-color: #F6F6F6; border: 1px solid rgba(0,0,0,0.30);"></div>@endif
                @if($pctClosed > 0)<div style="width: {{ $pctClosed }}%" class="bg-[#A22020]"></div>@endif
                @if($pctDraft > 0)<div style="width: {{ $pctDraft }}%" class="bg-[#6B7280]"></div>@endif
            </div>

            {{-- Legend --}}
            <table class="w-full text-[10px] font-semibold text-gray-800 border-separate" style="border-spacing: 0 4px;">
                <tr>
                    <td class="flex items-center gap-1.5"><div class="h-2.5 w-2.5 rounded-full bg-[#2AC6D6]"></div> Tender Open</td>
                    <td>: {{ $tenderOpen }}</td>
                    <td class="pl-2 flex items-center gap-1.5"><div class="h-2.5 w-2.5 rounded-full" style="background-color: #F6F6F6; border: 1px solid rgba(0,0,0,0.30);"></div> Tender Finished</td>
                    <td>: {{ $tenderFinished }}</td>
                </tr>
                <tr>
                    <td class="flex items-center gap-1.5"><div class="h-2.5 w-2.5 rounded-full bg-[#FCE300]"></div> Tender Bidding</td>
                    <td>: {{ $tenderBidding }}</td>
                    <td class="pl-2 flex items-center gap-1.5"><div class="h-2.5 w-2.5 rounded-full bg-[#A22020]"></div> Tender closed</td>
                    <td>: {{ $tenderClosed }}</td>
                </tr>
                <tr>
                    <td class="flex items-center gap-1.5"><div class="h-2.5 w-2.5 rounded-full bg-[#E06BE8]"></div> Tender Aanwijzing</td>
                    <td>: {{ $tenderAanwijzing }}</td>
                    <td class="pl-2 flex items-center gap-1.5"><div class="h-2.5 w-2.5 rounded-full bg-[#6B7280]"></div> Tender Draft</td>
                    <td>: {{ $tenderDraft }}</td>
                </tr>
            </table>
        </div>

        {{-- 3. BIDDING & PESERTA CARD --}}
        <div class="flex flex-col rounded-2xl bg-white p-6 shadow-sm border border-gray-100 relative">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-bold text-gray-900 tracking-wide">BIDDING & PESERTA :</h3>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#77ACEC] bg-opacity-15 text-[#3553A8]">
                    {{-- Icon Gavel dari file gambar --}}
                    <img src="{{ asset('images/palu.png') }}" alt="Palu Lelang" class="h-5 w-5 object-contain" onerror="this.style.display='none'">
                </div>
            </div>

            @if(empty($tenderStats))
                <div class="text-center text-gray-500 py-4 text-sm mt-4">
                    Belum ada tender yang berjalan.
                </div>
            @else
                {{-- Judul + Tombol Search --}}
                <div class="flex items-center justify-between gap-4 mb-5">
                    <p class="text-base font-bold text-gray-900 leading-snug flex-1"
                       style="-webkit-line-clamp: 2; -webkit-box-orient: vertical; display: -webkit-box; overflow: hidden;"
                       title="{{ $tenderStats['title'] }}">
                        {{ $tenderStats['title'] }}
                    </p>
                    {{-- Search Button — fixed position, always right --}}
                    <button type="button"
                            onclick="openSearchModal()"
                            title="Cari Tender Lain"
                            class="flex-shrink-0 flex h-9 w-9 items-center justify-center rounded-xl shadow-sm transition-all duration-200"
                            style="background: rgba(119, 172, 236, 0.15); color: #3553A8;"
                            onmouseover="this.style.background='#77ACEC'; this.style.color='white'; this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 12px rgba(119,172,236,0.3)';"
                            onmouseout="this.style.background='rgba(119, 172, 236, 0.15)'; this.style.color='#3553A8'; this.style.transform='scale(1)'; this.style.boxShadow='';">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </button>
                </div>
                
                <div class="flex justify-between mt-2 mb-8 px-4">
                    <div class="text-center w-1/2">
                        <p class="text-4xl font-extrabold text-gray-900">{{ $tenderStats['total_bid'] }}</p>
                        <p class="text-[11px] font-bold text-gray-900 mt-1">Total Bid</p>
                    </div>
                    <div class="text-center w-1/2">
                        <p class="text-4xl font-extrabold text-gray-900">{{ $tenderStats['total_peserta'] }}</p>
                        <p class="text-[11px] font-bold text-gray-900 mt-1">Total Peserta</p>
                    </div>
                </div>

                <div class="mt-auto flex justify-between items-end">
                    <div>
                        <p class="text-[10px] font-bold text-[#3553A8] mb-1">Bid Terendah :</p>
                        <p class="text-sm font-extrabold text-gray-900">
                            {{ $tenderStats['bid_terendah'] ? 'Rp ' . number_format($tenderStats['bid_terendah'], 0, ',', '.') : '-' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-[#3553A8] mb-1">Bid Tertinggi :</p>
                        <p class="text-sm font-extrabold text-gray-900">
                            {{ $tenderStats['bid_tertinggi'] ? 'Rp ' . number_format($tenderStats['bid_tertinggi'], 0, ',', '.') : '-' }}
                        </p>
                    </div>
                </div>
                @if($tenderStats['end_date'])
                <div class="text-right mt-3 text-[10px] font-bold text-[#3553A8]">
                    {{ $tenderStats['end_date'] }}
                </div>
                @endif
            @endif
        </div>

    </div>

    {{-- Chart Card --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">

        @php
            \Carbon\Carbon::setLocale('id');
            $nowJkt   = \Carbon\Carbon::now('Asia/Jakarta');
            $todayStr = $nowJkt->format('Y-m-d');

            // ── Reference point (from URL param) ──────────────────────────
            $selectedWeek = request('week', $nowJkt->format('Y-\WW'));
            $selYear = (int) substr($selectedWeek, 0, 4);
            $selWk   = (int) substr($selectedWeek, 6);
            $selMon  = \Carbon\Carbon::now()->setISODate($selYear, $selWk)->startOfWeek();
            $selSun  = $selMon->copy()->endOfWeek();
            $selWeekNum = min($selMon->weekOfMonth, 4);

            // Month reference
            $mStart      = $selMon->copy()->startOfMonth();
            $mEnd        = $selMon->copy()->endOfMonth();
            $daysInMonth = $mEnd->day;

            // ── Pre-fetch semua tender untuk eksistensi waktu ────────────
            $allTenders = \App\Models\Tender::select('id', 'status', 'start_date', 'created_at')
                ->get()->keyBy('id');

            // ── Pre-fetch semua event status dari tender_histories ────────
            // Dikelompokkan per tender_id, diurutkan berdasarkan waktu kejadian.
            // Gunakan whereNotNull('new_status') untuk menangkap semua event 
            // (termasuk 'winner_selected' yang mungkin tidak ber-action 'status_changed')
            $allHistories = \App\Models\TenderHistory::whereNotNull('new_status')
                ->select('tender_id', 'new_status', 'created_at')
                ->orderBy('tender_id')
                ->orderBy('created_at')
                ->get()
                ->groupBy('tender_id');

            // Helper: hitung jumlah tender per status pada titik waktu $endStr.
            $getCounts = function(string $endStr) use ($allTenders, $allHistories): array {
                $c = ['open'=>0,'bidding'=>0,'aanwijzing'=>0,'finished'=>0,'closed'=>0,'draft'=>0];
                foreach ($allTenders as $tenderId => $tender) {
                    // 1. Kapan tender mulai muncul di grafik?
                    // Draft -> pakai created_at. Lainnya -> pakai start_date (atau fallback ke created_at)
                    $rawDate = $tender->status === 'draft' 
                        ? $tender->created_at 
                        : ($tender->start_date ?: $tender->created_at);
                    
                    $baseDateStr = $rawDate instanceof \Carbon\Carbon
                        ? $rawDate->format('Y-m-d H:i:s')
                        : substr((string)$rawDate, 0, 19);
                    
                    if ($baseDateStr > $endStr) continue; // Tender belum dimulai pada tanggal ini

                    // 2. Cari status historis dari tender_histories
                    $statusAtDate = null;
                    $firstKnownStatus = null;
                    
                    if ($allHistories->has($tenderId)) {
                        $firstKnownStatus = $allHistories[$tenderId]->first()->new_status;
                        foreach ($allHistories[$tenderId] as $h) {
                            $hDate = $h->created_at instanceof \Carbon\Carbon
                                ? $h->created_at->format('Y-m-d H:i:s')
                                : substr((string)$h->created_at, 0, 19);
                            if ($hDate <= $endStr) {
                                $statusAtDate = $h->new_status; // status terakhir sebelum/pada tanggal ini
                            }
                        }
                    }

                    // 3. Fallback: Jika tanggal grafik < histori pertama yang tercatat, 
                    //    gunakan status awal (firstKnownStatus) BUKAN status terkini.
                    if ($statusAtDate === null) {
                        $statusAtDate = $firstKnownStatus ?? $tender->status;
                    }

                    if ($statusAtDate && array_key_exists($statusAtDate, $c)) {
                        $c[$statusAtDate]++;
                    }
                }
                return $c;
            };

            $pt = function(string $endStr, string $label, string $sub, bool $future) use ($getCounts): array {
                $c = $future
                    ? ['open'=>0,'bidding'=>0,'aanwijzing'=>0,'finished'=>0,'closed'=>0,'draft'=>0]
                    : $getCounts($endStr);
                return array_merge(['label'=>$label,'sub'=>$sub], $c);
            };

            // ── 1. 1 Minggu — 7 daily columns ────────────────────────────
            $dWeek = [];
            for ($i = 0; $i < 7; $i++) {
                $d = $selMon->copy()->addDays($i);
                $dWeek[] = $pt(
                    $d->copy()->endOfDay()->format('Y-m-d H:i:s'),
                    $d->isoFormat('dddd'),
                    $d->isoFormat('D MMM YYYY'),
                    $d->format('Y-m-d') > $todayStr
                );
            }

            // ── 2. 1 Bulan — adaptive 6 groups ───────────────────────────
            // Distribute days evenly: first ($days % 6) groups get (floor+1) days
            $dMonth = [];
            $base   = intdiv($daysInMonth, 6);
            $extra  = $daysInMonth % 6;
            $ptr    = 1;
            for ($g = 0; $g < 6; $g++) {
                $size   = $base + ($g < $extra ? 1 : 0);
                $dayEnd = min($ptr + $size - 1, $daysInMonth);
                $gS     = $mStart->copy()->day($ptr);
                $gE     = $mStart->copy()->day($dayEnd)->endOfDay();
                $dMonth[] = $pt(
                    $gE->format('Y-m-d H:i:s'),
                    $ptr.'-'.$dayEnd,
                    $gS->isoFormat('D').'-'.$gE->isoFormat('D MMM'),
                    $gS->format('Y-m-d') > $todayStr
                );
                $ptr = $dayEnd + 1;
            }

            // ── 3. 3 Bulan — 6 × 15-day groups ──────────────────────────
            $dQuarter = [];
            $qBase    = $mStart->copy()->subMonths(2)->startOfMonth();
            for ($i = 0; $i < 6; $i++) {
                $gS = $qBase->copy()->addDays($i * 15);
                $gE = $qBase->copy()->addDays(($i + 1) * 15 - 1)->endOfDay();
                $dQuarter[] = $pt(
                    $gE->format('Y-m-d H:i:s'),
                    $gS->isoFormat('D MMM'),
                    $gS->isoFormat('D MMM').' – '.$gE->isoFormat('D MMM YYYY'),
                    $gS->format('Y-m-d') > $todayStr
                );
            }

            // ── 4. 6 Bulan — 6 monthly groups ────────────────────────────
            $dHalf = [];
            $hBase = $mStart->copy()->subMonths(5)->startOfMonth();
            for ($m = 0; $m < 6; $m++) {
                $gS = $hBase->copy()->addMonths($m)->startOfMonth();
                $gE = $hBase->copy()->addMonths($m)->endOfMonth()->endOfDay();
                $dHalf[] = $pt(
                    $gE->format('Y-m-d H:i:s'),
                    $gS->isoFormat('MMM'),
                    $gS->isoFormat('MMMM YYYY'),
                    $gS->format('Y-m-d') > $todayStr
                );
            }

            // ── 5. 1 Tahun — 12 monthly groups ───────────────────────────
            $dYear = [];
            $yBase = \Carbon\Carbon::create($selMon->year, 1, 1)->startOfMonth();
            for ($m = 0; $m < 12; $m++) {
                $gS = $yBase->copy()->addMonths($m)->startOfMonth();
                $gE = $yBase->copy()->addMonths($m)->endOfMonth()->endOfDay();
                $dYear[] = $pt(
                    $gE->format('Y-m-d H:i:s'),
                    $gS->isoFormat('MMM'),
                    $gS->isoFormat('MMMM YYYY'),
                    $gS->format('Y-m-d') > $todayStr
                );
            }

            // ── Navigation URLs & Labels ──────────────────────────────────
            $chartNav = json_encode([
                'week'     => [
                    'prev'    => route('admin.dashboard', ['week' => $selMon->copy()->subWeek()->format('Y-\WW')]),
                    'next'    => route('admin.dashboard', ['week' => $selMon->copy()->addWeek()->format('Y-\WW')]),
                    'canNext' => $selMon->lt(\Carbon\Carbon::now()->startOfWeek()),
                    'label'   => $selMon->isoFormat('MMMM YYYY'),
                    'sub'     => 'Minggu ke-'.$selWeekNum.', '.$selMon->isoFormat('D MMM').' – '.$selSun->isoFormat('D MMM'),
                ],
                'month'    => [
                    'prev'    => route('admin.dashboard', ['week' => $mStart->copy()->subMonth()->startOfMonth()->format('Y-\WW')]),
                    'next'    => route('admin.dashboard', ['week' => $mStart->copy()->addMonth()->startOfMonth()->format('Y-\WW')]),
                    'canNext' => $mStart->lt(\Carbon\Carbon::now()->startOfMonth()),
                    'label'   => $mStart->isoFormat('MMMM YYYY'),
                    'sub'     => '1 – '.$daysInMonth.' '.$mStart->isoFormat('MMMM'),
                ],
                'quarter'  => [
                    'prev'    => route('admin.dashboard', ['week' => $mStart->copy()->subMonths(3)->startOfMonth()->format('Y-\WW')]),
                    'next'    => route('admin.dashboard', ['week' => $mStart->copy()->addMonths(3)->startOfMonth()->format('Y-\WW')]),
                    'canNext' => $mStart->lt(\Carbon\Carbon::now()->startOfMonth()),
                    'label'   => '3 Bulan',
                    'sub'     => $qBase->isoFormat('MMM YYYY').' – '.$mEnd->isoFormat('MMM YYYY'),
                ],
                'halfYear' => [
                    'prev'    => route('admin.dashboard', ['week' => $mStart->copy()->subMonths(6)->startOfMonth()->format('Y-\WW')]),
                    'next'    => route('admin.dashboard', ['week' => $mStart->copy()->addMonths(6)->startOfMonth()->format('Y-\WW')]),
                    'canNext' => $mStart->lt(\Carbon\Carbon::now()->startOfMonth()),
                    'label'   => '6 Bulan',
                    'sub'     => $hBase->isoFormat('MMM YYYY').' – '.$mEnd->isoFormat('MMM YYYY'),
                ],
                'year'     => [
                    'prev'    => route('admin.dashboard', ['week' => $mStart->copy()->subYear()->startOfMonth()->format('Y-\WW')]),
                    'next'    => route('admin.dashboard', ['week' => $mStart->copy()->addYear()->startOfMonth()->format('Y-\WW')]),
                    'canNext' => ((int)$selMon->year) < ((int)\Carbon\Carbon::now()->year),
                    'label'   => $selMon->year.' — Tahunan',
                    'sub'     => 'Jan – Des '.$selMon->year,
                ],
            ], JSON_HEX_TAG);

            $chartAllData = json_encode([
                'week'     => $dWeek,
                'month'    => $dMonth,
                'quarter'  => $dQuarter,
                'halfYear' => $dHalf,
                'year'     => $dYear,
            ], JSON_HEX_TAG);
        @endphp

        {{-- ── Header: Title + Tabs + Navigator ─────────────────────── --}}
        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
            <h3 class="text-lg font-bold text-gray-800">Recent Tender & Bidding Activity</h3>

            <div class="flex flex-wrap items-center gap-2">
                {{-- Period Tabs --}}
                <div class="flex items-center gap-0.5 bg-gray-100 rounded-xl p-1">
                    @foreach(['week'=>'1 Minggu','month'=>'1 Bulan','quarter'=>'3 Bulan','halfYear'=>'6 Bulan','year'=>'1 Tahun'] as $pKey => $pLbl)
                    <button type="button"
                            id="tab-{{ $pKey }}"
                            onclick="setChartPeriod('{{ $pKey }}')"
                            class="chart-period-tab px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 text-gray-500">
                        {{ $pLbl }}
                    </button>
                    @endforeach
                </div>

                {{-- Context Navigator --}}
                <div class="flex items-center gap-1 rounded-xl border border-gray-200 bg-gray-50 px-2 py-1.5 shadow-sm">
                    <a id="chartNavPrev" href="#"
                       class="flex h-7 w-7 items-center justify-center rounded-lg text-gray-500 hover:bg-[#3553A8] hover:text-white transition-colors duration-150"
                       title="Periode Sebelumnya">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </a>
                    <div class="px-2 text-center select-none" style="min-width: 130px;">
                        <p id="chartNavLabel" class="text-[11px] font-bold text-[#3553A8] leading-tight capitalize"></p>
                        <p id="chartNavSub"   class="text-[9px] text-gray-500 font-medium leading-tight mt-0.5 whitespace-nowrap"></p>
                    </div>
                    <a id="chartNavNext" href="#"
                       class="flex h-7 w-7 items-center justify-center rounded-lg text-gray-500 hover:bg-[#3553A8] hover:text-white transition-colors duration-150"
                       title="Periode Berikutnya">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap items-center justify-center gap-6 mb-12 text-sm font-semibold text-gray-500">
            <div class="flex items-center gap-2"><div class="h-5 w-5 rounded-full bg-[#2AC6D6]"></div><span>Tender Open</span></div>
            <div class="flex items-center gap-2"><div class="h-5 w-5 rounded-full bg-[#FCE300]"></div><span>Tender Bidding</span></div>
            <div class="flex items-center gap-2"><div class="h-5 w-5 rounded-full bg-[#E06BE8]"></div><span>Tender Aanwijzing</span></div>
            <div class="flex items-center gap-2"><div class="h-5 w-5 rounded-full" style="background-color:#F6F6F6;border:1px solid rgba(0,0,0,0.30);"></div><span>Tender Finished</span></div>
            <div class="flex items-center gap-2"><div class="h-5 w-5 rounded-full bg-[#A22020]"></div><span>Tender Closed</span></div>
            <div class="flex items-center gap-2"><div class="h-5 w-5 rounded-full bg-[#6B7280]"></div><span>Tender Draft</span></div>
        </div>

        {{-- Chart Area (JS-rendered) --}}
        <div class="relative w-full h-[320px] mt-4">
            <div id="chartYAxis"    class="absolute top-0 right-0 left-0 bottom-[60px] z-0"></div>
            <div id="chartBarsArea" class="absolute top-0 right-0 left-8 bottom-[60px] flex justify-around items-end z-10 px-2"></div>
            <div id="chartXLabels"  class="absolute bottom-0 right-0 left-8 h-[60px] flex justify-around items-center z-10 text-xs text-gray-500 text-center px-2"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- Chart Script                                                    --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <script>
    (function() {
        // ── Data injected from PHP ──────────────────────────────────────
        const CHART_DATA = {!! $chartAllData !!};
        const CHART_NAV  = {!! $chartNav !!};

        const COLORS = {
            open:       { bg:'#2AC6D6',  border:null },
            bidding:    { bg:'#FCE300',  border:null },
            aanwijzing: { bg:'#E06BE8',  border:null },
            finished:   { bg:'#F6F6F6',  border:'rgba(0,0,0,0.30)' },
            closed:     { bg:'#A22020',  border:null },
            draft:      { bg:'#6B7280',  border:null },
        };
        const STATUS_NAMES = {
            open:'Tender Open', bidding:'Tender Bidding',
            aanwijzing:'Tender Aanwijzing', finished:'Tender Finished',
            closed:'Tender Closed', draft:'Tender Draft',
        };
        const STATUSES = ['open','bidding','aanwijzing','finished','closed','draft'];

        // ── State ───────────────────────────────────────────────────────
        let _period = localStorage.getItem('chartPeriod') || 'week';

        // ── Set Period ──────────────────────────────────────────────────
        window.setChartPeriod = function(period) {
            _period = period;
            localStorage.setItem('chartPeriod', period);

            // Update tabs
            document.querySelectorAll('.chart-period-tab').forEach(function(btn) {
                btn.style.background = '';
                btn.style.color      = '#6B7280';
                btn.style.boxShadow  = '';
            });
            var activeTab = document.getElementById('tab-' + period);
            if (activeTab) {
                activeTab.style.background = 'white';
                activeTab.style.color      = '#3553A8';
                activeTab.style.boxShadow  = '0 1px 3px rgba(0,0,0,0.12)';
            }

            // Update navigator label + links
            var nav = CHART_NAV[period];
            document.getElementById('chartNavLabel').textContent = nav.label;
            document.getElementById('chartNavSub').textContent   = nav.sub;
            document.getElementById('chartNavPrev').href         = nav.prev;
            document.getElementById('chartNavNext').href         = nav.next;
            var nextEl = document.getElementById('chartNavNext');
            if (!nav.canNext) {
                nextEl.style.opacity       = '0.3';
                nextEl.style.pointerEvents = 'none';
            } else {
                nextEl.style.opacity       = '';
                nextEl.style.pointerEvents = '';
            }

            renderChart(CHART_DATA[period]);
        };

        // ── Render Chart ────────────────────────────────────────────────
        function renderChart(data) {
            var yAxisEl  = document.getElementById('chartYAxis');
            var barsEl   = document.getElementById('chartBarsArea');
            var xlabEl   = document.getElementById('chartXLabels');

            // Max value
            var maxVal = 6;
            data.forEach(function(pt) {
                STATUSES.forEach(function(s) { if (pt[s] > maxVal) maxVal = pt[s]; });
            });

            // Step
            var step = 1;
            if      (maxVal > 1000) step = 200;
            else if (maxVal > 500)  step = 100;
            else if (maxVal > 100)  step = 50;
            else if (maxVal > 50)   step = 10;
            else if (maxVal > 20)   step = 5;
            else if (maxVal > 10)   step = 2;
            var yMax = Math.ceil(maxVal / step) * step;

            // Y-Axis
            var yHtml = '';
            for (var v = yMax; v >= 0; v -= step) {
                var pct = (v / yMax) * 100;
                yHtml += '<div class="absolute w-full flex items-center" style="bottom:' + pct + '%;transform:translateY(50%);">' +
                    '<span class="text-xs text-gray-800 font-medium w-8 text-left leading-none">' + v + '</span>' +
                    '<div class="flex-1 border-b border-gray-400"></div></div>';
            }
            yAxisEl.innerHTML = yHtml;

            // Bars + X-Labels
            var barsHtml = '', xlHtml = '';
            data.forEach(function(pt, idx) {
                // Tooltip rows
                var rows = '';
                STATUSES.forEach(function(s) {
                    var c = COLORS[s];
                    var dotStyle = c.border
                        ? 'background-color:' + c.bg + ';border:1px solid ' + c.border + ';'
                        : 'background-color:' + c.bg + ';';
                    rows += '<tr><td class="w-4 py-0.5"><div class="h-2.5 w-2.5 rounded-full" style="' + dotStyle + '"></div></td>' +
                            '<td>' + STATUS_NAMES[s] + ': ' + pt[s] + '</td></tr>';
                });

                // Bar elements
                var barEls = '';
                STATUSES.forEach(function(s) {
                    if (pt[s] > 0) {
                        var h = (pt[s] / yMax) * 100;
                        var c = COLORS[s];
                        var bStyle = c.border
                            ? 'height:' + h + '%;background-color:' + c.bg + ';border:1px solid ' + c.border + ';border-bottom:none;'
                            : 'height:' + h + '%;background-color:' + c.bg + ';';
                        barEls += '<div class="w-3.5 rounded-t-[3px] shrink-0" style="' + bStyle + '"></div>';
                    }
                });

                barsHtml += '<div class="flex gap-1 h-full items-end group justify-center relative hover:z-20 cursor-pointer" ' +
                    'style="flex:1;max-width:80px;animation:barFadeIn 0.2s ease both;animation-delay:' + (idx * 0.04) + 's;">' +
                    '<div class="absolute top-4 left-1/2 -translate-x-1/2 w-44 bg-white border border-gray-200 shadow-lg rounded p-2 z-50 hidden group-hover:block pointer-events-none">' +
                    '<div class="font-bold text-gray-800 text-sm mb-0.5">' + pt.label + '</div>' +
                    '<div class="text-[10px] text-gray-400 mb-1.5">' + pt.sub + '</div>' +
                    '<table class="w-full text-[11px] text-gray-600 font-semibold font-sans">' + rows + '</table></div>' +
                    barEls + '</div>';

                xlHtml += '<div style="flex:1;max-width:80px;" class="text-center">' +
                    '<div class="font-semibold text-gray-700 text-xs">' + pt.label + '</div>' +
                    '<div class="mt-0.5 text-[10px] text-gray-500">' + pt.sub + '</div></div>';
            });

            barsEl.innerHTML = barsHtml;
            xlabEl.innerHTML = xlHtml;
        }

        // ── Boot ────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            setChartPeriod(_period);
        });
    })();
    </script>

    <style>
        @keyframes barFadeIn {
            from { opacity:0; transform:scaleY(0.2); transform-origin:bottom; }
            to   { opacity:1; transform:scaleY(1);   transform-origin:bottom; }
        }
    </style>



</div>

{{-- ============================================================ --}}
{{-- Search Tender Modal (Redesigned) --}}
{{-- ============================================================ --}}

{{-- Data tender diambil ulang di View (tanpa menyentuh Controller) --}}
@php
    $modalTenders = \App\Models\Tender::whereNotIn('status', ['draft'])
                        ->latest()
                        ->get(['id', 'title', 'status']);

    $statusConfig = [
        'open'       => ['label' => 'Open',       'bg' => '#E0FAF9', 'text' => '#0D8E87', 'dot' => '#2AC6D6'],
        'bidding'    => ['label' => 'Bidding',     'bg' => '#FEFCE0', 'text' => '#8A7600', 'dot' => '#FCE300'],
        'aanwijzing' => ['label' => 'Aanwijzing',  'bg' => '#FAE8FF', 'text' => '#9B2FAD', 'dot' => '#E06BE8'],
        'finished'   => ['label' => 'Finished',    'bg' => '#F6F6F6', 'text' => '#6B7280', 'dot' => '#D1D5DB'],
        'closed'     => ['label' => 'Closed',      'bg' => '#FEE2E2', 'text' => '#991B1B', 'dot' => '#A22020'],
    ];
@endphp

<div id="searchTenderModal"
     class="fixed inset-0 z-50 flex items-center justify-center hidden"
     style="background: rgba(15,23,42,0.55); backdrop-filter: blur(4px);">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 flex flex-col overflow-hidden"
         style="max-height: 82vh;">

        {{-- Header --}}
        <div class="px-6 pt-5 pb-4 flex items-center justify-between flex-shrink-0"
             style="background: linear-gradient(135deg, #3553A8 0%, #2AC6D6 100%);">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl"
                     style="background: rgba(255,255,255,0.2);">
                    <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white leading-tight">Pilih Tender</h3>
                    <p class="text-xs text-white leading-tight mt-0.5" style="opacity:0.75;"
                       id="modalSubtitle">Memuat data...</p>
                </div>
            </div>
            <button type="button" onclick="closeSearchModal()"
                    class="flex h-8 w-8 items-center justify-center rounded-full text-white transition-all"
                    style="background: rgba(255,255,255,0.2);"
                    onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Search Bar --}}
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50 flex-shrink-0">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text" id="tenderSearchInput" oninput="onTenderSearch()"
                       placeholder="Cari judul tender..."
                       class="w-full pl-10 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 shadow-sm transition-all"
                       style="outline:none;"
                       onfocus="this.style.boxShadow='0 0 0 3px rgba(53,83,168,0.15)'; this.style.borderColor='#3553A8';"
                       onblur="this.style.boxShadow=''; this.style.borderColor='#e5e7eb';">
                <button type="button" id="clearSearchBtn" onclick="clearTenderSearch()"
                        class="absolute inset-y-0 right-0 pr-3.5 items-center text-gray-400 hover:text-gray-600 hidden">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- List Container --}}
        <div class="flex-1 overflow-y-auto bg-white" id="tenderListContainer">

            {{-- Hidden data source: dibaca oleh JS --}}
            <div id="tenderDataSource" class="hidden">
                @foreach($modalTenders as $tender)
                    @php
                        $cfg = $statusConfig[$tender->status]
                            ?? ['label' => ucfirst($tender->status), 'bg' => '#F3F4F6', 'text' => '#6B7280', 'dot' => '#9CA3AF'];
                    @endphp
                    <span
                        data-id="{{ $tender->id }}"
                        data-title="{{ addslashes($tender->title) }}"
                        data-status-label="{{ $cfg['label'] }}"
                        data-status-bg="{{ $cfg['bg'] }}"
                        data-status-text="{{ $cfg['text'] }}"
                        data-status-dot="{{ $cfg['dot'] }}"
                        data-href="{{ route('admin.dashboard', array_merge(request()->query(), ['tender_id' => $tender->id])) }}"
                    ></span>
                @endforeach
            </div>

            {{-- Rendered list (diisi JS) --}}
            <div id="tenderRenderedList" class="p-3 space-y-1"></div>

            {{-- Empty state --}}
            <div id="tenderEmptyState" class="hidden py-14 text-center px-6">
                <div class="flex justify-center mb-3">
                    <svg class="w-14 h-14 text-gray-200" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <p class="text-sm font-bold text-gray-500">Tender tidak ditemukan</p>
                <p class="text-xs text-gray-400 mt-1">Coba kata kunci yang lain</p>
            </div>
        </div>

        {{-- Footer Pagination --}}
        <div id="tenderPaginationBar"
             class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between flex-shrink-0">
            <span class="text-xs text-gray-500 font-medium" id="paginationInfo"></span>
            <div class="flex items-center gap-1.5">
                <button type="button" id="btnPrevPage" onclick="changeTenderPage(-1)"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-all"
                        onmouseover="if(!this.disabled){this.style.background='#3553A8';this.style.color='white';this.style.borderColor='#3553A8';}"
                        onmouseout="if(!this.disabled){this.style.background='white';this.style.color='';this.style.borderColor='#e5e7eb';}">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <div class="flex items-center gap-1" id="pageDotsContainer"></div>
                <button type="button" id="btnNextPage" onclick="changeTenderPage(1)"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-all"
                        onmouseover="if(!this.disabled){this.style.background='#3553A8';this.style.color='white';this.style.borderColor='#3553A8';}"
                        onmouseout="if(!this.disabled){this.style.background='white';this.style.color='';this.style.borderColor='#e5e7eb';}">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    // ─── State ─────────────────────────────────────────────────────
    const TENDER_PAGE_SIZE = 10;
    let _tAll  = [];   // master list
    let _tFilt = [];   // setelah filter
    let _tPage = 1;

    // ─── Boot: baca data dari DOM ───────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const nodes = document.querySelectorAll('#tenderDataSource span[data-id]');
        _tAll = Array.from(nodes).map(el => ({
            id:          el.dataset.id,
            title:       el.dataset.title,
            statusLabel: el.dataset.statusLabel,
            statusBg:    el.dataset.statusBg,
            statusText:  el.dataset.statusText,
            statusDot:   el.dataset.statusDot,
            href:        el.dataset.href,
        }));
        _tFilt = [..._tAll];
        renderTenderList();
    });

    // ─── Render ────────────────────────────────────────────────────
    function renderTenderList() {
        const listEl   = document.getElementById('tenderRenderedList');
        const emptyEl  = document.getElementById('tenderEmptyState');
        const subtitle = document.getElementById('modalSubtitle');

        if (_tFilt.length === 0) {
            listEl.innerHTML = '';
            emptyEl.classList.remove('hidden');
            subtitle.textContent = 'Tidak ada hasil ditemukan';
            updateTenderPagination(0, 0, 0);
            return;
        }

        emptyEl.classList.add('hidden');

        const totalPages = Math.ceil(_tFilt.length / TENDER_PAGE_SIZE);
        if (_tPage > totalPages) _tPage = totalPages;
        if (_tPage < 1)          _tPage = 1;

        const start     = (_tPage - 1) * TENDER_PAGE_SIZE;
        const end       = Math.min(start + TENDER_PAGE_SIZE, _tFilt.length);
        const pageItems = _tFilt.slice(start, end);

        subtitle.textContent = _tFilt.length + ' tender tersedia';

        const currentUrl = window.location.href;

        let html = '';
        pageItems.forEach(function(t, idx) {
            const isActive = currentUrl.includes('tender_id=' + t.id);
            const num      = start + idx + 1;

            const activeBg     = isActive ? '#EEF1FF' : '';
            const activeBorder = isActive ? '1px solid #C7D0F5' : '1px solid transparent';
            const numBg        = isActive ? '#3553A8' : '#F3F4F6';
            const numColor     = isActive ? 'white'   : '#6B7280';
            const titleColor   = isActive ? '#3553A8' : '#1F2937';

            html += `<a href="${t.href}"
                class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all cursor-pointer tender-list-item"
                style="background:${activeBg}; border:${activeBorder}; animation: tiFadeIn 0.12s ease both; animation-delay:${idx * 0.025}s;"
                onmouseover="if(!${isActive}){this.style.background='#F8FAFF';this.style.borderColor='#E5E9FF';}"
                onmouseout="if(!${isActive}){this.style.background='';this.style.borderColor='transparent';}">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex-shrink-0 flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold transition-all"
                         style="background:${numBg}; color:${numColor};">
                        ${num}
                    </div>
                    <span class="text-sm font-semibold truncate" style="color:${titleColor};"
                          title="${t.title}">${t.title}</span>
                </div>
                <span class="flex-shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold whitespace-nowrap"
                      style="background-color:${t.statusBg}; color:${t.statusText};">
                    <span class="inline-block w-1.5 h-1.5 rounded-full flex-shrink-0"
                          style="background-color:${t.statusDot};"></span>
                    ${t.statusLabel}
                </span>
            </a>`;
        });

        listEl.innerHTML = html;
        updateTenderPagination(start + 1, end, _tFilt.length);
    }

    function updateTenderPagination(from, to, total) {
        const infoEl     = document.getElementById('paginationInfo');
        const btnPrev    = document.getElementById('btnPrevPage');
        const btnNext    = document.getElementById('btnNextPage');
        const dotsEl     = document.getElementById('pageDotsContainer');
        const barEl      = document.getElementById('tenderPaginationBar');
        const totalPages = Math.ceil(total / TENDER_PAGE_SIZE);

        infoEl.textContent = total > 0 ? 'Menampilkan ' + from + '–' + to + ' dari ' + total : '';

        // Show / hide footer
        barEl.style.display = (totalPages <= 1 && total > 0) ? 'none' : '';

        // Arrows
        btnPrev.disabled = _tPage <= 1;
        btnNext.disabled = _tPage >= totalPages;
        btnPrev.style.opacity = btnPrev.disabled ? '0.3' : '1';
        btnNext.style.opacity = btnNext.disabled ? '0.3' : '1';
        btnPrev.style.cursor  = btnPrev.disabled ? 'not-allowed' : 'pointer';
        btnNext.style.cursor  = btnNext.disabled ? 'not-allowed' : 'pointer';

        // Page dots (max 5)
        if (totalPages <= 1) { dotsEl.innerHTML = ''; return; }
        const maxD  = Math.min(totalPages, 5);
        let startD  = Math.max(1, _tPage - 2);
        let endD    = Math.min(totalPages, startD + maxD - 1);
        startD      = Math.max(1, endD - maxD + 1);

        let dotsHtml = '';
        for (let p = startD; p <= endD; p++) {
            const a = (p === _tPage);
            dotsHtml += `<button type="button" onclick="goTenderPage(${p})"
                style="height:28px; width:28px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; border:none; outline:none; transition:all .15s;
                       background:${a ? '#3553A8' : 'transparent'}; color:${a ? 'white' : '#6B7280'};"
                onmouseover="if(${!a}){this.style.background='#E5E9FF';}"
                onmouseout="if(${!a}){this.style.background='transparent';}"
                >${p}</button>`;
        }
        dotsEl.innerHTML = dotsHtml;
    }

    // ─── Actions ───────────────────────────────────────────────────
    function onTenderSearch() {
        const q = document.getElementById('tenderSearchInput').value.toLowerCase().trim();
        const clearBtn = document.getElementById('clearSearchBtn');
        if (q) { clearBtn.classList.remove('hidden'); clearBtn.classList.add('flex'); }
        else   { clearBtn.classList.add('hidden');    clearBtn.classList.remove('flex'); }

        _tFilt = q ? _tAll.filter(t => t.title.toLowerCase().includes(q)) : [..._tAll];
        _tPage = 1;
        renderTenderList();
    }

    function clearTenderSearch() {
        document.getElementById('tenderSearchInput').value = '';
        const clearBtn = document.getElementById('clearSearchBtn');
        clearBtn.classList.add('hidden');
        clearBtn.classList.remove('flex');
        _tFilt = [..._tAll];
        _tPage = 1;
        renderTenderList();
    }

    function changeTenderPage(dir) {
        const totalPages = Math.ceil(_tFilt.length / TENDER_PAGE_SIZE);
        _tPage = Math.max(1, Math.min(totalPages, _tPage + dir));
        renderTenderList();
        document.getElementById('tenderListContainer').scrollTop = 0;
    }

    function goTenderPage(p) {
        _tPage = p;
        renderTenderList();
        document.getElementById('tenderListContainer').scrollTop = 0;
    }

    // ─── Open / Close ──────────────────────────────────────────────
    function openSearchModal() {
        const modal = document.getElementById('searchTenderModal');
        modal.classList.remove('hidden');
        modal.style.opacity = '0';
        requestAnimationFrame(function() {
            modal.style.transition = 'opacity .2s ease';
            modal.style.opacity    = '1';
        });
        _tPage = 1;
        clearTenderSearch();
        setTimeout(function() { document.getElementById('tenderSearchInput').focus(); }, 200);
    }

    function closeSearchModal() {
        const modal = document.getElementById('searchTenderModal');
        modal.style.opacity = '0';
        setTimeout(function() {
            modal.classList.add('hidden');
            modal.style.transition = '';
        }, 200);
    }

    document.getElementById('searchTenderModal').addEventListener('click', function(e) {
        if (e.target === this) closeSearchModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSearchModal();
    });
</script>

<style>
    @keyframes tiFadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

@endsection
