<?php $__env->startSection('title', $tender->title); ?>
<?php $__env->startSection('page-title', 'Detail Tender'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <a href="<?php echo e(route('admin.tenders.index')); ?>"
           class="inline-flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-gray-900 transition-colors">
            <svg class="h-4 w-4 stroke-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
            Kembali Ke Daftar Tender
        </a>
        <div class="flex flex-wrap items-center gap-3">

            <a href="<?php echo e(route('admin.tenders.participants.index', $tender)); ?>"
               class="inline-flex items-center gap-2 rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-2 text-sm font-bold text-white
                      hover:bg-[#1E3066] transition-colors duration-150">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                </svg>
                Peserta <span class="rounded bg-[#5578D0] px-2 py-0.5 text-xs"><?php echo e($tender->participants->count()); ?></span>
            </a>

            <a href="<?php echo e(route('admin.tenders.bids.index', $tender)); ?>"
               class="inline-flex items-center gap-2 rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-2 text-sm font-bold text-white
                      hover:bg-[#1E3066] transition-colors duration-150">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                </svg>
                Bid <span class="rounded bg-[#5578D0] px-2 py-0.5 text-xs"><?php echo e($tender->bids->count()); ?></span>
            </a>

            <?php if($tender->result): ?>
                <a href="<?php echo e(route('admin.tenders.result.show', $tender)); ?>"
                   class="inline-flex items-center gap-2 rounded-md bg-white border border-[#3553A8] px-4 py-2 text-sm font-bold text-[#3553A8]
                          hover:bg-indigo-50 transition-colors duration-150">
                    ★ Hasil
                </a>
            <?php elseif($tender->status === 'closed'): ?>
                <?php if($tender->bids->count() > 0): ?>
                    <a href="<?php echo e(route('admin.tenders.winner.create', $tender)); ?>"
                       class="inline-flex items-center gap-2 rounded-md bg-white border border-[#3553A8] px-4 py-2 text-sm font-bold text-[#3553A8]
                              hover:bg-indigo-50 transition-colors duration-150">
                        Pilih Winner
                    </a>
                <?php else: ?>
                    <button type="button" disabled
                       class="inline-flex items-center gap-2 rounded-md bg-gray-200 border border-gray-300 px-4 py-2 text-sm font-bold text-gray-500 cursor-not-allowed"
                       title="Tender belum memiliki bid, tidak bisa pilih pemenang">
                        Pilih Winner (Tidak ada Bid)
                    </button>
                <?php endif; ?>
            <?php endif; ?>

            
            <a href="<?php echo e(route('admin.tenders.evaluation-criteria.create', $tender)); ?>"
               class="inline-flex items-center gap-2 rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-2 text-sm font-bold text-white
                      hover:bg-[#1E3066] transition-colors duration-150"
               title="Kelola Kriteria Evaluasi">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>
                </svg>
                Kriteria
            </a>

            <?php if(in_array($tender->status, ['closed', 'finished'])): ?>
                <a href="<?php echo e(route('admin.tenders.evaluations.create', $tender)); ?>"
                   class="inline-flex items-center gap-2 rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-2 text-sm font-bold text-white
                          hover:bg-[#1E3066] transition-colors duration-150"
                   title="Evaluasi Bid">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                    </svg>
                    Evaluasi
                </a>

                <a href="<?php echo e(route('admin.tenders.ranking', $tender)); ?>"
                   class="inline-flex items-center gap-2 rounded-md bg-[#28C5D4] border border-teal-400 px-4 py-2 text-sm font-bold text-white
                          hover:bg-teal-400 transition-colors duration-150"
                   title="Lihat Ranking">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                    Ranking
                </a>

                
                <?php if($tender->evaluation_method === 'two_envelope'): ?>
                <a href="<?php echo e(route('admin.tenders.envelope.technical', $tender)); ?>"
                   class="inline-flex items-center gap-2 rounded-md bg-purple-600 border border-purple-500 px-4 py-2 text-sm font-bold text-white
                          hover:bg-purple-700 transition-colors duration-150"
                   title="Evaluasi Teknis (Amplop 1)">
                    📦 Amplop 1
                </a>
                <a href="<?php echo e(route('admin.tenders.envelope.price', $tender)); ?>"
                   class="inline-flex items-center gap-2 rounded-md bg-emerald-600 border border-emerald-500 px-4 py-2 text-sm font-bold text-white
                          hover:bg-emerald-700 transition-colors duration-150"
                   title="Evaluasi Harga (Amplop 2)">
                    💰 Amplop 2
                </a>
                <a href="<?php echo e(route('admin.tenders.envelope.ranking', $tender)); ?>"
                   class="inline-flex items-center gap-2 rounded-md bg-amber-500 border border-amber-400 px-4 py-2 text-sm font-bold text-white
                          hover:bg-amber-600 transition-colors duration-150"
                   title="Ranking Gabungan 2 Amplop">
                    🏆 Ranking 2 Amplop
                </a>
                <?php endif; ?>
            <?php endif; ?>

            <a href="<?php echo e(route('admin.tenders.histories.index', $tender)); ?>"
               class="inline-flex items-center gap-2 rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-2 text-sm font-bold text-white
                      hover:bg-[#1E3066] transition-colors duration-150">
                History
            </a>
            <a href="<?php echo e(route('admin.tenders.edit', $tender)); ?>"
               class="inline-flex items-center gap-2 rounded-md bg-[#2B438A] border border-[#4A6BCC] px-4 py-2 text-sm font-bold text-white
                      hover:bg-[#1E3066] transition-colors duration-150">
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <div class="lg:col-span-2 space-y-6">

            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <div class="mb-6 flex items-start justify-between">
                    <h2 class="text-xl font-bold text-white"><?php echo e($tender->title); ?></h2>
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
                    <span class="inline-flex items-center justify-center rounded-full px-4 py-1.5 text-xs font-bold <?php echo e($badge); ?>">
                        <?php echo e(ucfirst($tender->status)); ?>

                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-200 mb-1">Deskripsi</p>
                        <p class="text-sm text-white"><?php echo e($tender->description); ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-200 mb-1">Spesifikasi</p>
                        <p class="text-sm text-white whitespace-pre-line"><?php echo e($tender->specification); ?></p>
                    </div>
                    <?php if($tender->open_bidding_price): ?>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-200 mb-1">Harga Pembukaan Bidding (HPS)</p>
                        <p class="text-lg font-bold text-white">
                            Rp <?php echo e(number_format($tender->open_bidding_price, 0, ',', '.')); ?>

                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if($tender->photos->isNotEmpty()): ?>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-indigo-200 mb-3">Foto Barang / Jasa</p>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                            <?php $__currentLoopData = $tender->photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="relative group overflow-hidden rounded-xl border border-[#4A6BCC] aspect-square">
                                    <a href="<?php echo e($photo->photo_url); ?>" target="_blank" rel="noopener noreferrer" class="block h-full w-full">
                                        <img src="<?php echo e($photo->photo_url); ?>"
                                             alt="Foto tender <?php echo e($tender->title); ?> - <?php echo e($i+1); ?>"
                                             class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                             onerror="this.closest('div').innerHTML='<p class=\'text-xs text-indigo-300 italic p-3\'>Foto tidak dapat ditampilkan.</p>'">
                                    </a>

                                    <div class="absolute inset-0 pointer-events-none flex items-end justify-center p-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-gradient-to-t from-black/60 to-transparent">
                                        <a href="<?php echo e($photo->photo_url); ?>" download="foto-tender-<?php echo e($tender->id); ?>-<?php echo e($i+1); ?>.jpg"
                                           class="pointer-events-auto inline-flex items-center gap-1.5 rounded-md bg-white/90 px-3 py-1.5 text-xs font-bold text-[#3553A8] hover:bg-white transition-colors duration-150 shadow-sm"
                                           onclick="event.stopPropagation();">
                                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="pt-4 border-t border-[#4A6BCC] mt-6">
                        <p class="text-xs font-medium text-indigo-200">Dibuat oleh: <?php echo e($tender->creator->name ?? '-'); ?></p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white">Timeline</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                    <?php
                        $rows = [
                            ['label' => 'Mulai Tender',      'val' => $tender->start_date],
                            ['label' => 'Selesai Tender',    'val' => $tender->end_date],
                            ['label' => 'Tanggal Aanwijzing','val' => $tender->aanwijzing_date],
                            ['label' => 'Bidding Mulai',     'val' => $tender->bidding_start],
                            ['label' => 'Bidding Selesai',   'val' => $tender->bidding_end],
                        ];
                    ?>
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="rounded-lg border border-[#4A6BCC] px-4 py-3">
                            <p class="text-xs text-indigo-200 mb-1"><?php echo e($row['label']); ?></p>
                            <p class="font-bold text-white">
                                <?php echo e($row['val'] ? $row['val']->format('d M Y, H:i') : '—'); ?>

                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-lg font-bold text-white flex items-center">
                    Aanwijzing / Pengumuman
                    <span class="ml-2 text-xs font-normal text-indigo-200">
                        (<?php echo e($tender->announcements->count()); ?>)
                    </span>
                </h2>

                <?php if($tender->announcements->isEmpty()): ?>
                    <p class="text-sm text-indigo-200">Belum ada pengumuman.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $tender->announcements->sortByDesc('published_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-lg border border-[#4A6BCC] px-5 py-4">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <p class="font-bold text-white text-base"><?php echo e($ann->title); ?></p>
                                    <span class="shrink-0 text-xs text-indigo-200">
                                        <?php echo e($ann->published_at?->format('d M Y, H:i') ?? '-'); ?>

                                    </span>
                                </div>
                                <p class="text-sm text-white mb-3"><?php echo e($ann->content); ?></p>
                                <p class="text-xs text-indigo-200">Oleh: <?php echo e($ann->creator->name ?? '-'); ?></p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <div class="space-y-6">

            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-4 text-base font-bold text-white">Ubah Status</h2>
                <form method="POST" action="<?php echo e(route('admin.tenders.status', $tender)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="mb-4">
                        <label for="status" class="mb-2 block text-sm font-medium text-indigo-200">Status Baru</label>
                        <?php if($tender->status === 'finished'): ?>

                            <div class="flex items-center gap-2 rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5">
                                <span class="inline-block h-2.5 w-2.5 rounded-full bg-purple-500"></span>
                                <span class="text-sm font-semibold text-white">Finished</span>
                                <span class="ml-auto text-xs text-indigo-300">Status final — diubah otomatis saat pilih pemenang</span>
                            </div>
                        <?php else: ?>
                        <select id="status" name="status"
                                class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                                       text-white outline-none focus:border-white focus:ring-1 focus:ring-white
                                       <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                            <?php $__currentLoopData = ['draft','open','aanwijzing','bidding','closed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s); ?>" <?php echo e($tender->status === $s ? 'selected' : ''); ?>>
                                    <?php echo e(ucfirst($s)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php endif; ?>
                        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-300"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-6">
                        <label for="description" class="mb-2 block text-sm font-medium text-indigo-200">
                            Catatan perubahan (opsional)
                        </label>
                        <input id="description" type="text" name="description"
                               placeholder="Alasan perubahan status..."
                               class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                                      text-white placeholder-indigo-200 outline-none
                                      focus:border-white focus:ring-1 focus:ring-white">
                    </div>
                    <button type="submit"
                            class="w-full rounded-md bg-[#28C5D4] px-4 py-2.5 text-sm font-bold text-white
                                   hover:bg-teal-400 transition-colors duration-150">
                        Ubah Status
                    </button>
                </form>
            </div>

            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-4 text-base font-bold text-white">Tambah Aanwijzing</h2>
                <form method="POST" action="<?php echo e(route('admin.tenders.announcements.store', $tender)); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4">
                        <label for="ann_title" class="mb-2 block text-sm font-bold text-white">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input id="ann_title" type="text" name="title" value="<?php echo e(old('title')); ?>"
                               placeholder="Judul Pengumuman..."
                               class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                                      text-white placeholder-indigo-200 outline-none
                                      focus:border-white focus:ring-1 focus:ring-white
                                      <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-300"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-4">
                        <label for="ann_content" class="mb-2 block text-sm font-bold text-white">
                            Isi <span class="text-red-500">*</span>
                        </label>
                        <textarea id="ann_content" name="content" rows="4"
                                  placeholder="Isi Pengumuman / aanwijzing..."
                                  class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                                         text-white placeholder-indigo-200 outline-none
                                         focus:border-white focus:ring-1 focus:ring-white
                                         <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('content')); ?></textarea>
                        <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-300"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="mb-6">
                        <label for="published_at" class="mb-2 block text-sm font-bold text-white">
                            Tanggal Publikasi <span class="text-red-500">*</span>
                        </label>
                        <input id="published_at" type="datetime-local" name="published_at"
                               value="<?php echo e(old('published_at')); ?>"
                               class="w-full rounded-md border border-[#4A6BCC] bg-[#2B438A] px-4 py-2.5 text-sm
                                      text-white outline-none [color-scheme:dark]
                                      focus:border-white focus:ring-1 focus:ring-white
                                      <?php $__errorArgs = ['published_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['published_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-300"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit"
                            class="w-full rounded-md bg-[#28C5D4] px-4 py-2.5 text-sm font-bold text-white
                                   hover:bg-teal-400 transition-colors duration-150">
                        + Tambah aanwijzing
                    </button>
                </form>
            </div>

            <?php if($tender->histories->count()): ?>
            <div class="rounded-xl bg-[#3553A8] p-6 shadow-sm">
                <h2 class="mb-6 text-base font-bold text-white">Riwayat Aktivitas</h2>
                <div class="space-y-4">
                    <?php $__currentLoopData = $tender->histories->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <p class="font-bold text-white text-sm mb-1"><?php echo e(str_replace('_', ' ', ucfirst($h->action))); ?></p>
                            <?php if($h->old_status && $h->new_status): ?>
                                <p class="text-xs text-indigo-200 mb-0.5">
                                    <?php echo e(ucfirst($h->old_status)); ?> → <?php echo e(ucfirst($h->new_status)); ?>

                                </p>
                            <?php endif; ?>
                            <?php if($h->description): ?>
                                <p class="text-xs text-indigo-200 mb-0.5"><?php echo e($h->description); ?></p>
                            <?php endif; ?>
                            <p class="text-[11px] text-indigo-200 opacity-80 mt-1">
                                <?php echo e($h->actor->name ?? '-'); ?> -
                                <?php echo e($h->created_at ? $h->created_at->format('d M Y, H:i') : '-'); ?>

                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\project-laravel\lelang-2.0\resources\views/admin/tenders/show.blade.php ENDPATH**/ ?>