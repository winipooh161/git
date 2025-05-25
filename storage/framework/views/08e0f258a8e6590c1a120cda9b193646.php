<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Панель администратора</a></li>
            <li class="breadcrumb-item active" aria-current="page">Категории шаблонов</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Категории шаблонов документов</h1>
        <a href="<?php echo e(route('admin.template-categories.create')); ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Добавить категорию
        </a>
    </div>
    
    <!-- Сообщения об успешных операциях -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Директория</th>
                            <th>Кол-во шаблонов</th>
                            <th>Порядок</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($category->name); ?></td>
                                <td><code><?php echo e($category->directory_name); ?></code></td>
                                <td><?php echo e($category->templates_count); ?></td>
                                <td><?php echo e($category->sort_order); ?></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input toggle-active" type="checkbox" 
                                               data-id="<?php echo e($category->id); ?>" 
                                               <?php echo e($category->is_active ? 'checked' : ''); ?>>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('admin.template-categories.edit', $category)); ?>" class="btn btn-outline-primary">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirmDelete(<?php echo e($category->id); ?>, '<?php echo e($category->name); ?>')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <form id="delete-form-<?php echo e($category->id); ?>" action="<?php echo e(route('admin.template-categories.destroy', $category)); ?>" method="POST" class="d-none">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">Категории шаблонов не найдены</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Пагинация -->
    <div class="mt-4 pagination">
        <?php echo e($categories->links()); ?>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключение активности категории
    document.querySelectorAll('.toggle-active').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const categoryId = this.dataset.id;
            const isActive = this.checked;
            
            fetch(`/admin/template-categories/${categoryId}/toggle-active`, {
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
                    // Показать уведомление
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alertDiv.setAttribute('role', 'alert');
                    alertDiv.innerHTML = `
                        Статус категории успешно изменен
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
                alert('Произошла ошибка при обновлении статуса категории');
                // Вернуть чекбокс в исходное состояние
                this.checked = !this.checked;
            });
        });
    });
});

function confirmDelete(id, name) {
    if (confirm(`Вы действительно хотите удалить категорию "${name}"?`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/admin/template-categories/index.blade.php ENDPATH**/ ?>