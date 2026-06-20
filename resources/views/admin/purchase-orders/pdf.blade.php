<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $po->po_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 14px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            text-transform: uppercase;
            font-size: 24px;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 12px;
        }
        .po-title {
            text-align: right;
            margin-bottom: 30px;
        }
        .po-title h2 {
            margin: 0;
            color: #2980b9;
            font-size: 28px;
        }
        .po-title p {
            margin: 0;
            font-weight: bold;
            color: #555;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table td {
            vertical-align: top;
            width: 50%;
        }
        .box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 15px;
            border-radius: 5px;
        }
        .box-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th, .items-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
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
            margin-bottom: 40px;
            padding: 15px;
            background-color: #fdfbfb;
            border-left: 4px solid #2980b9;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .signatures {
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }
        .signatures td {
            width: 50%;
            vertical-align: bottom;
            padding-top: 50px;
        }
        .signature-line {
            width: 200px;
            border-bottom: 1px solid #333;
            margin: 0 auto 10px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
            border-top: 1px solid #ecf0f1;
            padding-top: 15px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>KEMENTERIAN PENGADAAN DIGITAL RI</h1>
        <p>Gedung Utama Lt. 4, Jl. Teknologi No. 99, Jakarta 10110</p>
        <p>Email: procurement@instansi.go.id | Telp: (021) 1234-5678</p>
    </div>

    <div class="po-title">
        <h2>PURCHASE ORDER</h2>
        <p>PO Number: {{ $po->po_number }}</p>
        <p>Date: {{ $po->issued_date ? $po->issued_date->format('d F Y') : date('d F Y') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td style="padding-right: 15px;">
                <div class="box">
                    <div class="box-title">KEPADA VENDOR:</div>
                    <strong>{{ $po->vendor->company_name ?? 'Vendor Tidak Ditemukan' }}</strong><br>
                    Alamat: {{ $po->vendor->address ?? '-' }}<br>
                    Email: {{ $po->vendor->user->email ?? '-' }}<br>
                    Telp: {{ $po->vendor->phone_number ?? '-' }}
                </div>
            </td>
            <td style="padding-left: 15px;">
                <div class="box">
                    <div class="box-title">DETAIL TENDER:</div>
                    <strong>{{ $tender->title }}</strong><br>
                    Metode Seleksi: Terbuka<br>
                    Status Tender: Selesai<br>
                    Diterbitkan oleh: {{ $po->generator->name ?? 'Admin' }}
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="55%">Deskripsi Pekerjaan / Pengadaan</th>
                <th width="15%">Kuantitas</th>
                <th width="25%" style="text-align: right;">Total Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <strong>Pengadaan Berdasarkan Tender:</strong><br>
                    {{ $tender->title }}<br><br>
                    <small>Spesifikasi: Sesuai dokumen penawaran yang disetujui.</small>
                </td>
                <td>1 Paket</td>
                <td class="amount">{{ number_format($po->amount, 0, ',', '.') }}</td>
            </tr>
            <!-- Tambahkan baris PPN sebagai simulasi -->
            <tr>
                <td colspan="3" style="text-align: right;">Subtotal</td>
                <td class="amount">{{ number_format($po->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">PPN (11%) *Termasuk</td>
                <td class="amount">{{ number_format($po->amount * 0.11, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" style="text-align: right;"><strong>GRAND TOTAL</strong></td>
                <td class="amount" style="color: #2980b9; font-size: 16px;"><strong>Rp {{ number_format($po->amount, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if($po->notes)
    <div class="notes">
        <div class="notes-title">Catatan Tambahan:</div>
        {{ $po->notes }}
    </div>
    @endif

    <div class="notes" style="background-color: #fff; border-left: 4px solid #e67e22; font-size: 12px;">
        <div class="notes-title">Syarat & Ketentuan (Terms & Conditions):</div>
        <ol style="margin-top: 5px; padding-left: 20px;">
            <li>Vendor wajib mengirimkan barang/jasa sesuai dengan jadwal yang telah disepakati pada dokumen Aanwijzing.</li>
            <li>Pembayaran akan diproses maksimal 30 hari (Net 30) setelah Berita Acara Serah Terima (BAST) ditandatangani oleh kedua belah pihak.</li>
            <li>Dokumen Purchase Order ini adalah sah dan digenerate secara otomatis oleh Sistem E-Procurement.</li>
        </ol>
    </div>

    <table class="signatures">
        <tr>
            <td>
                <div>Disetujui Oleh,</div>
                <br><br><br><br>
                <div class="signature-line"></div>
                <strong>{{ $po->generator->name ?? 'Admin Procurement' }}</strong><br>
                Pejabat Pembuat Komitmen (PPK)
            </td>
            <td>
                <div>Menerima & Menyetujui,</div>
                <br><br><br><br>
                <div class="signature-line"></div>
                <strong>Direktur/Penanggung Jawab</strong><br>
                {{ $po->vendor->company_name ?? 'Vendor' }}
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini digenerate secara elektronik oleh Sistem E-Procurement pada {{ now()->format('d F Y, H:i:s') }}.<br>
        Scan QR Code pada dokumen asli untuk memverifikasi keabsahan dokumen.
    </div>

</body>
</html>
