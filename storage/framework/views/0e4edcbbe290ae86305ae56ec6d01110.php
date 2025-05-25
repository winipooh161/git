<div class="quiz-step active" id="quizStep4">
    <div class="text-center mb-4">
        <div class="quiz-step-icon mb-3 d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 p-3">
            <i class="fa-solid fa-image text-primary fs-3"></i>
        </div>
        <h3 class="quiz-step-title">Обложка документа</h3>
        <p class="text-muted">Создайте изображение для карточки документа</p>
    </div>
    
    <div class="mb-4">
        <div class="cover-upload-container">
            <!-- Если есть временное изображение из фоторедактора, добавляем скрытое поле -->
            <?php if(session('temp_certificate_cover')): ?>
                <input type="hidden" name="temp_cover_path" value="<?php echo e(session('temp_certificate_cover')); ?>">
            <?php endif; ?>
            
            <div class="mb-3">
                <label class="form-label fw-medium">Изображение обложки:</label>
                
                <?php if(session('temp_certificate_cover')): ?>
                <!-- Показать предпросмотр загруженного изображения -->
                <div id="cover_upload_status" class="mt-2">
                    <div class="alert alert-success py-2">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-check-circle me-2"></i>
                            <div>
                                <div>Изображение успешно создано в редакторе</div>
                                <div class="small text-muted">Файл уже сохранен на сервере</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <img src="<?php echo e(Storage::url(session('temp_certificate_cover'))); ?>" style="max-height: 150px; border-radius: 4px;" class="img-thumbnail">
                    </div>
                    <div class="mt-2">
                        <a href="<?php echo e(route('photo.editor')); ?>?template=<?php echo e(request()->route('template')->id); ?>" class="btn btn-outline-primary">
                            <i class="fa-solid fa-pencil me-2"></i>Изменить изображение
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <!-- Показать кнопку для перехода в фоторедактор -->
                <div class="text-center p-4 border rounded">
                    <div class="mb-3">
                        <i class="fa-solid fa-image fa-3x text-muted"></i>
                    </div>
                    <p>Для создания изображения воспользуйтесь встроенным редактором</p>
                    <div class="form-text mb-3">Рекомендуемый размер: 1200 x 630 пикселей. Максимальный размер файла: 20MB.</div>
                    <a href="<?php echo e(route('photo.editor')); ?>?template=<?php echo e(request()->route('template')->id); ?>" class="btn btn-primary">
                        <i class="fa-solid fa-camera me-2"></i>Открыть фоторедактор
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <?php $__errorArgs = ['temp_cover_path'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback d-block mt-2 text-center"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
    
    <div class="mt-4">
        <h6 class="fw-medium mb-3">Логотип на документе:</h6>
        <div class="logo-options">
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="logo_type" id="logo_default" value="default" checked>
                <label class="form-check-label d-flex align-items-center" for="logo_default">
                    <span class="me-2">Использовать из профиля</span>
                    <div class="small-logo-preview border rounded p-1">
                        <img src="<?php echo e(Auth::user()->company_logo ? asset('storage/' . Auth::user()->company_logo) : asset('images/default-logo.png')); ?>" 
                             style="max-height: 24px; max-width: 80px;" alt="Логотип">
                    </div>
                </label>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="logo_type" id="logo_custom" value="custom">
                <label class="form-check-label" for="logo_custom">
                    Загрузить новый логотип
                </label>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="radio" name="logo_type" id="logo_none" value="none">
                <label class="form-check-label" for="logo_none">
                    Без логотипа
                </label>
            </div>
            
            <!-- Контейнер для загрузки пользовательского логотипа (скрыт по умолчанию) -->
            <div id="custom_logo_container" class="mt-3 d-none">
                <div class="logo-upload-container">
                    <!-- Используем hidden вместо position-absolute -->
                    <input type="file" class="form-control form-control-lg <?php $__errorArgs = ['custom_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                        id="custom_logo" name="custom_logo" accept="image/*" data-file-uploaded="false"
                        style="display: none;">
                    
                    <label for="custom_logo" class="logo-upload-label">
                        <div id="logo_upload_placeholder" class="d-flex flex-column align-items-center justify-content-center p-3 border rounded">
                            <i class="fa-solid fa-cloud-arrow-up mb-2 text-primary"></i>
                            <span class="text-center small">Загрузить логотип</span>
                        </div>
                        
                        <div id="logo_preview_container" class="d-none">
                            <img src="#" id="logo_preview_image" class="img-fluid rounded w-100" alt="Предпросмотр логотипа">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" id="remove_logo">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </label>
                    
                    <?php $__errorArgs = ['custom_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback d-block mt-2 text-center"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cover-upload-container {
    position: relative;
    width: 100%;
}

.cover-upload-label {
    display: block;
    position: relative;
    width: 100%;
    min-height: 180px;
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
}

.cover-upload-label:hover {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.05);
}

#cover_preview_container {
    width: 100%;
    height: 100%;
    position: relative;
}

#cover_preview_image {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.logo-upload-label {
    display: block;
    position: relative;
    width: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
}

#logo_preview_container {
    width: 100%;
    height: 100%;
    position: relative;
}

#logo_preview_image {
    max-height: 60px;
    width: auto;
    object-fit: contain;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Глобальная переменная для отслеживания статуса обложки
    window.coverImageUploaded = false;
    
    // Проверяем, есть ли временное изображение из фоторедактора
    <?php if(session('temp_certificate_cover')): ?>
        // Устанавливаем флаг, что обложка загружена
        window.coverImageUploaded = true;
        
        // Обновляем информацию в итоговом шаге
        const summaryCover = document.getElementById('summary_cover');
        if (summaryCover) {
            summaryCover.innerHTML = `<span class="badge bg-success">Загружена из редактора</span>`;
        }
        
        // Синхронизируем данные с десктопной формой
        const desktopForm = document.getElementById('desktopCertificateForm');
        if (desktopForm) {
            // Проверяем, есть ли уже скрытое поле с путем к обложке
            let tempCoverInput = desktopForm.querySelector('input[name="temp_cover_path"]');
            if (!tempCoverInput) {
                // Если нет, создаем его
                tempCoverInput = document.createElement('input');
                tempCoverInput.type = 'hidden';
                tempCoverInput.name = 'temp_cover_path';
                desktopForm.appendChild(tempCoverInput);
            }
            // Устанавливаем значение
            tempCoverInput.value = "<?php echo e(session('temp_certificate_cover')); ?>";
        }
    <?php endif; ?>
    
    // Функция для проверки статуса обложки (вызывается при проверке шагов)
    window.checkCoverStatus = function() {
        // Если обложка уже загружена, возвращаем true
        if (window.coverImageUploaded) {
            return true;
        }
        
        // Проверяем наличие временной обложки
        const tempCoverPath = document.querySelector('input[name="temp_cover_path"]');
        return tempCoverPath && tempCoverPath.value ? true : false;
    };
    
    // Переключение типа логотипа
    const logoTypeRadios = document.querySelectorAll('input[name="logo_type"]');
    const customLogoContainer = document.getElementById('custom_logo_container');
    
    logoTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'custom') {
                if (customLogoContainer) {
                    customLogoContainer.classList.remove('d-none');
                    // Прокручиваем к контейнеру логотипа для лучшего UX
                    customLogoContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                if (customLogoContainer) customLogoContainer.classList.add('d-none');
            }
            
            // Вибрация для обратной связи
            if (window.safeVibrate) {
                window.safeVibrate(30);
            }
        });
    });
    
    // Обработчик для загрузки логотипа
    const customLogoInput = document.getElementById('custom_logo');
    const logoPreviewContainer = document.getElementById('logo_preview_container');
    const logoPlaceholder = document.getElementById('logo_upload_placeholder');
    const logoPreviewImage = document.getElementById('logo_preview_image');
    
    if (customLogoInput && logoPreviewContainer && logoPreviewImage && logoPlaceholder) {
        customLogoInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Проверка размера файла (до 5 МБ)
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    alert('Размер файла не должен превышать 5 МБ');
                    this.value = '';
                    return;
                }
                
                // Проверка типа файла
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Пожалуйста, выберите изображение (JPEG, PNG, GIF, WebP)');
                    this.value = '';
                    return;
                }
                
                // Показываем предпросмотр
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoPreviewImage.src = e.target.result;
                    logoPreviewContainer.classList.remove('d-none');
                    logoPlaceholder.classList.add('d-none');
                    
                    // Устанавливаем атрибут, указывающий, что файл загружен
                    customLogoInput.setAttribute('data-file-uploaded', 'true');
                    
                    // Отображаем кнопку удаления загруженного файла
                    const removeBtn = document.getElementById('remove_logo');
                    if (removeBtn) {
                        removeBtn.classList.remove('d-none');
                    }
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Добавляем обработчик для кнопки удаления логотипа
        const removeLogoBtn = document.getElementById('remove_logo');
        if (removeLogoBtn) {
            removeLogoBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Очищаем поле загрузки
                customLogoInput.value = '';
                customLogoInput.removeAttribute('data-file-uploaded');
                
                // Скрываем предпросмотр и показываем плейсхолдер
                logoPreviewContainer.classList.add('d-none');
                logoPlaceholder.classList.remove('d-none');
                
                // Скрываем кнопку удаления
                removeLogoBtn.classList.add('d-none');
            });
        }
    }
});
</script>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/quiz_steps/step4_cover.blade.php ENDPATH**/ ?>