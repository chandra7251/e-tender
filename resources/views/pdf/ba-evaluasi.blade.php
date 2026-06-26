@extends('pdf.layout')
@section('content')
<div class="doc-title">
  <h2>Berita Acara Evaluasi Penawaran</h2>
  <div class="doc-no">Nomor: {{ $doc_no }}</div>
</div>
<div class="section">
  <div class="section-title">Data Tender</div>
  <table class="kv">
    <tr><td>Judul Paket</td><td>{{ $tender->title }}</td></tr>
    <tr><td>Metode Evaluasi</td><td>{{ strtoupper($tender->evaluation_method ?? 'sistem gugur') }}</td></tr>
    <tr><td>Nilai HPS</td><td>Rp {{ number_format((float)$tender->open_bidding_price, 0, ',', '.') }}</td></tr>
    <tr><td>Tanggal Evaluasi</td><td>{{ now()->format('d/m/Y') }}</td></tr>
    <tr><td>Jumlah Penawaran Masuk</td><td>{{ $tender->bids->count() }}</td></tr>
  </table>
</div>
@if($tender->evaluationCriteria && $tender->evaluationCriteria->count() > 0)
<div class="section">
  <div class="section-title">Kriteria Evaluasi</div>
  <table>
    <thead><tr><th>Kriteria</th><th>Bobot</th><th>Tipe</th></tr></thead>
    <tbody>
    @foreach($tender->evaluationCriteria as $c)
    <tr><td>{{ $c->name }}</td><td>{{ $c->weight }}%</td><td>{{ ucfirst($c->type ?? '-') }}</td></tr>
    @endforeach
    </tbody>
  </table>
</div>
@endif
@if($tender->bids->count() > 0)
<div class="section">
  <div class="section-title">Hasil Evaluasi</div>
  <table>
    <thead><tr><th>#</th><th>Vendor</th><th>Perusahaan</th><th>Harga Penawaran</th><th>Total Skor</th><th>Keterangan</th></tr></thead>
    <tbody>
    @foreach($tender->bids->sortByDesc(fn($b) => $b->evaluations->sum('weighted_score')) as $i => $bid)
    @php $totalSkor = round($bid->evaluations->sum('weighted_score'), 2); @endphp
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ optional(optional($bid->vendor)->user)->name ?? '-' }}</td>
      <td>{{ optional($bid->vendor)->company_name ?? '-' }}</td>
      <td>Rp {{ number_format($bid->bid_price, 0, ',', '.') }}</td>
      <td><strong>{{ $totalSkor }}</strong></td>
      <td>{{ $totalSkor >= ($tender->passing_grade ?? 65) ? 'LULUS' : 'GUGUR' }}</td>
    </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endif
<div style="margin-top:12px;font-size:10px;line-height:1.6;">Demikian berita acara evaluasi ini dibuat dengan sebenar-benarnya.</div>
<table class="signature-block">
  <tr>
    <td>
      <div class="signature-title">Tim Evaluator</div>
      <div class="signature-line">______________________</div>
    </td>
    <td>
      <div class="signature-title">PPK / Ketua Pokja</div>
      <div class="signature-line">{{ optional($tender->creator)->name }}</div>
    </td>
  </tr>
</table>
@endsection
