@props(['title' => null, 'header' => null, 'footer' => null, 'hover' => false, 'class' => null])

<div class="card border-0 rounded-4 shadow-sm {{ $class ?? '' }} {{ isset($hover) && $hover ? 'hover-lift' : '' }} mb-4">
    @if(isset($header))
        <div class="card-header bg-transparent pt-4">
            {!! $header !!}
        </div>
    @elseif(isset($title))
        <div class="card-header bg-transparent pt-4">
            <h5 class="mb-0">{{ $title }}</h5>
        </div>
    @endif
    
    <div class="card-body {{ isset($title) || isset($header) ? '' : 'pt-4' }}">
        {{ $slot }}
    </div>
    
    @if(isset($footer))
        <div class="card-footer bg-transparent border-0 pb-3">
            {{ $footer }}
        </div>
    @endif
</div>
