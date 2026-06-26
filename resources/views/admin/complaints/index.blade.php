@extends('layouts.admin')
@section('title', 'Sanggahan & Banding')
@section('page-title', 'Manajemen Sanggahan & Banding')

@section('content')
<div class="space-y-6">

    {{-- Filter Status --}}
    <div class="flex flex-wrap gap-3">
        @foreach([''=> 'Semua','pending'=>'Pending','reviewed'=>'Ditinjau','accepted'=>'Diterima','rejected'=>'Ditolak'] as $val => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $val, 'page' => null]) }}"
           class="rounded-full px-4 py-1.5 text-xs font-semibold border transition
                   {{ request('status') === $val ? 'bg-[#3553A8] text-white border-[#3553A8]' : 'bg-white text-gray-600 border-gray-300 hover:border-[#3553A8] hover:text-[#3553A8]' }}">
             {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#3553A8] text-white">
                <tr>
                    <th class="px-4 py-3 text-left w-10">#</th>
                    <th class="px-4 py-3 text-left">Tender</th>
                    <th class="px-4 py-3 text-left">Vendor</th>
                    <th class="px-4 py-3 text-left">Tipe</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Batas Waktu</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($complaints as $c)
            @php
                $statusColor = match($c->status) {
                    'pending'  => 'bg-yellow-100 text-yellow-700',
                    'reviewed' => 'bg-blue-100 text-blue-700',
                    'accepted' => 'bg-green-100 text-green-700',
                    'rejected' => 'bg-red-100 text-red-700',
                    default    => 'bg-gray-100 text-gray-600',
                };
                $typeColor = $c->type === 'banding' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700';
                $isExpired = $c->deadline && now()->gt($c->deadline);
            @endphp
            <tr class="border-t border-gray-100 hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                <td class="px-4 py-3 font-medium text-gray-800 max-w-[180px] truncate" title="{{ $c->tender->title ?? '' }}">{{ $c->tender->title ?? '-' }}</td>
                <td class="px-4 py-3 text-gray-700">{{ optional($c->vendor->user)->name ?? '-' }}</td>
                <td class="px-4 py-3">
                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $typeColor }}">{{ strtoupper($c->type) }}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusColor }}">{{ strtoupper($c->status) }}</span>
                </td>
                <td class="px-4 py-3 text-xs {{ $isExpired ? 'text-red-500 font-semibold' : 'text-gray-500' }}">
                    {{ $c->deadline ? $c->deadline->format('d/m/Y H:i') : '-' }}
                    @if($isExpired) <span class="text-red-400">(Expired)</span> @endif
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $c->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    @if($c->status === 'pending')
                    <button onclick="openModal({{ $c->id }}, '{{ addslashes($c->reason) }}')"
                            class="rounded bg-[#3553A8] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#2B438A] transition">
                        Tanggapi
                    </button>
                    @else
                    <span class="text-xs text-gray-500 italic">{{ Str::limit($c->response ?? '-', 50) }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm">Belum ada sanggahan atau banding.</p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $complaints->links() }}</div>
    </div>
</div>

{{-- Modal Tanggapi --}}
<div id="respond-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-6">
        <h3 class="text-base font-bold text-gray-800 mb-4">Tanggapi Sanggahan / Banding</h3>
        <p id="modal-reason" class="text-sm text-gray-600 mb-4 bg-gray-50 rounded-lg p-3 max-h-32 overflow-y-auto whitespace-pre-wrap"></p>
        <form id="respond-form" method="POST" action="">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status Tanggapan</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3553A8]">
                    <option value="reviewed">Sedang Ditinjau</option>
                    <option value="accepted">Diterima</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggapan Resmi *</label>
                <textarea name="response" rows="4" required
                          placeholder="Tuliskan tanggapan resmi atas sanggahan ini..."
                          class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#3553A8] resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="rounded-lg bg-[#3553A8] px-4 py-2 text-sm font-bold text-white hover:bg-[#2B438A]">
                    Kirim Tanggapan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id, reason) {
    document.getElementById('modal-reason').textContent = reason;
    document.getElementById('respond-form').action = `/admin/complaints/${id}/respond`;
    const modal = document.getElementById('respond-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeModal() {
    const modal = document.getElementById('respond-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
