@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель администратора</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.subscription-plans.index') }}">Управление тарифами</a></li>
            <li class="breadcrumb-item active" aria-current="page">Редактирование тарифа</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Редактирование тарифа</h1>
    
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.subscription-plans.update', $subscriptionPlan) }}">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <!-- Основная информация -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Основная информация</h4>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Название тарифа *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $subscriptionPlan->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="slug" class="form-label">Символьный код *</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                id="slug" name="slug" value="{{ old('slug', $subscriptionPlan->slug) }}" required>
                            <div class="form-text">Уникальный идентификатор тарифа</div>
                            @error('slug')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description', $subscriptionPlan->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Параметры тарифа -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Параметры тарифа</h4>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Стоимость (₽) *</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" 
                                id="price" name="price" value="{{ old('price', $subscriptionPlan->price) }}" required>
                            @error('price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="duration_days" class="form-label">Длительность (дней)</label>
                            <input type="number" min="1" class="form-control @error('duration_days') is-invalid @enderror" 
                                id="duration_days" name="duration_days" value="{{ old('duration_days', $subscriptionPlan->duration_days) }}">
                            <div class="form-text">Оставьте пустым для бессрочного тарифа</div>
                            @error('duration_days')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Порядок сортировки</label>
                            <input type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror" 
                                id="sort_order" name="sort_order" value="{{ old('sort_order', $subscriptionPlan->sort_order ?? 0) }}">
                            @error('sort_order')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                {{ old('is_active', $subscriptionPlan->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Активен</label>
                        </div>
                    </div>
                </div>
                
                <!-- Возможности тарифа -->
                <div class="mt-4">
                    <h4 class="mb-3">Возможности тарифа</h4>
                    
                    <div id="features-container" class="mb-3">
                        @if(old('features'))
                            @foreach(old('features') as $feature)
                                <div class="feature-item input-group mb-2">
                                    <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="Введите возможность">
                                    <button type="button" class="btn btn-outline-danger remove-feature">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @elseif($subscriptionPlan->features)
                            @foreach($subscriptionPlan->features as $feature)
                                <div class="feature-item input-group mb-2">
                                    <input type="text" class="form-control" name="features[]" value="{{ $feature }}" placeholder="Введите возможность">
                                    <button type="button" class="btn btn-outline-danger remove-feature">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="feature-item input-group mb-2">
                                <input type="text" class="form-control" name="features[]" placeholder="Введите возможность">
                                <button type="button" class="btn btn-outline-danger remove-feature">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <button type="button" id="add-feature" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-plus me-1"></i> Добавить возможность
                    </button>
                </div>
                
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-outline-secondary me-2">Отмена</a>
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
            <p class="text-muted mb-3">Удаление тарифа возможно только если на него не подписан ни один пользователь.</p>
            
            <form method="POST" action="{{ route('admin.subscription-plans.destroy', $subscriptionPlan) }}" id="deletePlanForm">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-danger" onclick="confirmPlanDeletion()">
                    <i class="fa-solid fa-trash me-1"></i> Удалить тариф
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функционал добавления возможностей
    const featuresContainer = document.getElementById('features-container');
    const addFeatureBtn = document.getElementById('add-feature');
    
    // Шаблон строки возможности
    const featureTemplate = `
        <div class="feature-item input-group mb-2">
            <input type="text" class="form-control" name="features[]" placeholder="Введите возможность">
            <button type="button" class="btn btn-outline-danger remove-feature">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
    `;
    
    // Добавление новой возможности
    addFeatureBtn.addEventListener('click', function() {
        // Создаем временный контейнер и добавляем в него HTML
        const temp = document.createElement('div');
        temp.innerHTML = featureTemplate;
        
        // Добавляем созданный элемент в контейнер
        featuresContainer.appendChild(temp.firstElementChild);
        
        // Добавляем обработчик для кнопки удаления
        const removeButtons = document.querySelectorAll('.remove-feature');
        const lastRemoveButton = removeButtons[removeButtons.length - 1];
        
        lastRemoveButton.addEventListener('click', function() {
            this.closest('.feature-item').remove();
        });
    });
    
    // Привязка обработчиков к существующим кнопкам удаления
    document.querySelectorAll('.remove-feature').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.feature-item').remove();
        });
    });
});

function confirmPlanDeletion() {
    if (confirm('Вы уверены, что хотите удалить этот тариф? Это действие невозможно отменить.')) {
        document.getElementById('deletePlanForm').submit();
    }
}
</script>
@endsection
