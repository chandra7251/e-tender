<?php $__env->startSection('content'); ?>
<div class="doc-title" style="margin-top: 10px; margin-bottom: 20px;">
  <div style="font-size: 16px; font-weight: bold; color: #1a237e; text-decoration: underline;">SURAT PERJANJIAN / KONTRAK</div>
  <div style="font-size: 12px; font-weight: bold; margin-top: 5px;">Nomor : <?php echo e($contract->contract_number); ?></div>
</div>

<div style="margin-bottom: 20px; text-align: justify;">
  Pada hari ini, <span style="font-weight: bold;"><?php echo e(now()->isoFormat('dddd')); ?></span> tanggal <span style="font-weight: bold;"><?php echo e(now()->isoFormat('D')); ?></span> bulan <span style="font-weight: bold;"><?php echo e(now()->isoFormat('MMMM')); ?></span> tahun <span style="font-weight: bold;"><?php echo e(now()->isoFormat('Y')); ?></span>, telah disepakati Perjanjian Pelaksanaan Pekerjaan antara:
</div>

<table style="width: 100%; border: none; font-size: 12px; margin-bottom: 20px;">
  <tr>
    <td style="width: 3%; vertical-align: top; border: none; padding: 0; font-weight: bold;">I.</td>
    <td style="vertical-align: top; border: none; padding: 0;">
      <table style="width: 100%; border: none; font-size: 12px;">
        <tr>
          <td style="width: 30%; border: none; padding: 3px 0; vertical-align: top;">Nama Instansi</td>
          <td style="width: 5%; border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; font-weight: bold; vertical-align: top; color: #1a237e;"><?php echo e(strtoupper($settings['instansi_name'] ?? 'ZETA')); ?></td>
        </tr>
        <tr>
          <td style="border: none; padding: 3px 0; vertical-align: top;">Alamat</td>
          <td style="border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; vertical-align: top;"><?php echo e($settings['instansi_address'] ?? 'Gedung Pusat Pengadaan, Jakarta'); ?></td>
        </tr>
        <tr>
          <td colspan="3" style="border: none; padding: 10px 0 0 0; text-align: justify;">
            Dalam hal ini bertindak untuk dan atas nama <strong><?php echo e($settings['instansi_name'] ?? 'ZETA'); ?></strong>, yang selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<table style="width: 100%; border: none; font-size: 12px; margin-bottom: 30px;">
  <tr>
    <td style="width: 3%; vertical-align: top; border: none; padding: 0; font-weight: bold;">II.</td>
    <td style="vertical-align: top; border: none; padding: 0;">
      <table style="width: 100%; border: none; font-size: 12px;">
        <tr>
          <td style="width: 30%; border: none; padding: 3px 0; vertical-align: top;">Nama Vendor</td>
          <td style="width: 5%; border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; font-weight: bold; vertical-align: top;"><?php echo e(strtoupper(optional($contract->vendor->user)->name)); ?></td>
        </tr>
        <tr>
          <td style="border: none; padding: 3px 0; vertical-align: top;">Perusahaan</td>
          <td style="border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; font-weight: bold; vertical-align: top;"><?php echo e(strtoupper(optional($contract->vendor)->company_name)); ?></td>
        </tr>
        <tr>
          <td style="border: none; padding: 3px 0; vertical-align: top;">Telepon</td>
          <td style="border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; vertical-align: top;"><?php echo e(optional($contract->vendor)->phone); ?></td>
        </tr>
        <tr>
          <td style="border: none; padding: 3px 0; vertical-align: top;">Alamat</td>
          <td style="border: none; padding: 3px 0; text-align: center; vertical-align: top;">:</td>
          <td style="border: none; padding: 3px 0; vertical-align: top;"><?php echo e(optional($contract->vendor)->address); ?></td>
        </tr>
        <tr>
          <td colspan="3" style="border: none; padding: 10px 0 0 0; text-align: justify;">
            Dalam hal ini bertindak untuk dan atas nama <strong><?php echo e(optional($contract->vendor)->company_name); ?></strong>, yang selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<div style="margin-bottom: 25px;">
  <div style="font-weight: bold; font-size: 12px; margin-bottom: 8px; color: #1a237e; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Pasal 1 &mdash; Lingkup Pekerjaan</div>
  <div style="text-align: justify; line-height: 1.6;">
    Pihak Pertama memberikan pekerjaan kepada Pihak Kedua, dan Pihak Kedua menyatakan bersedia dan menyanggupi untuk melaksanakan pekerjaan: <br>
    <strong style="font-size: 13px;">"<?php echo e(strtoupper(optional($contract->tender)->title)); ?>"</strong>.
  </div>
</div>

<div style="margin-bottom: 25px;">
  <div style="font-weight: bold; font-size: 12px; margin-bottom: 8px; color: #1a237e; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Pasal 2 &mdash; Nilai Kontrak &amp; Jangka Waktu</div>
  <table style="width: 100%; border: none; font-size: 12px;">
    <tr>
      <td style="width: 25%; border: none; padding: 3px 0;">Nilai Kontrak</td>
      <td style="width: 5%; border: none; padding: 3px 0; text-align: center;">:</td>
      <td style="border: none; padding: 3px 0; font-weight: bold; font-size: 13px;">Rp <?php echo e(number_format($contract->contract_value, 0, ',', '.')); ?> <span style="font-size: 11px; font-weight: normal; color: #555;">(Sudah termasuk pajak yang berlaku)</span></td>
    </tr>
    <tr>
      <td style="border: none; padding: 3px 0;">Tanggal Mulai</td>
      <td style="border: none; padding: 3px 0; text-align: center;">:</td>
      <td style="border: none; padding: 3px 0; font-weight: bold;"><?php echo e(optional($contract->start_date)->format('d F Y')); ?></td>
    </tr>
    <tr>
      <td style="border: none; padding: 3px 0;">Tanggal Selesai</td>
      <td style="border: none; padding: 3px 0; text-align: center;">:</td>
      <td style="border: none; padding: 3px 0; font-weight: bold;"><?php echo e(optional($contract->end_date)->format('d F Y')); ?></td>
    </tr>
  </table>
</div>

<?php if($contract->terms): ?>
<div style="margin-bottom: 25px;">
  <div style="font-weight: bold; font-size: 12px; margin-bottom: 8px; color: #1a237e; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Pasal 3 &mdash; Syarat &amp; Ketentuan</div>
  <div style="text-align: justify; line-height: 1.6; white-space: pre-line;">
    <?php echo e($contract->terms); ?>

  </div>
</div>
<?php endif; ?>

<?php if($contract->deliveries && $contract->deliveries->count() > 0): ?>
<div style="margin-bottom: 30px;">
  <div style="font-weight: bold; font-size: 12px; margin-bottom: 10px; color: #1a237e; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Pasal 4 &mdash; Jadwal Pelaksanaan (Milestone)</div>
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 5%; text-align: center;">No</th>
        <th>Deskripsi Milestone</th>
        <th style="width: 25%; text-align: center;">Batas Waktu</th>
      </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $contract->deliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
        <td style="text-align: center;"><?php echo e($loop->iteration); ?></td>
        <td><?php echo e($d->title); ?></td>
        <td style="text-align: center;"><?php echo e(optional($d->due_date)->format('d F Y')); ?></td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<div style="margin-bottom: 20px; text-align: justify; line-height: 1.6;">
  Demikian Surat Perjanjian ini dibuat dalam rangkap 2 (dua), masing-masing bermeterai cukup dan mempunyai kekuatan hukum yang sama, untuk ditandatangani oleh kedua belah pihak.
</div>

<table style="width: 100%; border: none; font-size: 12px; margin-top: 40px; page-break-inside: avoid;">
  <tr>
    <td style="width: 50%; border: none; text-align: center; vertical-align: top;">
      <div style="font-weight: bold; margin-bottom: 70px;">PIHAK KEDUA<br><?php echo e(strtoupper(optional($contract->vendor)->company_name)); ?></div>
      <div style="font-weight: bold; text-decoration: underline;"><?php echo e(strtoupper(optional($contract->vendor->user)->name)); ?></div>
      <div style="font-size: 11px;">Direktur Utama</div>
      <?php if($contract->signed_by_vendor_at): ?>
      <div style="font-size: 10px; color: #555; margin-top: 5px;">Ditandatangani digital: <?php echo e($contract->signed_by_vendor_at->format('d/m/Y H:i')); ?></div>
      <?php endif; ?>
    </td>
    <td style="width: 50%; border: none; text-align: center; vertical-align: top;">
      <div style="font-weight: bold; margin-bottom: 50px;">PIHAK PERTAMA<br>PPK / PEJABAT PENGADAAN</div>
      <div style="display: inline-block; border: 1px dashed #999; padding: 15px; font-size: 9px; color: #999; margin-bottom: 10px;">METERAI<br>Rp 10.000</div><br>
      <div style="font-weight: bold; text-decoration: underline;"><?php echo e(strtoupper(optional($contract->creator)->name)); ?></div>
      <div style="font-size: 11px;">Pejabat Pembuat Komitmen</div>
      <?php if($contract->signed_by_admin_at): ?>
      <div style="font-size: 10px; color: #555; margin-top: 5px;">Ditandatangani digital: <?php echo e($contract->signed_by_admin_at->format('d/m/Y H:i')); ?></div>
      <?php endif; ?>
    </td>
  </tr>
</table>

<?php if($contract->document_hash): ?>
<div style="margin-top: 40px; padding: 10px; background-color: #f8fafc; border-left: 3px solid #1a237e; font-size: 10px; color: #555;">
  <strong>Sertifikasi Dokumen Elektronik:</strong><br>
  Dokumen ini telah dienkripsi. Hash SHA-256: <code><?php echo e($contract->document_hash); ?></code>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('pdf.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\project-laravel\lelang-2.0\resources\views/pdf/contract.blade.php ENDPATH**/ ?>