<?php $__env->startSection('title', 'Audit Log'); ?>
<?php $__env->startSection('page-title', 'Audit Log & Compliance'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full space-y-6">

    
    <form method="GET" action="<?php echo e(route('admin.audit-logs.index')); ?>"
          class="rounded-xl bg-white shadow-sm border border-gray-200 p-5">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Modul</label>
                <select name="module"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none">
                    <option value="">Semua Modul</option>
                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($mod); ?>" <?php echo e(request('module') === $mod ? 'selected' : ''); ?>><?php echo e(ucfirst($mod)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Aksi</label>
                <input type="text" name="action" value="<?php echo e(request('action')); ?>"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none"
                       placeholder="Cari aksi...">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                       class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#3553A8] focus:ring-1 focus:ring-[#3553A8] outline-none">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="rounded-md bg-[#3553A8] px-4 py-2 text-sm font-bold text-white hover:bg-[#2B438A] transition-colors">
                    Filter
                </button>
                <a href="<?php echo e(route('admin.audit-logs.index')); ?>"
                   class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                    Reset
                </a>
            </div>
        </div>
    </form>

    
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">Total: <strong><?php echo e($logs->total()); ?></strong> log</p>
        <a href="<?php echo e(route('admin.reports.export.audit-logs', request()->query())); ?>"
           class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
            </svg>
            Export CSV
        </a>
    </div>

    
    <div class="rounded-xl bg-white shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" style="min-width: 900px;">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs font-bold uppercase tracking-widest text-gray-500 bg-gray-50">
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">Modul</th>
                        <th class="px-5 py-3">Aksi</th>
                        <th class="px-5 py-3">Deskripsi</th>
                        <th class="px-5 py-3">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 whitespace-nowrap">
                            <span class="font-mono text-xs text-gray-600"><?php echo e($log->performed_at->format('d/m/Y H:i:s')); ?></span>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap font-medium text-gray-800">
                            <?php echo e($log->user?->name ?? 'System'); ?>

                        </td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-bold
                                <?php switch($log->module):
                                    case ('auth'): ?> bg-blue-100 text-blue-700 <?php break; ?>
                                    <?php case ('vendor'): ?> bg-purple-100 text-purple-700 <?php break; ?>
                                    <?php case ('tender'): ?> bg-indigo-100 text-indigo-700 <?php break; ?>
                                    <?php case ('evaluation'): ?> bg-teal-100 text-teal-700 <?php break; ?>
                                    <?php case ('report'): ?> bg-amber-100 text-amber-700 <?php break; ?>
                                    <?php default: ?> bg-gray-100 text-gray-700
                                <?php endswitch; ?>
                            "><?php echo e(ucfirst($log->module)); ?></span>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-gray-600 font-mono text-xs">
                            <?php echo e($log->action); ?>

                        </td>
                        <td class="px-5 py-3 text-gray-600 max-w-xs">
                            <p class="truncate" title="<?php echo e($log->description); ?>"><?php echo e($log->description); ?></p>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap font-mono text-xs text-gray-400">
                            <?php echo e($log->ip_address ?? '-'); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                            <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                            Belum ada log aktivitas.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php echo e($logs->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\project-laravel\lelang-2.0\resources\views/admin/audit-logs/index.blade.php ENDPATH**/ ?>