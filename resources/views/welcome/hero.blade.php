<!-- Героический баннер -->
<header class="py-5 position-relative">
    <!-- Контейнер для блобов -->
    <div class="blob-container">
      
        <div class="blob blob-2"></div>
    </div>
    
    <div class="container px-4 py-5">
        <div class="row gx-5 align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h1 class="display-4 fw-bold mb-4 gradient-text">Создавайте документы для клиентов с легкостью</h1>
                <p class="lead mb-5">Наш сервис поможет вашему бизнесу предлагать подарочные документы, 
                увеличивать продажи и повышать лояльность клиентов.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg hover-lift">
                        <i class="fa-solid fa-rocket me-2"></i> Начать бесплатно
                    </a>
                    <a href="#how-it-works" class="btn btn-outline-secondary btn-lg hover-lift">
                        <i class="ri-information-line me-2"></i> Узнать больше
                    </a>
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-center">
                <div class="position-relative" style="width: 90%;">
                    <img src="https://placehold.co/600x400?text=документы" class="img-fluid rounded-4 hover-lift" alt="Примеры документов" style="border-radius: var(--bs-border-radius-xl);">
                    <div class="position-absolute" style="top: -20px; right: -20px; z-index: -1;">
                        <div class="p-4 bg-white rounded-4" style="border-radius: var(--bs-border-radius-xl);"></div>
                    </div>
                    <div class="position-absolute" style="bottom: -20px; left: -20px; z-index: -1;">
                        <div class="p-4 bg-primary rounded-4 opacity-10" style="border-radius: var(--bs-border-radius-xl);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
