<div class="d-flex align-items-center justify-content-between  profile-header-mobile">
    <!-- Кнопка с иконкой info -->
    <button type="button" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" 
            style="width: 40px; height: 40px;" title="Информация" 
            data-bs-toggle="modal" data-bs-target="#userInfoModal">
        <i class="fa-solid fa-info"></i>
    </button>
    
    <!-- Аватар пользователя - теперь кликабельный -->
    <div class="position-relative"> <a href="<?php echo e(route('profile.index')); ?>" class="avatar-container avatar-link " title="Перейти в профиль">
        <?php if(Auth::user()->avatar): ?>
            <img src="<?php echo e(asset('storage/' . Auth::user()->avatar)); ?>" alt="<?php echo e(Auth::user()->name); ?>" 
                 class="rounded-circle shadow-sm border border-2 border-white" 
                 style="width: 104px; height: 104px; object-fit: cover;">
                  <!-- Кнопка тарифа/плана -->
      
        <?php else: ?>
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm"
                 style="width: 104px; height: 104px; font-size: 1.5rem;">
                <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

            </div>
        <?php endif; ?>
        
       
    </a>  <a href="<?php echo e(route('subscription.plans')); ?>" class="plan-badge position-absolute" title="Ваш тариф: <?php echo e(Auth::user()->currentPlan()->name); ?>">
            <div class="badge-icon">
                <i class="fa-solid fa-crown"></i>
            </div>
        </a></div>
   
    
    <!-- Кнопка с иконкой уведомлений - теперь открывает модальное окно -->
    <button type="button" 
            class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center position-relative"
            style="width: 40px; height: 40px;" title="Уведомления"
            data-bs-toggle="modal" data-bs-target="#userMessagesModal" id="notificationsButton">
        <i class="fa-solid fa-bell"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="unreadNotificationsCount">
            <?php echo e(Auth::user()->unreadNotificationsCount() > 9 ? '9+' : Auth::user()->unreadNotificationsCount()); ?>

            <span class="visually-hidden">непрочитанных уведомлений</span>
        </span>
    </button>
</div>

<!-- Модальное окно с информацией о пользователе - полноэкранное -->
<div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-white">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="userInfoModalLabel">Профиль и аналитика</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <?php if(Auth::user()->avatar): ?>
                        <img src="<?php echo e(asset('storage/' . Auth::user()->avatar)); ?>" alt="<?php echo e(Auth::user()->name); ?>" 
                             class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width: 120px; height: 120px; font-size: 3rem;">
                            <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                        </div>
                    <?php endif; ?>
                    <h4 class="mb-1"><?php echo e(Auth::user()->name); ?></h4>
                    <p class="text-muted mb-3"><?php echo e(Auth::user()->email); ?></p>
                    
                    <?php if(Auth::user()->phone): ?>
                        <p class="mb-3">
                            <i class="fa-solid fa-phone me-1"></i> 
                            <?php echo e(Auth::user()->phone); ?>

                        </p>
                    <?php endif; ?>
                    <div class="me-3 p-2 bg-light rounded d-inline-block">
                        <span class="text-muted me-1">Стики:</span>
                        <span class="fw-bold"><?php echo e(Auth::user()->sticks); ?></span>
                        <i class="fas fa-certificate text-warning ms-1" title="Стики - валюта для создания сертификатов"></i>
                    </div>
                </div>
                
                <!-- Вкладки для аналитики -->
                <?php if(Auth::user()->hasRole('predprinimatel')): ?>
                    <ul class="nav nav-tabs mb-4" id="analyticsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats-tab-pane" 
                                type="button" role="tab" aria-controls="stats-tab-pane" aria-selected="true">
                                <i class="fa-solid fa-chart-line me-2"></i>Статистика
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="analyticsTabContent">
                        <!-- Вкладка статистики -->
                        <div class="tab-pane fade show active" id="stats-tab-pane" role="tabpanel" aria-labelledby="stats-tab" tabindex="0">
                            <div id="statistics-content" class="position-relative">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Загрузка...</span>
                                    </div>
                                    <p class="mt-2">Загрузка статистики...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Сообщение для обычных пользователей -->
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Аналитика доступна только для предпринимателей
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript для загрузки аналитики через AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация загрузки данных аналитики при открытии модального окна
    const userInfoModal = document.getElementById('userInfoModal');
    
    if (userInfoModal) {
        userInfoModal.addEventListener('show.bs.modal', function() {
            // Проверяем, есть ли у пользователя роль предпринимателя
            <?php if(Auth::user()->hasRole('predprinimatel')): ?>
                // Загружаем статистику сразу при открытии модального окна
                loadAnalyticsTab('statistics');
            <?php endif; ?>
        });
        
        // Обработчики переключения вкладок
        const statTab = document.getElementById('stats-tab');
        
        if (statTab) {
            statTab.addEventListener('shown.bs.tab', function() {
                loadAnalyticsTab('statistics');
            });
        }
    }
    
    // Функция для загрузки содержимого вкладки аналитики через AJAX
    function loadAnalyticsTab(type) {
        const contentContainer = type === 'statistics' ? 
            document.getElementById('statistics-content') : 
            document.getElementById('reports-content');
            
        if (!contentContainer) return;
        
        // Показываем индикатор загрузки
        contentContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <p class="mt-2">Загрузка ${type === 'statistics' ? 'статистики' : 'отчетов'}...</p>
            </div>
        `;
        
        // Определяем URL для запроса в зависимости от типа
        const url = type === 'statistics' ? 
            '<?php echo e(route("entrepreneur.analytics.statistics")); ?>' : 
            '<?php echo e(route("entrepreneur.analytics.reports")); ?>';
            
        // Отправляем AJAX запрос
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Ошибка загрузки данных: ' + response.status);
            }
            return response.text();
        })
        .then(html => {
            // Обновляем контейнер с содержимым вкладки
            contentContainer.innerHTML = html;
            
            // Инициализируем любые скрипты или графики, если они есть в загруженном HTML
            if (type === 'statistics') {
                initializeStatisticsCharts();
            }
        })
        .catch(error => {
            contentContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    Произошла ошибка при загрузке данных: ${error.message}
                </div>
            `;
            console.error('Ошибка при загрузке аналитики:', error);
        });
    }
    
    // Функция для инициализации графиков в статистике
    function initializeStatisticsCharts() {
        // Проверяем, есть ли в загруженном контенте canvas для графиков
        const chartCanvas = document.getElementById('certificatesChart');
        if (chartCanvas && window.Chart) {
            // Графики будут инициализированы через код, загруженный в HTML
            console.log('Графики статистики инициализированы');
        }
    }
});
</script>

<!-- Модальное окно с уведомлениями пользователя - полноэкранное -->
<div class="modal fade" id="userMessagesModal" tabindex="-1" aria-labelledby="userMessagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-white">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="userMessagesModalLabel">Уведомления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <!-- Панель действий с уведомлениями -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active" data-filter="all">Все</button>
                            <button type="button" class="btn btn-outline-primary" data-filter="unread">Непрочитанные</button>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="markAllAsReadBtn">
                            <i class="fa-solid fa-check-double me-1"></i>Отметить все как прочитанные
                        </button>
                    </div>
                </div>
                
                <!-- Контейнер для списка уведомлений -->
                <div id="notificationsContainer" class="notifications-list">
                    <!-- Заглушка для отображения перед загрузкой данных -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Загрузка...</span>
                        </div>
                        <p class="mt-3 text-muted">Загрузка уведомлений...</p>
                    </div>
                </div>
                
                <!-- Кнопка "Загрузить еще" -->
                <div class="text-center mt-3 mb-4 d-none" id="loadMoreNotificationsContainer">
                    <button type="button" class="btn btn-outline-primary" id="loadMoreNotifications">
                        <i class="fa-solid fa-arrow-down me-1"></i>Загрузить еще
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?php echo e(route('notifications.index')); ?>" class="btn btn-primary">
                    Все уведомления <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.notifications-list .notification-item {
    border-left: 3px solid #e9ecef;
    transition: all 0.2s ease;
    position: relative;
}

.notifications-list .notification-item:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.notifications-list .notification-item.unread {
    border-left-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.03);
}

.notifications-list .notification-item .notification-icon {
    width: 50px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notifications-list .notification-item .notification-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.notifications-list .notification-item:hover .notification-actions {
    opacity: 1;
}

.notifications-list .notification-item .notification-time {
    font-size: 0.75rem;
    color: #6c757d;
}

.notifications-list .notification-item .notification-title {
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.notifications-list .notification-item .notification-message {
    color: #212529;
    margin-bottom: 0;
}

/* Анимация для новых уведомлений */
@keyframes highlight {
    0% { background-color: rgba(13, 110, 253, 0.2); }
    100% { background-color: rgba(13, 110, 253, 0.03); }
}

.notifications-list .notification-item.highlight {
    animation: highlight 2s ease;
}

/* Стили для пустого списка */
.notifications-list .empty-notifications {
    text-align: center;
    padding: 40px 0;
}

.notifications-list .empty-notifications i {
    font-size: 3rem;
    color: #6c757d;
    opacity: 0.4;
    margin-bottom: 1rem;
}

.notifications-list .empty-notifications p {
    color: #6c757d;
    margin-bottom: 0.5rem;
}
</style>

<script>
    // Загрузка уведомлений через AJAX при открытии модального окна
    document.addEventListener('DOMContentLoaded', function() {
        const messagesModal = document.getElementById('userMessagesModal');
        const notificationsButton = document.getElementById('notificationsButton');
        const unreadNotificationsCount = document.getElementById('unreadNotificationsCount');
        const notificationsContainer = document.getElementById('notificationsContainer');
        const markAllAsReadBtn = document.getElementById('markAllAsReadBtn');
        const loadMoreNotificationsBtn = document.getElementById('loadMoreNotifications');
        const loadMoreNotificationsContainer = document.getElementById('loadMoreNotificationsContainer');
        
        // Переменные для пагинации
        let currentPage = 1;
        let lastPage = 1;
        
        // Текущий фильтр (all, unread)
        let currentFilter = 'all';
        
        if (messagesModal) {
            messagesModal.addEventListener('show.bs.modal', function() {
                // Сбрасываем переменные пагинации при каждом открытии модального окна
                currentPage = 1;
                
                // Загружаем уведомления
                loadNotifications();
            });
        }
        
        // Обработчики для кнопок фильтрации
        document.querySelectorAll('[data-filter]').forEach(button => {
            button.addEventListener('click', function() {
                // Убираем активный класс у всех кнопок
                document.querySelectorAll('[data-filter]').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Добавляем активный класс текущей кнопке
                this.classList.add('active');
                
                // Устанавливаем текущий фильтр
                currentFilter = this.dataset.filter;
                
                // Сбрасываем страницу
                currentPage = 1;
                
                // Загружаем уведомления с новым фильтром
                loadNotifications(true);
            });
        });
        
        // Обработчик для кнопки "Отметить все как прочитанные"
        if (markAllAsReadBtn) {
            markAllAsReadBtn.addEventListener('click', function() {
                fetch('<?php echo e(route("notifications.mark-all-read")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновляем счетчик непрочитанных уведомлений
                        updateUnreadCount(data.unread_count);
                        
                        // Обновляем стили уведомлений в контейнере
                        document.querySelectorAll('.notification-item.unread').forEach(item => {
                            item.classList.remove('unread');
                        });
                        
                        // Если текущий фильтр - непрочитанные, обновляем список
                        if (currentFilter === 'unread') {
                            loadNotifications(true);
                        }
                    }
                })
                .catch(error => {
                    console.error('Ошибка при отметке всех уведомлений как прочитанных:', error);
                });
            });
        }
        
        // Обработчик для кнопки "Загрузить еще"
        if (loadMoreNotificationsBtn) {
            loadMoreNotificationsBtn.addEventListener('click', function() {
                currentPage++;
                loadNotifications(false);
            });
        }
        
        // Функция загрузки уведомлений
        function loadNotifications(replace = true) {
            // Показываем индикатор загрузки, если это первая загрузка или замена списка
            if (replace) {
                notificationsContainer.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Загрузка...</span>
                        </div>
                        <p class="mt-3 text-muted">Загрузка уведомлений...</p>
                    </div>
                `;
            } else {
                // Для подгрузки показываем индикатор рядом с кнопкой
                loadMoreNotificationsBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm mr-2"></span> Загрузка...
                `;
                loadMoreNotificationsBtn.disabled = true;
            }
            
            // Формируем URL с параметрами
            let url = new URL('<?php echo e(route("notifications.index")); ?>');
            url.searchParams.append('page', currentPage);
            
            if (currentFilter === 'unread') {
                url.searchParams.append('read', '0');
            }
            
            // Отправляем запрос на получение уведомлений
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Если это первая загрузка или замена, очищаем контейнер
                if (replace) {
                    notificationsContainer.innerHTML = '';
                }
                
                // Добавляем HTML с уведомлениями
                notificationsContainer.innerHTML += data.html;
                
                // Обновляем информацию о пагинации
                lastPage = data.has_more ? currentPage + 1 : currentPage;
                
                // Обновляем видимость кнопки "Загрузить еще"
                loadMoreNotificationsContainer.classList.toggle('d-none', !data.has_more);
                
                // Возвращаем кнопке нормальный текст
                loadMoreNotificationsBtn.innerHTML = `<i class="fa-solid fa-arrow-down me-1"></i>Загрузить еще`;
                loadMoreNotificationsBtn.disabled = false;
                
                // Обновляем счетчик непрочитанных уведомлений
                updateUnreadCount(data.unread_count);
                
                // Добавляем обработчики событий для кнопок действий с уведомлениями
                setupNotificationActions();
            })
            .catch(error => {
                console.error('Ошибка загрузки уведомлений:', error);
                notificationsContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Произошла ошибка при загрузке уведомлений. Пожалуйста, попробуйте еще раз.
                    </div>
                `;
                
                // Возвращаем кнопке нормальный текст
                loadMoreNotificationsBtn.innerHTML = `<i class="fa-solid fa-arrow-down me-1"></i>Загрузить еще`;
                loadMoreNotificationsBtn.disabled = false;
            });
        }
        
        // Функция для обновления счетчика непрочитанных уведомлений
        function updateUnreadCount(count) {
            if (unreadNotificationsCount) {
                unreadNotificationsCount.textContent = count > 9 ? '9+' : count;
                unreadNotificationsCount.classList.toggle('d-none', count === 0);
            }
        }
        
        // Функция для настройки обработчиков событий на кнопки действий с уведомлениями
        function setupNotificationActions() {
            // Обработчики для кнопок "Отметить как прочитанное"
            document.querySelectorAll('.mark-read-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const notificationId = this.dataset.id;
                    const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    
                    fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Обновляем счетчик непрочитанных уведомлений
                            updateUnreadCount(data.unread_count);
                            
                            // Удаляем класс unread у уведомления
                            notificationItem.classList.remove('unread');
                            
                            // Скрываем кнопку "Отметить как прочитанное"
                            this.style.display = 'none';
                            
                            // Если текущий фильтр - непрочитанные, скрываем это уведомление
                            if (currentFilter === 'unread') {
                                notificationItem.style.display = 'none';
                                
                                // Если не осталось уведомлений, показываем сообщение
                                if (!document.querySelector('.notification-item[style="display: block;"]')) {
                                    loadNotifications(true);
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при отметке уведомления как прочитанного:', error);
                    });
                });
            });
            
            // Обработчики для кнопок удаления уведомления
            document.querySelectorAll('.delete-notification-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (!confirm('Вы уверены, что хотите удалить это уведомление?')) {
                        return;
                    }
                    
                    const notificationId = this.dataset.id;
                    const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    
                    fetch(`/notifications/${notificationId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Обновляем счетчик непрочитанных уведомлений
                            updateUnreadCount(data.unread_count);
                            
                            // Удаляем уведомление из DOM с анимацией
                            notificationItem.style.opacity = '0';
                            setTimeout(() => {
                                notificationItem.remove();
                                
                                // Если не осталось уведомлений, показываем сообщение
                                if (!document.querySelector('.notification-item')) {
                                    loadNotifications(true);
                                }
                            }, 300);
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка при удалении уведомления:', error);
                    });
                });
            });
            
            // Обработчики для клика по уведомлению
            document.querySelectorAll('.notification-item-link').forEach(link => {
                link.addEventListener('click', function() {
                    const notificationId = this.closest('.notification-item').dataset.id;
                    const isUnread = this.closest('.notification-item').classList.contains('unread');
                    
                    // Если уведомление непрочитано, отмечаем его как прочитанное
                    if (isUnread) {
                        fetch(`/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Обновляем счетчик непрочитанных уведомлений
                                updateUnreadCount(data.unread_count);
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка при отметке уведомления как прочитанного:', error);
                        });
                    }
                });
            });
        }
    });
</script>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/profile/partials/_profile.blade.php ENDPATH**/ ?>