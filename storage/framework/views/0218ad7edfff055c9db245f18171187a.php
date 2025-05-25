<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Панель администратора</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.templates.index')); ?>">Шаблоны документов</a></li>
            <li class="breadcrumb-item active" aria-current="page">Создание шаблона</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Создание нового шаблона документа</h1>
    
    <!-- Отображение ошибок -->
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <!-- Отображение общей ошибки из сессии -->
    <?php if(session('error')): ?>
        <div class="alert alert-danger">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('admin.templates.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                
                <div class="row g-4">
                    <!-- Основная информация -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Основная информация</h4>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Название шаблона *</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="name" name="name" value="<?php echo e(old('name')); ?>" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Категория шаблона *</label>
                            <select class="form-select <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="category_id" name="category_id" required>
                                <option value="">-- Выберите категорию --</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id') == $category->id ? 'selected' : ''); ?>>
                                    <?php echo e($category->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="description" name="description" rows="3"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Изображение предпросмотра</label>
                            <input type="file" class="form-control <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="image" name="image" accept="image/*">
                            <div class="form-text">Рекомендуемый размер: 800x600px, максимум 7MB.</div>
                            <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Порядок сортировки</label>
                            <input type="number" class="form-control <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="sort_order" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" min="0" step="1">
                            <div class="form-text">Чем меньше число, тем выше в списке будет отображаться шаблон.</div>
                            <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_premium" name="is_premium" 
                                    <?php echo e(old('is_premium') ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_premium">Премиум шаблон</label>
                            </div>
                            <div class="form-text">Премиум шаблоны доступны только клиентам с премиум подпиской.</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                    <?php echo e(old('is_active', '1') == '1' ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_active">Активен</label>
                            </div>
                            <div class="form-text">Неактивные шаблоны не будут показываться клиентам.</div>
                        </div>
                    </div>
                    
                    <!-- Выбор HTML файла шаблона -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Файл шаблона</h4>
                        
                        <div class="mb-3">
                            <label for="template_path" class="form-label">Выберите PHP файл шаблона *</label>
                            <select class="form-select select2-template-picker <?php $__errorArgs = ['template_path'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="template_path" name="template_path" required
                                data-placeholder="Поиск шаблона..." data-allow-clear="true">
                                <option value="">-- Выберите файл шаблона --</option>
                                <?php $__currentLoopData = $templateFiles['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryId => $files): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php 
                                        $category = $templateFiles['categories']->firstWhere('id', $categoryId);
                                    ?>
                                    <optgroup label="<?php echo e($category ? $category->name : 'Другие'); ?>" data-category="<?php echo e($categoryId); ?>">
                                    <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($path); ?>" <?php echo e(old('template_path') == $path ? 'selected' : ''); ?>

                                                data-path="<?php echo e($path); ?>"
                                                data-category="<?php echo e($categoryId); ?>">
                                            <?php echo e($name); ?> <small class="text-muted">(<?php echo e($path); ?>)</small>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </optgroup>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['template_path'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">
                                PHP файлы шаблонов должны находиться в директории /public/templates/ или ее подпапках
                            </div>
                        </div>

                        <!-- Элементы быстрого фильтра по категориям -->
                        <div class="mb-4 template-category-filters">
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="badge bg-secondary category-filter-badge active" data-category="all">
                                    <i class="fa-solid fa-filter me-1"></i>Все
                                </span>
                                <?php $__currentLoopData = $templateFiles['categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-primary category-filter-badge" data-category="<?php echo e($category->id); ?>">
                                        <?php echo e($category->name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        
                        <!-- Предпросмотр шаблона -->
                        <div class="mt-4">
                            <h5>Предпросмотр выбранного шаблона</h5>
                            <div class="template-preview border rounded overflow-hidden mt-2 mb-4">
                                <iframe id="template-preview-frame" src="" frameborder="0" 
                                    style="width:100%; height:400px; display:none;"></iframe>
                                <div id="no-template-selected" class="p-4 text-center text-muted bg-light">
                                    <i class="fa-solid fa-image fa-2x mb-3"></i>
                                    <p>Выберите файл шаблона для предпросмотра</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="<?php echo e(route('admin.templates.index')); ?>" class="btn btn-outline-secondary me-2">Отмена</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Сохранить шаблон
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Добавляем CSS для Select2 -->
<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* Кастомные стили для Select2 */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        border-color: #dee2e6;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
    }
    
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option--highlighted {
        background-color: #0d6efd;
        color: #fff;
    }
    
    .select2-container--bootstrap-5 .select2-results__option {
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .select2-container--bootstrap-5 .select2-results__option:last-child {
        border-bottom: none;
    }
    
    .select2-container--bootstrap-5 .select2-results__option small {
        opacity: 0.7;
        font-size: 0.85em;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Стили для бейджей категорий */
    .category-filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
        opacity: 0.7;
    }
    
    .category-filter-badge:hover {
        opacity: 1;
        transform: translateY(-1px);
    }
    
    .category-filter-badge.active {
        opacity: 1;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    /* Делаем шрифт меньше для длинных путей */
    .template-path-item {
        font-size: 0.85em;
        color: #6c757d;
    }
    
    /* Адаптивность для мобильных */
    @media (max-width: 767.98px) {
        .select2-container--bootstrap-5 .select2-dropdown {
            width: 100% !important;
        }
        
        .category-filter-badge {
            margin-bottom: 0.25rem;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<!-- Добавляем JavaScript для Select2 и нашей кастомной логики -->
<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ru.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация Select2 с улучшенным UI и поиском
    $('.select2-template-picker').select2({
        theme: 'bootstrap-5',
        language: 'ru',
        width: '100%',
        placeholder: 'Поиск шаблона...',
        allowClear: true,
        templateResult: formatTemplateResult,
        templateSelection: formatTemplateSelection,
        escapeMarkup: function(m) { return m; }
    });
    
    // Функция форматирования опций в выпадающем списке
    function formatTemplateResult(template) {
        if (!template.id) return template.text;
        
        const path = $(template.element).data('path') || '';
        const pathParts = path.split('/');
        const fileName = pathParts.pop() || '';
        const directory = pathParts.join('/');
        
        return `<div class="d-flex flex-column">
                    <strong>${template.text}</strong>
                    <span class="template-path-item">${directory}/${fileName}</span>
                </div>`;
    }
    
    // Функция форматирования выбранного элемента
    function formatTemplateSelection(template) {
        if (!template.id) return template.text;
        return `<span>${template.text}</span>`;
    }
    
    // Логика для предпросмотра шаблона
    const templatePathSelect = document.getElementById('template_path');
    const previewFrame = document.getElementById('template-preview-frame');
    const noTemplateSelected = document.getElementById('no-template-selected');
    
    // Обновляем предпросмотр при изменении выбранного шаблона через Select2
    $('.select2-template-picker').on('change', function() {
        updatePreviewVisibility();
    });
    
    // Функция для обновления предпросмотра
    function updatePreviewVisibility() {
        const templatePath = templatePathSelect.value;
        
        if (templatePath) {
            // Строим URL для предпросмотра шаблона
            previewFrame.src = `/${templatePath}`;
            previewFrame.style.display = 'block';
            noTemplateSelected.style.display = 'none';
        } else {
            // Если шаблон не выбран, скрываем iframe
            previewFrame.style.display = 'none';
            noTemplateSelected.style.display = 'block';
        }
    }
    
    // Обработчик для бейджей быстрой фильтрации по категориям
    const categoryBadges = document.querySelectorAll('.category-filter-badge');
    categoryBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category');
            
            // Обновляем активный статус бейджей
            categoryBadges.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Если выбраны "Все категории"
            if (categoryId === 'all') {
                // Показываем все группы
                const allOptgroups = templatePathSelect.querySelectorAll('optgroup');
                allOptgroups.forEach(group => {
                    $(group).prop('disabled', false);
                });
                
                // Сбрасываем выбор и обновляем Select2
                $('.select2-template-picker').val('').trigger('change');
            } else {
                // Скрываем ненужные группы и показываем только выбранную категорию
                const allOptgroups = templatePathSelect.querySelectorAll('optgroup');
                allOptgroups.forEach(group => {
                    const groupCategory = group.getAttribute('data-category');
                    if (groupCategory === categoryId) {
                        $(group).prop('disabled', false);
                    } else {
                        $(group).prop('disabled', true);
                    }
                });
                
                // Сбрасываем выбор и обновляем Select2
                $('.select2-template-picker').val('').trigger('change');
            }
            
            // Обновляем Select2 после фильтрации
            $('.select2-template-picker').select2('destroy').select2({
                theme: 'bootstrap-5',
                language: 'ru',
                width: '100%',
                placeholder: 'Поиск шаблона...',
                allowClear: true,
                templateResult: formatTemplateResult,
                templateSelection: formatTemplateSelection,
                escapeMarkup: function(m) { return m; }
            });
        });
    });
    
    // Инициализация при загрузке страницы
    updatePreviewVisibility();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/admin/templates/create.blade.php ENDPATH**/ ?>