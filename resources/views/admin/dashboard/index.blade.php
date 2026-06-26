@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="w-full space-y-6">

    {{-- Stats Cards Row 1: Key Metrics --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Tender</p>
            <p class="mt-2 text-2xl font-bold text-gray-800">{{ $stats['tender_total'] }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $stats['tender_active'] }} aktif · {{ $stats['tender_finished'] }} selesai</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Vendor</p>
            <p class="mt-2 text-2xl font-bold text-gray-800">{{ $stats['vendor_total'] }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $stats['vendor_approved'] }} aktif · {{ $stats['vendor_pending'] }} pending</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Bid</p>
            <p class="mt-2 text-2xl font-bold text-gray-800">{{ $stats['bid_total'] }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $stats['participant_total'] }} peserta total</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Bidding Aktif</p>
            <p class="mt-2 text-2xl font-bold text-[#28C5D4]">{{ $stats['active_bidding_tenders'] }}</p>
            <p class="mt-1 text-xs text-gray-400">
                @if ($stats['lowest_bid'])
                    Bid terendah: Rp {{ number_format($stats['lowest_bid'], 0, ',', '.') }}
                @else
                    Belum ada bid
                @endif
            </p>
        </div>
    </div>

    {{-- Stats Cards Row 2: Financial --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-xl bg-[#3553A8] shadow-sm p-5">
            <p class="text-xs font-semibold text-indigo-200 uppercase tracking-wider">Total Nilai Kontrak</p>
            <p class="mt-2 text-2xl font-bold text-white">
                Rp {{ number_format($stats['total_contract_value'] ?? 0, 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-indigo-300">Dari semua PO yang terbit</p>
        </div>
        <div class="rounded-xl bg-emerald-600 shadow-sm p-5">
            <p class="text-xs font-semibold text-emerald-200 uppercase tracking-wider">Penghematan</p>
            <p class="mt-2 text-2xl font-bold text-white">
                Rp {{ number_format($stats['total_savings'] ?? 0, 0, ',', '.') }}
            </p>
            <p class="mt-1 text-xs text-emerald-200">
                {{ $stats['savings_percentage'] }}% dari HPS
                (Rp {{ number_format($stats['total_hps'] ?? 0, 0, ',', '.') }})
            </p>
        </div>
        <div class="rounded-xl bg-[#28C5D4] shadow-sm p-5">
            <p class="text-xs font-semibold text-teal-100 uppercase tracking-wider">Rata-rata per Tender</p>
            <p class="mt-2 text-2xl font-bold text-white">
                {{ number_format($stats['avg_bids_per_tender'] ?? 0, 1) }} bid
            </p>
            <p class="mt-1 text-xs text-teal-100">
                {{ number_format($stats['avg_participants_per_tender'] ?? 0, 1) }} peserta rata-rata
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Tender Status Distribution --}}
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 px-5 py-4 bg-gray-50">
                <h2 class="text-sm font-bold text-gray-800">Distribusi Status Tender</h2>
            </div>
            <div class="p-5">
                @php
                    $statusColors = [
                        'draft'     => ['bg' => 'bg-gray-400',    'label' => 'Draft'],
                        'open'      => ['bg' => 'bg-blue-500',    'label' => 'Open'],
                        'aanwijzing'=> ['bg' => 'bg-yellow-500',  'label' => 'Aanwijzing'],
                        'bidding'   => ['bg' => 'bg-[#28C5D4]',  'label' => 'Bidding'],
                        'closed'    => ['bg' => 'bg-red-500',     'label' => 'Closed'],
                        'finished'  => ['bg' => 'bg-emerald-500', 'label' => 'Finished'],
                    ];
                    $totalTenders = array_sum($tenderStatusDistribution);
                @endphp

                @if ($totalTenders > 0)
                    {{-- Bar chart --}}
                    <div class="flex rounded-full h-6 overflow-hidden mb-4">
                        @foreach ($statusColors as $status => $color)
                            @if (($tenderStatusDistribution[$status] ?? 0) > 0)
                                @php $pct = round(($tenderStatusDistribution[$status] / $totalTenders) * 100, 1); @endphp
                                <div class="{{ $color['bg'] }} transition-all duration-300" style="width: {{ $pct }}%"
                                     title="{{ $color['label'] }}: {{ $tenderStatusDistribution[$status] }} ({{ $pct }}%)">
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        @foreach ($statusColors as $status => $color)
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full {{ $color['bg'] }} shrink-0"></div>
                                <span class="text-xs text-gray-600">{{ $color['label'] }}: <strong>{{ $tenderStatusDistribution[$status] ?? 0 }}</strong></span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data tender.</p>
                @endif
            </div>
        </div>

        {{-- Monthly Trend --}}
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 px-5 py-4 bg-gray-50">
                <h2 class="text-sm font-bold text-gray-800">Tren Tender (6 Bulan Terakhir)</h2>
            </div>
            <div class="p-5">
                @if (count($monthlyTrend) > 0)
                    @php $maxMonthly = max($monthlyTrend) ?: 1; @endphp
                    <div class="flex items-end gap-2 h-40">
                        @foreach ($monthlyTrend as $month => $count)
                            @php $heightPct = round(($count / $maxMonthly) * 100); @endphp
                            <div class="flex-1 flex flex-col items-center justify-end gap-1">
                                <span class="text-xs font-bold text-gray-700">{{ $count }}</span>
                                <div class="w-full bg-[#3553A8] rounded-t transition-all duration-500"
                                     style="height: {{ max($heightPct, 5) }}%"></div>
                                <span class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($month . '-01')->format('M') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data tren.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Top Vendors --}}
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 px-5 py-4 bg-gray-50">
                <h2 class="text-sm font-bold text-gray-800">🏆 Top Vendor (Terbanyak Menang)</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($topVendors as $i => $vendor)
                    <div class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition-colors">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold
                            {{ $i === 0 ? 'bg-[#28C5D4] text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $vendor->company_name }}</p>
                        </div>
                        <span class="text-sm font-bold text-[#3553A8]">{{ $vendor->won_results_count }}×</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada data pemenang.</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 px-5 py-4 bg-gray-50">
                <h2 class="text-sm font-bold text-gray-800">📋 Aktivitas Terbaru</h2>
            </div>
            <div class="divide-y divide-gray-100 max-h-[320px] overflow-y-auto">
                @forelse ($recentActivities as $activity)
                    <div class="px-5 py-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-700 truncate flex-1">
                                <span class="font-semibold">{{ $activity->action }}</span>
                                —
                                {{ Str::limit($activity->description, 60) }}
                            </p>
                            <span class="text-[10px] text-gray-400 whitespace-nowrap ml-3">
                                {{ $activity->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $activity->tender?->title ?? '' }}
                            @if ($activity->actor) · {{ $activity->actor->name }} @endif
                        </p>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400">Belum ada aktivitas.</div>
                @endforelse
            </div>
        </div>
    </div>



    {{-- ===== TIMELINE: Tender Mendatang (14 Hari ke Depan) ===== --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700">📅 Timeline Tender Mendatang <span class="text-xs text-gray-400 font-normal">(14 hari ke depan)</span></h3>
            <a href="{{ route('admin.tenders.index') }}" class="text-xs text-[#3553A8] hover:underline">Lihat Semua &rarr;</a>
        </div>

        @if($upcomingTenders->isEmpty())
            <div class="py-8 text-center text-sm text-gray-400">Tidak ada tender aktif dalam 14 hari ke depan.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Tender</th>
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Status</th>
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Mulai Bidding</th>
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Tutup Bidding</th>
                        <th class="text-left py-2 px-2 text-gray-500 font-medium">Sisa Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($upcomingTenders as $ut)
                    @php
                        $statusColor = match($ut->status) {
                            'bidding'    => 'bg-blue-100 text-blue-700',
                            'open'       => 'bg-emerald-100 text-emerald-700',
                            'aanwijzing' => 'bg-yellow-100 text-yellow-700',
                            'draft'      => 'bg-gray-100 text-gray-500',
                            default        => 'bg-gray-100 text-gray-500',
                        };
                        $sisaWaktu = '-';
                        $sisaColor = 'text-gray-400';
                        if ($ut->bidding_end) {
                            $diff = now()->diffInHours($ut->bidding_end, false);
                            if ($diff > 0) {
                                if ($diff <= 1) {
                                    $sisaWaktu = '< 1 jam';
                                    $sisaColor = 'text-red-600 font-bold';
                                } elseif ($diff <= 24) {
                                    $sisaWaktu = $diff . ' jam';
                                    $sisaColor = 'text-orange-600 font-semibold';
                                } else {
                                    $sisaWaktu = ceil($diff / 24) . ' hari';
                                    $sisaColor = 'text-gray-600';
                                }
                            } else {
                                $sisaWaktu = 'Sudah lewat';
                                $sisaColor = 'text-red-400';
                            }
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-2 px-2">
                            <a href="{{ route('admin.tenders.show', $ut) }}" class="font-medium text-gray-800 hover:text-[#3553A8] truncate block max-w-[200px]">
                                {{ $ut->title }}
                            </a>
                        </td>
                        <td class="py-2 px-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $statusColor }}">
                                {{ ucfirst($ut->status) }}
                            </span>
                        </td>
                        <td class="py-2 px-2 text-gray-500">
                            {{ $ut->bidding_start ? $ut->bidding_start->format('d M H:i') : '-' }}
                        </td>
                        <td class="py-2 px-2 text-gray-500">
                            {{ $ut->bidding_end ? $ut->bidding_end->format('d M H:i') : '-' }}
                        </td>
                        <td class="py-2 px-2 {{ $sisaColor }}">
                            {{ $sisaWaktu }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @if ($stats['pending_submissions'] > 0)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/>
            </svg>
            <strong>{{ $stats['pending_submissions'] }}</strong> pengajuan vendor menunggu review.
            <a href="{{ route('admin.submissions.index') }}" class="ml-auto text-xs font-bold underline hover:no-underline">Review →</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Pie Chart: Distribusi Status Tender ──────────────────────────────
    const statusCtx = document.getElementById('tenderStatusChart');
    if (statusCtx) {
        const statusData = @json($tenderStatusDistribution);
        const statusLabels = { draft:'Draft', open:'Open', aanwijzing:'Aanwijzing', bidding:'Bidding', closed:'Closed', finished:'Finished' };
        const statusColors = { draft:'#9CA3AF', open:'#3B82F6', aanwijzing:'#F59E0B', bidding:'#28C5D4', closed:'#EF4444', finished:'#10B981' };
        const labels = Object.keys(statusData).map(k => statusLabels[k] || k);
        const values = Object.values(statusData);
        const colors = Object.keys(statusData).map(k => statusColors[k] || '#9CA3AF');

        new Chart(statusCtx, {
            type: 'doughnut',
            data: { labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { font: { size: 11 }, padding: 12 } },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} tender` } }
                }
            }
        });
    }

    // ── 2. Line Chart: Tren Tender per Bulan (6 bulan terakhir) ────────────
    const trendCtx = document.getElementById('monthlyTrendChart');
    if (trendCtx) {
        const trendData = @json($monthlyTrend);
        const labels    = Object.keys(trendData);
        const values    = Object.values(trendData);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Jumlah Tender',
                    data: values,
                    borderColor: '#3553A8',
                    backgroundColor: 'rgba(53,83,168,0.1)',
                    borderWidth: 2,
                    pointBackgroundColor: '#3553A8',
                    pointRadius: 5,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#F3F4F6' } },
                    x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                }
            }
        });
    }

    // ── 3. Bar Chart: Top 5 Vendor Pemenang ─────────────────────────────────
    const topVendorCtx = document.getElementById('topVendorChart');
    if (topVendorCtx) {
        const vendors = @json($topVendors);
        new Chart(topVendorCtx, {
            type: 'bar',
            data: {
                labels: vendors.map(v => v.company_name.length > 15 ? v.company_name.substring(0,15)+'...' : v.company_name),
                datasets: [{
                    label: 'Tender Dimenangkan',
                    data: vendors.map(v => v.won_results_count),
                    backgroundColor: ['#3553A8','#28C5D4','#10B981','#F59E0B','#EF4444'],
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#F3F4F6' } },
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                }
            }
        });
    }

});
</script>
@endpush
