<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TemplatePreviewController extends Controller
{
    /**
     * Отображает шаблон документа для предпросмотра в iframe с возможностью редактирования полей.
     *
     * @param  CertificateTemplate  $template
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(CertificateTemplate $template, Request $request)
    {
        // Получаем данные из запроса или задаем значения по умолчанию
        $previewData = [
            'recipient_name' => $request->input('recipient_name', 'Иванов Иван'),
            'company_name' => $request->input('company_name', config('app.name')),
            'amount' => $request->input('amount', '3 000'),
            'valid_from' => $request->input('valid_from', date('d.m.Y')),
            'valid_until' => $request->input('valid_until', date('d.m.Y', strtotime('+3 month'))),
            'message' => $request->input('message', 'Ваше сообщение или пожелание'),
            'certificate_number' => $request->input('certificate_number', 'CERT-DEMO'),
            // Используем стандартный логотип по умолчанию
            'company_logo' => $request->input('company_logo', asset('images/default-logo.png')),
            'show_logo' => true,
            // Принудительно устанавливаем editable в false, если это указано в запросе
            'editable' => $request->query('editable') === 'true' // Только true, если явно указано "true"
        ];

        // Проверяем наличие файла шаблона
        $templatePath = public_path($template->template_path);
        if (!file_exists($templatePath)) {
            Log::error("Template file not found: {$templatePath}");
            return response('<div class="alert alert-danger">Файл шаблона не найден</div>')->header('Content-Type', 'text/html');
        }

        // Используем Blade для рендеринга шаблона
        $html = view()->file($templatePath, $previewData)->render();

        // Предварительно вычисляем значение для использования в JavaScript
        $isEditableValue = $previewData['editable'] ? 'true' : 'false';

        // Добавляем скрипт для обработки postMessage с обновлением полей в iframe
        $html .= <<<HTML
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Обрабатываем сообщения от родительского окна
            window.addEventListener('message', function(event) {
                console.log('Получено сообщение от родительского окна:', event.data);
                
                // Обработка деактивации редактирования
                if (event.data && event.data.type === 'disable_editing') {
                    try {
                        console.log('Деактивация редактирования полей');
                        disableEditing();
                    } catch (error) {
                        console.error('Ошибка при деактивации редактирования:', error);
                    }
                }
                
                // Обработка обновления логотипа
                if (event.data && event.data.type === 'update_logo') {
                    try {
                        const logoUrl = event.data.logo_url;
                        console.log('Обновление логотипа через postMessage:', logoUrl);
                        updateLogos(logoUrl);
                    } catch (error) {
                        console.error('Ошибка обновления логотипа:', error);
                    }
                }
                
                // Обработка обновления полей формы
                if (event.data && event.data.type === 'update_fields') {
                    try {
                        const formData = event.data.data;
                        console.log('Обновление полей формы:', formData);
                        updateFormFields(formData);
                    } catch (error) {
                        console.error('Ошибка обновления полей формы:', error);
                    }
                }
                
                // Обработка фикса для проблем с companyLogoElements
                if (event.data && event.data.type === 'logo_elements_fix') {
                    console.log('Применяем фикс для обработчиков логотипов:', event.data.message);
                    // Исправления будут применены автоматически
                }
            });
            
            // Функция обновления полей в документе
            function updateFormFields(data) {
                // Обновляем поля получателя, если они существуют
                if (data.recipient_name) {
                    const recipientElements = document.querySelectorAll('[data-field="recipient_name"], .recipient-name');
                    recipientElements.forEach(elem => {
                        elem.textContent = data.recipient_name;
                    });
                }
                
                // Обновляем название компании
                if (data.company_name) {
                    const companyNameElements = document.querySelectorAll('[data-field="company_name"], .company-name');
                    companyNameElements.forEach(elem => {
                        elem.textContent = data.company_name;
                    });
                }
                
                // Обновляем номинал
                if (data.amount) {
                    const amountElements = document.querySelectorAll('[data-field="amount"], .certificate-amount');
                    amountElements.forEach(elem => {
                        elem.textContent = data.amount;
                    });
                }
                
                // Обновляем сообщение
                if (data.message) {
                    const messageElements = document.querySelectorAll('[data-field="message"], .message-text');
                    messageElements.forEach(elem => {
                        elem.textContent = data.message;
                    });
                }
                
                // Обновляем даты действия
                if (data.valid_from) {
                    const validFromElements = document.querySelectorAll('[data-field="valid_from"], .validity-date.valid-from');
                    validFromElements.forEach(elem => {
                        elem.textContent = data.valid_from;
                    });
                }
                
                if (data.valid_until) {
                    const validUntilElements = document.querySelectorAll('[data-field="valid_until"], .validity-date.valid-until');
                    validUntilElements.forEach(elem => {
                        elem.textContent = data.valid_until;
                    });
                }
                
                // Отправляем подтверждение родительскому окну
                if (window.parent && window.parent.postMessage) {
                    window.parent.postMessage({
                        type: 'fields_updated',
                        success: true
                    }, '*');
                }
            }
            
            // Функция для деактивации редактирования всех полей
            function disableEditing() {
                const editableElements = document.querySelectorAll('[contenteditable="true"]');
                editableElements.forEach(elem => {
                    elem.setAttribute('contenteditable', 'false');
                    elem.classList.remove('editable-field');
                    // Удаляем все обработчики событий для редактирования
                    elem.removeEventListener('focus', null);
                    elem.removeEventListener('blur', null);
                    elem.removeEventListener('input', null);
                });
                
                // Отправляем подтверждение родительскому окну
                if (window.parent && window.parent.postMessage) {
                    window.parent.postMessage({
                        type: 'editing_disabled',
                        success: true
                    }, '*');
                }
            }
            
            // Инициализируем редактируемые поля, если включен режим редактирования
            initEditableFields();
            
            // Сообщаем родительскому окну, что iframe готов
            if (window.parent && window.parent.postMessage) {
                window.parent.postMessage({
                    type: 'iframe_ready',
                    message: 'Iframe полностью загружен и готов к работе'
                }, '*');
            }
        });
        
        // Функция для инициализации редактируемых полей
        function initEditableFields() {
            // Исправление: используем предварительно вычисленное значение
            const isEditable = {$isEditableValue};
            if (!isEditable) return;
            
            // Поле названия компании
            makeFieldEditable('company_name', '[data-field="company_name"]');
            
            // Поле получателя
            makeFieldEditable('recipient_name', '[data-field="recipient_name"]');
            
            // Поле номинала
            makeFieldEditable('amount', '[data-field="amount"]');
            
            // Поле сообщения
            makeFieldEditable('message', '[data-field="message"]');
            
            // Даты действия
            makeFieldEditable('valid_from', '[data-field="valid_from"]');
            makeFieldEditable('valid_until', '[data-field="valid_until"]');
        }
        
        // Функция для создания редактируемого поля
        function makeFieldEditable(fieldName, selector) {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                // Уже должен быть contenteditable из шаблона, но на всякий случай
                if (!element.hasAttribute('contenteditable')) {
                    element.setAttribute('contenteditable', 'true');
                }
                
                if (!element.hasAttribute('data-field')) {
                    element.setAttribute('data-field', fieldName);
                }
                
                // Эффекты при наведении и фокусе (стили уже добавлены в шаблон)
                
                // Обработчик изменения содержимого
                element.addEventListener('blur', function() {
                    // Отправляем данные об изменении в родительское окно
                    if (window.parent && window.parent.postMessage) {
                        const data = {};
                        data[fieldName] = this.textContent.trim();
                        
                        window.parent.postMessage({
                            type: 'field_update',
                            field: fieldName,
                            value: this.textContent.trim(),
                            data: data
                        }, '*');
                    }
                });
            });
        }
        </script>
        HTML;

        // Сохраняем существующий скрипт для обновления логотипов (если нужен)
        $html .= <<<HTML
        <script>
        // Функция для обновления всех возможных логотипов на странице
        function updateLogos(logoUrl) {
            console.log('Обновление логотипов на:', logoUrl);
            
            try {
                // Проверяем, если logoUrl равен 'none', скрываем все элементы логотипа
                if (logoUrl === 'none' || logoUrl === '') {
                    // Находим все возможные элементы, которые могут быть логотипом
                    const companyLogoElements = document.querySelectorAll('.company-logo');
                    const logoImages = document.querySelectorAll('img[src*="logo"], img[alt*="logo"], img[alt*="Логотип"]');
                    
                    let hiddenCount = 0;
                    
                    // Скрываем все найденные элементы с классом company-logo
                    if (companyLogoElements.length > 0) {
                        console.log('Найдено элементов с классом company-logo:', companyLogoElements.length);
                        Array.from(companyLogoElements).forEach(element => {
                            if (element.tagName === 'IMG') {
                                element.style.display = 'none';
                                hiddenCount++;
                                console.log('Скрыт элемент с классом company-logo');
                            }
                        });
                    }
                    
                    // Скрываем все изображения с "logo" в src, которые не были скрыты ранее
                    if (logoImages.length > 0) {
                        console.log('Найдено изображений с logo в атрибутах:', logoImages.length);
                        const logoImagesArray = Array.from(logoImages);
                        logoImagesArray.forEach(img => {
                            if (!Array.from(companyLogoElements).includes(img)) {
                                img.style.display = 'none';
                                hiddenCount++;
                                console.log('Скрыто изображение логотипа');
                            }
                        });
                    }
                    
                    // Отправляем ответ родителю
                    if (window.parent && window.parent.postMessage) {
                        window.parent.postMessage({
                            type: 'logo_updated',
                            success: true,
                            count: hiddenCount,
                            mode: 'hidden'
                        }, '*');
                    }
                    
                    return hiddenCount;
                }
                
                // Для случая с обычным логотипом - оставляем существующий код
                const img = new Image();
                img.crossOrigin = "anonymous"; // Для избежания CORS-проблем
                
                img.onload = function() {
                    console.log('Изображение логотипа успешно загружено:', logoUrl);
                    
                    // Находим все возможные элементы, которые могут быть логотипом
                    const companyLogoElements = document.querySelectorAll('.company-logo');
                    const logoImages = document.querySelectorAll('img[src*="logo"], img[alt*="logo"], img[alt*="Логотип"]');
                    const allImages = document.querySelectorAll('img'); // Как запасной вариант
                    
                    let updatedCount = 0;
                    
                    // Обновляем все найденные элементы с классом company-logo
                    if (companyLogoElements.length > 0) {
                        console.log('Найдено элементов с классом company-logo:', companyLogoElements.length);
                        Array.from(companyLogoElements).forEach(element => {
                            if (element.tagName === 'IMG') {
                                element.src = logoUrl;
                                updatedCount++;
                                console.log('Обновлен элемент с классом company-logo');
                            }
                        });
                    } else {
                        console.log('Элементы с классом company-logo не найдены');
                    }
                    
                    // Обновляем все изображения с "logo" в src или alt
                    if (logoImages.length > 0) {
                        console.log('Найдено изображений с logo в атрибутах:', logoImages.length);
                        const logoImagesArray = Array.from(logoImages);
                        logoImagesArray.forEach(img => {
                            if (!Array.from(companyLogoElements).includes(img)) {
                                img.src = logoUrl;
                                updatedCount++;
                                console.log('Обновлено изображение логотипа');
                            }
                        });
                    } else {
                        console.log('Изображения с logo в атрибутах не найдены, ищем любые изображения...');
                        
                        // Если не нашли элементов по стандартным критериям, обновляем первое найденное изображение
                        if (updatedCount === 0 && allImages.length > 0) {
                            allImages[0].src = logoUrl;
                            updatedCount++;
                            console.log('Обновлено первое найденное изображение как логотип');
                        }
                    }
                    
                    // Отправляем ответ родителю
                    if (window.parent && window.parent.postMessage) {
                        window.parent.postMessage({
                            type: 'logo_updated',
                            success: true,
                            count: updatedCount
                        }, '*');
                    }
                    
                    return updatedCount;
                };
                
                img.onerror = function() {
                    console.error('Не удалось загрузить изображение логотипа:', logoUrl);
                    
                    if (window.parent && window.parent.postMessage) {
                        window.parent.postMessage({
                            type: 'logo_updated',
                            success: false,
                            error: 'Не удалось загрузить изображение логотипа'
                        }, '*');
                    }
                };
                
                // Запускаем загрузку изображения
                img.src = logoUrl;
            } catch (error) {
                console.error('Ошибка при обработке логотипа:', error);
                return 0;
            }
        }
        </script>
        HTML;

        return response($html)->header('Content-Type', 'text/html');
    }
}
