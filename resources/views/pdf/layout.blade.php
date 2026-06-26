<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin:0; padding:0; box-sizing:border-box; font-family: 'Helvetica', Arial, sans-serif; }
  body { font-size:12px; color:#000; line-height:1.5; background-color: #fff; }
  
  /* Header Styles */
  .header-table { width:100%; border-collapse:collapse; margin-bottom: 25px; border-bottom: 3px solid #1a237e; padding-bottom: 10px; }
  .header-table td { vertical-align:middle; border:none; padding: 0; padding-bottom: 10px; }
  .logo-text { font-size:24px; font-weight:bold; color:#1a237e; margin-top:5px; letter-spacing: 1px; }
  .instansi-tagline { font-size: 11px; color:#1a237e; font-weight: bold; }
  
  /* Typography */
  .doc-title { text-align:center; margin: 20px 0; }
  
  /* Generic Table Styles */
  table.data-table { width:100%; border-collapse:collapse; margin-bottom:15px; font-size:11px; }
  table.data-table th { background-color:#1a237e; color:white; padding:8px 10px; text-align:left; font-weight:bold; border: 1px solid #1a237e; }
  table.data-table td { padding:8px 10px; border:1px solid #ddd; vertical-align:top; color:#333; }
  
  /* Footer */
  @page { margin: 25mm 20mm 25mm 20mm; }
  .footer { position:fixed; bottom:-10mm; left:0; width:100%; font-size:9px; color:#1a237e; line-height: 1.4; border-top: 1px solid #1a237e; padding-top: 5px; }
</style>
</head>
<body>

@php
    $logoPath = public_path('images/logo.png');
    $logoSrc = '';
    if (file_exists($logoPath)) {
        $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp

<table class="header-table">
  <tr>
    <td style="width: 20%;">
      @if($logoSrc)
        <img src="{{ $logoSrc }}" style="max-height: 50px;" alt="Logo">
      @endif
    </td>
    <td style="text-align: right; width: 80%;">
      <div class="logo-text">{{ strtoupper($settings['instansi_name'] ?? 'ZETA') }}</div>
      <div class="instansi-tagline">E-PROCUREMENT SYSTEM</div>
    </td>
  </tr>
</table>

@yield('content')

<div class="footer">
  <table style="width:100%; border:none;">
    <tr>
      <td style="border:none; text-align:left; padding:0; font-weight:bold;">
        ZETA HEAD OFFICE<br>
        <span style="font-weight:normal; color:#555;">{{ $settings['instansi_address'] ?? 'Gedung Pusat Pengadaan, Jakarta, Indonesia' }}</span>
      </td>
      <td style="border:none; text-align:right; padding:0; color:#555;">
        Telp: {{ $settings['instansi_phone'] ?? '-' }} | Email: {{ $settings['instansi_email'] ?? '-' }}<br>
        Website: www.zeta.co.id
      </td>
    </tr>
  </table>
</div>

</body>
</html>
