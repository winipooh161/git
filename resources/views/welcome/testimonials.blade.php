<!-- Отзывы клиентов - новый дизайн -->
<section class="position-relative py-5" id="testimonials">
    <!-- Контейнер для блобов -->
    <div class="blob-container">
        <div class="blob blob-1" style="left: 10%; top: 20%; background: linear-gradient(45deg, #20c997, #0dcaf0);"></div>
        <div class="blob blob-2" style="right: 5%; bottom: 20%; background: linear-gradient(45deg, #e83e8c, #fd7e14);"></div>
    </div>
    
    <div class="container px-4">
        <div class="text-center mb-5">
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill mb-3">
                <i class="fa-solid fa-comments me-2"></i>Отзывы
            </span>
            <h2 class="display-5 fw-bold mb-3">Истории успеха наших клиентов</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Узнайте, как сервис помогает предпринимателям увеличивать продажи и повышать лояльность клиентов</p>
        </div>
        
        <!-- Фильтр отзывов -->
        <div class="d-flex justify-content-center mb-5 testimonial-tabs">
            <ul class="nav nav-pills" id="testimonialTabs" role="tablist">
                <li class="nav-item mx-2" role="presentation">
                    <button class="nav-link active px-4 py-2" id="all-tab" data-bs-toggle="pill" data-bs-target="#all" type="button" role="tab">
                        <i class="fa-solid fa-star me-2"></i>Все отзывы
                    </button>
                </li>
                <li class="nav-item mx-2" role="presentation">
                    <button class="nav-link px-4 py-2" id="beauty-tab" data-bs-toggle="pill" data-bs-target="#beauty" type="button" role="tab">
                        <i class="fa-solid fa-spa me-2"></i>Бьюти
                    </button>
                </li>
                <li class="nav-item mx-2" role="presentation">
                    <button class="nav-link px-4 py-2" id="food-tab" data-bs-toggle="pill" data-bs-target="#food" type="button" role="tab">
                        <i class="fa-solid fa-utensils me-2"></i>Рестораны
                    </button>
                </li>
                <li class="nav-item mx-2" role="presentation">
                    <button class="nav-link px-4 py-2" id="education-tab" data-bs-toggle="pill" data-bs-target="#education" type="button" role="tab">
                        <i class="fa-solid fa-graduation-cap me-2"></i>Образование
                    </button>
                </li>
            </ul>
        </div>
        
        <!-- Контент с отзывами -->
        <div class="tab-content" id="testimonialTabsContent">
            <!-- Все отзывы -->
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <div class="row g-4">
                    <!-- Отзыв 1 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <!-- Рейтинг -->
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                </div>
                            </div>
                            
                            <!-- Текст отзыва -->
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"документы стали настоящим хитом продаж в нашем салоне! Теперь 30% нашей выручки приходится на подарочные документы."</p>
                            </div>
                            
                            <!-- Информация о клиенте -->
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=А.К." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Анна К.</h5>
                                    <p class="text-muted small mb-0">Владелица салона красоты</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-primary rounded-pill">
                                        <i class="fa-solid fa-spa me-1"></i> Бьюти
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Декоративный элемент -->
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Отзыв 2 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <!-- Рейтинг -->
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star-half-stroke text-warning"></i>
                                </div>
                            </div>
                            
                            <!-- Текст отзыва -->
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"Мы начали предлагать подарочные документы на наши курсы, и это помогло привлечь новых клиентов. Удобно, что можно установить разные условия."</p>
                            </div>
                            
                            <!-- Информация о клиенте -->
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=М.П." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Михаил П.</h5>
                                    <p class="text-muted small mb-0">Директор школы танцев</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-info rounded-pill">
                                        <i class="fa-solid fa-graduation-cap me-1"></i> Образование
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Декоративный элемент -->
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Отзыв 3 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <!-- Рейтинг -->
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                </div>
                            </div>
                            
                            <!-- Текст отзыва -->
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"Продажи документов выросли на 150% во время праздников благодаря красивым шаблонам и удобной системе учета."</p>
                            </div>
                            
                            <!-- Информация о клиенте -->
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=Е.В." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Екатерина В.</h5>
                                    <p class="text-muted small mb-0">Управляющая рестораном</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-warning rounded-pill">
                                        <i class="fa-solid fa-utensils me-1"></i> Рестораны
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Декоративный элемент -->
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Отзыв 4 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <!-- Рейтинг -->
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                </div>
                            </div>
                            
                            <!-- Текст отзыва -->
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"Каждый третий клиент теперь выбирает документ в подарок. Спасибо за интеграцию с нашей системой учета, всё работает безупречно!"</p>
                            </div>
                            
                            <!-- Информация о клиенте -->
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=Д.М." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Дмитрий М.</h5>
                                    <p class="text-muted small mb-0">Владелец барбершопа</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-primary rounded-pill">
                                        <i class="fa-solid fa-spa me-1"></i> Бьюти
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Декоративный элемент -->
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Отзыв 5 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <!-- Рейтинг -->
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-regular fa-star text-warning"></i>
                                </div>
                            </div>
                            
                            <!-- Текст отзыва -->
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"Запустили документы перед новогодними праздниками. Результат превзошёл все ожидания! Система проста и интуитивно понятна."</p>
                            </div>
                            
                            <!-- Информация о клиенте -->
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=И.С." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Ирина С.</h5>
                                    <p class="text-muted small mb-0">Директор сети кондитерских</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-warning rounded-pill">
                                        <i class="fa-solid fa-utensils me-1"></i> Рестораны
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Декоративный элемент -->
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Отзыв 6 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <!-- Рейтинг -->
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                </div>
                            </div>
                            
                            <!-- Текст отзыва -->
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"Наши студенты могут дарить обучение своим друзьям. Это привлекло новых клиентов и увеличило средний чек. Рекомендую!"</p>
                            </div>
                            
                            <!-- Информация о клиенте -->
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=А.Р." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Алексей Р.</h5>
                                    <p class="text-muted small mb-0">Руководитель учебного центра</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-info rounded-pill">
                                        <i class="fa-solid fa-graduation-cap me-1"></i> Образование
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Декоративный элемент -->
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Фильтрованные отзывы для Бьюти -->
            <div class="tab-pane fade" id="beauty" role="tabpanel" aria-labelledby="beauty-tab">
                <div class="row g-4">
                    <!-- Отзыв 1 -->
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <!-- Контент отзыва -->
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                </div>
                            </div>
                            
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"документы стали настоящим хитом продаж в нашем салоне! Теперь 30% нашей выручки приходится на подарочные документы."</p>
                            </div>
                            
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=А.К." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Анна К.</h5>
                                    <p class="text-muted small mb-0">Владелица салона красоты</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-primary rounded-pill">
                                        <i class="fa-solid fa-spa me-1"></i> Бьюти
                                    </span>
                                </div>
                            </div>
                            
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Отзыв 4 (копия из All) -->
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                </div>
                            </div>
                            
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"Каждый третий клиент теперь выбирает документ в подарок. Спасибо за интеграцию с нашей системой учета, всё работает безупречно!"</p>
                            </div>
                            
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=Д.М." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Дмитрий М.</h5>
                                    <p class="text-muted small mb-0">Владелец барбершопа</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-primary rounded-pill">
                                        <i class="fa-solid fa-spa me-1"></i> Бьюти
                                    </span>
                                </div>
                            </div>
                            
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Фильтрованные отзывы для Рестораны -->
            <div class="tab-pane fade" id="food" role="tabpanel" aria-labelledby="food-tab">
                <!-- Копии отзывов 3 и 5 -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                </div>
                            </div>
                            
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"Продажи документов выросли на 150% во время праздников благодаря красивым шаблонам и удобной системе учета. Клиенты в восторге от дизайна!"</p>
                            </div>
                            
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=Е.В." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Екатерина В.</h5>
                                    <p class="text-muted small mb-0">Управляющая рестораном</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-warning rounded-pill">
                                        <i class="fa-solid fa-utensils me-1"></i> Рестораны
                                    </span>
                                </div>
                            </div>
                            
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-regular fa-star text-warning"></i>
                                </div>
                            </div>
                            
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"Запустили документы перед новогодними праздниками. Результат превзошёл все ожидания! Система проста и интуитивно понятна."</p>
                            </div>
                            
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=И.С." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Ирина С.</h5>
                                    <p class="text-muted small mb-0">Директор сети кондитерских</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-warning rounded-pill">
                                        <i class="fa-solid fa-utensils me-1"></i> Рестораны
                                    </span>
                                </div>
                            </div>
                            
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Фильтрованные отзывы для Образование -->
            <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="education-tab">
                <!-- Копии отзывов 2 и 6 -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star-half-stroke text-warning"></i>
                                </div>
                            </div>
                            
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"Мы начали предлагать подарочные документы на наши курсы, и это помогло привлечь новых клиентов. Удобно, что можно установить разные условия."</p>
                            </div>
                            
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=М.П." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Михаил П.</h5>
                                    <p class="text-muted small mb-0">Директор школы танцев</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-info rounded-pill">
                                        <i class="fa-solid fa-graduation-cap me-1"></i> Образование
                                    </span>
                                </div>
                            </div>
                            
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="testimonial-card-new p-4 rounded-4 h-100 position-relative">
                            <div class="d-flex mb-4">
                                <div class="testimonial-rating">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                </div>
                            </div>
                            
                            <div class="testimonial-text mb-4">
                                <p class="fs-5 fw-light fst-italic mb-0">"Наши студенты могут дарить обучение своим друзьям. Это привлекло новых клиентов и увеличило средний чек. Рекомендую!"</p>
                            </div>
                            
                            <div class="d-flex align-items-center mt-auto">
                                <div class="testimonial-avatar-wrapper me-3">
                                    <img src="https://placehold.co/150?text=А.Р." class="testimonial-avatar" alt="Фото клиента">
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Алексей Р.</h5>
                                    <p class="text-muted small mb-0">Руководитель учебного центра</p>
                                </div>
                                <div class="ms-auto industry-badge">
                                    <span class="badge bg-soft-info rounded-pill">
                                        <i class="fa-solid fa-graduation-cap me-1"></i> Образование
                                    </span>
                                </div>
                            </div>
                            
                            <div class="testimonial-quote-icon">
                                <i class="fa-solid fa-quote-left"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="#" class="btn btn-outline-primary rounded-pill px-4 py-2">
                <i class="fa-solid fa-plus me-2"></i> Больше историй успеха
            </a>
        </div>
    </div>
    
    <!-- CSS стили для нового блока отзывов -->
    <style>
        .testimonial-tabs .nav-link {
            border-radius: 50rem;
            color: #495057;
            background-color: rgba(255,255,255,0.7);
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }
        
        .testimonial-tabs .nav-link:hover {
            background-color: rgba(13,110,253,0.1);
            color: #0d6efd;
        }
        
        .testimonial-tabs .nav-link.active {
            background-color: rgba(13,110,253,0.1);
            color: #0d6efd;
            font-weight: 600;
            border-color: rgba(13,110,253,0.2);
        }
        
        .testimonial-card-new {
            background-color: rgba(255,255,255,0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .testimonial-card-new:hover {
            transform: translateY(-10px);
            border-color: rgba(13,110,253,0.2);
            box-shadow: 0 15px 35px rgba(13,110,253,0.1);
        }
        
        .testimonial-avatar-wrapper {
            width: 50px;
            height: 50px;
            overflow: hidden;
            border-radius: 50%;
            border: 2px solid rgba(13,110,253,0.1);
            background: #f8f9fa;
        }
        
        .testimonial-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .testimonial-quote-icon {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 2rem;
            opacity: 0.1;
            color: #0d6efd;
        }
        
        .bg-soft-primary {
            background-color: rgba(13,110,253,0.1);
            color: #0d6efd;
        }
        
        .bg-soft-warning {
            background-color: rgba(255,193,7,0.1);
            color: #ffc107;
        }
        
        .bg-soft-info {
            background-color: rgba(13,202,240,0.1);
            color: #0dcaf0;
        }
        
        .industry-badge .badge {
            font-weight: 500;
            padding: 0.5rem 0.8rem;
        }
    </style>
    
    <!-- Скрипт для инициализации табов -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация табов
        var triggerTabList = [].slice.call(document.querySelectorAll('#testimonialTabs button'));
        triggerTabList.forEach(function(triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);
            
            triggerEl.addEventListener('click', function(event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });
    });
    </script>
</section>
