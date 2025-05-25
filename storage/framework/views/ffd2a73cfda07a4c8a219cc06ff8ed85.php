<!-- Обложка документа -->
<div class="mb-2 mb-sm-3">
    <label for="cover_image" class="form-label small fw-bold">Обложка документа *</label>
    <input type="file" class="form-control form-control-sm <?php $__errorArgs = ['cover_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
        id="cover_image" name="cover_image" accept="image/*" required>
    <?php $__errorArgs = ['cover_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="invalid-feedback"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <div class="form-text small">Загрузите изображение для карточки документа. Рекомендуемый размер: 500x300px</div>
    
    <div id="cover_image_preview" class="mt-2 text-center"></div>
</div>

<!-- Логотип компании -->
<div class="mb-2 mb-sm-3">
    <label for="logo" class="form-label small fw-bold">Логотип компании</label>
    <div class="mb-2">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="logo_type" id="logo_default" value="default" checked>
            <label class="form-check-label small" for="logo_default">
                Использовать из профиля
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="logo_type" id="logo_custom" value="custom">
            <label class="form-check-label small" for="logo_custom">
                Загрузить новый
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="logo_type" id="logo_none" value="none">
            <label class="form-check-label small" for="logo_none">
                Не использовать логотип
            </label>
        </div>
    </div>
    
    <div id="default_logo_preview" class="mb-2 text-center p-2 border rounded">
        <img src="<?php echo e(Auth::user()->company_logo ? asset('storage/' . Auth::user()->company_logo) : asset('images/default-logo.png')); ?>" 
            class="img-thumbnail" style="max-height: 60px;" alt="Текущий логотип">
        <div class="small text-muted mt-1 fs-7">Текущий логотип</div>
    </div>
    
    <div id="custom_logo_container" class="d-none">
        <input type="file" class="form-control form-control-sm <?php $__errorArgs = ['custom_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
            id="custom_logo" name="custom_logo" accept="image/*">
        <div class="form-text small">Рекомендуемый размер: 300x100px, PNG или JPG</div>
        
        <div id="custom_logo_preview" class="mt-2 text-center"></div>
    </div>
    
    <?php $__errorArgs = ['custom_logo'];
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
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/form_tabs/visual_tab.blade.php ENDPATH**/ ?>