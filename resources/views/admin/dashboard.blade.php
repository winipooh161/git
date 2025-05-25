@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Панель администратора</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Панель администратора</h1>
    </div>
    
    <!-- Карточки со статистикой -->
    <div class="row g-4 mb-4">
        <!-- Пользователи -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm hover-lift h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fa-solid fa-users text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Пользователи</h6>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $usersCount }}</h3>
                    <div class="small text-muted mt-2">
                        <span class="text-success">{{ $entrepreneursCount }}</span> предпринимателей
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">Управление</a>
                </div>
            </div>
        </div>
        
        <!-- Шаблоны документов -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm hover-lift h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fa-solid fa-palette text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Шаблоны</h6>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $templatesCount }}</h3>
                    <div class="small text-muted mt-2">
                        Доступно для использования
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="{{ route('admin.templates.index') }}" class="btn btn-sm btn-outline-success">Управление</a>
                </div>
            </div>
        </div>
        
        <!-- документы -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm hover-lift h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fa-solid fa-certificate text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">документы</h6>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $certificatesCount }}</h3>
                    <div class="small text-muted mt-2">
                        Создано пользователями
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="#" class="btn btn-sm btn-outline-info">Посмотреть</a>
                </div>
            </div>
        </div>
        
        <!-- Действия -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm hover-lift h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fa-solid fa-bolt text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Быстрые действия</h6>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa-solid fa-user-plus me-1"></i> Добавить пользователя
                        </a>
                        <a href="{{ route('admin.templates.create') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa-solid fa-plus me-1"></i> Новый шаблон
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Последние пользователи -->
        <div class="col-md-6">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Последние пользователи</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-link text-decoration-none">
                        Все пользователи
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Имя</th>
                                    <th>Email</th>
                                    <th>Роль</th>
                                    <th>Дата</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Пользователей нет</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Последние документы -->
        <div class="col-md-6">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Последние документы</h5>
                    <a href="#" class="btn btn-sm btn-link text-decoration-none">
                        Все документы
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>№</th>
                                    <th>Создатель</th>
                                    <th>Сумма</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestCertificates as $certificate)
                                    <tr>
                                        <td>{{ $certificate->certificate_number }}</td>
                                        <td>{{ $certificate->user->name }}</td>
                                        <td>{{ $certificate->amount }} руб.</td>
                                        <td>
                                            @if ($certificate->status == 'active')
                                                <span class="badge bg-success">Активен</span>
                                            @elseif ($certificate->status == 'used')
                                                <span class="badge bg-secondary">Использован</span>
                                            @elseif ($certificate->status == 'expired')
                                                <span class="badge bg-warning">Истек</span>
                                            @elseif ($certificate->status == 'canceled')
                                                <span class="badge bg-danger">Отменен</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">документов нет</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
