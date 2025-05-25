@if($notifications->isEmpty())
    <div class="empty-notifications">
        <i class="fa-solid fa-bell-slash"></i>
        <h6 class="fw-bold mt-2">У вас нет уведомлений</h6>
        <p>Здесь будут отображаться уведомления о важных событиях</p>
    </div>
@else
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Новые уведомления</h6>
        @if($unread_count > 0)
            <span class="badge bg-primary rounded-pill">{{ $unread_count }}</span>
        @endif
    </div>

    @foreach($notifications as $notification)
        <div class="notification-item p-3 mb-2 rounded-3 shadow-sm {{ $notification->read_at ? '' : 'unread' }}" data-id="{{ $notification->id }}">
            <div class="d-flex">
                <div class="notification-icon me-3 {{ $notification->getBadgeClass() }}">
                    <i class="fa-solid {{ $notification->getIconClass() }} text-white"></i>
                </div>
                <div class="flex-grow-1">
                    <a href="{{ $notification->data['url'] ?? '#' }}" class="text-decoration-none notification-item-link">
                        <h6 class="notification-title mb-1">{{ $notification->title }}</h6>
                        <p class="notification-message mb-1">{{ $notification->message }}</p>
                        <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                        
                        @if(isset($notification->data['certificate_number']))
                            <div class="notification-meta mt-2">
                                <span class="badge bg-light text-dark">№ {{ $notification->data['certificate_number'] }}</span>
                                
                                @if(isset($notification->data['amount']))
                                    <span class="badge bg-light text-dark">
                                        {{ number_format($notification->data['amount'], 0, '.', ' ') }} ₽
                                    </span>
                                @endif
                            </div>
                        @endif
                        
                        @if(!$notification->read_at)
                            <span class="new-indicator"></span>
                        @endif
                    </a>
                </div>
            </div>
            <div class="notification-actions">
                @if(!$notification->read_at)
                    <button class="btn btn-sm btn-outline-primary mark-read-btn" data-id="{{ $notification->id }}" title="Отметить как прочитанное">
                        <i class="fa-solid fa-check"></i>
                    </button>
                @endif
                <button class="btn btn-sm btn-outline-danger delete-notification-btn" data-id="{{ $notification->id }}" title="Удалить">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    @endforeach
    
    <div class="text-center mt-2">
        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-primary">
            Показать все уведомления <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>
@endif

<style>
.new-indicator {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 8px;
    height: 8px;
    background-color: #0d6efd;
    border-radius: 50%;
    display: block;
}
</style>
