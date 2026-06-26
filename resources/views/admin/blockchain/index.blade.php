@extends('layouts.admin')
@section('title', 'Blockchain Verification - ZETA')
@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🔗 Blockchain Transparency Layer</h1>
        <p class="text-gray-500 mt-1">Setiap keputusan penting di-hash dan disimpan secara permanen. Tidak bisa dimanipulasi.</p>
    </div>
    <!-- Verify box -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-700 mb-3">🔍 Verifikasi Hash</h2>
        <div class="flex gap-3">
            <input id="hashInput" type="text" placeholder="Masukkan block hash atau payload hash..."
                class="flex-1 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
            <button onclick="verifyHash()" class="bg-blue-600 text-white px-6 py-3 rounded-lg text-sm font-semibold hover:bg-blue-700">
                Verifikasi
            </button>
        </div>
        <div id="verifyResult" class="hidden mt-4 p-4 rounded-lg"></div>
    </div>
    <!-- Chain Records -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">📜 Riwayat Blockchain Records</h2>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Event</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tender ID</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Block Hash</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Network</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($records as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $r->id }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-semibold bg-purple-100 text-purple-700">{{ $r->event_type }}</span>
                    </td>
                    <td class="px-4 py-3">{{ $r->tender_id }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-500" title="{{ $r->block_hash }}">{{ substr($r->block_hash,0,20) }}...</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs {{ $r->network==='local'?'bg-gray-100 text-gray-600':'bg-green-100 text-green-700' }}">{{ strtoupper($r->network) }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($r->verified)
                            <span class="text-green-600 font-semibold text-xs">✅ Verified</span>
                        @else
                            <span class="text-yellow-600 font-semibold text-xs">⏳ Pending</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $r->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Belum ada record blockchain.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        {{ $records->links() }}
    </div>
</div>
@endsection
@push('scripts')
<script>
function verifyHash() {
    const hash = document.getElementById('hashInput').value.trim();
    if (!hash) return;
    fetch(`/api/blockchain/verify?hash=${encodeURIComponent(hash)}`, { headers: { 'Accept': 'application/json' } })
    .then(r => r.json()).then(data => {
        const d = data.data;
        const el = document.getElementById('verifyResult');
        el.classList.remove('hidden');
        if (!d.found) {
            el.className = 'mt-4 p-4 rounded-lg bg-red-50 border border-red-200';
            el.innerHTML = '<p class="text-red-700 font-semibold">❌ Hash tidak ditemukan dalam sistem ZETA.</p>';
        } else {
            const ok = d.is_valid;
            el.className = `mt-4 p-4 rounded-lg ${ok?'bg-green-50 border border-green-200':'bg-red-50 border border-red-200'}`;
            el.innerHTML = `<p class="font-bold ${ok?'text-green-700':'text-red-700'}">${ok?'✅ Data VALID — Tidak ada manipulasi terdeteksi':'⚠️ PERINGATAN — Data mungkin telah diubah'}</p>
                <div class="mt-2 text-sm space-y-1">
                    <p><b>Event:</b> ${d.event_type}</p>
                    <p><b>Tender ID:</b> ${d.tender_id}</p>
                    <p><b>Waktu:</b> ${d.created_at}</p>
                    <p><b>TX Hash:</b> <code class="text-xs">${d.tx_hash||'-'}</code></p>
                    <p><b>Network:</b> ${d.network}</p>
                </div>`;
        }
    });
}
</script>
@endpush
