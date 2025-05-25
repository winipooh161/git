<?php if($notifications->isEmpty()): ?>
    <div class="empty-notifications">
        <i class="fa-solid fa-bell-slash"></i>
        <h6 class="fw-bold mt-2">У вас нет уведомлений</h6>
        <p>Здесь будут отображаться уведомления о важных событиях</p>
    </div>
<?php else: ?>
    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="notification-item p-3 mb-2 rounded-3 shadow-sm <?php echo e($notification->read_at ? '' : 'unread'); ?>" data-id="<?php echo e($notification->id); ?>">
            <div class="d-flex">
                <div class="notification-icon me-3 <?php echo e($notification->getBadgeClass()); ?>">
                    <i class="fa-solid <?php echo e($notification->getIconClass()); ?> text-white"></i>
                </div>
                <div class="flex-grow-1">
                    <a href="<?php echo e($notification->data['url'] ?? '#'); ?>" class="text-decoration-none notification-item-link">
                        <h6 class="notification-title mb-1"><?php echo e($notification->title); ?></h6>
                        <p class="notification-message mb-1"><?php echo e($notification->message); ?></p>
                        <div class="notification-time"><?php echo e($notification->created_at->diffForHumans()); ?></div>
                        
                        <?php if(isset($notification->data['certificate_number'])): ?>
                            <div class="notification-meta">
                                <span class="badge bg-light text-dark">№ <?php echo e($notification->data['certificate_number']); ?></span>
                                
                                <?php if(isset($notification->data['amount'])): ?>
                                    <span class="badge bg-light text-dark">
                                        <?php echo e(number_format($notification->data['amount'], 0, '.', ' ')); ?> ₽
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
            <div class="notification-actions">
                <?php if(!$notification->read_at): ?>
                    <button class="btn btn-sm btn-outline-primary mark-read-btn" data-id="<?php echo e($notification->id); ?>" title="Отметить как прочитанное">
                        <i class="fa-solid fa-check"></i>
                    </button>
                <?php endif; ?>
                <button class="btn btn-sm btn-outline-danger delete-notification-btn" data-id="<?php echo e($notification->id); ?>" title="Удалить">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/notifications/partials/_notifications_list.blade.php ENDPATH**/ ?>