@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Ошибка сервера (500)</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fa fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                        <h5>Что-то пошло не так</h5>
                        <p class="text-muted">Произошла внутренняя ошибка сервера. Пожалуйста, попробуйте позже.</p>
                    </div>
                    
                    @if(config('app.debug'))
                    <div class="alert alert-info">
                        <p><strong>Отладочная информация:</strong></p>
                        <pre>{{ $exception->getMessage() }}</pre>
                        <p><strong>Стек вызовов:</strong></p>
                        <pre>{{ $exception->getTraceAsString() }}</pre>
                    </div>
                    @endif
                    
                    <div class="text-center">
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="fa fa-home me-2"></i>Вернуться на главную
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
