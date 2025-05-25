<div class="quiz-step" id="quizStep5">
    <div class="text-center mb-4">
        <div class="quiz-step-icon mb-3 d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 p-3">
            <i class="fa-solid fa-wand-magic-sparkles text-primary fs-3"></i>
        </div>
        <h3 class="quiz-step-title">Дополнительные опции</h3>
        <p class="text-muted">Добавьте специальные эффекты и настройки для документа</p>
    </div>
    
    <!-- Выбор анимационного эффекта -->
    <div class="mb-4">
        <label class="form-label fw-medium mb-2">Анимационный эффект:</label>
        <input type="hidden" name="animation_effect_id" id="animation_effect_id" value="<?php echo e(old('animation_effect_id')); ?>">
        
        <div class="d-flex align-items-center">
            <input type="text" class="form-control form-control-lg me-2" id="selected_effect_name" 
                   placeholder="Не выбран" readonly value="<?php echo e(old('selected_effect_name')); ?>">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#animationEffectsModal">
                <i class="fa-solid fa-wand-sparkles me-2"></i>Выбрать
            </button>
        </div>
        <div class="form-text small">Выберите эффект анимации, который будет применен при просмотре документа</div>
    </div>
    
    <!-- Необязательное сообщение -->
    <div class="mb-4">
        <label for="message" class="form-label fw-medium mb-2">Сообщение или пожелание:</label>
        <textarea class="form-control form-control-lg <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
            id="message" name="message" rows="3" placeholder="Добавьте персональное сообщение или пожелание для получателя"><?php echo e(old('message')); ?></textarea>
        <?php $__errorArgs = ['message'];
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функция для обновления данных на итоговом шаге
    function updateSummaryData() {
        // Проверяем, выбран ли анимационный эффект
        const effectId = document.getElementById('animation_effect_id').value;
        const effectName = document.getElementById('selected_effect_name').value;
        
        // Обновляем данные на итоговом шаге если он есть
        const summaryAnimationEffect = document.getElementById('summary_animation_effect');
        if (summaryAnimationEffect) {
            if (effectId && effectName) {
                summaryAnimationEffect.innerHTML = `<span class="badge bg-primary">${effectName}</span>`;
            } else {
                summaryAnimationEffect.innerHTML = `<span class="badge bg-light text-dark">Не выбран</span>`;
            }
        }
        
        // Сохраняем значение в глобальной переменной для синхронизации
        if (window && typeof effectId !== 'undefined') {
            window.selectedEffectId = effectId ? parseInt(effectId) : null;
        }
    }
    
    // Проверяем, есть ли сохраненный выбор эффекта при загрузке страницы
    const savedEffectId = document.getElementById('animation_effect_id').value;
    if (savedEffectId) {
        // Если ID эффекта уже есть, сохраняем его в глобальной переменной
        window.selectedEffectId = parseInt(savedEffectId);
        
        // Если название не заполнено, загрузим его
        if (!document.getElementById('selected_effect_name').value) {
            fetch(`/api/animation-effects?id=${savedEffectId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.effects && data.effects.length > 0) {
                        document.getElementById('selected_effect_name').value = data.effects[0].name;
                        updateSummaryData();
                    }
                })
                .catch(error => console.error('Ошибка при загрузке данных эффекта:', error));
        }
    }
    
    // Обновляем итоговые данные при изменении эффекта
    document.getElementById('animation_effect_id').addEventListener('change', function() {
        console.log('ID эффекта изменен на: ', this.value);
        updateSummaryData();
    });
    
    // Добавляем проверку перед отправкой формы
    const forms = document.querySelectorAll('#mobileCertificateForm, #desktopCertificateForm');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            // Проверяем, установлено ли значение animation_effect_id если выбран эффект
            const effectNameInput = this.querySelector('#selected_effect_name');
            const effectIdInput = this.querySelector('#animation_effect_id');
            
            if (effectNameInput && effectNameInput.value && effectIdInput) {
                if (!effectIdInput.value && window.selectedEffectId) {
                    effectIdInput.value = window.selectedEffectId;
                    console.log('ID эффекта установлен перед отправкой:', effectIdInput.value);
                }
            }
        });
    });
});
</script>

<style>
/* Стили для модального окна выбора эффектов добавлены в animation_effects_modal.blade.php */
</style>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/quiz_steps/step5_extras.blade.php ENDPATH**/ ?>