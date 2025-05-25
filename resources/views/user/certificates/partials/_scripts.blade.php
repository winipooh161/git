<script>
// Временное добавление функции fetchWithErrorHandling, если она недоступна через app.js
if (!window.fetchWithErrorHandling) {
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
}

// Временное добавление функции safeVibrate, если она недоступна через app.js
if (!window.safeVibrate) {
    window.safeVibrate = function(pattern) {
        try {
            if (navigator.vibrate && typeof navigator.vibrate === 'function' && document.hasFocus()) {
                navigator.vibrate(pattern);
            }
        } catch (e) {
            console.warn('Vibration API не поддерживается или недоступна', e);
        }
    };
}

// Функция для копирования публичной ссылки документа
function copyPublicUrl(url, certNumber) {
    if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
        navigator.clipboard.writeText(url).then(() => {
            // Показываем toast-уведомление с успешным копированием
            const toastEl = document.getElementById('copyToast');
            document.getElementById('toastMessage').textContent = `Ссылка на документ ${certNumber} скопирована`;
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();
        }).catch(err => {
            console.error('Ошибка при копировании: ', err);
            fallbackCopyTextToClipboard(url, certNumber);
        });
    } else {
        fallbackCopyTextToClipboard(url, certNumber);
    }
}

// Запасной метод копирования для браузеров без поддержки Clipboard API
function fallbackCopyTextToClipboard(text, certNumber) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
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

// Функция для скачивания QR-кода
function downloadQRCode(certNumber, buttonElement) {
    // Находим изображение QR-кода в модальном окне
    const modal = buttonElement.closest('.modal');
    const qrImage = modal.querySelector('img');
    
    if (!qrImage) return;
    
    // Создаем элемент canvas для преобразования изображения
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    // Загружаем изображение в canvas
    const image = new Image();
    image.crossOrigin = "Anonymous";
    image.onload = function() {
        canvas.width = image.width;
        canvas.height = image.height;
        
        // Рисуем белый фон и изображение
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(image, 0, 0);
        
        // Конвертируем canvas в URL данных
        const dataURL = canvas.toDataURL('image/png');
        
        // Создаем временную ссылку для скачивания
        const downloadLink = document.createElement('a');
        downloadLink.href = dataURL;
        downloadLink.download = `qr-code-certificate-${certNumber}.png`;
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    };
    
    image.src = qrImage.src;
}

// Функция для добавления документа в папку с улучшенной обработкой ошибок
function addToFolder(certificateId, folderId) {
    // Показываем индикатор загрузки
    const toastEl = document.getElementById('copyToast');
    document.getElementById('toastMessage').textContent = 'Добавление в папку...';
    const toast = new bootstrap.Toast(toastEl, { delay: 1000 });
    toast.show();
    
    // Формируем URL для запроса
    const url = `/user/certificates/${certificateId}/add-to-folder/${folderId}`;
    console.log('Отправляем запрос на:', url);
    
    // Получаем CSRF токен
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Проверяем наличие CSRF-токена
    if (!csrfToken) {
        console.error('CSRF токен отсутствует. Проверьте наличие meta тега с name="csrf-token"');
        document.getElementById('toastMessage').textContent = 'Ошибка безопасности: CSRF токен отсутствует';
        document.getElementById('copyToast').classList.add('bg-danger');
        document.getElementById('copyToast').classList.remove('bg-success');
        const errorToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        errorToast.show();
        return;
    }
    
    // Используем fetch API напрямую для большего контроля
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({_token: csrfToken}) // Явно добавляем токен в тело запроса
    })
    .then(response => {
        console.log('Получен ответ, статус:', response.status);
        if (!response.ok) {
            if (response.status === 419) {
                throw new Error('Ошибка CSRF токена. Попробуйте обновить страницу.');
            }
            return response.json().then(err => {
                throw new Error(err.message || `Ошибка HTTP: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Данные ответа:', data);
        
        if (data.success) {
            document.getElementById('toastMessage').textContent = data.message || 'документ добавлен в папку';
            document.getElementById('copyToast').classList.add('bg-success');
            document.getElementById('copyToast').classList.remove('bg-danger');
            const newToast = new bootstrap.Toast(toastEl, { delay: 3000 });
            newToast.show();
            
            // Обновляем список текущих папок документа, если модальное окно открыто
            if (document.getElementById('folderManageModal').classList.contains('show')) {
                getCertificateFolders(certificateId);
            } else {
                // Если мы находимся в режиме просмотра папки, обновим страницу для отображения изменений
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        } else {
            document.getElementById('toastMessage').textContent = data.message || 'Произошла ошибка';
            document.getElementById('copyToast').classList.add('bg-danger');
            document.getElementById('copyToast').classList.remove('bg-success');
            const errorToast = new bootstrap.Toast(toastEl, { delay: 3000 });
            errorToast.show();
        }
    })
    .catch(error => {
        console.error('Ошибка при добавлении в папку:', error);
        
        document.getElementById('toastMessage').textContent = 'Ошибка при добавлении в папку: ' + error.message;
        document.getElementById('copyToast').classList.add('bg-danger');
        document.getElementById('copyToast').classList.remove('bg-success');
        const errorToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        errorToast.show();
    });
}

// Функция для удаления документа из папки
function removeFromFolder(certificateId, folderId) {
    // Показываем индикатор загрузки
    const toastEl = document.getElementById('copyToast');
    document.getElementById('toastMessage').textContent = 'Удаление из папки...';
    const toast = new bootstrap.Toast(toastEl, { delay: 1000 });
    toast.show();
    
    // Формируем URL для запроса
    const url = `/user/certificates/${certificateId}/remove-from-folder/${folderId}`;
    console.log('Отправляем запрос на:', url);
    
    // Получаем CSRF токен
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Проверяем наличие CSRF-токена
    if (!csrfToken) {
        console.error('CSRF токен отсутствует. Проверьте наличие meta тега с name="csrf-token"');
        document.getElementById('toastMessage').textContent = 'Ошибка безопасности: CSRF токен отсутствует';
        document.getElementById('copyToast').classList.add('bg-danger');
        document.getElementById('copyToast').classList.remove('bg-success');
        const errorToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        errorToast.show();
        return;
    }
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: JSON.stringify({_token: csrfToken}) // Явно добавляем токен в тело запроса
    })
    .then(response => {
        console.log('Получен ответ, статус:', response.status);
        if (!response.ok) {
            if (response.status === 419) {
                throw new Error('Ошибка CSRF токена. Попробуйте обновить страницу.');
            }
            return response.json().then(err => {
                throw new Error(err.message || `Ошибка HTTP: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Данные ответа:', data);
        
        if (data.success) {
            document.getElementById('toastMessage').textContent = data.message || 'документ удален из папки';
            document.getElementById('copyToast').classList.add('bg-success');
            document.getElementById('copyToast').classList.remove('bg-danger');
            const newToast = new bootstrap.Toast(toastEl, { delay: 3000 });
            newToast.show();
            
            // Обновляем список текущих папок документа
            if (document.getElementById('folderManageModal').classList.contains('show')) {
                getCertificateFolders(certificateId);
            }
            
            // Если мы находимся в конкретной папке и удалили документ из неё, обновляем страницу
            const urlParams = new URLSearchParams(window.location.search);
            const currentFolder = urlParams.get('folder');
            if (currentFolder && currentFolder == folderId) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            document.getElementById('toastMessage').textContent = data.message || 'Произошла ошибка';
            document.getElementById('copyToast').classList.add('bg-danger');
            document.getElementById('copyToast').classList.remove('bg-success');
            const errorToast = new bootstrap.Toast(toastEl, { delay: 3000 });
            errorToast.show();
        }
    })
    .catch(error => {
        console.error('Ошибка при удалении из папки:', error);
        
        document.getElementById('toastMessage').textContent = 'Ошибка при удалении из папки: ' + error.message;
        document.getElementById('copyToast').classList.add('bg-danger');
        document.getElementById('copyToast').classList.remove('bg-success');
        const errorToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        errorToast.show();
    });
}

// Функция для получения папок, в которых находится документ
function getCertificateFolders(certificateId) {
    // Показываем состояние загрузки
    const foldersList = document.getElementById('foldersList');
    if (foldersList) {
        foldersList.innerHTML = `
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <p class="mb-0 small">Загрузка списка папок...</p>
            </div>
        `;
    }
    
    // Формируем URL для запроса
    const url = `/user/certificates/${certificateId}/folders`;
    console.log('Запрашиваем папки по URL:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Получен ответ, статус:', response.status);
        if (!response.ok) {
            throw new Error(`Ошибка HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Данные папок:', data);
        
        if (data.success) {
            if (foldersList) {
                renderFoldersList(foldersList, data.folders, certificateId);
            }
        } else {
            if (foldersList) {
                foldersList.innerHTML = `
                    <div class="alert alert-danger py-2">
                        Ошибка: ${data.message || 'Не удалось загрузить папки'}
                    </div>
                `;
            }
            console.error('Ошибка при загрузке папок:', data.message);
        }
    })
    .catch(error => {
        console.error('Ошибка при получении папок документа:', error);
        
        if (foldersList) {
            foldersList.innerHTML = `
                <div class="alert alert-danger py-2">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Ошибка загрузки: ${error.message}
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-sm btn-outline-secondary" onclick="getCertificateFolders(${certificateId})">
                        <i class="fas fa-sync me-1"></i>Повторить попытку
                    </button>
                </div>
            `;
        }
    });
}

// Функция для отрисовки списка папок
function renderFoldersList(foldersList, folders, certificateId) {
    foldersList.innerHTML = ''; // Очищаем список
    
    if (!folders || folders.length === 0) {
        foldersList.innerHTML = `
            <div class="text-center p-3">
                <i class="fas fa-folder-open text-muted fa-2x mb-2"></i>
                <p class="mb-0 text-muted">У вас пока нет папок</p>
                <p class="small text-muted mb-0">Создайте новую папку ниже</p>
            </div>
        `;
        return;
    }
    
    folders.forEach(folder => {
        const isInFolder = folder.has_certificate;
        const folderColor = folder.color || 'primary';
        
        const folderItem = document.createElement('div');
        folderItem.className = 'list-group-item d-flex justify-content-between align-items-center';
        folderItem.innerHTML = `
            <div>
                <i class="fas fa-folder me-2" style="color: var(--bs-${folderColor})"></i>
                ${folder.name}
            </div>
            <div class="btn-group btn-group-sm" role="group">
                ${isInFolder ? 
                    `<button type="button" 
                      class="btn btn-outline-danger remove-from-folder-btn" 
                      data-folder-id="${folder.id}" 
                      data-certificate-id="${certificateId}">
                        <i class="fas fa-minus"></i>
                    </button>` : 
                    `<button type="button" 
                      class="btn btn-outline-success add-to-folder-btn" 
                      data-folder-id="${folder.id}" 
                      data-certificate-id="${certificateId}">
                        <i class="fas fa-plus"></i>
                    </button>`
                }
            </div>
        `;
        
        foldersList.appendChild(folderItem);
    });
    
    // Добавляем обработчики событий на кнопки
    document.querySelectorAll('.add-to-folder-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const folderId = this.getAttribute('data-folder-id');
            const certId = this.getAttribute('data-certificate-id');
            addToFolder(certId, folderId);
        });
    });
    
    document.querySelectorAll('.remove-from-folder-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const folderId = this.getAttribute('data-folder-id');
            const certId = this.getAttribute('data-certificate-id');
            removeFromFolder(certId, folderId);
        });
    });
}

// Обработчик создания новой папки в модальном окне
document.addEventListener('DOMContentLoaded', function() {
    const createFolderBtn = document.getElementById('createFolderBtn');
    const newFolderName = document.getElementById('newFolderName');
    const folderActionStatus = document.getElementById('folderActionStatus');
    
    if (createFolderBtn && newFolderName) {
        createFolderBtn.addEventListener('click', function() {
            const folderName = newFolderName.value.trim();
            if (!folderName) {
                alert('Введите название папки');
                return;
            }
            
            createFolderBtn.disabled = true;
            createFolderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Создание...';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch('/user/folders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    name: folderName
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Ошибка создания папки');
                return response.json();
            })
            .then(data => {
                newFolderName.value = '';
                createFolderBtn.disabled = false;
                createFolderBtn.innerHTML = 'Создать';
                
                // Показываем сообщение об успехе
                if (folderActionStatus) {
                    folderActionStatus.querySelector('span').textContent = 'Папка успешно создана';
                    folderActionStatus.style.display = 'block';
                    folderActionStatus.classList.remove('text-danger');
                    folderActionStatus.classList.add('text-success');
                }
                
                // Обновляем список папок, если открыто модальное окно
                const currentCertificateId = document.getElementById('currentCertificateId');
                if (currentCertificateId && currentCertificateId.value) {
                    getCertificateFolders(currentCertificateId.value);
                } else {
                    // Обновляем страницу, если модальное окно не открыто
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            })
            .catch(error => {
                createFolderBtn.disabled = false;
                createFolderBtn.innerHTML = 'Создать';
                console.error('Ошибка при создании папки:', error);
                
                // Показываем сообщение об ошибке
                if (folderActionStatus) {
                    folderActionStatus.querySelector('span').textContent = 'Ошибка: не удалось создать папку';
                    folderActionStatus.style.display = 'block';
                    folderActionStatus.classList.remove('text-success');
                    folderActionStatus.classList.add('text-danger');
                }
            });
        });
        
        // Добавляем обработчик Enter для поля ввода
        newFolderName.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                createFolderBtn.click();
            }
        });
    }
});

// Инициализация обработки двойного нажатия на карточки документов
document.addEventListener('DOMContentLoaded', function() {
    const certificateCards = document.querySelectorAll('.certificate-card');
    
    certificateCards.forEach(card => {
        // Сохраняем данные карточки
        const certificateId = card.dataset.certificateId;
        const publicUrl = card.dataset.publicUrl;
        const certificateNumber = card.dataset.certificateNumber;
        let clickTimer = null;
        let clickCount = 0;
        
        // Обработчик клика с задержкой для распознавания двойного клика
        card.addEventListener('click', function(e) {
            // Если клик был на кнопке или в выпадающем меню, не обрабатываем
            if (e.target.closest('.btn') || e.target.closest('.dropdown-menu')) {
                return;
            }
            
            e.preventDefault(); // Предотвращаем любые стандартные действия
            
            clickCount++;
            
            if (clickCount === 1) {
                clickTimer = setTimeout(function() {
                    clickCount = 0;
                    // Если это одиночный клик - открываем публичную страницу документа
                    window.open(publicUrl, '_blank');
                }, 300);
            } else if (clickCount === 2) {
                clearTimeout(clickTimer);
                clickCount = 0;
                // Если это двойной клик - показываем модальное окно управления папками
                handleCertificateAction(certificateId, certificateNumber);
                
                // Используем безопасную функцию вибрации
                window.safeVibrate && window.safeVibrate(100);
            }
        });
        
        // Сбрасываем счетчик при уходе мыши с элемента
        card.addEventListener('mouseout', function() {
            // Очищаем таймер при уходе с элемента, но не сбрасываем счетчик
            // чтобы не прерывать возможный двойной клик
            if (clickCount === 1) {
                clearTimeout(clickTimer);
                
                // Даем небольшую задержку перед сбросом счетчика
                setTimeout(() => {
                    clickCount = 0;
                }, 500);
            }
        });
    });
    
    // Обработка действия для документа - показ модального окна управления папками
    function handleCertificateAction(certificateId, certificateNumber) {
        // Устанавливаем номер документа в модальном окне
        document.getElementById('certificateNumberInModal').textContent = certificateNumber;
        
        // Сохраняем ID документа в скрытом поле модального окна
        document.getElementById('currentCertificateId').value = certificateId;
        
        // Показываем модальное окно сразу
        const folderManageModal = new bootstrap.Modal(document.getElementById('folderManageModal'));
        folderManageModal.show();
        
        // Затем загружаем папки (чтобы не блокировать UI)
        setTimeout(() => {
            getCertificateFolders(certificateId);
        }, 100);
    }
});

/**
 * Загружает библиотеку Intro.js если она не загружена
 * @returns {Promise} Promise, который разрешается после загрузки библиотеки
 */
function loadIntroJs() {
    return new Promise((resolve, reject) => {
        // Проверяем, загружена ли уже библиотека
        if (typeof introJs !== 'undefined') {
            resolve();
            return;
        }
        
        // Загружаем CSS для Intro.js
        if (!document.querySelector('link[href*="introjs.min.css"]')) {
            const linkCSS = document.createElement('link');
            linkCSS.rel = 'stylesheet';
            linkCSS.href = 'https://cdn.jsdelivr.net/npm/intro.js@7.0.1/minified/introjs.min.css';
            document.head.appendChild(linkCSS);
        }
        
        // Загружаем JavaScript библиотеку
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/intro.js@7.0.1/minified/intro.min.js';
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

// Обновляем текст в подсказке обучения 
function startUserCertificatesTour() {
    loadIntroJs().then(() => {
        const tour = introJs();
        
        tour.setOptions({
            nextLabel: 'Далее',
            prevLabel: 'Назад',
      
            doneLabel: 'Готово',
            showBullets: true,
            showProgress: true,
            disableInteraction: false,
            scrollToElement: true,
            overlayOpacity: 0.7,
            steps: [
                {
                    title: 'Добро пожаловать!',
                    intro: 'Здесь отображаются ваши подарочные документы. Давайте познакомимся с основными функциями.'
                },
                {
                    element: document.querySelector('.folder-system'),
                    title: 'Система папок',
                    intro: 'Здесь вы можете организовать ваши документы по папкам для удобного доступа.'
                },
                {
                    element: document.querySelector('button[data-bs-target="#createFolderModal"]'),
                    title: 'Создание папки',
                    intro: 'Нажмите на эту кнопку, чтобы создать новую папку для организации ваших документов.'
                },
                {
                    element: document.querySelector('.folder-navigation'),
                    title: 'Навигация по папкам',
                    intro: 'Используйте эти кнопки для переключения между папками с документами.'
                }
            ]
        });
        
        // Добавляем шаги в зависимости от наличия документов на странице
        if (document.querySelector('.certificate-card')) {
            tour.addSteps([
                {
                    element: document.querySelector('.certificate-card'),
                    title: 'Карточка документа',
                    intro: 'Это карточка вашего подарочного документа. Нажмите на нее, чтобы открыть детальную информацию.'
                },
                {
                    element: document.querySelector('.certificate-status-badge'),
                    title: 'Статус документа',
                    intro: 'Здесь показан текущий статус документа: активен, использован, истек или отменен.'
                },
                
                {
                    element: document.querySelector('.certificate-actions'),
                    title: 'Действия с документом',
                    intro: 'Здесь находятся кнопки для просмотра и управления документом.'
                },
                {
                    title: 'Дополнительные функции',
                    intro: 'Двойной клик на карточке документа или папке откроет дополнительное меню управления.'
                }
            ]);
        } else {
            tour.addSteps([
                {
                    element: document.querySelector('.col-12 .card'),
                    title: 'Пока нет документов',
                    intro: 'Здесь будут отображаться ваши подарочные документы, когда они появятся.'
                }
            ]);
        }
        
        tour.addStep({
            title: 'Готово!',
            intro: 'Теперь вы знаете, как управлять своими подарочными документами. Если у вас возникнут вопросы, вы всегда можете повторить обучение, нажав кнопку "Обучение".'
        });
        
        tour.start();
    }).catch(error => {
        console.error('Не удалось загрузить Intro.js:', error);
        alert('Не удалось загрузить обучение. Пожалуйста, проверьте интернет-соединение и попробуйте снова.');
    });
}

// Автоматический запуск обучения при первом посещении страницы
document.addEventListener('DOMContentLoaded', function() {
    // Проверяем, было ли уже показано обучение
    const hasSeenUserTour = localStorage.getItem('user_certificates_tour_seen');
    
    // Если обучение еще не было показано
    if (!hasSeenUserTour) {
        // Даем небольшую задержку для полной загрузки страницы
        setTimeout(() => {
            startUserCertificatesTour();
            // Отмечаем, что обучение было показано
            localStorage.setItem('user_certificates_tour_seen', 'true');
        }, 1000);
    }
});

// Обновленная функция очистки обработчиков и правильная инициализация обработчиков событий
document.addEventListener('DOMContentLoaded', function() {
    // Сначала очищаем все существующие модальные бэкдропы
    cleanupModalBackdrops();
    
    // Инициализируем обработчики двойного клика
    initDoubleClickHandlers();
    
    // Проверяем наличие нужных модальных окон и создаем их, если они отсутствуют
    ensureRequiredModals();
    
    // Добавляем слушателя для всех модальных окон, чтобы корректно очищать backdrop
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            setTimeout(cleanupModalBackdrops, 300);
        });
    });
});

// Функция для инициализации всех обработчиков двойного клика
function initDoubleClickHandlers() {
    // Очищаем существующие обработчики и инициализируем новые
    setupCertificateDoubleClickHandlers();
    setupFolderDoubleClickHandlers();
}

// Функция для настройки двойного клика на карточках документов
function setupCertificateDoubleClickHandlers() {
    console.log('Настройка обработчиков двойного клика для документов');
    const certificateCards = document.querySelectorAll('.certificate-card');
    
    certificateCards.forEach(card => {
        // Очищаем существующие обработчики
        const cardClone = card.cloneNode(true);
        if (card.parentNode) {
            card.parentNode.replaceChild(cardClone, card);
        }
        
        // Сохраняем данные документа
        const certificateId = cardClone.dataset.certificateId;
        const publicUrl = cardClone.dataset.publicUrl;
        const certificateNumber = cardClone.dataset.certificateNumber;
        
        // Добавляем класс для указания кликабельности
        cardClone.classList.add('clickable');
        
        let clickTimer = null;
        let clickCount = 0;
        
        // Добавляем новый обработчик
        cardClone.addEventListener('click', function(e) {
            // Если клик был на кнопке или в выпадающем меню, не обрабатываем
            if (e.target.closest('.btn') || e.target.closest('.dropdown-menu')) {
                return;
            }
            
            e.preventDefault();
            console.log('Клик на документе', certificateId);
            
            clickCount++;
            
            if (clickCount === 1) {
                clickTimer = setTimeout(function() {
                    clickCount = 0;
                    // Если это одиночный клик - открываем публичную страницу документа
                    window.open(publicUrl, '_blank');
                }, 300);
            } else if (clickCount === 2) {
                clearTimeout(clickTimer);
                clickCount = 0;
                // Если это двойной клик - показываем модальное окно управления папками
                console.log('Двойной клик на документе', certificateId);
                
                // Визуальный фидбэк
                cardClone.classList.add('long-press-animation');
                setTimeout(() => {
                    cardClone.classList.remove('long-press-animation');
                }, 500);
                
                // Вызываем функцию для отображения модального окна
                openFolderManageModal(certificateId, certificateNumber);
                
                // Используем безопасную функцию вибрации
                if (window.navigator && window.navigator.vibrate) {
                    window.navigator.vibrate(100);
                }
            }
        });
    });
}

// Функция для настройки двойного клика на папках
function setupFolderDoubleClickHandlers() {
    console.log('Настройка обработчиков двойного клика для папок');
    const folderButtons = document.querySelectorAll('.folder-btn');
    
    folderButtons.forEach(button => {
        // Очищаем существующие обработчики
        const buttonClone = button.cloneNode(true);
        if (button.parentNode) {
            button.parentNode.replaceChild(buttonClone, button);
        }
        
        // Сохраняем данные папки из атрибутов
        const folderId = buttonClone.getAttribute('data-folder-id');
        const folderName = buttonClone.getAttribute('data-folder-name');
        const folderColor = buttonClone.getAttribute('data-folder-color');
        const href = buttonClone.getAttribute('href');
        
        // Добавляем класс для указания кликабельности
        buttonClone.classList.add('clickable');
        
        let clickCount = 0;
        let clickTimer = null;
        
        // Добавляем новый обработчик
        buttonClone.addEventListener('click', function(e) {
            // Останавливаем стандартное поведение (переход по ссылке)
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Клик на папке', folderId);
            
            // Увеличиваем счетчик кликов
            clickCount++;
            
            // Если это первый клик
            if (clickCount === 1) {
                // Визуальный фидбек
                this.classList.add('btn-pressing');
                setTimeout(() => {
                    this.classList.remove('btn-pressing');
                }, 200);
                
                // Устанавливаем таймер для одиночного клика
                clickTimer = setTimeout(() => {
                    clickCount = 0;
                    console.log('Одиночный клик на папке, переход по URL:', href);
                    
                    // Переход по ссылке
                    if (typeof loadPageContent === 'function') {
                        loadPageContent(href);
                    } else {
                        window.location.href = href; // Резервный вариант
                    }
                }, 300);
            } 
            // Если это второй клик (двойной клик)
            else if (clickCount === 2) {
                // Очищаем таймер одиночного клика
                clearTimeout(clickTimer);
                clickCount = 0;
                
                console.log('Двойной клик на папке', folderId);
                
                // Визуальный фидбек
                this.classList.add('double-clicked');
                setTimeout(() => {
                    this.classList.remove('double-clicked');
                }, 400);
                
                // Вибрация для тактильной обратной связи
                if (window.navigator && window.navigator.vibrate) {
                    window.navigator.vibrate(100);
                }
                
                // Открываем модальное окно удаления папки
                openDeleteFolderModal(folderId, folderName, folderColor);
            }
        });
    });
}

// Функция для отображения модального окна управления папками документа
function openFolderManageModal(certificateId, certificateNumber) {
    console.log('Открытие модального окна управления папками для документа', certificateId);
    
    // Очищаем возможные оставшиеся модальные фоны
    cleanupModalBackdrops();
    
    // Проверяем наличие модального окна
    let folderManageModal = document.getElementById('folderManageModal');
    
    // Если модальное окно не найдено, создаем его
    if (!folderManageModal) {
        createFolderManageModal();
        folderManageModal = document.getElementById('folderManageModal');
    }
    
    // Устанавливаем номер документа в модальном окне
    document.getElementById('certificateNumberInModal').textContent = certificateNumber;
    
    // Сохраняем ID документа в скрытом поле модального окна
    document.getElementById('currentCertificateId').value = certificateId;
    
    // Показываем модальное окно управления папками
    const modal = new bootstrap.Modal(folderManageModal);
    modal.show();
    
    // Загружаем список папок для документа
    setTimeout(() => {
        getCertificateFolders(certificateId);
    }, 100);
}

// Функция для отображения модального окна удаления папки
function openDeleteFolderModal(folderId, folderName, folderColor) {
    console.log('Открытие модального окна удаления папки', folderId);
    
    // Очищаем возможные оставшиеся модальные фоны
    cleanupModalBackdrops();
    
    // Находим модальное окно
    const deleteFolderModal = document.getElementById('deleteFolderModal');
    
    if (!deleteFolderModal) {
        console.error('Модальное окно для удаления папки не найдено!');
        return;
    }
    
    // Обновляем данные в модальном окне
    const folderNameElement = document.getElementById('folderNameToDelete');
    if (folderNameElement) {
        folderNameElement.textContent = folderName;
        folderNameElement.className = `text-${folderColor || 'danger'}`;
    }
    
    // Обновляем действие формы удаления
    const deleteForm = document.getElementById('delete-folder-form');
    if (deleteForm) {
        deleteForm.action = `/user/folders/${folderId}`;
    }
    
    // Открываем модальное окно
    const modal = new bootstrap.Modal(deleteFolderModal);
    modal.show();
}

// Функция для создания модального окна управления папками документа
function createFolderManageModal() {
    console.log('Создание модального окна управления папками');
    
    // HTML-код модального окна
    const modalHTML = `
        <div class="modal fade" id="folderManageModal" tabindex="-1" aria-labelledby="folderManageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="folderManageModalLabel">Управление папками документа</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>документ № <span id="certificateNumberInModal">-</span></p>
                        <input type="hidden" id="currentCertificateId" value="">
                        
                        <h6 class="mt-3 mb-2">Папки документа:</h6>
                        <div id="foldersList" class="list-group mb-3">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Загрузка...</span>
                                </div>
                                <p class="mb-0 small">Загрузка списка папок...</p>
                            </div>
                        </div>
                        
                        <div id="folderActionStatus" class="alert alert-success" style="display: none;">
                            <span></span>
                        </div>
                        
                        <div class="mt-3">
                            <label for="newFolderName" class="form-label">Создать новую папку</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newFolderName" placeholder="Название папки">
                                <button class="btn btn-primary" type="button" id="createFolderBtn">Создать</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Добавляем модальное окно в DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Настраиваем кнопку создания новой папки
    const createFolderBtn = document.getElementById('createFolderBtn');
    const newFolderName = document.getElementById('newFolderName');
    
    if (createFolderBtn && newFolderName) {
        createFolderBtn.addEventListener('click', function() {
            const folderName = newFolderName.value.trim();
            if (!folderName) {
                alert('Введите название папки');
                return;
            }
            
            createFolderBtn.disabled = true;
            createFolderBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Создание...';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch('/user/folders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    name: folderName,
                    _token: csrfToken
                })
            })
            .then(response => response.json())
            .then(data => {
                newFolderName.value = '';
                createFolderBtn.disabled = false;
                createFolderBtn.innerHTML = 'Создать';
                
                if (data.success) {
                    // Показываем сообщение об успехе
                    const folderActionStatus = document.getElementById('folderActionStatus');
                    if (folderActionStatus) {
                        const statusSpan = folderActionStatus.querySelector('span');
                        if (statusSpan) statusSpan.textContent = 'Папка успешно создана';
                        folderActionStatus.style.display = 'block';
                        folderActionStatus.classList.remove('alert-danger');
                        folderActionStatus.classList.add('alert-success');
                        
                        // Скрываем сообщение через 3 секунды
                        setTimeout(() => {
                            folderActionStatus.style.display = 'none';
                        }, 3000);
                    }
                    
                    // Обновляем список папок
                    const currentCertificateId = document.getElementById('currentCertificateId');
                    if (currentCertificateId && currentCertificateId.value) {
                        getCertificateFolders(currentCertificateId.value);
                    }
                } else {
                    // Показываем сообщение об ошибке
                    const folderActionStatus = document.getElementById('folderActionStatus');
                    if (folderActionStatus) {
                        const statusSpan = folderActionStatus.querySelector('span');
                        if (statusSpan) statusSpan.textContent = data.message || 'Ошибка при создании папки';
                        folderActionStatus.style.display = 'block';
                        folderActionStatus.classList.remove('alert-success');
                        folderActionStatus.classList.add('alert-danger');
                    }
                }
            })
            .catch(error => {
                console.error('Ошибка при создании папки:', error);
                createFolderBtn.disabled = false;
                createFolderBtn.innerHTML = 'Создать';
                
                // Показываем сообщение об ошибке
                const folderActionStatus = document.getElementById('folderActionStatus');
                if (folderActionStatus) {
                    const statusSpan = folderActionStatus.querySelector('span');
                    if (statusSpan) statusSpan.textContent = 'Ошибка: не удалось создать папку';
                    folderActionStatus.style.display = 'block';
                    folderActionStatus.classList.remove('alert-success');
                    folderActionStatus.classList.add('alert-danger');
                }
            });
        });
    }
    
    // Добавляем обработчик нажатия Enter в поле ввода
    if (newFolderName) {
        newFolderName.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                createFolderBtn.click();
            }
        });
    }
    
    // Добавляем обработчик закрытия модального окна
    const folderManageModal = document.getElementById('folderManageModal');
    if (folderManageModal) {
        folderManageModal.addEventListener('hidden.bs.modal', function() {
            setTimeout(cleanupModalBackdrops, 300);
        });
    }
}

// Функция для очистки оставшихся modal-backdrop
function cleanupModalBackdrops() {
    console.log('Очистка модальных бэкдропов');
    
    const backdrops = document.querySelectorAll('.modal-backdrop');
    if (backdrops.length > 0) {
        backdrops.forEach(backdrop => {
            backdrop.classList.remove('modal-backdrop', 'fade', 'show');
            setTimeout(() => {
                if (backdrop.parentNode) {
                    backdrop.parentNode.removeChild(backdrop);
                }
            }, 100);
        });
    }
    
    // Убираем класс modal-open с body, если нет активных модальных окон
    const activeModals = document.querySelectorAll('.modal.show');
    if (activeModals.length === 0) {
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
}

// Функция для проверки наличия необходимых модальных окон
function ensureRequiredModals() {
    // Проверяем наличие модального окна управления папками
    if (!document.getElementById('folderManageModal')) {
        createFolderManageModal();
    }
}

// Переопределяем функцию глобально для доступа из всех обработчиков
window.initDoubleClickHandlers = initDoubleClickHandlers;

// Интеграция с функцией обновления контента через AJAX
const originalSetupEventHandlers = window.setupEventHandlers || function() {};
window.setupEventHandlers = function() {
    originalSetupEventHandlers();
    initDoubleClickHandlers();
};
</script>

<!-- Дополнительные стили для новых элементов -->
<style>


/* Стили для группировки документов по датам */
.date-group-heading {
    padding-left: 0.5rem;
    border-left: 3px solid var(--bs-primary);
}

/* Улучшаем стили для итоговой карточки */
.stats-card {
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stat-box {
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: scale(1.05);
}

/* Дополнительные стили для правильного отображения сетки */
@media (max-width: 575.98px) {
    .row-cols-1 > .col {
        flex: 0 0 auto;
        width: 100%;
    }
}

/* Исправление для некоторых браузеров, где flex-box может работать некорректно */
.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: calc(var(--bs-gutter-x) * -.5);
    margin-left: calc(var(--bs-gutter-x) * -.5);
}

.col {
    padding-right: calc(var(--bs-gutter-x) * .5);
    padding-left: calc(var(--bs-gutter-x) * .5);
    margin-top: var(--bs-gutter-y);
}
</style>