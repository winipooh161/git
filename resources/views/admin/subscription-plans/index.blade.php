@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель администратора</a></li>
            <li class="breadcrumb-item active" aria-current="page">Управление тарифами</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Управление тарифами</h1>
        <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Добавить тариф
        </a>
    </div>

    <!-- Сообщения об успехе или ошибке -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <!-- Таблица тарифов -->
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Стоимость</th>
                            <th>Длительность</th>
                            <th>Активен</th>
                            <th>Пользователей</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($plans as $plan)
                            <tr>
                                <td>{{ $plan->id }}</td>
                                <td>
                                    <strong>{{ $plan->name }}</strong><br>
                                    <small class="text-muted">{{ $plan->slug }}</small>
                                </td>
                                <td>{{ number_format($plan->price, 0, '.', ' ') }} ₽</td>
                                <td>
                                    @if ($plan->duration_days)
                                        {{ $plan->duration_days }} дн.
                                    @else
                                        <span class="badge bg-success">Бессрочный</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($plan->is_active)
                                        <span class="badge bg-success">Активен</span>
                                    @else
                                        <span class="badge bg-danger">Неактивен</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $plan->activeSubscriptions()->count() }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.subscription-plans.edit', $plan) }}" class="btn btn-outline-primary">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="if(confirm('Вы уверены? Это действие нельзя отменить.')) { document.getElementById('delete-plan-{{ $plan->id }}').submit(); }">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <form id="delete-plan-{{ $plan->id }}" action="{{ route('admin.subscription-plans.destroy', $plan) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Тарифы не найдены</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
