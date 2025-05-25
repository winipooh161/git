<div class="date-group-heading mb-2 mt-4">
    <h2 class="fs-6 fw-bold text-muted">
        @php
            $carbonDate = \Carbon\Carbon::parse($date);
            $today = \Carbon\Carbon::today();
            $yesterday = \Carbon\Carbon::yesterday();
        @endphp
        
        @if ($carbonDate->isSameDay($today))
            Сегодня
        @elseif ($carbonDate->isSameDay($yesterday))
            Вчера
        @else
            {{ $carbonDate->format('d.m.Y') }}
        @endif
    </h2>
</div>
