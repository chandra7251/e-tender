@extends('pdf.layout')
@section('content')
<div class="doc-title" style="margin-top: 10px; margin-bottom: 20px;">
  <div style="font-size: 16px; font-weight: bold; color: #1a237e;">PENGUMUMAN PEMENANG E-TENDERING</div>
  <div style="font-size: 12px; font-weight: bold;">Nomor : {{ $doc_no }}</div>
</div>

<div class="doc-title" style="margin-bottom: 30px;">
  <div style="font-size: 14px; font-weight: bold; text-decoration: underline;">PEKERJAAN</div>
  <div style="font-size: 14px; font-weight: bold;">{{ strtoupper($tender->title) }}</div>
</div>

<table style="width: 100%; border: none; font-size: 12px; margin-bottom: 20px;">
  <tr>
    <td style="width: 3%; vertical-align: top; border: none; padding: 0; font-weight: bold;">1.</td>
    <td style="vertical-align: top; border: none; padding: 0; padding-bottom: 15px; text-align: justify;">
      Dengan ini diumumkan kepada peserta Tender Umum <strong>"{{ $tender->title }}"</strong> bahwa sesuai hasil evaluasi administrasi, teknis, dan harga, peserta yang ditetapkan sebagai pemenang berdasarkan metode evaluasi {{ ucwords($tender->evaluation_method ?? 'Sistem Nilai') }} adalah sebagai berikut:
    </td>
  </tr>
  <tr>
    <td style="border: none; padding: 0;"></td>
    <td style="border: none; padding: 0;">
      <table style="width: 100%; border: none; font-size: 12px;">
        @if($tender->result && optional($tender->result->winnerVendor)->user)
        <tr>
          <td style="width: 35%; border: none; padding: 3px 0; vertical-align: top;">Nama Perusahaan</td>
          <td style="width: 5%; border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; font-weight: bold; vertical-align: top; color: #1a237e;">{{ strtoupper(optional($tender->result->winnerVendor)->company_name ?? optional($tender->result->winnerVendor->user)->name) }}</td>
        </tr>
        <tr>
          <td style="border: none; padding: 3px 0; vertical-align: top;">NPWP</td>
          <td style="border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; vertical-align: top;">{{ optional($tender->result->winnerVendor)->npwp ?? '-' }}</td>
        </tr>
        <tr>
          <td style="border: none; padding: 3px 0; vertical-align: top;">Alamat</td>
          <td style="border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; vertical-align: top;">{{ optional($tender->result->winnerVendor)->address ?? '-' }}</td>
        </tr>
        @php
            $winnerBid = $tender->bids->where('vendor_id', optional($tender->result->winnerVendor)->id)->first();
            $bidPrice = $winnerBid ? $winnerBid->bid_price : $tender->open_bidding_price;
        @endphp
        <tr>
          <td style="border: none; padding: 3px 0; vertical-align: top;">Biaya Pelaksanaan</td>
          <td style="border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; vertical-align: top;">
            <strong>Rp {{ number_format($bidPrice, 0, ',', '.') }}</strong><br>
            <span style="font-weight: normal; color: #555;">(belum termasuk PPN)</span>
          </td>
        </tr>
        <tr>
          <td style="border: none; padding: 3px 0; vertical-align: top;">Waktu Pelaksanaan</td>
          <td style="border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; font-weight: bold; vertical-align: top;">Sesuai ketentuan dalam perjanjian/kontrak.</td>
        </tr>
        @else
        <tr><td colspan="3" style="border: none; padding: 3px 0;">Belum ada pemenang yang ditetapkan.</td></tr>
        @endif
      </table>
    </td>
  </tr>
</table>

<table style="width: 100%; border: none; font-size: 12px; margin-bottom: 40px;">
  <tr>
    <td style="width: 3%; vertical-align: top; border: none; padding: 0; font-weight: bold;">2.</td>
    <td style="vertical-align: top; border: none; padding: 0; text-align: justify;">
      Demikian untuk diketahui, atas perhatian dan partisipasinya diucapkan terima kasih.
    </td>
  </tr>
</table>

<table style="width: 100%; border: none; font-size: 12px;">
  <tr>
    <td style="width: 45%; border: none;"></td>
    <td style="width: 55%; border: none;">
      <table style="width: 100%; border: none; margin-bottom: 20px;">
        <tr>
          <td style="width: 35%; border: none; padding: 0;">Dikeluarkan di</td>
          <td style="width: 5%; border: none; padding: 0; text-align: center;">:</td>
          <td style="border: none; padding: 0; font-weight: bold;">Jakarta</td>
        </tr>
        <tr>
          <td style="border: none; padding: 0; border-bottom: 1px solid #1a237e; padding-bottom: 5px;">Pada Tanggal</td>
          <td style="border: none; padding: 0; text-align: center; border-bottom: 1px solid #1a237e; padding-bottom: 5px;">:</td>
          <td style="border: none; padding: 0; border-bottom: 1px solid #1a237e; padding-bottom: 5px; font-weight: bold;">{{ now()->format('d F Y') }}</td>
        </tr>
      </table>
      
      <div style="text-align: center; margin-bottom: 70px; font-weight: bold; color: #1a237e;">
        {{ strtoupper($settings['instansi_name'] ?? 'ZETA') }}<br>
        UNIT PENGADAAN
      </div>
      <div style="text-align: center;">
        <span style="text-decoration: underline; font-weight: bold;">Ttd</span><br><br>
        <strong style="color: #1a237e;">UNIT PENGADAAN</strong>
      </div>
    </td>
  </tr>
</table>

<div style="margin-top: 50px; font-size: 10px; background-color: #f8fafc; border-left: 3px solid #1a237e; padding: 10px;">
  <div style="font-weight: bold; margin-bottom: 5px; color: #1a237e;">Catatan Penting:</div>
  <ul style="padding-left: 15px; margin: 0; text-align: justify; color: #333;">
    <li>Diimbau kepada penyedia barang dan jasa agar berhati-hati terhadap segala bentuk penipuan yang mengatasnamakan Direksi/Pejabat/Karyawan {{ $settings['instansi_name'] ?? 'ZETA' }}.</li>
    <li>{{ $settings['instansi_name'] ?? 'ZETA' }} tidak memungut biaya apapun dalam proses pengadaan barang/jasa dan barang siapa yang memberi dalam bentuk apapun kepada pejabat {{ $settings['instansi_name'] ?? 'ZETA' }} akan diberikan sanksi sesuai dengan ketentuan yang berlaku.</li>
  </ul>
</div>
@endsection
