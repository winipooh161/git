<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($certificate->template->name); ?> - <?php echo e(config('app.name')); ?></title>
    
    <!-- Мета-теги для SEO и социальных сетей -->
    <meta name="description" content="Подарочный сертификат от <?php echo e($data['company_name'] ?? config('app.name')); ?>">
    <meta property="og:title" content="<?php echo e($certificate->template->name); ?> - <?php echo e(config('app.name')); ?>">
    <meta property="og:description" content="Подарочный сертификат от <?php echo e($data['company_name'] ?? config('app.name')); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <meta property="og:type" content="website">
    <?php if($certificate->cover_image): ?>
    <meta property="og:image" content="<?php echo e(asset('storage/' . $certificate->cover_image)); ?>">
    <?php endif; ?>
    
    <?php echo $__env->make('certificates.partials.meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('certificates.partials.styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <!-- Дополнительные стили для исправления QR-кнопки -->
    <style>
        /* Принудительные стили для кнопки QR и QR-кода */
        .admin-qr-toggle {
            position: fixed !important;
            bottom: 20px !important;
            right: 20px !important;
            width: 40px !important;
            height: 40px !important;
            background: #3b82f6 !important;
            color: white !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 9999 !important;
            cursor: pointer !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) !important;
        }
        
        @media (max-width: 768px) {
            .admin-qr-toggle {
                display: flex !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php if(session('success')): ?>
    <div class="alert-success" style="
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #10b981;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 90%;
        text-align: center;
    ">
        <?php echo e(session('success')); ?>

    </div>
    <script>
        setTimeout(function() {
            document.querySelector('.alert-success').style.opacity = '0';
            document.querySelector('.alert-success').style.transition = 'opacity 0.5s ease';
            setTimeout(function() {
                document.querySelector('.alert-success').style.display = 'none';
            }, 500);
        }, 4000);
    </script>
    <?php endif; ?>
    
    <div class="main-container" id="mainContainer">
        <?php echo $__env->make('certificates.partials.cover-section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <?php
        // Подготавливаем данные сертификата для передачи в iframe
        $certificateData = [
            'recipient_name' => $certificate->recipient_name ?? '',
            'sender_name' => $certificate->sender_name ?? '',
            'amount' => $certificate->amount ?? 0,
            'message' => $certificate->message ?? '',
            'valid_until' => $certificate->valid_until ? $certificate->valid_until->format('d.m.Y') : '',
            'valid_from' => $certificate->valid_from ? $certificate->valid_from->format('d.m.Y') : '',
            'company_name' => $certificate->user->company ?? config('app.name'),
            'certificate_number' => $certificate->number ?? '',
            // Добавляем все дополнительные поля из $data, если они есть
            'additional' => $data ?? []
        ];
        ?>
        
        <?php echo $__env->make('certificates.partials.certificate-section', ['certificateData' => $certificateData], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        
        <!-- Информация о серии сертификатов -->
        <?php if(isset($certificate) && ($certificate->status === 'series' || $certificate->is_batch_parent)): ?>
            <div class="series-info-container">
                <div class="series-info-box">
                    <h4>
                        <i class="fa-solid fa-layer-group me-2"></i>
                        Информация о серии сертификатов
                    </h4>
                    <p>
                        Этот сертификат выпущен как серия из <strong><?php echo e($certificate->batch_size); ?></strong> экземпляров.
                        <?php if($certificate->activated_copies_count < $certificate->batch_size): ?>
                            <span class="text-success">
                                Доступно для активации: <strong><?php echo e($certificate->batch_size - $certificate->activated_copies_count); ?></strong> экз.
                            </span>
                        <?php else: ?>
                            <span class="text-danger">Все сертификаты из серии уже активированы.</span>
                        <?php endif; ?>
                    </p>
                    <div class="progress mt-3" style="height: 10px;">
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: <?php echo e(($certificate->activated_copies_count / $certificate->batch_size) * 100); ?>%;" 
                             aria-valuenow="<?php echo e($certificate->activated_copies_count); ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="<?php echo e($certificate->batch_size); ?>"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mt-1">
                        <span>Активировано: <?php echo e($certificate->activated_copies_count); ?></span>
                        <span>Всего: <?php echo e($certificate->batch_size); ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php echo $__env->make('certificates.partials.modals', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('certificates.partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <!-- Сохраняем данные сертификата в глобальной переменной для доступа из JS -->
    <script>
        window.certificateData = <?php echo json_encode($certificateData, 15, 512) ?>;
    </script>
    
    <!-- Добавляем запасной QR-код, если он не рендерится в секции certificate-section -->
    <div id="qr-button-fallback"></div>
    
    <!-- Добавляем кнопку "Получить сертификат" только если он еще не присвоен и серия не исчерпана-->
    <?php if((!isset($isClaimed) || !$isClaimed) && 
        ($certificate->status === 'series' || 
         (!$certificate->is_batch_parent || 
          ($certificate->is_batch_parent && $certificate->activated_copies_count < $certificate->batch_size)))): ?>
    <button id="getCertificateBtn" class="get-certificate-btn">
        <i class="fa-solid fa-gift"></i> Получить сертификат
    </button>
    <?php elseif($certificate->status === 'series' && $certificate->activated_copies_count >= $certificate->batch_size): ?>
    <div class="alert-info series-depleted">
        Все сертификаты из серии уже активированы
    </div>
    <?php endif; ?>
    
    <!-- Модальное окно для авторизации/регистрации -->
    <div id="authModal" class="auth-modal-overlay">
        <div class="auth-modal">
            <div class="auth-modal-header">
                <h3>Получение сертификата</h3>
                <button type="button" class="auth-modal-close">&times;</button>
            </div>
            
            <div class="auth-form-tabs">
                <div class="auth-tab active" data-tab="login">Вход</div>
                <div class="auth-tab" data-tab="register">Регистрация</div>
            </div>
            
            <div id="loginForm" class="auth-form">
                <form action="<?php echo e(route('login')); ?>" method="POST" id="certificateLoginForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="certificate_uuid" value="<?php echo e($certificate->uuid); ?>">
                    
                    <div class="form-group">
                        <label for="login-email" class="form-label">Email или телефон</label>
                        <input type="text" name="email" id="login-email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login-password" class="form-label">Пароль</label>
                        <input type="password" name="password" id="login-password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Запомнить меня</label>
                    </div>
                    
                    <button type="submit" class="auth-submit-btn">Войти и получить сертификат</button>
                </form>
            </div>
            
            <div id="registerForm" class="auth-form" style="display: none;">
                <form action="<?php echo e(route('register')); ?>" method="POST" id="certificateRegisterForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="certificate_uuid" value="<?php echo e($certificate->uuid); ?>">
                    
                    <div class="form-group">
                        <label for="register-name" class="form-label">Имя</label>
                        <input type="text" name="name" id="register-name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-email" class="form-label">Email</label>
                        <input type="email" name="email" id="register-email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-phone" class="form-label">Телефон</label>
                        <input type="tel" name="phone" id="register-phone" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="register-password" class="form-label">Пароль</label>
                        <input type="password" name="password" id="register-password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-password-confirm" class="form-label">Подтверждение пароля</label>
                        <input type="password" name="password_confirmation" id="register-password-confirm" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="auth-submit-btn">Зарегистрироваться и получить сертификат</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Скрипт для страницы с сертификатом (публичная версия) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Деактивируем все возможные contenteditable элементы
            const editableElements = document.querySelectorAll('[contenteditable]');
            editableElements.forEach(function(element) {
                // Удаляем атрибут contenteditable
                element.removeAttribute('contenteditable');
                
                // Дополнительно добавляем атрибут readonly для полной безопасности
                element.setAttribute('readonly', 'readonly');
            });
            
            // Находим iframe и блокируем редактирование в нем
            const iframe = document.getElementById('certificateIframe');
            if (iframe) {
                iframe.addEventListener('load', function() {
                    // Отправляем сообщение для деактивации редактирования в iframe
                    try {
                        iframe.contentWindow.postMessage({
                            type: 'disable_editing',
                            disable: true
                        }, '*');
                        console.log('Отправлен запрос на деактивацию редактирования в iframe');
                    } catch (e) {
                        console.error('Ошибка при отправке сообщения в iframe:', e);
                    }
                });
            }
            
            // Настройка кнопки "Поделиться"
            const shareButton = document.getElementById('shareButton');
            if (shareButton) {
                shareButton.addEventListener('click', function() {
                    if (navigator.share) {
                        navigator.share({
                            title: document.title,
                            text: 'Посмотрите мой сертификат!',
                            url: window.location.href
                        }).catch((error) => console.log('Ошибка при публикации:', error));
                    } else {
                        // Запасной вариант - копирование ссылки в буфер обмена
                        const dummy = document.createElement('input');
                        document.body.appendChild(dummy);
                        dummy.value = window.location.href;
                        dummy.select();
                        document.execCommand('copy');
                        document.body.removeChild(dummy);
                        
                        alert('Ссылка на сертификат скопирована в буфер обмена!');
                    }
                });
            }
            
            // Обработка нажатия на кнопку "Получить сертификат"
            const getCertificateBtn = document.getElementById('getCertificateBtn');
            const authModal = document.getElementById('authModal');
            const authModalCloseBtn = document.querySelector('.auth-modal-close');
            const authTabs = document.querySelectorAll('.auth-tab');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            
            // Функция открытия модального окна авторизации
            function openAuthModal() {
                authModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            // Функция закрытия модального окна авторизации
            function closeAuthModal() {
                authModal.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            // Обработчик для кнопки получения сертификата
            if (getCertificateBtn) {
                getCertificateBtn.addEventListener('click', function() {
                    // Сразу показываем индикатор загрузки
                    const loadingHtml = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Обработка...';
                    const originalHtml = getCertificateBtn.innerHTML;
                    getCertificateBtn.innerHTML = loadingHtml;
                    getCertificateBtn.disabled = true;
                    
                    // Проверяем, авторизован ли пользователь
                    fetch('<?php echo e(route("check.auth")); ?>', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.authenticated) {
                            // Добавляем диагностику
                            console.log('Пользователь авторизован, перенаправляем на получение сертификата');
                            
                            // Если пользователь авторизован, отправляем запрос на получение сертификата
                            const claimUrl = '<?php echo e(route("certificates.claim", $certificate->uuid)); ?>';
                            console.log('URL для получения сертификата:', claimUrl);
                            
                            // Показываем сообщение и перенаправляем
                            window.location.href = claimUrl;
                        } else {
                            // Если пользователь не авторизован, восстанавливаем кнопку и открываем модальное окно
                            getCertificateBtn.innerHTML = originalHtml;
                            getCertificateBtn.disabled = false;
                            openAuthModal();
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при проверке авторизации:', error);
                        // Восстанавливаем кнопку
                        getCertificateBtn.innerHTML = originalHtml;
                        getCertificateBtn.disabled = false;
                        // В случае ошибки показываем модальное окно авторизации
                        openAuthModal();
                    });
                });
            }
            
            // Обработчик для закрытия модального окна
            if (authModalCloseBtn) {
                authModalCloseBtn.addEventListener('click', closeAuthModal);
            }
            
            // Закрытие по клику за пределами модального окна
            authModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAuthModal();
                }
            });
            
            // Обработчики для табов
            authTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Убираем активный класс со всех табов
                    authTabs.forEach(t => t.classList.remove('active'));
                    
                    // Добавляем активный класс текущему табу
                    this.classList.add('active');
                    
                    // Скрываем все формы
                    loginForm.style.display = 'none';
                    registerForm.style.display = 'none';
                    
                    // Показываем нужную форму
                    const tabName = this.dataset.tab;
                    if (tabName === 'login') {
                        loginForm.style.display = 'block';
                    } else if (tabName === 'register') {
                        registerForm.style.display = 'block';
                    }
                });
            });
            
            // Обработка отправки форм для автоматического получения сертификата после авторизации
            const certificateLoginForm = document.getElementById('certificateLoginForm');
            if (certificateLoginForm) {
                certificateLoginForm.addEventListener('submit', function(e) {
                    // Добавляем редирект на страницу получения сертификата
                    const redirectInput = document.createElement('input');
                    redirectInput.type = 'hidden';
                    redirectInput.name = 'redirect_to';
                    redirectInput.value = '<?php echo e(route("certificates.claim", $certificate->uuid)); ?>';
                    this.appendChild(redirectInput);
                });
            }
            
            const certificateRegisterForm = document.getElementById('certificateRegisterForm');
            if (certificateRegisterForm) {
                certificateRegisterForm.addEventListener('submit', function(e) {
                    // Добавляем редирект на страницу получения сертификата
                    const redirectInput = document.createElement('input');
                    redirectInput.type = 'hidden';
                    redirectInput.name = 'redirect_to';
                    redirectInput.value = '<?php echo e(route("certificates.claim", $certificate->uuid)); ?>';
                    this.appendChild(redirectInput);
                });
            }

            // Проверка наличия элементов QR-кода и кнопки
            const qrToggle = document.getElementById('adminQrToggle');
            const qrCode = document.getElementById('adminQrCode');
            const qrFullscreen = document.getElementById('qrFullscreenOverlay');
            
            console.log('QR элементы при загрузке:', {
                qrToggle: qrToggle ? 'найден' : 'не найден',
                qrCode: qrCode ? 'найден' : 'не найден',
                qrFullscreen: qrFullscreen ? 'найден' : 'не найден'
            });
            
            // Если кнопка QR не найдена, создаем запасную версию
            if (!qrToggle) {
                console.log('Создаем запасную QR кнопку');
                const fallbackBtn = document.createElement('div');
                fallbackBtn.id = 'adminQrToggle';
                fallbackBtn.className = 'admin-qr-toggle';
                fallbackBtn.innerHTML = 'QR';
                document.body.appendChild(fallbackBtn);
                
                // Задаем обработчик нажатия
                fallbackBtn.addEventListener('click', function() {
                    console.log('Нажатие на запасную кнопку QR');
                    if (qrFullscreen) {
                        qrFullscreen.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    } else {
                        console.warn('Модальное окно QR не найдено');
                    }
                });
            }
        });
    </script>
</body>
</html>


<?php /**PATH C:\OSPanel\domains\sert\resources\views/certificates/public.blade.php ENDPATH**/ ?>