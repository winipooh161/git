<div class="card border-0 shadow-sm rounded-4 h-100 preview-card">
    <div class="card-header bg-transparent border-0 pt-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
        <div class="d-flex align-items-center mb-2 mb-sm-0">
            <h5 class="fw-bold mb-0 me-2 fs-6">Предпросмотр</h5>
        </div>
        <div class="device-toggle btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-secondary active" data-device="desktop">
                <i class="fa-solid fa-desktop"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-device="tablet">
                <i class="fa-solid fa-tablet-alt"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-device="mobile">
                <i class="fa-solid fa-mobile-alt"></i>
            </button>
        </div>
    </div>

    <div class="card-body p-2 p-sm-3">
        <div class="alert alert-info mb-2 mb-sm-3 py-2 small">
            <i class="fa-solid fa-info-circle me-1"></i>
            Заполните форму, чтобы увидеть изменения в документе
        </div>
        <div class="certificate-preview-container" data-current-device="desktop">
            <div class="certificate-preview-wrapper device-frame">
                <iframe id="certificatePreview" src="<?php echo e(route('template.preview', $template)); ?>" class="certificate-preview" frameborder="0" loading="lazy"></iframe>
            </div>
        </div>
    </div>
    
    <div class="card-footer bg-transparent border-0 pb-3 text-center">
        <div class="btn-toolbar justify-content-center">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-primary" id="zoomInButton">
                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                </button>
                <button type="button" class="btn btn-sm btn-primary" id="zoomOutButton">
                    <i class="fa-solid fa-magnifying-glass-minus"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="resetZoomButton">
                    <i class="fa-solid fa-arrows-to-circle"></i>
                </button>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="rotateViewButton">
                    <i class="fa-solid fa-rotate"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Стили для предпросмотра документа */
.certificate-preview-container {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
    overflow: hidden;
}

.certificate-preview-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

iframe.certificate-preview {
    width: 100%;
    height: 100%;
    border: 0;
    border-radius: 0.5rem;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    transform-origin: center center;
    transition: transform 0.3s ease;
}

/* Адаптивные стили для контейнера предпросмотра */
@media (min-width: 992px) {
    .certificate-preview-container {
        padding-top: 75%; /* 4:3 для десктопа */
        height: calc(100vh - 250px);
        min-height: 500px;
        max-height: 800px;
    }
}

@media (max-width: 991.98px) {
    .certificate-preview-container {
        padding-top: 75%; /* 4:3 для планшетов */
    }
}

@media (max-width: 767.98px) {
    .certificate-preview-container {
        padding-top: 100%; /* 1:1 для мобильных */
    }
}

/* Стили для разных устройств отображения */
.certificate-preview-container[data-current-device="desktop"] .device-frame {
    width: 100%;
    height: 100%;
}

.certificate-preview-container[data-current-device="tablet"] .device-frame {
    width: 75%;
    height: 90%;
    max-width: 768px;
    margin: 0 auto;
    border: 10px solid #e0e0e0;
    border-radius: 15px;
}

.certificate-preview-container[data-current-device="mobile"] .device-frame {
    width: 40%;
    height: 90%;
    max-width: 375px;
    margin: 0 auto;
    border: 8px solid #222;
    border-radius: 25px;
}

/* Стили для ландшафтной ориентации */
.certificate-preview-container.landscape[data-current-device="tablet"] .device-frame {
    width: 90%;
    height: 75%;
    max-height: 512px;
}

.certificate-preview-container.landscape[data-current-device="mobile"] .device-frame {
    width: 75%;
    height: 40%;
    max-height: 375px;
}

/* Адаптивная высота для предпросмотра на малых экранах */
@media (max-width: 767.98px) {
    .certificate-preview-container[data-current-device="tablet"] .device-frame {
        width: 90%;
    }
    
    .certificate-preview-container[data-current-device="mobile"] .device-frame {
        width: 60%;
    }
}
</style>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/certificate_preview.blade.php ENDPATH**/ ?>