
@php
    $val = fn($field, $default = '') => old($field, $tender?->{$field} ?? $default);
    $dateVal = function($field) use ($tender) {
        if (old($field)) return old($field);
        if ($tender?->{$field}) return $tender->{$field}->format('Y-m-d\TH:i');
        return '';
    };
@endphp

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

    <div>
        <label for="photos" class="mb-2 block text-sm font-bold text-white">
            Foto Barang / Jasa
            <span class="text-xs font-normal text-indigo-200">(opsional · maks. 3 foto · maks. 3 MB/foto · JPG / PNG)</span>
        </label>

        @if ($tender && $tender->photos->isNotEmpty())
            <div class="mb-4">
                <p class="mb-2 text-xs text-indigo-200">Foto tersimpan ({{ $tender->photos->count() }}/3):</p>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    @foreach ($tender->photos as $photo)
                        <div class="group relative overflow-hidden rounded-lg border border-[#4A6BCC] bg-[#2B438A] aspect-square block">
                            <img src="{{ $photo->photo_url }}" alt="Foto tender"
                                 class="h-full w-full object-cover">

                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-150 bg-black/40">
                                <button type="button" 
                                        onclick="if(confirm('Yakin ingin menghapus foto ini?')) document.getElementById('delete-photo-{{ $photo->id }}').submit();"
                                        class="inline-flex items-center gap-1 rounded-md bg-red-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-600">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if (!$tender || $tender->photos->count() < 3)
            <input id="photos" type="file" name="photos[]" accept="image/jpeg,image/png" multiple
                   class="block w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-3 py-2 text-sm
                          text-indigo-100 file:mr-4 file:rounded file:border-0
                          file:bg-[#4A6BCC] file:px-3 file:py-1.5 file:text-sm file:text-white
                          file:cursor-pointer hover:file:bg-[#5A7BE0]
                          @error('photos') border-red-400 @enderror
                          @error('photos.*') border-red-400 @enderror">
            @error('photos')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
            @error('photos.*')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
            <p class="mt-2 text-xs text-indigo-200">Anda dapat memilih beberapa file sekaligus.</p>
        @else
            <p class="text-sm text-yellow-400 bg-yellow-400/10 p-3 rounded-md border border-yellow-400/30">
                Batas maksimal 3 foto telah tercapai. Hapus salah satu foto di atas untuk menambahkan yang baru.
            </p>
        @endif
    </div>

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

{{-- Evaluation Method Settings --}}
<div class="rounded-xl bg-[#3553A8] p-6 shadow-sm mt-6" x-data="{ method: '{{ old('evaluation_method', $tender?->evaluation_method ?? 'lowest_price') }}' }">
    <h3 class="mb-6 text-lg font-bold text-white">Metode Evaluasi</h3>
    <div class="space-y-4">
        <div>
            <label for="evaluation_method" class="mb-2 block text-sm font-bold text-white">Metode Evaluasi</label>
            <select id="evaluation_method" name="evaluation_method" x-model="method"
                class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm text-white outline-none focus:border-white focus:ring-1 focus:ring-white">
                <option value="lowest_price">Harga Terendah</option>
                <option value="multi_criteria">Multi Kriteria (Scoring)</option>
                <option value="two_envelope">Sistem 2 Amplop (Teknis + Harga)</option>
            </select>
        </div>

        {{-- Two Envelope Settings --}}
        <div x-show="method === 'two_envelope'" x-transition class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="technical_weight" class="mb-2 block text-sm font-bold text-white">
                    Bobot Teknis (%)
                </label>
                <input id="technical_weight" type="number" name="technical_weight" min="0" max="100" step="1"
                    value="{{ old('technical_weight', $tender?->technical_weight ?? 60) }}"
                    class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm text-white outline-none focus:border-white focus:ring-1 focus:ring-white">
            </div>
            <div>
                <label for="price_weight" class="mb-2 block text-sm font-bold text-white">
                    Bobot Harga (%)
                </label>
                <input id="price_weight" type="number" name="price_weight" min="0" max="100" step="1"
                    value="{{ old('price_weight', $tender?->price_weight ?? 40) }}"
                    class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm text-white outline-none focus:border-white focus:ring-1 focus:ring-white">
            </div>
            <div>
                <label for="passing_grade" class="mb-2 block text-sm font-bold text-white">
                    Passing Grade Teknis
                </label>
                <input id="passing_grade" type="number" name="passing_grade" min="0" max="100" step="1"
                    value="{{ old('passing_grade', $tender?->passing_grade ?? 70) }}"
                    class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm text-white outline-none focus:border-white focus:ring-1 focus:ring-white">
            </div>
            <div class="sm:col-span-3">
                <p class="text-xs text-indigo-200">
                    ⚠️ Total bobot teknis + harga harus 100%. Passing grade adalah nilai minimum untuk lulus evaluasi teknis.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="rounded-xl bg-[#3553A8] p-6 shadow-sm mt-6">
    <h3 class="mb-6 text-lg font-bold text-white">Timeline Tender</h3>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

        <div>
            <label for="start_date" class="mb-2 block text-sm font-bold text-white">
                Tanggal Mulai <span class="text-red-500">*</span>
            </label>
            <input id="start_date" type="datetime-local" name="start_date"
                   @if(!$tender) min="{{ \Carbon\Carbon::today()->format('Y-m-d\T00:00') }}" @endif
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

        <div></div>

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
