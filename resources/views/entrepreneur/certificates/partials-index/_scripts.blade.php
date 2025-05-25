<script>
    // Функция для копирования публичной ссылки документа
    function copyPublicUrl(url, certNumber) {
        // Останавливаем всплытие события, чтобы не срабатывала ссылка-родитель
        event.preventDefault();
        event.stopPropagation();

        // Проверяем доступность Clipboard API перед его использованием
        if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
            // Копируем текст в буфер обмена с помощью современного API
            navigator.clipboard.writeText(url).then(() => {
                // Показываем toast-уведомление с успешным копированием
                const toastEl = document.getElementById('copyToast');
                document.getElementById('toastMessage').textContent =
                    `Ссылка на документ ${certNumber} скопирована`;
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 3000
                });
                toast.show();
            }).catch(err => {
                console.error('Ошибка при копировании: ', err);
                // Запасной вариант при возникновении ошибок доступа
                fallbackCopyTextToClipboard(url, certNumber);
            });
        } else {
            // Если Clipboard API недоступен, используем запасной метод
            fallbackCopyTextToClipboard(url, certNumber);
        }
    }

    // Запасной метод копирования для браузеров без поддержки Clipboard API
    function fallbackCopyTextToClipboard(text, certNumber) {
        const textArea = document.createElement("textarea");
        textArea.value = text;

        // Делаем элемент невидимым
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                const toastEl = document.getElementById('copyToast');
                document.getElementById('toastMessage').textContent = `Ссылка на документ ${certNumber} скопирована`;
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 3000
                });
                toast.show();
            } else {
                alert('Не удалось скопировать ссылку. Пожалуйста, скопируйте её вручную: ' + text);
            }
        } catch (err) {
            console.error('Ошибка при копировании: ', err);
            alert('Не удалось скопировать ссылку. Пожалуйста, скопируйте её вручную: ' + text);
        }

        document.body.removeChild(textArea);
    }

    // Добавляем код для умной поисковой строки и ленивой загрузки
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация номеров ячеек
        const gridCells = document.querySelectorAll('.grid-cell');
        gridCells.forEach((cell, index) => {
            cell.setAttribute('data-cell-number', (index + 1));
        });
        
        const searchInput = document.getElementById('search-certificate');
        const clearButton = document.getElementById('clear-search');
        const searchTypeIndicator = document.getElementById('search-type-indicator');
        const searchResults = document.getElementById('search-results');
        const searchResultsContainer = document.getElementById('search-results-container');
        const noResultsMessage = document.getElementById('no-results-message');
        const mainContainer = document.getElementById('certificates-container');
        const loadingIndicator = document.getElementById('loading-indicator');
        const paginationContainer = document.getElementById('pagination-container');

        let currentPage = 1;
        let isLoading = false;
        let hasMorePages = {{ $certificates->hasMorePages() ? 'true' : 'false' }};
        let searchMode = false;
        let searchTimer = null;
        let lastSearchQuery = '';
        let isPhoneMode = false;
        let phoneRawValue = '';

        // Функция для форматирования телефонного номера в формат +7 (XXX) XXX-XX-XX
        function formatPhoneNumber(value) {
            // ... существующий код ...
        }

        // Функция для получения чистого номера без форматирования
        function getDigitsOnly(value) {
            return value.replace(/\D/g, '');
        }

        // Определение типа ввода (телефон или имя) и применение маски
        searchInput.addEventListener('input', function(e) {
            // ... существующий код ...
        });

        // Специальная обработка при нажатии клавиш в поле телефона
        searchInput.addEventListener('keydown', function(e) {
            // ... существующий код ...
        });

        // Очистка поля поиска
        clearButton.addEventListener('click', function() {
            // ... существующий код ...
        });

        // Функция сброса поиска
        function resetSearch() {
            searchMode = false;
            searchResults.classList.add('d-none');
            mainContainer.classList.remove('d-none');
            if (paginationContainer) paginationContainer.classList.remove('d-none');
        }

        // Функция для выполнения поиска
        function performSearch(query) {
            // ... существующий код ...
        }

        // Функция для создания элемента документа
        function createCertificateElement(cert) {
            // ... существующий код ...
        }

        // Вспомогательная функция для форматирования статуса
        function getStatusBadge(status) {
            // ... существующий код ...
        }

        // Обработка события прокрутки для ленивой загрузки
        const scrollLoading = document.getElementById('scroll-loading');
        const certificatesContainer = document.getElementById('certificates-container').querySelector('.row');
        
        // Функция для проверки, достиг ли пользователь конца страницы
        function checkScroll() {
            // ... существующий код ...
        }
        
        // Функция для загрузки дополнительных сертификатов
        function loadMoreCertificates() {
            // ... существующий код ...
        }
        
        // Обработчик события прокрутки
        window.addEventListener('scroll', checkScroll);
        
        // Проверяем сразу после загрузки страницы
        setTimeout(checkScroll, 500);
    });
</script>
