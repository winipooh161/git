<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Классический сертификат</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2b579a;
            --primary-dark: #1e3e6d;
            --primary-light: #537dc2;
            --secondary: #ce4a1f;
            --dark: #333333;
            --light: #ffffff;
            --gold:rgb(0, 0, 0);
            --border-color: #e6e6e6;
            --background-color: #f9f9f9;
            
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.1);
            
            --border-radius: 0.5rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background: var(--background-color);
            color: var(--dark);
            min-height: 100vh;
            line-height: 1.5;
            font-size: 16px; /* Базовый размер шрифта */
        }
        
        .certificate-container {
            max-width: 100%; /* Изменено для мобильных устройств */
            margin: 0 auto;
            padding: 0;
        }
        
        .certificate-card {
            background: var(--light);
            border: 2px solid var(--gold);
            border-radius: var(--border-radius);
            padding: 5vw; /* Относительный отступ */
            position: relative;
            box-shadow: var(--shadow-lg);
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ3aGl0ZSIvPjxwYXRoIGQ9Ik0wIDEwTDEwIDAgMjAgMTBsMTAtMTAgMTAgMTAgMTAtMTAgMTAgMTAgMTAtMTAgMTAgMTAgMTAtMTBWMGgtMTAwdjEweiIgZmlsbD0icmdiYSgyMTIsMTc1LDU1LDAuMSkiLz48cGF0aCBkPSJNMCA0MEwxMCAzMCAyMCA0MCAzMCAzMCA0MCA0MCA1MCAzMCA2MCA0MCA3MCAzMCA4MCA0MCA5MCAzMCAxMDAgNDB2LTIwaC0xMDB2MjB6IiBmaWxsPSJyZ2JhKDIxMiwxNzUsNTUsMC4xKSIvPjxwYXRoIGQ9Ik0wIDcwTDEwIDYwIDIwIDcwIDMwIDYwIDQwIDcwIDUwIDYwIDYwIDcwIDcwIDYwIDgwIDcwIDkwIDYwIDEwMCA3MHYtMjBoLTEwMHYyMHoiIGZpbGw9InJnYmEoMjEyLDE3NSw1NSwwLjEpIi8+PHBhdGggZD0iTTAgMTAwTDEwIDkwIDIwIDEwMCAzMCA5MCA0MCAxMDAgNTAgOTAgNjAgMTAwIDcwIDkwIDgwIDEwMCA5MCA5MCAxMDAgMTAwdi0yMGgtMTAwdjIweiIgZmlsbD0icmdiYSgyMTIsMTc1LDU1LDAuMSkiLz48L3N2Zz4=');
            background-repeat: repeat;
            overflow: hidden; /* Для борьбы с переполнением на маленьких экранах */
        }
        
        .certificate-border {
            position: absolute;
            top: 10px;
            right: 10px;
            bottom: 10px;
            left: 10px;
            border: 1px solid var(--gold);
            pointer-events: none;
        }
        
        .certificate-inner {
            position: relative;
            z-index: 1;
        }
        
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4vw;
            flex-wrap: wrap; /* Добавлено для мобильных */
        }
        
        .company-info {
            flex: 1;
            min-width: 60%; /* Гарантируем минимальную ширину на мобильных */
        }
        
        .company-name {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
            min-height: 1.5em;
            word-wrap: break-word; /* Разрешаем перенос слов */
        }
        
        .company-logo {
            max-width: 100%; /* Полная ширина на маленьких экранах */
            max-height: 80px;
            object-fit: contain;
        }
        
        .certificate-type {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--secondary);
        }
        
        .certificate-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 2rem; /* Уменьшено для мобильных */
            color: var(--primary-dark);
            text-align: center;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            word-wrap: break-word;
        }
        
        .certificate-subtitle {
            text-align: center;
            font-size: 1rem; /* Уменьшено для мобильных */
            margin-bottom: 1.5rem;
            color: var(--secondary);
        }
        
        .recipient-section {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .recipient-label {
            font-size: 0.85rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .recipient-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.7rem; /* Уменьшено для мобильных */
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
            min-height: 1.7rem;
            word-wrap: break-word;
        }
        
        .certificate-amount {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem; /* Уменьшено для мобильных */
            font-weight: 800;
            color: var(--primary);
            text-align: center;
            margin-bottom: 1.5rem;
            min-height: 2rem;
            word-wrap: break-word;
        }
        
        .message-container {
            text-align: center;
            margin: 1.5rem 0;
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }
        
        .message-text {
            font-style: italic;
            color: var(--dark);
            min-height: 1.5em;
            word-wrap: break-word;
            font-size: 0.95rem; /* Уменьшено для мобильных */
        }
        
        .footer-section {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            flex-wrap: wrap; /* Для мобильных */
        }
        
        .cert-info {
            flex: 1;
            margin-bottom: 1rem;
        }
        
        .certificate-number {
            font-size: 0.85rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .validity-section {
            flex: 1;
            text-align: right;
        }
        
        .validity-label {
            font-size: 0.75rem;
            color: var(--dark);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }
        
        .validity-date {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--dark);
            min-height: 1.5em;
        }
        
        .qr-section {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .qr-code {
            max-width: 100px;
            margin: 0 auto;
        }
        
        .qr-caption {
            font-size: 0.75rem;
            color: var(--dark);
            margin-top: 0.5rem;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 8rem; /* Уменьшено для мобильных */
            opacity: 0.05;
            font-weight: bold;
            color: var(--primary);
            white-space: nowrap;
            pointer-events: none;
        }
        
        @media print {
            body {
                background: none;
            }
            
            .certificate-container {
                padding: 0;
            }
            
            .certificate-card {
                box-shadow: none;
                border: none;
            }
        }
        
        /* Стили для редактируемых полей */
        [contenteditable=true] {
            transition: background-color 0.2s;
            border-radius: 3px;
            padding: 2px;
            min-height: 1em;
        }
        
        [contenteditable=true]:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }
        
        [contenteditable=true]:focus {
            background-color: rgba(59, 130, 246, 0.2);
            outline: 2px solid rgba(59, 130, 246, 0.5);
        }
        
        /* Медиа-запросы для мобильных устройств */
        @media screen and (max-width: 480px) {
            body {
                font-size: 14px; /* Меньший базовый размер шрифта для мобильных */
            }
            
            .certificate-card {
                padding: 15px;
                height: 100vh;
                border-radius: 8px;
                margin: 0; /* Убираем внешние отступы */
            }
            
            .certificate-border {
                top: 8px;
                right: 8px;
                bottom: 8px;
                left: 8px;
            }
            
            .certificate-title {
                font-size: 1.5rem; /* Еще меньше на самых маленьких экранах */
                margin-bottom: 0.75rem;
                letter-spacing: 1px;
            }
            
            .certificate-subtitle {
                font-size: 0.85rem;
                margin-bottom: 1rem;
            }
            
            .header-section {
                flex-direction: column; /* Ставим элементы вертикально */
                align-items: center;
                text-align: center;
                margin-bottom: 1rem;
            }
            
            .company-info {
                margin-bottom: 0.5rem;
                width: 100%;
                text-align: center;
            }
            
            .company-name {
                font-size: 1.25rem;
            }
            
            .certificate-type {
                font-size: 0.7rem;
                margin-bottom: 0.5rem;
            }
            
            .recipient-name {
                font-size: 1.4rem;
                margin-bottom: 0.75rem;
            }
            
            .certificate-amount {
                font-size: 1.6rem;
                margin-bottom: 1rem;
            }
            
            .message-container {
                margin: 1rem 0;
                padding: 0.75rem 0;
            }
            
            .message-text {
                font-size: 0.85rem;
            }
            
            .footer-section {
                flex-direction: column; /* Ставим в столбик */
                margin-top: 1.5rem;
            }
            
            .validity-section {
                text-align: left; /* Выравниваем по левому краю на мобильных */
                margin-top: 0.5rem;
            }
            
            .watermark {
                font-size: 5rem; /* Еще меньше для мобильных */
            }
        }
        
        /* Средние устройства (планшеты) */
        @media screen and (min-width: 481px) and (max-width: 767px) {
            .certificate-card {
                padding: 25px;
            }
            
            .certificate-title {
                font-size: 1.8rem;
            }
            
            .certificate-amount {
                font-size: 1.8rem;
            }
            
            .watermark {
                font-size: 6rem;
            }
        }
        
        /* Для быстрой загрузки шрифтов и предотвращения мерцания при загрузке */
        .wf-loading {
            visibility: hidden;
        }
        
        .wf-active, .wf-inactive {
            visibility: visible;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="certificate-card">
            <div class="certificate-border"></div>
            <div class="watermark">СЕРТИФИКАТ</div>
            
            <div class="certificate-inner">
                <div class="header-section">
                    <div class="company-info">
                        <div class="company-name" data-field="company_name" contenteditable="true">Название компании</div>
                        <div class="certificate-type">Подарочный сертификат</div>
                    </div>
                </div>
                
                <div class="certificate-title">СЕРТИФИКАТ</div>
                <div class="certificate-subtitle">Подтверждает право на получение услуг или товаров</div>
                
                <div class="recipient-section">
                    <div class="recipient-label">Выдан получателю:</div>
                    <div class="recipient-name" data-field="recipient_name" contenteditable="true">Иванов Иван</div>
                </div>
                
                <div class="certificate-amount" data-field="amount" contenteditable="true">10 000 ₽</div>
                
                <div class="message-container">
                    <div class="message-text" data-field="message" contenteditable="true">Этот сертификат дарит вам возможность воспользоваться нашими услугами. Будем рады видеть вас!</div>
                </div>
                
                <div class="footer-section">
                    <div class="cert-info">
                        <!-- Номер сертификата больше не показывается -->
                    </div>
                    
                    <div class="validity-section">
                        <div class="validity-label">Действителен с:</div>
                        <div class="validity-date valid-from" data-field="valid_from" contenteditable="true">01.01.2025</div>
                        <div class="validity-label">Действителен до:</div>
                        <div class="validity-date valid-until" data-field="valid_until" contenteditable="true">01.04.2025</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Объявляем объект для хранения ссылок на все редактируемые поля
        const editableFields = {};
        
        // Функция инициализации
        function initTemplate() {
            console.log('Инициализация шаблона сертификата');
            
            // Находим все редактируемые поля и сохраняем ссылки на них
            document.querySelectorAll('[data-field]').forEach(element => {
                const fieldName = element.getAttribute('data-field');
                editableFields[fieldName] = element;
                console.log('Найдено поле:', fieldName);
                
                // Добавляем обработчик события input для отправки обновлений в родительское окно
                element.addEventListener('input', function() {
                    sendFieldUpdate(fieldName, element.textContent);
                });
            });
            
            // Отправляем сообщение родительскому окну о готовности шаблона
            sendMessageToParent({
                type: 'template_ready',
                message: 'Шаблон сертификата готов к получению данных'
            });
            
            // Запрашиваем начальные данные у родительского окна
            sendMessageToParent({
                type: 'request_initial_data',
                message: 'Запрос начальных данных для заполнения сертификата'
            });
        }
        
        // Функция для отправки обновления поля в родительское окно
        function sendFieldUpdate(fieldName, value) {
            console.log(`Отправка обновления поля ${fieldName}: ${value}`);
            sendMessageToParent({
                type: 'field_update',
                field: fieldName,
                value: value
            });
        }
        
        // Функция для безопасной отправки сообщений в родительское окно
        function sendMessageToParent(data) {
            if (window.parent && window.parent !== window) {
                window.parent.postMessage(data, '*');
            }
        }
        
        // Функция для обновления полей сертификата
        function updateTemplateFields(data) {
            console.log('Обновление полей шаблона:', data);
            
            // Обновляем каждое поле, если для него есть данные
            Object.keys(data).forEach(fieldName => {
                const element = editableFields[fieldName];
                if (element && data[fieldName]) {
                    // Обновляем текстовое содержимое элемента
                    element.textContent = data[fieldName];
                    console.log(`Поле ${fieldName} обновлено: ${data[fieldName]}`);
                }
            });
            
            // Подтверждаем обновление
            sendMessageToParent({
                type: 'fields_updated',
                success: true
            });
        }
        
        // Обработчик сообщений от родительского окна
        window.addEventListener('message', function(event) {
            // Проверяем, что сообщение пришло от родительского окна
            if (event.source !== window.parent) return;
            
            console.log('Получено сообщение:', event.data);
            
            // Обработка разных типов сообщений
            if (event.data && event.data.type) {
                switch (event.data.type) {
                    case 'update_fields':
                        // Обновляем поля сертификата
                        if (event.data.data) {
                            updateTemplateFields(event.data.data);
                        }
                        break;
                        
                    case 'request_current_values':
                        // Отправляем текущие значения всех полей
                        const currentValues = {};
                        Object.keys(editableFields).forEach(fieldName => {
                            currentValues[fieldName] = editableFields[fieldName].textContent;
                        });
                        
                        sendMessageToParent({
                            type: 'current_values',
                            values: currentValues
                        });
                        break;
                }
            }
        });
        
        // Запускаем инициализацию при загрузке документа
        document.addEventListener('DOMContentLoaded', initTemplate);
        
        // Запускаем также инициализацию сразу, если документ уже загружен
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            initTemplate();
        }
    </script>
</body>
</html>
