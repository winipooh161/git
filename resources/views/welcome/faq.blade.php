<!-- Новый блок FAQ с фильтрацией и поиском -->
<section class="position-relative py-5" id="faq">
    <!-- Контейнер для блобов -->
    <div class="blob-container">
        <div class="blob blob-1" style="left: 15%; top: 30%; background: linear-gradient(45deg, #fd7e14, #dc3545);"></div>
        <div class="blob blob-2" style="right: 10%; bottom: 20%; background: linear-gradient(45deg, #0dcaf0, #0d6efd);"></div>
    </div>
    
    <div class="container px-4">
        <div class="text-center mb-5">
            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill mb-3">
                <i class="fa-solid fa-circle-question me-2"></i>Помощь
            </span>
            <h2 class="display-5 fw-bold mb-3">Часто задаваемые вопросы</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Найдите ответы на популярные вопросы о сервисе</p>
        </div>

        <!-- Поиск по FAQ -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6">
                <div class="faq-search-box bg-white rounded-4 p-2 d-flex align-items-center shadow-sm">
                    <i class="fa-solid fa-magnifying-glass text-muted ms-3 me-2"></i>
                    <input type="text" id="faqSearch" class="form-control border-0 shadow-none" placeholder="Поиск по вопросам...">
                </div>
            </div>
        </div>

        <!-- Категории FAQ -->
        <div class="d-flex justify-content-center mb-5 faq-tabs">
            <ul class="nav nav-pills" id="faqTabs" role="tablist">
                <li class="nav-item mx-2" role="presentation">
                    <button class="nav-link active px-4 py-2" id="all-faq-tab" data-bs-toggle="pill" data-bs-target="#all-faq" type="button" role="tab">
                        <i class="fa-solid fa-border-all me-2"></i>Все вопросы
                    </button>
                </li>
                <li class="nav-item mx-2" role="presentation">
                    <button class="nav-link px-4 py-2" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab">
                        <i class="fa-solid fa-circle-info me-2"></i>Общие
                    </button>
                </li>
                <li class="nav-item mx-2" role="presentation">
                    <button class="nav-link px-4 py-2" id="billing-tab" data-bs-toggle="pill" data-bs-target="#billing" type="button" role="tab">
                        <i class="fa-solid fa-credit-card me-2"></i>Оплата
                    </button>
                </li>
                <li class="nav-item mx-2" role="presentation">
                    <button class="nav-link px-4 py-2" id="tech-tab" data-bs-toggle="pill" data-bs-target="#tech" type="button" role="tab">
                        <i class="fa-solid fa-gears me-2"></i>Техническое
                    </button>
                </li>
            </ul>
        </div>

        <!-- Контент FAQ -->
        <div class="tab-content" id="faqTabsContent">
            <!-- Все вопросы -->
            <div class="tab-pane fade show active" id="all-faq" role="tabpanel">
                <div class="row g-4 faq-items">
                    <!-- Вопрос 1 -->
                    <div class="col-lg-6 faq-item" data-category="general" data-keywords="начало работы регистрация настройка">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-rocket text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Как быстро я смогу начать использовать сервис?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">После регистрации и заполнения данных о вашей компании вы сможете сразу приступить к созданию документов. Весь процесс занимает обычно не более 15-20 минут. Мы предоставляем подробные инструкции на каждом этапе.</p>
                                <div class="mt-3 pt-3 border-top">
                                    <p class="small text-muted mb-0">Остались вопросы? <a href="#" class="text-primary">Посмотрите видеоинструкцию</a></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Вопрос 2 -->
                    <div class="col-lg-6 faq-item" data-category="tech" data-keywords="crm интеграция синхронизация">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-plug text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Можно ли интегрировать сервис с моей CRM-системой?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Да, в планах Бизнес и Премиум доступна интеграция с популярными CRM-системами, включая:</p>
                                <ul class="mt-2 text-muted">
                                    <li>1С</li>
                                    <li>Bitrix24</li>
                                    <li>AmoCRM</li>
                                    <li>Мой Склад</li>
                                </ul>
                                <p class="text-muted">Для нестандартных интеграций обратитесь в нашу службу поддержки.</p>
                                <div class="mt-3 pt-3 border-top">
                                    <p class="small text-muted mb-0">Подробнее: <a href="#" class="text-primary">Документация по API</a></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Вопрос 3 -->
                    <div class="col-lg-6 faq-item" data-category="general" data-keywords="получение документ клиент способы отправка">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-paper-plane text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Как клиенты получают документы?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Мы предлагаем несколько способов доставки документов:</p>
                                <ul class="mt-2 text-muted">
                                    <li>Email рассылка с PDF-файлом</li>
                                    <li>Печать физической версии</li>
                                    <li>QR-код для получения на специальной странице</li>
                                    <li>SMS с кодом активации</li>
                                </ul>
                                <p class="text-muted">Вы можете настроить любой из этих способов или использовать несколько одновременно.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Вопрос 4 -->
                    <div class="col-lg-6 faq-item" data-category="billing" data-keywords="бесплатно тестовый период пробный">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-gift text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Могу ли я попробовать сервис бесплатно?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Да, мы предоставляем бесплатный тестовый период на 14 дней без ограничений функционала. Вы сможете оценить все возможности сервиса и выбрать подходящий тариф.</p>
                                <p class="mt-2 text-muted">Кредитная карта для активации пробного периода не требуется.</p>
                                <div class="mt-3">
                                    <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Начать бесплатный период
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Вопрос 5 -->
                    <div class="col-lg-6 faq-item" data-category="billing" data-keywords="тарифы сменить план апгрейд даунгрейд">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-arrow-up-right-dots text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Могу ли я сменить тарифный план?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Да, вы можете в любое время перейти на более высокий тарифный план. Переход на более низкий план возможен при следующем платежном периоде. Разница в стоимости при апгрейде будет рассчитана пропорционально оставшемуся времени.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Вопрос 6 -->
                    <div class="col-lg-6 faq-item" data-category="tech" data-keywords="данные экспорт импорт безопасность">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-shield-halved text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Как защищены мои данные?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Безопасность ваших данных - наш приоритет. Мы используем шифрование SSL, регулярное резервное копирование и надежные серверы для хранения информации. Соответствуем требованиям GDPR и ФЗ-152. Вы всегда можете экспортировать свои данные или запросить их удаление.</p>
                                <div class="mt-3 pt-3 border-top">
                                    <p class="small text-muted mb-0"><a href="#" class="text-primary">Подробнее о безопасности</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Общие вопросы -->
            <div class="tab-pane fade" id="general" role="tabpanel">
                <div class="row g-4 faq-items">
                    <!-- Повторяем вопросы из категории general -->
                    <div class="col-lg-6 faq-item">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-rocket text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Как быстро я смогу начать использовать сервис?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">После регистрации и заполнения данных о вашей компании вы сможете сразу приступить к созданию документов. Весь процесс занимает обычно не более 15-20 минут. Мы предоставляем подробные инструкции на каждом этапе.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 faq-item">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-paper-plane text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Как клиенты получают документы?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Мы предлагаем несколько способов доставки документов: по email, распечатанную физическую версию, QR-код для получения на специальной странице или SMS с кодом активации.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Вопросы оплаты -->
            <div class="tab-pane fade" id="billing" role="tabpanel">
                <div class="row g-4 faq-items">
                    <!-- Повторяем вопросы из категории billing -->
                    <div class="col-lg-6 faq-item">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-gift text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Могу ли я попробовать сервис бесплатно?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Да, мы предоставляем бесплатный тестовый период на 14 дней без ограничений функционала. Кредитная карта для активации пробного периода не требуется.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 faq-item">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-arrow-up-right-dots text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Могу ли я сменить тарифный план?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Да, вы можете в любое время перейти на более высокий тарифный план. Переход на более низкий план возможен при следующем платежном периоде.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Дополнительный вопрос для этой категории -->
                    <div class="col-lg-6 faq-item">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-money-bill-transfer text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Какие способы оплаты вы принимаете?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Мы принимаем оплату банковскими картами Visa, Mastercard, МИР, а также через PayPal, по счету для юридических лиц и через платежные системы ЮMoney, QIWI и WebMoney.</p>
                                <div class="mt-3 d-flex gap-2">
                                    <img src="https://placehold.co/40x25?text=Visa" alt="Visa" class="img-fluid">
                                    <img src="https://placehold.co/40x25?text=MC" alt="Mastercard" class="img-fluid">
                                    <img src="https://placehold.co/40x25?text=МИР" alt="МИР" class="img-fluid">
                                    <img src="https://placehold.co/40x25?text=PayPal" alt="PayPal" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Технические вопросы -->
            <div class="tab-pane fade" id="tech" role="tabpanel">
                <div class="row g-4 faq-items">
                    <!-- Повторяем вопросы из категории tech -->
                    <div class="col-lg-6 faq-item">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-plug text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Можно ли интегрировать сервис с моей CRM-системой?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Да, в планах Бизнес и Премиум доступна интеграция с популярными CRM-системами, включая 1С, Bitrix24, AmoCRM и другие.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 faq-item">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-shield-halved text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Как защищены мои данные?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Безопасность ваших данных - наш приоритет. Мы используем шифрование SSL, регулярное резервное копирование и надежные серверы для хранения информации.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Дополнительный вопрос для этой категории -->
                    <div class="col-lg-6 faq-item">
                        <div class="faq-card p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-3">
                                <div class="faq-icon-wrapper me-3">
                                    <i class="fa-solid fa-desktop text-primary"></i>
                                </div>
                                <h4 class="mb-0 pt-1">Какие браузеры поддерживаются?</h4>
                                <button class="faq-toggle ms-auto bg-transparent border-0">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="faq-answer ps-5 pt-2" style="display: none;">
                                <p class="mb-0 text-muted">Наш сервис работает во всех современных браузерах, включая Chrome, Firefox, Safari, Edge и Opera. Для оптимальной работы рекомендуется использовать последние версии браузеров.</p>
                                <div class="mt-3 d-flex gap-3">
                                    <i class="fa-brands fa-chrome text-muted"></i>
                                    <i class="fa-brands fa-firefox-browser text-muted"></i>
                                    <i class="fa-brands fa-safari text-muted"></i>
                                    <i class="fa-brands fa-edge text-muted"></i>
                                    <i class="fa-brands fa-opera text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Блок с контактами и поддержкой -->
        <div class="text-center mt-5">
            <div class="mb-4">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-headset me-2"></i>Всегда на связи
                </span>
            </div>
            <p class="mb-4">Не нашли ответа на свой вопрос? Наша команда поддержки готова помочь.</p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="#" class="btn btn-outline-primary rounded-pill px-4 py-2 hover-lift">
                    <i class="fa-solid fa-message me-2"></i> Написать в чат
                </a>
                <a href="#" class="btn btn-outline-primary rounded-pill px-4 py-2 hover-lift">
                    <i class="fa-solid fa-envelope me-2"></i> Отправить email
                </a>
            </div>
        </div>
    </div>

    <!-- Стили для новой секции FAQ -->
    <style>
        .faq-tabs .nav-link {
            border-radius: 50rem;
            color: #495057;
            background-color: rgba(255,255,255,0.7);
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }
        
        .faq-tabs .nav-link:hover {
            background-color: rgba(13,110,253,0.1);
            color: #0d6efd;
        }
        
        .faq-tabs .nav-link.active {
            background-color: rgba(13,110,253,0.1);
            color: #0d6efd;
            font-weight: 600;
            border-color: rgba(13,110,253,0.2);
        }
        
        .faq-card {
            background-color: rgba(255,255,255,0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }
        
        .faq-card:hover {
            transform: translateY(-5px);
            border-color: rgba(13,110,253,0.2);
            box-shadow: 0 10px 20px rgba(13,110,253,0.05);
        }
        
        .faq-icon-wrapper {
            width: 40px;
            height: 40px;
            min-width: 40px;
            background: rgba(13,110,253,0.1);
            color: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        .faq-toggle {
            width: 30px;
            height: 30px;
            min-width: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .faq-toggle:hover {
            background-color: rgba(13,110,253,0.1);
            color: #0d6efd;
        }
        
        .faq-toggle.active i {
            transform: rotate(180deg);
        }
        
        .faq-search-box {
            transition: all 0.3s ease;
        }
        
        .faq-search-box:focus-within {
            box-shadow: 0 5px 15px rgba(13,110,253,0.1) !important;
        }
        
        .faq-search-box input:focus {
            box-shadow: none;
        }
        
        /* Анимация для ответов */
        .faq-answer {
            overflow: hidden;
            transition: all 0.3s ease-in-out;
        }
    </style>

    <!-- JavaScript для функциональности FAQ -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация табов
        var triggerTabList = [].slice.call(document.querySelectorAll('#faqTabs button'));
        triggerTabList.forEach(function(triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);
            
            triggerEl.addEventListener('click', function(event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });
        
        // Функциональность поиска
        const searchInput = document.getElementById('faqSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const faqItems = document.querySelectorAll('.faq-item');
                
                faqItems.forEach(item => {
                    const keywords = item.dataset.keywords ? item.dataset.keywords.toLowerCase() : '';
                    const text = item.textContent.toLowerCase();
                    
                    if (text.includes(searchTerm) || keywords.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
        
        // Функциональность аккордеона
        const faqToggles = document.querySelectorAll('.faq-toggle');
        faqToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const answer = this.closest('.faq-card').querySelector('.faq-answer');
                
                if (this.classList.contains('active')) {
                    this.classList.remove('active');
                    answer.style.display = 'none';
                } else {
                    this.classList.add('active');
                    answer.style.display = 'block';
                }
            });
        });
    });
    </script>
</section>
