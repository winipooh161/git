<div class="quiz-step" id="quizStep1">
    <div class="text-center mb-4">
        <div class="quiz-step-icon mb-3 d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 p-3">
            <i class="fa-solid fa-money-bill-wave text-primary fs-3"></i>
        </div>
        <h3 class="quiz-step-title">Номинал документа</h3>
        <p class="text-muted">Выберите тип номинала и укажите сумму</p>
    </div>
    
    <div class="mb-4">
   
        <div class="btn-group d-flex mb-3" role="group">
            <input type="radio" class="btn-check" name="amount_type" id="quiz_amount_type_money" value="money" checked>
            <label class="btn btn-outline-primary" for="quiz_amount_type_money">Денежный</label>
            
            <input type="radio" class="btn-check" name="amount_type" id="quiz_amount_type_percent" value="percent">
            <label class="btn btn-outline-primary" for="quiz_amount_type_percent">Процентный</label>
        </div>
    </div>
    
    <!-- Денежный номинал (показывается по умолчанию) -->
    <div id="quiz_money_amount_block" class="amount-block">
        <label for="quiz_amount" class="form-label fw-medium mb-2">Сумма документа:</label>
        <div class="input-group mb-3">
            <input type="number" class="form-control form-control-lg <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                id="quiz_amount" name="amount" value="<?php echo e(old('amount', 3000)); ?>" 
                min="100" step="100" required>
            <span class="input-group-text">₽</span>
        </div>
        <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        
        <div class="quick-values d-flex flex-wrap justify-content-between gap-2 mt-3">
            <button type="button" class="btn btn-outline-secondary flex-grow-1 quick-amount-btn" data-amount="1000">1 000 ₽</button>
            <button type="button" class="btn btn-outline-secondary flex-grow-1 quick-amount-btn" data-amount="3000">3 000 ₽</button>
            <button type="button" class="btn btn-outline-secondary flex-grow-1 quick-amount-btn" data-amount="5000">5 000 ₽</button>
            <button type="button" class="btn btn-outline-secondary flex-grow-1 quick-amount-btn" data-amount="10000">10 000 ₽</button>
        </div>
    </div>
    
    <!-- Процентный номинал (скрыт по умолчанию) -->
    <div id="quiz_percent_amount_block" class="amount-block d-none">
        <label for="quiz_percent_value" class="form-label fw-medium mb-2">Процент скидки:</label>
        <div class="input-group mb-3">
            <input type="number" class="form-control form-control-lg" 
                id="quiz_percent_value" name="percent_value" value="<?php echo e(old('percent_value', 10)); ?>" 
                min="1" max="100" step="1">
            <span class="input-group-text">%</span>
        </div>
        <?php $__errorArgs = ['percent_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        
        <div class="quick-values d-flex flex-wrap justify-content-between gap-2 mt-3">
            <button type="button" class="btn btn-outline-secondary flex-grow-1 quick-percent-btn" data-percent="5">5%</button>
            <button type="button" class="btn btn-outline-secondary flex-grow-1 quick-percent-btn" data-percent="10">10%</button>
            <button type="button" class="btn btn-outline-secondary flex-grow-1 quick-percent-btn" data-percent="15">15%</button>
            <button type="button" class="btn btn-outline-secondary flex-grow-1 quick-percent-btn" data-percent="20">20%</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключение между денежным и процентным номиналом
    const amountTypeRadios = document.querySelectorAll('input[name="amount_type"]');
    const quizMoneyBlock = document.getElementById('quiz_money_amount_block');
    const quizPercentBlock = document.getElementById('quiz_percent_amount_block');
    
    amountTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'money') {
                quizMoneyBlock.classList.remove('d-none');
                quizPercentBlock.classList.add('d-none');
                document.getElementById('quiz_amount').required = true;
                document.getElementById('quiz_percent_value').required = false;
            } else {
                quizMoneyBlock.classList.add('d-none');
                quizPercentBlock.classList.remove('d-none');
                document.getElementById('quiz_amount').required = false;
                document.getElementById('quiz_percent_value').required = true;
            }
        });
    });
    
    // Быстрый выбор суммы
    const quickAmountButtons = document.querySelectorAll('.quick-amount-btn');
    quickAmountButtons.forEach(button => {
        button.addEventListener('click', function() {
            const amount = this.getAttribute('data-amount');
            document.getElementById('quiz_amount').value = amount;
            
            // Убираем активный класс у всех кнопок и добавляем к текущей
            quickAmountButtons.forEach(btn => btn.classList.remove('active', 'btn-primary', 'text-white'));
            this.classList.add('active', 'btn-primary', 'text-white');
            
            // Вызываем обновление предпросмотра
            if (window.updatePreview && typeof window.updatePreview === 'function') {
                window.updatePreview();
            }
            
            // Эффект вибрации для обратной связи
            if (window.safeVibrate) {
                window.safeVibrate(30);
            }
        });
    });
    
    // Быстрый выбор процента
    const quickPercentButtons = document.querySelectorAll('.quick-percent-btn');
    quickPercentButtons.forEach(button => {
        button.addEventListener('click', function() {
            const percent = this.getAttribute('data-percent');
            document.getElementById('quiz_percent_value').value = percent;
            
            // Убираем активный класс у всех кнопок и добавляем к текущей
            quickPercentButtons.forEach(btn => btn.classList.remove('active', 'btn-primary', 'text-white'));
            this.classList.add('active', 'btn-primary', 'text-white');
            
            // Вызываем обновление предпросмотра
            if (window.updatePreview && typeof window.updatePreview === 'function') {
                window.updatePreview();
            }
            
            // Эффект вибрации для обратной связи
            if (window.safeVibrate) {
                window.safeVibrate(30);
            }
        });
    });
});
</script>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/quiz_steps/step1_amount.blade.php ENDPATH**/ ?>