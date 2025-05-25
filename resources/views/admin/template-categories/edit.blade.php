@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель администратора</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.template-categories.index') }}">Категории шаблонов</a></li>
            <li class="breadcrumb-item active" aria-current="page">Редактирование категории</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Редактирование категории шаблонов</h1>
    
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.template-categories.update', $templateCategory) }}">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Название категории *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $templateCategory->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="directory_name" class="form-label">Название директории *</label>
                            <div class="input-group">
                                <span class="input-group-text">templates/</span>
                                <input type="text" class="form-control @error('directory_name') is-invalid @enderror" 
                                    id="directory_name" name="directory_name" value="{{ old('directory_name', $templateCategory->directory_name) }}" required>
                            </div>
                            <div class="form-text">
                                При изменении будет создана новая директория. Шаблоны придется перенести вручную.
                            </div>
                            @error('directory_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Добавляем поле для выбора типа документа -->
                        <div class="mb-3">
                            <label for="document_type" class="form-label">Тип документа *</label>
                            <select class="form-select @error('document_type') is-invalid @enderror" 
                                id="document_type" name="document_type" required>
                                @foreach($documentTypes as $type => $label)
                                    <option value="{{ $type }}" {{ old('document_type', $templateCategory->document_type ?? 'certificate') == $type ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Тип документа определяет, для каких целей будут использоваться шаблоны этой категории
                            </div>
                            @error('document_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Порядок сортировки</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                id="sort_order" name="sort_order" value="{{ old('sort_order', $templateCategory->sort_order) }}" min="0">
                            <div class="form-text">
                                Чем меньше число, тем выше категория будет отображаться в списке
                            </div>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="4">{{ old('description', $templateCategory->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                    {{ old('is_active', $templateCategory->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Активна</label>
                            </div>
                            <div class="form-text">
                                Неактивные категории не будут показываться пользователям
                            </div>
                        </div>
                        
                        <div class="card bg-light mt-3">
                            <div class="card-body">
                                <h6 class="card-title">Информация:</h6>
                                <p class="card-text mb-0">Количество шаблонов: <strong>{{ $templateCategory->templates()->count() }}</strong></p>
                                <p class="card-text mb-0">Слаг: <code>{{ $templateCategory->slug }}</code></p>
                                <p class="card-text mb-0">Создана: {{ $templateCategory->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.template-categories.index') }}" class="btn btn-outline-secondary me-2">
                            Отмена
                        </a>
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
            <p class="text-muted mb-3">
                Удаление категории невозможно, если в ней содержатся шаблоны. 
                Перед удалением переместите все шаблоны в другую категорию.
            </p>
            
            <form method="POST" action="{{ route('admin.template-categories.destroy', $templateCategory) }}" id="deleteForm">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                    <i class="fa-solid fa-trash me-1"></i> Удалить категорию
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Вы уверены, что хотите удалить эту категорию? Это действие нельзя отменить. Все шаблоны в категории должны быть сначала перемещены.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection
