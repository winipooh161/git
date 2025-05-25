(function() {
    // Исправление ошибки companyLogoElements.includes is not a function
    window.addEventListener('DOMContentLoaded', function() {
        // Оригинальный NodeList.prototype.forEach
        const originalForEach = NodeList.prototype.forEach;

        // Патчим метод forEach для NodeList, чтобы перехватить использование .includes
        NodeList.prototype.forEach = function(callback, thisArg) {
            try {
                return originalForEach.call(this, callback, thisArg);
            } catch (error) {
                if (error.message && error.message.includes('includes is not a function')) {
                    // Используем Array.from для конвертации NodeList в массив
                    return Array.from(this).forEach(callback, thisArg);
                }
                throw error;
            }
        };

        // Обрабатываем случай, когда требуется проверка includes для NodeList
        // Перехватываем обращения к свойству includes для объектов типа NodeList
        const originalPropertyDescriptor = Object.getOwnPropertyDescriptor(Object.prototype, 'includes');
        
        // Создаем безопасную обертку для метода includes
        const safeIncludes = function(item) {
            // Для NodeList конвертируем в массив
            if (this instanceof NodeList || Object.prototype.toString.call(this) === '[object NodeList]') {
                return Array.from(this).includes(item);
            }
            // Для обычных объектов вызываем оригинальный метод
            if (originalPropertyDescriptor && originalPropertyDescriptor.value) {
                return originalPropertyDescriptor.value.call(this, item);
            }
            // Резервный вариант
            return false;
        };

        // Добавляем безопасный метод includes для NodeList
        if (!NodeList.prototype.includes) {
            NodeList.prototype.includes = function(item) {
                return Array.from(this).includes(item);
            };
        }

        // Сообщаем родительскому окну, что мы готовы
        if (window.parent !== window) {
            try {
                window.parent.postMessage({ 
                    type: 'iframe_ready', 
                    message: 'Iframe loaded and fixed NodeList issues'
                }, '*');
            } catch (e) {
                console.log('Failed to communicate with parent window');
            }
        }
    });

    // Слушаем сообщения от родительского окна
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'logo_elements_fix') {
            // Применяем дополнительные исправления
            console.log('Applying additional fixes from parent window');
            
            // Страховочный код для работы с компанией логотипами
            const originalGetElementsByClassName = document.getElementsByClassName;
            document.getElementsByClassName = function() {
                const elements = originalGetElementsByClassName.apply(this, arguments);
                if (arguments[0] && typeof arguments[0] === 'string' && 
                    arguments[0].includes('company-logo')) {
                    // Добавляем includes метод для этой конкретной коллекции
                    elements.includes = function(item) {
                        return Array.from(this).includes(item);
                    };
                }
                return elements;
            };
        }
    });
})();
