@extends('layouts.admin')

@section('title', 'Edit Tender')
@section('page-title', 'Edit Tender')

@section('content')
<div class="max-w-3xl space-y-6">

    <a href="{{ route('admin.tenders.show', $tender) }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-300 transition-colors">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Detail Tender
    </a>

    <form method="POST" action="{{ route('admin.tenders.update', $tender) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.tenders._form', compact('tender'))

        <div class="flex items-center justify-end gap-3 border-t border-gray-800 pt-4">
            <a href="{{ route('admin.tenders.show', $tender) }}"
               class="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-400
                      hover:text-white transition-colors duration-150">
                Batal
            </a>
            <button type="submit"
                    class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white
                           hover:bg-indigo-500 transition-colors duration-150">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
@endsection
