@extends('layouts.admin')

@section('title', 'Buat Tender')
@section('page-title', 'Buat Tender Baru')

@section('content')
<div class="max-w-3xl space-y-6">

    <a href="{{ route('admin.tenders.index') }}"
       class="inline-flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-gray-900 transition-colors">
        <svg class="h-4 w-4 stroke-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali Ke Daftar Tender
    </a>

    <form method="POST" action="{{ route('admin.tenders.store') }}">
        @csrf

        @include('admin.tenders._form', ['tender' => null])

        <div class="flex items-center justify-end gap-3 border-t border-gray-300 pt-6 mt-6">
            <a href="{{ route('admin.tenders.index') }}"
               class="rounded-md border border-[#3553A8] bg-[#F0F2F5] px-8 py-2.5 text-sm font-bold text-[#3553A8]
                      hover:bg-indigo-50 transition-colors duration-150">
                Batal
            </a>
            <button type="submit"
                    class="rounded-md bg-[#3553A8] border border-[#3553A8] px-8 py-2.5 text-sm font-bold text-white
                           hover:bg-[#2B438A] transition-colors duration-150">
                Tambah
            </button>
        </div>
    </form>

</div>
@endsection
