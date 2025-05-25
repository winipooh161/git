/**
 * Скрипт для обработки предпросмотра шаблона документа
 */

document.addEventListener('DOMContentLoaded', function() {
    // Сообщаем родительскому окну, что iframe готов
    if (window.parent !== window) {
        window.parent.postMessage({
            type: 'iframe_ready'
        }, '*');
        console.log('Отправлено сообщение о готовности iframe');
    }
    
    // Обработчик сообщений от родительского окна
    window.addEventListener('message', function(event) {
        if (!event.data || !event.data.type) return;
        
        try {
            console.log('Получено сообщение от родительского окна:', event.data);
            
            // Обработка разных типов сообщений
            switch (event.data.type) {
                case 'update_logo':
                    updateLogo(event.data.logo_url);
                    break;
                    
                case 'update_fields':
                    updateTemplateFields(event.data.fields);
                    break;
            }
        } catch (error) {
            console.error('Ошибка обработки сообщения:', error);
            
            // Отправляем сообщение об ошибке обратно
            if (window.parent !== window) {
                window.parent.postMessage({
                    type: event.data.type + '_error',
                    error: error.message
                }, '*');
            }
        }
    });
    
    /**
     * Обновление логотипа
     */
    function updateLogo(logoUrl) {
        console.log('Обновление логотипов на:', logoUrl);
        
        if (!logoUrl || logoUrl === 'none') {
            removeAllLogos();
            return;
        }
        
        // Обновляем все элементы с классом company-logo
        const companyLogoElements = document.querySelectorAll('.company-logo');
        console.log('Найдено элементов с классом company-logo:', companyLogoElements.length);
        
        if (companyLogoElements.length > 0) {
            companyLogoElements.forEach(function(img) {
                img.src = logoUrl;
                img.onload = function() {
                    console.log('Изображение логотипа успешно загружено:', logoUrl);
                    console.log('Найдено элементов с классом company-logo:', companyLogoElements.length);
                    
                    // Добавляем класс к родительским контейнерам
                    const logoContainers = document.querySelectorAll('.logo-container');
                    if (logoContainers.length > 0) {
                        console.log('Найдено контейнеров логотипа:', logoContainers.length);
                        logoContainers.forEach(function(container) {
                            const containerImg = container.querySelector('img');
                            // Используем преобразование NodeList в Array для метода includes
                            if (containerImg && Array.from(companyLogoElements).includes(containerImg)) {
                                console.log('Обновлен элемент с классом company-logo');
                                container.classList.add('logo-loaded');
                            }
                        });
                    }
                };
                
                img.onerror = function() {
                    console.error('Ошибка загрузки логотипа:', logoUrl);
                };
            });
        }
        
        // Также проверяем все изображения, у которых src или data-src содержит "logo"
        const allImages = document.querySelectorAll('img');
        const logoImages = Array.from(allImages).filter(img => 
            (img.src && img.src.includes('logo')) || 
            (img.dataset.src && img.dataset.src.includes('logo'))
        );
        
        console.log('Найдено изображений с logo в атрибутах:', logoImages.length);
        
        if (logoImages.length > 0) {
            logoImages.forEach(function(img) {
                if (!img.classList.contains('company-logo')) {
                    img.src = logoUrl;
                }
            });
        }
        
        // Сообщаем родительскому окну, что логотип обновлен
        if (window.parent !== window) {
            window.parent.postMessage({
                type: 'logo_updated',
                success: true
            }, '*');
        }
    }
    
    /**
     * Удаление всех логотипов
     */
    function removeAllLogos() {
        const logoElements = document.querySelectorAll('.company-logo, .logo-container');
        logoElements.forEach(function(el) {
            if (el.tagName === 'IMG') {
                el.style.display = 'none';
            } else {
                el.classList.add('no-logo');
                el.classList.remove('logo-loaded');
            }
        });
        
        // Сообщаем родительскому окну, что логотип обновлен
        if (window.parent !== window) {
            window.parent.postMessage({
                type: 'logo_updated',
                success: true,
                removed: true
            }, '*');
        }
    }
    
    /**
     * Обновление полей шаблона - улучшенная версия с расширенным поиском элементов
     */
    function updateTemplateFields(fields) {
        if (!fields) return;
        
        console.log('Обновление полей шаблона:', fields);
        
        // Обходим все переданные поля и обновляем соответствующие элементы
        Object.keys(fields).forEach(function(fieldKey) {
            const value = fields[fieldKey];
            
            // Формируем список возможных селекторов для элементов с этим полем
            const selectors = [
                // Стандартные селекторы используемые ранее
                `.template-field-${fieldKey}`,
                `[data-field="${fieldKey}"]`,
                
                // Дополнительные селекторы для поиска
                `.field-${fieldKey}`,
                `#field-${fieldKey}`,
                `.${fieldKey}-field`,
                `#${fieldKey}`,
                `.${fieldKey}`,
                `[data-template-field="${fieldKey}"]`,
                
                // Специальные случаи для типичных полей
                fieldKey === 'recipient_name' ? '.recipient-name, .customer-name, .client-name' : null,
                fieldKey === 'amount' ? '.certificate-amount, .price-amount, .amount-value' : null,
                fieldKey === 'valid_until' ? '.expiry-date, .valid-date, .expiration-date' : null
            ].filter(Boolean); // Удаляем null значения
            
            // Формируем общий CSS-селектор из всех возможных вариантов
            const combinedSelector = selectors.join(',');
            const elements = document.querySelectorAll(combinedSelector);
            
            console.log(`Поиск элементов для поля ${fieldKey} по селектору '${combinedSelector}': найдено ${elements.length}`);
            
            if (elements.length > 0) {
                elements.forEach(function(el, index) {
                    // Проверка типа элемента перед обновлением
                    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT') {
                        el.value = value;
                        // Вызов события change для инициирования обновления связанных элементов
                        const event = new Event('change', { bubbles: true });
                        el.dispatchEvent(event);
                        console.log(`Обновлен элемент формы #${index} для поля ${fieldKey}: ${value}`);
                    } else {
                        el.textContent = value;
                        console.log(`Обновлен текстовый элемент #${index} для поля ${fieldKey}: ${value}`);
                    }
                });
            } else {
                console.warn(`Не найдены элементы для обновления поля ${fieldKey}`);
                
                // Попробуем поискать по атрибуту name для форм
                const formElements = document.querySelectorAll(`[name="${fieldKey}"]`);
                if (formElements.length > 0) {
                    formElements.forEach(function(el) {
                        el.value = value;
                        console.log(`Обновлен элемент формы по name=${fieldKey}: ${value}`);
                    });
                }
            }
        });
        
        // Поиск и обновление специфических элементов, если такие есть
        tryUpdateSpecificElements(fields);
        
        // Сообщаем родительскому окну, что поля обновлены
        if (window.parent !== window) {
            window.parent.postMessage({
                type: 'fields_updated',
                success: true
            }, '*');
            console.log('Отправлено сообщение об успешном обновлении полей');
        }
    }
    
    /**
     * Попытка обновить специфические элементы, которые могут не соответствовать общему шаблону именования
     */
    function tryUpdateSpecificElements(fields) {
        // Поиск заголовка документа, если есть recipient_name
        if (fields.recipient_name) {
            const titleElements = document.querySelectorAll('h1, h2, h3, .certificate-title, .title');
            if (titleElements.length > 0) {
                titleElements.forEach(el => {
                    // Проверяем, есть ли в заголовке вхождения текста "документ" и нет ли конкретного имени
                    const lcText = el.textContent.toLowerCase();
                    if ((lcText.includes('документ') || lcText.includes('certificate')) && 
                        !lcText.includes(fields.recipient_name.toLowerCase())) {
                        // Добавляем имя получателя к заголовку
                        if (!el.dataset.originalText) {
                            el.dataset.originalText = el.textContent;
                        }
                        el.textContent = el.dataset.originalText + ' для ' + fields.recipient_name;
                        console.log(`Обновлен заголовок документа с добавлением имени получателя: ${fields.recipient_name}`);
                    }
                });
            }
        }
        
        // Специфическая обработка для суммы/номинала
        if (fields.amount) {
            const amountValue = fields.amount;
            // Пытаемся отформатировать сумму, если это число
            let formattedAmount = amountValue;
            if (!isNaN(parseFloat(amountValue))) {
                formattedAmount = Number(amountValue).toLocaleString('ru-RU') + ' ₽';
            }
            
            // Ищем ценники, метки стоимости и т.д.
            const priceElements = document.querySelectorAll('.price, .amount, .value, .sum, .certificate-sum');
            if (priceElements.length > 0) {
                priceElements.forEach(el => {
                    // Только если элемент не содержит дочерних элементов (простой текст)
                    if (el.children.length === 0) {
                        el.textContent = formattedAmount;
                        console.log(`Обновлен элемент суммы: ${formattedAmount}`);
                    }
                });
            }
        }
    }

    // Запускаем логирование для отладки
    console.log('Script template-preview.js loaded and initialized');
    
    // Логирование структуры DOM для отладки
    console.log('Структура DOM iframe:');
    console.log('Теги body:', document.body.innerHTML.slice(0, 500) + '...');
    
    // Находим основные контейнеры документа
    const certificateContainers = document.querySelectorAll('.certificate, .certificate-container, .template-container, main');
    console.log(`Найдено контейнеров документа: ${certificateContainers.length}`);
});
