@extends('layouts.admin')
@section('title', __('app.ai_price_predict') . ' - ZETA')
@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🤖 {{ __('app.ai_price_predict') }}</h1>
        <p class="text-gray-500 mt-1">Analisis harga, scoring vendor, dan deteksi anomali berbasis AI</p>
    </div>
    <!-- Tender Selector -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Tender untuk Dianalisis</label>
        <select id="tenderSelect" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">-- Pilih Tender --</option>
            @foreach($tenders as $tender)
                <option value="{{ $tender->id }}">{{ $tender->title }} (HPS: Rp {{ number_format($tender->hps) }})</option>
            @endforeach
        </select>
        <button onclick="analyzeNow()" class="mt-3 bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
            ⚡ Analisis Sekarang
        </button>
    </div>
    <!-- Loading -->
    <div id="aiLoading" class="hidden text-center py-12">
        <div class="animate-spin h-10 w-10 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-3"></div>
        <p class="text-gray-500 text-sm">AI sedang menganalisis data...</p>
    </div>
    <!-- Results -->
    <div id="aiResult" class="hidden space-y-6">
        <!-- Price Prediction -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">💰 Prediksi Harga</h2>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-xs text-blue-500 mb-1">Prediksi Harga Menang</p>
                    <p id="predictedPrice" class="text-xl font-bold text-blue-700">-</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-xs text-green-500 mb-1">Batas Bawah</p>
                    <p id="lowerBound" class="text-xl font-bold text-green-700">-</p>
                </div>
                <div class="bg-orange-50 rounded-lg p-4 text-center">
                    <p class="text-xs text-orange-500 mb-1">Batas Atas</p>
                    <p id="upperBound" class="text-xl font-bold text-orange-700">-</p>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-3">
                <span class="text-sm text-gray-500">Confidence:</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3">
                    <div id="confidenceBar" class="bg-blue-500 h-3 rounded-full transition-all" style="width:0%"></div>
                </div>
                <span id="confidencePct" class="text-sm font-bold text-blue-700">-</span>
            </div>
            <p id="priceRecommendation" class="mt-3 text-sm text-gray-600 bg-yellow-50 p-3 rounded-lg"></p>
        </div>
        <!-- Vendor Ranking -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">🏆 AI Vendor Ranking</h2>
            <div id="vendorRanking" class="space-y-3"></div>
        </div>
        <!-- Anomaly Report -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">⚠️ Anomaly Report</h2>
            <div id="anomalyReport" class="space-y-3"></div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function formatRp(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n)); }
function analyzeNow() {
    const id = document.getElementById('tenderSelect').value;
    if (!id) return alert('Pilih tender terlebih dahulu.');
    document.getElementById('aiLoading').classList.remove('hidden');
    document.getElementById('aiResult').classList.add('hidden');
    fetch(`/api/ai/analyze/${id}`, { headers: { 'Accept': 'application/json', 'Authorization': 'Bearer ' + (localStorage.getItem('admin_token') || '') } })
    .then(r => r.json()).then(data => {
        const d = data.data;
        document.getElementById('predictedPrice').textContent = formatRp(d.price_prediction.predicted_price);
        document.getElementById('lowerBound').textContent     = formatRp(d.price_prediction.lower_bound);
        document.getElementById('upperBound').textContent     = formatRp(d.price_prediction.upper_bound);
        document.getElementById('confidenceBar').style.width  = d.price_prediction.confidence + '%';
        document.getElementById('confidencePct').textContent  = d.price_prediction.confidence + '%';
        document.getElementById('priceRecommendation').textContent = d.price_prediction.recommendation;
        // Vendor ranking
        const vrEl = document.getElementById('vendorRanking');
        vrEl.innerHTML = d.vendor_ranking.map((v,i) => `
            <div class="flex items-center gap-4 p-3 rounded-lg ${i===0?'bg-yellow-50 border border-yellow-200':'bg-gray-50'}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm ${i===0?'bg-yellow-400 text-white':'bg-gray-200 text-gray-600'}">${i+1}</div>
                <div class="flex-1">
                    <p class="font-semibold text-sm">${v.vendor_name}</p>
                    <p class="text-xs text-gray-500">${formatRp(v.bid_price)} &bull; Win rate: ${v.breakdown.win_rate_pct}% &bull; Rating: ${v.breakdown.avg_rating}/5</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-blue-700">${v.total_score}</p>
                    <p class="text-xs ${v.recommendation==='SANGAT_DIREKOMENDASIKAN'?'text-green-600':'text-gray-500'}">${v.recommendation.replace(/_/g,' ')}</p>
                </div>
            </div>`).join('');
        // Anomaly
        const anEl = document.getElementById('anomalyReport');
        anEl.innerHTML = d.anomaly_report.map(a => `
            <div class="flex items-center gap-4 p-3 rounded-lg ${a.is_anomaly?'bg-red-50 border border-red-200':'bg-gray-50'}">
                <span class="text-lg">${a.is_anomaly?'⚠️':'✅'}</span>
                <div class="flex-1">
                    <p class="font-semibold text-sm">${a.vendor_name}</p>
                    <p class="text-xs text-gray-500">${formatRp(a.bid_price)} &bull; HPS Ratio: ${(a.hps_ratio*100).toFixed(1)}%</p>
                    <p class="text-xs ${a.is_anomaly?'text-red-600':'text-green-600'}">${a.reason}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold ${a.anomaly_score>60?'text-red-600':'text-gray-500'}">${a.anomaly_score}</p>
                    <p class="text-xs">${a.flag}</p>
                </div>
            </div>`).join('') || '<p class="text-gray-400 text-sm">Belum ada bid untuk dianalisis.</p>';
        document.getElementById('aiLoading').classList.add('hidden');
        document.getElementById('aiResult').classList.remove('hidden');
    }).catch(() => { document.getElementById('aiLoading').classList.add('hidden'); alert('Gagal mengambil data AI.'); });
}
</script>
@endpush
