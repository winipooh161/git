<div class="folder-system ">
    <div class="d-flex align-items-center ">
        <button type="button" class="btn btn-sm btn-outline-primary  flex-shrink-0" data-bs-toggle="modal" data-bs-target="#createFolderModal">
            <i class="fa-solid fa-folder-plus me-1" style="margin:0 !important;"></i> 
        </button>
        
        <div class="folder-navigation overflow-hidden">
            <div class="btn-group d-flex flex-nowrap" style="min-width: max-content;">
                <a href="<?php echo e(route('user.certificates.index')); ?>" class="btn btn-sm <?php echo e(!request('folder') ? '' : 'btn-outline-secondary'); ?>">
                    <i class="fa-solid fa-certificate me-1"></i>Все
                </a>
                
                <?php $__empty_1 = true; $__currentLoopData = $folders ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('user.certificates.index', ['folder' => $folder->id])); ?>"  style="border:none;"
                    class="btn btn-sm <?php echo e(request('folder') == $folder->id ? 'btn-primary' : 'btn-outline-secondary'); ?> folder-btn"
                    data-folder-id="<?php echo e($folder->id); ?>" data-folder-name="<?php echo e($folder->name); ?>"
                    data-folder-color="<?php echo e($folder->color); ?>">
                    <i class="fa-solid fa-folder me-1 text-<?php echo e($folder->color); ?>"></i><?php echo e($folder->name); ?>

                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <span class="btn btn-sm btn-outline-secondary disabled" style="border:none;">
                    <i class="fa-solid fa-folder-open me-1"></i>Нет папок
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/user/certificates/partials/_folder_system.blade.php ENDPATH**/ ?>