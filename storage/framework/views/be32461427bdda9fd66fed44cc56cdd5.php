

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 fw-bold mb-0">Уведомления</h1>
        <div class="d-flex">
            <div class="btn-group me-2" role="group">
                <a href="<?php echo e(route('notifications.index')); ?>" class="btn btn-outline-primary <?php echo e(!request()->has('read') ? 'active' : ''); ?>">Все</a>
                <a href="<?php echo e(route('notifications.index', ['read' => '0'])); ?>" class="btn btn-outline-primary <?php echo e(request()->get('read') === '0' ? 'active' : ''); ?>">Непрочитанные</a>
                <a href="<?php echo e(route('notifications.index', ['read' => '1'])); ?>" class="btn btn-outline-primary <?php echo e(request()->get('read') === '1' ? 'active' : ''); ?>">Прочитанные</a>
            </div>
            
            <form action="<?php echo e(route('notifications.mark-all-read')); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline-secondary" <?php echo e(Auth::user()->unreadNotificationsCount() > 0 ? '' : 'disabled'); ?>>
                    <i class="fa-solid fa-check-double me-1"></i>Отметить все как прочитанные
                </button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="notifications-list">
                <?php echo $__env->make('notifications.partials._notifications_list', ['notifications' => $notifications], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>

    <?php if($notifications->hasPages()): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($notifications->withQueryString()->links()); ?>

        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработчики для кнопок "Отметить как прочитанное"
    document.querySelectorAll('.mark-read-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const notificationId = this.dataset.id;
            const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
            
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Удаляем класс unread у уведомления
                    notificationItem.classList.remove('unread');
                    
                    // Скрываем кнопку "Отметить как прочитанное"
                    this.style.display = 'none';
                    
                    // Если текущий фильтр - непрочитанные, скрываем это уведомление
                    if (window.location.href.includes('read=0')) {
                        notificationItem.style.opacity = '0';
                        setTimeout(() => {
                            notificationItem.remove();
                            
                            // Если не осталось уведомлений, добавляем сообщение
                            if (!document.querySelector('.notification-item')) {
                                document.querySelector('.notifications-list').innerHTML = `
                                    <div class="empty-notifications">
                                        <i class="fa-solid fa-bell-slash"></i>
                                        <h6 class="fw-bold mt-2">У вас нет уведомлений</h6>
                                        <p>Здесь будут отображаться уведомления о важных событиях</p>
                                    </div>
                                `;
                            }
                        }, 300);
                    }
                }
            })
            .catch(error => {
                console.error('Ошибка при отметке уведомления как прочитанного:', error);
            });
        });
    });
    
    // Обработчики для кнопок удаления уведомления
    document.querySelectorAll('.delete-notification-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Вы уверены, что хотите удалить это уведомление?')) {
                return;
            }
            
            const notificationId = this.dataset.id;
            const notificationItem = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
            
            fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Удаляем уведомление из DOM с анимацией
                    notificationItem.style.opacity = '0';
                    setTimeout(() => {
                        notificationItem.remove();
                        
                        // Если не осталось уведомлений, добавляем сообщение
                        if (!document.querySelector('.notification-item')) {
                            document.querySelector('.notifications-list').innerHTML = `
                                <div class="empty-notifications">
                                    <i class="fa-solid fa-bell-slash"></i>
                                    <h6 class="fw-bold mt-2">У вас нет уведомлений</h6>
                                    <p>Здесь будут отображаться уведомления о важных событиях</p>
                                </div>
                            `;
                        }
                    }, 300);
                }
            })
            .catch(error => {
                console.error('Ошибка при удалении уведомления:', error);
            });
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<style>
.notifications-list .notification-item {
    border-left: 3px solid #e9ecef;
    transition: all 0.2s ease;
    position: relative;
}

.notifications-list .notification-item:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.notifications-list .notification-item.unread {
    border-left-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.03);
}

.notifications-list .notification-item .notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notifications-list .notification-item .notification-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.notifications-list .notification-item:hover .notification-actions {
    opacity: 1;
}

.notifications-list .notification-item .notification-time {
    font-size: 0.75rem;
    color: #6c757d;
}

.notifications-list .notification-item .notification-meta {
    margin-top: 0.5rem;
}

.notifications-list .notification-item .notification-title {
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.notifications-list .notification-item .notification-message {
    color: #212529;
    margin-bottom: 0;
}

.empty-notifications {
    text-align: center;
    padding: 40px 0;
}

.empty-notifications i {
    font-size: 3rem;
    color: #6c757d;
    opacity: 0.4;
    margin-bottom: 1rem;
}

.empty-notifications p {
    color: #6c757d;
    margin-bottom: 0.5rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/notifications/index.blade.php ENDPATH**/ ?>