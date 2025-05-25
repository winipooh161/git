<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['title' => null, 'header' => null, 'footer' => null, 'hover' => false, 'class' => null]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['title' => null, 'header' => null, 'footer' => null, 'hover' => false, 'class' => null]); ?>
<?php foreach (array_filter((['title' => null, 'header' => null, 'footer' => null, 'hover' => false, 'class' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="card border-0 rounded-4 shadow-sm <?php echo e($class ?? ''); ?> <?php echo e(isset($hover) && $hover ? 'hover-lift' : ''); ?> mb-4">
    <?php if(isset($header)): ?>
        <div class="card-header bg-transparent pt-4">
            <?php echo $header; ?>

        </div>
    <?php elseif(isset($title)): ?>
        <div class="card-header bg-transparent pt-4">
            <h5 class="mb-0"><?php echo e($title); ?></h5>
        </div>
    <?php endif; ?>
    
    <div class="card-body <?php echo e(isset($title) || isset($header) ? '' : 'pt-4'); ?>">
        <?php echo e($slot); ?>

    </div>
    
    <?php if(isset($footer)): ?>
        <div class="card-footer bg-transparent border-0 pb-3">
            <?php echo e($footer); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/components/card.blade.php ENDPATH**/ ?>