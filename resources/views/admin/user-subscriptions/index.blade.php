@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Панель администратора</a></li>
            <li class="breadcrumb-item active" aria-current="page">Управление подписками пользователей</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Подписки пользователей</h1>
        <a href="{{ route('admin.user-subscriptions.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-2"></i>Назначить подписку
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
    
    <!-- Таблица подписок -->
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Пользователь</th>
                            <th>Тариф</th>
                            <th>Дата начала</th>
                            <th>Дата окончания</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subscriptions as $subscription)
                            <tr>
                                <td>{{ $subscription->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($subscription->user->avatar)
                                            <img src="{{ asset('storage/' . $subscription->user->avatar) }}" alt="Avatar" 
                                                class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                                style="width: 32px; height: 32px;">
                                                {{ substr($subscription->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $subscription->user->name }}</strong><br>
                                            <small class="text-muted">{{ $subscription->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $subscription->subscriptionPlan->name }}</strong><br>
                                    <small class="text-muted">{{ number_format($subscription->subscriptionPlan->price, 0, '.', ' ') }} ₽</small>
                                </td>
                                <td>{{ $subscription->start_date->format('d.m.Y') }}</td>
                                <td>
                                    @if ($subscription->end_date)
                                        {{ $subscription->end_date->format('d.m.Y') }}
                                        @if ($subscription->end_date->isPast())
                                            <span class="badge bg-danger">Истек</span>
                                        @elseif ($subscription->end_date->diffInDays(now()) < 7)
                                            <span class="badge bg-warning">Скоро истекает</span>
                                        @endif
                                    @else
                                        <span class="badge bg-success">Бессрочный</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($subscription->is_active)
                                        <span class="badge bg-success">Активна</span>
                                    @else
                                        <span class="badge bg-secondary">Неактивна</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.user-subscriptions.edit', $subscription) }}" class="btn btn-outline-primary">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="if(confirm('Вы уверены? Это действие нельзя отменить.')) { document.getElementById('delete-subscription-{{ $subscription->id }}').submit(); }">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <form id="delete-subscription-{{ $subscription->id }}" action="{{ route('admin.user-subscriptions.destroy', $subscription) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Подписки не найдены</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Пагинация -->
            @if ($subscriptions->hasPages())
                <div class="mt-4">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
