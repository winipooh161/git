<footer class="bg-dark text-white py-5 position-relative overflow-hidden">
    <!-- Декоративные элементы -->
    <div class="position-absolute top-0 start-0 translate-middle-x">
        <div class="footer-blob" style="width: 500px; height: 500px; background: linear-gradient(45deg, #4e73df, #6610f2); opacity: 0.05; border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;"></div>
    </div>
    <div class="position-absolute bottom-0 end-0">
        <div class="footer-blob" style="width: 400px; height: 400px; background: linear-gradient(45deg, #fd7e14, #e83e8c); opacity: 0.05; border-radius: 67% 33% 30% 70% / 38% 64% 36% 62%;"></div>
    </div>
    
    <div class="container position-relative">
        <div class="row g-4 mb-5">
            <!-- Информация о компании -->
            <div class="col-lg-5 col-md-6">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white p-2 rounded-3 me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-certificate text-primary fs-4"></i>
                    </div>
                    <h5 class="mb-0 text-white fs-4 fw-bold">{{ config('app.name', 'Laravel') }}</h5>
                </div>
                <p class="text-white-50 mb-4">Современная платформа для создания, продажи и управления подарочными документами для вашего бизнеса.</p>
                
                <!-- Контактная информация -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3 text-primary"><i class="fa-regular fa-envelope"></i></div>
                        <span class="text-white-50">support@example.com</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3 text-primary"><i class="fa-solid fa-phone"></i></div>
                        <span class="text-white-50">+7 (999) 123-45-67</span>
                    </div>
                </div>
                
                <!-- Социальные сети -->
                <div class="d-flex social-links">
                    <a href="#" class="social-icon" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="social-icon" title="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#" class="social-icon" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="social-icon" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#" class="social-icon" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            
            <!-- Навигация и ссылки -->
            <div class="col-lg-4 col-md-6">
                <div class="row">
                    <div class="col-6">
                        <h5 class="footer-heading text-white mb-4">Навигация</h5>
                        <ul class="list-unstyled footer-links">
                            <li class="mb-2"><a href="#" class="footer-link"><i class="ri-home-4-line small me-2"></i>Главная</a></li>
                            <li class="mb-2"><a href="#benefits" class="footer-link"><i class="ri-award-line small me-2"></i>Преимущества</a></li>
                            <li class="mb-2"><a href="#pricing" class="footer-link"><i class="ri-price-tag-3-line small me-2"></i>Тарифы</a></li>
                            <li class="mb-2"><a href="#faq" class="footer-link"><i class="ri-question-line small me-2"></i>FAQ</a></li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <h5 class="footer-heading text-white mb-4">Поддержка</h5>
                        <ul class="list-unstyled footer-links">
                            <li class="mb-2"><a href="#" class="footer-link"><i class="ri-question-answer-line me-2"></i>Помощь</a></li>
                            <li class="mb-2"><a href="#" class="footer-link"><i class="ri-file-text-line me-2"></i>Документация</a></li>
                            <li class="mb-2"><a href="#" class="footer-link"><i class="ri-customer-service-2-line me-2"></i>Контакты</a></li>
                            <li class="mb-2"><a href="#" class="footer-link"><i class="ri-graduation-cap-line me-2"></i>Обучение</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Подписка на новости -->
            <div class="col-lg-3">
                <h5 class="footer-heading text-white mb-4">Подписка на новости</h5>
                <p class="text-white-50 mb-3">Будьте в курсе обновлений и новых возможностей сервиса</p>
                
                <form class="newsletter-form mb-3">
                    <div class="input-group">
                        <input type="email" class="form-control bg-transparent text-white border-end-0 rounded-pill-start" placeholder="Ваш email">
                        <button type="submit" class="btn btn-primary rounded-pill-end">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
                
                <div>
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-1 me-1 p-2">
                        <i class="fa-solid fa-bolt me-1"></i> Быстро
                    </span>
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-1 me-1 p-2">
                        <i class="fa-solid fa-shield me-1"></i> Надежно
                    </span>
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-1 p-2">
                        <i class="fa-solid fa-lock me-1"></i> Безопасно
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Нижняя часть футера -->
        <hr class="border-secondary opacity-25">
        <div class="row py-3 align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-md-0 text-white-50 small">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Все права защищены.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item"><a href="#" class="footer-link small">Политика конфиденциальности</a></li>
                    <li class="list-inline-item text-white-50 small">•</li>
                    <li class="list-inline-item"><a href="#" class="footer-link small">Условия использования</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>