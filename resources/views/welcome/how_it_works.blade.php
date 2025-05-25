<!-- Как это работает -->
<section class="position-relative" id="how-it-works">
    <!-- Контейнер для блобов -->
    <div class="blob-container">
        <div class="blob blob-1" style="left: 5%; top: 20%;"></div>
        <div class="blob blob-2" style="right: 5%; bottom: 10%;"></div>
    </div>
    
    <div class="container px-4">
        <div class="text-center mb-5">
            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill mb-3">Процесс</span>
            <h2 class="display-5 fw-bold mb-3">Как это работает</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Всего 4 простых шага для запуска документов в вашем бизнесе</p>
        </div>

        <div class="position-relative mt-5">
            <!-- Линия процесса -->
            <div class="position-absolute d-none d-md-block" style="top: 30px; bottom: 0; left: 50%; width: 2px; background: linear-gradient(to bottom, #e9ecef 0%, #4e73df 100%);"></div>
            
            <!-- Шаг 1 -->
            <div class="row mb-5">
                <div class="col-md-5 text-md-end">
                    <div class=" p-4 rounded-4 h-100 hover-lift">
                        <h3 class="h4 mb-3">Регистрация и создание аккаунта</h3>
                        <p class="text-muted mb-0">Зарегистрируйтесь на платформе и настройте профиль вашей компании с логотипом и информацией о бизнесе</p>
                    </div>
                </div>
                <div class="col-md-2 text-center">
                    <div class="step-icon rounded-circle d-inline-flex justify-content-center align-items-center bg-white border border-3 border-primary mb-3" style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold; color: #4e73df; z-index: 1; position: relative;">1</div>
                </div>
                <div class="col-md-5 d-none d-md-block">
                    <img src="https://placehold.co/400x250?text=Регистрация" class="img-fluid rounded-4 hover-lift" alt="Регистрация">
                </div>
            </div>
            
            <!-- Шаг 2 -->
            <div class="row mb-5">
                <div class="col-md-5 d-none d-md-block">
                    <img src="https://placehold.co/400x250?text=Выбор+шаблона" class="img-fluid rounded-4 hover-lift" alt="Выбор шаблона">
                </div>
                <div class="col-md-2 text-center">
                    <div class="step-icon rounded-circle d-inline-flex justify-content-center align-items-center bg-white border border-3 border-primary mb-3" style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold; color: #4e73df; z-index: 1; position: relative;">2</div>
                </div>
                <div class="col-md-5">
                    <div class=" p-4 rounded-4 h-100 hover-lift">
                        <h3 class="h4 mb-3">Выберите шаблон документа</h3>
                        <p class="text-muted mb-0">Выберите дизайн из библиотеки шаблонов или создайте свой собственный стиль документов</п>
                    </div>
                </div>
            </div>
            
            <!-- Шаг 3 -->
            <div class="row mb-5">
                <div class="col-md-5 text-md-end">
                    <div class=" p-4 rounded-4 h-100 hover-lift">
                        <h3 class="h4 mb-3">Настройте условия документов</h3>
                        <p class="text-muted mb-0">Задайте сумму, срок действия, условия использования и другие параметры для ваших документов</п>
                    </div>
                </div>
                <div class="col-md-2 text-center">
                    <div class="step-icon rounded-circle d-inline-flex justify-content-center align-items-center bg-white border border-3 border-primary mb-3" style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold; color: #4e73df; z-index: 1; position: relative;">3</div>
                </div>
                <div class="col-md-5 d-none d-md-block">
                    <img src="https://placehold.co/400x250?text=Настройка+условий" class="img-fluid rounded-4 hover-lift" alt="Настройка условий">
                </div>
            </div>
            
            <!-- Шаг 4 -->
            <div class="row">
                <div class="col-md-5 d-none d-md-block">
                    <img src="https://placehold.co/400x250?text=Продажи" class="img-fluid rounded-4 hover-lift" alt="Продажи и отслеживание">
                </div>
                <div class="col-md-2 text-center">
                    <div class="step-icon rounded-circle d-inline-flex justify-content-center align-items-center bg-white border border-3 border-primary mb-3" style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold; color: #4e73df; z-index: 1; position: relative;">4</div>
                </div>
                <div class="col-md-5">
                    <div class=" p-4 rounded-4 h-100 hover-lift">
                        <h3 class="h4 mb-3">Начните продавать и отслеживать</h3>
                        <p class="text-muted mb-0">Продавайте документы онлайн или офлайн и отслеживайте их использование через вашу персональную панель</п>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-pill">Попробовать бесплатно</a>
        </div>
    </div>
</section>
