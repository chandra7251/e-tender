<?php $__env->startSection('title', 'Kontrak Digital'); ?>
<?php $__env->startSection('page-title', 'Manajemen Kontrak Digital'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="flex flex-wrap gap-3">
        <?php $__currentLoopData = [
            ''             => 'Semua',
            'draft'        => 'Draft',
            'sent_to_vendor' => 'Dikirim ke Vendor',
            'signed_vendor'  => 'TTD Vendor',
            'signed_admin'   => 'TTD Admin',
            'active'         => 'Aktif',
            'completed'      => 'Selesai',
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(request()->fullUrlWithQuery(['status' => $val])); ?>"
           class="rounded-full px-4 py-1.5 text-xs font-semibold border transition
                   <?php echo e(request('status', '') === $val ? 'bg-[#3553A8] text-white border-[#3553A8]' : 'bg-white text-gray-600 border-gray-300 hover:border-[#3553A8]'); ?>">
             <?php echo e($label); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#3553A8] text-white">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">No. Kontrak</th>
                    <th class="px-4 py-3 text-left">Tender</th>
                    <th class="px-4 py-3 text-left">Vendor</th>
                    <th class="px-4 py-3 text-left">Nilai Kontrak</th>
                    <th class="px-4 py-3 text-left">Periode</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $statusColor = match($c->status) {
                    'draft'          => 'bg-gray-100 text-gray-600',
                    'sent_to_vendor' => 'bg-blue-100 text-blue-700',
                    'signed_vendor'  => 'bg-yellow-100 text-yellow-700',
                    'signed_admin'   => 'bg-indigo-100 text-indigo-700',
                    'active'         => 'bg-green-100 text-green-700',
                    'completed'      => 'bg-emerald-100 text-emerald-700',
                    'terminated'     => 'bg-red-100 text-red-700',
                    default          => 'bg-gray-100 text-gray-600',
                };
                $statusLabel = match($c->status) {
                    'draft'          => 'Draft',
                    'sent_to_vendor' => 'Dikirim',
                    'signed_vendor'  => 'TTD Vendor',
                    'signed_admin'   => 'TTD Admin',
                    'active'         => 'Aktif',
                    'completed'      => 'Selesai',
                    'terminated'     => 'Diputus',
                    default          => ucfirst($c->status),
                };
            ?>
            <tr class="border-t border-gray-100 hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-500"><?php echo e($loop->iteration); ?></td>
                <td class="px-4 py-3 font-mono text-xs text-gray-800 font-semibold"><?php echo e($c->contract_number); ?></td>
                <td class="px-4 py-3 text-gray-700 max-w-[160px] truncate"><?php echo e($c->tender->title ?? '-'); ?></td>
                <td class="px-4 py-3 text-gray-700"><?php echo e(optional($c->vendor->user)->name ?? '-'); ?></td>
                <td class="px-4 py-3 font-semibold text-gray-800">Rp <?php echo e(number_format($c->contract_value, 0, ',', '.')); ?></td>
                <td class="px-4 py-3 text-xs text-gray-500">
                    <?php echo e(optional($c->start_date)->format('d/m/Y')); ?> &ndash; <?php echo e(optional($c->end_date)->format('d/m/Y')); ?>

                </td>
                <td class="px-4 py-3">
                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold <?php echo e($statusColor); ?>"><?php echo e($statusLabel); ?></span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="<?php echo e(route('admin.contracts.show', $c->id)); ?>"
                           class="rounded bg-[#3553A8] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#2B438A] transition">
                            Detail
                        </a>
                        <?php if($c->status === 'draft'): ?>
                        <form method="POST" action="<?php echo e(route('admin.contracts.send', $c->id)); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="rounded bg-[#F09459] px-3 py-1.5 text-xs font-semibold text-white hover:bg-orange-600 transition">
                                Kirim ke Vendor
                            </button>
                        </form>
                        <?php elseif($c->status === 'signed_vendor'): ?>
                        <form method="POST" action="<?php echo e(route('admin.contracts.sign', $c->id)); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="rounded bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700 transition">
                                TTD Admin
                            </button>
                        </form>
                        <?php elseif($c->status === 'active'): ?>
                        <form method="POST" action="<?php echo e(route('admin.contracts.complete', $c->id)); ?>"
                              onsubmit="return confirm('Tandai kontrak ini sebagai SELESAI?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="rounded bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 transition">
                                Selesaikan
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm">Belum ada kontrak digital.</p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">
            <?php echo e($contracts->withQueryString()->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\project-laravel\lelang-2.0\resources\views/admin/contracts/index.blade.php ENDPATH**/ ?>