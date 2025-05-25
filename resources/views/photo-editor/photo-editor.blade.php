@extends('layouts.photo-editor')

@section('content')
  <div class="editor-container">
    <!-- Верхняя панель -->
    <div class="editor-header">
      <button class="icon-button close-btn">
        <i class="fas fa-times icon"></i>
      </button>
      <div class="editor-header-title">Редактор</div>
      <button class="icon-button save-btn accent">
        <i class="fas fa-check icon"></i>
      </button>
    </div>
    
    <!-- Скрытое поле для хранения ID шаблона -->
    @if(isset($templateId))
    <input type="hidden" id="template_id" value="{{ $templateId }}">
    @endif
    
    <!-- Область с канвасом для редактирования -->
    <div class="canvas-area">
      <div class="canvas-container">
        <canvas id="canvas"></canvas>
      </div>
      
      <!-- Загрузка фото (показывается, когда нет фото) - Улучшенная версия с дополнительными опциями -->
      <div class="upload-placeholder" id="upload-placeholder">
        <div class="upload-buttons">
          <button id="upload-image-btn" class="action-button accent">
            <i class="fas fa-camera icon"></i> 
          </button>
          <button id="capture-image-btn" class="action-button accent" style="margin-left: 10px;">
            <i class="fas fa-video icon"></i> 
          </button>
          <!-- Добавляем подсказку о поддержке вставки из буфера обмена -->
        
        </div>
      </div>
      <input type="file" id="image-upload" class="file-input" accept="image/jpeg,image/png,image/gif,image/webp">
      
      <!-- Индикатор загрузки (скрыт по умолчанию) -->
      <div class="loading-indicator" id="loading-indicator" style="display: none;">
        <div class="spinner mb-2">
          <i class="fas fa-spinner fa-3x"></i>
        </div>
        <div>Обработка изображения...</div>
      </div>
    </div>
    
    <!-- Нижняя панель с инструментами -->
    <div class="editor-toolbar">
      <!-- Вкладки инструментов -->
      <div class="toolbar-tabs">
        <div class="tab-button active" data-tab="filters">Фильтры</div>
        <div class="tab-button" data-tab="adjustments">Настройки</div>
        <div class="tab-button" data-tab="tools">Инструменты</div>
        <div class="tab-button" data-tab="stickers">Стикеры</div>
      </div>
      
      <!-- Содержимое вкладок -->
      <div class="toolbar-content active" id="filters-tab">
        <div class="filter-scroller">
          <!-- Используем стили вместо отсутствующих изображений -->
          <div class="filter-item" data-filter="normal">
            <div class="filter-preview" style="background-color: #f0f0f0;">
              <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-image"></i>
              </div>
            </div>
            <div class="filter-name">Обычный</div>
          </div>
          <div class="filter-item" data-filter="vintage">
            <div class="filter-preview" style="background-color: #d3b38d;">
              <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-image"></i>
              </div>
            </div>
            <div class="filter-name">Винтаж</div>
          </div>
          <div class="filter-item" data-filter="sepia">
            <div class="filter-preview" style="background-color: #a0826c;">
              <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-image"></i>
              </div>
            </div>
            <div class="filter-name">Сепия</div>
          </div>
          <div class="filter-item" data-filter="grayscale">
            <div class="filter-preview" style="background-color: #808080;">
              <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-image"></i>
              </div>
            </div>
            <div class="filter-name">Ч/Б</div>
          </div>
          <div class="filter-item" data-filter="lomo">
            <div class="filter-preview" style="background-color: #4d7ea8;">
              <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-image"></i>
              </div>
            </div>
            <div class="filter-name">Ломо</div>
          </div>
          <div class="filter-item" data-filter="clarity">
            <div class="filter-preview" style="background-color: #6b9ac4;">
              <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-image"></i>
              </div>
            </div>
            <div class="filter-name">Четкость</div>
          </div>
        </div>
      </div>
      
      <div class="toolbar-content" id="adjustments-tab" style="display:none">
        <div class="adjustments-container">
          <div class="adjustment-slider">
            <div class="slider-label">
              <span>Яркость</span>
              <span id="brightness-value">0</span>
            </div>
            <input type="range" id="brightness-slider" min="-100" max="100" value="0">
          </div>
          
          <div class="adjustment-slider">
            <div class="slider-label">
              <span>Контрастность</span>
              <span id="contrast-value">0</span>
            </div>
            <input type="range" id="contrast-slider" min="-100" max="100" value="0">
          </div>
          
          <div class="adjustment-slider">
            <div class="slider-label">
              <span>Насыщенность</span>
              <span id="saturation-value">0</span>
            </div>
            <input type="range" id="saturation-slider" min="-100" max="100" value="0">
          </div>
        </div>
      </div>
      
      <div class="toolbar-content" id="tools-tab" style="display:none">
        <div class="tool-button-grid">
          <div class="tool-button" id="crop-btn">
            <i class="fas fa-crop-alt"></i>
            <span>Обрезка</span>
          </div>
          <div class="tool-button" id="rotate-btn">
            <i class="fas fa-sync-alt"></i>
            <span>Поворот</span>
          </div>
          <div class="tool-button" id="text-btn">
            <i class="fas fa-font"></i>
            <span>Текст</span>
          </div>
          <div class="tool-button" id="draw-btn">
            <i class="fas fa-paint-brush"></i>
            <span>Рисовать</span>
          </div>
          <div class="tool-button" id="mask-btn">
            <i class="fas fa-mask"></i>
            <span>Маски</span>
          </div>
          <div class="tool-button" id="overlay-image-btn">
            <i class="fas fa-images"></i>
            <span>Наложить фото</span>
          </div>
          <div class="tool-button" id="undo-btn">
            <i class="fas fa-undo"></i>
            <span>Отменить</span>
          </div>
          <div class="tool-button" id="redo-btn">
            <i class="fas fa-redo"></i>
            <span>Повторить</span>
          </div>
          <div class="tool-button" id="reset-btn">
            <i class="fas fa-trash"></i>
            <span>Сбросить</span>
          </div>
        </div>
      </div>
      
      <div class="toolbar-content" id="stickers-tab" style="display:none">
        <div class="sticker-scroller">
          <!-- Используем иконки Font Awesome вместо отсутствующих изображений стикеров -->
          <div class="sticker-item" data-icon="heart">
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ff6b6b">
              <i class="fas fa-heart fa-2x"></i>
            </div>
          </div>
          <div class="sticker-item" data-icon="star">
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ffd43b">
              <i class="fas fa-star fa-2x"></i>
            </div>
          </div>
          <div class="sticker-item" data-icon="smile">
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ffd43b">
              <i class="fas fa-smile fa-2x"></i>
            </div>
          </div>
          <div class="sticker-item" data-icon="fire">
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ff922b">
              <i class="fas fa-fire fa-2x"></i>
            </div>
          </div>
          <div class="sticker-item" data-icon="thumbs-up">
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#74c0fc">
              <i class="fas fa-thumbs-up fa-2x"></i>
            </div>
          </div>
          <div class="sticker-item" data-icon="crown">
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ffd43b">
              <i class="fas fa-crown fa-2x"></i>
            </div>
          </div>
          <div class="sticker-item" id="add-sticker-btn">
            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#fff">
              <i class="fas fa-plus fa-2x"></i>
            </div>
          </div>
          <input type="file" id="sticker-upload" class="file-input" accept="image/*">
        </div>
      </div>
    </div>
    
    <!-- Текстовый редактор (всплывающий) -->
    <div class="text-editor-modal" id="text-editor">
      <div class="text-editor-content slide-up">
        <div class="text-input-container">
          <input type="text" id="text-input" class="text-input-field" placeholder="Введите текст...">
        </div>
        
        <div class="text-options">
          <div class="text-option-row">
            <div class="color-option active" style="background-color: #ffffff" data-color="#ffffff"></div>
            <div class="color-option" style="background-color: #000000" data-color="#000000"></div>
            <div class="color-option" style="background-color: #ff0000" data-color="#ff0000"></div>
            <div class="color-option" style="background-color: #00ff00" data-color="#00ff00"></div>
            <div class="color-option" style="background-color: #0000ff" data-color="#0000ff"></div>
            <div class="color-option" style="background-color: #ffff00" data-color="#ffff00"></div>
          </div>
          
          <div class="text-option-row">
            <button id="text-bold-btn" class="icon-button">
              <i class="fas fa-bold"></i>
            </button>
            <button id="text-italic-btn" class="icon-button">
              <i class="fas fa-italic"></i>
            </button>
            <select id="font-family" style="background-color: var(--card-dark); color: white; border: 1px solid var(--border-dark); padding: 5px; border-radius: 4px;">
              <option value="Roboto">Roboto</option>
              <option value="Arial">Arial</option>
              <option value="Helvetica">Helvetica</option>
              <option value="Verdana">Verdana</option>
              <option value="Georgia">Georgia</option>
              <option value="Courier New">Courier New</option>
              <option value="Impact">Impact</option>
            </select>
          </div>
        </div>
        
        <div style="display:flex;gap:10px;justify-content:center">
          <button id="cancel-text-btn" class="action-button">Отмена</button>
          <button id="apply-text-btn" class="action-button accent">Применить</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Файловый инпут для наложения изображений -->
  <input type="file" id="overlay-upload" class="file-input" accept="image/*">

  <!-- Уведомление о неподдерживаемом браузере (показывается только если нужно) -->
  <div id="browser-warning" style="display: none;" class="position-fixed top-0 start-0 end-0 bg-danger text-white p-2 text-center">
    <small>Ваш браузер может не поддерживать все функции редактора. Рекомендуем использовать Chrome, Edge или Firefox.</small>
  </div>

  <!-- Подключение скриптов -->
  @include('photo-editor.partials.scripts')

  <script>
    // Проверка поддержки необходимых функций браузером
    document.addEventListener('DOMContentLoaded', function() {
      // Проверяем поддержку FormData, Blob, URL и других необходимых API
      const isCompatible = (
        window.FormData !== undefined && 
        window.Blob !== undefined && 
        window.URL !== undefined &&
        window.FileReader !== undefined
      );
      
      if (!isCompatible) {
        document.getElementById('browser-warning').style.display = 'block';
      }
    });
    
    function saveToCertificate() {
        try {
            // Показываем индикатор загрузки, если он существует
            const loadingElement = document.getElementById('loading-indicator');
            if (loadingElement) loadingElement.style.display = 'flex';
            
            // Получаем URL для сохранения
            const saveUrl = '{{ route('photo.save.to.certificate', ['templateId' => $templateId ?? 0]) }}';
            console.log('Отправка изображения по URL:', saveUrl);
            
            // Уменьшаем качество до 80% для уменьшения размера
            const imageData = canvas.toDataURL('image/jpeg', 0.8);
            
            // Измеряем размер данных для лога
            const dataSize = Math.round(imageData.length / 1024);
            console.log(`Размер данных изображения: ~${dataSize} КБ`);
            
            // Создаем объект FormData для отправки файла
            const formData = new FormData();
            
            // Преобразуем base64 в Blob для уменьшения размера запроса
            fetch(imageData)
                .then(res => res.blob())
                .then(blob => {
                    // Создаем файл из blob
                    const imageFile = new File([blob], "certificate_image.jpg", { type: "image/jpeg" });
                    formData.append('image', imageFile);
                    
                    // Добавляем csrf-токен
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    // Отправляем данные на сервер
                    return fetch(saveUrl, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    });
                })
                .then(response => {
                    console.log('Получен ответ:', response.status + ' ' + response.statusText);
                    if (!response.ok) {
                        throw new Error('Ошибка HTTP: ' + response.status + ' ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Данные успешно получены:', data);
                    
                    // Скрываем индикатор загрузки
                    if (loadingElement) loadingElement.style.display = 'none';
                    
                    // Сохраняем путь к изображению в sessionStorage и localStorage
                    if (data.path) {
                        // Сохраняем путь во всех возможных хранилищах для надежности
                        sessionStorage.setItem('temp_certificate_cover', data.path);
                        localStorage.setItem('temp_certificate_cover_backup', data.path);
                        document.cookie = `temp_certificate_cover=${encodeURIComponent(data.path)}; path=/; max-age=3600`;
                        
                        console.log('Путь к изображению сохранен:', data.path);
                        console.log('sessionStorage:', sessionStorage.getItem('temp_certificate_cover'));
                        console.log('localStorage:', localStorage.getItem('temp_certificate_cover_backup'));
                        
                        // Показываем уведомление об успешном сохранении
                        showMessage('Обложка успешно сохранена! Перенаправление...', 'success');
                        
                        // Добавляем небольшую задержку перед редиректом
                        setTimeout(() => {
                            if (data.redirect) {
                                console.log('Перенаправление на страницу:', data.redirect);
                                // Добавляем параметр к URL для гарантированной передачи
                                window.location.href = `${data.redirect}?cover=${encodeURIComponent(data.path)}`;
                            }
                        }, 1500);
                    } else {
                        console.error('В ответе сервера отсутствует путь к сохраненному изображению');
                        showMessage('Произошла ошибка при сохранении обложки', 'error');
                    }
                })
                .catch(error => {
                    console.error('Ошибка сохранения изображения:', error);
                    // Скрываем индикатор загрузки
                    if (loadingElement) loadingElement.style.display = 'none';
                    
                    // Пробуем альтернативный метод отправки
                    console.log('Пробуем альтернативный метод отправки...');
                    saveImageAlternativeMethod();
                });
        } catch (e) {
            console.error('Ошибка при подготовке изображения:', e);
            showMessage('Произошла ошибка при подготовке изображения', 'error');
            
            // Скрываем индикатор загрузки
            const loadingElement = document.getElementById('loading-indicator');
            if (loadingElement) loadingElement.style.display = 'none';
        }
    }

    // Альтернативный метод сохранения изображения
    function saveImageAlternativeMethod() {
        // Получаем URL для сохранения
        const saveUrl = '{{ route('photo.save.to.certificate', ['templateId' => $templateId ?? 0]) }}';
        
        // Получаем изображение с меньшим качеством
        const imageData = canvas.toDataURL('image/jpeg', 0.7);
        
        // Отправляем данные на сервер как JSON
        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                image: imageData
            })
        })
        .then(response => {
            console.log('Альтернативный метод - получен ответ:', response.status);
            if (!response.ok) {
                throw new Error('Ошибка HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Альтернативный метод - данные получены:', data);
            
            if (data.path) {
                // Сохраняем путь во всех возможных хранилищах
                sessionStorage.setItem('temp_certificate_cover', data.path);
                localStorage.setItem('temp_certificate_cover_backup', data.path);
                document.cookie = `temp_certificate_cover=${encodeURIComponent(data.path)}; path=/; max-age=3600`;
                
                console.log('Путь к изображению сохранен (альтернативный метод):', data.path);
                showMessage('Изображение успешно сохранено!', 'success');
                
                setTimeout(() => {
                    if (data.redirect) {
                        console.log('Перенаправление на страницу:', data.redirect);
                        // Добавляем параметр к URL для гарантированной передачи
                        window.location.href = `${data.redirect}?cover=${encodeURIComponent(data.path)}`;
                    }
                }, 1500);
            } else {
                console.error('В ответе сервера отсутствует путь к сохраненному изображению');
                showMessage('Ошибка: не получен путь к изображению', 'error');
            }
        })
        .catch(error => {
            console.error('Альтернативный метод - ошибка:', error);
            showMessage('Не удалось сохранить изображение. Пожалуйста, попробуйте снова или обратитесь к администратору.', 'error');
        });
    }
    
    // Добавляем функцию для отображения уведомлений
    function showMessage(message, type = 'info') {
        // Если функция уже реализована в фоторедакторе, используем её
        // Иначе создаем базовую реализацию
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
            return;
        }
        
        // Создаем простое уведомление
        const toast = document.createElement('div');
        toast.className = 'photo-editor-toast';
        toast.innerHTML = message;
        
        // Стили в зависимости от типа
        const backgroundColor = type === 'success' ? '#4CAF50' : 
                                type === 'error' ? '#F44336' : 
                                type === 'warning' ? '#FF9800' : '#2196F3';
        
        // Применяем стили
        Object.assign(toast.style, {
            position: 'fixed',
            bottom: '20px',
            left: '50%',
            transform: 'translateX(-50%)',
            backgroundColor: backgroundColor,
            color: 'white',
            padding: '10px 20px',
            borderRadius: '4px',
            boxShadow: '0 2px 5px rgba(0,0,0,0.3)',
            zIndex: '10000',
            opacity: '0',
            transition: 'opacity 0.3s'
        });
        
        // Добавляем в DOM
        document.body.appendChild(toast);
        
        // Показываем и скрываем через timeout
        setTimeout(() => {
            toast.style.opacity = '1';
        }, 10);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
  </script>
@endsection

