<?php $__env->startSection('title', 'Tender Management'); ?>
<?php $__env->startSection('page-title', 'Tender Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <p class="text-sm font-bold text-gray-600"><?php echo e($tenders->total()); ?> Tender ditemukan</p>
        <a href="<?php echo e(route('admin.tenders.create')); ?>"
           class="inline-flex items-center gap-2 rounded-md bg-[#28C5D4] px-4 py-2.5 text-sm font-bold
                  text-white hover:bg-teal-400 transition-colors duration-150 self-start sm:self-auto">
            <svg class="h-4 w-4 stroke-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Buat Tender
        </a>
    </div>

    <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">

        <form method="GET" action="<?php echo e(route('admin.tenders.index')); ?>"
              class="flex flex-col gap-3 sm:flex-row sm:items-center mb-6">

            <select name="status"
                    class="rounded-md border-0 bg-white px-4 py-2.5 text-sm font-medium
                           text-gray-700 outline-none focus:ring-2 focus:ring-[#2B438A]">
                <option value="">Semua Status</option>
                <?php $__currentLoopData = ['draft','open','aanwijzing','bidding','closed','finished']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php echo e(request('status') === $s ? 'selected' : ''); ?>>
                        <?php echo e(ucfirst($s)); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <button type="submit"
                    class="rounded-md bg-[#2B438A] px-6 py-2.5 text-sm font-semibold text-white
                           hover:bg-[#1E3066] transition-colors duration-150">
                Filter
            </button>

            <?php if(request('search') || request('status')): ?>
                <a href="<?php echo e(route('admin.tenders.index')); ?>"
                   class="rounded-md border border-[#4A6BCC] px-4 py-2.5 text-sm text-indigo-200
                          hover:text-white transition-colors duration-150">
                    Reset
                </a>
            <?php endif; ?>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-white" style="min-width: 680px;">
                <thead>
                    <tr class="border-b border-[#4A6BCC] text-left text-xs font-bold uppercase tracking-wider text-indigo-100">
                        <th class="px-2 py-4">Perusahaan</th>
                        <th class="px-2 py-4 text-center">Status</th>
                        <th class="px-2 py-4">Mulai</th>
                        <th class="px-2 py-4">Selesai</th>
                        <th class="px-2 py-4">Bidding Start</th>
                        <th class="px-2 py-4">Bidding End</th>
                        <th class="px-2 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#4A6BCC]">
                    <?php $__empty_1 = true; $__currentLoopData = $tenders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-[#2B438A] transition-colors duration-150">
                            <td class="px-2 py-4 font-semibold tracking-wide">
                                <?php echo e($tender->title); ?>

                            </td>
                            <td class="px-2 py-4 text-center">
                                <?php
                                    $badge = match($tender->status) {
                                        'open'       => 'bg-[#28C5D4] text-white',
                                        'aanwijzing' => 'bg-violet-500 text-white',
                                        'bidding'    => 'bg-[#F09459] text-white',
                                        'closed'     => 'bg-gray-500 text-white',
                                        'finished'   => 'bg-[#34D399] text-white',
                                        default      => 'bg-[#788B9A] text-white', // draft
                                    };
                                ?>
                                <span class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-bold w-20 <?php echo e($badge); ?>">
                                    <?php echo e(ucfirst($tender->status)); ?>

                                </span>
                            </td>
                            <td class="px-2 py-4 text-indigo-50 whitespace-nowrap">
                                <?php echo e($tender->start_date?->format('d M Y') ?? '-'); ?>

                            </td>
                            <td class="px-2 py-4 text-indigo-50 whitespace-nowrap">
                                <?php echo e($tender->end_date?->format('d M Y') ?? '-'); ?>

                            </td>
                            <td class="px-2 py-4 text-indigo-50 whitespace-nowrap">
                                <?php echo e($tender->bidding_start?->format('d M Y') ?? '-'); ?>

                            </td>
                            <td class="px-2 py-4 text-indigo-50 whitespace-nowrap">
                                <?php echo e($tender->bidding_end?->format('d M Y') ?? '-'); ?>

                            </td>
                            <td class="px-2 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="<?php echo e(route('admin.tenders.show', $tender)); ?>"
                                       class="rounded bg-[#2B438A] border border-[#4A6BCC] px-3 py-1.5 text-[11px] font-semibold text-white
                                              hover:bg-[#1E3066] transition-colors duration-150">
                                        Detail
                                    </a>
                                    <a href="<?php echo e(route('admin.tenders.edit', $tender)); ?>"
                                       class="rounded bg-[#2B438A] border border-[#4A6BCC] px-3 py-1.5 text-[11px] font-semibold text-white
                                              hover:bg-[#1E3066] transition-colors duration-150">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-2 py-10 text-center text-sm text-indigo-200">
                                Tidak ada tender ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if($tenders->hasPages()): ?>
        <div class="mt-4">
            <?php echo e($tenders->links()); ?>

        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\project-laravel\lelang-2.0\resources\views/admin/tenders/index.blade.php ENDPATH**/ ?>