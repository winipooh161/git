<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Предпросмотр анимации - {{ $animationEffect->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
        }
        
        .controls {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 100;
        }
        
        .controls .btn {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .effect-info {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            z-index: 100;
        }
        
        /* Стили для частиц */
        .particle {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            z-index: 10;
            will-change: transform, opacity;
        }
        
        /* Анимация для частиц */
        @keyframes floatCenter {
            0% { transform: translate(0, 0) rotate(0deg); opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 0.7; }
            100% { 
                transform: translate(var(--center-x), var(--center-y)) rotate(var(--rotation)); 
                opacity: 0; 
            }
        }
        
        @keyframes floatUp {
            0% { transform: translate(0, 0) rotate(0deg); opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 0.7; }
            100% { 
                transform: translate(var(--drift-x), -100vh) rotate(var(--rotation)); 
                opacity: 0; 
            }
        }
        
        @keyframes floatDown {
            0% { transform: translate(0, -100%) rotate(0deg); opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 0.7; }
            100% { 
                transform: translate(var(--drift-x), 100vh) rotate(var(--rotation)); 
                opacity: 0; 
            }
        }
        
        @keyframes floatRandom {
            0% { transform: translate(0, 0) rotate(0deg); opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 0.7; }
            100% { 
                transform: translate(var(--random-x), var(--random-y)) rotate(var(--rotation)); 
                opacity: 0; 
            }
        }
    </style>
</head>
<body>
    <div class="effect-info">
        <h5>{{ $animationEffect->name }}</h5>
        <div><small>{{ $animationEffect->description }}</small></div>
        <div class="d-flex gap-2 mt-2">
            <span class="badge bg-primary">{{ $animationEffect->type }}</span>
            <span class="badge bg-secondary">{{ $animationEffect->direction }}</span>
            <span class="badge bg-secondary">{{ $animationEffect->speed }}</span>
            <span class="badge bg-secondary">{{ $animationEffect->quantity }} шт.</span>
        </div>
    </div>
    
    <div class="animation-container" id="animationContainer"></div>
    
    <div class="controls">
        <button id="playButton" class="btn btn-lg btn-light rounded-circle me-2">
            <i class="fa-solid fa-play"></i>
        </button>
        <a href="{{ route('admin.animation-effects.edit', $animationEffect) }}" class="btn btn-sm btn-primary">
            <i class="fa-solid fa-arrow-left me-2"></i>Вернуться к редактированию
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Получаем данные эффекта из PHP
            const effectData = {
                type: '{{ $animationEffect->type }}',
                direction: '{{ $animationEffect->direction }}',
                speed: '{{ $animationEffect->speed }}',
                particles: {!! json_encode($animationEffect->particles) !!},
                color: '{{ $animationEffect->color }}',
                size_min: {{ $animationEffect->size_min }},
                size_max: {{ $animationEffect->size_max }},
                quantity: {{ $animationEffect->quantity }}
            };
            
            const playButton = document.getElementById('playButton');
            const animationContainer = document.getElementById('animationContainer');
            
            // Определяем множитель скорости
            let speedFactor = 1;
            if (effectData.speed === 'slow') {
                speedFactor = 1.5;
            } else if (effectData.speed === 'fast') {
                speedFactor = 0.7;
            }
            
            // Функция для создания и запуска анимации
            function playAnimation() {
                // Очистим предыдущие частицы
                animationContainer.innerHTML = '';
                
                // Получаем размеры окна для рассчета конечной позиции
                const width = window.innerWidth;
                const height = window.innerHeight;
                
                // Создаем частицы
                for (let i = 0; i < effectData.quantity; i++) {
                    // Создаем элемент частицы
                    const particle = document.createElement('span');
                    particle.className = 'particle';
                    
                    // Выбираем случайную частицу из массива
                    const randomIndex = Math.floor(Math.random() * effectData.particles.length);
                    particle.textContent = effectData.particles[randomIndex];
                    
                    // Устанавливаем размер
                    const size = Math.floor(Math.random() * (effectData.size_max - effectData.size_min + 1)) + effectData.size_min;
                    particle.style.fontSize = `${size}px`;
                    
                    // Начальная позиция зависит от направления
                    let startX, startY;
                    let animationName;
                    
                    if (effectData.direction === 'bottom') {
                        // Для падения сверху вниз (снег, листья)
                        startX = Math.random() * width;
                        startY = -50; // Немного выше экрана
                        particle.style.top = `${startY}px`;
                        particle.style.left = `${startX}px`;
                        
                        // Случайный дрифт по X при падении
                        const driftX = (Math.random() * 200) - 100; // ±100px
                        particle.style.setProperty('--drift-x', `${driftX}px`);
                        animationName = 'floatDown';
                    } 
                    else if (effectData.direction === 'top') {
                        // Для подъема снизу вверх (пузыри)
                        startX = Math.random() * width;
                        startY = height + 50; // Немного ниже экрана
                        particle.style.bottom = `${-startY}px`;
                        particle.style.left = `${startX}px`;
                        
                        // Случайный дрифт по X при подъеме
                        const driftX = (Math.random() * 200) - 100; // ±100px
                        particle.style.setProperty('--drift-x', `${driftX}px`);
                        animationName = 'floatUp';
                    } 
                    else if (effectData.direction === 'random') {
                        // Случайное начальное положение
                        startX = Math.random() * width;
                        startY = Math.random() * height;
                        particle.style.top = `${startY}px`;
                        particle.style.left = `${startX}px`;
                        
                        // Случайное конечное положение
                        const randomX = (Math.random() * width * 2) - width; // -width to +width
                        const randomY = (Math.random() * height * 2) - height; // -height to +height
                        particle.style.setProperty('--random-x', `${randomX}px`);
                        particle.style.setProperty('--random-y', `${randomY}px`);
                        animationName = 'floatRandom';
                    }
                    else {
                        // Для движения в центр (конфетти, звезды)
                        // Выбираем случайно левую или правую сторону экрана
                        const side = Math.random() > 0.5 ? 'left' : 'right';
                        if (side === 'left') {
                            startX = -50; // За левой границей экрана
                            startY = Math.random() * height;
                            particle.style.left = `${startX}px`;
                            particle.style.top = `${startY}px`;
                        } else {
                            startX = width + 50; // За правой границей экрана
                            startY = Math.random() * height;
                            particle.style.right = `${-startX}px`;
                            particle.style.top = `${startY}px`;
                        }
                        
                        // Рассчитываем движение к центру
                        const centerX = width / 2;
                        const centerY = height / 2;
                        
                        const targetX = centerX - startX;
                        const targetY = centerY - startY;
                        
                        particle.style.setProperty('--center-x', `${targetX}px`);
                        particle.style.setProperty('--center-y', `${targetY}px`);
                        animationName = 'floatCenter';
                    }
                    
                    // Случайное вращение частицы
                    const rotation = Math.floor(Math.random() * 720) - 360; // -360 до +360 градусов
                    particle.style.setProperty('--rotation', `${rotation}deg`);
                    
                    // Применяем анимацию с учетом скорости
                    const duration = (2 + Math.random() * 2) * speedFactor;
                    particle.style.animation = `${animationName} ${duration}s ease-out forwards`;
                    
                    // Случайная задержка для запуска анимации
                    const delay = Math.random() * 3; // задержка до 3 секунд
                    particle.style.animationDelay = `${delay}s`;
                    
                    // Добавляем частицу в DOM
                    animationContainer.appendChild(particle);
                    
                    // Удаляем частицу после завершения анимации
                    setTimeout(() => {
                        if (animationContainer.contains(particle)) {
                            animationContainer.removeChild(particle);
                        }
                    }, (duration + delay + 0.5) * 1000);
                }
            }
            
            // Обработчик кнопки проигрывания
            playButton.addEventListener('click', playAnimation);
            
            // Автоматический запуск анимации при загрузке
            setTimeout(playAnimation, 500);
            
            // Повторный запуск анимации каждые 8 секунд
            setInterval(playAnimation, 8000);
        });
    </script>
</body>
</html>
