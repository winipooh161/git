<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('items', [
            ['title' => 'Аналитика', 'url' => '#'],
            ['title' => 'Отчеты', 'url' => '#']
        ]); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php $__env->startComponent('components.page-header'); ?>
        <?php $__env->slot('title', 'Отчеты'); ?>
        <?php $__env->slot('subtitle', 'Анализ эффективности ваших документов'); ?>
        <?php $__env->slot('actions'); ?>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-download me-2"></i>Экспорт
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-file-pdf me-2 text-danger"></i>Экспорт в PDF</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-file-excel me-2 text-success"></i>Экспорт в Excel</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-file-csv me-2 text-primary"></i>Экспорт в CSV</a></li>
                </ul>
            </div>
            <button class="btn btn-primary ms-2" id="printReport">
                <i class="fa-solid fa-print me-2"></i>Печать
            </button>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>
    
    <!-- Фильтры отчета -->
    <?php $__env->startComponent('components.card', ['class' => 'mb-4']); ?>
        <form id="reportFiltersForm" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="dateRange" class="form-label">Период</label>
                <select id="dateRange" class="form-select">
                    <option value="7">Последние 7 дней</option>
                    <option value="30" selected>Последние 30 дней</option>
                    <option value="90">Последние 3 месяца</option>
                    <option value="180">Последние 6 месяцев</option>
                    <option value="365">Последний год</option>
                    <option value="custom">Произвольный период</option>
                </select>
            </div>
            
            <div class="col-md-3" id="customDateContainer" style="display: none;">
                <label for="startDate" class="form-label">С даты</label>
                <input type="date" id="startDate" class="form-control" value="<?php echo e(date('Y-m-d', strtotime('-30 days'))); ?>">
            </div>
            
            <div class="col-md-3" id="customDateEndContainer" style="display: none;">
                <label for="endDate" class="form-label">По дату</label>
                <input type="date" id="endDate" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
            </div>
            
            <div class="col-md-3">
                <label for="groupBy" class="form-label">Группировка</label>
                <select id="groupBy" class="form-select">
                    <option value="day">По дням</option>
                    <option value="week">По неделям</option>
                    <option value="month" selected>По месяцам</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-filter me-2"></i>Применить
                </button>
            </div>
        </form>
    <?php echo $__env->renderComponent(); ?>
    
    <div class="row g-4">
        <!-- Основные показатели -->
        <div class="col-12">
            <div class="row g-4">
                <div class="col-md-3">
                    <?php $__env->startComponent('components.card', ['hover' => true]); ?>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fa-solid fa-certificate text-primary fa-lg"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Всего документов</h6>
                                <h3 class="mb-0">125</h3>
                                <div class="small text-success">
                                    <i class="fa-solid fa-arrow-up me-1"></i> +12% к прошлому периоду
                                </div>
                            </div>
                        </div>
                    <?php echo $__env->renderComponent(); ?>
                </div>
                
                <div class="col-md-3">
                    <?php $__env->startComponent('components.card', ['hover' => true]); ?>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fa-solid fa-check-circle text-success fa-lg"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Использовано</h6>
                                <h3 class="mb-0">78</h3>
                                <div class="small text-success">
                                    <i class="fa-solid fa-arrow-up me-1"></i> +8% к прошлому периоду
                                </div>
                            </div>
                        </div>
                    <?php echo $__env->renderComponent(); ?>
                </div>
                
                <div class="col-md-3">
                    <?php $__env->startComponent('components.card', ['hover' => true]); ?>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fa-solid fa-percentage text-warning fa-lg"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Конверсия</h6>
                                <h3 class="mb-0">62.4%</h3>
                                <div class="small text-success">
                                    <i class="fa-solid fa-arrow-up me-1"></i> +2.1% к прошлому периоду
                                </div>
                            </div>
                        </div>
                    <?php echo $__env->renderComponent(); ?>
                </div>
                
                <div class="col-md-3">
                    <?php $__env->startComponent('components.card', ['hover' => true]); ?>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fa-solid fa-ruble-sign text-info fa-lg"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Общая сумма</h6>
                                <h3 class="mb-0">235 000 ₽</h3>
                                <div class="small text-success">
                                    <i class="fa-solid fa-arrow-up me-1"></i> +15% к прошлому периоду
                                </div>
                            </div>
                        </div>
                    <?php echo $__env->renderComponent(); ?>
                </div>
            </div>
        </div>
        
        <!-- Графики -->
        <div class="col-lg-8">
            <?php $__env->startComponent('components.card', ['header' => '
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="mb-0">Динамика создания и использования</h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active">Линейный</button>
                        <button type="button" class="btn btn-outline-secondary">Столбчатый</button>
                    </div>
                </div>
            ']); ?>
                <div class="chart-container" style="position: relative; height:400px;">
                    <canvas id="mainChart"></canvas>
                </div>
            <?php echo $__env->renderComponent(); ?>
        </div>
        
        <div class="col-lg-4">
            <?php $__env->startComponent('components.card', ['title' => 'Распределение по статусам']); ?>
                <div class="chart-container mb-4" style="position: relative; height:250px;">
                    <canvas id="statusChart"></canvas>
                </div>
                
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator status-active me-2"></div>
                            <span>Активные</span>
                        </div>
                        <span class="fw-bold">47 (37.6%)</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator status-pending me-2"></div>
                            <span>Использованные</span>
                        </div>
                        <span class="fw-bold">78 (62.4%)</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator status-expired me-2"></div>
                            <span>Истекшие</span>
                        </div>
                        <span class="fw-bold">15 (12%)</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator status-cancelled me-2"></div>
                            <span>Отмененные</span>
                        </div>
                        <span class="fw-bold">5 (4%)</span>
                    </div>
                </div>
            <?php echo $__env->renderComponent(); ?>
        </div>
        
        <!-- Таблица с детальными данными -->
        <div class="col-12">
            <?php $__env->startComponent('components.card', ['title' => 'Детальная статистика']); ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Период</th>
                                <th>Создано</th>
                                <th>Использовано</th>
                                <th>Конверсия</th>
                                <th>Сумма (₽)</th>
                                <th>Средний чек (₽)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Январь 2023</td>
                                <td>25</td>
                                <td>15</td>
                                <td>60%</td>
                                <td>45 000</td>
                                <td>1 800</td>
                            </tr>
                            <tr>
                                <td>Февраль 2023</td>
                                <td>32</td>
                                <td>20</td>
                                <td>62.5%</td>
                                <td>60 000</td>
                                <td>1 875</td>
                            </tr>
                            <tr>
                                <td>Март 2023</td>
                                <td>28</td>
                                <td>18</td>
                                <td>64.3%</td>
                                <td>54 000</td>
                                <td>1 929</td>
                            </tr>
                            <tr>
                                <td>Апрель 2023</td>
                                <td>40</td>
                                <td>25</td>
                                <td>62.5%</td>
                                <td>75 000</td>
                                <td>1 875</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td>Всего</td>
                                <td>125</td>
                                <td>78</td>
                                <td>62.4%</td>
                                <td>235 000</td>
                                <td>1 880</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php echo $__env->renderComponent(); ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключение между стандартным и произвольным периодом
    document.getElementById('dateRange').addEventListener('change', function() {
        const customDateContainers = [
            document.getElementById('customDateContainer'),
            document.getElementById('customDateEndContainer')
        ];
        
        if (this.value === 'custom') {
            customDateContainers.forEach(container => container.style.display = 'block');
        } else {
            customDateContainers.forEach(container => container.style.display = 'none');
        }
    });
    
    // Основной график
    const mainChartCtx = document.getElementById('mainChart').getContext('2d');
    const mainChart = new Chart(mainChartCtx, {
        type: 'line',
        data: {
            labels: ['Январь', 'Февраль', 'Март', 'Апрель'],
            datasets: [
                {
                    label: 'Создано',
                    data: [25, 32, 28, 40],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Использовано',
                    data: [15, 20, 18, 25],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top',
                    align: 'end'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // График статусов
    const statusChartCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusChartCtx, {
        type: 'doughnut',
        data: {
            labels: ['Активные', 'Использованные', 'Истекшие', 'Отмененные'],
            datasets: [{
                data: [47, 78, 15, 5],
                backgroundColor: [
                    '#198754', // Активные - зеленый
                    '#6c757d', // Использованные - серый
                    '#ffc107', // Истекшие - желтый
                    '#dc3545'  // Отмененные - красный
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20
                    }
                }
            },
            cutout: '65%'
        }
    });
    
    // Печать отчета
    document.getElementById('printReport').addEventListener('click', function() {
        window.print();
    });
    
    // Предотвращаем отправку формы (в реальном приложении должна быть Ajax-логика)
    document.getElementById('reportFiltersForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Здесь должен быть код для обновления данных отчета
        alert('В реальном приложении здесь будет обновление данных отчета');
    });
});
</script>

<style>
@media print {
    .btn, form, footer, .navbar, .sidebar-nav, .breadcrumb {
        display: none !important;
    }
    
    body {
        padding-top: 0 !important;
    }
    
    main {
        margin-left: 0 !important;
    }
    
    .card {
        break-inside: avoid;
    }
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/analytics/reports.blade.php ENDPATH**/ ?>