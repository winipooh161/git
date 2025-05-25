@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель администратора</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.user-subscriptions.index') }}">Подписки пользователей</a></li>
            <li class="breadcrumb-item active" aria-current="page">Назначение подписки</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Назначение подписки пользователю</h1>
    
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.user-subscriptions.store') }}">
                @csrf
                
                <!-- Выбор пользователя -->
                <div class="mb-4">
                    <label for="user_id" class="form-label">Выберите пользователя *</label>
                    <select class="form-select select2 @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                        <option value="">-- Выберите пользователя --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    
                    <div class="form-text mt-2">
                        <i class="fa-solid fa-circle-info me-1"></i> При назначении новой подписки любая активная подписка пользователя будет деактивирована.
                    </div>
                </div>
                
                <div class="row g-4">
                    <!-- Выбор тарифа -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="subscription_plan_id" class="form-label">Выберите тариф *</label>
                            <select class="form-select @error('subscription_plan_id') is-invalid @enderror" id="subscription_plan_id" name="subscription_plan_id" required>
                                <option value="">-- Выберите тариф --</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}
                                        data-duration="{{ $plan->duration_days }}" data-name="{{ $plan->name }}" data-price="{{ $plan->price }}">
                                        {{ $plan->name }} - {{ number_format($plan->price, 0, '.', ' ') }} ₽
                                        @if($plan->duration_days)
                                            ({{ $plan->duration_days }} дней)
                                        @else
                                            (Бессрочный)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('subscription_plan_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div id="plan-details" class="card bg-light p-3 mb-3 d-none">
                            <h5 id="plan-name" class="card-title"></h5>
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-muted mb-1">Стоимость:</p>
                                    <p id="plan-price" class="fw-bold"></p>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted mb-1">Длительность:</p>
                                    <p id="plan-duration" class="fw-bold"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Параметры подписки -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Дата начала *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Дата окончания</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                id="end_date" name="end_date" value="{{ old('end_date') }}">
                            <div class="form-text">Оставьте поле пустым для бессрочной подписки</div>
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                {{ old('is_active') !== null ? (old('is_active') ? 'checked' : '') : 'checked' }}>
                            <label class="form-check-label" for="is_active">Активировать подписку</label>
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.user-subscriptions.index') }}" class="btn btn-outline-secondary me-2">Отмена</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-check me-1"></i> Назначить подписку
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация Select2 для поля выбора пользователя
    $('.select2').select2({
        theme: 'bootstrap-5',
        placeholder: "Выберите пользователя",
        allowClear: true
    });
    
    // Обработка выбора тарифа
    const planSelect = document.getElementById('subscription_plan_id');
    const planDetails = document.getElementById('plan-details');
    const planName = document.getElementById('plan-name');
    const planPrice = document.getElementById('plan-price');
    const planDuration = document.getElementById('plan-duration');
    const endDateInput = document.getElementById('end_date');
    const startDateInput = document.getElementById('start_date');
    
    // Функция для обновления информации о тарифе
    function updatePlanInfo() {
        const selectedOption = planSelect.options[planSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            // Получаем данные из data-атрибутов
            const name = selectedOption.dataset.name;
            const price = selectedOption.dataset.price;
            const duration = selectedOption.dataset.duration;
            
            // Обновляем отображение
            planName.textContent = name;
            planPrice.textContent = new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(price);
            
            if (duration) {
                planDuration.textContent = duration + ' дней';
                
                // Автоматически устанавливаем дату окончания
                if (startDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + parseInt(duration));
                    
                    // Форматируем дату в формат YYYY-MM-DD для input type="date"
                    const endDateFormatted = endDate.toISOString().split('T')[0];
                    endDateInput.value = endDateFormatted;
                }
            } else {
                planDuration.textContent = 'Бессрочный';
                endDateInput.value = ''; // Очищаем дату окончания для бессрочного тарифа
            }
            
            planDetails.classList.remove('d-none');
        } else {
            planDetails.classList.add('d-none');
        }
    }
    
    // Вызываем функцию при загрузке страницы
    if (planSelect.value) {
        updatePlanInfo();
    }
    
    // Обработчик изменения тарифа
    planSelect.addEventListener('change', updatePlanInfo);
    
    // Обработчик изменения даты начала
    startDateInput.addEventListener('change', function() {
        if (planSelect.value) {
            updatePlanInfo();
        }
    });
});
</script>
@endpush
@endsection
