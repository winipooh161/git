<div class="card border-0 shadow-sm rounded-4 certificate-quiz-container">
    <div class="card-header bg-transparent border-0 pt-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="fw-bold mb-0 fs-6">Создание документа</h5>
            <span class="quiz-progress">Шаг <span id="currentStep">1</span> из <span id="totalSteps">5</span></span>
        </div>
        
        <!-- Индикатор прогресса -->
        <div class="progress" style="height: 5px;">
            <div class="progress-bar" id="quizProgressBar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
    
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('entrepreneur.certificates.store', $template)); ?>" id="mobileCertificateForm" class="certificate-form" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <!-- Контейнер для шагов квиза -->
            <div class="quiz-steps-container">
                <!-- Шаг 1: Обложка документа -->
                <?php echo $__env->make('entrepreneur.certificates.partials.quiz_steps.step4_cover', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                
                <!-- Шаг 2: Номинал документа -->
                <?php echo $__env->make('entrepreneur.certificates.partials.quiz_steps.step1_amount', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                
                <!-- Шаг 3: Срок действия -->
                <?php echo $__env->make('entrepreneur.certificates.partials.quiz_steps.step3_validity', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                
                <!-- Шаг 4: Анимация и дополнительные функции -->
                <?php echo $__env->make('entrepreneur.certificates.partials.quiz_steps.step5_extras', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                
                <!-- Шаг 5: Информация о получателе -->
                <?php echo $__env->make('entrepreneur.certificates.partials.quiz_steps.step2_recipient', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            <!-- Кнопки навигации -->
            <div class="quiz-navigation mt-4">
                <div class="row">
                    <div class="col">
                        <button type="button" class="btn btn-outline-secondary w-100" id="prevStepBtn" disabled>
                            <i class="fa-solid fa-chevron-left me-2"></i>Назад
                        </button>
                    </div>
                    <div class="col">
                        <button type="button" class="btn btn-primary w-100" id="nextStepBtn">
                            Далее<i class="fa-solid fa-chevron-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success w-100 d-none" id="submitQuizBtn">
                            <i class="fa-solid fa-check me-2"></i>Создать 
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.certificate-quiz-container {
    position: relative;
    border-radius: 1rem;
    height: 100%;
}

.quiz-progress {
    font-size: 0.8rem;
    color: #6c757d;
}

.quiz-step {
    display: none;
}

.quiz-step.active {
    display: block;
    animation: fadeIn 0.3s ease-in-out;
}

.quiz-step-title {
    font-weight: 600;
    margin-bottom: 1rem;
    color: #0d6efd;
}

.quiz-navigation {
    margin-top: 1.5rem;
}

/* Адаптивные стили для квиза на разных экранах */
@media (min-width: 992px) {
    .certificate-quiz-container {
        max-height: calc(100vh - 140px);
        overflow-y: auto;
    }
    
    .quiz-step {
        padding: 0.5rem 1rem;
    }
    
    .quiz-step-title {
        font-size: 1.4rem;
    }
    
    .form-control-lg {
        padding: 0.65rem 1rem;
        font-size: 1rem;
    }
    
    .btn {
        padding: 0.5rem 1rem;
    }
}

/* Анимация для плавного появления шагов */
@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Элементы квиза
    const quizSteps = document.querySelectorAll('.quiz-step');
    const prevBtn = document.getElementById('prevStepBtn');
    const nextBtn = document.getElementById('nextStepBtn');
    const submitBtn = document.getElementById('submitQuizBtn');
    const currentStepIndicator = document.getElementById('currentStep');
    const progressBar = document.getElementById('quizProgressBar');
    const quizForm = document.getElementById('mobileCertificateForm');
    
    let currentStep = 0;
    const totalSteps = quizSteps.length;
    
    // Инициализация квиза
    function initQuiz() {
        // Показываем первый шаг
        showStep(currentStep);
        
        // Обновляем общее количество шагов в индикаторе
        document.getElementById('totalSteps').textContent = totalSteps;
    }
    
    // Функция для отображения определенного шага
    function showStep(stepIndex) {
        // Скрываем все шаги
        quizSteps.forEach(step => {
            step.classList.remove('active');
        });
        
        // Показываем нужный шаг
        quizSteps[stepIndex].classList.add('active');
        
        // Обновляем индикатор текущего шага
        currentStepIndicator.textContent = stepIndex + 1;
        
        // Обновляем прогресс-бар
        const progressPercentage = ((stepIndex + 1) / totalSteps) * 100;
        progressBar.style.width = `${progressPercentage}%`;
        progressBar.setAttribute('aria-valuenow', progressPercentage);
        
        // Управление состоянием кнопок
        prevBtn.disabled = stepIndex === 0;
        
        // На последнем шаге показываем кнопку отправки формы
        if (stepIndex === totalSteps - 1) {
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
        } else {
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
        }
        
        // На десктопных устройствах не нужно прокручивать страницу
        if (window.innerWidth < 992) {
            // Прокручиваем страницу вверх для лучшего UX
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        } else {
            // В десктопной версии прокручиваем только контейнер квиза
            const quizContainer = document.querySelector('.certificate-quiz-container');
            if (quizContainer) {
                quizContainer.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }
        
        // Эффект вибрации при переходе к следующему шагу (только если устройство поддерживает)
        if (window.safeVibrate && stepIndex > 0) {
            window.safeVibrate(50);
        }
        
        // Обновляем предпросмотр документа
        if (window.updatePreview && typeof window.updatePreview === 'function') {
            setTimeout(window.updatePreview, 100);
        }
    }
    
    // Обновленная функция проверки перед переходом к следующему шагу
    function nextStep() {
        // Получаем текущий шаг
        const currentQuizStep = quizSteps[currentStep];
        const requiredFields = currentQuizStep.querySelectorAll('[required]');
        let isValid = true;
        
        // Удаляем старые сообщения об ошибках
        const oldErrorMessages = currentQuizStep.querySelectorAll('.quiz-error, .feedback-text');
        oldErrorMessages.forEach(msg => msg.remove());
        
        // Особая обработка для первого шага (загрузка обложки)
        if (currentStep === 0) {
            // Проверяем наличие загруженной обложки из фоторедактора
            const hasTempCover = document.querySelector('input[name="temp_cover_path"]')?.value;
            
            // Если обложка не загружена, считаем шаг невалидным
            if (!hasTempCover) {
                isValid = false;
                
                // Показываем сообщение об ошибке
                const errorMsg = document.createElement('div');
                errorMsg.classList.add('alert', 'alert-danger', 'mt-3', 'mb-0', 'py-2', 'quiz-error');
                errorMsg.innerHTML = '<i class="fa-solid fa-exclamation-circle me-2"></i>Пожалуйста, создайте обложку документа в фоторедакторе';
                currentQuizStep.appendChild(errorMsg);
                
                // Прокрутка к сообщению об ошибке
                errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Вибрация для обратной связи
                if (window.safeVibrate) {
                    window.safeVibrate([30, 50, 30]);
                }
                
                return; // Останавливаем переход
            }
            
            // Проверяем необходимость загрузки логотипа
            const logoType = document.querySelector('input[name="logo_type"]:checked')?.value;
            if (logoType === 'custom') {
                const customLogo = document.getElementById('custom_logo');
                const customLogoUploaded = customLogo?.files?.length > 0 || customLogo?.getAttribute('data-file-uploaded') === 'true';
                
                if (!customLogoUploaded) {
                    isValid = false;
                    
                    // Находим контейнер логотипа
                    const logoContainer = document.getElementById('custom_logo_container');
                    if (logoContainer) {
                        const errorMsg = document.createElement('div');
                        errorMsg.classList.add('text-danger', 'small', 'mt-2');
                        errorMsg.textContent = 'Необходимо загрузить логотип';
                        logoContainer.appendChild(errorMsg);
                    }
                }
            }
        }
        
        // Стандартная проверка обязательных полей
        requiredFields.forEach(field => {
            // Пропускаем скрытые поля (кроме file inputs, которые могут быть скрыты)
            if (field.offsetParent === null && field.type !== 'file') {
                return;
            }
            
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                
                // Добавляем сообщение об ошибке под полем, если его еще нет
                const fieldParent = field.closest('.form-group') || field.parentElement;
                if (!fieldParent.querySelector('.invalid-feedback')) {
                    const feedbackDiv = document.createElement('div');
                    feedbackDiv.classList.add('invalid-feedback');
                    feedbackDiv.style.display = 'block';
                    feedbackDiv.textContent = `Пожалуйста, заполните это поле`;
                    field.after(feedbackDiv);
                }
            } else {
                field.classList.remove('is-invalid');
                
                // Удаляем сообщение об ошибке, если оно есть
                const fieldParent = field.closest('.form-group') || field.parentElement;
                const feedback = fieldParent.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            }
        });
        
        if (!isValid) {
            // Показываем общее сообщение об ошибке, если оно ещё не было добавлено
            if (!currentQuizStep.querySelector('.quiz-error')) {
                const errorMsg = document.createElement('div');
                errorMsg.classList.add('alert', 'alert-danger', 'mt-3', 'mb-0', 'py-2', 'quiz-error');
                errorMsg.innerHTML = '<i class="fa-solid fa-exclamation-circle me-2"></i>Пожалуйста, заполните все обязательные поля';
                currentQuizStep.appendChild(errorMsg);
            }
            
            // Вибрация для обратной связи об ошибке
            if (window.safeVibrate) {
                window.safeVibrate([30, 50, 30]);
            }
            
            // Прокрутка к первому невалидному полю
            const firstInvalidField = currentQuizStep.querySelector('.is-invalid');
            if (firstInvalidField) {
                firstInvalidField.focus();
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            return; // Останавливаем переход к следующему шагу
        }
        
        // Если валидация прошла успешно, переходим к следующему шагу
        if (currentStep < totalSteps - 1) {
            currentStep++;
            showStep(currentStep);
            
            // Показываем краткое уведомление об успешном прохождении шага
            const successToast = document.createElement('div');
            successToast.className = 'position-fixed bottom-0 end-0 p-3';
            successToast.style.zIndex = 9999;
            successToast.innerHTML = `
                <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fa-solid fa-check-circle me-2"></i>Шаг ${currentStep} из ${totalSteps}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            document.body.appendChild(successToast);
            const toast = new bootstrap.Toast(successToast.querySelector('.toast'), {delay: 3000});
            toast.show();
            
            // Удалим элемент после скрытия
            successToast.addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }
    }
    
    // Переход к предыдущему шагу
    function prevStep() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    }
    
    // Назначение обработчиков событий для кнопок
    nextBtn.addEventListener('click', nextStep);
    prevBtn.addEventListener('click', prevStep);
    
    // Инициализация при загрузке страницы
    initQuiz();
    
    // Обработка отправки формы с улучшенной синхронизацией дат
    quizForm.addEventListener('submit', function(e) {
        // Предотвращаем стандартную отправку для валидации
        e.preventDefault();
        
        console.log('Начинается итоговая валидация формы...');
        
        // Проверка обязательных полей на всех шагах
        let isAllValid = true;
        let invalidStep = -1;
        let firstInvalidField = null;
        let missingFields = [];
        
        // Проверяем все шаги на валидность
        quizSteps.forEach((step, index) => {
            const requiredFields = step.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                // Проверяем, не скрыто ли поле
                if (field.offsetParent === null && field.type !== 'file') {
                    return;
                }
                
                if (!field.value.trim()) {
                    isAllValid = false;
                    field.classList.add('is-invalid');
                    
                    if (invalidStep === -1) {
                        invalidStep = index;
                        firstInvalidField = field;
                    }
                    
                    // Добавляем информацию о невалидном поле
                    const fieldLabel = field.closest('.mb-3')?.querySelector('.form-label')?.textContent || field.name;
                    missingFields.push(`${fieldLabel.trim()} (шаг ${index + 1})`);
                }
            });
        });
        
        // Проверка наличия обложки - самая важная проверка
        const hasTempCover = document.querySelector('input[name="temp_cover_path"]')?.value;
        if (!hasTempCover) {
            isAllValid = false;
            invalidStep = 0; // Первый шаг
            missingFields.unshift('Обложка документа (шаг 1)');
        }
        
        // Если есть ошибки, переходим к первому невалидному шагу и показываем сообщения
        if (!isAllValid) {
            // Переходим к шагу с первой ошибкой
            currentStep = invalidStep;
            showStep(currentStep);
            
            // Показываем детальное сообщение об ошибках
            const activeStep = quizSteps[currentStep];
            const errorMsg = document.createElement('div');
            errorMsg.classList.add('alert', 'alert-danger', 'mt-3', 'py-2');
            
            // Если полей много, показываем сокращенное сообщение
            if (missingFields.length > 3) {
                errorMsg.innerHTML = `<i class="fa-solid fa-exclamation-circle me-2"></i>Пожалуйста, заполните обязательные поля (всего ${missingFields.length})`;
            } else {
                errorMsg.innerHTML = `<i class="fa-solid fa-exclamation-circle me-2"></i>Необходимо заполнить:<br>`;
                errorMsg.innerHTML += `<ul class="mb-0 mt-1">
                    ${missingFields.map(field => `<li>${field}</li>`).join('')}
                </ul>`;
            }
            
            activeStep.appendChild(errorMsg);
            errorMsg.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Прокрутка к первому невалидному полю
            if (firstInvalidField) {
                setTimeout(() => {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 500);
            }
            
            // Вибрация для обратной связи
            if (window.safeVibrate) {
                window.safeVibrate([30, 50, 30, 50, 30]);
            }
            
            return false;
        }
        
        // Дополнительная проверка на корректность дат valid_from и valid_until
        const validFromValue = document.querySelector('input[name="valid_from"]').value;
        const validUntilValue = document.querySelector('input[name="valid_until"]').value;
        
        if (validUntilValue) {
            const validUntilDate = new Date(validUntilValue);
            const today = new Date();
            
            // Проверяем, что дата окончания не в прошлом
            if (validUntilDate <= today) {
                console.error('Дата окончания действия не может быть в прошлом');
                alert('Дата окончания действия не может быть в прошлом. Пожалуйста, выберите корректную дату.');
                return false;
            }
        }
        
        console.log('Валидация успешна, отправляем форму...');
        
        // Форма валидна, отправляем её
        this.submit();
    });
});
</script>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/certificate_quiz.blade.php ENDPATH**/ ?>