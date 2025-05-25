@extends('layouts.lk')

@section('content')
<div class="container-fluid py-3 py-md-4">
    <!-- Хлебные крошки -->
    <nav aria-label="breadcrumb" class="mb-2 mb-md-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('entrepreneur.certificates.index') }}">Мои документы</a></li>
            <li class="breadcrumb-item"><a href="{{ route('entrepreneur.certificates.show', $certificate) }}">Просмотр документа</a></li>
            <li class="breadcrumb-item active" aria-current="page">Подготовка к печати</li>
        </ol>
    </nav>
    
    <h1 class="fs-4 fs-md-3 fw-bold mb-3 mb-md-4">Печать документа № {{ $certificate->certificate_number }}</h1>
    
    <div class="row g-3 g-md-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <h5 class="card-title mb-3">Настройка параметров печати</h5>
                    
                    @if (session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif
                    
                    <form method="POST" action="{{ route('entrepreneur.certificates.print.generate', $certificate) }}" target="_blank">
                        @csrf
                        
                        <div class="row g-3 mb-4">
                            <!-- Формат бумаги -->
                            <div class="col-md-6">
                                <label for="format" class="form-label">Формат бумаги</label>
                                <select class="form-select @error('format') is-invalid @enderror" id="format" name="format" required>
                                    <option value="a4" selected>A4 (210 × 297 мм)</option>
                                    <option value="a5">A5 (148 × 210 мм)</option>
                                    <option value="a6">A6 (105 × 148 мм)</option>
                                    <option value="letter">Letter (215.9 × 279.4 мм)</option>
                                    <option value="legal">Legal (215.9 × 355.6 мм)</option>
                                    <option value="custom">Произвольный размер</option>
                                </select>
                                @error('format')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Ориентация -->
                            <div class="col-md-6">
                                <label for="orientation" class="form-label">Ориентация</label>
                                <select class="form-select @error('orientation') is-invalid @enderror" id="orientation" name="orientation" required>
                                    <option value="landscape" selected>Альбомная</option>
                                    <option value="portrait">Книжная</option>
                                </select>
                                @error('orientation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Произвольный размер (скрыт по умолчанию) -->
                            <div class="col-12" id="customSizeContainer" style="display: none;">
                                <div class="card border bg-light p-3">
                                    <h6 class="mb-3">Произвольный размер</h6>
                                    <div class="row g-3">
                                        <div class="col-4">
                                            <label for="custom_width" class="form-label">Ширина</label>
                                            <input type="number" class="form-control @error('custom_width') is-invalid @enderror" 
                                                id="custom_width" name="custom_width" value="{{ old('custom_width', 210) }}" min="50" max="1000" step="1">
                                            @error('custom_width')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-4">
                                            <label for="custom_height" class="form-label">Высота</label>
                                            <input type="number" class="form-control @error('custom_height') is-invalid @enderror" 
                                                id="custom_height" name="custom_height" value="{{ old('custom_height', 297) }}" min="50" max="1000" step="1">
                                            @error('custom_height')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-4">
                                            <label for="unit" class="form-label">Единицы</label>
                                            <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit">
                                                <option value="mm" selected>мм</option>
                                                <option value="cm">см</option>
                                                <option value="inch">дюймы</option>
                                            </select>
                                            @error('unit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mb-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fa-solid fa-info-circle fa-lg"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="alert-heading mb-1">Рекомендации по печати</h6>
                                    <p class="mb-0 small">Для достижения наилучшего качества печати рекомендуется использовать плотную бумагу (от 200 г/м²) или фотобумагу. При печати в PDF-файле убедитесь, что масштаб установлен на 100% (реальный размер).</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('entrepreneur.certificates.show', $certificate) }}" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-arrow-left me-2"></i>Назад
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-file-pdf me-2"></i>Создать PDF для печати
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card border-0 rounded-4 shadow-sm mt-3">
                <div class="card-body p-3 p-md-4">
                    <h5 class="card-title mb-3">Предпросмотр документа</h5>
                    <div class="preview-container bg-light rounded p-3" style="position: relative; overflow: hidden;">
                        <div id="preview-wrapper" style="transform-origin: top left; transition: all 0.3s ease;">
                            <iframe id="preview-frame" src="{{ route('template.preview', [
                                'template' => $certificate->template,
                                'recipient_name' => $certificate->recipient_name,
                                'amount' => number_format($certificate->amount, 0, '.', ' ') . ' ₽',
                                'valid_from' => $certificate->valid_from->format('d.m.Y'),
                                'valid_until' => $certificate->valid_until->format('d.m.Y'),
                                'message' => $certificate->message ?? '',
                                'certificate_number' => $certificate->certificate_number,
                                'company_name' => $certificate->user->company ?? config('app.name')
                            ]) }}" frameborder="0" style="width:100%; height:400px; border: none;"></iframe>
                        </div>
                        <div id="paper-size-indicator" class="position-absolute top-0 start-0 w-100 h-100 border border-primary border-2" style="pointer-events: none; opacity: 0.2;"></div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">Размер и масштаб в предпросмотре приблизительны</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-3 p-md-4">
                    <h5 class="card-title mb-3">Информация о документе</h5>
                    
                    <div class="mb-3">
                        <img src="{{ $certificate->template->image ? asset('storage/' . $certificate->template->image) : asset('images/certificate-placeholder.jpg') }}" 
                             class="img-fluid rounded-3" alt="{{ $certificate->template->name }}">
                    </div>
                    
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Шаблон:</dt>
                        <dd class="col-sm-7">{{ $certificate->template->name }}</dd>
                        
                        <dt class="col-sm-5">Номинал:</dt>
                        <dd class="col-sm-7">{{ number_format($certificate->amount, 0, '.', ' ') }} ₽</dd>
                        
                        <dt class="col-sm-5">Получатель:</dt>
                        <dd class="col-sm-7">{{ $certificate->recipient_name }}</dd>
                        
                        <dt class="col-sm-5">Действителен до:</dt>
                        <dd class="col-sm-7">{{ $certificate->valid_until->format('d.m.Y') }}</dd>
                        
                        <dt class="col-sm-5">Статус:</dt>
                        <dd class="col-sm-7">
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
                    </dl>
                </div>
            </div>
            
            <div class="card border-0 rounded-4 shadow-sm mt-3">
                <div class="card-body p-3 p-md-4">
                    <h5 class="card-title mb-3">Быстрая печать</h5>
                    
                    <p class="text-muted small mb-3">Выберите готовый размер для быстрой печати:</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('entrepreneur.certificates.print.generate', [$certificate, 'format' => 'a4', 'orientation' => 'landscape']) }}" 
                           class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="fa-solid fa-file-pdf me-2"></i>A4 Альбомная
                        </a>
                        <a href="{{ route('entrepreneur.certificates.print.generate', [$certificate, 'format' => 'a5', 'orientation' => 'landscape']) }}" 
                           class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="fa-solid fa-file-pdf me-2"></i>A5 Альбомная
                        </a>
                        <a href="{{ route('entrepreneur.certificates.print.generate', [$certificate, 'format' => 'a6', 'orientation' => 'landscape']) }}" 
                           class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="fa-solid fa-file-pdf me-2"></i>A6 Альбомная
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatSelect = document.getElementById('format');
    const customSizeContainer = document.getElementById('customSizeContainer');
    const orientationSelect = document.getElementById('orientation');
    const previewWrapper = document.getElementById('preview-wrapper');
    const previewFrame = document.getElementById('preview-frame');
    const paperSizeIndicator = document.getElementById('paper-size-indicator');
    
    // Показываем/скрываем блок настройки произвольного размера
    formatSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customSizeContainer.style.display = 'block';
        } else {
            customSizeContainer.style.display = 'none';
        }
        updatePreview();
    });
    
    // Обновляем превью при изменении ориентации
    orientationSelect.addEventListener('change', function() {
        updatePreview();
    });
    
    // Обновляем превью при изменении пользовательских размеров
    document.getElementById('custom_width').addEventListener('input', updatePreview);
    document.getElementById('custom_height').addEventListener('input', updatePreview);
    document.getElementById('unit').addEventListener('change', updatePreview);
    
    // Функция обновления превью с имитацией формата и ориентации
    function updatePreview() {
        const format = formatSelect.value;
        const orientation = orientationSelect.value;
        
        // Настраиваем масштаб и размеры для имитации разных форматов бумаги
        let scale, containerWidth, containerHeight;
        
        // Устанавливаем базовые пропорции для разных форматов
        switch(format) {
            case 'a4':
                if (orientation === 'landscape') {
                    containerWidth = 297;
                    containerHeight = 210;
                    scale = 1;
                } else { // portrait
                    containerWidth = 210;
                    containerHeight = 297;
                    scale = 0.85;
                }
                break;
                
            case 'a5':
                if (orientation === 'landscape') {
                    containerWidth = 210;
                    containerHeight = 148;
                    scale = 0.7;
                } else { // portrait
                    containerWidth = 148;
                    containerHeight = 210;
                    scale = 0.65;
                }
                break;
                
            case 'a6':
                if (orientation === 'landscape') {
                    containerWidth = 148;
                    containerHeight = 105;
                    scale = 0.5;
                } else { // portrait
                    containerWidth = 105;
                    containerHeight = 148;
                    scale = 0.45;
                }
                break;
                
            case 'letter':
                if (orientation === 'landscape') {
                    containerWidth = 279;
                    containerHeight = 216;
                    scale = 0.95;
                } else { // portrait
                    containerWidth = 216;
                    containerHeight = 279;
                    scale = 0.8;
                }
                break;
                
            case 'legal':
                if (orientation === 'landscape') {
                    containerWidth = 356;
                    containerHeight = 216;
                    scale = 0.9;
                } else { // portrait
                    containerWidth = 216;
                    containerHeight = 356;
                    scale = 0.75;
                }
                break;
                
            case 'custom':
                // Для пользовательского размера берём значения из полей
                const width = parseFloat(document.getElementById('custom_width').value) || 210;
                const height = parseFloat(document.getElementById('custom_height').value) || 297;
                const unit = document.getElementById('unit').value;
                
                // Конвертируем единицы в мм для единообразия
                let widthInMm = width;
                let heightInMm = height;
                
                if (unit === 'cm') {
                    widthInMm = width * 10;
                    heightInMm = height * 10;
                } else if (unit === 'inch') {
                    widthInMm = width * 25.4;
                    heightInMm = height * 25.4;
                }
                
                // Определяем размеры и масштаб в зависимости от ориентации
                if (orientation === 'landscape') {
                    containerWidth = Math.max(widthInMm, heightInMm);
                    containerHeight = Math.min(widthInMm, heightInMm);
                } else { // portrait
                    containerWidth = Math.min(widthInMm, heightInMm);
                    containerHeight = Math.max(widthInMm, heightInMm);
                }
                
                // Адаптивный масштаб для пользовательского размера
                // Чем меньше размер, тем меньше масштаб
                const area = containerWidth * containerHeight;
                const a4Area = 210 * 297;
                scale = Math.max(0.3, Math.min(1, Math.sqrt(area / a4Area)));
                break;
                
            default:
                // По умолчанию используем A4
                containerWidth = 297;
                containerHeight = 210;
                scale = 1;
        }
        
        // Устанавливаем масштаб превью
        previewWrapper.style.transform = `scale(${scale})`;
        
        // Обновляем индикатор размера бумаги
        // Устанавливаем соотношение сторон контейнера для индикатора бумаги
        const previewContainerWidth = previewWrapper.parentElement.offsetWidth - 30; // Учитываем padding
        const previewContainerHeight = previewWrapper.parentElement.offsetHeight - 30;
        
        // Вычисляем размеры индикатора с сохранением пропорций
        const aspectRatio = containerWidth / containerHeight;
        
        let indicatorWidth, indicatorHeight;
        
        if (orientation === 'landscape') {
            if (previewContainerWidth / previewContainerHeight > aspectRatio) {
                // Ограничено по высоте
                indicatorHeight = previewContainerHeight;
                indicatorWidth = indicatorHeight * aspectRatio;
            } else {
                // Ограничено по ширине
                indicatorWidth = previewContainerWidth;
                indicatorHeight = indicatorWidth / aspectRatio;
            }
        } else { // portrait
            if (previewContainerWidth / previewContainerHeight > 1/aspectRatio) {
                // Ограничено по высоте
                indicatorHeight = previewContainerHeight;
                indicatorWidth = indicatorHeight * (1/aspectRatio);
            } else {
                // Ограничено по ширине
                indicatorWidth = previewContainerWidth;
                indicatorHeight = indicatorWidth * aspectRatio;
            }
        }
        
        // Применяем размеры к индикатору
        paperSizeIndicator.style.width = `${indicatorWidth}px`;
        paperSizeIndicator.style.height = `${indicatorHeight}px`;
        
        // Центрируем индикатор
        paperSizeIndicator.style.left = `${(previewContainerWidth - indicatorWidth) / 2 + 15}px`;
        paperSizeIndicator.style.top = `${(previewContainerHeight - indicatorHeight) / 2 + 15}px`;
    }
    
    // Отправка логотипа в iframe (если он есть)
    previewFrame.onload = function() {
        const logoUrl = '{{ $certificate->company_logo === null ? "none" : ($certificate->company_logo ? asset("storage/" . $certificate->company_logo) : ($certificate->user->company_logo ? asset("storage/" . $certificate->user->company_logo) : asset("images/default-logo.png"))) }}';
        
        try {
            previewFrame.contentWindow.postMessage({
                type: 'update_logo',
                logo_url: logoUrl
            }, '*');
        } catch (error) {
            console.error('Ошибка при отправке логотипа:', error);
        }
    };
    
    // Инициализация
    updatePreview();
    
    // Обновление при изменении размера окна
    window.addEventListener('resize', updatePreview);
});
</script>

<style>
/* Стили для контейнера предпросмотра */
.preview-container {
    height: 450px;
    position: relative;
    overflow: hidden;
}

@media (max-width: 768px) {
    .preview-container {
        height: 350px;
    }
}

/* Улучшенная анимация изменений */
#preview-wrapper, #paper-size-indicator {
    transition: all 0.3s ease;
}
</style>
@endsection
