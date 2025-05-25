<div class="modal fade" id="animationEffectsModal" tabindex="-1" aria-labelledby="animationEffectsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="animationEffectsModalLabel">Выбор анимационного эффекта</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <!-- Фильтр для эффектов -->
                <div class="mb-3 d-lg-flex d-none align-items-center">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fa-solid fa-filter text-muted"></i>
                        </span>
                        <input type="text" class="form-control form-control-sm ps-0 border-start-0" id="effectFilter" placeholder="Поиск по эффектам">
                    </div>
                </div>
                
                <!-- Упрощенный выбор категорий для мобильных устройств -->
                <div class="mb-3 d-md-none">
                    <select class="form-select form-select-sm" id="effectCategoryFilter">
                        <option value="all">Все типы эффектов</option>
                        <option value="emoji">Эмодзи</option>
                        <option value="confetti">Конфетти</option>
                        <option value="snow">Снег</option>
                        <option value="fireworks">Фейерверк</option>
                        <option value="bubbles">Пузыри</option>
                        <option value="leaves">Листья</option>
                        <option value="stars">Звёзды</option>
                    </select>
                </div>
                
                <!-- Сетка эффектов с оптимизированным размером для мобильных -->
                <div class="row g-2 g-md-3" id="effectsList">
                    <div class="col-12 text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Загрузка...</span>
                        </div>
                        <p class="mt-2">Загрузка доступных эффектов...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <span class="text-muted small d-none d-md-inline">Нажмите на эффект для выбора</span>
                <div>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-sm btn-primary" id="selectEffectButton" disabled data-bs-dismiss="modal">Выбрать эффект</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Стили для оптимизации модального окна на мобильных устройствах */
@media (max-width: 767.98px) {
    .effect-card {
        margin-bottom: 0.5rem;
    }
    
    .effect-card .card-body {
        padding: 0.75rem;
    }
    
    .effect-card .card-footer {
        padding: 0.5rem;
    }
    
    .particles-preview {
        height: 30px;
        margin: 5px 0;
    }
    
    /* Убираем кнопки выбора на карточках на мобильных устройствах */
    .select-effect-btn-mobile {
        display: none;
    }
    
    /* Делаем всю карточку кликабельной */
    .effect-card {
        cursor: pointer;
    }
}
</style><?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/animation_effects_modal.blade.php ENDPATH**/ ?>