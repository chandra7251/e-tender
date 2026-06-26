@extends('layouts.admin')

@section('title', 'Hasil Tender')
@section('page-title', 'Hasil Tender')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.tenders.show', $tender) }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#3553A8] hover:text-[#2B438A] transition-colors">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Kembali ke Detail Tender
        </a>

        <div class="flex gap-3">
            @if ($tender->status !== 'finished')
                <form method="POST" action="{{ route('admin.tenders.finish', $tender) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors flex items-center gap-2"
                            onclick="return confirm('Tandai tender sebagai Finished?')">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Mark as Finished
                    </button>
                </form>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-800 border border-emerald-200">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                    Tender Finished
                </span>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden relative">

        <div class="absolute top-0 left-0 right-0 h-2 bg-emerald-500"></div>

        <div class="p-6 sm:p-10">

            <div class="flex flex-col md:flex-row items-center md:items-start gap-6 border-b border-gray-200 pb-8 mb-8">
                <div class="flex-shrink-0">
                    <div class="h-20 w-20 rounded-full bg-emerald-100 border-4 border-emerald-50 flex items-center justify-center shadow-sm">
                        <svg class="h-10 w-10 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                    </div>
                </div>
                <div class="text-center md:text-left flex-1">
                    <p class="text-sm font-bold text-emerald-600 uppercase tracking-widest mb-1">Pemenang Tender</p>
                    <h2 class="text-3xl font-black text-gray-900">{{ $result->winner->company_name ?? '-' }}</h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-center justify-center md:justify-start gap-1.5">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        {{ $result->winner->user->email ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 mb-10">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tender</p>
                    <p class="text-base font-bold text-gray-900">{{ $tender->title }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nilai Penawaran (Winning Bid)</p>
                    <p class="text-2xl font-black text-[#3553A8]">Rp {{ number_format($result->winning_bid_amount, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Metode Seleksi</p>
                    <p class="text-sm font-medium text-gray-800">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-gray-100 px-2.5 py-1.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-200">
                            {{ $result->selection_method === 'lowest_price' ? 'Harga Terendah' : 'Pertimbangan Admin' }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tanggal Penetapan</p>
                    <p class="text-sm font-medium text-gray-800 flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        {{ $result->decided_at?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </div>
            </div>

            @if ($result->notes)
            <div class="mb-8 rounded-lg bg-gray-50 border border-gray-200 p-5 shadow-sm">
                <p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Catatan Penunjukan</p>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $result->notes }}</p>
            </div>
            @endif

            <div class="flex flex-col sm:flex-row justify-between items-center border-t border-gray-100 pt-6">
                <div class="text-sm text-gray-500 mb-4 sm:mb-0">
                    Diputuskan oleh: <span class="font-bold text-gray-800">{{ $result->decider->name ?? '-' }}</span>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.tenders.histories.index', $tender) }}"
                       class="rounded-lg bg-white border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition-colors flex items-center gap-2">
                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Lihat History
                    </a>
                </div>
            </div>

        </div>
    </div>

    <div class="bg-gradient-to-r from-[#3553A8] to-[#2980b9] rounded-xl shadow-md border border-[#2B438A] p-6 text-white flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">

        <div class="absolute right-0 top-0 bottom-0 w-64 opacity-10 pointer-events-none">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
              <path fill="#ffffff" d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,81.4,-46.4C91.1,-33.7,97.5,-18.4,95.5,-3.5C93.4,11.5,82.9,26.1,72.6,39.3C62.4,52.4,52.4,64,39.8,71.7C27.2,79.4,12.1,83.1,-3.5,89C-19.1,94.9,-35.1,102.9,-46.9,96.6C-58.7,90.2,-66.2,69.5,-73.2,50.7C-80.2,31.8,-86.6,14.8,-86.8,-2.3C-87,-19.4,-80.9,-36.5,-70.7,-50.2C-60.5,-63.9,-46.2,-74.1,-31.6,-80.7C-17,-87.3,-2.1,-90.3,12.7,-88.4C27.5,-86.4,42.2,-79.6,44.7,-76.4Z" transform="translate(100 100) scale(1.1)" />
            </svg>
        </div>

        <div class="relative z-10">
            <h3 class="text-xl font-bold mb-1">Dokumen Purchase Order (PO)</h3>
            <p class="text-blue-100 text-sm max-w-lg">Purchase Order adalah dokumen administratif tahap akhir untuk meresmikan pengadaan pemenang tender dan nilai *Winning Bid*.</p>
        </div>
        <div class="relative z-10 flex-shrink-0">
            @if ($result->purchaseOrder)
                <a href="{{ route('admin.tenders.purchase-order.show', $tender) }}"
                   class="rounded-lg bg-white text-[#3553A8] px-6 py-3 text-sm font-bold shadow-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Lihat Dokumen PO
                </a>
            @else
                <a href="{{ route('admin.tenders.purchase-order.create', $tender) }}"
                   class="rounded-lg bg-emerald-500 text-white px-6 py-3 text-sm font-bold shadow-lg hover:bg-emerald-400 transition-colors flex items-center gap-2">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Generate Purchase Order
                </a>
            @endif
        </div>
    </div>

</div>
@endsection
