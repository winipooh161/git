<div class="input-group">
    <span class="input-group-text bg-light border-end-0">
        <i class="fa-solid fa-search text-muted"></i>
    </span>
    <input type="text" id="search-certificate" class="form-control border-start-0"
        placeholder="Поиск по имени или телефону" autocomplete="off">
    <button type="button" id="clear-search" class="btn btn-outline-secondary d-none">
        <i class="fa-solid fa-times"></i>
    </button>
</div>
<div id="search-type-indicator" class="small text-muted mt-1"></div>

<!-- Контейнер для результатов поиска -->
<div id="search-results" class="mb-4 d-none">
    <h3 class="fs-5 fw-bold mb-3">Результаты поиска</h3>
    <div id="search-results-container" class="row row-cols-1 row-cols-sm-2 g-3">
        <!-- Сюда будут добавляться результаты поиска -->
    </div>
    <div id="no-results-message" class="text-center py-4 d-none">
        <i class="fa-solid fa-search text-muted fa-2x mb-2"></i>
        <p class="text-muted">документы не найдены</p>
    </div>
</div>

<!-- Индикатор загрузки -->
<div id="loading-indicator" class="text-center py-3 d-none">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Загрузка...</span>
    </div>
</div>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/_search.blade.php ENDPATH**/ ?>