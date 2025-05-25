/**
 * Обработчик для работы с iframe документов
 */
document.addEventListener('DOMContentLoaded', function() {
    // Слушаем сообщения из родительского окна
    window.addEventListener('message', function(event) {
        // Проверяем, что сообщение имеет ожидаемый тип
        if (event.data && event.data.type === 'update_logo') {
            try {
                updateLogo(event.data.logo_url);
            } catch (error) {
                // Отправляем информацию об ошибке обратно
                event.source.postMessage({
                    type: 'logo_updated',
                    success: false,
                    error: error.message
                }, '*');
            }
        }
    });
    
    // Функция для обновления логотипа
    function updateLogo(logoUrl) {
        // Находим все элементы с классом company-logo
        const companyLogoElements = document.querySelectorAll('.company-logo');
        let count = 0;
        
        if (companyLogoElements && companyLogoElements.length > 0) {
            // Обновляем src для каждого элемента
            companyLogoElements.forEach(function(element) {
                if (logoUrl === 'none') {
                    element.style.display = 'none';
                } else {
                    element.src = logoUrl;
                    element.style.display = '';
                    count++;
                }
            });
            
            // Отправляем сообщение обратно о успешном обновлении
            window.parent.postMessage({
                type: 'logo_updated',
                success: true,
                count: count
            }, '*');
        } else {
            // Отправляем сообщение об отсутствии элементов для обновления
            window.parent.postMessage({
                type: 'logo_updated',
                success: false,
                error: 'Не найдены элементы с классом company-logo'
            }, '*');
        }
    }
    
    // Отправляем сообщение о загрузке документа
    window.parent.postMessage({
        type: 'certificate_loaded',
        template: document.querySelector('meta[name="template-id"]')?.content || 'unknown'
    }, '*');
});
