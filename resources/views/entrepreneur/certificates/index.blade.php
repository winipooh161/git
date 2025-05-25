@extends('layouts.lk')

@section('content')
<div class="container py-4">
 
          @include('profile.partials._profile')
      
   
    
    <!-- Вывод информационного сообщения, если заканчиваются стики -->
    @if(Auth::user()->sticks < 3)
    <div class="alert alert-warning mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle fs-4 me-2"></i>
            <div>
                <strong>Внимание!</strong> У вас осталось мало стиков ({{ Auth::user()->sticks }}). 
                <a href="{{ route('subscription.plans') }}" class="alert-link">Обновите ваш тарифный план</a> для получения дополнительных стиков.
            </div>
        </div>
    </div>
    @endif

    <!-- Статистика -->
    
    
    {{-- Форма поиска --}}
    @include('entrepreneur.certificates.partials-index._search_form')
    
    {{-- Контейнер для результатов поиска --}}
    @include('entrepreneur.certificates.partials-index._search_results')

    {{-- Индикатор загрузки --}}
    @include('entrepreneur.certificates.partials-index._loading_indicator')

    {{-- Сообщения об успешных операциях --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Основной контейнер для документов --}}
    @include('entrepreneur.certificates.partials-index._certificates_grid', ['certificates' => $certificates])

    {{-- Пагинация --}}
    @if (isset($certificates) && $certificates->hasPages())
        <div class="mt-4 d-flex justify-content-center pagination" id="pagination-container">
            {{ $certificates->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- Toast-уведомление для подтверждения копирования --}}
@include('entrepreneur.certificates.partials-index._copy_toast')

{{-- Стили и скрипты --}}
@include('entrepreneur.certificates.partials-index._styles')
@include('entrepreneur.certificates.partials-index._scripts')
@endsection
