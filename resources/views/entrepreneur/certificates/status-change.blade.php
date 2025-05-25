@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Изменение статуса сертификата</h4>
                </div>
                
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error') || isset($error))
                        <div class="alert alert-danger">
                            {{ session('error') ?? $error }}
                        </div>
                    @endif
                    
                    @if(isset($certificate))
                        <div class="mb-4 p-3 border rounded bg-light">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="mb-1">#{{ $certificate->certificate_number }}</h5>
                                    <p class="mb-1">{{ $certificate->template->name ?? 'Сертификат' }}</p>
                                    <p class="mb-1">
                                        <span class="fw-bold">Сумма:</span>
                                        {{ number_format($certificate->amount, 0, '.', ' ') }} 
                                        {{ $certificate->is_percent ? '%' : '₽' }}
                                    </p>
                                    
                                    @if($certificate->isClaimed())
                                    <p class="mb-1">
                                        <span class="fw-bold">Получатель:</span> 
                                        {{ $certificate->recipient_name ?? 'Не указан' }}
                                    </p>
                                    @if($certificate->recipient_email)
                                    <p class="mb-1">
                                        <span class="fw-bold">Email:</span> 
                                        {{ $certificate->recipient_email }}
                                    </p>
                                    @endif
                                    @if($certificate->recipient_phone)
                                    <p class="mb-1">
                                        <span class="fw-bold">Телефон:</span> 
                                        {{ $certificate->recipient_phone }}
                                    </p>
                                    @endif
                                    <p class="mb-0">
                                        <span class="badge bg-success">Сертификат активирован</span>
                                    </p>
                                    @else
                                    <p class="mb-0">
                                        <span class="badge bg-warning text-dark">Сертификат не активирован</span>
                                    </p>
                                    @endif
                                    <p class="mb-1 mt-2">
                                        <span class="fw-bold">Действует до:</span> 
                                        {{ $certificate->valid_until->format('d.m.Y') }}
                                    </p>
                                </div>
                                <div>
                                    <span class="badge {{ $certificate->status === 'active' ? 'bg-success' : 'bg-secondary' }} fs-6">
                                        {{ $certificate->status === 'active' ? 'Активен' : 'Использован' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        @if(!isset($readonly) || !$readonly)
                        <form action="{{ route('entrepreneur.certificates.update-status-qr') }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="certificate_id" value="{{ $certificate->id }}">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Изменить статус сертификата:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" value="active" id="statusActive" 
                                        {{ $certificate->status === 'active' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusActive">
                                        Активен
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" value="used" id="statusUsed"
                                        {{ $certificate->status === 'used' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusUsed">
                                        Использован
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-check-circle"></i> Изменить статус
                                </button>
                                
                                <a href="{{ route('entrepreneur.certificates.show', $certificate) }}" class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-eye"></i> Просмотреть детали сертификата
                                </a>
                            </div>
                        </form>
                        @else
                        <div class="alert alert-info">
                            <p class="mb-2">Вы не можете изменить статус этого сертификата, так как не являетесь его создателем.</p>
                            <a href="{{ route('entrepreneur.certificates.index') }}" class="btn btn-outline-primary">
                                <i class="fa-solid fa-list"></i> Вернуться к списку сертификатов
                            </a>
                        </div>
                        @endif
                        
                        <div class="mt-4 text-center">
                            <p class="text-muted">
                                <small>QR-код создан {{ now()->format('d.m.Y в H:i') }}</small>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
