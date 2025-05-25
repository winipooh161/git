<script>
document.addEventListener("DOMContentLoaded", function () {
    // Элементы DOM для предпросмотра
    const previewFrame = document.getElementById('certificatePreview');
    const previewContainer = document.querySelector('.certificate-preview-container');
    let scale = 1;
    let logoUrl = '<?php echo e(Auth::user()->company_logo ? asset('storage/' . Auth::user()->company_logo) : asset('images/default-logo.png')); ?>';
    
    // Улучшенная функция обновления предпросмотра
    window.updatePreview = function() {
        // Получаем форму, которую будем использовать для получения данных
        const formSelector = '#mobileCertificateForm';
        
        // Получаем значения из полей формы
        const recipientName = document.querySelector(`${formSelector} [name="recipient_name"]`)?.value || 'Имя получателя';
        
        // Определяем номинал на основе типа
        let amountText = '';
        const amountTypeRadios = document.querySelectorAll(`${formSelector} [name="amount_type"]`);
        let amountType = 'money';
        
        for (const radio of amountTypeRadios) {
            if (radio.checked) {
                amountType = radio.value;
                break;
            }
        }
        
        if (amountType === 'money') {
            const amount = document.querySelector(`${formSelector} [name="amount"]`)?.value || '3000';
            amountText = `${Number(amount).toLocaleString('ru-RU')} ₽`;
        } else {
            const percent = document.querySelector(`${formSelector} [name="percent_value"]`)?.value || '10';
            amountText = `${percent}%`;
        }
        
        const message = document.querySelector(`${formSelector} [name="message"]`)?.value || '';
        
        // Устанавливаем текущую дату и срок действия
        const validFromEl = document.getElementById('valid_from');
        const validFrom = validFromEl?.value 
            ? new Date(validFromEl.value).toLocaleDateString('ru-RU')
            : new Date().toLocaleDateString('ru-RU');
            
        const validUntilEl = document.querySelector(`${formSelector} [name="valid_until"]`);
        const validUntil = validUntilEl?.value 
            ? new Date(validUntilEl.value).toLocaleDateString('ru-RU')
            : new Date(Date.now() + 90*24*60*60*1000).toLocaleDateString('ru-RU');
        
        // Компания
        const companyName = '<?php echo e(Auth::user()->company ?? config('app.name')); ?>';
        
        // Создаем параметры для запроса
        const params = new URLSearchParams({
            recipient_name: recipientName,
            amount: amountText,
            valid_from: validFrom,
            valid_until: validUntil,
            message: message,
            certificate_number: 'CERT-PREVIEW',
            company_name: companyName
        });
        
        // Обновляем iframe с новыми параметрами
        const iframeSrc = `<?php echo e(route('template.preview', $template)); ?>?${params.toString()}`;
        
        // Функция для обновления iframe и обработки его загрузки
        function updateIframe(iframe) {
            if (!iframe) return;
            
            // Проверяем, нужно ли обновлять iframe
            if (iframe.src.split('?')[0] === iframeSrc.split('?')[0]) {
                // Только обновляем параметры для существующего iframe
                iframe.src = iframeSrc;
            } else {
                // Полностью меняем src, если изменился базовый URL
                iframe.src = iframeSrc;
            }
            
            // После загрузки iframe отправляем логотип и добавляем скрипт
            iframe.onload = function() {
                // Добавляем скрипт для исправления ошибки NodeList.includes
                try {
                    const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
                    
                    // Проверяем, не добавлен ли уже скрипт
                    if (iframeDocument && !iframeDocument.getElementById('certificate-iframe-fix')) {
                        const scriptElement = iframeDocument.createElement('script');
                        scriptElement.id = 'certificate-iframe-fix';
                        scriptElement.src = '<?php echo e(asset("js/certificate-iframe-fix.js")); ?>';
                        // Добавляем скрипт в начало head для раннего исполнения
                        iframeDocument.head.insertBefore(scriptElement, iframeDocument.head.firstChild);
                    }
                } catch (e) {
                    console.log('Cannot access iframe document due to same-origin policy');
                }
                
                // Отправляем логотип через postMessage
                setTimeout(() => {
                    try {
                        iframe.contentWindow.postMessage({
                            type: 'update_logo',
                            logo_url: logoUrl
                        }, '*');
                    } catch (error) {
                        console.error("Ошибка при отправке сообщения в iframe:", error);
                    }
                }, 300);
            };
        }
        
        // Обновляем iframe
        updateIframe(previewFrame);
    };
    
    // Обработка загрузки логотипа
    function handleLogoUpload(input, previewElement) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const tempLogoUrl = e.target.result;
                
                // Обновляем превью на странице
                if (previewElement) {
                    previewElement.innerHTML = `
                        <img src="${tempLogoUrl}" class="img-thumbnail" style="max-height: 60px;" alt="Загруженный логотип">
                        <div class="small text-muted mt-1">Новый логотип</div>
                    `;
                }
                
                // Временно используем URL из FileReader для предпросмотра
                logoUrl = tempLogoUrl;
                window.updatePreview();
                
                // Отправляем файл на сервер для временного хранения
                const formData = new FormData();
                formData.append('logo', input.files[0]);
                formData.append('_token', '<?php echo e(csrf_token()); ?>');
                
                fetch('<?php echo e(route('entrepreneur.certificates.temp-logo')); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Сохраняем URL логотипа с сервера
                        logoUrl = data.logo_url;
                        // Обновляем превью с серверным URL логотипа
                        window.updatePreview();
                    } else {
                        console.error('Ошибка загрузки логотипа:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Произошла ошибка:', error);
                });
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Инициализация общих функций
    function initCommonFunctionality() {
        // Управление масштабом предпросмотра с адаптивным шагом
        document.querySelectorAll('#zoomInButton').forEach(button => {
            button?.addEventListener('click', function() {
                const zoomStep = window.innerWidth < 768 ? 1.05 : 1.1;
                scale *= zoomStep;
                if (previewFrame) previewFrame.style.transform = `scale(${scale})`;
            });
        });
        
        document.querySelectorAll('#zoomOutButton').forEach(button => {
            button?.addEventListener('click', function() {
                const zoomStep = window.innerWidth < 768 ? 0.95 : 0.9;
                scale *= zoomStep;
                if (previewFrame) previewFrame.style.transform = `scale(${scale})`;
            });
        });
        
        document.querySelectorAll('#resetZoomButton').forEach(button => {
            button?.addEventListener('click', function() {
                scale = 1;
                if (previewFrame) previewFrame.style.transform = 'scale(1)';
            });
        });
        
        // Переключение между устройствами с учетом размера экрана
        const deviceButtons = document.querySelectorAll('.device-toggle button');
        deviceButtons.forEach(button => {
            button.addEventListener('click', function() {
                deviceButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const device = this.getAttribute('data-device');
                if (previewContainer) {
                    previewContainer.setAttribute('data-current-device', device);
                }
                
                // Автоматически сбрасываем масштаб при переключении устройства
                scale = 1;
                if (previewFrame) previewFrame.style.transform = 'scale(1)';
                
                // Для мобильных устройств, если выбран desktop, переключаем на tablet
                if (window.innerWidth < 576 && device === 'desktop') {
                    setTimeout(() => {
                        const tabletButton = document.querySelector('[data-device="tablet"]');
                        if (tabletButton) tabletButton.click();
                    }, 100);
                }
            });
        });
        
        // Поворот устройства с улучшенной адаптивностью
        document.querySelectorAll('#rotateViewButton').forEach(button => {
            button?.addEventListener('click', function() {
                if (previewContainer) {
                    const currentDevice = previewContainer.getAttribute('data-current-device');
                    if (currentDevice !== 'desktop') {
                        previewContainer.classList.toggle('landscape');
                        // Сбрасываем масштаб при повороте
                        scale = 1;
                        if (previewFrame) previewFrame.style.transform = 'scale(1)';
                    }
                }
            });
        });
        
        // Обработка загрузки iframe для скрытия индикатора загрузки
        document.querySelectorAll('iframe.certificate-preview').forEach(frame => {
            frame.addEventListener('load', function() {
                const container = this.closest('.certificate-preview-container');
                if (container) {
                    container.parentElement.classList.add('certificate-preview-loaded');
                }
            });
        });
        
        // Инициализация предпросмотра
        window.updatePreview();
    }
    
    // Загрузка и обработка анимационных эффектов
    function initAnimationEffects() {
        const effectsList = document.getElementById('effectsList');
        const selectEffectButton = document.getElementById('selectEffectButton');
        const animationEffectIdInput = document.getElementById('animation_effect_id');
        const selectedEffectNameInput = document.getElementById('selected_effect_name');
        
        // Делаем selectedEffectId глобальной переменной для доступа из разных функций
        window.selectedEffectId = null;
        window.effects = [];
        
        // Делаем эти функции и переменные глобальными для доступа из обеих версий
        window.selectEffectButton = selectEffectButton;
        window.animationEffectIdInput = animationEffectIdInput;
        window.selectedEffectNameInput = selectedEffectNameInput;
        
        // Функция для загрузки списка эффектов
        window.loadAnimationEffects = function() {
            if (!effectsList) return; // Проверяем существование элемента
            
            fetch('<?php echo e(route("animation-effects.get")); ?>')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка сети: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    window.effects = data;
                    renderEffectsList(data);
                })
                .catch(error => {
                    console.error('Ошибка при загрузке эффектов:', error);
                    if (effectsList) {
                        effectsList.innerHTML = `
                            <div class="col-12 text-center py-4">
                                <i class="fa-solid fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                                <p>Не удалось загрузить анимационные эффекты</p>
                                <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadAnimationEffects()">
                                    <i class="fa-solid fa-refresh me-1"></i>Попробовать снова
                                </button>
                            </div>
                        `;
                    }
                });
        };
        
        // Функция для отображения списка эффектов
        function renderEffectsList(effects) {
            if (!effectsList) return; // Проверяем существование элемента
            
            if (!effects || effects.length === 0) {
                effectsList.innerHTML = `
                    <div class="col-12 text-center py-4">
                        <i class="fa-solid fa-ghost text-muted fs-1 mb-3"></i>
                        <p>Анимационные эффекты не найдены</p>
                    </div>
                `;
                return;
            }
            
            // Определяем, мобильное ли это устройство
            const isMobileDevice = window.innerWidth < 768;
            
            // Создаем карточку для отсутствия эффекта
            let effectsHtml = `
                <div class="col-sm-6 col-lg-4">
                    <div class="card h-100 effect-card ${!selectedEffectId ? 'selected' : ''}" data-effect-id="">
                        <div class="card-body text-center">
                            <h6 class="card-title">Без эффекта</h6>
                            <p class="card-text text-muted small">документ без анимации</p>
                        </div>
                        <div class="card-footer bg-transparent text-center">
                            <button type="button" class="btn btn-sm ${!selectedEffectId ? 'btn-primary' : 'btn-outline-primary'} select-effect-btn ${isMobileDevice ? 'select-effect-btn-mobile' : ''}" data-effect-id="">
                                ${!selectedEffectId ? 'Выбрано' : 'Выбрать'}
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Добавляем карточки для каждого эффекта
            effects.forEach(effect => {
                const isSelected = selectedEffectId === effect.id;
                const particlesPreview = Array.isArray(effect.particles) && effect.particles.length > 0
                    ? effect.particles.slice(0, 5).join(' ')
                    : '✨';
                
                effectsHtml += `
                    <div class="col-sm-6 col-lg-4">
                        <div class="card h-100 effect-card ${isSelected ? 'selected' : ''}" data-effect-id="${effect.id}">
                            <div class="card-body text-center">
                                <h6 class="card-title">${effect.name}</h6>
                                <p class="particles-preview">${particlesPreview}</p>
                                <p class="card-text text-muted small">${effect.description || 'Анимационный эффект'}</p>
                                <div class="badge bg-secondary-subtle text-secondary small mb-1">${getEffectTypeName(effect.type)}</div>
                            </div>
                            <div class="card-footer bg-transparent text-center">
                                <button type="button" class="btn btn-sm ${isSelected ? 'btn-primary' : 'btn-outline-primary'} select-effect-btn ${isMobileDevice ? 'select-effect-btn-mobile' : ''}" data-effect-id="${effect.id}">
                                    ${isSelected ? 'Выбрано' : 'Выбрать'}
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            effectsList.innerHTML = effectsHtml;
            
            // Добавляем обработчики событий для карточек и кнопок
            document.querySelectorAll('.effect-card').forEach(card => {
                // Для мобильных устройств добавляем прямой обработчик клика на карточку
                if (isMobileDevice) {
                    card.addEventListener('click', function() {
                        const effectId = this.getAttribute('data-effect-id');
                        
                        // Выбираем эффект
                        window.previewEffect(effectId ? parseInt(effectId) : null);
                        
                        // На мобильных устройствах сразу применяем эффект и закрываем модальное окно
                        if (selectEffectButton) {
                            selectEffectButton.click();
                            
                            // Закрываем модальное окно
                            const modal = bootstrap.Modal.getInstance(document.getElementById('animationEffectsModal'));
                            if (modal) {
                                modal.hide();
                            }
                        }
                        
                        // Вибрация для обратной связи
                        if (window.safeVibrate) {
                            window.safeVibrate([20, 30, 50]);
                        }
                    });
                } else {
                    // Для десктопов оставляем обработчик только на кнопку
                    const button = card.querySelector('.select-effect-btn');
                    if (button) {
                        button.addEventListener('click', function(e) {
                            e.stopPropagation(); // Останавливаем всплытие события
                            const effectId = this.getAttribute('data-effect-id');
                            window.previewEffect(effectId ? parseInt(effectId) : null);
                        });
                    }
                }
            });
        }
        
        // Получение названия типа эффекта
        function getEffectTypeName(type) {
            const types = {
                'emoji': 'Эмодзи',
                'confetti': 'Конфетти',
                'snow': 'Снег',
                'fireworks': 'Фейерверк',
                'bubbles': 'Пузыри',
                'leaves': 'Листья',
                'stars': 'Звёзды'
            };
            return types[type] || type;
        }
        
        // Функция для предпросмотра и выбора эффекта
        window.previewEffect = function(effectId) {
            // Снимаем выделение со всех карточек
            document.querySelectorAll('.effect-card').forEach(card => {
                card.classList.remove('selected');
                const button = card.querySelector('.btn');
                if (button) {
                    button.classList.replace('btn-primary', 'btn-outline-primary');
                    button.textContent = 'Выбрать';
                }
            });
            
            // Выделяем выбранную карточку
            if (effectId !== null) {
                const selectedCard = document.querySelector(`.effect-card[data-effect-id="${effectId}"]`);
                if (selectedCard) {
                    selectedCard.classList.add('selected');
                    const button = selectedCard.querySelector('.btn');
                    if (button) {
                        button.classList.replace('btn-outline-primary', 'btn-primary');
                        button.textContent = 'Выбрано';
                    }
                }
            } else {
                // Если выбрано "Без эффекта"
                const noEffectCard = document.querySelector('.effect-card[data-effect-id=""]');
                if (noEffectCard) {
                    noEffectCard.classList.add('selected');
                    const button = noEffectCard.querySelector('.btn');
                    if (button) {
                        button.classList.replace('btn-outline-primary', 'btn-primary');
                    }
                }
            }
            
            // Сохраняем выбранный эффект в глобальную переменную
            window.selectedEffectId = effectId;
            if (selectEffectButton) selectEffectButton.disabled = false;
            
            // Немедленно применяем эффект к полю ввода для надежности
            if (animationEffectIdInput) {
                animationEffectIdInput.value = effectId !== null ? effectId : '';
                console.log('ID эффекта установлен: ', animationEffectIdInput.value);
            } else {
                console.error('Элемент animation_effect_id не найден!');
            }
            
            // Активируем временный предпросмотр эффекта, если он выбран
            if (effectId !== null) {
                const effect = effects.find(e => e.id === effectId);
                if (effect) {
                    showEffectPreview(effect);
                }
            }
        };
        
        // Предпросмотр эффекта в модальном окне
        function showEffectPreview(effect) {
            // Создаем временный контейнер для предпросмотра эффекта
            const previewContainer = document.createElement('div');
            previewContainer.className = 'effect-preview-container';
            previewContainer.style.position = 'absolute';
            previewContainer.style.top = '0';
            previewContainer.style.left = '0';
            previewContainer.style.width = '100%';
            previewContainer.style.height = '100%';
            previewContainer.style.pointerEvents = 'none';
            previewContainer.style.zIndex = '1050';
            document.body.appendChild(previewContainer);
            
            // Создаем частицы для эффекта
            const particleCount = Math.min(effect.quantity || 30, 30);
            const particles = Array.isArray(effect.particles) ? effect.particles : ['✨'];
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                
                // Случайное расположение и стили
                particle.style.position = 'absolute';
                particle.style.left = `${Math.random() * 90 + 5}%`;
                particle.style.top = `${Math.random() * 50 + 25}%`;
                
                // Случайный размер
                const size = Math.floor(Math.random() * 16) + 16;
                particle.style.fontSize = `${size}px`;
                
                // Случайная задержка анимации
                const delay = Math.random() * 2;
                particle.style.animationDelay = `${delay}s`;
                
                // Анимация
                particle.style.animation = `float-preview 2s ease-in-out infinite`;
                
                // Содержимое частицы
                const particleText = particles[Math.floor(Math.random() * particles.length)];
                particle.textContent = particleText;
                
                // Добавляем частицу в контейнер
                previewContainer.appendChild(particle);
            }
            
            // Вибрация для тактильной обратной связи (если доступно)
            if (window.safeVibrate) {
                window.safeVibrate([20, 30, 20]);
            }
            
            // Удаляем предпросмотр через несколько секунд
            setTimeout(() => {
                if (previewContainer.parentNode) {
                    previewContainer.parentNode.removeChild(previewContainer);
                }
            }, 2000);
        }
        
        // Применение выбранного эффекта
        if (selectEffectButton) {
            selectEffectButton.addEventListener('click', function() {
                // Находим все элементы снова на тот случай, если они были перерисованы
                const currentAnimEffectInput = document.getElementById('animation_effect_id');
                const currentEffectNameInput = document.getElementById('selected_effect_name');
                
                // Обновляем значение поля ID анимации
                if (currentAnimEffectInput) {
                    currentAnimEffectInput.value = window.selectedEffectId || '';
                    console.log('При клике установлен ID эффекта: ', currentAnimEffectInput.value);
                } else if (animationEffectIdInput) {
                    animationEffectIdInput.value = window.selectedEffectId || '';
                    console.log('При клике установлен ID эффекта (из замыкания): ', animationEffectIdInput.value);
                } else {
                    console.error('Элементы полей эффекта не найдены!');
                }
                
                // Обновляем отображаемое название
                if (window.selectedEffectId && (currentEffectNameInput || selectedEffectNameInput)) {
                    const effectInput = currentEffectNameInput || selectedEffectNameInput;
                    const selectedEffect = window.effects.find(effect => effect.id === window.selectedEffectId);
                    effectInput.value = selectedEffect ? selectedEffect.name : 'Выбранный эффект';
                } else if (currentEffectNameInput || selectedEffectNameInput) {
                    const effectInput = currentEffectNameInput || selectedEffectNameInput;
                    effectInput.value = 'Без эффекта';
                }
                
                // Синхронизируем значения в обеих формах
                syncEffectValuesBetweenForms();
                
                // Обратная связь при выборе
                if (window.safeVibrate) {
                    window.safeVibrate(50);
                }
                
                // Обновить итоговую информацию в шаге 5
                const summaryEffect = document.getElementById('summary_effect');
                if (summaryEffect) {
                    if (window.selectedEffectId) {
                        const effectName = currentEffectNameInput?.value || selectedEffectNameInput?.value || 'Выбранный эффект';
                        summaryEffect.innerHTML = `<span class="badge bg-primary">${effectName}</span>`;
                    } else {
                        summaryEffect.innerHTML = `<span class="badge bg-secondary">Нет эффекта</span>`;
                    }
                }
            });
        }
        
        // Функция для синхронизации значений между мобильной и десктопной формами
        function syncEffectValuesBetweenForms() {
            const mobileCertForm = document.getElementById('mobileCertificateForm');
            const desktopCertForm = document.getElementById('desktopCertificateForm');
            
            if (mobileCertForm && desktopCertForm) {
                const mobileEffectIdInput = mobileCertForm.querySelector('[name="animation_effect_id"]');
                const desktopEffectIdInput = desktopCertForm.querySelector('[name="animation_effect_id"]');
                
                if (mobileEffectIdInput && desktopEffectIdInput) {
                    mobileEffectIdInput.value = window.selectedEffectId || '';
                    desktopEffectIdInput.value = window.selectedEffectId || '';
                }
            }
        }
        
        // Инициализация при открытии модального окна
        const animationModal = document.getElementById('animationEffectsModal');
        if (animationModal) {
            animationModal.addEventListener('show.bs.modal', function () {
                // Если список эффектов еще не загружен
                if (effects.length === 0) {
                    window.loadAnimationEffects();
                }
            });
            
            // Дополнительная обработка для мобильных устройств
            if (window.innerWidth < 768) {
                animationModal.addEventListener('shown.bs.modal', function() {
                    // Добавляем небольшую подсказку для мобильных устройств
                    const hintElement = document.createElement('div');
                    hintElement.classList.add('text-center', 'text-muted', 'small', 'my-2', 'mobile-hint');
                    hintElement.textContent = 'Нажмите на карточку эффекта для выбора';
                    
                    // Проверяем, нет ли уже подсказки
                    if (!document.querySelector('.mobile-hint')) {
                        const modalBody = animationModal.querySelector('.modal-body');
                        if (modalBody) {
                            modalBody.prepend(hintElement);
                        }
                    }
                });
            }
        }
    }
    
    // Инициализация всех общих компонентов
    initCommonFunctionality();
    initAnimationEffects();
});
</script>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/certificate_scripts.blade.php ENDPATH**/ ?>