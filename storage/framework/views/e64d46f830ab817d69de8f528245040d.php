<?php $__env->startSection('content'); ?>
<div class="container py-4">
 
          <?php echo $__env->make('profile.partials._profile', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      
   
    
    <!-- Вывод информационного сообщения, если заканчиваются стики -->
    <?php if(Auth::user()->sticks < 3): ?>
    <div class="alert alert-warning mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle fs-4 me-2"></i>
            <div>
                <strong>Внимание!</strong> У вас осталось мало стиков (<?php echo e(Auth::user()->sticks); ?>). 
                <a href="<?php echo e(route('subscription.plans')); ?>" class="alert-link">Обновите ваш тарифный план</a> для получения дополнительных стиков.
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Статистика -->
    
    
    
    <?php echo $__env->make('entrepreneur.certificates.partials-index._search_form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    
    <?php echo $__env->make('entrepreneur.certificates.partials-index._search_results', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('entrepreneur.certificates.partials-index._loading_indicator', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    
    <?php echo $__env->make('entrepreneur.certificates.partials-index._certificates_grid', ['certificates' => $certificates], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php if(isset($certificates) && $certificates->hasPages()): ?>
        <div class="mt-4 d-flex justify-content-center pagination" id="pagination-container">
            <?php echo e($certificates->withQueryString()->links()); ?>

        </div>
    <?php endif; ?>
</div>


<?php echo $__env->make('entrepreneur.certificates.partials-index._copy_toast', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php echo $__env->make('entrepreneur.certificates.partials-index._styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('entrepreneur.certificates.partials-index._scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/index.blade.php ENDPATH**/ ?>