@php
    $status = $certificate->status;
    $statusLabel = $certificate->status_label;
@endphp

@if($status === 'series')
    <span class="badge bg-primary">
        <i class="fa-solid fa-layer-group me-1"></i> Серия ({{ $certificate->activated_copies_count }}/{{ $certificate->batch_size }})
    </span>
@elseif($status === 'active')
    <span class="badge bg-success">
        <i class="fa-solid fa-check-circle me-1"></i> {{ $statusLabel }}
    </span>
@elseif($status === 'used')
    <span class="badge bg-secondary">
        <i class="fa-solid fa-check me-1"></i> {{ $statusLabel }}
    </span>
@elseif($status === 'expired')
    <span class="badge bg-warning">
        <i class="fa-solid fa-hourglass-end me-1"></i> {{ $statusLabel }}
    </span>
@elseif($status === 'canceled')
    <span class="badge bg-danger">
        <i class="fa-solid fa-ban me-1"></i> {{ $statusLabel }}
    </span>
@elseif($status === 'refunded')
    <span class="badge bg-info">
        <i class="fa-solid fa-undo me-1"></i> {{ $statusLabel }}
    </span>
@elseif($status === 'on_hold')
    <span class="badge bg-secondary">
        <i class="fa-solid fa-pause me-1"></i> {{ $statusLabel }}
    </span>
@else
    <span class="badge bg-light text-dark">
        {{ $statusLabel }}
    </span>
@endif
