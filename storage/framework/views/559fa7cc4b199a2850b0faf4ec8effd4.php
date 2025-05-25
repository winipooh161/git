<div class="quiz-step" id="quizStep3">
    <div class="text-center mb-4">
        <div class="quiz-step-icon mb-3 d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 p-3">
            <i class="fa-solid fa-calendar-alt text-primary fs-3"></i>
        </div>
        <h3 class="quiz-step-title">Срок действия</h3>
        <p class="text-muted">Выберите, до какой даты будет действителен документ</p>
    </div>
    
    <input type="hidden" name="valid_from" id="valid_from" value="<?php echo e(now()->format('Y-m-d')); ?>">
    
    <!-- Выбор длительности через предустановленные варианты -->
    <div class="mb-4">
        <label class="form-label fw-medium mb-2">Выберите длительность:</label>
        <div class="row gap-2 mx-0">
            <button type="button" class="col btn btn-outline-secondary duration-btn" data-duration="30">1 месяц</button>
            <button type="button" class="col btn btn-outline-secondary duration-btn" data-duration="90">3 месяца</button>
            <button type="button" class="col btn btn-outline-secondary duration-btn" data-duration="180">6 месяцев</button>
        </div>
        <div class="row gap-2 mx-0 mt-2">
            <button type="button" class="col btn btn-outline-secondary duration-btn" data-duration="365">1 год</button>
            <button type="button" class="col btn btn-outline-secondary duration-btn" data-duration="custom">Другой срок</button>
        </div>
    </div>
    
    <!-- Ручной выбор даты (скрыт по умолчанию) -->
    <div id="custom_date_selector" class="mb-3 mt-3 d-none">
        <label for="valid_until" class="form-label fw-medium mb-2">Действует до: *</label>
        <input type="date" class="form-control form-control-lg <?php $__errorArgs = ['valid_until'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
            id="valid_until" name="valid_until" 
            value="<?php echo e(old('valid_until', now()->addMonths(3)->format('Y-m-d'))); ?>" 
            min="<?php echo e(now()->format('Y-m-d')); ?>" required>
        <?php $__errorArgs = ['valid_until'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    
    <!-- Показ выбранной даты в красивом формате -->
    <div class="card bg-light mt-3">
        <div class="card-body text-center">
            <h6 class="card-subtitle mb-2 text-muted">Документ будет действовать до:</h6>
            <h5 class="card-title" id="displayValidUntil"><?php echo e(now()->addMonths(3)->format('d.m.Y')); ?></h5>
            <p class="card-text small" id="daysRemaining">Осталось <?php echo e(now()->addMonths(3)->diffInDays(now())); ?> дней</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализируем глобальный объект для отслеживания состояния форм
    window.certificateFormState = window.certificateFormState || {};
    
    // Находим элементы интерфейса
    const durationButtons = document.querySelectorAll('.duration-btn');
    const validUntilInput = document.getElementById('valid_until');
    const customDateSelector = document.getElementById('custom_date_selector');
    const displayValidUntil = document.getElementById('displayValidUntil');
    const daysRemaining = document.getElementById('daysRemaining');
    
    // Инициализируем глобальное состояние срока действия
    window.certificateFormState.duration = window.certificateFormState.duration || 90;
    
    // Функция для обновления отображения даты
    function updateDisplayDate(date) {
        if (!date) return;
        
        // Проверяем, является ли date строкой даты или объектом Date
        const displayDate = typeof date === 'string' ? new Date(date) : date;
        
        // Форматируем дату для отображения
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        try {
            displayValidUntil.textContent = displayDate.toLocaleDateString('ru-RU', options);
            
            // Вычисляем разницу в днях
            const today = new Date();
            const diffTime = Math.abs(displayDate - today);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            daysRemaining.textContent = `Осталось ${diffDays} дней`;
            
            // Синхронизируем с десктопной версией формы
            syncWithDesktopForm(validUntilInput.value);
        } catch (error) {
            console.error('Ошибка при форматировании даты:', error, date);
        }
    }
    
    // Функция для синхронизации с десктопной формой
    function syncWithDesktopForm(dateValue) {
        const desktopForm = document.getElementById('desktopCertificateForm');
        if (desktopForm) {
            const desktopDateInput = desktopForm.querySelector('[name="valid_until"]');
            if (desktopDateInput && desktopDateInput.value !== dateValue) {
                console.log('Синхронизация с десктопной формой:', dateValue);
                desktopDateInput.value = dateValue;
                
                // Сохраняем глобальное состояние
                window.certificateFormState.validUntil = dateValue;
                window.certificateFormState.duration = calculateDuration(dateValue);
                
                // Вызываем событие изменения для обновления всего, что зависит от этого поля
                const event = new Event('change', { bubbles: true });
                desktopDateInput.dispatchEvent(event);
            }
        }
    }
    
    // Функция для вычисления примерной длительности в днях от текущей даты
    function calculateDuration(dateValue) {
        try {
            const targetDate = new Date(dateValue);
            const today = new Date();
            const diffTime = Math.abs(targetDate - today);
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        } catch (error) {
            console.error('Ошибка при вычислении длительности:', error);
            return 90; // значение по умолчанию - 3 месяца
        }
    }
    
    // Обработчики для кнопок длительности
    durationButtons.forEach(button => {
        button.addEventListener('click', function() {
            const duration = this.getAttribute('data-duration');
            
            // Сбрасываем активное состояние у всех кнопок и добавляем к текущей
            durationButtons.forEach(btn => btn.classList.remove('active', 'btn-primary', 'text-white'));
            this.classList.add('active', 'btn-primary', 'text-white');
            
            if (duration === 'custom') {
                // Показываем селектор даты
                customDateSelector.classList.remove('d-none');
                validUntilInput.focus();
                return;
            } else {
                // Скрываем селектор даты
                customDateSelector.classList.add('d-none');
                
                // Устанавливаем срок действия документа на указанное количество дней
                const days = parseInt(duration);
                
                // Сохраняем выбранную длительность глобально
                window.certificateFormState.duration = days;
                
                // Расчет новой даты
                const today = new Date();
                const validUntil = new Date(today);
                validUntil.setDate(today.getDate() + days);
                
                // Форматируем дату для input в формате YYYY-MM-DD
                const year = validUntil.getFullYear();
                const month = String(validUntil.getMonth() + 1).padStart(2, '0');
                const day = String(validUntil.getDate()).padStart(2, '0');
                const formattedDate = `${year}-${month}-${day}`;
                
                // Устанавливаем значение в поле формы
                validUntilInput.value = formattedDate;
                
                // Обновляем состояние формы
                window.certificateFormState.validUntil = formattedDate;
                
                // Обновляем отображение и синхронизируем с другой формой
                updateDisplayDate(validUntil);
                
                console.log(`Установлена длительность: ${days} дней, дата: ${formattedDate}`);
            }
            
            // Эффект вибрации для обратной связи
            if (window.safeVibrate) window.safeVibrate(30);
        });
    });
    
    // Обработчик изменения даты при ручном вводе
    validUntilInput.addEventListener('change', function() {
        console.log('Ручное изменение даты окончания:', this.value);
        
        // Сохраняем новое значение в глобальное состояние
        window.certificateFormState.validUntil = this.value;
        window.certificateFormState.duration = calculateDuration(this.value);
        
        // Обновляем интерфейс
        updateDisplayDate(this.value);
    });
    
    // Функция инициализации с восстановлением сохраненного состояния
    function initDateValidity() {
        // Проверяем, есть ли сохраненное значение
        if (window.certificateFormState.validUntil) {
            validUntilInput.value = window.certificateFormState.validUntil;
            updateDisplayDate(window.certificateFormState.validUntil);
            
            // Восстанавливаем активную кнопку
            const duration = window.certificateFormState.duration;
            const durationButton = document.querySelector(`.duration-btn[data-duration="${duration}"]`);
            
            if (durationButton) {
                durationButtons.forEach(btn => btn.classList.remove('active', 'btn-primary', 'text-white'));
                durationButton.classList.add('active', 'btn-primary', 'text-white');
            } else {
                // Если не нашли кнопку, возможно это пользовательская дата
                const customButton = document.querySelector('.duration-btn[data-duration="custom"]');
                if (customButton) {
                    durationButtons.forEach(btn => btn.classList.remove('active', 'btn-primary', 'text-white'));
                    customButton.classList.add('active', 'btn-primary', 'text-white');
                    customDateSelector.classList.remove('d-none');
                }
            }
        } else {
            // По умолчанию выбираем 3 месяца (90 дней)
            const defaultButton = document.querySelector('.duration-btn[data-duration="90"]');
            if (defaultButton) defaultButton.click();
        }
    }
    
    // Обработчик для синхронизации с десктопной формой
    function listenForDesktopChanges() {
        const desktopForm = document.getElementById('desktopCertificateForm');
        if (desktopForm) {
            const desktopDateInput = desktopForm.querySelector('[name="valid_until"]');
            if (desktopDateInput) {
                desktopDateInput.addEventListener('change', function() {
                    if (this.value !== validUntilInput.value) {
                        console.log('Синхронизация с мобильной формой:', this.value);
                        validUntilInput.value = this.value;
                        updateDisplayDate(this.value);
                        
                        // Определяем примерную длительность для выбора соответствующей кнопки
                        const duration = calculateDuration(this.value);
                        window.certificateFormState.duration = duration;
                        
                        // Пытаемся найти ближайшую кнопку для активации
                        const durationOptions = [30, 90, 180, 365];
                        const closestDuration = durationOptions.reduce((prev, curr) => 
                            Math.abs(curr - duration) < Math.abs(prev - duration) ? curr : prev
                        );
                        
                        // Активируем ближайшую кнопку, если разница не более 5 дней
                        if (Math.abs(closestDuration - duration) <= 5) {
                            const button = document.querySelector(`.duration-btn[data-duration="${closestDuration}"]`);
                            if (button) {
                                durationButtons.forEach(btn => btn.classList.remove('active', 'btn-primary', 'text-white'));
                                button.classList.add('active', 'btn-primary', 'text-white');
                                customDateSelector.classList.add('d-none');
                            }
                        } else {
                            // Иначе активируем кнопку "Другой срок"
                            const customButton = document.querySelector('.duration-btn[data-duration="custom"]');
                            if (customButton) {
                                durationButtons.forEach(btn => btn.classList.remove('active', 'btn-primary', 'text-white'));
                                customButton.classList.add('active', 'btn-primary', 'text-white');
                                customDateSelector.classList.remove('d-none');
                            }
                        }
                    }
                });
            }
        }
    }
    
    // Инициализируем компонент после загрузки DOM
    initDateValidity();
    listenForDesktopChanges();
});
</script>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/quiz_steps/step3_validity.blade.php ENDPATH**/ ?>