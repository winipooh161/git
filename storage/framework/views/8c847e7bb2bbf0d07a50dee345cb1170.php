<aside id="sidebarMenu" class="offcanvas-lg offcanvas-start bg-white border-end" tabindex="-1">
    <div class="offcanvas-header d-lg-none">
        <h5 class="offcanvas-title">Меню</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="sidebar-sticky d-flex flex-column h-100">
            <!-- Лого и название проекта -->
            <div class="sidebar-logo d-none d-lg-flex align-items-center ps-4 py-3">
                <a href="<?php echo e(url('/')); ?>" class="text-decoration-none d-flex align-items-center">
                    <span class="fs-5 fw-semibold text-dark sidebar-logo-text"><?php echo e(config('app.name', 'Laravel')); ?></span>
                </a>
                <button id="sidebarToggleBtn" class="btn btn-sm btn-link ms-auto me-3 text-dark d-none d-lg-block">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
 
            <!-- Переключатель ролей -->
            <?php if(Auth::user()->hasAnyRole(['predprinimatel', 'user'])): ?>
            <div class="px-3 py-2 border-bottom">
                <div class="role-switcher">
                    <form action="<?php echo e(route('role.switch')); ?>" method="POST" id="roleSwitchForm">
                        <?php echo csrf_field(); ?>
                      
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="role" id="role_predprinimatel" value="predprinimatel" 
                                <?php echo e(Auth::user()->hasRole('predprinimatel') && !session('active_role') || session('active_role') == 'predprinimatel' ? 'checked' : ''); ?>

                                onchange="document.getElementById('roleSwitchForm').submit()">
                            <label class="btn btn-outline-primary btn-sm rounded-start-2" for="role_predprinimatel">
                                <i class="fa-solid fa-briefcase me-1"></i>  <span class="sidebar-text">Предприниматель</span>
                            </label>
                            
                            <input type="radio" class="btn-check" name="role" id="role_user" value="user" 
                                <?php echo e(Auth::user()->hasRole('user') && !Auth::user()->hasRole('predprinimatel') || session('active_role') == 'user' ? 'checked' : ''); ?>

                                onchange="document.getElementById('roleSwitchForm').submit()">
                            <label class="btn btn-outline-primary btn-sm rounded-end-2" for="role_user">
                                <i class="fa-solid fa-user me-1"></i> <span class="sidebar-text">Клиент</span>
                            </label>
                        </div>
                     
                    </form>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Упрощенная навигация -->
            <nav class="sidebar-nav flex-grow-1 py-3">
                <ul class="nav flex-column">
                    <?php if(Auth::user()->hasRole('admin')): ?>
                        <!-- Навигация администратора -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                                <i class="fa-solid fa-gauge-high sidebar-icon"></i>
                                <span class="sidebar-text">Панель управления</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('admin.users.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.users.index')); ?>">
                                <i class="fa-solid fa-users sidebar-icon"></i>
                                <span class="sidebar-text">Пользователи</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('admin.templates.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.templates.index')); ?>">
                                <i class="fa-solid fa-palette sidebar-icon"></i>
                                <span class="sidebar-text">Шаблоны</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('admin.template-categories.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.template-categories.index')); ?>">
                                <i class="fa-solid fa-folder sidebar-icon"></i>
                                <span class="sidebar-text">Категории шаблонов</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('admin.certificates.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.certificates.index')); ?>">
                                <i class="fa-solid fa-certificate sidebar-icon"></i>
                                <span class="sidebar-text">документы</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('admin.animation-effects.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.animation-effects.index')); ?>">
                                <i class="fa-solid fa-wand-sparkles sidebar-icon"></i>
                                <span class="sidebar-text">Анимационные эффекты</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('admin.telegram.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.telegram.index')); ?>">
                                <i class="fa-brands fa-telegram sidebar-icon"></i>
                                <span class="sidebar-text">Telegram бот</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php
                        // Определяем активную роль для отображения соответствующего меню
                        $activeRole = session('active_role');
                        
                        // Если активная роль не выбрана, но у пользователя есть роль "предприниматель", используем её
                        if (!$activeRole && Auth::user()->hasRole('predprinimatel')) {
                            $activeRole = 'predprinimatel';
                        }
                        
                        // Если активная роль не выбрана и у пользователя нет роли "предприниматель", но есть роль "пользователь"
                        if (!$activeRole && Auth::user()->hasRole('user')) {
                            $activeRole = 'user';
                        }
                    ?>
                    
                    <?php if($activeRole == 'predprinimatel' && Auth::user()->hasAnyRole(['predprinimatel', 'admin'])): ?>
                        <!-- Навигация предпринимателя - только самое важное -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('entrepreneur.certificates.index') ? 'active' : ''); ?>" href="<?php echo e(route('entrepreneur.certificates.index')); ?>">
                                <i class="fa-solid fa-certificate sidebar-icon"></i>
                                <span class="sidebar-text">Мои документы</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('entrepreneur.certificates.select-template') ? 'active' : ''); ?>" href="<?php echo e(route('entrepreneur.certificates.select-template')); ?>">
                                <i class="fa-solid fa-plus sidebar-icon"></i>
                                <span class="sidebar-text">Создать документ</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('entrepreneur.analytics.*') ? 'active' : ''); ?>" href="<?php echo e(route('entrepreneur.analytics.statistics')); ?>">
                                <i class="fa-solid fa-chart-simple sidebar-icon"></i>
                                <span class="sidebar-text">Аналитика</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if($activeRole == 'user' && Auth::user()->hasAnyRole(['user', 'predprinimatel', 'admin'])): ?>
                        <!-- Навигация обычного пользователя -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(Route::is('user.certificates.*') ? 'active' : ''); ?>" href="<?php echo e(route('user.certificates.index')); ?>">
                                <i class="fa-solid fa-certificate sidebar-icon"></i>
                                <span class="sidebar-text">Мои документы</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Общие упрощенные пункты меню -->
                    <li class="nav-item sidebar-divider"></li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Route::is('profile.*') ? 'active' : ''); ?>" href="<?php echo e(route('profile.index')); ?>">
                            <i class="fa-solid fa-user sidebar-icon"></i>
                            <span class="sidebar-text">Профиль</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('logout')); ?>" 
                           onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                            <i class="fa-solid fa-right-from-bracket sidebar-icon"></i>
                            <span class="sidebar-text">Выход</span>
                        </a>
                        <form id="sidebar-logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                            <?php echo csrf_field(); ?>
                        </form>
                    </li>
                    
                    <!-- Добавляем пункт меню Тарифы -->
                    <li class="nav-item">
                        <a href="<?php echo e(route('subscription.plans')); ?>" class="nav-link <?php echo e(Route::is('subscription.plans') ? 'active' : ''); ?>">
                            <i class="fa-solid fa-crown sidebar-icon"></i>
                            <span class="sidebar-text">Тарифы</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Подвал бокового меню - упрощен -->
            <div class="sidebar-footer border-top py-3 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="sidebar-version text-muted small">
                        <span>Версия 1.0.0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

<style>
/* Дополнительные стили для переключателя ролей */
.role-switcher .btn-outline-primary {
    --bs-btn-color: #6c757d;
    --bs-btn-border-color: #dee2e6;
    --bs-btn-hover-bg: #f8f9fa;
    --bs-btn-hover-border-color: #ced4da;
    --bs-btn-active-bg: #0d6efd;
    --bs-btn-active-color: white;
    padding: 0.25rem 0.5rem;
    font-size: 0.8125rem;
}

/* Дополнительный отступ для переключателя на узкой версии меню */
body.sidebar-collapsed .role-switcher {
    padding: 0.25rem;
    text-align: center;
}
</style>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/components/sidebar.blade.php ENDPATH**/ ?>