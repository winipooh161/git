<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
    <div>
        <h1 class="h2 mb-1">{{ $title }}</h1>
        @if(isset($subtitle))
            <p class="text-muted">{{ $subtitle }}</p>
        @endif
    </div>
    
    @if(isset($actions))
        <div class="d-flex mt-3 mt-md-0">
            {{ $actions }}
        </div>
    @endif
</div>
