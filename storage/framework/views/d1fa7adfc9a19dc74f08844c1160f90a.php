<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Панель администратора</a></li>
            <li class="breadcrumb-item active" aria-current="page">Пользователи</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Управление пользователями</h1>
        <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Добавить пользователя
        </a>
    </div>

    <!-- Сообщения об успешных операциях -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Таблица пользователей -->
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Роли</th>
                            <th>Тариф</th>
                            <th>Дата регистрации</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($user->id); ?></td>
                                <td><?php echo e($user->name); ?></td>
                                <td><?php echo e($user->email); ?></td>
                                <td>
                                    <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            <?php echo e($role->name); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td>
                                    <?php $currentPlan = $user->currentPlan(); ?>
                                    <?php if($currentPlan): ?>
                                        <span class="badge bg-<?php echo e($currentPlan->slug == 'start' ? 'secondary' : 
                                            ($currentPlan->slug == 'vip' ? 'info' : 
                                            ($currentPlan->slug == 'premium' ? 'primary' : 'dark'))); ?>">
                                            <?php echo e($currentPlan->name); ?>

                                        </span>
                                        
                                        <?php if($user->activeSubscription && $user->activeSubscription->end_date): ?>
                                            <small class="d-block text-muted">до <?php echo e($user->activeSubscription->end_date->format('d.m.Y')); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Нет тарифа</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($user->created_at->format('d.m.Y H:i')); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="btn btn-outline-primary">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="if(confirm('Вы уверены?')) { document.getElementById('delete-user-<?php echo e($user->id); ?>').submit(); }">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <form id="delete-user-<?php echo e($user->id); ?>" action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST" class="d-none">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center">Пользователей не найдено</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Пагинация -->
    <div class="mt-4 pagination">
        <?php echo e($users->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/admin/users/index.blade.php ENDPATH**/ ?>