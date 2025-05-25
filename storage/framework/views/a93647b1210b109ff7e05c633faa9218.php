<!-- Основной контейнер для документов -->
<div id="certificates-container" class="grid-container">
    <!-- Всегда отображаем сетку с классом grid-visual независимо от наличия карточек -->
    <div class="row row-cols-3 row-cols-sm-3 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 card-grid certificate-row grid-visual">
        <!-- Карточка-кнопка создания нового документа - всегда видна -->
        <div class="col certificate-item h-100 grid-cell">
            <a href="<?php echo e(route('entrepreneur.certificates.select-template')); ?>" class="card-link">
                <div class="card border-0 shadow-sm h-100 certificate-card create-certificate-card">
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4">
                        <div class="create-icon-wrapper mb-3">
                            <i class="fa-solid fa-plus fa-3x"></i>
                        </div>
                        <h5 class="mb-0 text-center">Создать документ</h5>
                    </div>
                </div>
            </a>
        </div>

        <!-- Отображаем существующие карточки, если они есть -->
        <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col certificate-item h-100 grid-cell" data-recipient-name="<?php echo e($certificate->recipient_name); ?>"
                data-recipient-phone="<?php echo e($certificate->recipient_phone); ?>"
                data-certificate-number="<?php echo e($certificate->certificate_number); ?>">
                <div class="card border-0 shadow-sm h-100 certificate-card"
                    data-certificate-id="<?php echo e($certificate->id); ?>"
                    data-public-url="<?php echo e(route('certificates.public', $certificate->uuid)); ?>"
                    data-certificate-number="<?php echo e($certificate->certificate_number); ?>">
                    <a href="<?php echo e(route('certificates.public', $certificate->uuid)); ?>" class="card-link"
                        target="_blank">
                        <div class="certificate-cover-wrapper">
                            <img src="<?php echo e($certificate->cover_image_url); ?>" class="certificate-cover-image"
                                alt="Обложка документа">
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
                        </div>

                        <!-- Действия с документом -->
                        <div class="certificate-actions" onclick="event.stopPropagation();">
                            <button type="button" class="btn btn-outline-primary btn-sm copy-link-btn"
                                onclick="copyPublicUrl('<?php echo e(route('certificates.public', $certificate->uuid)); ?>', '<?php echo e($certificate->certificate_number); ?>')">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                            <a href="<?php echo e(route('entrepreneur.certificates.edit', $certificate)); ?>"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                        </div>
                    </a>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
        <!-- Добавляем пустые ячейки для поддержания структуры сетки -->
        <?php
            // Определяем количество столбцов в зависимости от размера экрана
            // (используем значение по умолчанию 3 для мобильной версии)
            $totalColumns = 3;
            
            // Вычисляем сколько пустых ячеек нужно добавить для заполнения сетки
            $existingCells = count($certificates) > 0 ? count($certificates) + 1 : 1; // +1 для карточки "создать"
            $emptyNeeded = max(15, $totalColumns - ($existingCells % $totalColumns));
            if ($emptyNeeded == $totalColumns) $emptyNeeded = 0;
            
            // Добавляем минимум 15 ячеек если нет сертификатов
            if (count($certificates) == 0) $emptyNeeded = 15;
        ?>
        
        <?php for($i = 0; $i < $emptyNeeded; $i++): ?>
        <div class="col certificate-item h-100 grid-cell">
            <div class="card border-0 shadow-sm h-100 certificate-card empty-card">
                <div class="certificate-cover-wrapper bg-light">
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                    </div>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
</div> <!-- Конец основного контейнера для документов -->
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/_certificates_grid.blade.php ENDPATH**/ ?>