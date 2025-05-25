<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainContainer = document.getElementById('mainContainer');
    const coverSection = document.getElementById('coverSection');
    const certificateSection = document.getElementById('certificateSection');
    const swipeIndicator = document.getElementById('swipeIndicator');
    const iframe = document.getElementById('certificate-frame') || document.getElementById('certificateIframe');
    let animationTriggered = false; // Флаг для отслеживания запуска анимации
    let effectLoaded = false; // Флаг для отслеживания загрузки эффекта
    
    // Получаем URL логотипа
    const logoUrl = '{{ $certificate->company_logo === null ? "none" : ($certificate->company_logo ? asset("storage/" . $certificate->company_logo) : ($certificate->user->company_logo ? asset("storage/" . $certificate->user->company_logo) : asset("images/default-logo.png"))) }}';
    console.log("Логотип для публичного документа:", logoUrl);
    
    // Функция для перехода к документу
    function showCertificate() {
        if (mainContainer) {
            mainContainer.classList.add('scrolled');
        }
    }
    
    // Функция для возврата к обложке
    function showCover() {
        if (mainContainer) {
            mainContainer.classList.remove('scrolled');
        }
    }
    
    // Обработчики для тач-скрина (свайп)
    let touchStartY = 0;
    let touchEndY = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartY = e.changedTouches[0].screenY;
    }, false);
    
    document.addEventListener('touchend', function(e) {
        touchEndY = e.changedTouches[0].screenY;
        handleSwipe();
    }, false);
    
    function handleSwipe() {
        const swipeDistance = touchStartY - touchEndY;
        
        // Определяем направление свайпа (вверх или вниз)
        if (swipeDistance > 50) { // Свайп вверх
            if (mainContainer && !mainContainer.classList.contains('scrolled')) {
                console.log('Свайп вверх обнаружен, показываем документ');
                showCertificate();
            }
        } else if (swipeDistance < -50) { // Свайп вниз
            if (mainContainer && mainContainer.classList.contains('scrolled') && window.scrollY === 0) {
                console.log('Свайп вниз обнаружен, показываем обложку');
                showCover();
            }
        }
    }
    
    // Обработка колеса мыши
    let isScrolling = false;
    window.addEventListener('wheel', function(e) {
        if (isScrolling) return;
        isScrolling = true;
        
        setTimeout(() => {
            isScrolling = false;
        }, 1000); // Предотвращаем множественные события прокрутки
        
        if (e.deltaY > 0) { // Прокрутка вниз
            if (mainContainer && !mainContainer.classList.contains('scrolled')) {
                console.log('Прокрутка вниз обнаружена, показываем документ');
                showCertificate();
            }
        } else if (e.deltaY < 0) { // Прокрутка вверх
            if (mainContainer && mainContainer.classList.contains('scrolled') && window.scrollY === 0) {
                console.log('Прокрутка вверх обнаружена, показываем обложку');
                showCover();
            }
        }
    });
    
    // Улучшенный обработчик для индикатора свайпа
    function initSwipeIndicator() {
        const swipeIndicator = document.getElementById('swipeIndicator');
        
        if (swipeIndicator) {
            console.log('Инициализация обработчиков для индикатора свайпа');
            
            // Добавляем несколько обработчиков для надежности
            ['click', 'touchend', 'keydown'].forEach(function(eventType) {
                swipeIndicator.addEventListener(eventType, function(e) {
                    // Для клавиатуры проверяем, что это Enter или Space
                    if (eventType === 'keydown' && (e.key !== 'Enter' && e.key !== ' ')) {
                        return;
                    }
                    
                    console.log('Событие на индикаторе свайпа:', eventType);
                    e.preventDefault();
                    showCertificate();
                    
                    // Добавляем визуальное подтверждение нажатия
                    this.style.opacity = '0.7';
                    setTimeout(() => {
                        this.style.opacity = '1';
                    }, 300);
                });
            });
            
            // Добавляем анимацию для привлечения внимания
            setTimeout(() => {
                swipeIndicator.classList.add('animate__animated', 'animate__pulse');
            }, 1000);
        } else {
            console.warn('Индикатор свайпа не найден в DOM!');
        }
    }
    
    // Инициализируем индикатор при загрузке страницы
    initSwipeIndicator();
    
    // Повторно инициализируем через небольшую задержку для решения проблем с асинхронной загрузкой
    setTimeout(initSwipeIndicator, 1000);
    
    // Клик по индикатору свайпа также переключает вид
    if (swipeIndicator) {
        swipeIndicator.addEventListener('click', function() {
            console.log('Нажатие на индикатор свайпа, показываем документ');
            showCertificate();
        });
    }
    
    // Функция обновления логотипа в iframe
    function updateLogoInIframe() {
        try {
            if (!iframe || !iframe.contentWindow) {
                console.warn("iframe не найден или не доступен");
                return;
            }
            
            // Отправляем сообщение с URL логотипа в iframe
            iframe.contentWindow.postMessage({
                type: 'update_logo',
                logo_url: logoUrl
            }, '*');
            console.log("Логотип отправлен в iframe");
        } catch (error) {
            console.error("Ошибка отправки логотипа:", error);
        }
    }
    
    // Дождемся загрузки iframe
    if (iframe) {
        iframe.addEventListener('load', function() {
            console.log("Iframe загружен, отправляем логотип...");
            
            // Первая попытка после небольшой задержки
            setTimeout(updateLogoInIframe, 500);
            
            // Дополнительная попытка через более длительное время для надежности
            setTimeout(updateLogoInIframe, 1500);
        });
        
        // Для iframe, которые могли быть загружены до установки обработчиков
        if (iframe.complete) {
            console.log("Iframe уже загружен, отправляем логотип немедленно...");
            updateLogoInIframe();
        }
    } else {
        console.warn("iframe не найден на странице");
    }
    
    // Обработчик для получения ответа от iframe
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'logo_updated') {
            if (event.data.success) {
                console.log("Логотип успешно обновлен в iframe, обновлено элементов:", event.data.count);
            } else {
                console.warn("Не удалось обновить логотип:", event.data.error);
            }
        }
        
        // Реагируем на сообщение о загрузке документа
        if (event.data && event.data.type === 'certificate_loaded') {
            console.log("Получено уведомление о загрузке документа:", event.data.template);
        }
    });
    
    // Обработка нажатия на кнопку QR-кода для мобильных устройств
    const qrToggle = document.getElementById('adminQrToggle');
    const qrCode = document.getElementById('adminQrCode');
    const qrFullscreen = document.getElementById('qrFullscreenOverlay');
    const qrCloseBtn = document.querySelector('.qr-close-btn');

    console.log('QR элементы:', {
        toggle: qrToggle ? 'найден' : 'не найден',
        code: qrCode ? 'найден' : 'не найден',
        fullscreen: qrFullscreen ? 'найден' : 'не найден',
        closeBtn: qrCloseBtn ? 'найден' : 'не найден'
    });

    // Функция для открытия QR-кода с анимацией
    function openQRModal() {
        console.log('Открываем QR код на весь экран');
        if (qrFullscreen) {
            qrFullscreen.classList.add('active');
            // Предотвращаем скролл под модальным окном
            document.body.style.overflow = 'hidden';
        } else {
            console.error('Модальное окно QR не найдено!');
        }
    }

    // Функция для закрытия QR-кода с анимацией
    function closeQRModal() {
        console.log('Закрываем QR код');
        if (qrFullscreen) {
            qrFullscreen.classList.remove('active');
            // Возвращаем скролл после закрытия
            document.body.style.overflow = '';
        }
    }

    // Обработчик для открытия QR-кода на весь экран при нажатии на toggle
    if (qrToggle) {
        console.log('Добавляем обработчик клика для кнопки QR');
        qrToggle.addEventListener('click', function(e) {
            console.log('Нажатие на кнопку QR');
            e.preventDefault();
            e.stopPropagation(); // Предотвращаем всплытие события
            openQRModal();
        });
    } else {
        console.warn('Кнопка QR не найдена');
    }

    // Обработчик для открытия QR-кода на весь экран при нажатии на обычный QR
    if (qrCode) {
        qrCode.addEventListener('click', function(e) {
            console.log('Нажатие на QR код');
            e.stopPropagation(); // Предотвращаем всплытие события
            openQRModal();
        });
    }

    // Обработчик для закрытия QR-кода на весь экран
    if (qrCloseBtn) {
        qrCloseBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Предотвращаем всплытие события
            closeQRModal();
        });
    }

    // Закрытие по клику на overlay
    if (qrFullscreen) {
        qrFullscreen.addEventListener('click', function(e) {
            if (e.target === qrFullscreen) {
                closeQRModal();
            }
        });
    }
});

// Добавляем функции для модального окна печати
function showPrintOptions() {
    const printOptionsOverlay = document.getElementById('printOptionsOverlay');
    if (printOptionsOverlay) {
        printOptionsOverlay.classList.add('active');
    }
}

function hidePrintOptions() {
    const printOptionsOverlay = document.getElementById('printOptionsOverlay');
    if (printOptionsOverlay) {
        printOptionsOverlay.classList.remove('active');
    }
}

// Закрытие по клику вне контента
const printOptionsOverlay = document.getElementById('printOptionsOverlay');
if (printOptionsOverlay) {
    printOptionsOverlay.addEventListener('click', function(e) {
        if (e.target === this) {
            hidePrintOptions();
        }
    });
}

// Закрытие по нажатия Esc
document.addEventListener('keydown', function(e) {
    const printOptionsOverlay = document.getElementById('printOptionsOverlay');
    if (e.key === 'Escape' && printOptionsOverlay && printOptionsOverlay.classList.contains('active')) {
        hidePrintOptions();
    }
});

// Добавляем функцию для обновления оставшихся дней и склонения слова
function updateDaysRemaining() {
    try {
        // Получаем элементы для обновления
        const daysRemainingElement = document.getElementById('daysRemaining');
        const daysTextElement = document.getElementById('daysText');
        
        // Проверяем наличие элементов
        if (!daysRemainingElement || !daysTextElement) {
            console.log('Элементы для отображения оставшихся дней не найдены');
            return;
        }
        
        // Получаем даты из документа
        const validUntilDateStr = '{{ isset($certificate->valid_until) ? $certificate->valid_until : "" }}';
        if (!validUntilDateStr) {
            console.log('Дата окончания сертификата не указана');
            return;
        }
        
        const validUntilDate = new Date(validUntilDateStr);
        const currentDate = new Date();
        
        // Вычисляем разницу в днях
        const timeDiff = validUntilDate - currentDate;
        const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
        
        // Обновляем число дней
        daysRemainingElement.textContent = daysDiff > 0 ? daysDiff : 0;
        
        // Функция для правильного склонения слова "день"
        function getDaysDeclension(days) {
            if (days % 10 === 1 && days % 100 !== 11) {
                return 'день';
            } else if ([2, 3, 4].includes(days % 10) && ![12, 13, 14].includes(days % 100)) {
                return 'дня';
            } else {
                return 'дней';
            }
        }
        
        // Обновляем текст с правильным склонением
        if (daysDiff <= 0) {
            daysRemainingElement.textContent = '0';
            daysTextElement.textContent = 'дней';
            daysRemainingElement.classList.add('expired');
        } else {
            daysTextElement.textContent = getDaysDeclension(daysDiff);
            
            // Добавляем соответствующие классы для стилизации
            if (daysDiff <= 3) {
                daysRemainingElement.classList.add('critical');
            } else if (daysDiff <= 7) {
                daysRemainingElement.classList.add('warning');
            } else {
                daysRemainingElement.classList.add('normal');
            }
        }
    } catch (error) {
        console.error('Ошибка при обновлении счетчика дней:', error);
    }
}

// Запускаем функцию при загрузке страницы, только если элементы существуют
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('daysRemaining') && document.getElementById('daysText')) {
        updateDaysRemaining();
    }
});

// QR-код функционал - объединяем с уже существующей логикой QR
document.addEventListener('DOMContentLoaded', function() {
    const qrToggle = document.getElementById('adminQrToggle');
    const qrFullscreen = document.getElementById('qrFullscreenOverlay');
    const qrCloseBtn = document.querySelector('.qr-close-btn');

    if (qrToggle && qrFullscreen) {
        qrToggle.addEventListener('click', function() {
            qrFullscreen.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if (qrCloseBtn && qrFullscreen) {
        qrCloseBtn.addEventListener('click', function() {
            qrFullscreen.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    if (qrFullscreen) {
        qrFullscreen.addEventListener('click', function(e) {
            if (e.target === this) {
                qrFullscreen.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
});

// Передача данных в iframe
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('certificateIframe');

    if (iframe) {
        console.log('Найден iframe для передачи данных:', iframe.id);
        
        // Выведем все атрибуты iframe для диагностики
        console.log('Атрибуты iframe:', Array.from(iframe.attributes).map(attr => `${attr.name}="${attr.value}"`).join(', '));
        
        // Добавляем обработчик события загрузки iframe
        iframe.addEventListener('load', function() {
            console.log("Iframe загружен, готовимся к передаче данных сертификата...");
            
            // Отправляем сообщение для деактивации редактирования
            iframe.contentWindow.postMessage({
                type: 'disable_editing',
                disable: true
            }, '*');
            
            // Первая попытка передачи данных сразу после загрузки iframe
            sendDataToIframe();
            
            // Дополнительные попытки с задержкой
            setTimeout(sendDataToIframe, 500);
            setTimeout(sendDataToIframe, 1500);
            setTimeout(sendDataToIframe, 3000);
        });
        
        // Функция для передачи данных в iframe
        function sendDataToIframe() {
            try {
                // Пробуем получить данные сначала из нового атрибута, затем из старого
                let encodedData = iframe.getAttribute('data-certificate');
                
                if (!encodedData) {
                    // Попробуем старый атрибут, если новый не найден
                    encodedData = iframe.getAttribute('data-certificate-data');
                    console.log('Используем запасной атрибут data-certificate-data');
                }
                
                if (encodedData) {
                    try {
                        let data;
                        
                        // Проверяем, начинается ли строка с '{', что означает JSON-строку
                        if (encodedData.trim().startsWith('{')) {
                            // Если это уже JSON-строка, просто парсим её
                            data = JSON.parse(encodedData);
                            console.log('Данные получены из JSON-строки');
                        } else {
                            // Иначе декодируем из base64 и парсим
                            try {
                                const jsonStr = atob(encodedData);
                                data = JSON.parse(jsonStr);
                                console.log('Данные декодированы из base64');
                            } catch (base64Error) {
                                console.error('Ошибка при декодировании base64:', base64Error);
                                // Последняя попытка - может быть это просто экранированный JSON
                                data = JSON.parse(encodedData);
                                console.log('Данные получены из экранированной JSON-строки');
                            }
                        }
                        
                        console.log('Отправка данных сертификата в iframe:', data);
                        
                        // Отправляем данные в iframe через postMessage
                        iframe.contentWindow.postMessage({
                            type: 'update_fields',
                            data: data
                        }, '*');
                        
                    } catch (parseError) {
                        console.error('Ошибка при обработке данных сертификата:', parseError);
                        console.log('Данные для обработки:', encodedData.substring(0, 100) + '...');
                    }
                } else {
                    console.warn('Данные сертификата отсутствуют в атрибутах data-certificate и data-certificate-data');
                    
                    // Резервный вариант - попытка получить из window
                    if (window.certificateData) {
                        console.log('Используем данные из window.certificateData');
                        iframe.contentWindow.postMessage({
                            type: 'update_fields',
                            data: window.certificateData
                        }, '*');
                    }
                }
            } catch (error) {
                console.error('Общая ошибка при передаче данных сертификата:', error);
            }
        }
    } else {
        console.warn('Iframe для сертификата не найден на странице');
    }
});
</script>
