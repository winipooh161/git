@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель администратора</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.template-categories.index') }}">Категории шаблонов</a></li>
            <li class="breadcrumb-item active" aria-current="page">Создание категории</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Создание новой категории шаблонов</h1>
    
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.template-categories.store') }}">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Название категории *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="directory_name" class="form-label">Название директории *</label>
                            <div class="input-group">
                                <span class="input-group-text">templates/</span>
                                <input type="text" class="form-control @error('directory_name') is-invalid @enderror" 
                                    id="directory_name" name="directory_name" value="{{ old('directory_name') }}" required>
                            </div>
                            <div class="form-text">
                                Латинские буквы, цифры и дефисы. Пример: "business", "holiday-cards"
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
                                    <option value="{{ $type }}" {{ old('document_type') == $type ? 'selected' : '' }}>
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
                                id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
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
                                id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                    {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Активна</label>
                            </div>
                            <div class="form-text">
                                Неактивные категории не будут показываться пользователям
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            При создании категории автоматически будет создана соответствующая директория 
                            в папке <code>public/templates/</code>, если она еще не существует.
                            Файлы шаблонов должны иметь расширение <code>.php</code>.
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.template-categories.index') }}" class="btn btn-outline-secondary me-2">
                            Отмена
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i> Сохранить категорию
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Автоматически создаем slug для директории из названия
    const nameInput = document.getElementById('name');
    const directoryInput = document.getElementById('directory_name');
    
    nameInput.addEventListener('input', function() {
        // Только если поле директории пустое или не было изменено пользователем
        if (!directoryInput.dataset.userModified) {
            const slug = nameInput.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '') // Удаляем специальные символы
                .replace(/[\s_-]+/g, '-') // Заменяем пробелы и подчеркивания на дефисы
                .replace(/^-+|-+$/g, ''); // Удаляем начальные и конечные дефисы
                
            directoryInput.value = slug;
        }
    });
    
    // Отмечаем, что пользователь изменил значение вручную
    directoryInput.addEventListener('input', function() {
        directoryInput.dataset.userModified = 'true';
    });
});
</script>
@endsection
