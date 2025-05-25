<!-- Тарифные планы -->
<section class="position-relative" id="pricing">
    <!-- Контейнер для блобов -->
    <div class="blob-container">
        <div class="blob blob-1" style="right: 10%; top: 15%; background: linear-gradient(45deg, #20c997, #0dcaf0);"></div>
        <div class="blob blob-2" style="left: 5%; bottom: 10%; background: linear-gradient(45deg, #6f42c1, #e83e8c);"></div>
    </div>
    
    <div class="container px-4">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">Тарифы</span>
            <h2 class="display-5 fw-bold mb-3">Выберите подходящий план</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Гибкие тарифы для бизнеса любого размера</p>
        </div>
        
        <div class="row g-4 pricing-cards">
            <!-- Стартап -->
            <div class="col-lg-4">
                <div class="bg-transparent p-0 rounded-4 h-100 position-relative pricing-card">
                    <div class="p-4 pt-5">
                        <div class="text-center mb-4">
                            <h3 class="h4 mb-3">Стартап</h3>
                            <div class="pricing-value">
                                <span class="display-5 fw-bold">₽1,000</span>
                                <span class="text-muted">/мес</span>
                            </div>
                        </div>
                        
                        <ul class="list-unstyled pricing-features">
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                До 50 документов в месяц
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                5 шаблонов на выбор
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                Базовая аналитика
                            </li>
                            <li class="d-flex align-items-center mb-3 text-muted">
                                <div class="rounded-circle bg-light p-2 me-3">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                Персональный дизайн
                            </li>
                            <li class="d-flex align-items-center mb-3 text-muted">
                                <div class="rounded-circle bg-light p-2 me-3">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                CRM-интеграция
                            </li>
                        </ul>
                    </div>
                    
                    <div class="p-4 text-center mt-2">
                        <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill px-5 py-3 hover-lift">Выбрать план</a>
                    </div>
                </div>
            </div>
            
            <!-- Бизнес -->
            <div class="col-lg-4">
                <div class="bg-transparent p-0 rounded-4 h-100 position-relative pricing-card pricing-card-featured">
                    <div class="position-absolute top-0 start-50 translate-middle">
                        <span class="badge bg-warning rounded-pill px-3 py-2">Популярный</span>
                    </div>
                    
                    <div class="p-4 pt-5">
                        <div class="text-center mb-4">
                            <h3 class="h4 mb-3">Бизнес</h3>
                            <div class="pricing-value">
                                <span class="display-5 fw-bold">₽3,000</span>
                                <span class="text-muted">/мес</span>
                            </div>
                        </div>
                        
                        <ul class="list-unstyled pricing-features">
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                До 200 документов в месяц
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                15 шаблонов на выбор
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                Расширенная аналитика
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                Персональный дизайн (1 шаблон)
                            </li>
                            <li class="d-flex align-items-center mb-3 text-muted">
                                <div class="rounded-circle bg-light p-2 me-3">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                CRM-интеграция
                            </li>
                        </ul>
                    </div>
                    
                    <div class="p-4 text-center mt-2">
                        <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-5 py-3 hover-lift">Выбрать план</a>
                    </div>
                </div>
            </div>
            
            <!-- Премиум -->
            <div class="col-lg-4">
                <div class="bg-transparent p-0 rounded-4 h-100 position-relative pricing-card">
                    <div class="p-4 pt-5">
                        <div class="text-center mb-4">
                            <h3 class="h4 mb-3">Премиум</h3>
                            <div class="pricing-value">
                                <span class="display-5 fw-bold">₽7,000</span>
                                <span class="text-muted">/мес</span>
                            </div>
                        </div>
                        
                        <ul class="list-unstyled pricing-features">
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                Безлимитные документы
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                Все шаблоны
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                Полная аналитика с отчетами
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                Персональный дизайн (до 5 шаблонов)
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                    <i class="fa-solid fa-check text-success"></i>
                                </div>
                                CRM-интеграция
                            </li>
                        </ul>
                    </div>
                    
                    <div class="p-4 text-center mt-2">
                        <a href="{{ route('register') }}" class="btn btn-outline-primary rounded-pill px-5 py-3 hover-lift">Выбрать план</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <p class="text-muted">Нужен индивидуальный план? <a href="#" class="text-primary">Свяжитесь с нами</a></p>
        </div>
    </div>
</section>
