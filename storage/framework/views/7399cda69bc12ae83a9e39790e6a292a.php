<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Панель администратора</a></li>
            <li class="breadcrumb-item active" aria-current="page">Шаблоны документов</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Шаблоны документов</h1>
        <a href="<?php echo e(route('admin.templates.create')); ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Добавить шаблон
        </a>
    </div>
    
    <!-- Сообщения об успешных операциях -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Фильтр по типу документа -->
    <div class="mb-4">
        <div class="btn-group" role="group" aria-label="Фильтр по типу документа">
            <a href="<?php echo e(route('admin.templates.index')); ?>" class="btn btn-outline-secondary <?php echo e(!request()->has('type') ? 'active' : ''); ?>">
                Все типы
            </a>
            <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.templates.index', ['type' => $type])); ?>" 
                   class="btn btn-outline-secondary <?php echo e(request('type') == $type ? 'active' : ''); ?>">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    
    <div class="row g-4">
        <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card position-relative h-100">
                    <div class="position-relative">
                        <img src="<?php echo e($template->image_url); ?>" class="card-img-top" alt="<?php echo e($template->name); ?>">
                        
                        <?php if($template->is_premium): ?>
                            <span class="badge bg-warning position-absolute top-0 end-0 m-3">Премиум</span>
                        <?php endif; ?>
                        
                        <?php if(!$template->is_active): ?>
                            <div class="position-absolute top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center rounded-top-4" style="background-color: rgba(0,0,0,0.5);">
                                <span class="badge bg-danger px-3 py-2">Отключен</span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Отображаем тип документа -->
                        <span class="badge bg-info position-absolute top-0 start-0 m-3">
                            <?php echo e($template->document_type_label); ?>

                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($template->name); ?></h5>
                        <p class="card-text text-muted small"><?php echo e(Str::limit($template->description, 100)); ?></p>
                        
                        <!-- Показываем категорию шаблона -->
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fa-solid fa-folder me-1"></i> 
                                <?php echo e($template->category->name ?? 'Без категории'); ?>

                            </small>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo e(route('admin.templates.edit', $template)); ?>" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-pen-to-square"></i> Редактировать
                                </a>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="if(confirm('Вы уверены?')) { document.getElementById('delete-template-<?php echo e($template->id); ?>').submit(); }">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <form id="delete-template-<?php echo e($template->id); ?>" action="<?php echo e(route('admin.templates.destroy', $template)); ?>" method="POST" class="d-none">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                </form>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isActive<?php echo e($template->id); ?>" 
                                       <?php echo e($template->is_active ? 'checked' : ''); ?> data-template-id="<?php echo e($template->id); ?>">
                                <label class="form-check-label small" for="isActive<?php echo e($template->id); ?>">Активен</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <i class="fa-regular fa-face-frown text-muted fa-3x mb-3"></i>
                <p class="mb-0">Шаблоны не найдены</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Пагинация -->
    <div class="mt-4 pagination">
        <?php echo e($templates->links()); ?>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключатель активности шаблона
    document.querySelectorAll('.form-check-input').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const templateId = this.dataset.templateId;
            const isActive = this.checked;
            
            fetch(`/admin/templates/${templateId}/toggle-active`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка сети');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Обновить UI
                    const templateCard = this.closest('.card');
                    const imageContainer = templateCard.querySelector('.position-relative');
                    const existingOverlay = imageContainer.querySelector('.position-absolute.top-0.start-0.end-0.bottom-0');
                    
                    if (isActive) {
                        if (existingOverlay) existingOverlay.remove();
                    } else {
                        if (!existingOverlay) {
                            const newOverlay = document.createElement('div');
                            newOverlay.className = 'position-absolute top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center rounded-top-4';
                            newOverlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
                            newOverlay.innerHTML = '<span class="badge bg-danger px-3 py-2">Отключен</span>';
                            imageContainer.appendChild(newOverlay);
                        }
                    }
                    
                    // Показать уведомление
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alertDiv.setAttribute('role', 'alert');
                    alertDiv.innerHTML = `
                        Статус шаблона успешно изменен
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    
                    // Автоматически скрыть уведомление через 3 секунды
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alertDiv);
                        bsAlert.close();
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при обновлении статуса шаблона');
                // Вернуть чекбокс в исходное состояние
                this.checked = !this.checked;
            });
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/admin/templates/index.blade.php ENDPATH**/ ?>