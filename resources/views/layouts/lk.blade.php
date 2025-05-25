<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Личный кабинет</title>

    <!-- Favicon и иконки -->
    <link type="image/x-icon" rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Дополнительные иконки для десктопных браузеров -->
    <link type="image/png" sizes="16x16" rel="icon" href="{{ asset('icons/favicon-16x16.png') }}">
    <link type="image/png" sizes="32x32" rel="icon" href="{{ asset('icons/favicon-32x32.png') }}">
    <link type="image/png" sizes="96x96" rel="icon" href="{{ asset('icons/favicon-96x96.png') }}">
    <link type="image/png" sizes="120x120" rel="icon" href="{{ asset('icons/favicon-120x120.png') }}">

    <!-- Иконки для Android -->
    <link type="image/png" sizes="72x72" rel="icon" href="{{ asset('icons/android-icon-72x72.png') }}">
    <link type="image/png" sizes="96x96" rel="icon" href="{{ asset('icons/android-icon-96x96.png') }}">
    <link type="image/png" sizes="144x144" rel="icon" href="{{ asset('icons/android-icon-144x144.png') }}">
    <link type="image/png" sizes="192x192" rel="icon" href="{{ asset('icons/android-icon-192x192.png') }}">
    <link type="image/png" sizes="512x512" rel="icon" href="{{ asset('icons/android-icon-512x512.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Иконки для iOS (Apple) -->
    <link sizes="57x57" rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon-57x57.png') }}">
    <link sizes="60x60" rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon-60x60.png') }}">
    <link sizes="72x72" rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon-72x72.png') }}">
    <link sizes="76x76" rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon-76x76.png') }}">
    <link sizes="114x114" rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon-114x114.png') }}">
    <link sizes="120x120" rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon-120x120.png') }}">
    <link sizes="144x144" rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon-144x144.png') }}">
    <link sizes="152x152" rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon-152x152.png') }}">
    <link sizes="180x180" rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon-180x180.png') }}">

    <!-- Иконки для MacOS (Apple) -->
    <link color="#e52037" rel="mask-icon" href="{{ asset('icons/safari-pinned-tab.svg') }}">

    <!-- Иконки и цвета для плиток Windows -->
    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="msapplication-TileImage" content="{{ asset('icons/mstile-144x144.png') }}">
    <meta name="msapplication-square70x70logo" content="{{ asset('icons/mstile-70x70.png') }}">
    <meta name="msapplication-square150x150logo" content="{{ asset('icons/mstile-150x150.png') }}">
    <meta name="msapplication-wide310x150logo" content="{{ asset('icons/mstile-310x150.png') }}">
    <meta name="msapplication-square310x310logo" content="{{ asset('icons/mstile-310x310.png') }}">
    <meta name="application-name" content="{{ config('app.name', 'Laravel') }}">
    <meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700" rel="stylesheet">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#000000">
    <meta name="description" content="Платформа для управления документами">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('/icons/icon-192x192.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Laravel') }}">

    <!-- Иконки -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Select2 (добавляем глобально для всех страниц) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    @stack('styles')

    <!-- Scripts -->
    @vite(['resources/css/lk.css', 'resources/js/app.js'])
    <script src="{{ asset('js/notifications.js') }}"></script>

    <style>

    </style>
</head>

<body>
    <div id="app" class="d-flex flex-column vh-100">
        <!-- ===================== МОБИЛЬНАЯ ШАПКА ===================== -->
        <header class="navbar navbar-expand-lg sticky-top bg-white d-lg-none">
            <div class="container-fluid" style="display: flex; align-items: center; justify-content: space-between;" >

                @if (session('active_role') == 'predprinimatel')
                    <!-- Кнопка "назад" для возврата в режим клиента -->
                    <form action="{{ route('role.switch') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" value="user">
                        <button type="submit" class="role-switcher-btn" title="Вернуться к режиму клиента">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                    </form>
                @else
                    @if (Auth::user()->hasRole('user'))
                        <!-- Кнопка с аватаром для переключения на режим предпринимателя -->
                        <form action="{{ route('role.switch') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="predprinimatel">
                            <button type="submit" class="role-switcher-btn" title="Перейти в режим предпринимателя">
                                @if (Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class=""
                                        alt="{{ Auth::user()->name }}">
                                @else
                                    <div
                                        class="w-100 h-100 bg-primary text-white d-flex align-items-center justify-content-center">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </button>
                        </form>
                    @endif
                @endif
                <div class="div_ggg" style="width: 50px; height: 2px; content: ''; background: #000;"></div>
                <!-- Кнопка быстрого создания документа -->
                @if (Auth::user()->hasRole('predprinimatel') && session('active_role') != 'predprinimatel')
                    <div class="" style="padding-right: 10px">
                        <button id="scanQrButton"
                            class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 38px; height: 38px;" title="Сканировать QR-код">
                            <i class="fa-solid fa-camera fs-5"></i>
                        </button>
                    </div>
                @elseif(Auth::user()->hasRole('predprinimatel') && session('active_role') == 'predprinimatel')
                    <div class="" style="padding-right: 10px">
                        <form action="{{ route('entrepreneur.certificates.initiate-create') }}" method="GET"
                            id="quickCreateForm">
                            <button type="submit"
                                class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 38px; height: 38px;" title="Создать документ">
                                <i class="fa-solid fa-plus fs-5"></i>
                            </button>
                        </form>
                    </div>
                @elseif(Auth::user()->hasRole('user'))
                    <div class="" style="padding-right: 10px">
                        <form action="{{ route('role.switch') }}" method="POST" id="quickCreateForm">
                            @csrf
                            <input type="hidden" name="role" value="predprinimatel">
                            <input type="hidden" name="redirect"
                                value="{{ route('entrepreneur.certificates.initiate-create') }}">
                            <button type="submit"
                                class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                style="width: 38px; height: 38px;" title="Создать документ">
                                <i class="fa-solid fa-plus fs-5"></i>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
            
            <!-- Система папок -->
            @if (session('active_role') == 'predprinimatel')
            @else
                <div class="container-fluid mt-2">
                    @include('user.certificates.partials._folder_system', [
                        'folders' => $folders ?? [],
                        'currentFolder' => $currentFolder ?? null,
                    ])
                </div>
            @endif
        </header>

        <div class="container-fluid vh-100 d-flex flex-grow-1 p-0">
            <!-- ===================== БОКОВАЯ НАВИГАЦИЯ ===================== -->
            @include('components.sidebar')

            <!-- ===================== ОСНОВНОЕ СОДЕРЖИМОЕ ===================== -->
            <main class="flex-grow-1 main-content overflow-auto">
                <div class="content-wrapper">
                    @yield('content')
                </div>
            </main>
        </div>

        <!-- Баннер установки PWA -->
        <div id="pwa-install-banner" class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-1">Установите приложение</h5>
                    <p class="mb-0 small text-muted">Добавьте наше приложение на главный экран для быстрого доступа</p>

                    <!-- Инструкции для iOS -->
                    <div id="pwa-install-ios-instructions">
                        <ol id="pwa-install-steps" class="small">
                            <li>Нажмите <i class="fa fa-share-square"></i> в нижней панели браузера</li>
                            <li>Прокрутите и выберите "Добавить на главный экран"</li>
                            <li>Нажмите "Добавить" в правом верхнем углу</li>
                        </ol>
                    </div>
                </div>
                <div class="col-auto">
                    <button id="pwa-install-btn" class="btn btn-primary btn-sm">Установить</button>
                    <button id="pwa-close-btn" class="btn btn-outline-secondary btn-sm ms-2">Позже</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Select2 (добавляем глобально) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ru.js"></script>

    <!-- Скрипт для PWA установки -->
    <script>
        // Переменная для хранения события установки (Android)
        let deferredPrompt;
        const pwaInstallBanner = document.getElementById('pwa-install-banner');
        const pwaInstallBtn = document.getElementById('pwa-install-btn');
        const pwaCloseBtn = document.getElementById('pwa-close-btn');
        const pwaIosInstructions = document.getElementById('pwa-install-ios-instructions');

        // Проверяем, установлено ли уже приложение
        function isAppInstalled() {
            // Проверка для iOS 
            if (navigator.standalone) {
                return true;
            }

            // Проверка для Android и других устройств
            if (window.matchMedia('(display-mode: standalone)').matches) {
                return true;
            }

            // Проверяем локальное хранилище - возможно пользователь отклонил установку
            if (localStorage.getItem('pwa-install-dismissed')) {
                return true;
            }

            return false;
        }

        // Проверяем, является ли устройство iOS
        function isIOS() {
            return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        }

        // Показываем баннер с правильными инструкциями
        function showInstallBanner() {
            // Не показываем баннер если приложение уже установлено или открыто в приложении
            if (isAppInstalled()) return;

            // Настраиваем баннер в зависимости от платформы
            if (isIOS()) {
                // Для iOS показываем инструкции
                pwaInstallBtn.style.display = 'none';
                pwaIosInstructions.style.display = 'block';
            } else {
                // Для Android скрываем инструкции и показываем кнопку
                pwaInstallBtn.style.display = 'block';
                pwaIosInstructions.style.display = 'none';
            }

            // Показываем баннер
            pwaInstallBanner.style.display = 'block';
        }

        // Обрабатываем событие beforeinstallprompt (только для поддерживаемых браузеров)
        window.addEventListener('beforeinstallprompt', (e) => {
            // Предотвращаем автоматическое появление диалога установки браузера
            e.preventDefault();

            // Сохраняем событие для использования позже
            deferredPrompt = e;

            // Проверяем, активно ли мобильное устройство
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator
                .userAgent);

            // Показываем наш баннер только на мобильных устройствах
            if (isMobile) {
                showInstallBanner();
            }
        });

        // Обработчик события по нажатию на кнопку установки
        pwaInstallBtn.addEventListener('click', async () => {
            // Скрываем баннер
            pwaInstallBanner.style.display = 'none';

            // Проверяем, доступно ли событие установки
            if (deferredPrompt) {
                // Показываем диалог установки
                deferredPrompt.prompt();

                // Ожидаем ответа пользователя
                const {
                    outcome
                } = await deferredPrompt.userChoice;

                // Очищаем сохраненное событие
                deferredPrompt = null;

                // Если пользователь отказался, запоминаем это
                if (outcome === 'dismissed') {
                    localStorage.setItem('pwa-install-dismissed', 'true');
                }
            }
        });

        // Обработчик закрытия баннера
        pwaCloseBtn.addEventListener('click', () => {
            pwaInstallBanner.style.display = 'none';

            // Запоминаем, что пользователь закрыл баннер
            localStorage.setItem('pwa-install-dismissed', 'true');

            // Через неделю можно снова показать баннер
            setTimeout(() => {
                localStorage.removeItem('pwa-install-dismissed');
            }, 7 * 24 * 60 * 60 * 1000);
        });

        // Проверяем при загрузке страницы, нужно ли показывать баннер для iOS
        document.addEventListener('DOMContentLoaded', () => {
            // Ждем немного, чтобы не раздражать пользователя сразу
            setTimeout(() => {
                if (isIOS() && !isAppInstalled()) {
                    showInstallBanner();
                }
            }, 2000);
        });

        // Проверяем, запущено ли приложение в режиме PWA
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('Приложение запущено в режиме PWA');
        }
    </script>

    <!-- Скрипт для управления боковым меню -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Инициализация всплывающих подсказок Bootstrap
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => {
                new bootstrap.Tooltip(tooltip);
            });

            // Переключение бокового меню
            const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
            if (sidebarToggleBtn) {
                // Определяем все элементы, которые нужно скрывать при сжатии меню
                const sidebarText = document.querySelectorAll('.sidebar-text');
                const sidebarUserName = document.querySelector('.sidebar-user-name');
                const sidebarUserRole = document.querySelector('.sidebar-user-role');
                const sidebarLogoText = document.querySelector('.sidebar-logo-text');
                const sidebarVersion = document.querySelector('.sidebar-version');

                // Функция для переключения состояния меню
                function toggleSidebar() {
                    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');

                    // Обновляем иконку кнопки переключения
                    const icon = sidebarToggleBtn.querySelector('i');
                    if (isCollapsed) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-bars-staggered');

                        // Скрываем текстовые элементы
                        sidebarText.forEach(item => {
                            item.style.opacity = '0';
                            item.style.visibility = 'hidden';
                        });

                        if (sidebarUserName) sidebarUserName.style.display = 'none';
                        if (sidebarUserRole) sidebarUserRole.style.display = 'none';
                        if (sidebarLogoText) sidebarLogoText.style.display = 'none';
                        if (sidebarVersion) sidebarVersion.style.display = 'none';
                    } else {
                        icon.classList.remove('fa-bars-staggered');
                        icon.classList.add('fa-bars');

                        // Показываем текстовые элементы после анимации
                        setTimeout(() => {
                            sidebarText.forEach(item => {
                                item.style.opacity = '1';
                                item.style.visibility = 'visible';
                            });

                            if (sidebarUserName) sidebarUserName.style.display = 'block';
                            if (sidebarUserRole) sidebarUserRole.style.display = 'block';
                            if (sidebarLogoText) sidebarLogoText.style.display = 'block';
                            if (sidebarVersion) sidebarVersion.style.display = 'block';
                        }, 300); // Задержка соответствует длительности transition
                    }

                    // Сохраняем состояние в localStorage
                    localStorage.setItem('sidebar-collapsed', isCollapsed);
                }

                // Обработка клика по кнопке переключения
                sidebarToggleBtn.addEventListener('click', toggleSidebar);

                // Восстановление состояния при загрузке
                const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                if (isCollapsed) {
                    document.body.classList.add('sidebar-collapsed');

                    // Скрываем текстовые элементы сразу
                    sidebarText.forEach(item => {
                        item.style.opacity = '0';
                        item.style.visibility = 'hidden';
                    });

                    if (sidebarUserName) sidebarUserName.style.display = 'none';
                    if (sidebarUserRole) sidebarUserRole.style.display = 'none';
                    if (sidebarLogoText) sidebarLogoText.style.display = 'none';
                    if (sidebarVersion) sidebarVersion.style.display = 'none';

                    // Обновляем также иконку при загрузке
                    const icon = sidebarToggleBtn.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-bars-staggered');
                    }
                }
            }

            // Обработка мобильного меню
            const mobileLinks = document.querySelectorAll('.offcanvas-body .nav-link');
            mobileLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    const sidebarMenu = document.getElementById('sidebarMenu');
                    if (window.innerWidth < 992) {
                        const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebarMenu);
                        if (bsOffcanvas) {
                            bsOffcanvas.hide();
                        }
                    }
                });
            });
        });
    </script>

    <!-- Скрипт для сканирования QR-кода -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scanQrButton = document.getElementById('scanQrButton');
            if (scanQrButton) {
                scanQrButton.addEventListener('click', startQrScanner);
            }

            // Создаем элементы для сканера QR-кода
            function createQrScannerElements() {
                // Проверяем, существует ли уже контейнер для сканера
                if (document.getElementById('qr-scanner-container')) {
                    return;
                }

                // Создаем контейнер для сканера
                const scannerContainer = document.createElement('div');
                scannerContainer.id = 'qr-scanner-container';
                scannerContainer.style.position = 'fixed';
                scannerContainer.style.top = '0';
                scannerContainer.style.left = '0';
                scannerContainer.style.width = '100%';
                scannerContainer.style.height = '100%';
                scannerContainer.style.backgroundColor = 'rgba(0,0,0,0.9)';
                scannerContainer.style.zIndex = '9999';
                scannerContainer.style.display = 'flex';
                scannerContainer.style.flexDirection = 'column';
                scannerContainer.style.justifyContent = 'center';
                scannerContainer.style.alignItems = 'center';

                // Создаем элемент видео
                const videoElement = document.createElement('video');
                videoElement.id = 'qr-video';
                videoElement.style.maxWidth = '100%';
                videoElement.style.maxHeight = '80%';
                videoElement.style.borderRadius = '8px';

                // Создаем кнопку закрытия
                const closeButton = document.createElement('button');
                closeButton.textContent = 'Закрыть';
                closeButton.className = 'btn btn-light mt-3';
                closeButton.addEventListener('click', stopQrScanner);

                // Сообщение о сканировании
                const scanMessage = document.createElement('div');
                scanMessage.id = 'scan-message';
                scanMessage.textContent = 'Наведите камеру на QR-код';
                scanMessage.style.color = 'white';
                scanMessage.style.marginBottom = '10px';
                scanMessage.style.fontSize = '16px';

                // Добавляем элементы в контейнер
                scannerContainer.appendChild(scanMessage);
                scannerContainer.appendChild(videoElement);
                scannerContainer.appendChild(closeButton);

                // Добавляем контейнер в body
                document.body.appendChild(scannerContainer);
            }

            // Функция запуска сканера QR-кода
            function startQrScanner() {
                // Создаем элементы UI для сканера
                createQrScannerElements();

                const videoElement = document.getElementById('qr-video');
                const scanMessage = document.getElementById('scan-message');

                // Запрашиваем доступ к камере
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    // Предпочитаем заднюю камеру на мобильных устройствах
                    navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'environment'
                            }
                        })
                        .then(function(stream) {
                            videoElement.srcObject = stream;
                            videoElement.play();

                            // После успешного доступа к камере запускаем сканирование
                            scanQrCode(videoElement);
                        })
                        .catch(function(error) {
                            scanMessage.textContent = 'Ошибка доступа к камере: ' + error.message;
                            scanMessage.style.color = '#ff3333';
                        });
                } else {
                    scanMessage.textContent = 'Ваше устройство не поддерживает доступ к камере';
                    scanMessage.style.color = '#ff3333';
                }
            }

            // Функция остановки сканера
            function stopQrScanner() {
                const qrScannerContainer = document.getElementById('qr-scanner-container');
                const videoElement = document.getElementById('qr-video');

                // Останавливаем поток с камеры
                if (videoElement && videoElement.srcObject) {
                    videoElement.srcObject.getTracks().forEach(track => track.stop());
                }

                // Удаляем контейнер сканера из DOM
                if (qrScannerContainer) {
                    document.body.removeChild(qrScannerContainer);
                }
            }

            // Функция для сканирования QR-кода
            function scanQrCode(videoElement) {
                // Для сканирования QR-кода потребуется библиотека, например jsQR
                // Проверяем, загружена ли библиотека
                if (typeof jsQR !== 'function') {
                    // Если библиотека не загружена, добавляем её
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
                    script.onload = function() {
                        // После загрузки библиотеки запускаем процесс сканирования
                        startScanning(videoElement);
                    };
                    document.head.appendChild(script);
                } else {
                    // Если библиотека уже загружена, сразу запускаем сканирование
                    startScanning(videoElement);
                }
            }

            // Функция процесса сканирования
            function startScanning(videoElement) {
                const scanMessage = document.getElementById('scan-message');
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                let scanning = true;

                // Функция обработки каждого кадра с камеры
                function scanFrame() {
                    if (!scanning) return;

                    if (videoElement.readyState === videoElement.HAVE_ENOUGH_DATA) {
                        // Устанавливаем размеры canvas соответствующие видео
                        canvas.height = videoElement.videoHeight;
                        canvas.width = videoElement.videoWidth;

                        // Отрисовываем текущий кадр с видео на canvas
                        context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

                        // Получаем данные изображения
                        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

                        // Пытаемся найти QR-код на изображении
                        const code = jsQR(imageData.data, imageData.width, imageData.height, {
                            inversionAttempts: "dontInvert",
                        });

                        // Если QR-код найден
                        if (code) {
                            scanning = false;

                            // Показываем сообщение об успехе
                            scanMessage.textContent = 'QR-код успешно считан!';
                            scanMessage.style.color = '#4CAF50';

                            // Выполняем действие с полученным результатом
                            handleQrResult(code.data);
                        }
                    }

                    // Запрашиваем следующий кадр
                    if (scanning) {
                        requestAnimationFrame(scanFrame);
                    }
                }

                // Запускаем процесс сканирования
                scanFrame();
            }

            // Обработка результата сканирования
            function handleQrResult(result) {
                console.log('Результат сканирования:', result);

                // Пробуем открыть URL, если результат является действительной ссылкой
                if (result.startsWith('http')) {
                    // Переходим по ссылке через небольшую задержку
                    setTimeout(() => {
                        window.location.href = result;
                    }, 1000);
                } else {
                    // Если это не URL, можно обработать по-другому
                    alert('Отсканировано: ' + result);
                }

                // В любом случае закрываем сканер
                setTimeout(stopQrScanner, 1500);
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
