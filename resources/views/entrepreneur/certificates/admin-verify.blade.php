@extends('layouts.lk')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-0 rounded-4 shadow">
                <div class="card-body p-4">
                    <h2 class="card-title mb-4">Проверка документа</h2>
                    
                    @if (session('success'))
                        <div class="alert alert-success mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fa-solid fa-certificate fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">документ подтвержден</h5>
                                <p class="mb-0">документ #{{ $certificate->certificate_number }} является действительным.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <dl>
                                <dt>Номер документа</dt>
                                <dd>{{ $certificate->certificate_number }}</dd>
                                
                                <dt>Получатель</dt>
                                <dd>{{ $certificate->recipient_name }}</dd>
                                
                                <dt>Сумма</dt>
                                <dd>{{ number_format($certificate->amount, 0, '.', ' ') }} ₽</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl>
                                <dt>Статус</dt>
                                <dd>
                                    @if($certificate->status == 'active')
                                        <span class="badge bg-success">Активен</span>
                                    @elseif($certificate->status == 'used')
                                        <span class="badge bg-secondary">Использован</span>
                                    @elseif($certificate->status == 'expired')
                                        <span class="badge bg-warning">Истек</span>
                                    @elseif($certificate->status == 'canceled')
                                        <span class="badge bg-danger">Отменен</span>
                                    @endif
                                </dd>
                                
                                <dt>Срок действия</dt>
                                <dd>{{ $certificate->valid_from->format('d.m.Y') }} - {{ $certificate->valid_until->format('d.m.Y') }}</dd>
                                
                                <dt>Шаблон</dt>
                                <dd>{{ $certificate->template->name }}</dd>
                            </dl>
                        </div>
                    </div>
                    
                    @if($certificate->status == 'active')
                        <form action="{{ route('entrepreneur.certificates.mark-as-used', $certificate) }}" method="POST" class="text-center">
                            @csrf
                            <p class="mb-4">Подтвердите использование документа. Это действие изменит статус документа на "Использован".</p>
                            <button type="submit" class="btn btn-lg btn-primary">
                                <i class="fa-solid fa-check-circle me-2"></i>Подтвердить использование
                            </button>
                        </form>
                    @elseif($certificate->status == 'used')
                        <div class="text-center">
                            <div class="alert alert-success">
                                <i class="fa-solid fa-check-circle me-2"></i>документ уже был использован {{ $certificate->used_at ? $certificate->used_at->format('d.m.Y H:i') : 'ранее' }}
                            </div>
                            <a href="{{ route('entrepreneur.certificates.show', $certificate) }}" class="btn btn-primary">
                                <i class="fa-solid fa-eye me-2"></i>Просмотр деталей
                            </a>
                        </div>
                    @elseif($certificate->status == 'expired')
                        <div class="text-center">
                            <div class="alert alert-warning">
                                <i class="fa-solid fa-exclamation-circle me-2"></i>Срок действия документа истек
                            </div>
                            <a href="{{ route('entrepreneur.certificates.show', $certificate) }}" class="btn btn-primary">
                                <i class="fa-solid fa-eye me-2"></i>Просмотр деталей
                            </a>
                        </div>
                    @elseif($certificate->status == 'canceled')
                        <div class="text-center">
                            <div class="alert alert-danger">
                                <i class="fa-solid fa-times-circle me-2"></i>документ был отменен
                            </div>
                            <a href="{{ route('entrepreneur.certificates.show', $certificate) }}" class="btn btn-primary">
                                <i class="fa-solid fa-eye me-2"></i>Просмотр деталей
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent text-center p-3">
                    <div class="btn-group">
                        <a href="{{ route('entrepreneur.certificates.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-2"></i>К списку документов
                        </a>
                        <a href="{{ route('entrepreneur.certificates.show', $certificate) }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-eye me-2"></i>Просмотр документа
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
