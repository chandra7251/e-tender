<?php $__env->startSection('title', 'Ranking Evaluasi'); ?>
<?php $__env->startSection('page-title', 'Ranking Evaluasi Tender'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">

    <a href="<?php echo e(route('admin.tenders.show', $tender)); ?>"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors font-medium">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
        </svg>
        Kembali ke Detail Tender
    </a>

    <div class="rounded-xl bg-[#3553A8] px-5 py-4 shadow-sm">
        <p class="text-xs text-indigo-200">Tender</p>
        <p class="mt-0.5 text-base font-bold text-white"><?php echo e($tender->title); ?></p>
    </div>

    <?php if(!$isFullyEvaluated): ?>
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-700 flex items-center gap-2">
            <svg class="h-5 w-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/>
            </svg>
            <span><strong>Perhatian:</strong> Belum semua bid dievaluasi. Ranking mungkin belum akurat.</span>
            <a href="<?php echo e(route('admin.tenders.evaluations.create', $tender)); ?>"
               class="ml-auto text-xs font-bold text-yellow-800 underline hover:no-underline">Evaluasi Sekarang →</a>
        </div>
    <?php endif; ?>

    
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200 px-5 py-3 bg-gray-50">
            <h2 class="text-sm font-bold text-gray-800">Kriteria Evaluasi</h2>
        </div>
        <div class="p-5 flex flex-wrap gap-3">
            <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-lg border border-gray-200 px-4 py-3 min-w-[150px]">
                    <p class="text-xs font-bold text-gray-800"><?php echo e($c->name); ?></p>
                    <p class="text-lg font-bold text-[#3553A8] mt-0.5"><?php echo e(number_format($c->weight, 0)); ?>%</p>
                    <p class="text-[10px] text-gray-400">Maks skor: <?php echo e($c->max_score); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200 px-5 py-3 bg-gray-50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-800">Hasil Ranking</h2>
            <?php if($tender->status === 'closed' && !$tender->hasWinner()): ?>
                <a href="<?php echo e(route('admin.tenders.winner.create', $tender)); ?>"
                   class="rounded-md bg-[#28C5D4] px-4 py-2 text-xs font-bold text-white hover:bg-teal-400 transition-colors">
                    Pilih Pemenang →
                </a>
            <?php endif; ?>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" style="min-width: 800px;">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs font-bold uppercase tracking-widest text-gray-500 bg-gray-50">
                        <th class="px-5 py-3">Rank</th>
                        <th class="px-5 py-3">Vendor</th>
                        <th class="px-5 py-3 text-right">Bid Amount</th>
                        <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="px-5 py-3 text-center"><?php echo e($c->name); ?><br><span class="font-normal">(<?php echo e(number_format($c->weight, 0)); ?>%)</span></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <th class="px-5 py-3 text-center">Total Skor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $rankedBids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $bid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 transition-colors <?php echo e($i === 0 ? 'bg-emerald-50' : ''); ?>">
                        <td class="px-5 py-3 whitespace-nowrap">
                            <?php if($i === 0): ?>
                                <span class="rounded-full bg-[#28C5D4] px-2.5 py-0.5 text-xs font-bold text-white">★ #1</span>
                            <?php else: ?>
                                <span class="text-gray-500 font-semibold">#<?php echo e($i + 1); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 font-semibold text-gray-800 whitespace-nowrap">
                            <?php echo e($bid->vendor->company_name ?? '-'); ?>

                        </td>
                        <td class="px-5 py-3 text-right font-mono font-bold whitespace-nowrap <?php echo e($i === 0 ? 'text-[#28C5D4]' : 'text-gray-700'); ?>">
                            Rp <?php echo e(number_format($bid->bid_amount, 0, ',', '.')); ?>

                        </td>
                        <?php $__currentLoopData = $bid->evaluation_details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="px-5 py-3 text-center">
                            <span class="font-mono font-bold text-gray-800"><?php echo e(number_format($detail['raw_score'], 1)); ?></span>
                            <span class="text-gray-400 text-xs">/<?php echo e($detail['max_score']); ?></span>
                            <br>
                            <span class="text-xs text-[#3553A8] font-semibold"><?php echo e(number_format($detail['weighted_score'], 2)); ?></span>
                        </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="px-5 py-3 text-center">
                            <?php if($bid->total_weighted_score !== null): ?>
                                <span class="text-lg font-bold <?php echo e($i === 0 ? 'text-[#28C5D4]' : 'text-gray-800'); ?>">
                                    <?php echo e(number_format($bid->total_weighted_score, 2)); ?>

                                </span>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\project-laravel\lelang-2.0\resources\views/admin/evaluations/ranking.blade.php ENDPATH**/ ?>