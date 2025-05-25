@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель администратора</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Пользователи</a></li>
            <li class="breadcrumb-item active" aria-current="page">Редактирование пользователя</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Редактирование пользователя</h1>
    
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <!-- Основная информация -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Основная информация</h4>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Имя пользователя *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email адрес *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Новый пароль</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">Оставьте поле пустым, чтобы не менять текущий пароль</div>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                            <input type="password" class="form-control" 
                                id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Информация об аккаунте</label>
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">ID пользователя:</dt>
                                        <dd class="col-sm-8">{{ $user->id }}</dd>
                                        
                                        <dt class="col-sm-4">Дата регистрации:</dt>
                                        <dd class="col-sm-8">{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                                        
                                        <dt class="col-sm-4">Последнее обновление:</dt>
                                        <dd class="col-sm-8">{{ $user->updated_at->format('d.m.Y H:i') }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Роли и настройки -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Роли и настройки</h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Назначьте роли *</label>
                            
                            @foreach($roles as $role)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]" 
                                        id="role_{{ $role->id }}" value="{{ $role->id }}" 
                                        {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                        {{ $role->name }}
                                        <small class="text-muted d-block">{{ $role->description }}</small>
                                    </label>
                                </div>
                            @endforeach
                            
                            @error('roles')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Управление тарифом пользователя -->
                        <div class="mb-3">
                            <label class="form-label">Тариф пользователя</label>
                            
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">Текущий тариф</h5>
                                    @php
                                        $currentPlan = $user->currentPlan();
                                        $activeSubscription = $user->activeSubscription;
                                    @endphp
                                    
                                    @if($currentPlan)
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge bg-primary me-2">{{ $currentPlan->name }}</span>
                                            @if($activeSubscription && $activeSubscription->end_date)
                                                <span class="text-muted">до {{ $activeSubscription->end_date->format('d.m.Y') }}</span>
                                            @else
                                                <span class="badge bg-success">Бессрочный</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            У пользователя нет активного тарифа
                                        </div>
                                    @endif
                                    
                                    <h5 class="card-title mb-2">Изменить тариф</h5>
                                    <div class="mb-3">
                                        <label for="subscription_plan_id" class="form-label">Новый тариф</label>
                                        <select class="form-select" id="subscription_plan_id" name="subscription_plan_id">
                                            <option value="">Не менять тариф</option>
                                            @foreach($subscriptionPlans as $plan)
                                                <option value="{{ $plan->id }}" 
                                                    data-duration="{{ $plan->duration_days }}">
                                                    {{ $plan->name }} 
                                                    ({{ $plan->price > 0 ? number_format($plan->price, 0, '.', ' ') . ' ₽' : 'Бесплатно' }})
                                                    @if($plan->duration_days)
                                                        - {{ $plan->duration_days }} дней
                                                    @else
                                                        - Бессрочно
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="deactivate_other_subscriptions" name="deactivate_other_subscriptions" value="1" checked>
                                        <label class="form-check-label" for="deactivate_other_subscriptions">
                                            Деактивировать другие тарифы пользователя
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Изменение ролей пользователя может повлиять на его доступ к различным разделам системы.
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">Отмена</a>
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
            <p class="text-muted mb-3">Если вы удалите этого пользователя, будут также удалены все связанные с ним данные. Это действие нельзя будет отменить.</p>
            
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" id="deleteUserForm">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-danger" onclick="confirmUserDeletion()">
                    <i class="fa-solid fa-trash me-1"></i> Удалить пользователя
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функционал показа/скрытия пароля
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Меняем иконку
        const icon = this.querySelector('i');
        if (type === 'text') {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});

function confirmUserDeletion() {
    if (confirm('Вы уверены, что хотите удалить этого пользователя? Это действие необратимо.')) {
        document.getElementById('deleteUserForm').submit();
    }
}
</script>
@endsection
