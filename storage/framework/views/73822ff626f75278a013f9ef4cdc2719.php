<?php
    $status = $certificate->status;
    $statusLabel = $certificate->status_label;
?>

<?php if($status === 'series'): ?>
    <span class="badge bg-primary">
        <i class="fa-solid fa-layer-group me-1"></i> Серия (<?php echo e($certificate->activated_copies_count); ?>/<?php echo e($certificate->batch_size); ?>)
    </span>
<?php elseif($status === 'active'): ?>
    <span class="badge bg-success">
        <i class="fa-solid fa-check-circle me-1"></i> <?php echo e($statusLabel); ?>

    </span>
<?php elseif($status === 'used'): ?>
    <span class="badge bg-secondary">
        <i class="fa-solid fa-check me-1"></i> <?php echo e($statusLabel); ?>

    </span>
<?php elseif($status === 'expired'): ?>
    <span class="badge bg-warning">
        <i class="fa-solid fa-hourglass-end me-1"></i> <?php echo e($statusLabel); ?>

    </span>
<?php elseif($status === 'canceled'): ?>
    <span class="badge bg-danger">
        <i class="fa-solid fa-ban me-1"></i> <?php echo e($statusLabel); ?>

    </span>
<?php elseif($status === 'refunded'): ?>
    <span class="badge bg-info">
        <i class="fa-solid fa-undo me-1"></i> <?php echo e($statusLabel); ?>

    </span>
<?php elseif($status === 'on_hold'): ?>
    <span class="badge bg-secondary">
        <i class="fa-solid fa-pause me-1"></i> <?php echo e($statusLabel); ?>

    </span>
<?php else: ?>
    <span class="badge bg-light text-dark">
        <?php echo e($statusLabel); ?>

    </span>
<?php endif; ?>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/_status_badge.blade.php ENDPATH**/ ?>