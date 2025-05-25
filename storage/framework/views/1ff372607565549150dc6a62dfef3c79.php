<style>
/* Стили для отметки времени на карточке документа */
.certificate-time-badge {
    position: absolute;
    bottom: 10px;
    left: 10px;max-width: 77px;
    background: rgba(0, 0, 0, 0.5);
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.8rem;
}

/* Стили для группировки документов по датам */
.date-group-heading {
    padding-left: 0.5rem;
    border-left: 3px solid var(--bs-primary);
}

/* Улучшаем стили для итоговой карточки */
.stats-card {
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stat-box {
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: scale(1.05);
}

/* Дополнительные стили для правильного отображения сетки */
@media (max-width: 575.98px) {
    .row-cols-1 > .col {
        flex: 0 0 auto;
        width: 100%;
    }
}

/* Исправление для некоторых браузеров, где flex-box может работать некорректно */
.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: calc(var(--bs-gutter-x) * -.5);
    margin-left: calc(var(--bs-gutter-x) * -.5);
}

.col {
    padding-right: calc(var(--bs-gutter-x) * .5);
    padding-left: calc(var(--bs-gutter-x) * .5);
    margin-top: var(--bs-gutter-y);
}
</style>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/user/certificates/partials/_styles.blade.php ENDPATH**/ ?>