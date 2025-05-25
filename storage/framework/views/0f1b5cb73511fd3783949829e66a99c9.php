<div class="date-group-heading mb-2 mt-4">
    <h2 class="fs-6 fw-bold text-muted">
        <?php
            $carbonDate = \Carbon\Carbon::parse($date);
            $today = \Carbon\Carbon::today();
            $yesterday = \Carbon\Carbon::yesterday();
        ?>
        
        <?php if($carbonDate->isSameDay($today)): ?>
            Сегодня
        <?php elseif($carbonDate->isSameDay($yesterday)): ?>
            Вчера
        <?php else: ?>
            <?php echo e($carbonDate->format('d.m.Y')); ?>

        <?php endif; ?>
    </h2>
</div>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/user/certificates/partials/_date_heading.blade.php ENDPATH**/ ?>