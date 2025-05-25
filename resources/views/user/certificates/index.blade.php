@extends('layouts.lk')

@section('content')
<div class="container-fluid py-3 py-md-4">
  <div class="d-none d-md-block">
                    @include('user.certificates.partials._folder_system', [
                        'folders' => $folders ?? [],
                        'currentFolder' => $currentFolder ?? null,
                    ])
                </div>
    @include('user.certificates.partials._alerts')
    
    <!-- Основной контейнер для сертификатов -->
    <div id="certificates-container" class="grid-container">
        <!-- Всегда отображаем сетку с классом grid-visual независимо от наличия карточек -->
        <div class="row row-cols-3 row-cols-sm-3 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 card-grid certificate-row grid-visual">
            @if($certificates->count() > 0)
                @foreach ($certificates as $certificate)
                    <div class="col certificate-item h-100 grid-cell">
                        @include('user.certificates.partials._certificate_card', ['certificate' => $certificate])
                    </div>
                @endforeach
            @endif
            
            <!-- Добавляем пустые ячейки для поддержания структуры сетки -->
            @php
                // Определяем количество столбцов в зависимости от размера экрана
                // (используем значение по умолчанию 3 для мобильной версии)
                $totalColumns = 3;
                
                // Вычисляем сколько пустых ячеек нужно добавить для заполнения сетки
                $existingCells = $certificates->count() > 0 ? $certificates->count() : 0;
                $emptyNeeded = max(15, $totalColumns - ($existingCells % $totalColumns));
                if ($emptyNeeded == $totalColumns) $emptyNeeded = 0;
                
                // Добавляем минимум 15 ячеек если нет сертификатов
                if ($existingCells == 0) $emptyNeeded = 15;
            @endphp
            
            @for($i = 0; $i < $emptyNeeded; $i++)
            <div class="col certificate-item h-100 grid-cell">
                <div class="card border-0 shadow-sm h-100 certificate-card empty-card">
                    <div class="certificate-cover-wrapper bg-light">
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Индикатор загрузки при бесконечной прокрутке -->
    <div id="infinite-scroll-loader" class="text-center my-4 d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
        <p class="mt-2 text-muted">Загрузка документов...</p>
    </div>

    <!-- Подключение модальных окон -->
    @include('user.certificates.partials._modals')

    <!-- Подключение скриптов -->
    @include('user.certificates.partials._scripts')

    <!-- Подключение стилей -->
    @include('user.certificates.partials._styles')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Переменные для бесконечной прокрутки
        let currentPage = 1;
        let isLoading = false;
        let hasMorePages = {{ $certificates->hasMorePages() ? 'true' : 'false' }};
        
        const certificatesContainer = document.getElementById('certificates-container');
        const loader = document.getElementById('infinite-scroll-loader');
        
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
            
            // Показываем индикатор загрузки
            loader.classList.remove('d-none');
            
            // Формируем URL с учетом возможного фильтра по папке
            const folderParam = new URLSearchParams(window.location.search).get('folder');
            const url = `/user/certificates/load-more?page=${currentPage}${folderParam ? `&folder=${folderParam}` : ''}`;
            
            // AJAX-запрос для получения следующей страницы
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                isLoading = false;
                loader.classList.add('d-none');
                
                if (data.html) {
                    // Создаем временный элемент для парсинга HTML
                    const tempContainer = document.createElement('div');
                    tempContainer.innerHTML = data.html;
                    
                    // Ищем контейнер для карточек или создаем новый
                    let cardContainer = document.querySelector('.certificate-row');
                    
                    if (!cardContainer) {
                        const newRow = document.createElement('div');
                        newRow.className = 'row row-cols-3 row-cols-sm-3 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3 card-grid certificate-row';
                        certificatesContainer.appendChild(newRow);
                        cardContainer = newRow;
                    }
                    
                    // Добавляем новые сертификаты в контейнер
                    const cards = tempContainer.querySelectorAll('.col');
                    cards.forEach(card => {
                        // Добавляем класс h-100 для поддержания одинаковой высоты
                        card.classList.add('h-100');
                        cardContainer.appendChild(card);
                        
                        // Добавляем плавную анимацию появления
                        setTimeout(() => {
                            card.classList.add('certificate-item-visible');
                        }, 10);
                    });
                    
                    // Обновляем состояние пагинации
                    hasMorePages = data.has_more_pages;
                    
                    // Инициализируем обработчики для новых карточек
                    if (typeof initDoubleClickHandlers === 'function') {
                        initDoubleClickHandlers();
                    }
                    
                    // Эта функция выравнивает высоту карточек
                    equalizeCardHeights();
                } else {
                    hasMorePages = false;
                }
                
                // Если больше нет страниц, показываем сообщение
                if (!hasMorePages) {
                    const endMessage = document.createElement('div');
                    endMessage.className = 'text-center my-4';
                    endMessage.innerHTML = '<p class="text-muted">Все документы загружены</p>';
                    certificatesContainer.appendChild(endMessage);
                }
            })
            .catch(error => {
                console.error('Ошибка при загрузке документов:', error);
                isLoading = false;
                loader.classList.add('d-none');
                
                // Показываем сообщение об ошибке
                const errorMessage = document.createElement('div');
                errorMessage.className = 'alert alert-danger my-3';
                errorMessage.textContent = 'Произошла ошибка при загрузке документов. Попробуйте обновить страницу.';
                certificatesContainer.appendChild(errorMessage);
            });
        }
        
        // Функция для выравнивания высоты карточек
        function equalizeCardHeights() {
            const cards = document.querySelectorAll('.certificate-card');
            if (cards.length === 0) return;
            
            // Сбрасываем высоту всех карточек
            cards.forEach(card => card.style.height = 'auto');
            
            // Находим максимальную высоту
            let maxHeight = 0;
            cards.forEach(card => {
                const height = card.offsetHeight;
                if (height > maxHeight) {
                    maxHeight = height;
                }
            });
            
            // Устанавливаем одинаковую высоту для всех карточек
            if (maxHeight > 0) {
                cards.forEach(card => card.style.minHeight = maxHeight + 'px');
            }
        }
        
        // Вызываем функцию выравнивания высоты после загрузки страницы
        window.addEventListener('load', equalizeCardHeights);
        
        // Также вызываем при изменении размера окна
        window.addEventListener('resize', equalizeCardHeights);
        
        // Добавляем обработчик события прокрутки
        window.addEventListener('scroll', checkScroll);
        
        // Проверяем прокрутку сразу после загрузки страницы
        // для случаев, когда контента мало и прокрутка не требуется
        setTimeout(checkScroll, 500);
    });
    </script>

    <!-- Стили для анимации новых карточек и сетки карточек -->
    <style>
        /* Улучшенные стили для визуализации сетки */
        .grid-container {
            position: relative;
            min-height: 200px; /* Минимальная высота для контейнера */
        }

        /* Добавляем визуальную сетку на заднем плане */
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
        
        /* Убираем скругление углов для карточек сертификатов */
        .certificate-card {
            border-radius: 0 !important;
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

    <script>
        // JavaScript для добавления номеров ячеек
        document.addEventListener('DOMContentLoaded', function() {
            // Добавляем номера к ячейкам сетки для лучшей визуализации
            const gridCells = document.querySelectorAll('.grid-cell');
            gridCells.forEach((cell, index) => {
                cell.setAttribute('data-cell-number', (index + 1));
            });
        });
    </script>
@endsection
