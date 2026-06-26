@extends('layouts.admin')
@section('title', 'Pengaturan Instansi')
@section('page-title', 'Pengaturan Instansi & White-Label')

@section('content')
<div class="max-w-3xl space-y-6">

    @if(session('success'))
    <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}"
          class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
        @csrf @method('PUT')
        <h3 class="text-sm font-bold text-gray-700 border-b pb-3">Informasi Instansi</h3>
        @php
        $fields = [
            'instansi_name'        => ['label'=>'Nama Instansi',          'placeholder'=>'PT Contoh Tbk'],
            'instansi_tagline'     => ['label'=>'Tagline',                'placeholder'=>'Pengadaan Cepat, Transparan, Akuntabel'],
            'instansi_address'     => ['label'=>'Alamat',                 'placeholder'=>'Jl. Sudirman Kav. 1, Jakarta'],
            'instansi_phone'       => ['label'=>'Telepon',                'placeholder'=>'(021) 1234-5678'],
            'instansi_email'       => ['label'=>'Email',                  'placeholder'=>'procurement@instansi.go.id'],
            'document_header'      => ['label'=>'Header Dokumen PDF',     'placeholder'=>'INSTANSI PEMERINTAH REPUBLIK INDONESIA'],
            'document_footer'      => ['label'=>'Footer Dokumen PDF',     'placeholder'=>'Dokumen ini diterbitkan secara elektronik.'],
            'sanggahan_window_days'=> ['label'=>'Batas Hari Sanggahan',   'placeholder'=>'5'],
        ];
        @endphp
        @foreach($fields as $key => $opt)
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">{{ $opt['label'] }}</label>
            <input type="text" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}"
                   placeholder="{{ $opt['placeholder'] }}"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none transition">
        </div>
        @endforeach

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Warna Utama (Hex)</label>
            <div class="flex items-center gap-3">
                <input type="color" name="primary_color" value="{{ $settings['primary_color'] ?? '#3553A8' }}"
                       class="h-10 w-14 rounded cursor-pointer border border-gray-300">
                <input type="text" id="color-text" value="{{ $settings['primary_color'] ?? '#3553A8' }}"
                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none"
                       readonly>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="hidden" name="enable_2fa" value="false">
                <input type="checkbox" name="enable_2fa" value="true" class="sr-only peer"
                        {{ ($settings['enable_2fa'] ?? 'false') === 'true' ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer
                            peer-checked:after:translate-x-full peer-checked:after:border-white
                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                            after:bg-white after:border-gray-300 after:border after:rounded-full
                            after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3553A8]"></div>
            </label>
            <span class="text-sm font-medium text-gray-700">Aktifkan Two-Factor Authentication (2FA)</span>
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="rounded-lg bg-[#3553A8] px-6 py-2.5 text-sm font-bold text-white hover:bg-[#2B438A] transition">
                Simpan Pengaturan
            </button>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-bold text-gray-700 border-b pb-3 mb-5">Logo Instansi</h3>
        @if(!empty($settings['instansi_logo']))
        <div class="mb-4">
            <p class="text-xs text-gray-500 mb-2">Logo saat ini:</p>
            <img src="{{ asset('storage/' . $settings['instansi_logo']) }}"
                 alt="Logo Instansi" class="h-20 object-contain rounded border p-2">
        </div>
        @endif
        <form method="POST" action="{{ route('admin.settings.logo') }}"
              enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Upload Logo Baru (PNG/JPG, max 2MB)</label>
                <input type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml"
                       class="block w-full text-sm text-gray-600
                              file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-sm file:font-semibold file:bg-[#3553A8] file:text-white
                              hover:file:bg-[#2B438A] cursor-pointer">
            </div>
            <button type="submit"
                    class="rounded-lg bg-[#F09459] px-5 py-2 text-sm font-bold text-white hover:bg-orange-600 transition">
                Upload Logo
            </button>
        </form>
    </div>

</div>

<script>
    document.querySelector('input[type="color"]')?.addEventListener('input', function() {
        document.getElementById('color-text').value = this.value;
    });
</script>
@endsection
