@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель администратора</a></li>
            <li class="breadcrumb-item active" aria-current="page">Шаблоны документов</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Шаблоны документов</h1>
        <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i> Добавить шаблон
        </a>
    </div>
    
    <!-- Сообщения об успешных операциях -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <!-- Фильтр по типу документа -->
    <div class="mb-4">
        <div class="btn-group" role="group" aria-label="Фильтр по типу документа">
            <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary {{ !request()->has('type') ? 'active' : '' }}">
                Все типы
            </a>
            @foreach($documentTypes as $type => $label)
                <a href="{{ route('admin.templates.index', ['type' => $type]) }}" 
                   class="btn btn-outline-secondary {{ request('type') == $type ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
    
    <div class="row g-4">
        @forelse ($templates as $template)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card position-relative h-100">
                    <div class="position-relative">
                        <img src="{{ $template->image_url }}" class="card-img-top" alt="{{ $template->name }}">
                        
                        @if ($template->is_premium)
                            <span class="badge bg-warning position-absolute top-0 end-0 m-3">Премиум</span>
                        @endif
                        
                        @if (!$template->is_active)
                            <div class="position-absolute top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center rounded-top-4" style="background-color: rgba(0,0,0,0.5);">
                                <span class="badge bg-danger px-3 py-2">Отключен</span>
                            </div>
                        @endif
                        
                        <!-- Отображаем тип документа -->
                        <span class="badge bg-info position-absolute top-0 start-0 m-3">
                            {{ $template->document_type_label }}
                        </span>
                    </div>
                    
                    <div class="card-body">
                        <h5 class="card-title">{{ $template->name }}</h5>
                        <p class="card-text text-muted small">{{ Str::limit($template->description, 100) }}</p>
                        
                        <!-- Показываем категорию шаблона -->
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fa-solid fa-folder me-1"></i> 
                                {{ $template->category->name ?? 'Без категории' }}
                            </small>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-pen-to-square"></i> Редактировать
                                </a>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="if(confirm('Вы уверены?')) { document.getElementById('delete-template-{{ $template->id }}').submit(); }">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <form id="delete-template-{{ $template->id }}" action="{{ route('admin.templates.destroy', $template) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isActive{{ $template->id }}" 
                                       {{ $template->is_active ? 'checked' : '' }} data-template-id="{{ $template->id }}">
                                <label class="form-check-label small" for="isActive{{ $template->id }}">Активен</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fa-regular fa-face-frown text-muted fa-3x mb-3"></i>
                <p class="mb-0">Шаблоны не найдены</p>
            </div>
        @endforelse
    </div>
    
    <!-- Пагинация -->
    <div class="mt-4 pagination">
        {{ $templates->links() }}
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключатель активности шаблона
    document.querySelectorAll('.form-check-input').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const templateId = this.dataset.templateId;
            const isActive = this.checked;
            
            fetch(`/admin/templates/${templateId}/toggle-active`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка сети');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Обновить UI
                    const templateCard = this.closest('.card');
                    const imageContainer = templateCard.querySelector('.position-relative');
                    const existingOverlay = imageContainer.querySelector('.position-absolute.top-0.start-0.end-0.bottom-0');
                    
                    if (isActive) {
                        if (existingOverlay) existingOverlay.remove();
                    } else {
                        if (!existingOverlay) {
                            const newOverlay = document.createElement('div');
                            newOverlay.className = 'position-absolute top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center rounded-top-4';
                            newOverlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
                            newOverlay.innerHTML = '<span class="badge bg-danger px-3 py-2">Отключен</span>';
                            imageContainer.appendChild(newOverlay);
                        }
                    }
                    
                    // Показать уведомление
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                    alertDiv.setAttribute('role', 'alert');
                    alertDiv.innerHTML = `
                        Статус шаблона успешно изменен
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    document.body.appendChild(alertDiv);
                    
                    // Автоматически скрыть уведомление через 3 секунды
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alertDiv);
                        bsAlert.close();
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при обновлении статуса шаблона');
                // Вернуть чекбокс в исходное состояние
                this.checked = !this.checked;
            });
        });
    });
});
</script>
@endsection