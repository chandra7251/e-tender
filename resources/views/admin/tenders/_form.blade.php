{{-- Shared form partial for create and edit tender --}}
{{-- Usage: @include('admin.tenders._form', ['tender' => $tender|null]) --}}

@php
    $val = fn($field, $default = '') => old($field, $tender?->{$field} ?? $default);
    $dateVal = function($field) use ($tender) {
        if (old($field)) return old($field);
        if ($tender?->{$field}) return $tender->{$field}->format('Y-m-d\TH:i');
        return '';
    };
@endphp

{{-- Error summary --}}
@if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600 mb-6">
        <p class="font-bold mb-1">Terdapat kesalahan input:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="rounded-xl bg-[#3553A8] p-6 space-y-6 shadow-sm">

    {{-- Title --}}
    <div>
        <label for="title" class="mb-2 block text-sm font-bold text-white">
            Judul Tender <span class="text-red-500">*</span>
        </label>
        <input id="title" type="text" name="title" value="{{ $val('title') }}"
               placeholder="Contoh: Pengadaan Laptop 2026"
               class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                      text-white placeholder-indigo-200 outline-none
                      focus:border-white focus:ring-1 focus:ring-white
                      @error('title') border-red-400 @enderror">
        @error('title')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="mb-2 block text-sm font-bold text-white">
            Deskripsi <span class="text-red-500">*</span>
        </label>
        <textarea id="description" name="description" rows="4"
                  placeholder="Deskripsi singkat tender...."
                  class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                         text-white placeholder-indigo-200 outline-none
                         focus:border-white focus:ring-1 focus:ring-white
                         @error('description') border-red-400 @enderror">{{ $val('description') }}</textarea>
        @error('description')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
    </div>

    {{-- Specification --}}
    <div>
        <label for="specification" class="mb-2 block text-sm font-bold text-white">
            Spesifikasi <span class="text-red-500">*</span>
        </label>
        <textarea id="specification" name="specification" rows="4"
                  placeholder="Detail spesifikasi teknis...."
                  class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                         text-white placeholder-indigo-200 outline-none
                         focus:border-white focus:ring-1 focus:ring-white
                         @error('specification') border-red-400 @enderror">{{ $val('specification') }}</textarea>
        @error('specification')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
    </div>

    {{-- Status --}}
    <div>
        <label for="status" class="mb-2 block text-sm font-bold text-white">
            Status <span class="text-red-500">*</span>
        </label>
        <select id="status" name="status"
                class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                       text-white outline-none
                       focus:border-white focus:ring-1 focus:ring-white
                       @error('status') border-red-400 @enderror">
            @foreach (['draft','open','aanwijzing','bidding','closed','finished'] as $s)
                <option value="{{ $s }}" {{ $val('status', 'draft') === $s ? 'selected' : '' }}>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
        @error('status')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
    </div>

</div>

{{-- Timeline --}}
<div class="rounded-xl bg-[#3553A8] p-6 shadow-sm mt-6">
    <h3 class="mb-6 text-lg font-bold text-white">Timeline Tender</h3>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

        <div>
            <label for="start_date" class="mb-2 block text-sm font-bold text-white">
                Tanggal Mulai <span class="text-red-500">*</span>
            </label>
            <input id="start_date" type="datetime-local" name="start_date"
                   value="{{ $dateVal('start_date') }}"
                   class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                          text-white outline-none [color-scheme:dark]
                          focus:border-white focus:ring-1 focus:ring-white
                          @error('start_date') border-red-400 @enderror">
            @error('start_date')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="end_date" class="mb-2 block text-sm font-bold text-white">
                Tanggal Selesai <span class="text-red-500">*</span>
            </label>
            <input id="end_date" type="datetime-local" name="end_date"
                   value="{{ $dateVal('end_date') }}"
                   class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                          text-white outline-none [color-scheme:dark]
                          focus:border-white focus:ring-1 focus:ring-white
                          @error('end_date') border-red-400 @enderror">
            @error('end_date')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="aanwijzing_date" class="mb-2 block text-sm font-bold text-white">
                Tanggal Aanwijzing <span class="text-xs font-normal text-indigo-200">(opsional)</span>
            </label>
            <input id="aanwijzing_date" type="datetime-local" name="aanwijzing_date"
                   value="{{ $dateVal('aanwijzing_date') }}"
                   class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                          text-white outline-none [color-scheme:dark]
                          focus:border-white focus:ring-1 focus:ring-white">
        </div>

        <div></div>{{-- spacer --}}

        <div>
            <label for="bidding_start" class="mb-2 block text-sm font-bold text-white">
                Bidding Mulai <span class="text-red-500">*</span>
            </label>
            <input id="bidding_start" type="datetime-local" name="bidding_start"
                   value="{{ $dateVal('bidding_start') }}"
                   class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                          text-white outline-none [color-scheme:dark]
                          focus:border-white focus:ring-1 focus:ring-white
                          @error('bidding_start') border-red-400 @enderror">
            @error('bidding_start')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="bidding_end" class="mb-2 block text-sm font-bold text-white">
                Bidding Selesai <span class="text-red-500">*</span>
            </label>
            <input id="bidding_end" type="datetime-local" name="bidding_end"
                   value="{{ $dateVal('bidding_end') }}"
                   class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                          text-white outline-none [color-scheme:dark]
                          focus:border-white focus:ring-1 focus:ring-white
                          @error('bidding_end') border-red-400 @enderror">
            @error('bidding_end')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
        </div>

    </div>
</div>
