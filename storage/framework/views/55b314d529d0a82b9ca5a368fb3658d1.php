<div id="certificates-container" class="grid-container">
    <!-- Всегда отображаем сетку с классом grid-visual независимо от наличия карточек -->
    <div class="row row-cols-3 row-cols-sm-3 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 card-grid certificate-row grid-visual">
        <!-- Карточка-кнопка создания нового документа - всегда видна -->
     

        <!-- Отображаем существующие карточки, если они есть -->
        <?php $__currentLoopData = $certificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certificate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col certificate-item h-100 grid-cell" data-recipient-name="<?php echo e($certificate->recipient_name); ?>"
                data-recipient-phone="<?php echo e($certificate->recipient_phone); ?>"
                data-certificate-number="<?php echo e($certificate->certificate_number); ?>">
                <div class="card border-0 shadow-sm h-100 certificate-card <?php echo e($certificate->status === 'series' ? 'series-card' : ''); ?>"
                    data-certificate-id="<?php echo e($certificate->id); ?>"
                    data-public-url="<?php echo e(route('certificates.public', $certificate->uuid)); ?>"
                    data-certificate-number="<?php echo e($certificate->certificate_number); ?>"
                    data-is-batch-parent="<?php echo e($certificate->is_batch_parent ? 'true' : 'false'); ?>"
                    data-batch-size="<?php echo e($certificate->batch_size); ?>"
                    data-activated-copies="<?php echo e($certificate->activated_copies_count); ?>">
                    <a href="<?php echo e(route('certificates.public', $certificate->uuid)); ?>" class="card-link"
                        target="_blank">
                        <div class="certificate-cover-wrapper">
                            <img src="<?php echo e($certificate->cover_image_url); ?>" class="certificate-cover-image"
                                alt="Обложка документа">
                            <div class="certificate-status-badge">
                                <?php if($certificate->status == 'series'): ?>
                                    <!-- Специальный бейдж для серий с индикатором активации -->
                                    <span class="badge bg-primary">
                                        <i class="fa-solid fa-layer-group me-1"></i>
                                        Серия (<?php echo e($certificate->activated_copies_count); ?>/<?php echo e($certificate->batch_size); ?>)
                                    </span>
                                <?php elseif($certificate->status == 'active'): ?>
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
                            <?php if($certificate->status == 'series'): ?>
                            <button type="button" 
                               class="btn btn-outline-success btn-sm show-qr-btn" 
                               title="QR код серии" 
                               data-bs-toggle="modal" 
                               data-bs-target="#qrModal"
                               data-certificate-uuid="<?php echo e($certificate->uuid); ?>"
                               data-certificate-number="<?php echo e($certificate->certificate_number); ?>"
                               onclick="event.preventDefault(); event.stopPropagation();">
                                <i class="fa-solid fa-qrcode"></i>
                            </button>
                            <?php endif; ?>
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
</div>

<style>
/* Стили для карточек сертификатов-серий */
.series-card {
    border-left: 3px solid #0d6efd !important;
}

.series-progress-info {
    padding: 10px;
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
    border-radius: 0 0 5px 5px;
}
</style>

<!-- Модальное окно для отображения QR-кода -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalLabel">QR-код сертификата</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body text-center">
        <div id="qrCodeContainer" class="d-flex justify-content-center mb-3">
          <!-- QR-код будет добавлен сюда через JavaScript -->
        </div>
        <p id="certificateInfo" class="mb-3"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
        <a href="#" id="downloadQrBtn" class="btn btn-primary">Скачать QR-код</a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик для открытия модального окна с QR-кодом
    const qrModal = document.getElementById('qrModal');
    if (qrModal) {
        qrModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const uuid = button.getAttribute('data-certificate-uuid');
            const certificateNumber = button.getAttribute('data-certificate-number');
            
            // Получаем публичный URL для сертификата
            const publicUrl = `${window.location.origin}/certificates/${uuid}`;
            
            // Очищаем и обновляем контейнер QR-кода
            const qrContainer = document.getElementById('qrCodeContainer');
            qrContainer.innerHTML = '';
            
            // Отображаем информацию о сертификате
            document.getElementById('certificateInfo').textContent = 
                `Сертификат: ${certificateNumber}`;
            
            // Генерируем QR-код с помощью внешней библиотеки (QRious или аналогичной)
            // Предполагается, что эта библиотека уже подключена к странице
            generateQRCode(publicUrl, qrContainer);
            
            // Настраиваем кнопку скачивания
            const downloadBtn = document.getElementById('downloadQrBtn');
            downloadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                downloadQRCode(publicUrl, certificateNumber);
            });
        });
    }
});

// Функция для генерации QR-кода
function generateQRCode(url, container) {
    // Если используется библиотека QRious
    if (typeof QRious !== 'undefined') {
        const qr = new QRious({
            element: document.createElement('canvas'),
            value: url,
            size: 250
        });
        container.appendChild(qr.element);
    } else {
        // Запасной вариант - использование API для генерации QR
        const img = document.createElement('img');
        img.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(url)}`;
        img.alt = "QR-код сертификата";
        img.style.maxWidth = "100%";
        container.appendChild(img);
    }
}

// Функция для скачивания QR-кода
function downloadQRCode(url, certificateNumber) {
    // Создаем временный canvas для генерации изображения для скачивания
    const canvas = document.createElement('canvas');
    const qr = new QRious({
        element: canvas,
        value: url,
        size: 500 // Больший размер для скачивания
    });
    
    // Преобразуем canvas в URL для скачивания
    const downloadLink = document.createElement('a');
    downloadLink.href = canvas.toDataURL('image/png');
    downloadLink.download = `QR-${certificateNumber}.png`;
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials-index/_certificates_grid.blade.php ENDPATH**/ ?>