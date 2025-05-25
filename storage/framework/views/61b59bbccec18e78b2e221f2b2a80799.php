

    <?php if(!isset($totalCertificates) || $totalCertificates == 0): ?>
        <!-- Блок, отображаемый при отсутствии данных -->
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body p-3 p-md-5 text-center">
                <div class="mb-3 mb-md-4">
                    <i class="fa-solid fa-chart-simple fa-3x text-muted opacity-25"></i>
                </div>
                <h4 class="fs-5 fs-md-4">Данных для анализа пока нет</h4>
                <p class="text-muted mb-3 mb-md-4 small">Для получения статистики необходимо создать и использовать документы.</p>
                <a href="<?php echo e(route('entrepreneur.certificates.select-template')); ?>" class="btn btn-primary btn-sm btn-md">
                    <i class="fa-solid fa-plus me-1 me-md-2"></i>Создать первый документ
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Вкладки для переключения между типами статистики -->
        <ul class="nav nav-pills flex-column flex-sm-row nav-justified gap-1 mb-3 mb-md-4 analytics-tabs" id="statisticsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active w-100" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab">
                    <i class="fa-solid fa-chart-pie me-1 me-md-2"></i><span class="d-none d-sm-inline">Обзор</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link w-100" id="sales-tab" data-bs-toggle="pill" data-bs-target="#sales" type="button" role="tab">
                    <i class="fa-solid fa-money-bill-trend-up me-1 me-md-2"></i><span class="d-none d-sm-inline">Продажи</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link w-100" id="customers-tab" data-bs-toggle="pill" data-bs-target="#customers" type="button" role="tab">
                    <i class="fa-solid fa-users me-1 me-md-2"></i><span class="d-none d-sm-inline">Получатели</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link w-100" id="templates-tab" data-bs-toggle="pill" data-bs-target="#templates" type="button" role="tab">
                    <i class="fa-solid fa-palette me-1 me-md-2"></i><span class="d-none d-sm-inline">Шаблоны</span>
                </button>
            </li>
        </ul>
        
        <!-- Содержимое вкладок -->
        <div class="tab-content" id="statisticsTabsContent">
            <!-- Вкладка "Обзор" -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <div class="row g-3">
                    <!-- Статистика по документам -->
                    <div class="col-12 col-md-6">
                        <?php $__env->startComponent('components.card', ['title' => 'Статистика документов']); ?>
                            <?php if(isset($totalCertificates) && $totalCertificates > 0): ?>
                                <div class="chart-container" style="position: relative; height:250px; min-height:200px;">
                                    <canvas id="certificatesOverviewChart"></canvas>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-3 py-md-5">
                                    <p class="text-muted small">Недостаточно данных для построения графика</p>
                                </div>
                            <?php endif; ?>
                        <?php echo $__env->renderComponent(); ?>
                    </div>
                    
                    <!-- Тепловая карта активности -->
                    <div class="col-12 col-md-6">
                        <?php $__env->startComponent('components.card', ['title' => 'Активность по дням недели']); ?>
                            <?php if(isset($totalCertificates) && $totalCertificates > 5): ?> 
                                <div class="chart-container" style="position: relative; height:250px; min-height:200px;">
                                    <canvas id="heatmapChart"></canvas>
                                </div>
                                <div class="small text-muted text-center mt-2">Интенсивность создания документов по дням недели</div>
                            <?php else: ?>
                                <div class="text-center py-3 py-md-5">
                                    <p class="text-muted small">Для построения тепловой карты необходимо больше данных (минимум 5 документов)</p>
                                </div>
                            <?php endif; ?>
                        <?php echo $__env->renderComponent(); ?>
                    </div>
                    
                    <!-- Эффективность по месяцам -->
                    <div class="col-12">
                        <?php $__env->startComponent('components.card', ['title' => 'Эффективность по месяцам']); ?>
                            <?php if(isset($monthlyStats) && count($monthlyStats) > 1): ?> 
                                <div class="chart-container" style="position: relative; height:250px; min-height:200px;">
                                    <canvas id="monthlyPerformanceChart"></canvas>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-3 py-md-5">
                                    <p class="text-muted small">Для анализа эффективности нужны данные минимум за 2 месяца</p>
                                </div>
                            <?php endif; ?>
                        <?php echo $__env->renderComponent(); ?>
                    </div>
                    
                    <!-- Ключевые показатели эффективности -->
                    <div class="col-12">
                        <?php $__env->startComponent('components.card', ['title' => 'Ключевые показатели эффективности (KPI)']); ?>
                            <div class="row g-2 g-md-4">
                                <div class="col-6 col-md-3">
                                    <div class="border rounded-4 p-2 p-md-3 text-center h-100">
                                        <h6 class="text-muted mb-2 small">Коэффициент конверсии</h6>
                                        <h2 class="mb-0 fs-4 fs-md-3">
                                            <?php if(isset($totalCertificates) && $totalCertificates > 0): ?>
                                                <?php echo e(number_format(($usedCertificates / $totalCertificates * 100), 1)); ?>%
                                            <?php else: ?>
                                                0%
                                            <?php endif; ?>
                                        </h2>
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                style="width: <?php echo e(isset($totalCertificates) && $totalCertificates > 0 ? ($usedCertificates / $totalCertificates * 100) : 0); ?>%" 
                                                aria-valuenow="<?php echo e(isset($totalCertificates) && $totalCertificates > 0 ? ($usedCertificates / $totalCertificates * 100) : 0); ?>" 
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <?php if(isset($conversionChange)): ?>
                                            <p class="small <?php echo e($conversionChange >= 0 ? 'text-success' : 'text-danger'); ?> mt-2 mb-0">
                                                <i class="fa-solid fa-arrow-<?php echo e($conversionChange >= 0 ? 'up' : 'down'); ?> me-1"></i> 
                                                <?php echo e(abs($conversionChange)); ?>% 
                                            </p>
                                        <?php else: ?>
                                            <p class="small text-muted mt-2 mb-0">Нет данных</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-6 col-md-3">
                                    <div class="border rounded-4 p-2 p-md-3 text-center h-100">
                                        <h6 class="text-muted mb-2 small">Средний чек</h6>
                                        <h2 class="mb-0 fs-4 fs-md-3">
                                            <?php if(isset($totalCertificates) && $totalCertificates > 0 && isset($totalAmount)): ?>
                                                <?php echo e(number_format($totalAmount / $totalCertificates, 0, '.', ' ')); ?> ₽
                                            <?php else: ?>
                                                0 ₽
                                            <?php endif; ?>
                                        </h2>
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="small text-muted mt-2 mb-0">Нет данных</p>
                                    </div>
                                </div>
                                
                                <div class="col-6 col-md-3">
                                    <div class="border rounded-4 p-2 p-md-3 text-center h-100">
                                        <h6 class="text-muted mb-2 small">Кол-во в месяц</h6>
                                        <h2 class="mb-0 fs-4 fs-md-3">
                                            <?php if(isset($certificatesPerMonth)): ?>
                                                <?php echo e(number_format($certificatesPerMonth, 1)); ?>

                                            <?php else: ?>
                                                0
                                            <?php endif; ?>
                                        </h2>
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="small text-muted mt-2 mb-0">Недостаточно данных</p>
                                    </div>
                                </div>
                                
                                <div class="col-6 col-md-3">
                                    <div class="border rounded-4 p-2 p-md-3 text-center h-100">
                                        <h6 class="text-muted mb-2 small">Ср. время активации</h6>
                                        <h2 class="mb-0 fs-4 fs-md-3">
                                            <?php if(isset($avgActivationTime)): ?>
                                                <?php echo e($avgActivationTime); ?> дн.
                                            <?php else: ?>
                                                - дн.
                                            <?php endif; ?>
                                        </h2>
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <p class="small text-muted mt-2 mb-0">Нет данных</p>
                                    </div>
                                </div>
                            </div>
                        <?php echo $__env->renderComponent(); ?>
                    </div>
                </div>
            </div>
            
            <!-- Вкладка "Продажи" -->
            <div class="tab-pane fade" id="sales" role="tabpanel" aria-labelledby="sales-tab">
                <?php if(isset($totalCertificates) && $totalCertificates > 0): ?>
                    <div class="row g-3 g-md-4">
                        <div class="col-12 col-lg-8">
                            <?php $__env->startComponent('components.card', ['title' => 'Динамика продаж']); ?>
                                <div class="chart-container" style="position: relative; height:300px; min-height:200px;">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            <?php echo $__env->renderComponent(); ?>
                        </div>
                        
                        <div class="col-12 col-lg-4">
                            <?php $__env->startComponent('components.card', ['title' => 'Распределение по суммам']); ?>
                                <div class="chart-container" style="position: relative; height:300px; min-height:200px;">
                                    <canvas id="amountDistributionChart"></canvas>
                                </div>
                            <?php echo $__env->renderComponent(); ?>
                        </div>
                        
                        <div class="col-12">
                            <?php $__env->startComponent('components.card', ['title' => 'Топ продаж']); ?>
                                <?php if(isset($topCertificates) && count($topCertificates) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>№ документа</th>
                                                    <th>Дата</th>
                                                    <th>Получатель</th>
                                                    <th class="d-none d-md-table-cell">Шаблон</th>
                                                    <th>Сумма</th>
                                                    <th>Статус</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $topCertificates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($cert->certificate_number); ?></td>
                                                    <td><?php echo e($cert->created_at->format('d.m.Y')); ?></td>
                                                    <td><?php echo e($cert->recipient_name); ?></td>
                                                    <td class="d-none d-md-table-cell"><?php echo e($cert->template->name); ?></td>
                                                    <td><?php echo e(number_format($cert->amount, 0, '.', ' ')); ?> ₽</td>
                                                    <td>
                                                        <?php if($cert->status == 'active'): ?>
                                                            <span class="badge bg-success">Активен</span>
                                                        <?php elseif($cert->status == 'used'): ?>
                                                            <span class="badge bg-secondary">Использован</span>
                                                        <?php elseif($cert->status == 'expired'): ?>
                                                            <span class="badge bg-warning">Истек</span>
                                                        <?php elseif($cert->status == 'canceled'): ?>
                                                            <span class="badge bg-danger">Отменен</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3 py-md-5">
                                        <p class="text-muted small">Данных о документах пока нет</p>
                                    </div>
                                <?php endif; ?>
                            <?php echo $__env->renderComponent(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3 py-md-5">
                        <div class="mb-3">
                            <i class="fa-solid fa-chart-line fa-2x text-muted opacity-25"></i>
                        </div>
                        <h5 class="fs-6 fs-md-5">Нет данных для анализа продаж</h5>
                        <p class="text-muted small">Создайте документы, чтобы увидеть статистику продаж</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Вкладка "Получатели" -->
            <div class="tab-pane fade" id="customers" role="tabpanel" aria-labelledby="customers-tab">
                <?php if(isset($totalCertificates) && $totalCertificates > 0): ?>
                    <div class="row g-3 g-md-4">
                        <div class="col-12 col-md-6">
                            <?php $__env->startComponent('components.card', ['title' => 'Лояльность клиентов']); ?>
                                <?php if(isset($recipientStats) && count($recipientStats) > 0): ?>
                                    <div class="chart-container" style="position: relative; height:250px; min-height:200px;">
                                        <canvas id="loyaltyChart"></canvas>
                                    </div>
                                    <div class="small text-muted text-center mt-2">Распределение клиентов по количеству покупок</div>
                                <?php else: ?>
                                    <div class="text-center py-3 py-md-5">
                                        <p class="text-muted small">Недостаточно данных для анализа лояльности</p>
                                    </div>
                                <?php endif; ?>
                            <?php echo $__env->renderComponent(); ?>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <?php $__env->startComponent('components.card', ['title' => 'Демография получателей']); ?>
                                <div class="text-center py-3 py-md-5">
                                    <p class="text-muted small">Данные о демографии недоступны</p>
                                </div>
                            <?php echo $__env->renderComponent(); ?>
                        </div>
                        
                        <div class="col-12">
                            <?php $__env->startComponent('components.card', ['title' => 'Топ получателей']); ?>
                                <?php if(isset($topRecipients) && count($topRecipients) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Получатель</th>
                                                    <th class="d-none d-md-table-cell">Email</th>
                                                    <th>Кол-во</th>
                                                    <th>Сумма</th>
                                                    <th class="d-none d-md-table-cell">Ср. чек</th>
                                                    <th class="d-none d-md-table-cell">Последняя активность</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $topRecipients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($recipient->name); ?></td>
                                                    <td class="d-none d-md-table-cell"><?php echo e($recipient->email); ?></td>
                                                    <td><?php echo e($recipient->certificates_count); ?></td>
                                                    <td><?php echo e(number_format($recipient->total_amount, 0, '.', ' ')); ?> ₽</td>
                                                    <td class="d-none d-md-table-cell"><?php echo e(number_format($recipient->average_amount, 0, '.', ' ')); ?> ₽</td>
                                                    <td class="d-none d-md-table-cell"><?php echo e($recipient->last_activity->format('d.m.Y')); ?></td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3 py-md-5">
                                        <p class="text-muted small">Нет данных о получателях</p>
                                    </div>
                                <?php endif; ?>
                            <?php echo $__env->renderComponent(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3 py-md-5">
                        <div class="mb-3">
                            <i class="fa-solid fa-users fa-2x text-muted opacity-25"></i>
                        </div>
                        <h5 class="fs-6 fs-md-5">Нет данных о получателях</h5>
                        <p class="text-muted small">Создайте документы для получения статистики</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Вкладка "Шаблоны" -->
            <div class="tab-pane fade" id="templates" role="tabpanel" aria-labelledby="templates-tab">
                <?php if(isset($templateStats) && count($templateStats) > 0): ?>
                    <div class="row g-3 g-md-4">
                        <div class="col-12 col-md-6">
                            <?php $__env->startComponent('components.card', ['title' => 'Популярность шаблонов']); ?>
                                <div class="chart-container" style="position: relative; height:250px; min-height:200px;">
                                    <canvas id="templatesPopularityChart"></canvas>
                                </div>
                            <?php echo $__env->renderComponent(); ?>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <?php $__env->startComponent('components.card', ['title' => 'Конверсия по шаблонам']); ?>
                                <div class="chart-container" style="position: relative; height:250px; min-height:200px;">
                                    <canvas id="templatesConversionChart"></canvas>
                                </div>
                            <?php echo $__env->renderComponent(); ?>
                        </div>
                        
                        <div class="col-12">
                            <?php $__env->startComponent('components.card', ['title' => 'Эффективность шаблонов']); ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th>Шаблон</th>
                                                <th>Создано</th>
                                                <th>Использовано</th>
                                                <th class="d-none d-md-table-cell">Конверсия</th>
                                                <th>Сумма</th>
                                                <th class="d-none d-md-table-cell">Ср. чек</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $templateStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($stat['template_name']); ?></td>
                                                <td><?php echo e($stat['count']); ?></td>
                                                <td><?php echo e($stat['used']); ?></td>
                                                <td class="d-none d-md-table-cell"><?php echo e(number_format(($stat['used'] / $stat['count'] * 100), 1)); ?>%</td>
                                                <td><?php echo e(number_format($stat['amount'], 0, '.', ' ')); ?> ₽</td>
                                                <td class="d-none d-md-table-cell"><?php echo e(number_format($stat['amount'] / $stat['count'], 0, '.', ' ')); ?> ₽</td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr class="fw-bold">
                                                <td>Всего</td>
                                                <td><?php echo e($totalCertificates); ?></td>
                                                <td><?php echo e($usedCertificates); ?></td>
                                                <td class="d-none d-md-table-cell"><?php echo e(number_format(($usedCertificates / $totalCertificates * 100), 1)); ?>%</td>
                                                <td><?php echo e(number_format($totalAmount, 0, '.', ' ')); ?> ₽</td>
                                                <td class="d-none d-md-table-cell"><?php echo e(number_format($totalAmount / $totalCertificates, 0, '.', ' ')); ?> ₽</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php echo $__env->renderComponent(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3 py-md-5">
                        <div class="mb-3">
                            <i class="fa-solid fa-palette fa-2x text-muted opacity-25"></i>
                        </div>
                        <h5 class="fs-6 fs-md-5">Нет данных для анализа шаблонов</h5>
                        <p class="text-muted small">Создайте документы с разными шаблонами для сбора статистики</п>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Проверяем размер экрана для адаптивности графиков
    function isSmallScreen() {
        return window.innerWidth < 768;
    }
    
    // Настройки для графиков на мобильных устройствах
    const mobileChartOptions = {
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 8,
                    padding: 10,
                    font: {
                        size: 10
                    }
                }
            },
            tooltip: {
                bodyFont: {
                    size: 10
                },
                titleFont: {
                    size: 10
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    font: {
                        size: 8
                    },
                    maxRotation: 45,
                    minRotation: 45
                }
            },
            y: {
                ticks: {
                    font: {
                        size: 8
                    }
                }
            }
        }
    };
    
    // Проверяем, есть ли на странице графики и данные для них
    <?php if(isset($totalCertificates) && $totalCertificates > 0): ?>
    
    // Данные для графиков из бэкенда
    const certificateData = {
        active: <?php echo e($activeCertificates); ?>,
        used: <?php echo e($usedCertificates); ?>,
        expired: <?php echo e($expiredCertificates); ?>,
        canceled: <?php echo e($canceledCertificates); ?>

    };

    // Инициализация только тех графиков, для которых у нас есть данные
    if (document.getElementById('certificatesOverviewChart')) {
        const certificatesOverviewCtx = document.getElementById('certificatesOverviewChart').getContext('2d');
        
        const chartOptions = {
            type: 'bar',
            data: {
                labels: ['Активные', 'Использованные', 'Истекшие', 'Отмененные'],
                datasets: [{
                    label: 'Количество документов',
                    data: [
                        certificateData.active,
                        certificateData.used,
                        certificateData.expired,
                        certificateData.canceled
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.6)',
                        'rgba(108, 117, 125, 0.6)',
                        'rgba(255, 193, 7, 0.6)',
                        'rgba(220, 53, 69, 0.6)'
                    ],
                    borderColor: [
                        'rgba(25, 135, 84, 1)',
                        'rgba(108, 117, 125, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        };
        
        // Применяем мобильные опции если нужно
        if (isSmallScreen()) {
            Object.assign(chartOptions.options, mobileChartOptions);
        }
        
        new Chart(certificatesOverviewCtx, chartOptions);
    }

    // Обработчик изменения размера окна для адаптивности
    window.addEventListener('resize', function() {
        // Здесь можно добавить код для пересоздания графиков при изменении размера окна
        // но это может быть ресурсоемкой операцией
    });
    <?php endif; ?>
});
</script>

<style>
/* Улучшенные стили для мобильной адаптивности */
.analytics-tabs .nav-link {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

@media (max-width: 767.98px) {
    .analytics-tabs .nav-link {
        padding: 0.4rem 0.6rem;
        min-height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .chart-container {
        height: 200px !important;
        min-height: 180px !important;
    }
    
    .card-title {
        font-size: 1rem !important;
    }
    
    .card .table {
        font-size: 0.8rem;
    }
    
    .badge {
        font-size: 0.7rem;
    }
}

@media (max-width: 575.98px) {
    .analytics-tabs {
        flex-direction: row;
        flex-wrap: nowrap;
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 5px;
    }
    
    .analytics-tabs::-webkit-scrollbar {
        height: 3px;
    }
    
    .analytics-tabs::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 3px;
    }
    
    .analytics-tabs .nav-item {
        flex: 0 0 auto;
        width: auto;
    }
}

/* Стили для индикаторов статуса - сохраняем их */
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

@media (min-width: 768px) {
    .status-indicator {
        width: 16px;
        height: 16px;
    }
}

.status-active {
    background-color: #198754;
}

.status-expired {
    background-color: #ffc107;
}

.status-pending {
    background-color: #6c757d;
}

.status-cancelled {
    background-color: #dc3545;
}
</style>
<?php $__env->stopPush(); ?>

<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/analytics/statistics.blade.php ENDPATH**/ ?>