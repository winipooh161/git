<!-- Призыв к действию -->
<section class="position-relative overflow-visible py-6" id="cta">
    <!-- Контейнер для блобов с увеличенной высотой -->
    <div class="blob-container" style="top: -50px; bottom: -50px; height: calc(100% + 100px);">
        <div class="blob blob-1" style="left: 5%; top: 15%; background: linear-gradient(45deg, #6f42c1, #4e73df);"></div>
        <div class="blob blob-2" style="right: 10%; bottom: 5%; background: linear-gradient(45deg, #fd7e14, #e83e8c);"></div>
    </div>
    
    <div class="container px-4 py-5 position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-transparent rounded-4 p-5 text-center position-relative overflow-hidden cta-card">
                    <div class="position-relative z-2">
                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill mb-3">Начните сейчас</span>
                        <h2 class="display-5 fw-bold mb-3 gradient-text">Готовы создавать документы?</h2>
                        <p class="lead mb-5">Зарегистрируйтесь сегодня и получите доступ к бесплатному плану на 14 дней!</p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 py-3 hover-lift">Начать бесплатно</a>
                            <a href="#" class="btn btn-outline-secondary btn-lg px-4 py-3 hover-lift">Запросить демо</a>
                        </div>
                    </div>
                    
                    <!-- Декоративные элементы внутри карточки -->
                    <div class="position-absolute rounded-circle bg-primary bg-opacity-5" 
                         style="width: 300px; height: 300px; bottom: -150px; right: -150px; z-index: 0;"></div>
                    <div class="position-absolute rounded-circle bg-info bg-opacity-5" 
                         style="width: 200px; height: 200px; top: -100px; left: -100px; z-index: 0;"></div>
                </div>
            </div>
        </div>
    </div>
</section>
