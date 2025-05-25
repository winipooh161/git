document.addEventListener('DOMContentLoaded', function() {
    // Проверяем, загружены ли jQuery и Select2
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        // Инициализация всех селектов с классом select2-template-picker
        $('.select2-template-picker').select2({
            theme: 'bootstrap-5',
            language: 'ru',
            width: '100%',
            placeholder: 'Поиск шаблона...',
            allowClear: true
        });
        
        console.log('Select2 успешно инициализирован');
    } else {
        console.error('jQuery или Select2 не загружены! Загрузка библиотек...');
        
        // Динамическая загрузка jQuery, если его нет
        if (typeof jQuery === 'undefined') {
            var jqScript = document.createElement('script');
            jqScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
            document.head.appendChild(jqScript);
            
            jqScript.onload = function() {
                // Загружаем Select2 после загрузки jQuery
                var s2Script = document.createElement('script');
                s2Script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
                document.head.appendChild(s2Script);
                
                s2Script.onload = function() {
                    // Загружаем языковой файл
                    var s2LangScript = document.createElement('script');
                    s2LangScript.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ru.js';
                    document.head.appendChild(s2LangScript);
                    
                    // Инициализируем после загрузки всех скриптов
                    s2LangScript.onload = function() {
                        $('.select2-template-picker').select2({
                            theme: 'bootstrap-5',
                            language: 'ru',
                            width: '100%',
                            placeholder: 'Поиск шаблона...',
                            allowClear: true
                        });
                        console.log('Select2 успешно инициализирован после динамической загрузки');
                    };
                };
            };
        }
        // Если jQuery есть, но нет Select2
        else if (typeof jQuery.fn.select2 === 'undefined') {
            var s2Script = document.createElement('script');
            s2Script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            document.head.appendChild(s2Script);
            
            s2Script.onload = function() {
                // Загружаем языковой файл
                var s2LangScript = document.createElement('script');
                s2LangScript.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ru.js';
                document.head.appendChild(s2LangScript);
                
                s2LangScript.onload = function() {
                    $('.select2-template-picker').select2({
                        theme: 'bootstrap-5',
                        language: 'ru',
                        width: '100%',
                        placeholder: 'Поиск шаблона...',
                        allowClear: true
                    });
                    console.log('Select2 успешно инициализирован после динамической загрузки');
                };
            };
        }
    }
});
