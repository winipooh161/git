<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
    <div>
        <h1 class="h2 mb-1"><?php echo e($title); ?></h1>
        <?php if(isset($subtitle)): ?>
            <p class="text-muted"><?php echo e($subtitle); ?></p>
        <?php endif; ?>
    </div>
    
    <?php if(isset($actions)): ?>
        <div class="d-flex mt-3 mt-md-0">
            <?php echo e($actions); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/components/page-header.blade.php ENDPATH**/ ?>