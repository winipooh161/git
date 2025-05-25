<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Панель администратора</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">Пользователи</a></li>
            <li class="breadcrumb-item active" aria-current="page">Редактирование пользователя</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Редактирование пользователя</h1>
    
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('admin.users.update', $user)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="row g-4">
                    <!-- Основная информация -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Основная информация</h4>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Имя пользователя *</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
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
                            <label for="email" class="form-label">Email адрес *</label>
                            <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
                            <?php $__errorArgs = ['email'];
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
                            <label for="password" class="form-label">Новый пароль</label>
                            <div class="input-group">
                                <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="password" name="password" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Оставьте поле пустым, чтобы не менять текущий пароль</div>
                            <?php $__errorArgs = ['password'];
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
                            <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                            <input type="password" class="form-control" 
                                id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Информация об аккаунте</label>
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">ID пользователя:</dt>
                                        <dd class="col-sm-8"><?php echo e($user->id); ?></dd>
                                        
                                        <dt class="col-sm-4">Дата регистрации:</dt>
                                        <dd class="col-sm-8"><?php echo e($user->created_at->format('d.m.Y H:i')); ?></dd>
                                        
                                        <dt class="col-sm-4">Последнее обновление:</dt>
                                        <dd class="col-sm-8"><?php echo e($user->updated_at->format('d.m.Y H:i')); ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Роли и настройки -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Роли и настройки</h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Назначьте роли *</label>
                            
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]" 
                                        id="role_<?php echo e($role->id); ?>" value="<?php echo e($role->id); ?>" 
                                        <?php echo e(in_array($role->id, old('roles', $userRoles)) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="role_<?php echo e($role->id); ?>">
                                        <?php echo e($role->name); ?>

                                        <small class="text-muted d-block"><?php echo e($role->description); ?></small>
                                    </label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php $__errorArgs = ['roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback d-block"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Изменение ролей пользователя может повлиять на его доступ к различным разделам системы.
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-secondary me-2">Отмена</a>
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
            <p class="text-muted mb-3">Если вы удалите этого пользователя, будут также удалены все связанные с ним данные. Это действие нельзя будет отменить.</p>
            
            <form method="POST" action="<?php echo e(route('admin.users.destroy', $user)); ?>" id="deleteUserForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="button" class="btn btn-outline-danger" onclick="confirmUserDeletion()">
                    <i class="fa-solid fa-trash me-1"></i> Удалить пользователя
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функционал показа/скрытия пароля
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Меняем иконку
        const icon = this.querySelector('i');
        if (type === 'text') {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});

function confirmUserDeletion() {
    if (confirm('Вы уверены, что хотите удалить этого пользователя? Это действие необратимо.')) {
        document.getElementById('deleteUserForm').submit();
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>