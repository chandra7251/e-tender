@extends('layouts.admin')
@section('title', 'Blacklist Vendor')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Blacklist Vendor</h1>
            <p class="text-gray-500">Kelola vendor yang diblokir dari partisipasi tender</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['total_blacklisted'] }}</div>
            <div class="text-sm text-red-500">Vendor Di-blacklist</div>
        </div>
        <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['total_active'] }}</div>
            <div class="text-sm text-green-500">Vendor Aktif</div>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm border p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="text-xs text-gray-500 block mb-1">Cari Vendor</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama perusahaan..."
                class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="text-xs text-gray-500 block mb-1">Filter</label>
            <select name="filter" class="border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Semua</option>
                <option value="blacklisted" {{ request('filter') === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                <option value="active" {{ request('filter') === 'active' ? 'selected' : '' }}>Aktif</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            Filter
        </button>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan Blacklist</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($vendors as $vendor)
                <tr class="{{ $vendor->is_blacklisted ? 'bg-red-50/50' : '' }}">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $vendor->company_name }}</div>
                        <div class="text-sm text-gray-500">{{ $vendor->user->email ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3">
                        @if($vendor->is_blacklisted)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">⛔ Blacklisted</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">✅ Aktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">
                        {{ $vendor->blacklist_reason ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        @if($vendor->is_blacklisted && $vendor->blacklisted_at)
                            {{ $vendor->blacklisted_at->format('d M Y') }}
                            <br><span class="text-xs">oleh {{ $vendor->blacklister->name ?? '-' }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($vendor->is_blacklisted)
                            <form action="{{ route('admin.vendors.unblacklist', $vendor) }}" method="POST"
                                onsubmit="return confirm('Hapus vendor ini dari blacklist?')">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    Hapus Blacklist
                                </button>
                            </form>
                        @else
                            <button type="button"
                                onclick="document.getElementById('blacklist-modal-{{ $vendor->id }}').classList.remove('hidden')"
                                class="px-3 py-1.5 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Blacklist
                            </button>

                            {{-- Blacklist Modal --}}
                            <div id="blacklist-modal-{{ $vendor->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
                                    <h3 class="text-lg font-bold text-gray-800 mb-2">⛔ Blacklist Vendor</h3>
                                    <p class="text-sm text-gray-500 mb-4">Vendor: <strong>{{ $vendor->company_name }}</strong></p>
                                    <form action="{{ route('admin.vendors.blacklist', $vendor) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Blacklist *</label>
                                            <textarea name="blacklist_reason" rows="3" required
                                                class="w-full border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                                placeholder="Jelaskan alasan vendor di-blacklist..."></textarea>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button"
                                                onclick="document.getElementById('blacklist-modal-{{ $vendor->id }}').classList.add('hidden')"
                                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                                Batal
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                Konfirmasi Blacklist
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Tidak ada data vendor.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $vendors->links() }}</div>
</div>
@endsection
