@extends('layouts.admin')
@section('title','E-Catalogue Vendor')
@section('page-title','E-Catalogue Vendor')
@section('content')
<div class="w-full space-y-6">

  <!-- Stats -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="rounded-xl bg-white border border-gray-200 p-5">
      <p class="text-xs font-semibold text-gray-500 uppercase">Total Item</p>
      <p class="mt-2 text-2xl font-bold text-gray-800">{{ $totalItems }}</p>
    </div>
    <div class="rounded-xl bg-white border border-gray-200 p-5">
      <p class="text-xs font-semibold text-gray-500 uppercase">Item Aktif</p>
      <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $activeItems }}</p>
    </div>
    <div class="rounded-xl bg-white border border-gray-200 p-5">
      <p class="text-xs font-semibold text-gray-500 uppercase">Kategori</p>
      <p class="mt-2 text-2xl font-bold text-[#3553A8]">{{ $categories->count() }}</p>
    </div>
    <div class="rounded-xl bg-white border border-gray-200 p-5">
      <p class="text-xs font-semibold text-gray-500 uppercase">Non-Aktif</p>
      <p class="mt-2 text-2xl font-bold text-red-500">{{ $totalItems - $activeItems }}</p>
    </div>
  </div>

  <!-- Filter -->
  <form method="GET" class="flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama item..." class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3553A8]">
    <select name="category" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#3553A8]">
      <option value="">Semua Kategori</option>
      @foreach($categories as $cat)
      <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
      @endforeach
    </select>
    <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
      <option value="">Semua Status</option>
      <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
      <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
    </select>
    <button type="submit" class="rounded-lg bg-[#3553A8] px-4 py-2 text-sm font-semibold text-white">Filter</button>
    <a href="{{ route('admin.catalogue.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600">Reset</a>
  </form>

  <!-- Grid Items -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($items as $item)
    <div class="rounded-xl bg-white border border-gray-200 overflow-hidden hover:shadow-md transition">
      <!-- Photo -->
      <div class="h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
        @if($item->photos->first())
          <img src="{{ asset('storage/'.$item->photos->first()->photo_path) }}" class="h-full w-full object-cover">
        @else
          <svg class="h-12 w-12 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        @endif
      </div>
      <div class="p-4">
        <div class="flex items-start justify-between gap-2">
          <h3 class="text-sm font-semibold text-gray-800 leading-tight line-clamp-2">{{ $item->name }}</h3>
          <span class="shrink-0 inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">{{ $item->is_active ? 'Aktif' : 'Non-aktif' }}</span>
        </div>
        <p class="mt-1 text-xs text-gray-400">{{ $item->category->name ?? 'Tanpa Kategori' }}</p>
        <p class="mt-1 text-xs text-gray-500">{{ $item->vendor->company_name ?? '-' }}</p>
        @if($item->price_estimate)
        <p class="mt-2 text-sm font-bold text-[#3553A8]">Rp {{ number_format($item->price_estimate,0,',','.') }} / {{ $item->unit }}</p>
        @endif
        <div class="mt-3 flex gap-2">
          <a href="{{ route('admin.catalogue.show', $item->id) }}" class="flex-1 text-center rounded-lg border border-gray-300 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50">Detail</a>
          <form method="POST" action="{{ route('admin.catalogue.toggle', $item->id) }}" class="flex-1">
            @csrf @method('PATCH')
            <button type="submit" class="w-full rounded-lg py-1.5 text-xs font-medium {{ $item->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}">
              {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
          </form>
        </div>
      </div>
    </div>
    @empty
    <div class="col-span-full py-16 text-center text-gray-400">
      <p class="text-lg">Belum ada item katalog</p>
      <p class="text-sm mt-1">Vendor belum menambahkan produk ke katalog</p>
    </div>
    @endforelse
  </div>
  <div>{{ $items->withQueryString()->links() }}</div>
</div>
@endsection
