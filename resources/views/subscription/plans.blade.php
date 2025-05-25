@extends('layouts.lk')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Тарифные планы</h1>
    
    <!-- Информация о стиках -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <h2 class="mb-0"><i class="fas fa-certificate text-warning me-2"></i> Стики</h2>
                <div class="ms-auto fs-4">
                    <span class="badge bg-primary rounded-pill p-2">У вас: {{ Auth::user()->sticks }} стиков</span>
                </div>
            </div>
            <p>Стики — это валюта для создания сертификатов. Каждый созданный сертификат требует 1 стик.</p>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Стики выдаются при активации тарифного плана. Чем выше тариф, тем больше стиков вы получите.
            </div>
        </div>
    </div>
    
    <!-- Текущий тариф -->
    @if($currentPlan)
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h2 class="h5 mb-0">Ваш текущий тариф</h2>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h3 class="mb-0">{{ $currentPlan->name }}</h3>
                    <p class="text-muted mb-2">Активен до: {{ $activeSubscription && $activeSubscription->end_date ? $activeSubscription->end_date->format('d.m.Y') : 'Бессрочно' }}</p>
                </div>
                <div class="ms-auto">
                    <span class="badge bg-success rounded-pill p-2 fs-6">
                        <i class="fas fa-certificate me-1"></i> {{ $currentPlan->sticks_amount }} стиков
                    </span>
                </div>
            </div>
            
            <div class="mt-3">
                <p>Возможности тарифа:</p>
                <ul class="list-group">
                    @if(is_array($currentPlan->features))
                        @foreach($currentPlan->features as $feature)
                            <li class="list-group-item border-0 ps-0 py-1">
                                <i class="fas fa-check text-success me-2"></i> {{ $feature }}
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Доступные тарифы -->
    <h2 class="mb-3">Доступные тарифы</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($plans as $plan)
        <div class="col">
            <div class="card h-100 {{ $currentPlan && $currentPlan->id === $plan->id ? 'border-primary' : '' }}">
                <div class="card-header {{ $currentPlan && $currentPlan->id === $plan->id ? 'bg-primary text-white' : 'bg-light' }}">
                    <h3 class="h5 mb-0">{{ $plan->name }}</h3>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <span class="display-6">{{ number_format($plan->price, 0, ',', ' ') }} ₽</span>
                        <small class="text-muted">/ {{ $plan->duration_days ? $plan->duration_days . ' дней' : 'месяц' }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <span class="badge bg-warning p-2 fs-6">
                            <i class="fas fa-certificate me-1"></i> {{ $plan->sticks_amount }} стиков
                        </span>
                    </div>
                    
                    <ul class="list-group list-group-flush flex-grow-1">
                        @if(is_array($plan->features))
                            @foreach($plan->features as $feature)
                                <li class="list-group-item border-0 ps-0 py-1">
                                    <i class="fas fa-check text-success me-2"></i> {{ $feature }}
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    
                    <div class="mt-auto">
                        @if($currentPlan && $currentPlan->id === $plan->id)
                            <button class="btn btn-outline-primary w-100" disabled>Активен</button>
                        @else
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#subscribePlanModal{{ $plan->id }}">Активировать</button>
                            
                            <!-- Модальное окно для подтверждения подписки -->
                            <div class="modal fade" id="subscribePlanModal{{ $plan->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Подтверждение подписки</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Вы собираетесь активировать тарифный план <strong>{{ $plan->name }}</strong>.</p>
                                            <p>Стоимость: <strong>{{ number_format($plan->price, 0, ',', ' ') }} ₽</strong></p>
                                            <p>Вы получите: <strong>{{ $plan->sticks_amount }} стиков</strong></p>
                                            
                                            <form action="{{ route('subscription.subscribe', $plan) }}" method="POST" id="subscribePlanForm{{ $plan->id }}">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="form-label">Способ оплаты</label>
                                                    <select name="payment_method" class="form-select">
                                                        <option value="card">Банковская карта</option>
                                                        <option value="qiwi">QIWI Кошелек</option>
                                                        <option value="yoomoney">ЮMoney</option>
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                            <button type="submit" form="subscribePlanForm{{ $plan->id }}" class="btn btn-primary">Оплатить и активировать</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    @if($currentPlan && $currentPlan->slug !== 'start')
    <div class="mt-4">
        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelSubscriptionModal">
            <i class="fas fa-times"></i> Отменить подписку
        </button>
        
        <!-- Модальное окно для подтверждения отмены подписки -->
        <div class="modal fade" id="cancelSubscriptionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Подтверждение отмены подписки</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Вы собираетесь отменить свою подписку на тарифный план <strong>{{ $currentPlan->name }}</strong>.
                        </div>
                        <p>После отмены текущей подписки:</p>
                        <ul>
                            <li>Вам будет назначен базовый тариф "Старт"</li>
                            <li>Ваши оставшиеся стики сохранятся</li>
                            <li>Премиум возможности будут недоступны</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <form action="{{ route('subscription.cancel') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">Подтвердить отмену</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
