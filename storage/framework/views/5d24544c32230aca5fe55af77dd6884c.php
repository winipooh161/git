

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3 py-md-4">
    <!-- Хлебные крошки - адаптивный вариант -->
    <nav aria-label="breadcrumb" class="mb-2 mb-md-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?php echo e(route('entrepreneur.certificates.index')); ?>">Мои документы</a></li>
            <?php if(isset($template)): ?>
                <li class="breadcrumb-item d-none d-md-inline">Создание документа</li>
                <li class="breadcrumb-item active" aria-current="page">Редактор изображения</li>
            <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">Фото редактор</li>
            <?php endif; ?>
        </ol>
    </nav>

    <h1 class="fs-4 fs-md-3 fw-bold mb-3 mb-md-4">
        <?php if(isset($template)): ?>
            Создание обложки для документа
        <?php else: ?>
            Фото редактор
        <?php endif; ?>
    </h1>

    <!-- Контент фото-редактора -->
    <div class="photo-editor-container">
        <!-- Здесь будет содержимое фото-редактора -->
    </div>
    
    <!-- Блок с кнопками навигации в режиме редактирования для сертификата -->
    <?php if(isset($template) && isset($redirectAfter) && $redirectAfter): ?>
    <div class="position-fixed bottom-0 start-0 w-100 p-3 bg-dark bg-opacity-75">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?php echo e(route('entrepreneur.certificates.select-template')); ?>" class="btn btn-outline-light">
                    <i class="fa-solid fa-arrow-left me-2"></i>Вернуться к выбору шаблона
                </a>
                <button type="button" class="btn btn-primary" onclick="showSaveDialog()">
                    <i class="fa-solid fa-save me-2"></i>Сохранить и продолжить
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Добавьте сюда скрипты и другие необходимые элементы для работы фото-редактора -->
<script>
// Функция для сохранения изображения непосредственно в сертификат
function saveToCertificate(templateId) {
    if (!templateId) {
        console.error('ID шаблона не указан');
        showErrorMessage('Ошибка сохранения: ID шаблона не указан');
        return;
    }
    
    // Получаем данные изображения из фоторедактора
    let imageData;
    try {
        imageData = photoEditor.getCanvasDataURL();
        console.log('Получены данные изображения длиной: ' + imageData.length);
    } catch (error) {
        console.error('Ошибка при получении данных изображения:', error);
        showErrorMessage('Не удалось получить данные изображения');
        return;
    }
    
    // Проверяем наличие данных изображения
    if (!imageData || imageData.length < 100) {
        console.error('Данные изображения отсутствуют или некорректны');
        showErrorMessage('Изображение не сформировано. Пожалуйста, создайте изображение');
        return;
    }
    
    // Отображаем индикатор загрузки
    showLoadingMessage('Сохранение изображения...');
    
    // Подготовка данных запроса
    const requestData = {
        image_data: imageData
    };
    
    // Подготовка заголовков с учетом передачи больших объемов данных
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };
    
    // Проверяем, доступен ли CSRF-токен (на случай, если мы его снова включим)
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }
    
    console.log('Отправляем запрос на сохранение изображения');
    
    // Отправляем запрос
    fetch(`/photo-save-to-certificate/${templateId}`, {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(requestData),
        // Добавляем настройки для больших данных
        keepalive: true,
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Получен ответ:', response.status, response.statusText);
        
        if (!response.ok) {
            return response.text().then(text => {
                try {
                    // Пытаемся разобрать JSON, если он есть
                    const errorData = JSON.parse(text);
                    throw new Error(`Ошибка HTTP: ${response.status} ${response.statusText}, сообщение: ${errorData.error || 'Неизвестная ошибка'}`);
                } catch (e) {
                    // Если не получилось разобрать JSON, возвращаем текст как есть
                    throw new Error(`Ошибка HTTP: ${response.status} ${response.statusText}\n${text}`);
                }
            });
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('Изображение успешно сохранено:', data);
            // Показываем сообщение об успешном сохранении
            showSuccessMessage('Изображение успешно сохранено');
            
            // Если есть URL для перенаправления - переходим по нему с небольшой задержкой
            if (data.redirect_url) {
                setTimeout(() => {
                    console.log('Перенаправляем на:', data.redirect_url);
                    window.location.href = data.redirect_url;
                }, 1000);
            }
        } else {
            throw new Error(data.error || 'Неизвестная ошибка при сохранении');
        }
    })
    .catch(error => {
        console.error('Ошибка сохранения изображения:', error);
        showErrorMessage('Ошибка сохранения: ' + error.message);
    });
}

// Функция отображения диалога сохранения
function showSaveDialog() {
    // Если это режим редактирования для сертификата
    <?php if(isset($template) && isset($redirectAfter) && $redirectAfter): ?>
    // Сохраняем непосредственно в сертификат
    saveToCertificate(<?php echo e($template->id); ?>);
    <?php else: ?>
    // Обычное сохранение с диалогом
    // ...existing code for usual saving...
    <?php endif; ?>
}
</script>

<!-- Добавьте мета-тег с CSRF-токеном -->
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/photo-editor.blade.php ENDPATH**/ ?>