<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
  .card { background: white; max-width: 560px; margin: 0 auto; border-radius: 12px;
          padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
  .badge { display:inline-block; padding: 4px 12px; border-radius: 999px;
           font-size:12px; font-weight:700; }
  .badge-1h  { background:#FEE2E2; color:#991B1B; }
  .badge-24h { background:#FEF9C3; color:#92400E; }
  h2 { color: #1e293b; margin-top: 16px; }
  .info-row { display:flex; justify-content:space-between; padding: 10px 0;
              border-bottom: 1px solid #f1f5f9; font-size:14px; }
  .label { color:#64748b; }
  .value { font-weight:600; color:#0f172a; }
  .cta { display:block; text-align:center; margin-top:24px; padding:14px;
         background:#3553A8; color:white; text-decoration:none;
         border-radius:8px; font-weight:700; font-size:15px; }
  .footer { text-align:center; margin-top:20px; font-size:12px; color:#94a3b8; }
</style>
</head>
<body>
<div class="card">
  <span class="badge  $reminderType === '1h' ? 'badge-1h' : 'badge-24h' ">
     $reminderType === '1h' ? '⚠️ 1 JAM LAGI' : '⏰ 24 JAM LAGI' 
  </span>
  <h2>Deadline Bidding Segera Berakhir!</h2>
  <p style="color:#475569;font-size:14px;">Jangan sampai terlewat — periode bidding untuk tender berikut akan segera ditutup.</p>

  <div style="margin-top:20px;">
    <div class="info-row">
      <span class="label">Tender</span>
      <span class="value"> $tender->title </span>
    </div>
    <div class="info-row">
      <span class="label">Bidding Ditutup</span>
      <span class="value"> $tender->bidding_end?->format('d M Y, H:i')  WIB</span>
    </div>
    <div class="info-row" style="border:none;">
      <span class="label">Status</span>
      <span class="value" style="color:#2563EB;">Bidding Aktif</span>
    </div>
  </div>

  <a href=" config('app.url') " class="cta">Ajukan Penawaran Sekarang &rarr;</a>

  <div class="footer">
    ZETA E-Procurement &bull; Email otomatis, jangan dibalas.
  </div>
</div>
</body>
</html>
