@props(['type' => 'info', 'icon' => null, 'title' => null, 'time' => null, 'read' => false])

@php
    $typeClasses = [
        'info' => 'bg-primary bg-opacity-10 text-primary',
        'success' => 'bg-success bg-opacity-10 text-success',
        'warning' => 'bg-warning bg-opacity-10 text-warning',
        'danger' => 'bg-danger bg-opacity-10 text-danger',
        'secondary' => 'bg-secondary bg-opacity-10 text-secondary'
    ];
    
    $typeIcons = [
        'info' => 'fa-solid fa-info-circle',
        'success' => 'fa-solid fa-check-circle',
        'warning' => 'fa-solid fa-exclamation-triangle',
        'danger' => 'fa-solid fa-exclamation-circle',
        'secondary' => 'fa-solid fa-bell'
    ];
    
    $bgClass = $typeClasses[$type] ?? $typeClasses['info'];
    $iconClass = $icon ?? $typeIcons[$type] ?? $typeIcons['info'];
@endphp

<a {{ $attributes->merge(['class' => 'dropdown-item d-flex align-items-center py-2']) }} href="#">
    <div class="flex-shrink-0 me-3">
        <div class="avatar rounded-circle {{ $bgClass }} p-2" style="width: 40px; height: 40px;">
            <i class="{{ $iconClass }}"></i>
        </div>
    </div>
    <div class="flex-grow-1 {{ $read ? 'text-muted' : '' }}">
        <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold">{{ $title }}</div>
            @if(!$read)
                <div class="ms-2">
                    <span class="badge rounded-pill bg-primary" style="width: 8px; height: 8px; padding: 0;"></span>
                </div>
            @endif
        </div>
        <div class="text-truncate small">{{ $slot }}</div>
        @if($time)
            <div class="text-muted small mt-1">{{ $time }}</div>
        @endif
    </div>
</a>
