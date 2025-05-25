/**
 * Обработчик системы уведомлений
 */
class NotificationHandler {
    constructor(options = {}) {
        this.options = {
            countSelector: '#unreadNotificationsCount',
            buttonSelector: '#notificationsButton',
            containerSelector: '#notificationsContainer',
            modalContentSelector: '#notificationsModalContent',
            modalSelector: '#userMessagesModal',
            checkIntervalSeconds: 60, // Проверка новых уведомлений каждую минуту
            ...options
        };
        
        this.unreadCount = 0;
        this.checkInterval = null;
        this.notificationSound = null;
        
        this.init();
    }
    
    /**
     * Инициализация обработчика уведомлений
     */
    init() {
        // Получаем начальное количество непрочитанных уведомлений
        this.updateUnreadCount();
        
        // Настраиваем звук уведомлений
        this.setupNotificationSound();
        
        // Запускаем периодическую проверку новых уведомлений
        this.startChecking();
        
        // Настраиваем обработчики событий
        this.setupEventHandlers();
        
        console.log('NotificationHandler initialized');
    }
    
    /**
     * Настройка звука уведомлений
     */
    setupNotificationSound() {
        this.notificationSound = new Audio('/sounds/notification.mp3');
        this.notificationSound.volume = 0.5;
        
        // Предзагружаем звук
        this.notificationSound.load();
    }
    
    /**
     * Настройка обработчиков событий
     */
    setupEventHandlers() {
        // Обработчик для кнопки уведомлений
        const button = document.querySelector(this.options.buttonSelector);
        if (button) {
            button.addEventListener('click', () => this.loadNotifications());
        }
        
        // Обработчик вкладки браузера для проверки уведомлений при возврате на страницу
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.updateUnreadCount();
            }
        });
        
        // Глобальный обработчик для кнопок "Отметить как прочитанное"
        document.addEventListener('click', (e) => {
            if (e.target.closest('.mark-read-btn')) {
                e.preventDefault();
                const button = e.target.closest('.mark-read-btn');
                const notificationId = button.dataset.id;
                this.markAsRead(notificationId, button);
            }
        });
        
        // Глобальный обработчик для кнопок удаления уведомлений
        document.addEventListener('click', (e) => {
            if (e.target.closest('.delete-notification-btn')) {
                e.preventDefault();
                const button = e.target.closest('.delete-notification-btn');
                const notificationId = button.dataset.id;
                this.deleteNotification(notificationId, button);
            }
        });
    }
    
    /**
     * Запуск периодической проверки уведомлений
     */
    startChecking() {
        // Периодически проверяем наличие новых уведомлений
        this.checkInterval = setInterval(() => {
            this.updateUnreadCount();
        }, this.options.checkIntervalSeconds * 1000);
    }
    
    /**
     * Остановка периодической проверки
     */
    stopChecking() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
        }
    }
    
    /**
     * Обновление счетчика непрочитанных уведомлений
     */
    updateUnreadCount() {
        fetch('/notifications/unread', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            const prevCount = this.unreadCount;
            this.unreadCount = data.unread_count;
            
            // Обновляем отображение счетчика
            this.updateCountBadge();
            
            // Если появились новые уведомления и пользователь находится на странице
            if (this.unreadCount > prevCount && document.visibilityState === 'visible') {
                this.notifyNewMessages();
            }
        })
        .catch(error => console.error('Ошибка при проверке уведомлений:', error));
    }
    
    /**
     * Обновление визуального отображения счетчика
     */
    updateCountBadge() {
        const badge = document.querySelector(this.options.countSelector);
        if (badge) {
            if (this.unreadCount > 0) {
                badge.textContent = this.unreadCount > 9 ? '9+' : this.unreadCount;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }
        
        // Обновляем иконку кнопки
        const button = document.querySelector(this.options.buttonSelector);
        if (button) {
            if (this.unreadCount > 0) {
                button.classList.add('has-notifications');
            } else {
                button.classList.remove('has-notifications');
            }
        }
    }
    
    /**
     * Уведомление пользователя о новых сообщениях
     */
    notifyNewMessages() {
        // Воспроизводим звук уведомления
        if (this.notificationSound) {
            this.notificationSound.play().catch(e => console.warn('Не удалось воспроизвести звук уведомления:', e));
        }
        
        // Анимируем кнопку уведомлений
        const button = document.querySelector(this.options.buttonSelector);
        if (button) {
            button.classList.add('notification-pulse');
            setTimeout(() => {
                button.classList.remove('notification-pulse');
            }, 2000);
        }
        
        // Показываем всплывающее уведомление, если API доступен
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Новые уведомления', {
                body: 'У вас есть новые непрочитанные уведомления',
                icon: '/images/notification-icon.png'
            });
        } 
        // Запрашиваем разрешение, если еще не запрашивали
        else if ('Notification' in window && Notification.permission !== 'denied') {
            Notification.requestPermission();
        }
    }
    
    /**
     * Загрузка списка уведомлений в модальное окно
     */
    loadNotifications() {
        const modal = document.querySelector(this.options.modalSelector);
        const container = document.querySelector(this.options.modalContentSelector);
        
        if (modal && container) {
            // Показываем индикатор загрузки
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <p class="mt-3 text-muted">Загрузка уведомлений...</p>
                </div>
            `;
            
            // Загружаем уведомления через AJAX
            fetch('/notifications/unread', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Обновляем содержимое контейнера
                container.innerHTML = data.html;
                
                // Обновляем счетчик непрочитанных уведомлений
                this.unreadCount = data.unread_count;
                this.updateCountBadge();
            })
            .catch(error => {
                console.error('Ошибка при загрузке уведомлений:', error);
                container.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        Произошла ошибка при загрузке уведомлений. Пожалуйста, попробуйте позже.
                    </div>
                `;
            });
        }
    }
    
    /**
     * Отметка уведомления как прочитанного
     * 
     * @param {string} id ID уведомления
     * @param {Element} button Кнопка, вызвавшая действие
     */
    markAsRead(id, button) {
        const item = button.closest('.notification-item');
        
        // Отправляем запрос на сервер
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Удаляем класс unread у уведомления
                item.classList.remove('unread');
                
                // Обновляем счетчик непрочитанных уведомлений
                this.unreadCount = data.unread_count;
                this.updateCountBadge();
                
                // Скрываем кнопку и индикатор нового уведомления
                button.style.display = 'none';
                const newIndicator = item.querySelector('.new-indicator');
                if (newIndicator) newIndicator.style.display = 'none';
            }
        })
        .catch(error => console.error('Ошибка при отметке уведомления как прочитанного:', error));
    }
    
    /**
     * Удаление уведомления
     * 
     * @param {string} id ID уведомления
     * @param {Element} button Кнопка, вызвавшая действие
     */
    deleteNotification(id, button) {
        if (!confirm('Вы уверены, что хотите удалить это уведомление?')) {
            return;
        }
        
        const item = button.closest('.notification-item');
        
        // Отправляем запрос на сервер
        fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Анимируем удаление элемента
                item.style.opacity = '0';
                setTimeout(() => {
                    item.remove();
                    
                    // Обновляем счетчик непрочитанных уведомлений
                    this.unreadCount = data.unread_count;
                    this.updateCountBadge();
                    
                    // Проверяем, не опустел ли контейнер
                    const container = document.querySelector(this.options.containerSelector);
                    if (container && !container.querySelector('.notification-item')) {
                        this.loadNotifications(); // Перезагружаем список
                    }
                }, 300);
            }
        })
        .catch(error => console.error('Ошибка при удалении уведомления:', error));
    }
}

// Инициализация обработчика уведомлений при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    window.notificationHandler = new NotificationHandler();
    
    // Запрашиваем разрешение на отправку уведомлений, если браузер поддерживает
    if ('Notification' in window && Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        // Запрашиваем разрешение после взаимодействия пользователя со страницей
        document.addEventListener('click', function requestNotificationPermission() {
            Notification.requestPermission();
            document.removeEventListener('click', requestNotificationPermission);
        });
    }
});
