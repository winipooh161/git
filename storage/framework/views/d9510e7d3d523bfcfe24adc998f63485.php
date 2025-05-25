<!-- Сообщение -->
<div class="mb-2 mb-sm-3">
    <label for="message" class="form-label small fw-bold">Сообщение или пожелание</label>
    <textarea class="form-control form-control-sm <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
        id="message" name="message" rows="3"><?php echo e(old('message')); ?></textarea>
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
    <div class="form-text small">Добавьте персональное сообщение или пожелание для получателя</div>
</div>

<!-- Выбор анимационного эффекта -->
<div class="mb-2 mb-sm-3">
    <label for="animation_effect_id" class="form-label small fw-bold">Анимационный эффект</label>
    <div class="input-group">
        <input type="hidden" name="animation_effect_id" id="animation_effect_id" value="<?php echo e(old('animation_effect_id')); ?>">
        <input type="text" class="form-control form-control-sm" id="selected_effect_name" placeholder="Не выбран" readonly>
        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#animationEffectsModal">
            <i class="fa-solid fa-wand-sparkles me-1"></i>Выбрать
        </button>
    </div>
    <div class="form-text small">Выберите анимационный эффект, который будет отображаться при просмотре документа</div>
</div>

<!-- Место для дополнительных настроек -->
<div class="alert alert-info py-2 small">
    <i class="fa-solid fa-info-circle me-1"></i>
    Совет: вы сможете распечатать документ после его создания
</div>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/form_tabs/advanced_tab.blade.php ENDPATH**/ ?>