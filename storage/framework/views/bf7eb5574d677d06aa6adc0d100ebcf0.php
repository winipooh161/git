<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Панель администратора</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.template-categories.index')); ?>">Категории шаблонов</a></li>
            <li class="breadcrumb-item active" aria-current="page">Редактирование категории</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Редактирование категории шаблонов</h1>
    
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('admin.template-categories.update', $templateCategory)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Название категории *</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="name" name="name" value="<?php echo e(old('name', $templateCategory->name)); ?>" required>
                            <?php $__errorArgs = ['name'];
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
                        
                        <div class="mb-3">
                            <label for="directory_name" class="form-label">Название директории *</label>
                            <div class="input-group">
                                <span class="input-group-text">templates/</span>
                                <input type="text" class="form-control <?php $__errorArgs = ['directory_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="directory_name" name="directory_name" value="<?php echo e(old('directory_name', $templateCategory->directory_name)); ?>" required>
                            </div>
                            <div class="form-text">
                                При изменении будет создана новая директория. Шаблоны придется перенести вручную.
                            </div>
                            <?php $__errorArgs = ['directory_name'];
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
                        
                        <!-- Добавляем поле для выбора типа документа -->
                        <div class="mb-3">
                            <label for="document_type" class="form-label">Тип документа *</label>
                            <select class="form-select <?php $__errorArgs = ['document_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="document_type" name="document_type" required>
                                <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type); ?>" <?php echo e(old('document_type', $templateCategory->document_type ?? 'certificate') == $type ? 'selected' : ''); ?>>
                                        <?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="form-text">
                                Тип документа определяет, для каких целей будут использоваться шаблоны этой категории
                            </div>
                            <?php $__errorArgs = ['document_type'];
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
                                id="sort_order" name="sort_order" value="<?php echo e(old('sort_order', $templateCategory->sort_order)); ?>" min="0">
                            <div class="form-text">
                                Чем меньше число, тем выше категория будет отображаться в списке
                            </div>
                            <?php $__errorArgs = ['sort_order'];
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
                    
                    <div class="col-md-6">
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
                                id="description" name="description" rows="4"><?php echo e(old('description', $templateCategory->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
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
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                    <?php echo e(old('is_active', $templateCategory->is_active) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_active">Активна</label>
                            </div>
                            <div class="form-text">
                                Неактивные категории не будут показываться пользователям
                            </div>
                        </div>
                        
                        <div class="card bg-light mt-3">
                            <div class="card-body">
                                <h6 class="card-title">Информация:</h6>
                                <p class="card-text mb-0">Количество шаблонов: <strong><?php echo e($templateCategory->templates()->count()); ?></strong></p>
                                <p class="card-text mb-0">Слаг: <code><?php echo e($templateCategory->slug); ?></code></p>
                                <p class="card-text mb-0">Создана: <?php echo e($templateCategory->created_at->format('d.m.Y H:i')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="<?php echo e(route('admin.template-categories.index')); ?>" class="btn btn-outline-secondary me-2">
                            Отмена
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Сохранить изменения
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Опасная зона -->
    <div class="card border-0 rounded-4 shadow-sm mt-4 border-danger border-top border-4">
        <div class="card-body p-4">
            <h5 class="card-title text-danger mb-3">Опасная зона</h5>
            <p class="text-muted mb-3">
                Удаление категории невозможно, если в ней содержатся шаблоны. 
                Перед удалением переместите все шаблоны в другую категорию.
            </p>
            
            <form method="POST" action="<?php echo e(route('admin.template-categories.destroy', $templateCategory)); ?>" id="deleteForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                    <i class="fa-solid fa-trash me-1"></i> Удалить категорию
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Вы уверены, что хотите удалить эту категорию? Это действие нельзя отменить. Все шаблоны в категории должны быть сначала перемещены.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/admin/template-categories/edit.blade.php ENDPATH**/ ?>