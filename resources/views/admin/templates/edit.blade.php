@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель администратора</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.templates.index') }}">Шаблоны документов</a></li>
            <li class="breadcrumb-item active" aria-current="page">Редактирование шаблона</li>
        </ol>
    </nav>
    
    <h1 class="mb-4">Редактирование шаблона документа</h1>
    
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.templates.update', $template) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <!-- Основная информация -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Основная информация</h4>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Название шаблона *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $template->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Категория шаблона *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                id="category_id" name="category_id" required>
                                <option value="">-- Выберите категорию --</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $template->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description', $template->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Изображение предпросмотра</label>
                            
                            @if ($template->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $template->image) }}" class="img-thumbnail" style="max-height: 150px;" alt="{{ $template->name }}">
                                </div>
                            @endif
                            
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                id="image" name="image" accept="image/*">
                            <div class="form-text">Оставьте пустым, чтобы сохранить текущее изображение. Рекомендуемый размер: 800x600px, максимум 7MB.</div>
                            @error('image')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_premium" name="is_premium" value="1" 
                                    {{ old('is_premium', $template->is_premium) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_premium">Премиум шаблон</label>
                            </div>
                            <div class="form-text">Премиум шаблоны доступны только клиентам с премиум подпиской.</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                    {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Активен</label>
                            </div>
                            <div class="form-text">Неактивные шаблоны не будут показываться клиентам.</div>
                        </div>
                    </div>
                    
                    <!-- Выбор HTML файла шаблона -->
                    <div class="col-md-6">
                        <h4 class="mb-3">Файл шаблона</h4>
                        
                        <div class="mb-3">
                            <label for="template_path" class="form-label">Выберите PHP файл шаблона *</label>
                            <select class="form-select select2-template-picker @error('template_path') is-invalid @enderror" 
                                id="template_path" name="template_path" required
                                data-placeholder="Поиск шаблона..." data-allow-clear="true">
                                <option value="">-- Выберите файл шаблона --</option>
                                @foreach($templateFiles['files'] as $categoryId => $files)
                                    @php 
                                        $category = $templateFiles['categories']->firstWhere('id', $categoryId);
                                    @endphp
                                    <optgroup label="{{ $category ? $category->name : 'Другие' }}" data-category="{{ $categoryId }}">
                                    @foreach($files as $path => $name)
                                        <option value="{{ $path }}" {{ old('template_path', $template->template_path) == $path ? 'selected' : '' }}
                                                data-path="{{ $path }}"
                                                data-category="{{ $categoryId }}">
                                            {{ $name }} <small class="text-muted">({{ $path }})</small>
                                        </option>
                                    @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('template_path')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <div class="form-text">
                                PHP файлы шаблонов должны находиться в директории /public/templates/ или ее подпапках
                            </div>
                        </div>
                        
                        <!-- Элементы быстрого фильтра по категориям -->
                        <div class="mb-4 template-category-filters">
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="badge bg-secondary category-filter-badge active" data-category="all">
                                    <i class="fa-solid fa-filter me-1"></i>Все
                                </span>
                                @foreach($templateFiles['categories'] as $category)
                                    <span class="badge bg-primary category-filter-badge" 
                                          data-category="{{ $category->id }}"
                                          {{ $template->category_id == $category->id ? 'class=active' : '' }}>
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Предпросмотр шаблона -->
                        <div class="mt-4">
                            <h5>Предпросмотр выбранного шаблона</h5>
                            <div class="template-preview border rounded overflow-hidden mt-2 mb-4">
                                <iframe id="template-preview-frame" src="{{ asset($template->template_path) }}" frameborder="0" 
                                    style="width:100%; height:300px;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-4 mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary me-2">Отмена</a>
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
            <p class="text-muted mb-3">Если вы удалите этот шаблон, он будет недоступен для всех пользователей. Если шаблон используется в существующих документах, удаление может привести к ошибкам отображения.</p>
            
            <form method="POST" action="{{ route('admin.templates.destroy', $template) }}" id="deleteTemplateForm">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-danger" onclick="confirmTemplateDeletion()">
                    <i class="fa-solid fa-trash me-1"></i> Удалить шаблон
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    const templatePathSelect = document.getElementById('template_path');
    const previewFrame = document.getElementById('template-preview-frame');
    
    // Фильтрация шаблонов по категории
    categorySelect.addEventListener('change', function() {
        const selectedCategory = this.value;
        const optgroups = templatePathSelect.querySelectorAll('optgroup');
        
        optgroups.forEach(group => {
            const category = group.getAttribute('data-category');
            if (category === selectedCategory) {
                group.style.display = '';
                // Сбрасываем текущий выбор
                templatePathSelect.value = '';
                updatePreviewVisibility();
            } else {
                group.style.display = 'none';
            }
        });
    });
    
    // Обработчик изменения выбранного шаблона
    templatePathSelect.addEventListener('change', function() {
        const templatePath = this.value;
        
        if (templatePath) {
            previewFrame.src = `/${templatePath}`;
        }
    });
    
    // Если категория уже выбрана, фильтруем шаблоны
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
});

function confirmTemplateDeletion() {
    if (confirm('Вы уверены, что хотите удалить этот шаблон? Это действие может привести к ошибкам в существующих документах.')) {
        document.getElementById('deleteTemplateForm').submit();
    }
}
</script>

<!-- Добавляем CSS для Select2 -->
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* Кастомные стили для Select2 */
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        border-color: #dee2e6;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
    }
    
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option--highlighted {
        background-color: #0d6efd;
        color: #fff;
    }
    
    .select2-container--bootstrap-5 .select2-results__option {
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .select2-container--bootstrap-5 .select2-results__option:last-child {
        border-bottom: none;
    }
    
    .select2-container--bootstrap-5 .select2-results__option small {
        opacity: 0.7;
        font-size: 0.85em;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Стили для бейджей категорий */
    .category-filter-badge {
        cursor: pointer;
        transition: all 0.2s ease;
        opacity: 0.7;
    }
    
    .category-filter-badge:hover {
        opacity: 1;
        transform: translateY(-1px);
    }
    
    .category-filter-badge.active {
        opacity: 1;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    /* Делаем шрифт меньше для длинных путей */
    .template-path-item {
        font-size: 0.85em;
        color: #6c757d;
    }
</style>
@endpush

<!-- Добавляем JavaScript для Select2 и нашей кастомной логики -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ru.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация Select2 с улучшенным UI и поиском
    $('.select2-template-picker').select2({
        theme: 'bootstrap-5',
        language: 'ru',
        width: '100%',
        placeholder: 'Поиск шаблона...',
        allowClear: true,
        templateResult: formatTemplateResult,
        templateSelection: formatTemplateSelection,
        escapeMarkup: function(m) { return m; }
    });
    
    // Функция форматирования опций в выпадающем списке
    function formatTemplateResult(template) {
        if (!template.id) return template.text;
        
        const path = $(template.element).data('path') || '';
        const pathParts = path.split('/');
        const fileName = pathParts.pop() || '';
        const directory = pathParts.join('/');
        
        return `<div class="d-flex flex-column">
                    <strong>${template.text}</strong>
                    <span class="template-path-item">${directory}/${fileName}</span>
                </div>`;
    }
    
    // Функция форматирования выбранного элемента
    function formatTemplateSelection(template) {
        if (!template.id) return template.text;
        return `<span>${template.text}</span>`;
    }
    
    // Логика для предпросмотра шаблона и фильтрации по категориям
    const categorySelect = document.getElementById('category_id');
    const templatePathSelect = document.getElementById('template_path');
    const previewFrame = document.getElementById('template-preview-frame');
    
    // Обновляем предпросмотр при изменении выбранного шаблона через Select2
    $('.select2-template-picker').on('change', function() {
        const templatePath = $(this).val();
        
        if (templatePath) {
            previewFrame.src = `/${templatePath}`;
        }
    });
    
    // Фильтрация шаблонов по категории
    categorySelect.addEventListener('change', function() {
        const selectedCategory = this.value;
        const optgroups = templatePathSelect.querySelectorAll('optgroup');
        
        optgroups.forEach(group => {
            const category = group.getAttribute('data-category');
            if (category === selectedCategory) {
                group.style.display = '';
                // Сбрасываем текущий выбор
                templatePathSelect.value = '';
                
                // Обновляем Select2
                $('.select2-template-picker').val('').trigger('change');
            } else {
                group.style.display = 'none';
            }
        });
    });
    
    // Обработчик для бейджей быстрой фильтрации по категориям
    const categoryBadges = document.querySelectorAll('.category-filter-badge');
    categoryBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category');
            
            // Обновляем активный статус бейджей
            categoryBadges.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Если выбраны "Все категории"
            if (categoryId === 'all') {
                // Показываем все группы
                const allOptgroups = templatePathSelect.querySelectorAll('optgroup');
                allOptgroups.forEach(group => {
                    $(group).prop('disabled', false);
                });
            } else {
                // Скрываем ненужные группы и показываем только выбранную категорию
                const allOptgroups = templatePathSelect.querySelectorAll('optgroup');
                allOptgroups.forEach(group => {
                    const groupCategory = group.getAttribute('data-category');
                    if (groupCategory === categoryId) {
                        $(group).prop('disabled', false);
                        
                        // По возможности, выбираем первый шаблон из этой категории
                        const firstOption = group.querySelector('option');
                        if (firstOption) {
                            $('.select2-template-picker').val(firstOption.value).trigger('change');
                        }
                    } else {
                        $(group).prop('disabled', true);
                    }
                });
            }
            
            // Обновляем Select2 после фильтрации
            $('.select2-template-picker').select2('destroy').select2({
                theme: 'bootstrap-5',
                language: 'ru',
                width: '100%',
                placeholder: 'Поиск шаблона...',
                allowClear: true,
                templateResult: formatTemplateResult,
                templateSelection: formatTemplateSelection,
                escapeMarkup: function(m) { return m; }
            });
        });
    });
    
    // Если категория уже выбрана, фильтруем шаблоны
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
