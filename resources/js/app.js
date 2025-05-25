// Импортируем bootstrap и делаем его доступным глобально
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Импортируем библиотеку IMask для создания масок ввода
import IMask from 'imask';
window.IMask = IMask;

// Импортируем функции для управления боковой панелью
import { initSidebar, initSidebarCollapse } from './sidebar';


/**
 * Безопасная функция вибрации, которая проверяет поддержку и не вызывает ошибок
 * Использует проверку на взаимодействие пользователя с документом
 * @param {number|number[]} pattern - Длительность вибрации или массив с паттерном
 */
window.safeVibrate = function(pattern) {
    try {
        // Проверяем поддержку вибрации и взаимодействие пользователя с документом
        if (navigator.vibrate && typeof navigator.vibrate === 'function' && 
            document.hasFocus() && document.wasInteractedWith) {
            navigator.vibrate(pattern);
        }
    } catch (e) {
        console.warn('Vibration API не поддерживается или недоступна', e);
    }
};

// Добавляем флаг для отслеживания взаимодействия пользователя с документом
document.wasInteractedWith = false;
document.addEventListener('click', function() {
    document.wasInteractedWith = true;
}, { once: false, passive: true, capture: true });

document.addEventListener('touchstart', function() {
    document.wasInteractedWith = true;
}, { once: false, passive: true, capture: true });

/**
 * Улучшенная функция для выполнения AJAX-запросов с обработкой ошибок
 * @param {string} url - URL для запроса
 * @param {Object} options - Параметры fetch
 * @returns {Promise} - Promise с результатом
 */
window.fetchWithErrorHandling = async function(url, options = {}) {
    try {
        // Добавляем CSRF-токен к запросам
        if (!options.headers) {
            options.headers = {};
        }
        
        if (!options.headers['Content-Type'] && !options.headers['content-type']) {
            options.headers['Content-Type'] = 'application/json';
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            options.headers['X-CSRF-TOKEN'] = csrfToken;
        }
        
        const response = await fetch(url, options);
        
        // Проверяем статус ответа
        if (!response.ok) {
            const contentType = response.headers.get('content-type');
            let errorData = {};
            
            // Пытаемся распарсить ошибку как JSON, если это возможно
            if (contentType && contentType.includes('application/json')) {
                try {
                    errorData = await response.json();
                } catch (e) {
                    console.error('Не удалось распарсить JSON ответ', e);
                }
            } else {
                errorData.message = `Ошибка HTTP: ${response.status} ${response.statusText}`;
            }
            
            throw new Error(errorData.message || `Ошибка HTTP: ${response.status}`);
        }
        
        // Проверяем, есть ли контент в ответе
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text();
        }
    } catch (error) {
        console.error('Ошибка при выполнении запроса:', error);
        throw error;
    }
};

// Функция инициализации масок ввода и валидации полей
function initInputMasks() {
    // Маска для телефона в формате +7 (XXX
    // ) XXX-XX-XX
  
    
    // Валидация для имени (только русские буквы)
    const nameInputs = document.querySelectorAll('input[name="name"]');
    if (nameInputs.length > 0) {
        nameInputs.forEach(input => {
            input.addEventListener('input', function() {
                // Оставляем только русские буквы, пробелы и дефисы
                this.value = this.value.replace(/[^а-яА-ЯёЁ\s-]/g, '');
                // Ограничиваем длину
                if (this.value.length > 255) {
                    this.value = this.value.substring(0, 255);
                }
            });
        });
    }
    
    // Ограничение для поля компании (до 70 символов)
    const companyInputs = document.querySelectorAll('input[name="company"]');
    if (companyInputs.length > 0) {
        companyInputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.length > 70) {
                    this.value = this.value.substring(0, 70);
                }
            });
        });
    }

    // Валидация email поля 
    const emailInputs = document.querySelectorAll('input[type="email"], input[name="email"]');
    if (emailInputs.length > 0) {
        emailInputs.forEach(input => {
            // Добавляем HTML5 паттерн для валидации
            input.setAttribute('pattern', '[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}');
            input.setAttribute('title', 'Введите корректный email адрес');
        });
    }
}

// Функция инициализации всех компонентов Bootstrap
function initBootstrapComponents() {
    // Инициализация тултипов
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltipTriggerList.length > 0) {
        [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    }
    
    // Инициализация поповеров
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    if (popoverTriggerList.length > 0) {
        [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    }

    // Инициализация каруселей (для testimonials)
    const carouselList = document.querySelectorAll('.carousel');
    if (carouselList.length > 0) {
        [...carouselList].map(carouselEl => new bootstrap.Carousel(carouselEl, {
            interval: 5000
        }));
    }
    
    // Инициализация аккордеонов (для FAQ)
    const accordionList = document.querySelectorAll('.accordion');
    if (accordionList.length > 0) {
        [...accordionList].forEach(accordionEl => {
            // К каждой кнопке аккордеона добавляем обработчик
            const buttons = accordionEl.querySelectorAll('.accordion-button');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    // Находим target из data-bs-target
                    const targetId = this.getAttribute('data-bs-target');
                    if (targetId) {
                        const target = document.querySelector(targetId);
                        if (target && bootstrap.Collapse) {
                            const collapse = new bootstrap.Collapse(target, {
                                toggle: true
                            });
                        }
                    }
                });
            });
        });
    }
}

// Вызываем функции инициализации при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация компонентов bootstrap
    initBootstrapComponents();
    
    // Инициализация масок для ввода
    initInputMasks();
    
    // Если есть функции для сайдбара, инициализируем их
    if (typeof initSidebar === 'function') {
        initSidebar();
    }
    
    if (typeof initSidebarCollapse === 'function') {
        initSidebarCollapse();
    }
});

// Экспортируем bootstrap для использования в других файлах
export { bootstrap };
