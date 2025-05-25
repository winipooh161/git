@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Личный кабинет</li>
        </ol>
    </nav>
    
    <!-- Приветствие и быстрые действия -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h1 class="h2 mb-1">Добро пожаловать, {{ Auth::user()->name }}!</h1>
            <p class="text-muted">Управление документами и аналитика</p>
        </div>
        <div class="d-flex mt-3 mt-md-0">
            <a href="{{ route('entrepreneur.certificates.select-template') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>Создать документ
            </a>
        </div>
    </div>
    
    <!-- Информационная панель -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm hover-lift h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 rounded-circle p-3" style="background: rgba(13,110,253,0.1);">
                            <i class="fa-solid fa-certificate text-primary fa-fw fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Всего документов</h6>
                        </div>
                    </div>
                    <h2 class="mb-0">{{ $totalCertificates }}</h2>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm hover-lift h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 rounded-circle p-3" style="background: rgba(25,135,84,0.1);">
                            <i class="fa-solid fa-check-circle text-success fa-fw fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Активные</h6>
                        </div>
                    </div>
                    <h2 class="mb-0">{{ $activeCertificates }}</h2>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $totalCertificates > 0 ? ($activeCertificates / $totalCertificates * 100) : 0 }}%" 
                             aria-valuenow="{{ $totalCertificates > 0 ? ($activeCertificates / $totalCertificates * 100) : 0 }}" 
                             aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        {{ $totalCertificates > 0 ? number_format($activeCertificates / $totalCertificates * 100, 0) : 0 }}% от общего числа
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm hover-lift h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 rounded-circle p-3" style="background: rgba(108,117,125,0.1);">
                            <i class="fa-solid fa-check-double text-secondary fa-fw fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Использовано</h6>
                        </div>
                    </div>
                    <h2 class="mb-0">{{ $usedCertificates }}</h2>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-secondary" role="progressbar" 
                             style="width: {{ $totalCertificates > 0 ? ($usedCertificates / $totalCertificates * 100) : 0 }}%" 
                             aria-valuenow="{{ $totalCertificates > 0 ? ($usedCertificates / $totalCertificates * 100) : 0 }}" 
                             aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        {{ $totalCertificates > 0 ? number_format($usedCertificates / $totalCertificates * 100, 0) : 0 }}% от общего числа
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 rounded-4 shadow-sm hover-lift h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 rounded-circle p-3" style="background: rgba(253,126,20,0.1);">
                            <i class="fa-solid fa-ruble-sign text-warning fa-fw fa-lg"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0">Общая сумма</h6>
                        </div>
                    </div>
                    <h2 class="mb-0">{{ number_format($totalAmount, 0, '.', ' ') }} ₽</h2>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-success d-flex align-items-center">
                            <i class="fa-solid fa-arrow-up me-1"></i> 12%
                        </span>
                        <span class="small text-muted">За последний месяц</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Графики и таблицы -->
    <div class="row g-4 mb-4">
        <!-- График активности -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-transparent pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Статистика документов</h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary active">Неделя</button>
                            <button type="button" class="btn btn-outline-secondary">Месяц</button>
                            <button type="button" class="btn btn-outline-secondary">Год</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <!-- Здесь будет график -->
                        <canvas id="certificatesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Статистика по категориям -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm h-100">
                <div class="card-header bg-transparent pt-4">
                    <h5 class="mb-0">Распределение статусов</h5>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="chart-container mb-3" style="position: relative; height:200px; width:200px;">
                        <!-- Здесь будет круговая диаграмма -->
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="status-indicator status-active me-2"></div>
                                <span>Активные</span>
                            </div>
                            <span class="fw-bold">{{ $activeCertificates }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="status-indicator status-expired me-2"></div>
                                <span>Истекшие</span>
                            </div>
                            <span class="fw-bold">{{ $expiredCertificates }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="status-indicator status-pending me-2"></div>
                                <span>Использованные</span>
                            </div>
                            <span class="fw-bold">{{ $usedCertificates }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Последние документы -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center pt-4">
            <h5 class="mb-0">Последние документы</h5>
            <a href="{{ route('entrepreneur.certificates.index') }}" class="btn btn-sm btn-outline-primary">
                Все документы <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">№ документа</th>
                            <th>Получатель</th>
                            <th>Шаблон</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th class="text-end pe-4">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentCertificates as $certificate)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $certificate->certificate_number }}</td>
                                <td>{{ $certificate->recipient_name }}</td>
                                <td>{{ $certificate->template->name }}</td>
                                <td>{{ number_format($certificate->amount, 0, '.', ' ') }} ₽</td>
                                <td>
                                    @if ($certificate->status == 'active')
                                        <span class="badge bg-success">Активен</span>
                                    @elseif ($certificate->status == 'used')
                                        <span class="badge bg-secondary">Использован</span>
                                    @elseif ($certificate->status == 'expired')
                                        <span class="badge bg-warning">Истек</span>
                                    @elseif ($certificate->status == 'canceled')
                                        <span class="badge bg-danger">Отменен</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('entrepreneur.certificates.show', $certificate) }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Просмотр">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('entrepreneur.certificates.edit', $certificate) }}" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Редактировать">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-regular fa-folder-open text-muted fa-3x mb-3"></i>
                                        <h6 class="mb-2">У вас еще нет созданных документов</h6>
                                        <p class="text-muted mb-4">Создайте свой первый документ прямо сейчас!</p>
                                        <a href="{{ route('entrepreneur.certificates.select-template') }}" class="btn btn-primary">
                                            <i class="fa-solid fa-plus me-2"></i>Создать первый документ
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Рекомендации -->
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5>Узнайте больше о возможностях документов</h5>
                    <p class="text-muted mb-0">Мы подготовили для вас подробное руководство по использованию документов для вашего бизнеса.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="#" class="btn btn-outline-primary">Перейти к руководству</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Контейнер для карточек с сертификатами -->
<div class="row" id="certificates-container">
    @foreach($recentCertificates as $certificate)
    <div class="col-md-4 mb-3 certificate-item">
        <!-- Здесь ваш HTML для карточки сертификата -->
        <div class="card certificate-card">
            <!-- ... existing code ... -->
        </div>
    </div>
    @endforeach
</div>

<!-- Индикатор загрузки -->
<div id="loading-indicator" class="text-center my-3 d-none">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Загрузка...</span>
    </div>
    <p class="mt-2 text-muted">Загрузка документов...</p>
</div>

<!-- Скрипт для бесконечной прокрутки -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let isLoading = false;
    let hasMorePages = true;
    
    const certificatesContainer = document.getElementById('certificates-container');
    const loadingIndicator = document.getElementById('loading-indicator');
    
    // Функция для проверки, достиг ли пользователь конца страницы
    function checkScroll() {
        if (isLoading || !hasMorePages) return;
        
        const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
        
        // Если пользователь прокрутил до 80% высоты страницы
        if (scrollTop + clientHeight >= scrollHeight * 0.8) {
            loadMoreCertificates();
        }
    }
    
    // Функция для загрузки дополнительных сертификатов
    function loadMoreCertificates() {
        isLoading = true;
        currentPage++;
        
        if (loadingIndicator) {
            loadingIndicator.classList.remove('d-none');
        }
        
        // Получаем CSRF-токен из мета-тега
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // AJAX-запрос для получения следующей страницы
        fetch(`/entrepreneur/dashboard?page=${currentPage}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            isLoading = false;
            
            if (loadingIndicator) {
                loadingIndicator.classList.add('d-none');
            }
            
            if (data.certificates && data.certificates.length > 0) {
                // Добавляем новые карточки на страницу
                renderCertificates(data.certificates);
                
                // Обновляем статус пагинации
                hasMorePages = data.has_more_pages;
            } else {
                hasMorePages = false;
            }
            
            // Если больше нет страниц, показываем уведомление
            if (!hasMorePages) {
                showEndOfContentMessage();
            }
        })
        .catch(error => {
            console.error('Ошибка при загрузке данных:', error);
            isLoading = false;
            
            if (loadingIndicator) {
                loadingIndicator.classList.add('d-none');
            }
        });
    }
    
    // Функция для рендеринга карточек сертификатов
    function renderCertificates(certificates) {
        certificates.forEach(certificate => {
            const cardElement = document.createElement('div');
            cardElement.className = 'col-md-4 mb-3 certificate-item';
            cardElement.setAttribute('data-certificate-id', certificate.id);
            
            // Здесь формируем HTML-код карточки на основе данных certificate
            // Можно использовать шаблонные строки для вставки данных
            cardElement.innerHTML = `
                <div class="card certificate-card">
                    <div class="card-body">
                        <h5 class="card-title">${certificate.recipient_name}</h5>
                        <p class="card-text">
                            Номер: ${certificate.certificate_number}<br>
                            Сумма: ${certificate.amount}<br>
                            Статус: ${certificate.status}
                        </p>
                        <a href="/entrepreneur/certificates/${certificate.id}" class="btn btn-sm btn-primary">Подробнее</a>
                    </div>
                </div>
            `;
            
            certificatesContainer.appendChild(cardElement);
            
            // Добавляем плавную анимацию появления
            setTimeout(() => {
                cardElement.classList.add('certificate-item-visible');
            }, 10);
        });
    }
    
    // Функция для отображения сообщения о конце контента
    function showEndOfContentMessage() {
        const endMessage = document.createElement('div');
        endMessage.className = 'col-12 text-center my-4';
        endMessage.innerHTML = `
            <p class="text-muted">Все документы загружены</p>
        `;
        certificatesContainer.appendChild(endMessage);
    }
    
    // Добавляем обработчик события прокрутки
    window.addEventListener('scroll', checkScroll);
    
    // Проверяем прокрутку сразу после загрузки страницы
    // для случаев, когда контента мало и прокрутка не требуется
    setTimeout(checkScroll, 500);
});
</script>

<!-- Стили для анимации появления карточек -->
<style>
.certificate-item {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.5s ease, transform 0.5s ease;
}
.certificate-item-visible {
    opacity: 1;
    transform: translateY(0);
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Проверка наличия элемента, чтобы избежать ошибок на страницах, где графиков нет
    if (!document.getElementById('certificatesChart')) return;
    
    // Линейный график для документов
    var ctx = document.getElementById('certificatesChart').getContext('2d');
    var certificatesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
            datasets: [{
                label: 'Создано',
                data: [3, 2, 5, 1, 4, 6, 2],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }, {
                label: 'Использовано',
                data: [1, 1, 2, 0, 3, 1, 1],
                borderColor: '#6c757d',
                backgroundColor: 'rgba(108, 117, 125, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
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
    
    // Проверка наличия элемента статус-чарта
    if (!document.getElementById('statusChart')) return;
    
    // Круговая диаграмма для статусов документов
    var ctxStatus = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Активные', 'Истекшие', 'Использованные'],
            datasets: [{
                data: [
                    {{ $activeCertificates ?? 0 }}, 
                    {{ $expiredCertificates ?? 0 }}, 
                    {{ $usedCertificates ?? 0 }}
                ],
                backgroundColor: [
                    '#198754',
                    '#ffc107',
                    '#6c757d'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endpush
@endsection
