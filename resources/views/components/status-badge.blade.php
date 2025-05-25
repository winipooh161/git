@props(['status'])

@php
$classes = [
    'active' => 'bg-success',
    'used' => 'bg-secondary',
    'expired' => 'bg-warning',
    'canceled' => 'bg-danger',
    'pending' => 'bg-info',
    'draft' => 'bg-light text-dark'
];

$labels = [
    'active' => 'Активен',
    'used' => 'Использован',
    'expired' => 'Истек',
    'canceled' => 'Отменен',
    'pending' => 'Ожидает',
    'draft' => 'Черновик'
];

$class = $classes[$status] ?? 'bg-light';
$label = $labels[$status] ?? $status;
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $class]) }}>{{ $label }}</span>
