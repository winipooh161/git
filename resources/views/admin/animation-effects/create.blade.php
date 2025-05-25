@extends('layouts.lk')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Создание анимационного эффекта</h1>
        <a href="{{ route('admin.animation-effects.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Назад к списку
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Основные параметры</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.animation-effects.store') }}" method="POST" id="effectForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Название эффекта *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="type" class="form-label">Тип эффекта *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                    <option value="emoji" {{ old('type') == 'emoji' ? 'selected' : '' }}>Эмодзи (смайлики)</option>
                                    <option value="confetti" {{ old('type') == 'confetti' ? 'selected' : '' }}>Конфетти</option>
                                    <option value="snow" {{ old('type') == 'snow' ? 'selected' : '' }}>Снежинки</option>
                                    <option value="fireworks" {{ old('type') == 'fireworks' ? 'selected' : '' }}>Фейерверк</option>
                                    <option value="bubbles" {{ old('type') == 'bubbles' ? 'selected' : '' }}>Пузыри</option>
                                    <option value="leaves" {{ old('type') == 'leaves' ? 'selected' : '' }}>Листья</option>
                                    <option value="stars" {{ old('type') == 'stars' ? 'selected' : '' }}>Звезды</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="2">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Краткое описание эффекта (отображается в модальном окне выбора)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="particles" class="form-label">Частицы анимации *</label>
                            <input type="text" class="form-control @error('particles') is-invalid @enderror" 
                                id="particles" name="particles" value="{{ old('particles', '🎉,🎊,✨,🎁,💫,🎈') }}" required>
                            @error('particles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Укажите символы или эмодзи через запятую (например: 🎉,🎊,✨,🎁,💫)</div>
                            
                            <div class="mt-2" id="particles-preview"></div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="direction" class="form-label">Направление движения *</label>
                                <select class="form-select @error('direction') is-invalid @enderror" 
                                    id="direction" name="direction" required>
                                    <option value="center" {{ old('direction') == 'center' ? 'selected' : '' }}>К центру</option>
                                    <option value="top" {{ old('direction') == 'top' ? 'selected' : '' }}>Вверх</option>
                                    <option value="bottom" {{ old('direction') == 'bottom' ? 'selected' : '' }}>Вниз</option>
                                    <option value="random" {{ old('direction') == 'random' ? 'selected' : '' }}>Случайно</option>
                                </select>
                                @error('direction')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="speed" class="form-label">Скорость анимации *</label>
                                <select class="form-select @error('speed') is-invalid @enderror" 
                                    id="speed" name="speed" required>
                                    <option value="slow" {{ old('speed') == 'slow' ? 'selected' : '' }}>Медленно</option>
                                    <option value="normal" {{ old('speed', 'normal') == 'normal' ? 'selected' : '' }}>Стандартно</option>
                                    <option value="fast" {{ old('speed') == 'fast' ? 'selected' : '' }}>Быстро</option>
                                </select>
                                @error('speed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="color" class="form-label">Цвет (для не-эмодзи)</label>
                                <input type="color" class="form-control form-control-color w-100 @error('color') is-invalid @enderror" 
                                    id="color" name="color" value="{{ old('color', '#ffcc00') }}">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="size_min" class="form-label">Минимальный размер (px) *</label>
                                <input type="number" class="form-control @error('size_min') is-invalid @enderror" 
                                    id="size_min" name="size_min" value="{{ old('size_min', 16) }}" min="8" max="64" required>
                                @error('size_min')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="size_max" class="form-label">Максимальный размер (px) *</label>
                                <input type="number" class="form-control @error('size_max') is-invalid @enderror" 
                                    id="size_max" name="size_max" value="{{ old('size_max', 32) }}" min="8" max="100" required>
                                @error('size_max')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="quantity" class="form-label">Количество частиц *</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                    id="quantity" name="quantity" value="{{ old('quantity', 50) }}" min="10" max="200" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sort_order" class="form-label">Порядок сортировки</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                    id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" value="1" 
                                        id="is_active" name="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Активен
                                    </label>
                                    <div class="form-text small">Если отмечено, эффект будет доступен для выбора</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.animation-effects.index') }}" class="btn btn-outline-secondary me-2">Отмена</a>
                            <button type="submit" class="btn btn-primary">Сохранить эффект</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Предпросмотр эффекта</h5>
                </div>
                <div class="card-body">
                    <div class="animated-effect-preview">
                        <div class="preview-container" style="height: 300px; background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d); position: relative; overflow: hidden; border-radius: 8px;">
                            <!-- Здесь будет предпросмотр эффекта -->
                            <div id="preview-placeholder" class="text-center text-white pt-5">
                                <p><i class="fa-solid fa-wand-sparkles fa-3x mb-3"></i></p>
                                <p>Заполните форму для<br>предпросмотра эффекта</p>
                            </div>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <button type="button" class="btn btn-sm btn-primary" id="previewButton" disabled>
                                <i class="fa-solid fa-play me-1"></i>Запустить анимацию
                            </button>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="help-box p-3 bg-light rounded">
                        <h6 class="fw-bold"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>Подсказки</h6>
                        <ul class="mb-0 ps-3 small">
                            <li>Для эмодзи используйте комбинации символов вроде 🎉, ✨, 🌟</li>
                            <li>Разделяйте частицы запятыми</li>
                            <li>Рекомендуемое количество: 50-70 частиц для оптимальной производительности</li>
                            <li>Для анимации снега лучше использовать ❄️, ❅, ❆</li>
                            <li>Для эффектов с листьями хорошо подходят 🍂, 🍁, 🍃</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .preview-particle {
        position: absolute;
        font-size: 24px;
    }
    
    /* Базовые анимации для различных эффектов */
    .animation-confetti {
        animation: preview-float-confetti 2s ease-in-out infinite;
    }
    
    .animation-snow {
        animation: preview-float-snow 5s ease-in-out infinite;
    }
    
    .animation-fireworks {
        animation: preview-float-fireworks 1.5s ease-out forwards;
    }
    
    .animation-bubbles {
        animation: preview-float-bubbles 4s ease-in-out infinite;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }
    
    .animation-leaves {
        animation: preview-float-leaves 4s ease-in-out infinite;
    }
    
    .animation-stars {
        animation: preview-float-stars 3s ease-in-out infinite;
    }
    
    .animation-emoji {
        animation: preview-float 3s ease-in-out infinite;
    }
    
    /* Анимации для разных эффектов */
    @keyframes preview-float-confetti {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        50% { transform: translateY(-20px) rotate(180deg); opacity: 0.8; }
        100% { transform: translateY(0) rotate(360deg); opacity: 1; }
    }
    
    @keyframes preview-float-snow {
        0% { transform: translateY(0) rotate(0deg); opacity: 0.8; }
        50% { transform: translateY(15px) rotate(5deg); opacity: 1; }
        100% { transform: translateY(0) rotate(0deg); opacity: 0.8; }
    }
    
    @keyframes preview-float-fireworks {
        0% { transform: scale(0.3) translateY(0); opacity: 1; }
        50% { transform: scale(1) translateY(-30px); opacity: 0.8; }
        100% { transform: scale(1.2) translateY(-50px); opacity: 0; }
    }
    
    @keyframes preview-float-bubbles {
        0% { transform: translateY(0) scale(1) rotate(0deg); opacity: 0.6; }
        50% { transform: translateY(-20px) scale(1.1) rotate(10deg); opacity: 0.9; }
        100% { transform: translateY(0) scale(1) rotate(0deg); opacity: 0.6; }
    }
    
    @keyframes preview-float-leaves {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        25% { transform: translateY(-10px) rotate(5deg); opacity: 0.9; }
        50% { transform: translateY(0) rotate(10deg); opacity: 0.8; }
        75% { transform: translateY(10px) rotate(5deg); opacity: 0.9; }
        100% { transform: translateY(0) rotate(0deg); opacity: 1; }
    }
    
    @keyframes preview-float-stars {
        0% { transform: scale(1) rotate(0deg); opacity: 0.8; }
        50% { transform: scale(1.2) rotate(45deg); opacity: 1; }
        100% { transform: scale(1) rotate(90deg); opacity: 0.8; }
    }
    
    @keyframes preview-float {
        0% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(180deg); }
        100% { transform: translateY(0) rotate(360deg); }
    }
    
    /* Анимации для разных направлений движения */
    @keyframes move-to-center {
        0% { transform: translate(0, 0) scale(1); opacity: 1; }
        100% { transform: translate(calc(50% - 50vw), calc(50% - 50vh)) scale(0); opacity: 0; }
    }
    
    @keyframes move-top {
        0% { transform: translateY(0); opacity: 1; }
        100% { transform: translateY(-100px); opacity: 0; }
    }
    
    @keyframes move-bottom {
        0% { transform: translateY(0); opacity: 1; }
        100% { transform: translateY(100px); opacity: 0; }
    }
    
    @keyframes move-random-1 {
        0% { transform: translate(0, 0); opacity: 1; }
        50% { transform: translate(20px, -30px); opacity: 0.7; }
        100% { transform: translate(40px, 0); opacity: 0; }
    }
    
    @keyframes move-random-2 {
        0% { transform: translate(0, 0); opacity: 1; }
        50% { transform: translate(-25px, -15px); opacity: 0.7; }
        100% { transform: translate(-40px, -30px); opacity: 0; }
    }
    
    @keyframes move-random-3 {
        0% { transform: translate(0, 0); opacity: 1; }
        50% { transform: translate(15px, 20px); opacity: 0.7; }
        100% { transform: translate(30px, 40px); opacity: 0; }
    }
    
    /* Улучшаем стилизацию контейнера предпросмотра */
    .preview-container {
        background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    
    /* Дополнительные стили для предпросмотра частиц */
    #particles-preview .badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    #particles-preview .badge:hover {
        transform: scale(1.2);
    }
    
    /* Стили для кнопки предпросмотра */
    #previewButton {
        transition: all 0.3s ease;
    }
    
    #previewButton:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const particlesInput = document.getElementById('particles');
    const previewContainer = document.getElementById('particles-preview');
    const previewButton = document.getElementById('previewButton');
    
    // Функция обновления предпросмотра частиц
    function updateParticlesPreview() {
        const particles = particlesInput.value.split(',').map(p => p.trim()).filter(p => p);
        
        let previewHTML = '<div class="d-flex flex-wrap gap-2">';
        particles.forEach(particle => {
            previewHTML += `<span class="badge bg-light text-dark p-2">${particle}</span>`;
        });
        previewHTML += '</div>';
        
        previewContainer.innerHTML = previewHTML;
        
        // Активируем кнопку предпросмотра, если есть частицы
        previewButton.disabled = particles.length === 0;
    }
    
    // Обработчик изменения поля частиц
    particlesInput.addEventListener('input', updateParticlesPreview);
    
    // Обновленная функция для визуального предпросмотра анимации
    previewButton.addEventListener('click', function() {
        const previewArea = document.querySelector('.preview-container');
        const particles = particlesInput.value.split(',').map(p => p.trim()).filter(p => p);
        const effectType = document.getElementById('type').value;
        const direction = document.getElementById('direction').value;
        const speed = document.getElementById('speed').value;
        const minSize = parseInt(document.getElementById('size_min').value);
        const maxSize = parseInt(document.getElementById('size_max').value);
        const quantity = parseInt(document.getElementById('quantity').value);
        const color = document.getElementById('color').value;
        
        // Очищаем предыдущий предпросмотр
        document.querySelectorAll('.preview-particle').forEach(el => el.remove());
        document.getElementById('preview-placeholder').style.display = 'none';
        
        // Создаем частицы для предпросмотра
        const particleCount = Math.min(quantity, 30); // Ограничиваем для предпросмотра
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('span');
            particle.className = `preview-particle animation-${effectType}`;
            
            // Выбираем случайную частицу
            const randomParticle = particles[Math.floor(Math.random() * particles.length)];
            particle.textContent = randomParticle;
            
            // Случайное позиционирование в зависимости от направления
            let left, top, animationName;
            
            switch (direction) {
                case 'center':
                    left = Math.random() * 90 + 5; // 5-95%
                    top = Math.random() * 90 + 5; // 5-95%
                    animationName = 'move-to-center';
                    break;
                case 'top':
                    left = Math.random() * 90 + 5; // 5-95%
                    top = 70 + Math.random() * 25; // 70-95%
                    animationName = 'move-top';
                    break;
                case 'bottom':
                    left = Math.random() * 90 + 5; // 5-95%
                    top = 5 + Math.random() * 25; // 5-30%
                    animationName = 'move-bottom';
                    break;
                case 'random':
                default:
                    left = Math.random() * 90 + 5; // 5-95%
                    top = Math.random() * 90 + 5; // 5-95%
                    animationName = ['move-random-1', 'move-random-2', 'move-random-3'][Math.floor(Math.random() * 3)];
                    break;
            }
            
            // Случайный размер
            const size = Math.floor(Math.random() * (maxSize - minSize + 1)) + minSize;
            
            // Случайная задержка анимации
            const delay = Math.random() * 3;
            
            // Случайная длительность анимации в зависимости от скорости
            let duration;
            switch (speed) {
                case 'slow': duration = 5 + Math.random() * 3; break;
                case 'fast': duration = 2 + Math.random() * 1; break;
                case 'normal':
                default: duration = 3 + Math.random() * 2; break;
            }
            
            // Вращение для некоторых типов эффектов
            let rotate = '';
            if (['confetti', 'leaves', 'stars'].includes(effectType)) {
                rotate = `rotate(${Math.random() * 360}deg)`;
            }
            
            // Применяем уникальные стили для каждого типа эффекта
            switch (effectType) {
                case 'confetti':
                    particle.style.opacity = '0.8';
                    break;
                case 'snow':
                    particle.style.textShadow = '0 0 5px rgba(255,255,255,0.8)';
                    particle.style.opacity = '0.9';
                    break;
                case 'fireworks':
                    particle.style.textShadow = `0 0 8px ${color}, 0 0 12px ${color}`;
                    break;
                case 'bubbles':
                    particle.style.opacity = '0.6';
                    particle.style.borderRadius = '50%';
                    if (effectType !== 'emoji') {
                        particle.style.background = `radial-gradient(circle at 30% 30%, rgba(255,255,255,0.8), ${color})`;
                        particle.textContent = '';
                        particle.style.width = `${size}px`;
                        particle.style.height = `${size}px`;
                    }
                    break;
                case 'leaves':
                    // Добавляем случайный наклон для эффекта листьев
                    rotate = `rotate(${Math.random() * 180 - 90}deg)`;
                    break;
                case 'stars':
                    particle.style.textShadow = `0 0 10px ${color}, 0 0 20px ${color}`;
                    break;
            }
            
            // Применяем стили
            particle.style.left = `${left}%`;
            particle.style.top = `${top}%`;
            particle.style.fontSize = `${size}px`;
            particle.style.animationDelay = `${delay}s`;
            particle.style.animationDuration = `${duration}s`;
            if (rotate) {
                particle.style.transform = rotate;
            }
            
            // Для не-эмодзи устанавливаем цвет
            if (effectType !== 'emoji' && !['bubbles'].includes(effectType)) {
                particle.style.color = color;
            }
            
            // Добавляем в контейнер
            previewArea.appendChild(particle);
        }
    });
    
    // Инициализация предпросмотра при загрузке
    updateParticlesPreview();
});
</script>
@endsection
