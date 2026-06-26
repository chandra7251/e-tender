@extends('pdf.layout')
@section('content')

<div style="text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 25px;">
    <h2 style="margin: 0; font-size: 18px; letter-spacing: 1.5px; color: #0f172a; text-transform: uppercase;">Surat Perjanjian / Kontrak</h2>
    <div style="font-size: 11px; color: #64748b; margin-top: 5px;">Dokumen Resmi E-Procurement</div>
</div>

<div style="text-align: right; margin-bottom: 25px;">
    <div style="font-size: 16px; font-weight: bold; color: #1a237e; letter-spacing: 1px;">KONTRAK KERJA</div>
    <div style="font-size: 12px; color: #334155; margin-top: 3px;">Nomor: <strong>{{ $contract->contract_number }}</strong></div>
    <div style="font-size: 12px; color: #334155;">Tanggal: {{ now()->format('d F Y') }}</div>
</div>

<table style="width: 100%; border-collapse: separate; border-spacing: 15px 0; margin-bottom: 25px; margin-left: -15px; margin-right: -15px;">
    <tr>
        <td style="width: 50%; border: 1px solid #e2e8f0; padding: 15px; vertical-align: top; background-color: #f8fafc; border-radius: 4px;">
            <div style="font-weight: bold; font-size: 11px; color: #64748b; margin-bottom: 10px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">PIHAK PERTAMA (INSTANSI)</div>
            <div style="font-weight: bold; font-size: 13px; color: #0f172a; margin-bottom: 5px;">{{ strtoupper($settings['instansi_name'] ?? 'ZETA') }}</div>
            <div style="font-size: 11px; color: #334155; line-height: 1.6;">
                Alamat: {{ $settings['instansi_address'] ?? 'Gedung Pusat Pengadaan, Jakarta' }}<br>
                Email: {{ $settings['instansi_email'] ?? '-' }}<br>
                Telp: {{ $settings['instansi_phone'] ?? '-' }}
            </div>
        </td>
        <td style="width: 50%; border: 1px solid #e2e8f0; padding: 15px; vertical-align: top; background-color: #f8fafc; border-radius: 4px;">
            <div style="font-weight: bold; font-size: 11px; color: #64748b; margin-bottom: 10px; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">PIHAK KEDUA (VENDOR)</div>
            <div style="font-weight: bold; font-size: 13px; color: #0f172a; margin-bottom: 5px;">{{ strtoupper(optional($contract->vendor)->company_name) }}</div>
            <div style="font-size: 11px; color: #334155; line-height: 1.6;">
                Perwakilan: {{ optional($contract->vendor->user)->name }}<br>
                Alamat: {{ optional($contract->vendor)->address }}<br>
                Telp: {{ optional($contract->vendor)->phone }}
            </div>
        </td>
    </tr>
</table>

<div style="margin-bottom: 20px;">
    <div style="background-color: #1a237e; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px; margin-bottom: 10px; letter-spacing: 0.5px;">DETAIL PEKERJAAN</div>
    <table style="width: 100%; font-size: 11px; border-collapse: collapse; border: 1px solid #e2e8f0;">
        <tr>
            <td style="width: 25%; padding: 8px 10px; border-bottom: 1px solid #f1f5f9; color: #475569; background-color: #fafafa;">Nama Pekerjaan</td>
            <td style="width: 5%; padding: 8px 5px; border-bottom: 1px solid #f1f5f9; text-align: center; background-color: #fafafa;">:</td>
            <td style="width: 70%; padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #0f172a;">{{ strtoupper(optional($contract->tender)->title) }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 10px; border-bottom: 1px solid #f1f5f9; color: #475569; background-color: #fafafa;">Nilai Kontrak</td>
            <td style="padding: 8px 5px; border-bottom: 1px solid #f1f5f9; text-align: center; background-color: #fafafa;">:</td>
            <td style="padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #1a237e; font-size: 12px;">Rp {{ number_format($contract->contract_value, 0, ',', '.') }} <span style="font-weight: normal; font-size: 10px; color: #64748b;">(Sudah termasuk PPN)</span></td>
        </tr>
        <tr>
            <td style="padding: 8px 10px; border-bottom: 1px solid #f1f5f9; color: #475569; background-color: #fafafa;">Masa Pelaksanaan</td>
            <td style="padding: 8px 5px; border-bottom: 1px solid #f1f5f9; text-align: center; background-color: #fafafa;">:</td>
            <td style="padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-weight: bold; color: #0f172a;">{{ optional($contract->start_date)->format('d F Y') }} <span style="font-weight: normal; margin: 0 5px;">s.d</span> {{ optional($contract->end_date)->format('d F Y') }}</td>
        </tr>
    </table>
</div>

@if($contract->terms)
<div style="margin-bottom: 20px;">
    <div style="background-color: #1a237e; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px; margin-bottom: 10px; letter-spacing: 0.5px;">SYARAT &amp; KETENTUAN</div>
    <div style="padding: 15px; border: 1px solid #e2e8f0; background-color: #f8fafc; font-size: 11px; line-height: 1.6; color: #334155; text-align: justify; white-space: pre-line;">{{ $contract->terms }}</div>
</div>
@endif

@if($contract->deliveries && $contract->deliveries->count() > 0)
<div style="margin-bottom: 30px;">
    <div style="background-color: #1a237e; color: #ffffff; padding: 6px 12px; font-weight: bold; font-size: 12px; margin-bottom: 10px; letter-spacing: 0.5px;">JADWAL PELAKSANAAN (MILESTONE)</div>
    <table class="data-table" style="width: 100%; border-collapse: collapse; font-size: 11px; text-align: left;">
        <thead>
            <tr>
                <th style="background-color: #334155; color: #fff; padding: 8px; border: 1px solid #334155; text-align: center; width: 5%;">No</th>
                <th style="background-color: #334155; color: #fff; padding: 8px; border: 1px solid #334155;">Deskripsi Pekerjaan / Milestone</th>
                <th style="background-color: #334155; color: #fff; padding: 8px; border: 1px solid #334155; text-align: center; width: 25%;">Batas Waktu</th>
            </tr>
        </thead>
        <tbody>
        @foreach($contract->deliveries as $i => $d)
            <tr>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: center;">{{ $loop->iteration }}</td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; color: #0f172a;">{{ $d->title }}</td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #0f172a;">{{ optional($d->due_date)->format('d F Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

<div style="margin-top: 40px; margin-bottom: 20px; text-align: justify; font-size: 11px; color: #475569; line-height: 1.6;">
    Demikian Surat Perjanjian ini dibuat dan disepakati oleh kedua belah pihak dalam keadaan sadar dan tanpa paksaan dari pihak manapun, dibuat dalam rangkap 2 (dua) bermeterai cukup yang masing-masing mempunyai kekuatan hukum yang sama.
</div>

<table style="width: 100%; border: none; font-size: 11px; margin-top: 30px; page-break-inside: avoid;">
    <tr>
        <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
            <div style="margin-bottom: 70px; color: #0f172a;">
                <strong>PIHAK KEDUA</strong><br>
                {{ strtoupper(optional($contract->vendor)->company_name) }}
            </div>
            <div style="font-weight: bold; text-decoration: underline; color: #0f172a; font-size: 12px;">{{ strtoupper(optional($contract->vendor->user)->name) }}</div>
            <div style="color: #64748b;">Direktur Utama</div>
            @if($contract->signed_by_vendor_at)
            <div style="font-size: 9px; color: #94a3b8; margin-top: 5px;">Ditandatangani digital:<br>{{ $contract->signed_by_vendor_at->format('d/m/Y H:i') }}</div>
            @endif
        </td>
        <td style="width: 50%; text-align: center; vertical-align: top; border: none;">
            <div style="margin-bottom: 20px; color: #0f172a;">
                <strong>PIHAK PERTAMA</strong><br>
                PPK / PEJABAT PENGADAAN
            </div>
            <div style="display: inline-block; border: 1px dashed #cbd5e1; padding: 15px; font-size: 9px; color: #94a3b8; margin-bottom: 15px;">METERAI<br>Rp 10.000</div><br>
            <div style="font-weight: bold; text-decoration: underline; color: #0f172a; font-size: 12px;">{{ strtoupper(optional($contract->creator)->name) }}</div>
            <div style="color: #64748b;">Pejabat Pembuat Komitmen</div>
            @if($contract->signed_by_admin_at)
            <div style="font-size: 9px; color: #94a3b8; margin-top: 5px;">Ditandatangani digital:<br>{{ $contract->signed_by_admin_at->format('d/m/Y H:i') }}</div>
            @endif
        </td>
    </tr>
</table>

@if($contract->document_hash)
<div style="margin-top: 40px; padding: 12px; background-color: #f8fafc; border: 1px solid #e2e8f0; font-size: 9px; color: #64748b; border-radius: 4px; text-align: center;">
    <strong>Sertifikasi Dokumen Elektronik</strong> &mdash; Dokumen ini telah dienkripsi dengan sistem keamanan E-Procurement.<br>
    SHA-256: <code>{{ $contract->document_hash }}</code>
</div>
@endif

@endsection
