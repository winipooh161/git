@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель администратора</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.user-subscriptions.index') }}">Подписки пользователей</a></li>
            <li class="breadcrumb-item active" aria-current="page">Редактирование подписки</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Редактирование подписки</h1>
    
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-body p-4">
            <!-- Информация о пользователе -->
            <div class="mb-4 p-3 bg-light rounded-3">
                <h5 class="mb-3">Информация о пользователе</h5>
                <div class="d-flex align-items-center">
                    @if ($userSubscription->user->avatar)
                        <img src="{{ asset('storage/' . $userSubscription->user->avatar) }}" 
                            class="rounded-circle me-3" style="width: 64px; height: 64px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                            style="width: 64px; height: 64px; font-size: 24px;">
                            {{ substr($userSubscription->user->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-1">{{ $userSubscription->user->name }}</h5>
                        <p class="mb-1"><i class="fa-solid fa-envelope text-muted me-2"></i> {{ $userSubscription->user->email }}</p>
                        @if($userSubscription->user->phone)
                            <p class="mb-0"><i class="fa-solid fa-phone text-muted me-2"></i> {{ $userSubscription->user->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('admin.user-subscriptions.update', $userSubscription) }}">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <!-- Выбор тарифа -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="subscription_plan_id" class="form-label">Тариф *</label>
                            <select class="form-select @error('subscription_plan_id') is-invalid @enderror" id="subscription_plan_id" name="subscription_plan_id" required>
                                <option value="">-- Выберите тариф --</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" 
                                        {{ old('subscription_plan_id', $userSubscription->subscription_plan_id) == $plan->id ? 'selected' : '' }}
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
                        
                        <div id="plan-details" class="card bg-light p-3 mb-3">
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
                                id="start_date" name="start_date" value="{{ old('start_date', $userSubscription->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Дата окончания</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                id="end_date" name="end_date" value="{{ old('end_date', $userSubscription->end_date ? $userSubscription->end_date->format('Y-m-d') : '') }}">
                            <div class="form-text">Оставьте поле пустым для бессрочной подписки</div>
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                {{ old('is_active', $userSubscription->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Активная подписка</label>
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.user-subscriptions.index') }}" class="btn btn-outline-secondary me-2">Отмена</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Сохранить изменения
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Опасная зона -->
    <div class="card border-0 rounded-4 shadow-sm mt-4 border-danger border-top border-4">
        <div class="card-body p-4">
            <h5 class="card-title text-danger mb-3">Опасная зона</h5>
            <p class="text-muted mb-3">Удаление подписки приведет к потере доступа пользователя к связанным функциям тарифа.</p>
            
            <form method="POST" action="{{ route('admin.user-subscriptions.destroy', $userSubscription) }}" id="deleteSubscriptionForm">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-danger" onclick="confirmSubscriptionDeletion()">
                    <i class="fa-solid fa-trash me-1"></i> Удалить подписку
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
            } else {
                planDuration.textContent = 'Бессрочный';
            }
            
            planDetails.classList.remove('d-none');
        } else {
            planDetails.classList.add('d-none');
        }
    }
    
    // Вызываем функцию при загрузке страницы
    updatePlanInfo();
    
    // Обработчик изменения тарифа
    planSelect.addEventListener('change', function() {
        updatePlanInfo();
        
        // Автоматически устанавливаем дату окончания при смене тарифа
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const duration = selectedOption.dataset.duration;
            
            if (duration && startDateInput.value) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + parseInt(duration));
                
                // Форматируем дату в формат YYYY-MM-DD для input type="date"
                const endDateFormatted = endDate.toISOString().split('T')[0];
                endDateInput.value = endDateFormatted;
            } else if (!duration) {
                endDateInput.value = ''; // Очищаем дату окончания для бессрочного тарифа
            }
        }
    });
    
    // Обработчик изменения даты начала
    startDateInput.addEventListener('change', function() {
        const selectedOption = planSelect.options[planSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const duration = selectedOption.dataset.duration;
            
            if (duration) {
                const startDate = new Date(this.value);
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + parseInt(duration));
                
                // Форматируем дату в формат YYYY-MM-DD для input type="date"
                const endDateFormatted = endDate.toISOString().split('T')[0];
                endDateInput.value = endDateFormatted;
            }
        }
    });
});

function confirmSubscriptionDeletion() {
    if (confirm('Вы уверены, что хотите удалить эту подписку? Это действие невозможно отменить.')) {
        document.getElementById('deleteSubscriptionForm').submit();
    }
}
</script>
@endpush
@endsection
