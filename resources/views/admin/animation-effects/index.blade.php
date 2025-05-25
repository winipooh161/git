@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель управления</a></li>
            <li class="breadcrumb-item active" aria-current="page">Анимационные эффекты</li>
        </ol>
    </nav>

    <!-- Заголовок и кнопки действий -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Анимационные эффекты</h1>
        <a href="{{ route('admin.animation-effects.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Добавить эффект
        </a>
    </div>

    <!-- Уведомления -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif

    <!-- Таблица эффектов -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent py-3">
            <h5 class="card-title mb-0">Список анимационных эффектов</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" width="80">ID</th>
                            <th scope="col">Имя</th>
                            <th scope="col">Тип</th>
                            <th scope="col" class="text-center">Статус</th>
                            <th scope="col" class="text-end">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($animationEffects as $effect)
                            <tr>
                                <td>{{ $effect->id }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $effect->name }}</span>
                                        @if($effect->description)
                                            <span class="text-muted small">{{ Str::limit($effect->description, 50) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $typeLabels = [
                                            'emoji' => ['Эмодзи', 'primary'],
                                            'confetti' => ['Конфетти', 'info'],
                                            'snow' => ['Снег', 'light'],
                                            'fireworks' => ['Фейерверк', 'warning'],
                                            'bubbles' => ['Пузыри', 'success'],
                                            'leaves' => ['Листья', 'success'],
                                            'stars' => ['Звёзды', 'warning']
                                        ];
                                        $typeInfo = $typeLabels[$effect->type] ?? ['Эффект', 'secondary'];
                                    @endphp
                                    <span class="badge bg-{{ $typeInfo[1] }}-subtle text-{{ $typeInfo[1] }}">{{ $typeInfo[0] }}</span>
                                    
                                    @if(count($effect->particles) > 0)
                                        <div class="particles-preview mt-1">
                                            @foreach(array_slice($effect->particles, 0, 5) as $particle)
                                                <span class="me-1">{{ $particle }}</span>
                                            @endforeach
                                            @if(count($effect->particles) > 5)
                                                <span class="small text-muted">+{{ count($effect->particles) - 5 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.animation-effects.toggle-status', $effect) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $effect->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                            @if($effect->is_active)
                                                <i class="fa-solid fa-check-circle me-1"></i>Активен
                                            @else
                                                <i class="fa-solid fa-times-circle me-1"></i>Неактивен
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('admin.animation-effects.preview', $effect) }}" class="btn btn-sm btn-outline-info me-2" target="_blank">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.animation-effects.edit', $effect) }}" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.animation-effects.destroy', $effect) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Вы уверены, что хотите удалить этот эффект?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-ghost fa-3x text-muted mb-3"></i>
                                        <p class="mb-1">Анимационные эффекты не найдены</p>
                                        <a href="{{ route('admin.animation-effects.create') }}" class="btn btn-sm btn-primary mt-2">Создать первый эффект</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Пагинация -->
    <div class="d-flex justify-content-center mt-4 pagination">
        {{ $animationEffects->links() }}
    </div>
</div>

<style>
    .particles-preview {
        font-size: 1.2em;
        letter-spacing: 0.05em;
    }
</style>
@endsection
