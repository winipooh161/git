<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo e(route('entrepreneur.dashboard')); ?>">Панель управления</a></li>
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!$loop->last): ?>
                <li class="breadcrumb-item"><a href="<?php echo e($item['url']); ?>"><?php echo e($item['title']); ?></a></li>
            <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($item['title']); ?></li>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ol>
</nav>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/components/breadcrumb.blade.php ENDPATH**/ ?>