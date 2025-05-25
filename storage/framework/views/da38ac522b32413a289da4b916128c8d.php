

<?php $__env->startSection('content'); ?>
<div class="certificate-iframe-editor">
    <div class="editor-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between py-2">
                <h1 class="h5 fw-bold mb-0">Создание документа</h1>
                <div>
                    <button id="createCertificateBtn" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-check me-1"></i> Создать документ
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="editor-body vh-100">
        <div class="certificate-iframe-container">
            <div class="loading-overlay" id="iframeLoading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <p class="mt-2">Загрузка редактора...</p>
            </div>
            
            <iframe id="certificateEditorIframe" src="<?php echo e(route('template.preview', ['template' => $template, 'editable' => true])); ?>" class="certificate-editor-iframe"></iframe>
            
            <!-- Форма для отправки данных, заполняется из iframe -->
            <form id="certificateDataForm" action="<?php echo e(route('entrepreneur.certificates.store', $template)); ?>" method="POST" enctype="multipart/form-data" style="display:none;">
                <?php echo csrf_field(); ?>
                <!-- Эти поля будут заполняться динамически из iframe -->
                <input type="hidden" name="recipient_name" id="recipient_name_hidden" value="Иванов Иван">
                <input type="hidden" name="recipient_phone" id="recipient_phone_hidden" value="+7 (999) 999-99-99">
                <input type="hidden" name="amount" id="amount_hidden">
                <input type="hidden" name="message" id="message_hidden">
                <input type="hidden" name="company_name" id="company_name_hidden">
                <input type="hidden" name="valid_from" id="valid_from_hidden">
                <input type="hidden" name="valid_until" id="valid_until_hidden">
                <input type="hidden" name="temp_cover_path" id="temp_cover_path_hidden">
                <input type="hidden" name="batch_size" id="batch_size_hidden" value="1"> <!-- Изменено имя ID для скрытого поля batch_size -->
            </form>
            
            <!-- Боковая панель с элементами управления (снизу) -->
            <div class="certificate-sidebar" id="certificateSidebar">
                <div class="sidebar-toggle" id="sidebarToggle">
                    <i class="fa-solid fa-chevron-up"></i>
                </div>
                
                <div class="sidebar-content">
                    <h5 class="sidebar-title">Настройки документа</h5>
                    
                    <!-- Раздел с настройками документа -->
                    <div class="sidebar-section">
                        <h6>Информация о получателе</h6>
                        <div class="mb-3">
                            <label for="recipient_name" class="form-label">Имя получателя</label>
                            <input type="text" class="form-control" id="recipient_name" placeholder="Введите имя получателя" value="Иванов Иван">
                        </div>
                    </div>
                    
                    <div class="sidebar-section">
                        <h6>Параметры документа</h6>
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Название организации</label>
                            <input type="text" class="form-control" id="company_name" placeholder="Название вашей компании">
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Номинал</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="amount" placeholder="Введите сумму">
                                <span class="input-group-text">₽</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="valid_from" class="form-label">Действителен с</label>
                            <input type="date" class="form-control" id="valid_from">
                        </div>
                        <div class="mb-3">
                            <label for="valid_until" class="form-label">Действителен до</label>
                            <input type="date" class="form-control" id="valid_until">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Текст сообщения</label>
                            <textarea class="form-control" id="message" rows="3" placeholder="Введите текст сообщения"></textarea>
                        </div>
                    </div>
                    
                    <div class="sidebar-section">
                        <h6>Дизайн</h6>
                        <div class="d-grid mb-3">
                            <a href="<?php echo e(route('photo.editor', ['template' => $template->id])); ?>" class="btn btn-outline-primary">
                                <i class="fa-solid fa-image me-2"></i>Создать обложку в фоторедакторе
                            </a>
                        </div>
                    </div>
                    
                    <!-- Добавляем поле для указания количества сертификатов -->
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Количество сертификатов в серии</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="batch_size" class="form-label">Укажите количество сертификатов для создания:</label>
                                    <div class="input-group mb-2">
                                        <input type="number" class="form-control" id="batch_size" name="batch_size" 
                                               min="1" max="<?php echo e(Auth::user()->sticks); ?>" value="1" required>
                                        <span class="input-group-text">шт.</span>
                                    </div>
                                    <div class="form-text">
                                        <i class="fa fa-info-circle me-1"></i>
                                        За каждый сертификат списывается 1 стик.
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="sticks-info p-3 bg-light rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">Доступно стиков:</span>
                                            <span class="badge bg-primary fs-6"><?php echo e(Auth::user()->sticks); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Будет списано:</span>
                                            <span class="badge bg-danger fs-6" id="sticksToUse">1</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3" id="batchWarning" style="display: none;">
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle me-2"></i>
                                    <span id="warningMessage"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="sidebar-actions">
                        <div class="d-grid">
                            <button type="button" id="sidebarCreateBtn" class="btn btn-primary">
                                <i class="fa-regular fa-floppy-disk me-2"></i>Создать документ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.certificate-iframe-editor {
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow: hidden;
}

.editor-header {
    background-color: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.5rem 0;
    z-index: 10;
}

.editor-body {
    flex: 1;
    position: relative;
    overflow: hidden;
}

.certificate-iframe-container {
    position: relative;
    height: 100%;
}

.certificate-editor-iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 5;
    transition: opacity 0.3s ease;
}

/* Стили для боковой панели, выезжающей снизу */
.certificate-sidebar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 80%; /* Высота панели */
    background: #fff;
    border-top: 1px solid #e5e7eb;
    transform: translateY(calc(100% - 40px)); /* Оставляем 40px для язычка */
    transition: transform 0.3s ease;
    z-index: 10;
    box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.1);
    border-radius: 12px 12px 0 0;
}

.certificate-sidebar.open {
    transform: translateY(0);
}

.sidebar-toggle {
    width: 100%;
    height: 40px;
    position: fixed;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: #fff;
    border-radius: 12px 12px 0 0;
    border-bottom: 1px solid #e5e7eb;
}

.sidebar-toggle i {
    transition: transform 0.3s ease;
}

.certificate-sidebar.open .sidebar-toggle i {
    transform: rotate(180deg);
}

.sidebar-content {
    padding: 50px 1.5rem 1.5rem 1.5rem; /* Верхний padding увеличен, чтобы не перекрываться с язычком */
    height: 100%;
    overflow-y: auto;
}

.sidebar-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}

.sidebar-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.sidebar-section h6 {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #4b5563;
}

.sidebar-actions {
    padding-top: 1rem;
}

/* Стили для облегчения восприятия полей формы внутри iframe */
#certificateEditorIframe {
    background-color: #f9fafb;
}

@media (max-width: 768px) {
    .certificate-sidebar {
        height: 90%;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('certificateEditorIframe');
    const loadingOverlay = document.getElementById('iframeLoading');
    const sidebar = document.getElementById('certificateSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const createCertificateBtn = document.getElementById('createCertificateBtn');
    const sidebarCreateBtn = document.getElementById('sidebarCreateBtn');
    const dataForm = document.getElementById('certificateDataForm');
    let iframeInitialized = false;
    
    // Функция форматирования даты в формате YYYY-MM-DD
    function formatDate(date) {
        const d = new Date(date);
        const month = '' + (d.getMonth() + 1);
        const day = '' + d.getDate();
        const year = d.getFullYear();
        
        return [year, month.padStart(2, '0'), day.padStart(2, '0')].join('-');
    }
    
    // Функция форматирования даты для отображения в формате DD.MM.YYYY
    function formatDateForDisplay(dateStr) {
        const d = new Date(dateStr);
        const month = '' + (d.getMonth() + 1);
        const day = '' + d.getDate();
        const year = d.getFullYear();
        
        return [day.padStart(2, '0'), month.padStart(2, '0'), year].join('.');
    }
    
    // Функция для получения значения из URL параметра
    function getUrlParam(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }
    
    // Функция для получения значения из cookie
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
        return null;
    }
    
    // Получаем путь к обложке из различных источников
    function getCoverPath() {
        let path = null;
        
        // Проверяем URL-параметр (приоритет выше)
        path = getUrlParam('cover');
        if (path) {
            console.log("Получен путь к обложке из URL:", path);
            return path;
        }
        
        // Проверяем sessionStorage
        path = sessionStorage.getItem('temp_certificate_cover');
        if (path) {
            console.log("Получен путь к обложке из sessionStorage:", path);
            return path;
        }
        
        // Проверяем localStorage
        path = localStorage.getItem('temp_certificate_cover_backup');
        if (path) {
            console.log("Получен путь к обложке из localStorage:", path);
            return path;
        }
        
        // Проверяем cookie
        path = getCookie('temp_certificate_cover');
        if (path) {
            console.log("Получен путь к обложке из cookie:", path);
            return path;
        }
        
        console.log("Путь к обложке не найден в локальных хранилищах");
        
        // Проверяем скрытое поле формы на случай, если оно уже было заполнено
        const hiddenField = document.getElementById('temp_cover_path_hidden');
        if (hiddenField && hiddenField.value) {
            console.log("Путь к обложке найден в скрытом поле формы:", hiddenField.value);
            return hiddenField.value;
        }
        
        return null;
    }
    
    // Функция получения значений из формы
    function getFormValues() {
        const amountValue = document.getElementById('amount').value;
        return {
            recipient_name: document.getElementById('recipient_name').value,
            company_name: document.getElementById('company_name').value,
            amount: amountValue ? parseInt(amountValue).toLocaleString('ru-RU') + ' ₽' : '10 000 ₽', // Задаем значение по умолчанию
            message: document.getElementById('message').value,
            valid_from: document.getElementById('valid_from').value ? 
                formatDateForDisplay(document.getElementById('valid_from').value) : '',
            valid_until: document.getElementById('valid_until').value ? 
                formatDateForDisplay(document.getElementById('valid_until').value) : ''
        };
    }
    
    // Устанавливаем значения дат по умолчанию
    document.getElementById('valid_from').value = formatDate(new Date());
    document.getElementById('valid_until').value = formatDate(new Date(Date.now() + 90 * 24 * 60 * 60 * 1000)); // +90 дней
    document.getElementById('company_name').value = "<?php echo e(Auth::user()->company_name ?? config('app.name')); ?>";
    
    // Обработчик события загрузки iframe
    iframe.addEventListener('load', function() {
        // Скрываем индикатор загрузки
        loadingOverlay.style.opacity = '0';
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
        }, 300);
        
        // По умолчанию частично показываем панель (как язычок)
        setTimeout(() => {
            iframeInitialized = true;
            
            // Получаем путь к обложке из всех возможных источников
            const coverPath = getCoverPath();
            console.log("Обнаружен путь к обложке:", coverPath);
            
            if (coverPath) {
                // Сохраняем путь во всех возможных хранилищах для надежности
                sessionStorage.setItem('temp_certificate_cover', coverPath);
                localStorage.setItem('temp_certificate_cover_backup', coverPath);
                document.cookie = `temp_certificate_cover=${encodeURIComponent(coverPath)}; path=/; max-age=3600`;
                
                // Устанавливаем значение в скрытое поле формы
                const hiddenField = document.getElementById('temp_cover_path_hidden');
                if (hiddenField) {
                    hiddenField.value = coverPath;
                    console.log('Путь к обложке установлен в скрытое поле:', hiddenField.value);
                } else {
                    console.error('Не найдено скрытое поле temp_cover_path_hidden!');
                }
                
                // Добавляем индикатор, что обложка уже создана
                const coverBtn = document.querySelector('a[href*="photo.editor"]');
                
                if (coverBtn) {
                    coverBtn.innerHTML = '<i class="fa-solid fa-check me-2"></i>Обложка создана';
                    coverBtn.classList.remove('btn-outline-primary');
                    coverBtn.classList.add('btn-success');
                }
            } else {
                console.warn("Внимание! Путь к обложке не найден в локальных хранилищах.");
            }
            
            // Обновляем значения в iframe при загрузке
            updateIframeFields();
        }, 500);
        
        // Инициализируем взаимодействие с iframe
        initIframeInteraction();
        
        console.log('Iframe загружен, ожидаем сообщения о готовности');
    });
    
    // Обработчик переключения боковой панели
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('open');
        console.log("Sidebar toggled:", sidebar.classList.contains('open'));
        
        // Иконка переворачивается автоматически через CSS трансформацию
    });
    
    // Функция инициализации взаимодействия с iframe
    function initIframeInteraction() {
        // Первичное обновление iframe с данными из формы
        updateIframeFields();
        
        // Настраиваем обработчики событий для двусторонней синхронизации
        setupTwoWaySync();
        
        console.log("Sidebar initialized");
    }
    
    // Функция настройки двусторонней синхронизации между формой и iframe
    function setupTwoWaySync() {
        // Обработчик сообщений от iframe
        window.addEventListener('message', function(event) {
            // Проверяем, что сообщение от нашего iframe
            if (event.source !== iframe.contentWindow) return;
            
            console.log('Получено сообщение от iframe:', event.data);
            
            // Обрабатываем разные типы сообщений
            if (event.data && event.data.type) {
                switch (event.data.type) {
                    case 'template_ready':
                        console.log('Шаблон сертификата готов');
                        // Сразу отправляем текущие значения формы в iframe
                        updateIframeFields();
                        break;
                        
                    case 'request_initial_data':
                        console.log('Получен запрос начальных данных от iframe');
                        // Отправляем текущие значения формы в iframe
                        updateIframeFields();
                        break;
                        
                    case 'field_update':
                        // Обрабатываем обновления полей из iframe
                        handleFieldUpdate(event.data.field, event.data.value);
                        break;
                        
                    case 'current_values':
                        // Обрабатываем текущие значения из iframe
                        console.log('Получены текущие значения из iframe:', event.data.values);
                        // Обновляем значения формы из iframe
                        if (event.data.values) {
                            Object.keys(event.data.values).forEach(fieldName => {
                                handleFieldUpdate(fieldName, event.data.values[fieldName]);
                            });
                        }
                        break;
                    
                    case 'fields_updated':
                        console.log('Поля в iframe успешно обновлены');
                        // Запрашиваем текущие значения полей из iframe для синхронизации
                        requestCurrentValues();
                        break;
                }
            }
        });
    }
    
    // Функция обработки обновления поля от iframe
    function handleFieldUpdate(fieldName, value) {
        console.log(`Обновление поля ${fieldName} из iframe: ${value}`);
        
        // Обновляем соответствующее поле в форме
        switch(fieldName) {
            case 'recipient_name':
                document.getElementById('recipient_name').value = value;
                break;
                
            case 'company_name':
                document.getElementById('company_name').value = value;
                break;
                
            case 'amount':
                // Убираем символ валюты и пробелы, оставляем только цифры
                const cleanAmount = value.replace(/[^\d]/g, '');
                document.getElementById('amount').value = cleanAmount;
                document.getElementById('amount_hidden').value = cleanAmount; // Сразу обновляем скрытое поле
                console.log(`Поле amount обновлено из iframe: ${cleanAmount}`);
                break;
                
            case 'message':
                document.getElementById('message').value = value;
                break;
                
            case 'valid_from':
                // Преобразуем дату из формата DD.MM.YYYY в формат YYYY-MM-DD
                if (value) {
                    const parts = value.split('.');
                    if (parts.length === 3) {
                        document.getElementById('valid_from').value = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                }
                break;
                
            case 'valid_until':
                // Преобразуем дату из формата DD.MM.YYYY в формат YYYY-MM-DD
                if (value) {
                    const parts = value.split('.');
                    if (parts.length === 3) {
                        document.getElementById('valid_until').value = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                }
                break;
        }
    }
    
    // Функция запроса текущих значений из iframe
    function requestCurrentValues() {
        if (!iframeInitialized || !iframe.contentWindow) return;
        
        iframe.contentWindow.postMessage({
            type: 'request_current_values'
        }, '*');
    }
    
    // Функция обновления полей в iframe
    function updateIframeFields() {
        // Проверяем, что iframe загружен
        if (!iframeInitialized || !iframe.contentWindow) {
            console.log('Iframe еще не инициализирован, отложим отправку данных');
            setTimeout(updateIframeFields, 500);
            return;
        }
        
        const formValues = getFormValues();
        console.log('Отправка данных формы в iframe:', formValues);
        
        // Отправляем данные в iframe
        iframe.contentWindow.postMessage({
            type: 'update_fields',
            data: formValues
        }, '*');
    }
    
    // Обработчики изменения полей формы
    document.querySelectorAll('#company_name, #recipient_name, #amount, #message, #valid_from, #valid_until').forEach(input => {
        input.addEventListener('input', updateIframeFields);
        input.addEventListener('change', updateIframeFields);
    });
    
    // Обработчик кнопки создания сертификата в шапке
    if (createCertificateBtn) {
        createCertificateBtn.addEventListener('click', submitForm);
    }
    
    // Обработчик кнопки создания сертификата в боковой панели
    if (sidebarCreateBtn) {
        sidebarCreateBtn.addEventListener('click', submitForm);
    }
    
    // Функция отправки формы
    function submitForm() {
        // Проверяем обязательные поля
        // Сначала запрашиваем актуальные значения из iframe
        requestCurrentValues();
        
        // Небольшая задержка для получения обновленных значений
        setTimeout(() => {
            const amount = document.getElementById('amount').value;
            const message = document.getElementById('message').value;
            const validUntil = document.getElementById('valid_until').value;
            const companyName = document.getElementById('company_name').value;
            
            if (!companyName) {
                alert('Пожалуйста, введите название организации');
                return;
            }
            
            if (!amount) {
                alert('Пожалуйста, введите номинал');
                return;
            }
            
            if (!message) {
                alert('Пожалуйста, введите текст сообщения');
                return;
            }
            
            if (!validUntil) {
                alert('Пожалуйста, укажите дату окончания срока действия');
                return;
            }
            
            // Заполняем скрытые поля формы
            document.getElementById('recipient_name_hidden').value = document.getElementById('recipient_name').value;
            document.getElementById('amount_hidden').value = amount;
            document.getElementById('message_hidden').value = message;
            document.getElementById('company_name_hidden').value = companyName;
            document.getElementById('valid_from_hidden').value = document.getElementById('valid_from').value;
            document.getElementById('valid_until_hidden').value = validUntil;
            
            // Обновляем значение batch_size в скрытом поле формы
            const batchSizeVisible = document.getElementById('batch_size');
            const batchSizeHidden = document.getElementById('batch_size_hidden');
            if (batchSizeVisible && batchSizeHidden) {
                batchSizeHidden.value = batchSizeVisible.value;
                console.log("Установлено количество сертификатов в серии:", batchSizeVisible.value);
            } else if (batchSizeVisible) {
                // Если скрытого поля нет, создаем его
                dataForm.querySelector('input[name="batch_size"]').value = batchSizeVisible.value;
                console.log("Обновлено скрытое поле batch_size:", batchSizeVisible.value);
            }
            
            // Получаем путь к обложке из всех возможных источников
            const coverPath = getCoverPath();
            
            if (coverPath) {
                document.getElementById('temp_cover_path_hidden').value = coverPath;
                console.log("Использую путь к обложке при отправке формы:", coverPath);
            } else {
                // ВАЖНО! Если путь к обложке не найден перед самой отправкой, пробуем аварийный вариант
                const possibleCoverPaths = [
                    getUrlParam('cover'),
                    sessionStorage.getItem('temp_certificate_cover'),
                    localStorage.getItem('temp_certificate_cover_backup'),
                    getCookie('temp_certificate_cover')
                ];
                
                // Выводим в консоль все возможные пути для отладки
                console.warn("Аварийная проверка возможных путей к обложке:");
                console.table(possibleCoverPaths);
                
                // Берем первый найденный путь
                const recoveredPath = possibleCoverPaths.find(path => path && path.trim() !== '');
                if (recoveredPath) {
                    document.getElementById('temp_cover_path_hidden').value = recoveredPath;
                    console.log("Аварийное восстановление пути к обложке:", recoveredPath);
                } else {
                    // Проверяем, может путь уже был установлен ранее в скрытое поле
                    const existingPath = document.getElementById('temp_cover_path_hidden').value;
                    
                    if (!existingPath || existingPath.trim() === '') {
                        alert('Пожалуйста, создайте обложку документа в фоторедакторе');
                        console.error("Путь к обложке не найден! Процесс отправки формы остановлен.");
                        return;
                    } else {
                        console.log("Используем существующий путь из скрытого поля:", existingPath);
                    }
                }
            }
            
            console.log("Отправка формы с данными:", {
                recipient_name: document.getElementById('recipient_name_hidden').value,
                amount: document.getElementById('amount_hidden').value,
                message: message,
                cover_path: document.getElementById('temp_cover_path_hidden').value,
                company_name: companyName
            });
            
            // Дополнительная проверка скрытого поля непосредственно перед отправкой
            const hiddenCoverField = document.getElementById('temp_cover_path_hidden');
            if (!hiddenCoverField || !hiddenCoverField.value) {
                console.error('КРИТИЧЕСКАЯ ОШИБКА: Скрытое поле пути к обложке пусто перед отправкой');
                alert('Произошла ошибка при получении пути к обложке. Пожалуйста, попробуйте создать сертификат заново.');
                return;
            }
            
            // Отправляем форму
            dataForm.submit();
        }, 500);
    }
    
    // Обновление количества стиков для списания при изменении количества сертификатов
    const batchSize = document.getElementById('batch_size');
    const sticksToUse = document.getElementById('sticksToUse');
    const batchWarning = document.getElementById('batchWarning');
    const warningMessage = document.getElementById('warningMessage');
    const availableSticks = <?php echo e(Auth::user()->sticks); ?>;
    
    // Синхронизируем значение с скрытым полем формы
    function updateHiddenBatchSize() {
        // Обновляем скрытое поле формы с тем же именем
        const formInput = document.getElementById('batch_size_hidden');
        if (formInput && batchSize) {
            formInput.value = batchSize.value;
            console.log('Обновлено скрытое поле batch_size:', batchSize.value);
        }
    }
    
    // Обработчик изменения поля
    if (batchSize) {
        batchSize.addEventListener('input', function() {
            // Обновляем количество стиков
            sticksToUse.textContent = this.value;
            
            // Обновляем скрытое поле формы
            updateHiddenBatchSize();
            
            // Проверяем значение
            validateBatchSize();
        });

        // Обработчик при потере фокуса для коррекции значения
        batchSize.addEventListener('blur', function() {
            correctBatchSize();
            // Обновляем скрытое поле формы после коррекции
            updateHiddenBatchSize();
        });
    }
    
    // Функция валидации количества сертификатов
    function validateBatchSize() {
        let value = parseInt(batchSize.value);
        
        // Проверка на отрицательные значения или не число
        if (isNaN(value) || value < 1) {
            value = 1;
            batchSize.value = value;
        }
        
        // Проверка на максимальное значение
        if (value > availableSticks) {
            batchWarning.style.display = 'block';
            warningMessage.textContent = `У вас недостаточно стиков для создания ${value} сертификатов. Максимально доступно: ${availableSticks} шт.`;
        } else {
            // Обновляем количество списываемых стиков
            sticksToUse.textContent = value;
            batchWarning.style.display = 'none';
        }
    }
    
    // Функция для коррекции значения при потере фокуса
    function correctBatchSize() {
        let value = parseInt(batchSize.value);
        
        // Если значение некорректное, устанавливаем минимальное
        if (isNaN(value) || value < 1) {
            value = 1;
            batchSize.value = value;
        } 
        // Если значение больше доступных стиков, устанавливаем максимально возможное
        else if (value > availableSticks) {
            value = availableSticks;
            batchSize.value = value;
        }
        
        // Обновляем отображение
        sticksToUse.textContent = value;
        batchWarning.style.display = 'none';
        
        console.log('Скорректировано и установлено значение:', value);
    }
    
    // Инициализируем при загрузке
    if (batchSize) {
        validateBatchSize();
        updateHiddenBatchSize();
        
        // Глобальная функция для доступа из других скриптов
        window.getBatchSize = function() {
            return parseInt(batchSize.value) || 1;
        };
    }
});

// Немедленно выполняемая функция для обеспечения синхронизации полей batch_size
(function() {
    // Выполняем при загрузке DOM
    document.addEventListener('DOMContentLoaded', function() {
        const visibleField = document.getElementById('batch_size');
        const hiddenField = document.getElementById('batch_size_hidden');
        
        if (visibleField && hiddenField) {
            // Начальная синхронизация
            hiddenField.value = visibleField.value;
            console.log("Начальная синхронизация batch_size:", visibleField.value);
        }
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/create_iframe.blade.php ENDPATH**/ ?>