@extends('layouts.admin')

@section('title', 'Vendor Management')
@section('page-title', 'Vendor Management')

@section('content')
<div class="space-y-4">

    <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
        {{-- ── Filter & Search Bar ─────────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('admin.vendors.index') }}"
              class="flex flex-col gap-3 sm:flex-row sm:items-center mb-6">

            {{-- Search removed as requested --}}

            {{-- Status filter --}}
            <select name="status"
                    class="rounded-md border-0 bg-white px-4 py-2.5 text-sm font-medium
                           text-gray-700 outline-none focus:ring-2 focus:ring-[#2B438A]">
                <option value="">Semua Status</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <button type="submit"
                    class="rounded-md bg-[#2B438A] px-6 py-2.5 text-sm font-semibold text-white
                           hover:bg-[#1E3066] transition-colors duration-150">
                Filter
            </button>

            @if (request('search') || request('status'))
                <a href="{{ route('admin.vendors.index') }}"
                   class="rounded-md border border-[#4A6BCC] px-4 py-2.5 text-sm text-indigo-200
                          hover:text-white transition-colors duration-150">
                    Reset
                </a>
            @endif
        </form>

        {{-- ── Table ─────────────────────────────────────────────── --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-white" style="min-width: 580px;">
                <thead>
                    <tr class="border-b border-[#4A6BCC] text-left text-xs font-bold uppercase tracking-wider text-indigo-100">
                        <th class="px-2 py-4">Perusahaan</th>
                        <th class="px-2 py-4">Email</th>
                        <th class="px-2 py-4">Phone</th>
                        <th class="px-2 py-4">Status</th>
                        <th class="px-2 py-4">Terdaftar</th>
                        <th class="px-2 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#4A6BCC]">
                    @forelse ($vendors as $vendor)
                        <tr class="hover:bg-[#2B438A] transition-colors duration-150">
                            <td class="px-2 py-4 font-semibold tracking-wide whitespace-nowrap">
                                {{ $vendor->company_name }}
                            </td>
                            <td class="px-2 py-4 text-indigo-50 text-xs">
                                {{ $vendor->user->email ?? '-' }}
                            </td>
                            <td class="px-2 py-4 text-indigo-50 whitespace-nowrap">
                                {{ $vendor->phone ?? '-' }}
                            </td>
                            <td class="px-2 py-4">
                                @php
                                    $badge = match($vendor->verification_status) {
                                        'approved' => 'bg-[#28C5D4] text-white',
                                        'rejected' => 'bg-[#788B9A] text-white',
                                        default    => 'bg-[#F09459] text-white',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">
                                    {{ ucfirst($vendor->verification_status) }}
                                </span>
                            </td>
                            <td class="px-2 py-4 text-indigo-50 whitespace-nowrap">
                                {{ $vendor->created_at->format('d M Y') }}
                            </td>
                            <td class="px-2 py-4 text-right">
                                <a href="{{ route('admin.vendors.show', $vendor) }}"
                                   class="rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-1.5 text-xs font-semibold text-white
                                          hover:bg-[#1E3066] transition-colors duration-150 whitespace-nowrap">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-2 py-10 text-center text-sm text-indigo-200">
                                Tidak ada vendor ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($vendors->hasPages())
        <div class="mt-4">
            {{ $vendors->links() }}
        </div>
    @endif

</div>
@endsection
