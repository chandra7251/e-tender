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

    {{-- Open Bidding Price (HPS) --}}
    <div>
        <label for="open_bidding_price" class="mb-2 block text-sm font-bold text-white">
            Harga Pembukaan Bidding (HPS)
            <span class="text-xs font-normal text-indigo-200">(opsional · dalam Rupiah)</span>
        </label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-semibold text-indigo-200">
                Rp
            </span>
            <input id="open_bidding_price" type="number" name="open_bidding_price" min="0" step="1"
                   value="{{ old('open_bidding_price', $tender?->open_bidding_price) }}"
                   placeholder="0"
                   class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] pl-10 pr-4 py-2.5 text-sm
                          text-white placeholder-indigo-200 outline-none
                          focus:border-white focus:ring-1 focus:ring-white
                          @error('open_bidding_price') border-red-400 @enderror">
        </div>
        @error('open_bidding_price')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
    </div>

    {{-- Photo --}}
    <div>
        <label for="photo" class="mb-2 block text-sm font-bold text-white">
            Foto Barang / Jasa
            <span class="text-xs font-normal text-indigo-200">(opsional · maks. 3 MB · JPG / PNG)</span>
        </label>

        {{-- Preview foto yang sudah ada (saat edit) --}}
        @if ($tender?->photo_url)
            <div class="mb-3">
                <img src="{{ $tender->photo_url }}" alt="Foto tender"
                     class="h-40 w-full rounded-lg object-cover border border-[#4A6BCC]">
                <p class="mt-1 text-xs text-indigo-200">Foto saat ini. Upload foto baru untuk mengganti.</p>
            </div>
        @endif

        <input id="photo" type="file" name="photo" accept="image/jpeg,image/png"
               class="block w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-3 py-2 text-sm
                      text-indigo-100 file:mr-4 file:rounded file:border-0
                      file:bg-[#4A6BCC] file:px-3 file:py-1.5 file:text-sm file:text-white
                      file:cursor-pointer hover:file:bg-[#5A7BE0]
                      @error('photo') border-red-400 @enderror">
        @error('photo')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
    </div>

    {{-- Status — read-only, diubah via menu Ubah Status di halaman detail --}}
    <div>
        <label class="mb-2 block text-sm font-bold text-white">Status</label>
        <div class="flex items-center gap-3 rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5">
            @php
                $currentStatus = $tender?->status ?? 'draft';
                $statusColor = match($currentStatus) {
                    'open'       => 'bg-green-500',
                    'aanwijzing' => 'bg-yellow-500',
                    'bidding'    => 'bg-blue-500',
                    'closed'     => 'bg-gray-500',
                    'finished'   => 'bg-purple-500',
                    default      => 'bg-slate-400', // draft
                };
            @endphp
            <span class="inline-block h-2.5 w-2.5 rounded-full {{ $statusColor }}"></span>
            <span class="text-sm font-semibold text-white">{{ ucfirst($currentStatus) }}</span>
            <span class="ml-auto text-xs text-indigo-300">Ubah status melalui halaman detail tender</span>
        </div>
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
