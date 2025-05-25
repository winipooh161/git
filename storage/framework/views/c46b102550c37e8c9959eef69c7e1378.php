<style>
    /* Стили для карточек документов */
    .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
    }

    .certificate-card {
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 0;
    }

    .card-link:hover .certificate-card {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    /* Стили для постоянной визуализации сетки */
    .grid-container {
        position: relative;
        min-height: 200px; /* Минимальная высота для контейнера */
    }

    /* Улучшенная визуализация сетки */
    .grid-visual {
        position: relative;
        background-image: linear-gradient(rgba(13, 110, 253, 0.05) 1px, transparent 1px),
                          linear-gradient(90deg, rgba(13, 110, 253, 0.05) 1px, transparent 1px);
        background-size: 20px 20px;
        background-position: -1px -1px;
        padding: 10px;
    }

    /* Добавляем рамку вокруг всей сетки */
    .grid-visual:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 1px dashed rgba(13, 110, 253, 0.3);
        pointer-events: none;
    }

    /* Усиливаем отображение границ каждой ячейки */
    .grid-visual .grid-cell {
        position: relative;
        padding: 5px;
    }

    .grid-visual .grid-cell::after {
        content: "";
        position: absolute;
        top: 5px;
        left: 5px;
        right: 5px;
        bottom: 5px;
        border: 1px dashed rgba(13, 110, 253, 0.2);
        border-radius: 4px;
        pointer-events: none;
        z-index: 1;
    }

    /* Стили для пустых карточек */
    .empty-card {
        opacity: 0.5;
        border: 1px dashed #dee2e6 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }

    .empty-card:hover {
        transform: none !important;
    }
    
    /* Номера ячеек в сетке для лучшей отладки */
    .grid-cell {
        position: relative;
    }
    .grid-cell:hover::before {
        content: attr(data-cell-number);
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        z-index: 10;
    }
</style>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/_styles.blade.php ENDPATH**/ ?>