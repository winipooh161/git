<div class="card border-0 rounded-4 shadow-sm h-100 certificate-card"
    data-certificate-id="<?php echo e($certificate->id); ?>"
    data-public-url="<?php echo e(route('certificates.public', $certificate->uuid)); ?>"
    data-certificate-number="<?php echo e($certificate->certificate_number); ?>">
    <!-- Используем загруженную обложку в качестве главного изображения карточки -->
    <div class="certificate-cover-wrapper">
        <img src="<?php echo e($certificate->cover_image_url); ?>" class="certificate-cover-image" alt="Обложка документа">
        <div class="certificate-status-badge">
            <?php if($certificate->status == 'active'): ?>
                <span class="badge bg-success">Активен</span>
            <?php elseif($certificate->status == 'used'): ?>
                <span class="badge bg-secondary">Использован</span>
            <?php elseif($certificate->status == 'expired'): ?>
                <span class="badge bg-warning">Истек</span>
            <?php elseif($certificate->status == 'canceled'): ?>
                <span class="badge bg-danger">Отменен</span>
            <?php endif; ?>
        </div>
        
        <!-- Добавляем отметку времени -->
       
           
       
        
   
    </div>
 
    <!-- Действия с документом -->
    <div class="certificate-actions">
        <a href="<?php echo e(route('certificates.public', $certificate->uuid)); ?>" class="btn btn-primary btn-sm" target="_blank">
            <i class="fa-solid fa-external-link-alt me-1" style="margin:0 !important"></i>
        </a>
     
      
    </div>
</div>

<?php /**PATH C:\OSPanel\domains\sert\resources\views/user/certificates/partials/_certificate_card.blade.php ENDPATH**/ ?>