<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $po->po_number }}</title>
    <style>
        @page {
            margin: 30px 40px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 13px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            text-transform: uppercase;
            font-size: 20px;
            letter-spacing: 1px;
        }
        .header p {
            margin: 3px 0 0;
            color: #7f8c8d;
            font-size: 11px;
        }
        .po-title {
            text-align: right;
            margin-bottom: 20px;
        }
        .po-title h2 {
            margin: 0;
            color: #2980b9;
            font-size: 22px;
        }
        .po-title p {
            margin: 2px 0 0;
            font-weight: bold;
            color: #555;
            font-size: 12px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            vertical-align: top;
            width: 50%;
        }
        .box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        .box-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #2c3e50;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .items-table th, .items-table td {
            border: 1px solid #dee2e6;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }
        .items-table .amount {
            text-align: right;
            font-weight: bold;
        }
        .items-table .total-row {
            background-color: #f8f9fa;
        }
        .notes {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #fdfbfb;
            border-left: 4px solid #2980b9;
            font-size: 12px;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .signatures {
            width: 100%;
            margin-top: 30px;
            text-align: center;
        }
        .signatures td {
            width: 50%;
            vertical-align: bottom;
        }
        .signature-space {
            height: 70px;
        }
        .signature-line {
            width: 180px;
            border-bottom: 1px solid #333;
            margin: 0 auto 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #95a5a6;
            border-top: 1px solid #ecf0f1;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ config('app.name', 'SISTEM E-PROCUREMENT') }}</h1>
        <p>Dokumen Surat Pemesanan / Purchase Order (PO) Resmi</p>
    </div>

    <div class="po-title">
        <h2>PURCHASE ORDER</h2>
        <p>Nomor PO: {{ $po->po_number }}</p>
        <p>Tanggal: {{ $po->issued_date ? $po->issued_date->format('d F Y') : date('d F Y') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td style="padding-right: 10px;">
                <div class="box">
                    <div class="box-title">KEPADA VENDOR:</div>
                    <strong>{{ $po->vendor?->company_name ?? 'Vendor Tidak Ditemukan' }}</strong><br>
                    Alamat: {{ $po->vendor?->address ?? '-' }}<br>
                    Email: {{ $po->vendor?->user?->email ?? '-' }}<br>
                    Telp: {{ $po->vendor?->phone_number ?? '-' }}
                </div>
            </td>
            <td style="padding-left: 10px;">
                <div class="box">
                    <div class="box-title">DETAIL TENDER:</div>
                    <strong>{{ $tender->title }}</strong><br>
                    Status Tender: Selesai<br>
                    Diterbitkan oleh: {{ $po->generator?->name ?? 'Admin' }}
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" style="text-align: center;">No</th>
                <th width="70%">Nama / Deskripsi Tender</th>
                <th width="25%" style="text-align: right;">Total Penawaran (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>
                    {{ $tender->title }}
                </td>
                <td class="amount">{{ number_format($po->amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" style="text-align: right;"><strong>GRAND TOTAL</strong></td>
                <td class="amount" style="color: #2980b9; font-size: 14px;"><strong>Rp {{ number_format($po->amount, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if($po->notes)
    <div class="notes">
        <div class="notes-title">Catatan Tambahan:</div>
        {{ $po->notes }}
    </div>
    @endif

    <div class="notes" style="background-color: #fff; border-left: 4px solid #e67e22;">
        <div class="notes-title">Ketentuan Sistem Tender & Bidding:</div>
        <ol style="margin-top: 5px; margin-bottom: 0; padding-left: 20px;">
            <li>Dokumen Purchase Order (PO) ini dihasilkan secara otomatis oleh sistem sebagai tahap akhir (Result Output) dari proses Tender.</li>
            <li>Nilai Grand Total di atas merupakan hasil mutlak dari sistem <i>Bidding</i> berbasis waktu (Time-Based) yang diajukan oleh Vendor.</li>
            <li>Vendor pemenang telah melewati proses Registrasi, Verifikasi Dokumen (Approved), serta proses Aanwijzing sesuai aturan sistem.</li>
        </ol>
    </div>

    <table class="signatures">
        <tr>
            <td>
                <div style="font-size: 12px;">Disetujui Oleh,</div>
                <div class="signature-space"></div>
                <div class="signature-line"></div>
                <strong>{{ $po->generator?->name ?? 'Admin Procurement' }}</strong><br>
                <span style="font-size: 11px;">Pejabat Pembuat Komitmen (PPK)</span>
            </td>
            <td>
                <div style="font-size: 12px;">Menerima & Menyetujui,</div>
                <div class="signature-space"></div>
                <div class="signature-line"></div>
                <strong>Direktur/Penanggung Jawab</strong><br>
                <span style="font-size: 11px;">{{ $po->vendor?->company_name ?? 'Vendor' }}</span>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh Sistem E-Procurement (Tender & Bidding System) pada {{ now()->format('d M Y, H:i:s') }}.<br>
    </div>

</body>
</html>
