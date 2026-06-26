@extends('pdf.layout')
@section('content')
<div class="doc-title">
  <h2>Rekapitulasi Tender</h2>
  <div class="doc-no">Nomor: REKAP-{{ str_pad($tender->id,4,'0',STR_PAD_LEFT) }}/{{ now()->format('m') }}/{{ now()->format('Y') }}</div>
</div>
<div class="section">
  <div class="section-title">Informasi Tender</div>
  <table class="kv">
    <tr><td>Judul Tender</td><td>{{ $tender->title }}</td></tr>
    <tr><td>Status</td><td>{{ strtoupper($tender->status) }}</td></tr>
    <tr><td>HPS Total</td><td>Rp {{ number_format((float)$tender->open_bidding_price, 0, ',', '.') }}</td></tr>
    <tr><td>Periode Bidding</td><td>{{ optional($tender->bidding_start)->format('d/m/Y') }} s/d {{ optional($tender->bidding_end)->format('d/m/Y') }}</td></tr>
    <tr><td>Jumlah Peserta</td><td>{{ $tender->participants->count() }} vendor</td></tr>
    <tr><td>Jumlah Penawaran</td><td>{{ $tender->bids->count() }} penawaran</td></tr>
    <tr><td>Dibuat Oleh</td><td>{{ optional($tender->creator)->name }}</td></tr>
  </table>
</div>
@if($tender->bids->count() > 0)
<div class="section">
  <div class="section-title">Daftar Penawaran</div>
  <table>
    <thead><tr><th>#</th><th>Nama Vendor</th><th>Perusahaan</th><th>Harga Penawaran</th><th>Selisih vs HPS</th></tr></thead>
    <tbody>
    @foreach($tender->bids->sortBy('bid_price') as $i => $bid)
    @php
      $selisih = $bid->bid_price - $tender->open_bidding_price;
      $selisihColor = $selisih <= 0 ? '#2e7d32' : '#c62828';
      $selisihStr = ($selisih > 0 ? '+' : '') . ' Rp ' . number_format(abs($selisih), 0, ',', '.');
    @endphp
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ optional(optional($bid->vendor)->user)->name }}</td>
      <td>{{ optional($bid->vendor)->company_name }}</td>
      <td>Rp {{ number_format($bid->bid_price,0,',','.') }}</td>
      <td style="color: {{ $selisihColor }}">{{ $selisihStr }}</td>
    </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endif
@if($tender->result && optional($tender->result->winnerVendor)->user)
<div class="section">
  <div class="section-title">Pemenang Tender</div>
  <table class="kv">
    <tr><td>Pemenang</td><td><strong>{{ optional($tender->result->winnerVendor->user)->name }}</strong></td></tr>
    <tr><td>Perusahaan</td><td>{{ optional($tender->result->winnerVendor)->company_name }}</td></tr>
  </table>
</div>
@endif
<table class="signature-block">
  <tr>
    <td>
      <div class="signature-title">Panitia/Pokja</div>
      <div class="signature-line">______________________</div>
      <div class="signature-meta">Panitia Pengadaan</div>
    </td>
    <td>
      <div class="signature-title">Mengetahui, PPK</div>
      <div class="signature-line">{{ optional($tender->creator)->name ?? 'Admin Utama' }}</div>
      <div class="signature-meta">Pejabat Pembuat Komitmen</div>
    </td>
  </tr>
</table>
@endsection
