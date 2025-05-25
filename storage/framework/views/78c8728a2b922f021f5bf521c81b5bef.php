<?php $__env->startSection('content'); ?>
<div class="certificate-editor">
    <div class="editor-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between py-2 py-sm-3">
                <h1 class="h4 h5-sm fw-bold mb-0">Создание документа</h1>
                <a href="<?php echo e(route('entrepreneur.certificates.select-template')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1 me-sm-2"></i><span class="d-none d-sm-inline">Вернуться к шаблонам</span>
                </a>
            </div>
        </div>
    </div>
    
    <div class="editor-body">
        <div class="container-fluid">
            <div class="row">
                <!-- Визуальный предпросмотр документа - на десктопе слева -->
                <div class="col-lg-7 col-xl-8 mb-3 d-none d-lg-block">
                    <?php echo $__env->make('entrepreneur.certificates.partials.certificate_preview', ['template' => $template], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                
                <!-- Форма редактирования документа в формате квиза -->
                <div class="col-lg-5 col-xl-4">
                    <?php echo $__env->make('entrepreneur.certificates.partials.certificate_quiz', ['template' => $template], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                    <!-- Кнопка для показа/скрытия предпросмотра на мобильных -->
                    <div class="d-lg-none mt-3 text-center">
                        <button id="togglePreviewBtn" class="btn btn-outline-primary">
                            <i class="fa-solid fa-eye me-2"></i>Показать предпросмотр
                        </button>
                    </div>
                </div>
                
                <!-- Визуальный предпросмотр документа - на мобильных внизу (скрыт по умолчанию) -->
                <div class="col-12 d-lg-none mt-3 preview-mobile-container d-none">
                    <?php echo $__env->make('entrepreneur.certificates.partials.certificate_preview', ['template' => $template], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно выбора анимационных эффектов -->
<?php echo $__env->make('entrepreneur.certificates.partials.animation_effects_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- Стили для страницы -->
<?php $__env->startPush('styles'); ?>
    <?php echo $__env->make('entrepreneur.certificates.partials.certificate_styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopPush(); ?>

<!-- Скрипты для страницы -->
<?php $__env->startPush('scripts'); ?>
    <?php echo $__env->make('entrepreneur.certificates.partials.certificate_scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopPush(); ?>

<!-- Добавляем скрипт для обработки единой формы квиза и переключения предпросмотра -->
<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Безопасная функция для вибрации устройства
    window.safeVibrate = function(pattern) {
        if (navigator.vibrate && typeof navigator.vibrate === 'function') {
            try {
                navigator.vibrate(pattern);
            } catch (e) {
                console.log('Vibration API not supported or permission denied');
            }
        }
    };

    // Обработчик для отслеживания изменений в предпросмотре
    const previewFrame = document.getElementById('certificatePreview');
    if (previewFrame) {
        previewFrame.addEventListener('load', function() {
            console.log('Предпросмотр документа загружен');
            // Скрываем индикатор загрузки
            const container = this.closest('.certificate-preview-container');
            if (container) {
                container.parentElement.classList.add('certificate-preview-loaded');
            }
        });
    }
    
    // Переключение видимости предпросмотра на мобильных устройствах
    const togglePreviewBtn = document.getElementById('togglePreviewBtn');
    const previewMobileContainer = document.querySelector('.preview-mobile-container');
    
    if (togglePreviewBtn && previewMobileContainer) {
        togglePreviewBtn.addEventListener('click', function() {
            const isHidden = previewMobileContainer.classList.contains('d-none');
            
            if (isHidden) {
                // Показываем предпросмотр
                previewMobileContainer.classList.remove('d-none');
                togglePreviewBtn.innerHTML = '<i class="fa-solid fa-eye-slash me-2"></i>Скрыть предпросмотр';
                
                // Плавная прокрутка к предпросмотру
                setTimeout(() => {
                    previewMobileContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
                
                // Обновляем предпросмотр
                if (window.updatePreview) {
                    window.updatePreview();
                }
                
                // Добавляем класс для анимации появления
                previewMobileContainer.classList.add('preview-fade-in');
            } else {
                // Скрываем предпросмотр с анимацией
                previewMobileContainer.classList.add('preview-fade-out');
                
                // После завершения анимации скрываем элемент
                setTimeout(() => {
                    previewMobileContainer.classList.remove('preview-fade-out');
                    previewMobileContainer.classList.add('d-none');
                    previewMobileContainer.classList.remove('preview-fade-in');
                    togglePreviewBtn.innerHTML = '<i class="fa-solid fa-eye me-2"></i>Показать предпросмотр';
                    
                    // Прокручиваем к кнопке
                    togglePreviewBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
            
            // Вибрация для тактильной обратной связи
            if (window.safeVibrate) {
                window.safeVibrate(30);
            }
        });
    }
    
    // Глобальная проверка обложки перед отправкой формы
    document.querySelector('#mobileCertificateForm')?.addEventListener('submit', function(e) {
        // Проверяем, есть ли обложка документа
        const hasTempCoverPath = document.querySelector('input[name="temp_cover_path"]')?.value;
        const hasCoverFile = document.querySelector('input[name="cover_image"]')?.files?.length > 0;
        
        if (!hasTempCoverPath && !hasCoverFile && !window.coverImageUploaded) {
            e.preventDefault();
            
            // Показываем модальное окно с сообщением об ошибке
            const alertHTML = `
            <div class="modal fade" id="coverRequiredModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Требуется обложка документа</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="text-center mb-3">
                      <i class="fa-solid fa-image fa-3x text-danger mb-3"></i>
                      <p>Пожалуйста, создайте обложку документа с помощью фоторедактора.</p>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <a href="<?php echo e(route('photo.editor')); ?>?template=<?php echo e($template->id); ?>" class="btn btn-primary">
                      <i class="fa-solid fa-camera me-2"></i>Открыть фоторедактор
                    </a>
                  </div>
                </div>
              </div>
            </div>`;
            
            // Добавляем модальное окно в DOM
            const modalContainer = document.createElement('div');
            modalContainer.innerHTML = alertHTML;
            document.body.appendChild(modalContainer.firstChild);
            
            // Показываем модальное окно
            const modal = new bootstrap.Modal(document.getElementById('coverRequiredModal'));
            modal.show();
            
            return false;
        }
    });

    // Исправление для iframe и ошибки "companyLogoElements.includes is not a function"
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'logo_elements_check') {
            // Отправляем безопасную версию обработчика обратно в iframe
            try {
                const iframe = document.getElementById('certificatePreview');
                if (iframe && iframe.contentWindow === event.source) {
                    iframe.contentWindow.postMessage({
                        type: 'logo_elements_fix',
                        message: 'Используйте Array.from для NodeList'
                    }, '*');
                }
            } catch (error) {
                console.error('Ошибка при обработке сообщения от iframe:', error);
            }
        } else if (event.data && event.data.type === 'iframe_ready') {
            // Iframe загружен и исправления применены
            console.log('Iframe ready:', event.data.message);
            
            // Обновляем предпросмотр, так как iframe полностью готов
            if (window.updatePreview && typeof window.updatePreview === 'function') {
                window.updatePreview();
            }
        }
    });
    
    // Запускаем обновление предпросмотра при загрузке страницы
    window.addEventListener('load', function() {
        setTimeout(() => {
            if (window.updatePreview && typeof window.updatePreview === 'function') {
                console.log('Обновление предпросмотра при загрузке страницы...');
                window.updatePreview();
            }
        }, 500);
    });
});
</script>
<?php $__env->stopPush(); ?>

<!-- Добавляем стили для анимации появления/скрытия предпросмотра на мобильных -->
<?php $__env->startPush('styles'); ?>
<style>
.preview-fade-in {
    animation: fadeInPreview 0.3s ease-in forwards;
}

.preview-fade-out {
    animation: fadeOutPreview 0.3s ease-out forwards;
}

@keyframes fadeInPreview {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeOutPreview {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(20px); }
}

/* Улучшенные стили для мобильного предпросмотра */
@media (max-width: 991px) {
    .preview-mobile-container .certificate-preview-container {
        min-height: 300px;
        padding-top: 66.67%; /* 2:3 соотношение для мобильных */
    }
    
    .preview-mobile-container .card-header {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    
    .preview-mobile-container .card-body {
        padding: 0.5rem;
    }
    
    .preview-mobile-container .card-footer {
        padding-top: 0.5rem;
        padding-bottom: 1rem;
    }
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/create.blade.php ENDPATH**/ ?>