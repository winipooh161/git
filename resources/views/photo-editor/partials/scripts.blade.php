<script>
document.addEventListener('DOMContentLoaded', () => {
  // Initialize Fabric.js canvas
  const canvas = new fabric.Canvas('canvas', {
    preserveObjectStacking: true,
    selection: true
  });

  // State variables
  let isDrawingMode = false;
  let isInCropMode = false;
  let isMaskMode = false;
  let isImageLoaded = false;
  let cropRect = null;
  let activeTextObj = null;
  let currentBrushColor = '#ffffff';
  let currentBrushSize = 10;
  let currentMaskShape = 'rectangle';
  
  // Новые переменные для хранения текущих значений фильтров
  let currentBrightness = 0;
  let currentContrast = 0;
  let currentSaturation = 0;

  // Получаем элемент плейсхолдера
  const uploadPlaceholder = document.getElementById('upload-placeholder');
  const loadingIndicator = document.getElementById('loading-indicator');

  // Filters definitions
  // Расширенный набор фильтров
const filters = {
  normal:    { name: 'Обычный', params: {} },
  vintage:   { name: 'Винтаж',   params: { brightness: -0.1, contrast: 0.1, sepia: true, vignette: true } },
  sepia:     { name: 'Сепия',    params: { sepia: true } },
  grayscale: { name: 'Ч/Б',      params: { grayscale: true } },
  lomo:      { name: 'Ломо',     params: { brightness: 0.05, contrast: 0.2, saturation: 0.3 } },
  clarity:   { name: 'Четкость', params: { contrast: 0.3, sharpen: true } },

};


  // Sticker definitions
  // Расширенный набор стикеров
const stickers = [
  { icon: 'heart',       unicodeChar: '\uf004', color: '#ff6b6b' },
  { icon: 'star',        unicodeChar: '\uf005', color: '#ffd43b' },
  { icon: 'smile',       unicodeChar: '\uf118', color: '#ffd43b' },
  { icon: 'fire',        unicodeChar: '\uf06d', color: '#ff922b' },
  { icon: 'thumbs-up',   unicodeChar: '\uf164', color: '#74c0fc' },
  { icon: 'crown',       unicodeChar: '\uf521', color: '#ffd43b' },
  { icon: 'laugh',       unicodeChar: '\uf59a', color: '#ffe066' },
  { icon: 'kiss',        unicodeChar: '\uf596', color: '#ff6b6b' },
  { icon: 'thumbs-down', unicodeChar: '\uf165', color: '#868e96' },
  { icon: 'trophy',      unicodeChar: '\uf091', color: '#ffd43b' },
  { icon: 'music',       unicodeChar: '\uf001', color: '#74c0fc' },
  { icon: 'camera',      unicodeChar: '\uf030', color: '#ffa94d' },
  { icon: 'lightbulb',   unicodeChar: '\uf0eb', color: '#ffe066' },
  { icon: 'moon',        unicodeChar: '\uf186', color: '#5c7cfa' },
  { icon: 'sun',         unicodeChar: '\uf185', color: '#ffd43b' },
  { icon: 'bolt',        unicodeChar: '\uf0e7', color: '#ffec99' },
  { icon: 'heart-broken',unicodeChar: '\uf7a9', color: '#ff6b6b' }
];


  //
  // -- Utility functions --
  //

  function setCanvasSize() {
    const container = document.querySelector('.canvas-container');
    canvas.setWidth(container.clientWidth);
    canvas.setHeight(container.clientHeight);
    canvas.renderAll();
  }

  // Функция для расчета масштаба в режиме "cover"
  function getCoverScale(imgWidth, imgHeight, canvasWidth, canvasHeight) {
    // Вычисляем соотношение сторон изображения и холста
    const imgRatio = imgWidth / imgHeight;
    const canvasRatio = canvasWidth / canvasHeight;
    
    // Выбираем масштаб, который обеспечит полное покрытие холста
    // При этом часть изображения может быть обрезана
    if (canvasRatio > imgRatio) {
      // Холст шире изображения относительно высоты
      return canvasWidth / imgWidth;
    } else {
      // Холст выше изображения относительно ширины
      return canvasHeight / imgHeight;
    }
  }

  function centerBackgroundImage() {
    const bg = canvas.backgroundImage;
    if (!bg) return;
    bg.set({
      left: canvas.getWidth() / 2,
      top: canvas.getHeight() / 2,
      originX: 'center',
      originY: 'center'
    });
    canvas.renderAll();
  }

  // Функция для скрытия плейсхолдера
  function hideUploadPlaceholder() {
    if (uploadPlaceholder) {
      uploadPlaceholder.style.opacity = '0';
      uploadPlaceholder.style.zIndex = '-10';
      uploadPlaceholder.style.pointerEvents = 'none';
      uploadPlaceholder.style.visibility = 'hidden';
    }
  }

  // Функция для отображения плейсхолдера
  function showUploadPlaceholder() {
    if (uploadPlaceholder) {
      uploadPlaceholder.style.opacity = '1';
      uploadPlaceholder.style.zIndex = '5';
      uploadPlaceholder.style.pointerEvents = 'auto';
      uploadPlaceholder.style.visibility = 'visible';
    }
  }

  // Функция для отображения индикатора загрузки
  function showLoading() {
    if (loadingIndicator) loadingIndicator.style.display = 'flex';
  }

  // Функция для скрытия индикатора загрузки
  function hideLoading() {
    if (loadingIndicator) loadingIndicator.style.display = 'none';
  }

  // Функция для отображения уведомления
  function showNotification(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Показываем уведомление
    setTimeout(() => {
      toast.style.opacity = '1';
    }, 10);
    
    // Скрываем через указанное время
    setTimeout(() => {
      toast.style.opacity = '0';
      setTimeout(() => {
        document.body.removeChild(toast);
      }, 300);
    }, duration);
  }

  //
  // -- History (Undo/Redo) --
  //

  const history = {
    states: [],
    index: -1,
    max: 30,

    save() {
      // truncate future states
      if (this.index < this.states.length - 1) {
        this.states = this.states.slice(0, this.index + 1);
      }
      const json = canvas.toJSON(['selectable', 'hasControls', 'name']);
      this.states.push(JSON.stringify(json));
      if (this.states.length > this.max) this.states.shift();
      this.index = this.states.length - 1;
      updateUndoRedoButtons();
    },

    undo() {
      if (this.index > 0) {
        this.index--;
        this._load();
      }
    },

    redo() {
      if (this.index < this.states.length - 1) {
        this.index++;
        this._load();
      }
    },

    _load() {
      const state = this.states[this.index];
      canvas.loadFromJSON(state, () => {
        canvas.renderAll();
        updateUndoRedoButtons();
      });
    },

    init() {
      this.save();
    }
  };

  function updateUndoRedoButtons() {
    document.getElementById('undo-btn').classList.toggle('disabled', history.index <= 0);
    document.getElementById('redo-btn').classList.toggle('disabled', history.index >= history.states.length - 1);
  }

  //
  // -- Image loading --
  //

  document.getElementById('upload-image-btn').addEventListener('click', () => {
    document.getElementById('image-upload').click();
  });

  document.getElementById('image-upload').addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 20 * 1024 * 1024) {
      showNotification('Максимальный размер файла - 20MB', 'error');
      return;
    }
    const valid = ['image/jpeg','image/png','image/gif','image/webp'];
    if (!valid.includes(file.type)) {
      showNotification('Неподдерживаемый формат файла', 'error');
      return;
    }

    // Показываем индикатор загрузки
    showLoading();

    const reader = new FileReader();
    reader.onload = ev => {
      fabric.Image.fromURL(ev.target.result, img => {
        canvas.clear();
        
        // Рассчитываем масштаб в режиме "cover"
        const scale = getCoverScale(img.width, img.height, canvas.getWidth(), canvas.getHeight());
        
        // Заменяем установку backgroundImage на добавление обычного изображения
        const fabricImage = new fabric.Image(img.getElement(), {
          scaleX: scale,
          scaleY: scale,
          originX: 'center',
          originY: 'center',
          left: canvas.getWidth()/2,
          top: canvas.getHeight()/2,
          name: 'mainImage',
          selectable: true, // Делаем изображение выбираемым
          hasControls: true, // Показываем элементы управления
          hasBorders: true // Показываем границы
        });
        
        // Добавляем изображение как объект, а не как фон
        canvas.add(fabricImage);
        canvas.setActiveObject(fabricImage);
        
        hideUploadPlaceholder();
        hideLoading();
        isImageLoaded = true;
        history.init();
        showNotification('Изображение успешно загружено', 'success');
        canvas.renderAll();
      }, (err) => {
        console.error('Ошибка загрузки изображения:', err);
        hideLoading();
        showNotification('Ошибка загрузки изображения', 'error');
      });
    };
    reader.onerror = () => {
      hideLoading();
      showNotification('Ошибка чтения файла', 'error');
    };
    reader.readAsDataURL(file);
  });

  // Поддержка вставки из буфера обмена (Ctrl+V)
  document.addEventListener('paste', function(e) {
    if (!e.clipboardData || !e.clipboardData.items) return;
    
    const items = e.clipboardData.items;
    let imageItem = null;
    
    for (let i = 0; i < items.length; i++) {
      if (items[i].type.indexOf('image') !== -1) {
        imageItem = items[i];
        break;
      }
    }
    
    if (imageItem) {
      const blob = imageItem.getAsFile();
      const reader = new FileReader();
      
      showLoading();
      
      reader.onload = ev => {
        fabric.Image.fromURL(ev.target.result, img => {
          canvas.clear();
          
          // Рассчитываем масштаб в режиме "cover"
          const scale = getCoverScale(img.width, img.height, canvas.getWidth(), canvas.getHeight());
          
          // Аналогично создаем интерактивное изображение вместо фона
          const fabricImage = new fabric.Image(img.getElement(), {
            scaleX: scale,
            scaleY: scale,
            originX: 'center',
            originY: 'center',
            left: canvas.getWidth()/2,
            top: canvas.getHeight()/2,
            name: 'mainImage',
            selectable: true,
            hasControls: true,
            hasBorders: true
          });
          
          canvas.add(fabricImage);
          canvas.setActiveObject(fabricImage);
          
          hideUploadPlaceholder();
          hideLoading();
          isImageLoaded = true;
          history.init();
          showNotification('Изображение вставлено из буфера обмена', 'success');
          canvas.renderAll();
        }, (err) => {
          console.error('Ошибка загрузки изображения из буфера:', err);
          hideLoading();
          showNotification('Ошибка загрузки изображения', 'error');
        });
      };
      reader.onerror = () => {
        hideLoading();
        showNotification('Ошибка чтения данных из буфера обмена', 'error');
      };
      reader.readAsDataURL(blob);
    }
  });

  //
  // -- Filters & Adjustments --
  //

  function applyFilter(filterKey) {
    if (!isImageLoaded) return;
    
    // Получаем главное изображение вместо фонового
    const mainImage = canvas.getObjects().find(obj => obj.name === 'mainImage');
    if (!mainImage) return;
    
    const fdef = filters[filterKey];
    const farr = [];

    // clear existing
    mainImage.filters = [];

    // apply brightness
    if ('brightness' in fdef.params) {
      farr.push(new fabric.Image.filters.Brightness({ brightness: fdef.params.brightness }));
      currentBrightness = fdef.params.brightness * 100;
      document.getElementById('brightness-slider').value = currentBrightness;
      document.getElementById('brightness-value').textContent = Math.round(currentBrightness);
    } else {
      currentBrightness = 0;
      document.getElementById('brightness-slider').value = 0;
      document.getElementById('brightness-value').textContent = "0";
    }
    
    // contrast
    if ('contrast' in fdef.params) {
      farr.push(new fabric.Image.filters.Contrast({ contrast: fdef.params.contrast }));
      currentContrast = fdef.params.contrast * 100;
      document.getElementById('contrast-slider').value = currentContrast;
      document.getElementById('contrast-value').textContent = Math.round(currentContrast);
    } else {
      currentContrast = 0;
      document.getElementById('contrast-slider').value = 0;
      document.getElementById('contrast-value').textContent = "0";
    }
    
    // sepia
    if (fdef.params.sepia) {
      farr.push(new fabric.Image.filters.Sepia());
    }
    
    // grayscale
    if (fdef.params.grayscale) {
      farr.push(new fabric.Image.filters.Grayscale());
    }
    
    // saturation
    if ('saturation' in fdef.params) {
      farr.push(new fabric.Image.filters.Saturation({ saturation: fdef.params.saturation }));
      currentSaturation = fdef.params.saturation * 100;
      document.getElementById('saturation-slider').value = currentSaturation;
      document.getElementById('saturation-value').textContent = Math.round(currentSaturation);
    } else {
      currentSaturation = 0;
      document.getElementById('saturation-slider').value = 0;
      document.getElementById('saturation-value').textContent = "0";
    }
    
    // blur
    if ('blur' in fdef.params) {
      farr.push(new fabric.Image.filters.Blur({ blur: fdef.params.blur }));
    }
    
    // noise
    if ('noise' in fdef.params) {
      farr.push(new fabric.Image.filters.Noise({ noise: fdef.params.noise }));
    }
    
    // sharpen (approximate)
    if (fdef.params.sharpen) {
      farr.push(new fabric.Image.filters.Convolute({
        matrix: [  0, -1,  0, -1,  5, -1,  0, -1,  0 ]
      }));
    }

    mainImage.filters = farr;
    mainImage.applyFilters();
    canvas.renderAll();
    history.save();
  }

  function initFilters() {
    document.querySelectorAll('.filter-item').forEach(el => {
      el.addEventListener('click', () => {
        const key = el.getAttribute('data-filter');
        applyFilter(key);
        document.querySelectorAll('.filter-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');
      });
    });
  }

  // Исправляем работу слайдеров настроек
  function applyAdjustment(type, value) {
    if (!isImageLoaded) return;
    
    // Получаем главное изображение
    const mainImage = canvas.getObjects().find(obj => obj.name === 'mainImage');
    if (!mainImage) return;
    
    // Если у объекта нет фильтров, создаем пустой массив
    if (!mainImage.filters) {
      mainImage.filters = [];
    }
    
    // Нормализуем значение (от -1 до 1 для яркости и контраста, от -1 до 1 для насыщенности)
    const normalizedValue = value / 100;
    
    // Определяем, какой фильтр мы ищем
    const filterClass = {
      'brightness': fabric.Image.filters.Brightness,
      'contrast': fabric.Image.filters.Contrast,
      'saturation': fabric.Image.filters.Saturation
    }[type];
    
    // Ищем индекс существующего фильтра данного типа
    const filterIndex = mainImage.filters.findIndex(f => f instanceof filterClass);
    
    // Если фильтр уже существует, удаляем его
    if (filterIndex > -1) {
      mainImage.filters.splice(filterIndex, 1);
    }
    
    // Создаем и добавляем новый фильтр с обновленным значением
    const filterParams = {};
    filterParams[type] = normalizedValue;
    const newFilter = new filterClass(filterParams);
    mainImage.filters.push(newFilter);
    
    // Применяем фильтры и обновляем холст
    mainImage.applyFilters();
    canvas.renderAll();
    
    // Сохраняем состояние в истории
    history.save();
  }

  // Adjustments sliders
  ['brightness','contrast','saturation'].forEach(adj => {
    const slider = document.getElementById(adj + '-slider');
    const valueTxt = document.getElementById(adj + '-value');
    
    slider.addEventListener('input', () => {
      const v = parseInt(slider.value, 10);
      valueTxt.textContent = v;
      
      // Применяем фильтр при перемещении слайдера
      applyAdjustment(adj, v);
    });
  });

  //
  // -- Tools: Crop, Rotate, Text, Draw, Mask, Undo/Redo, Reset --
  //

  function startCropMode() {
    if (!isImageLoaded) {
      showNotification('Сначала загрузите изображение', 'info');
      return;
    }
    isInCropMode = true;
    const w = canvas.getWidth() * 0.8;
    const h = canvas.getHeight() * 0.8;
    cropRect = new fabric.Rect({
      left: (canvas.getWidth()-w)/2, top: (canvas.getHeight()-h)/2,
      width: w, height: h,
      fill: 'rgba(0,0,0,0.3)', stroke: '#fff', strokeDashArray:[5,5],
      hasControls: true, selectable: true
    });
    canvas.add(cropRect).setActiveObject(cropRect);
  }

  function applyCrop() {
    if (!cropRect) return;
    history.save();
    
    // Получаем главное изображение вместо фона
    const mainImage = canvas.getObjects().find(obj => obj.name === 'mainImage');
    if (!mainImage) return;
    
    const rect = cropRect.getBoundingRect();
    
    // Создаем объект clipPath для обрезки изображения
    const clipPath = new fabric.Rect({
      left: rect.left - mainImage.left + mainImage.width/2*mainImage.scaleX,
      top: rect.top - mainImage.top + mainImage.height/2*mainImage.scaleY,
      width: rect.width,
      height: rect.height,
      originX: 'center',
      originY: 'center'
    });
    
    mainImage.clipPath = clipPath;
    canvas.remove(cropRect);
    cropRect = null;
    isInCropMode = false;
    canvas.renderAll();
    showNotification('Изображение обрезано', 'success');
  }

  document.getElementById('crop-btn').addEventListener('click', () => {
    if (!isImageLoaded) {
      showNotification('Сначала загрузите изображение', 'info');
      return;
    }
    
    if (isInCropMode) {
      applyCrop();
      document.getElementById('crop-btn').classList.remove('active');
    } else {
      startCropMode();
      document.getElementById('crop-btn').classList.add('active');
    }
  });

  document.getElementById('rotate-btn').addEventListener('click', () => {
    if (!isImageLoaded) {
      showNotification('Сначала загрузите изображение', 'info');
      return;
    }
    
    history.save();
    
    // Получаем главное изображение вместо фона
    const mainImage = canvas.getObjects().find(obj => obj.name === 'mainImage');
    if (!mainImage) return;
    
    const angle = (mainImage.angle || 0) + 90;
    mainImage.rotate(angle);
    canvas.renderAll();
    showNotification('Изображение повернуто', 'success');
  });

  // Text tool
  function showTextEditor() {
    if (!isImageLoaded) {
      showNotification('Сначала загрузите изображение', 'info');
      return;
    }
    document.getElementById('text-editor').style.display = 'flex';
    document.getElementById('text-input').focus();
  }
  
  function hideTextEditor() {
    document.getElementById('text-editor').style.display = 'none';
    document.getElementById('text-input').value = '';
  }
  
  function applyText() {
    const txt = document.getElementById('text-input').value.trim();
    if (!txt || !isImageLoaded) { hideTextEditor(); return; }
    
    history.save();
    const colorOpt = document.querySelector('.color-option.active');
    const color = colorOpt.getAttribute('data-color');
    const bold = document.getElementById('text-bold-btn').classList.contains('active');
    const italic = document.getElementById('text-italic-btn').classList.contains('active');
    const font = document.getElementById('font-family').value;
    
    const textObj = new fabric.Text(txt, {
      left: canvas.getWidth()/2,
      top: canvas.getHeight()/2,
      originX: 'center', 
      originY: 'center',
      fill: color, 
      fontFamily: font,
      fontWeight: bold ? 'bold' : 'normal',
      fontStyle: italic ? 'italic' : 'normal'
    });
    
    canvas.add(textObj).setActiveObject(textObj).renderAll();
    hideTextEditor();
    showNotification('Текст добавлен', 'success');
  }
  
  document.getElementById('text-btn').addEventListener('click', showTextEditor);
  document.getElementById('cancel-text-btn').addEventListener('click', hideTextEditor);
  document.getElementById('apply-text-btn').addEventListener('click', applyText);
  
  // Обработка нажатия Enter в поле текста
  document.getElementById('text-input').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      applyText();
    }
  });

  // Toggle bold/italic
  document.getElementById('text-bold-btn').addEventListener('click', e => e.currentTarget.classList.toggle('active'));
  document.getElementById('text-italic-btn').addEventListener('click', e => e.currentTarget.classList.toggle('active'));
  document.querySelectorAll('.color-option').forEach(opt => {
    opt.addEventListener('click', () => {
      document.querySelectorAll('.color-option').forEach(o => o.classList.remove('active'));
      opt.classList.add('active');
    });
  });

  // Drawing tool
  document.getElementById('draw-btn').addEventListener('click', e => {
    if (!isImageLoaded) {
      showNotification('Сначала загрузите изображение', 'info');
      return;
    }
    
    isDrawingMode = !isDrawingMode;
    canvas.isDrawingMode = isDrawingMode;
    e.currentTarget.classList.toggle('active', isDrawingMode);
    
    if (isDrawingMode) {
      canvas.freeDrawingBrush.color = currentBrushColor;
      canvas.freeDrawingBrush.width = currentBrushSize;
      showDrawingPalette();
    } else {
      hideDrawingPalette();
    }
  });

  // Drawing palette
  function showDrawingPalette() {
    let pal = document.getElementById('drawing-palette');
    if (!pal) {
      pal = document.createElement('div');
      pal.id = 'drawing-palette';
      pal.innerHTML = `
        <div class="palette-title">Цвет кисти:</div>
        <div class="color-palette">
          ${['#ffffff','#000000','#ff0000','#00ff00','#0000ff','#ffff00','#ff00ff','#00ffff'].map(c=>
            `<div class="color-swatch" data-color="${c}" style="background:${c}"></div>`
          ).join('')}
        </div>
        <div>Размер: <span id="brush-size-value">${currentBrushSize}</span>px</div>
        <input type="range" id="brush-size" min="1" max="50" value="${currentBrushSize}">
        <button id="close-palette">Закрыть</button>
      `;
      document.body.appendChild(pal);
      pal.querySelectorAll('.color-swatch').forEach(s => {
        s.addEventListener('click', () => {
          currentBrushColor = s.getAttribute('data-color');
          canvas.freeDrawingBrush.color = currentBrushColor;
          pal.querySelectorAll('.color-swatch').forEach(o=>o.classList.remove('active'));
          s.classList.add('active');
        });
      });
      pal.querySelector('#brush-size').addEventListener('input', ev => {
        currentBrushSize = +ev.target.value;
        document.getElementById('brush-size-value').textContent = currentBrushSize;
        canvas.freeDrawingBrush.width = currentBrushSize;
      });
      pal.querySelector('#close-palette').addEventListener('click', hideDrawingPalette);
      
      // Активируем текущий цвет
      pal.querySelector(`.color-swatch[data-color="${currentBrushColor}"]`).classList.add('active');
    }
    pal.style.display = 'flex';
  }

  function hideDrawingPalette() {
    const pal = document.getElementById('drawing-palette');
    if (pal) pal.style.display = 'none';
  }

  // Mask tool
  document.getElementById('mask-btn').addEventListener('click', e => {
    if (!isImageLoaded) {
      showNotification('Сначала загрузите изображение', 'info');
      return;
    }
    
    isMaskMode = !isMaskMode;
    e.currentTarget.classList.toggle('active', isMaskMode);
    
    if (isMaskMode) {
      if (canvas.backgroundImage) {
        canvas.backgroundImage.selectable = false;
      }
      canvas.on('mouse:down', createMaskShape);
      showMaskControls();
    } else {
      exitMaskMode();
    }
  });

  function showMaskControls() {
    let ctrl = document.getElementById('mask-controls');
    if (!ctrl) {
      ctrl = document.createElement('div');
      ctrl.id = 'mask-controls';
      ctrl.className = 'mask-controls';
      ctrl.innerHTML = `
        <div class="palette-title">Форма маски:</div>
        <div class="mask-shape-menu">
          ${['rectangle','circle','triangle','heart'].map(shape=>
            `<div class="mask-shape-option" data-shape="${shape}">
              <i class="fas fa-${shape === 'heart' ? 'heart' : shape === 'circle' ? 'circle' : shape === 'triangle' ? 'play' : 'square'}"></i>
              ${shape.charAt(0).toUpperCase() + shape.slice(1)}
            </div>`
          ).join('')}
        </div>
        <button id="exit-mask" class="action-button">Выйти</button>
      `;
      document.body.appendChild(ctrl);
      ctrl.querySelectorAll('.mask-shape-option').forEach(b => {
        b.addEventListener('click', () => {
          currentMaskShape = b.getAttribute('data-shape');
          ctrl.querySelectorAll('.mask-shape-option').forEach(o=>o.classList.remove('active'));
          b.classList.add('active');
        });
      });
      ctrl.querySelector('#exit-mask').addEventListener('click', exitMaskMode);
    }
    ctrl.style.display = 'flex';
    ctrl.querySelector(`.mask-shape-option[data-shape="${currentMaskShape}"]`)?.classList.add('active');
  }

  function hideMaskControls() {
    const ctrl = document.getElementById('mask-controls');
    if (ctrl) ctrl.style.display = 'none';
  }

  function createMaskShape(ev) {
    if (!isMaskMode) return;
    const p = canvas.getPointer(ev.e);
    let obj;
    
    switch (currentMaskShape) {
      case 'rectangle':
        obj = new fabric.Rect({ left: p.x, top: p.y, width:100, height:100, fill:'rgba(0,0,0,0.5)' });
        break;
      case 'circle':
        obj = new fabric.Circle({ left: p.x, top: p.y, radius:50, fill:'rgba(0,0,0,0.5)' });
        break;
      case 'triangle':
        obj = new fabric.Triangle({ left: p.x, top: p.y, width:100, height:100, fill:'rgba(0,0,0,0.5)' });
        break;
      case 'heart':
        // Улучшенный путь для сердца
        const path = 'M 0, 0 C -5, -10 -20, -5 -20, 10 C -20, 20 -10, 30 0, 40 C 10, 30 20, 20 20, 10 C 20, -5 5, -10 0, 0 Z';
        obj = new fabric.Path(path);
        obj.set({ left:p.x, top:p.y, scaleX:1, scaleY:1, fill:'rgba(0,0,0,0.5)' });
        break;
    }
    
    if (obj) {
      obj.set({ originX:'center', originY:'center' });
      canvas.add(obj).setActiveObject(obj).renderAll();
    }
  }

  function applyMask(maskObj) {
    if (!maskObj) return;
    
    // Получаем главное изображение
    const mainImage = canvas.getObjects().find(obj => obj.name === 'mainImage');
    if (!mainImage) return;
    
    history.save();
    
    // Клонируем маску
    const m = fabric.util.object.clone(maskObj);
    m.set({
      left: maskObj.left - mainImage.left + mainImage.width/2*mainImage.scaleX,
      top: maskObj.top - mainImage.top + mainImage.height/2*mainImage.scaleY,
      originX: 'center',
      originY: 'center'
    });
    
    mainImage.clipPath = m;
    canvas.remove(maskObj).renderAll();
    exitMaskMode();
    showNotification('Маска применена', 'success');
  }

  // Обработчик для применения маски когда пользователь дважды кликает на форму
  canvas.on('mouse:dblclick', function(opt) {
    if (isMaskMode && opt.target) {
      applyMask(opt.target);
    }
  });

  function exitMaskMode() {
    isMaskMode = false;
    canvas.off('mouse:down', createMaskShape);
    
    // Удаляем проверку backgroundImage
    document.getElementById('mask-btn').classList.remove('active');
    hideMaskControls();
  }

  // Undo / Redo / Reset
  document.getElementById('undo-btn').addEventListener('click', () => {
    history.undo();
    showNotification('Отмена действия', 'info');
  });
  
  document.getElementById('redo-btn').addEventListener('click', () => {
    history.redo();
    showNotification('Повтор действия', 'info');
  });
  
  document.getElementById('reset-btn').addEventListener('click', () => {
    if (confirm('Сбросить все изменения?')) {
      canvas.clear();
      isImageLoaded = false;
      showUploadPlaceholder();
      history.init();
    }
  });

  //
  // -- Overlay Image --
  //

  function initOverlayButton() {
    // Проверяем, есть ли кнопка в HTML
    if (!document.getElementById('overlay-image-btn')) {
      const toolGrid = document.querySelector('.tool-button-grid');
      if (toolGrid) {
        const overlayBtn = document.createElement('div');
        overlayBtn.className = 'tool-button';
        overlayBtn.id = 'overlay-image-btn';
        overlayBtn.innerHTML = `
          <i class="fas fa-images"></i>
          <span>Наложить фото</span>
        `;
        toolGrid.appendChild(overlayBtn);
        
        // Добавляем невидимый input для файлов
        const input = document.createElement('input');
        input.type = 'file';
        input.id = 'overlay-upload';
        input.className = 'file-input';
        input.accept = 'image/*';
        document.body.appendChild(input);
      }
    }
    
    // Добавляем обработчик события
    document.getElementById('overlay-image-btn').addEventListener('click', () => {
      if (!isImageLoaded) {
        showNotification('Сначала загрузите изображение', 'info');
        return;
      }
      document.getElementById('overlay-upload').click();
    });
    
    // Обработчик выбора файла для наложения
    document.getElementById('overlay-upload').addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return;
      
      showLoading();
      
      const reader = new FileReader();
      reader.onload = ev => {
        fabric.Image.fromURL(ev.target.result, img => {
          // Масштабируем изображение до разумного размера
          const maxDim = Math.min(canvas.width, canvas.height) / 2;
          const scale = Math.min(maxDim / img.width, maxDim / img.height);
          
          img.set({
            left: canvas.getWidth() / 2,
            top: canvas.getHeight() / 2,
            originX: 'center',
            originY: 'center',
            scaleX: scale,
            scaleY: scale
          });
          
          canvas.add(img).setActiveObject(img).renderAll();
          hideLoading();
          history.save();
          showNotification('Изображение добавлено', 'success');
        }, (err) => {
          console.error('Ошибка загрузки изображения:', err);
          hideLoading();
          showNotification('Ошибка загрузки изображения', 'error');
        });
      };
      reader.onerror = () => {
        hideLoading();
        showNotification('Ошибка чтения файла', 'error');
      };
      reader.readAsDataURL(file);
    });
  }

  //
  // -- Stickers --
  //

  function initStickers() {
    // Загружаем Font Awesome как шрифт для использования с Canvas
    const fontAwesomeLink = document.createElement('link');
    fontAwesomeLink.rel = 'stylesheet';
    fontAwesomeLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
    document.head.appendChild(fontAwesomeLink);

    // CSS для добавления Font Awesome как шрифта для Canvas
    const fontFaceStyle = document.createElement('style');
    fontFaceStyle.textContent = `
      @font-face {
        font-family: 'FontAwesome';
        font-style: normal;
        font-weight: 900;
        font-display: block;
        src: url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.woff2) format('woff2'),
             url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/webfonts/fa-solid-900.ttf) format('truetype');
      }
    `;
    document.head.appendChild(fontFaceStyle);

    document.querySelectorAll('.sticker-item').forEach(el => {
      el.addEventListener('click', () => {
        if (!isImageLoaded) {
          showNotification('Сначала загрузите изображение', 'info');
          return;
        }

        const icon = el.getAttribute('data-icon');
        if (!icon) return;
        
        const sticker = stickers.find(s => s.icon === icon);
        if (!sticker) return;
        
        history.save();
        
        // Создаем текстовый объект с иконкой Font Awesome
        const textObj = new fabric.Text(sticker.unicodeChar, {
          fontFamily: 'FontAwesome',
          fontSize: 60,
          left: canvas.getWidth()/2,
          top: canvas.getHeight()/2,
          originX: 'center',
          originY: 'center',
          fill: sticker.color
        });
        
        canvas.add(textObj).setActiveObject(textObj).renderAll();
        showNotification('Стикер добавлен', 'success');
      });
    });

    // Загрузка пользовательских стикеров
    document.getElementById('add-sticker-btn').addEventListener('click', () => {
      if (!isImageLoaded) {
        showNotification('Сначала загрузите изображение', 'info');
        return;
      }
      document.getElementById('sticker-upload').click();
    });

    document.getElementById('sticker-upload').addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return;
      
      showLoading();
      
      const reader = new FileReader();
      reader.onload = ev => {
        fabric.Image.fromURL(ev.target.result, img => {
          // Масштабируем стикер до разумного размера
          const maxDim = 100;
          const scale = Math.min(maxDim / img.width, maxDim / img.height);
          
          img.set({
            left: canvas.getWidth()/2,
            top: canvas.getHeight()/2,
            originX: 'center',
            originY: 'center',
            scaleX: scale,
            scaleY: scale
          });
          
          canvas.add(img).setActiveObject(img).renderAll();
          hideLoading();
          history.save();
          showNotification('Стикер добавлен', 'success');
        });
      };
      reader.readAsDataURL(file);
    });
  }

  //
  // -- Camera capture --
  //

  function showCameraModal() {
    // Скрываем панель инструментов
    const editorToolbar = document.querySelector('.editor-toolbar');
    if (editorToolbar) {
      editorToolbar.style.display = 'none';
    }
    
    const modal = document.createElement('div');
    modal.className = 'editor-modal';
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0,0,0,0.9)';
    modal.style.zIndex = '1000';
    modal.style.display = 'flex';
    modal.style.flexDirection = 'column';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    
    // Переменная для отслеживания режима камеры
    let facingMode = 'environment'; // По умолчанию основная (задняя) камера
    
    modal.innerHTML = `
      <div class="video-container" style="position:relative;width:100%;max-width:640px;margin:0 auto;">
        <video id="camera-feed" autoplay playsinline style="width:100%;border-radius:8px;"></video>
        <div id="camera-loading" style="position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:white;background:rgba(0,0,0,0.5);">
          <i class="fas fa-spinner fa-spin"></i> Подключение...
        </div>
      </div>
      <div class="controls" style="display:flex;gap:10px;margin-top:20px;">
        <button id="switch-camera" class="action-button" style="display:none;">
          <i class="fas fa-sync"></i> Сменить камеру
        </button>
        <button id="cancel-camera" class="action-button">Отмена</button>
        <button id="capture-camera" class="action-button accent">Сфотографировать</button>
      </div>
    `;
    document.body.appendChild(modal);

    const video = modal.querySelector('#camera-feed');
    const loading = modal.querySelector('#camera-loading');
    const cancelBtn = modal.querySelector('#cancel-camera');
    const snapBtn = modal.querySelector('#capture-camera');
    const switchCameraBtn = modal.querySelector('#switch-camera');
    snapBtn.disabled = true;
    
    // Функция для запуска камеры с заданным режимом
    function startCamera(facingModeValue) {
      // Остановить текущий поток, если есть
      if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
      }
      
      loading.style.display = 'flex';
      snapBtn.disabled = true;
      
      // Запрашиваем доступ к камере с выбранным режимом
      navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: facingModeValue },
        audio: false 
      })
      .then(stream => {
        video.srcObject = stream;
        loading.style.display = 'none';
        snapBtn.disabled = false;
        switchCameraBtn.style.display = 'block';
      })
      .catch(err => {
        console.error('Ошибка доступа к камере:', err);
        let errorMessage = 'Не удалось получить доступ к камере';
        
        // Более детальное сообщение в зависимости от ошибки
        if (err.name === 'NotAllowedError' || err.name === 'SecurityError') {
          errorMessage = 'Доступ к камере заблокирован. Пожалуйста, разрешите доступ в настройках браузера.';
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
          errorMessage = 'Камера не найдена на вашем устройстве.';
        } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
          errorMessage = 'Камера уже используется другим приложением.';
        } else if (err.name === 'OverconstrainedError') {
          errorMessage = 'Требуемые параметры камеры недоступны.';
        } else if (err.name === 'TypeError') {
          errorMessage = 'Не удалось получить доступ к параметрам камеры.';
        }
        
        loading.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${errorMessage}`;
        showNotification(errorMessage, 'error');
        
        // Добавляем альтернативную опцию загрузки изображения
        const alternativeDiv = document.createElement('div');
        alternativeDiv.style.textAlign = 'center';
        alternativeDiv.style.marginTop = '20px';
        alternativeDiv.style.color = 'white';
        alternativeDiv.innerHTML = `
          <p>Вы можете загрузить фото вместо съемки:</p>
          <button id="alt-upload" class="action-button accent">Выбрать файл</button>
        `;
        modal.querySelector('.video-container').appendChild(alternativeDiv);
        
        document.getElementById('alt-upload').addEventListener('click', () => {
          modal.remove();
          document.getElementById('image-upload').click();
        });
      });
    }

    // Проверяем доступность API камеры
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      loading.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Ваш браузер не поддерживает доступ к камере';
      showNotification('Доступ к камере не поддерживается вашим браузером', 'error');
      
      // Добавляем альтернативную опцию загрузки изображения
      const alternativeDiv = document.createElement('div');
      alternativeDiv.style.textAlign = 'center';
      alternativeDiv.style.marginTop = '20px';
      alternativeDiv.style.color = 'white';
      alternativeDiv.innerHTML = `
        <p>Вы можете загрузить фото вместо съемки:</p>
        <button id="alt-upload" class="action-button accent">Выбрать файл</button>
      `;
      modal.querySelector('.video-container').appendChild(alternativeDiv);
      
      document.getElementById('alt-upload').addEventListener('click', () => {
        modal.remove();
        document.getElementById('image-upload').click();
      });
      
      return;
    }

    // Запускаем камеру с исходным режимом
    startCamera(facingMode);

    // Обработчик для кнопки переключения камеры
    switchCameraBtn.addEventListener('click', () => {
      // Меняем режим камеры и запускаем заново
      facingMode = facingMode === 'environment' ? 'user' : 'environment';
      startCamera(facingMode);
      
      // Уведомляем пользователя о переключении
      const modeText = facingMode === 'environment' ? 'основную' : 'фронтальную';
      showNotification(`Переключено на ${modeText} камеру`, 'info');
    });

    // Обработчик для кнопки фото
    snapBtn.addEventListener('click', () => {
      try {
        // Создаем canvas для захвата кадра
        const c = document.createElement('canvas');
        c.width = video.videoWidth;
        c.height = video.videoHeight;
        
        // Получаем контекст холста
        const ctx = c.getContext('2d');
        
        // Для фронтальной камеры делаем зеркальное отображение
        if (facingMode === 'user') {
          ctx.translate(c.width, 0);
          ctx.scale(-1, 1);
        }
        
        ctx.drawImage(video, 0, 0);
        
        showLoading();
        
        // Конвертируем canvas в blob
        c.toBlob(blob => {
          if (!blob) {
            throw new Error('Не удалось создать изображение из видеопотока');
          }
          
          // Создаем File из blob
          const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' });
          
          // Создаем FileReader для чтения blob как Data URL
          const reader = new FileReader();
          reader.onload = (e) => {
            // Загружаем изображение в редактор
            fabric.Image.fromURL(e.target.result, img => {
              canvas.clear();
              
              // Рассчитываем масштаб в режиме "cover"
              const scale = getCoverScale(img.width, img.height, canvas.getWidth(), canvas.getHeight());
              
              // Заменяем использование backgroundImage на обычный объект
              const fabricImage = new fabric.Image(img.getElement(), {
                scaleX: scale,
                scaleY: scale,
                originX: 'center',
                originY: 'center',
                left: canvas.getWidth()/2,
                top: canvas.getHeight()/2,
                name: 'mainImage',
                selectable: true,
                hasControls: true,
                hasBorders: true
              });
              
              canvas.add(fabricImage);
              canvas.setActiveObject(fabricImage);
              canvas.renderAll();
              
              hideUploadPlaceholder();
              hideLoading();
              isImageLoaded = true;
              history.init();
              
              // Закрываем модальное окно камеры и останавливаем стрим
              if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
              }
              modal.remove();
              
              // Показываем панель инструментов после получения фото
              if (editorToolbar) {
                editorToolbar.style.display = '';
              }
              
              showNotification('Фото успешно сделано', 'success');
              
              // Показываем подсказку о возможности перемещения
              if (typeof showMoveHint === 'function') {
                showMoveHint();
              }
            }, (err) => {
              console.error('Ошибка загрузки фото:', err);
              hideLoading();
              showNotification('Ошибка загрузки фото', 'error');
            });
          };
          
          reader.onerror = () => {
            hideLoading();
            showNotification('Ошибка чтения данных фото', 'error');
          };
          
          reader.readAsDataURL(blob);
        }, 'image/jpeg', 0.95);
      } catch (err) {
        console.error('Ошибка при захвате изображения:', err);
        hideLoading();
        showNotification('Не удалось сделать снимок', 'error');
        
        // Показываем панель инструментов в случае ошибки
        if (editorToolbar) {
          editorToolbar.style.display = '';
        }
      }
    });

    // Обработчик для кнопки отмены
    cancelBtn.addEventListener('click', () => {
      if (video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
      }
      modal.remove();
      
      // Показываем панель инструментов при отмене
      if (editorToolbar) {
        editorToolbar.style.display = '';
      }
    });
  }

  document.getElementById('capture-image-btn').addEventListener('click', () => {
    // Предварительная проверка перед открытием модального окна
    if (typeof navigator.mediaDevices === 'undefined') {
      showNotification('Ваш браузер не поддерживает доступ к камере. Используйте загрузку файла.', 'info');
      // Сразу открываем диалог выбора файла
      document.getElementById('image-upload').click();
    } else {
      showCameraModal();
    }
  });

  // Обработчик сохранения (кнопка с галочкой)
  document.querySelector('.save-btn').addEventListener('click', () => {
    if (!isImageLoaded) {
      showNotification('Нет изображения для сохранения', 'error');
      return;
    }
    
    // Создаем временный canvas для экспорта
    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = canvas.width;
    tempCanvas.height = canvas.height;
    const tempCtx = tempCanvas.getContext('2d');
    
    // Получаем все объекты на холсте
    const objects = canvas.getObjects();
    
    // Находим основное изображение
    const mainImage = objects.find(obj => obj.name === 'mainImage');
    if (!mainImage) {
      showNotification('Основное изображение не найдено', 'error');
      return;
    }
    
    // Делаем все объекты временно невидимыми, кроме основного изображения
    const visibilityStates = objects.map(obj => obj.visible);
    objects.forEach(obj => {
      if (obj !== mainImage) {
        obj.visible = false;
      }
    });
    
    // Обновляем холст с видимым только основным изображением
    canvas.renderAll();
    
    // Сохраняем данные основного изображения
    const dataUrl = canvas.toDataURL({
      format: 'png',
      multiplier: 1
    });
    
    // Восстанавливаем видимость объектов
    objects.forEach((obj, i) => {
      obj.visible = visibilityStates[i];
    });
    
    // Восстанавливаем отображение холста
    canvas.renderAll();
    
    // Показываем диалоговое окно с вариантами действий
    showSaveDialog(dataUrl);
  });

  // Получаем ID шаблона, если он был передан в URL
  const templateId = '{{ $templateId ?? "" }}';

  // Функция для отображения диалогового окна с вариантами сохранения
  function showSaveDialog(imageData) {
    // Если шаблон уже выбран (пришли с экрана выбора шаблона)
    if (templateId) {
      // Сразу сохраняем и переходим к созданию документа
      saveToCertificate(imageData, templateId);
      return;
    }
    
    // Создаем модальное окно
    const modal = document.createElement('div');
    modal.className = 'editor-modal';
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0,0,0,0.8)';
    modal.style.zIndex = '1000';
    modal.style.display = 'flex';
    modal.style.flexDirection = 'column';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    
    modal.innerHTML = `
      <div class="save-dialog" style="background: #fff; border-radius: 8px; padding: 20px; width: 90%; max-width: 500px; text-align: center;">
        <h5 style="margin-bottom: 16px;">Что вы хотите сделать с изображением?</h5>
        <div style="margin-bottom: 20px;">
          <img src="${imageData}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">
        </div>
        <div class="button-group" style="display: flex; flex-direction: column; gap: 10px;">
          <button id="save-download" class="action-button" style="padding: 10px; background: #3897f0; color: white; border: none; border-radius: 4px;">
            <i class="fas fa-download"></i> Скачать изображение
          </button>
          <button id="save-certificate" class="action-button accent" style="padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px;">
            <i class="fas fa-certificate"></i> Использовать для документа
          </button>
          <button id="cancel-save" class="action-button" style="padding: 10px; background: #f8f9fa; border: none; border-radius: 4px;">
            Отмена
          </button>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    // Обработчики кнопок
    document.getElementById('save-download').addEventListener('click', () => {
      const link = document.createElement('a');
      link.download = 'edited-image.png';
      link.href = imageData;
      link.click();
      showNotification('Изображение сохранено', 'success');
      modal.remove();
    });
    
    document.getElementById('save-certificate').addEventListener('click', () => {
      showTemplateSelection(imageData);
      modal.remove();
    });
    
    document.getElementById('cancel-save').addEventListener('click', () => {
      modal.remove();
    });
  }
  
  // Функция для отображения выбора шаблона для документа
  function showTemplateSelection(imageData) {
    // Запрашиваем доступные шаблоны с сервера
    showLoading();
    
    fetch('/entrepreneur/certificates/select-template?format=json', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      hideLoading();
      
      if (data.templates && data.templates.length > 0) {
        displayTemplateSelector(data.templates, imageData);
      } else {
        showNotification('Ошибка загрузки шаблонов', 'error');
      }
    })
    .catch(error => {
      console.error('Ошибка получения шаблонов:', error);
      hideLoading();
      showNotification('Ошибка получения шаблонов документов', 'error');
    });
  }
  
  // Функция для отображения селектора шаблонов
  function displayTemplateSelector(templates, imageData) {
    const modal = document.createElement('div');
    modal.className = 'editor-modal';
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0,0,0,0.8)';
    modal.style.zIndex = '1000';
    modal.style.display = 'flex';
    modal.style.flexDirection = 'column';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    
    let templatesHtml = templates.map(template => `
      <div class="template-item" style="border: 2px solid #eee; border-radius: 8px; padding: 10px; margin-bottom: 10px; cursor: pointer;" 
           data-template-id="${template.id}">
        <img src="${template.preview_url}" style="max-width: 100%; height: auto; border-radius: 4px;">
        <div style="margin-top: 8px; font-weight: bold;">${template.name}</div>
      </div>
    `).join('');
    
    modal.innerHTML = `
      <div class="template-selector" style="background: #fff; border-radius: 8px; padding: 20px; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto;">
        <h5 style="margin-bottom: 16px;">Выберите шаблон для документа</h5>
        <div class="templates-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
          ${templatesHtml}
        </div>
        <div style="margin-top: 20px; text-align: center;">
          <button id="cancel-template" class="action-button" style="padding: 10px; background: #f8f9fa; border: none; border-radius: 4px;">
            Отмена
          </button>
        </div>
      </div>
    `;
    
    document.body.appendChild(modal);
    
    // Обработчик отмены
    document.getElementById('cancel-template').addEventListener('click', () => {
      modal.remove();
    });
    
    // Обработчики выбора шаблона
    document.querySelectorAll('.template-item').forEach(item => {
      item.addEventListener('click', () => {
        const templateId = item.getAttribute('data-template-id');
        saveToCertificate(imageData, templateId);
        modal.remove();
      });
    });
  }
  
  // Функция для сохранения изображения и перехода к созданию документа
  function saveToCertificate(imageData, templateId) {
    showLoading();
    
    try {
      // Преобразуем base64 в Blob
      const byteString = atob(imageData.split(',')[1]);
      const ab = new ArrayBuffer(byteString.length);
      const ia = new Uint8Array(ab);
      for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
      }
      const blob = new Blob([ab], { type: 'image/png' });
      
      // Создаем форму для отправки
      const formData = new FormData();
      formData.append('image', blob, 'edited-image.png');
      formData.append('_token', '{{ csrf_token() }}');
      
      // Используем полный URL с правильным доменом и протоколом
      const baseUrl = window.location.origin; // Получаем текущий домен и протокол
      const saveUrl = `${baseUrl}/photo-save-to-certificate/${templateId}`;
      
      console.log('Отправка изображения по URL:', saveUrl);
      
      // Отправляем изображение
      fetch(saveUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin' // Для отправки cookies с CSRF-токеном
      })
      .then(response => {
        console.log('Получен ответ:', response.status, response.statusText);
        
        if (response.ok) {
          return response.json()
            .then(data => {
              console.log('Успешный JSON ответ:', data);
              if (data.redirect) {
                window.location.href = data.redirect;
              } else {
                // Если нет прямого редиректа, используем стандартный URL
                window.location.href = `${baseUrl}/entrepreneur/certificates/create/${templateId}`;
              }
            })
            .catch(err => {
              console.error('Ошибка при разборе JSON:', err);
              // Если не можем разобрать JSON, переходим по стандартному URL
              window.location.href = `${baseUrl}/entrepreneur/certificates/create/${templateId}`;
            });
        } else if (response.status === 401) {
          // Если пользователь не авторизован
          alert('Для сохранения документа необходимо авторизоваться');
          window.location.href = `${baseUrl}/login?redirect=/photo-editor?template=${templateId}`;
        } else {
          throw new Error(`Ошибка HTTP: ${response.status} ${response.statusText}`);
        }
      })
      .catch(error => {
        console.error('Ошибка сохранения изображения:', error);
        hideLoading();
        showNotification('Ошибка сохранения изображения: ' + error.message, 'error');
      });
    } catch (error) {
      console.error('Ошибка обработки изображения:', error);
      hideLoading();
      showNotification('Ошибка обработки изображения: ' + error.message, 'error');
    }
  }

  // Обработчик закрытия (кнопка с крестиком)
  document.querySelector('.close-btn').addEventListener('click', () => {
    if (isImageLoaded && confirm('Вы уверены, что хотите выйти? Несохраненные изменения будут потеряны.')) {
      window.history.back();
    } else if (!isImageLoaded) {
      window.history.back();
    }
  });

  //
  // -- Initialization --
  //

  function initUI() {
    // Tabs
    document.querySelectorAll('.tab-button').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.tab-button').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.toolbar-content').forEach(c => c.style.display = 'none');
        tab.classList.add('active');
        document.getElementById(`${tab.dataset.tab}-tab`).style.display = 'block';
        // exit special modes
        if (isDrawingMode) { document.getElementById('draw-btn').click(); }
        if (isInCropMode) { document.getElementById('crop-btn').click(); }
        if (isMaskMode) { document.getElementById('mask-btn').click(); }
      });
    });

    // Проверяем, что плейсхолдер отображается корректно
    if (!isImageLoaded) {
      showUploadPlaceholder();
    } else {
      hideUploadPlaceholder();
    }
  }

  // Run inits
  setCanvasSize();
  window.addEventListener('resize', setCanvasSize);
  initFilters();
  initUI();
  initStickers();
  initOverlayButton();
  history.init();
});

</script>

