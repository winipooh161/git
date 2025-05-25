<!-- Секция с документом -->
<div class="certificate-section">
    <div class="certificate-container">
        <?php if(isset($certificate->template) && $certificate->template->template_path): ?>
            <!-- Добавляем информацию о серии сертификатов, если это серия -->
            <?php if($certificate->is_batch_parent && $certificate->batch_size > 1): ?>
                <div class="series-badge">
                    <span class="series-badge-text">
                        Серия: <?php echo e($certificate->activated_copies_count); ?>/<?php echo e($certificate->batch_size); ?>

                    </span>
                </div>
            <?php endif; ?>
            
            <iframe id="certificateIframe" src="<?php echo e(route('template.preview', $certificate->template)); ?>?editable=false" 
                frameborder="0" width="100%" style="min-height: 800px;"
                data-certificate="<?php echo e(isset($certificateData) ? base64_encode(json_encode($certificateData)) : base64_encode(json_encode($data ?? []))); ?>">
            </iframe>
        <?php else: ?>
            <div class="alert alert-danger">
                Шаблон для этого сертификата не найден
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- QR-код для предпринимателя -->
<div id="qrFullscreenOverlay" class="qr-fullscreen-overlay">
    <div class="qr-fullscreen-container">
        <div class="qr-close-btn">&times;</div>
        <h3>Для предпринимателя</h3>
        
        <!-- Оставляем только QR-код для предпринимателя -->
        <?php if(isset($certificate->user_id)): ?>
        <div class="qr-code-container">
            <?php echo QrCode::size(250)->generate(route('entrepreneur.certificates.status-change', ['uuid' => $certificate->uuid])); ?>

        </div>
        <p class="entrepreneur-note">Отсканируйте для быстрого изменения статуса сертификата</p>
        <?php endif; ?>
    </div>
</div>

<div id="adminQrToggle" class="admin-qr-toggle">
    <i class="fa-solid fa-qrcode"></i>
</div>

<style>
/* Стиль для бейджа с информацией о серии */
.series-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 10;
    background-color: rgba(59, 130, 246, 0.9);
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.series-badge-text {
    display: flex;
    align-items: center;
}

.series-badge-text:before {
    content: '';
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: white;
    border-radius: 50%;
    margin-right: 6px;
}
</style>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/certificates/partials/certificate-section.blade.php ENDPATH**/ ?>