@extends('layouts.lk')

@section('content')
<div class="certificate-iframe-editor">
    <div class="editor-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between py-2">
                <h1 class="h5 fw-bold mb-0">Редактирование документа #{{ $certificate->certificate_number }}</h1>
                <div>
                    <button id="updateCertificateBtn" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-save me-1"></i> Сохранить изменения
                    </button>
                    <a href="{{ route('entrepreneur.certificates.show', $certificate) }}" class="btn btn-outline-secondary btn-sm ms-2">
                        <i class="fa-solid fa-times me-1"></i> Отмена
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="editor-body vh-100">
        <div class="certificate-iframe-container">
            <div class="loading-overlay" id="iframeLoading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <p class="mt-2">Загрузка редактора...</p>
            </div>
            
            <iframe id="certificateEditorIframe" src="{{ route('template.preview', ['template' => $template, 'editable' => true, 'certificate' => $certificate]) }}" class="certificate-editor-iframe"></iframe>
            
            <!-- Форма для отправки данных, заполняется из iframe -->
            <form id="certificateDataForm" action="{{ route('entrepreneur.certificates.update', $certificate) }}" method="POST" enctype="multipart/form-data" style="display:none;">
                @csrf
                @method('PUT')
                <!-- Эти поля будут заполняться динамически из iframe -->
                <input type="hidden" name="recipient_name" id="recipient_name_hidden" value="{{ old('recipient_name', $certificate->recipient_name) }}">
                <input type="hidden" name="recipient_phone" id="recipient_phone_hidden" value="{{ old('recipient_phone', $certificate->recipient_phone) }}">
                <input type="hidden" name="recipient_email" id="recipient_email_hidden" value="{{ old('recipient_email', $certificate->recipient_email) }}">
                <input type="hidden" name="amount" id="amount_hidden" value="{{ old('amount', $certificate->is_percent ? 0 : $certificate->amount) }}">
                <input type="hidden" name="percent_value" id="percent_value_hidden" value="{{ old('percent_value', $certificate->is_percent ? $certificate->amount : 0) }}">
                <input type="hidden" name="amount_type" id="amount_type_hidden" value="{{ $certificate->is_percent ? 'percent' : 'money' }}">
                <input type="hidden" name="message" id="message_hidden" value="{{ old('message', $certificate->message) }}">
                <input type="hidden" name="company_name" id="company_name_hidden" value="{{ $certificate->company_name }}">
                <input type="hidden" name="valid_from" id="valid_from_hidden" value="{{ old('valid_from', $certificate->valid_from->format('Y-m-d')) }}">
                <input type="hidden" name="valid_until" id="valid_until_hidden" value="{{ old('valid_until', $certificate->valid_until->format('Y-m-d')) }}">
                <input type="hidden" name="temp_cover_path" id="temp_cover_path_hidden" value="">
                <input type="hidden" name="status" id="status_hidden" value="{{ old('status', $certificate->status) }}">
                <input type="hidden" name="animation_effect_id" id="animation_effect_id" value="{{ old('animation_effect_id', $certificate->animation_effect_id) }}">
                <input type="hidden" name="logo_type" id="logo_type_hidden" value="current">
                
                @if (is_array($template->fields) && count($template->fields) > 0)
                    @foreach ($template->fields as $key => $field)
                        <input type="hidden" name="custom_fields[{{ $key }}]" id="custom_{{ $key }}_hidden" 
                            value="{{ old('custom_fields.'.$key, isset($certificate->custom_fields[$key]) ? $certificate->custom_fields[$key] : '') }}">
                    @endforeach
                @endif
            </form>
            
            <!-- Форма удаления сертификата -->
            <form method="POST" action="{{ route('entrepreneur.certificates.destroy', $certificate) }}" id="cancelForm" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
            
            <!-- Боковая панель с элементами управления (снизу) -->
            <div class="certificate-sidebar" id="certificateSidebar">
                <div class="sidebar-toggle" id="sidebarToggle">
                    <i class="fa-solid fa-chevron-up"></i>
                </div>
                
                <div class="sidebar-content">
                    <h5 class="sidebar-title">Настройки документа</h5>
                    
                    <!-- Раздел с настройками документа -->
                    <div class="sidebar-section">
                        <h6>Информация о получателе</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="recipient_name" class="form-label">Имя получателя *</label>
                                <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" 
                                    id="recipient_name" placeholder="Введите имя получателя" value="{{ old('recipient_name', $certificate->recipient_name) }}" required>
                                @error('recipient_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="recipient_phone" class="form-label">Телефон получателя *</label>
                                <input type="tel" class="form-control bg-light @error('recipient_phone') is-invalid @enderror" 
                                    id="recipient_phone" value="{{ old('recipient_phone', $certificate->recipient_phone) }}" readonly>
                                <div class="form-text small text-muted">Номер телефона нельзя изменить после создания документа</div>
                                @error('recipient_phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="recipient_email" class="form-label">Email получателя</label>
                                <input type="email" class="form-control @error('recipient_email') is-invalid @enderror" 
                                    id="recipient_email" value="{{ old('recipient_email', $certificate->recipient_email) }}">
                                @error('recipient_email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="sidebar-section">
                        <h6>Параметры документа</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="amount_type" class="form-label">Тип номинала *</label>
                                <div class="d-flex">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" name="amount_type" id="amount_type_money" 
                                            value="money" {{ !$certificate->is_percent ? 'checked' : '' }}>
                                        <label class="form-check-label" for="amount_type_money">
                                            Денежный
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="amount_type" id="amount_type_percent" 
                                            value="percent" {{ $certificate->is_percent ? 'checked' : '' }}>
                                        <label class="form-check-label" for="amount_type_percent">
                                            Процентный
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div id="money_amount_block" class="{{ $certificate->is_percent ? 'd-none' : '' }}">
                                    <label for="amount" class="form-label">Сумма документа (руб.) *</label>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                        id="amount" value="{{ old('amount', $certificate->is_percent ? 0 : $certificate->amount) }}" min="0" step="100">
                                    @error('amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div id="percent_amount_block" class="{{ !$certificate->is_percent ? 'd-none' : '' }}">
                                    <label for="percent_value" class="form-label">Процент скидки *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('percent_value') is-invalid @enderror" 
                                            id="percent_value" value="{{ old('percent_value', $certificate->is_percent ? $certificate->amount : 0) }}" 
                                            min="1" max="100" step="1">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('percent_value')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="status" class="form-label">Статус *</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" required>
                                    <option value="active" {{ (old('status', $certificate->status) == 'active') ? 'selected' : '' }}>Активен</option>
                                    <option value="expired" {{ (old('status', $certificate->status) == 'expired') ? 'selected' : '' }}>Истек</option>
                                    <option value="canceled" {{ (old('status', $certificate->status) == 'canceled') ? 'selected' : '' }}>Отменен</option>
                                    @if($certificate->status == 'used')
                                    <option value="used" selected disabled>Использован</option>
                                    @endif
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Название организации</label>
                                <input type="text" class="form-control" id="company_name" value="{{ old('company_name', $certificate->company_name) }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="valid_from" class="form-label">Действителен с *</label>
                                <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                                    id="valid_from" value="{{ old('valid_from', $certificate->valid_from->format('Y-m-d')) }}" readonly>
                                @error('valid_from')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="valid_until" class="form-label">Действителен до *</label>
                                <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                    id="valid_until" value="{{ old('valid_until', $certificate->valid_until->format('Y-m-d')) }}">
                                @error('valid_until')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label">Сообщение/пожелания</label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                    id="message" rows="3">{{ old('message', $certificate->message) }}</textarea>
                                @error('message')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Обложка документа -->
                    <div class="sidebar-section">
                        <h6>Обложка документа</h6>
                        
                        @if($certificate->cover_image)
                            <div class="mb-3">
                                <div class="current-cover-image p-2 border rounded text-center mb-3">
                                    <img src="{{ asset('storage/' . $certificate->cover_image) }}" 
                                         class="img-fluid rounded" style="max-height: 150px;" alt="Обложка документа">
                                </div>
                            </div>
                        @endif
                        
                        <div class="d-grid">
                            <a href="{{ route('photo.editor', ['template' => $template->id, 'certificate' => $certificate->id]) }}" class="btn btn-outline-primary">
                                <i class="fa-solid fa-image me-2"></i>{{ $certificate->cover_image ? 'Изменить обложку' : 'Создать обложку' }}
                            </a>
                        </div>
                    </div>
                    
                    <!-- Логотип компании -->
                    <div class="sidebar-section">
                        <h6>Логотип компании</h6>
                        <div class="mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="logo_type" id="logo_current" value="current" checked>
                                <label class="form-check-label" for="logo_current">
                                    Текущий логотип
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="logo_type" id="logo_default" value="default">
                                <label class="form-check-label" for="logo_default">
                                    Из профиля
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="logo_type" id="logo_none" value="none">
                                <label class="form-check-label" for="logo_none">
                                    Без логотипа
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Дополнительные поля шаблона -->
                    @if (is_array($template->fields) && count($template->fields) > 0)
                    <div class="sidebar-section">
                        <h6>Дополнительные поля</h6>
                        <div class="row g-3">
                            @foreach ($template->fields as $key => $field)
                                <div class="col-md-6">
                                    <label for="custom_{{ $key }}" class="form-label">
                                        {{ $field['label'] ?? $key }} 
                                        @if (isset($field['required']) && $field['required'])
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="text" class="form-control @error('custom_fields.'.$key) is-invalid @enderror" 
                                        id="custom_{{ $key }}" 
                                        value="{{ old('custom_fields.'.$key, isset($certificate->custom_fields[$key]) ? $certificate->custom_fields[$key] : '') }}"
                                        {{ isset($field['required']) && $field['required'] ? 'required' : '' }}>
                                    @error('custom_fields.'.$key)
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Выбор анимационного эффекта -->
                    <div class="sidebar-section">
                        <h6>Анимационный эффект</h6>
                        <div class="input-group">
                            <input type="text" class="form-control" id="selected_effect_name" 
                                value="{{ $certificate->animationEffect ? $certificate->animationEffect->name : 'Без эффекта' }}" readonly>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#animationEffectsModal">
                                <i class="fa-solid fa-wand-sparkles me-1"></i>Выбрать
                            </button>
                        </div>
                        <div class="form-text">Анимационный эффект при просмотре документа</div>
                    </div>
                    
                    @if($certificate->status != 'canceled')
                    <div class="sidebar-section border-danger border-top border-4">
                        <h6 class="text-danger">Опасная зона</h6>
                        <p class="text-muted small mb-3">Если вы хотите отменить документ, нажмите кнопку ниже. Это действие нельзя будет отменить.</p>
                        
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmCancellation()">
                            <i class="fa-solid fa-ban me-1"></i> Отменить документ
                        </button>
                    </div>
                    @endif
                    
                    <div class="sidebar-actions">
                        <div class="d-grid">
                            <button type="button" id="sidebarUpdateBtn" class="btn btn-primary">
                                <i class="fa-regular fa-floppy-disk me-2"></i>Сохранить изменения
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно выбора анимационных эффектов -->
<div class="modal fade" id="animationEffectsModal" tabindex="-1" aria-labelledby="animationEffectsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="animationEffectsModalLabel">Выбор анимационного эффекта</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3" id="effectsList">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Загрузка...</span>
                        </div>
                        <p class="mt-2">Загрузка доступных эффектов...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-sm btn-primary" id="selectEffectButton" disabled data-bs-dismiss="modal">Выбрать эффект</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.certificate-iframe-editor {
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow: hidden;
}

.editor-header {
    background-color: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.5rem 0;
    z-index: 10;
}

.editor-body {
    flex: 1;
    position: relative;
    overflow: hidden;
}

.certificate-iframe-container {
    position: relative;
    height: 100%;
}

.certificate-editor-iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 5;
    transition: opacity 0.3s ease;
}

/* Стили для боковой панели, выезжающей снизу */
.certificate-sidebar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 80%; /* Высота панели */
    background: #fff;
    border-top: 1px solid #e5e7eb;
    transform: translateY(calc(100% - 40px)); /* Оставляем 40px для язычка */
    transition: transform 0.3s ease;
    z-index: 10;
    box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.1);
    border-radius: 12px 12px 0 0;
}

.certificate-sidebar.open {
    transform: translateY(0);
}

.sidebar-toggle {
    width: 100%;
    height: 40px;
    position: fixed;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: #fff;
    border-radius: 12px 12px 0 0;
    border-bottom: 1px solid #e5e7eb;
}

.sidebar-toggle i {
    transition: transform 0.3s ease;
}

.certificate-sidebar.open .sidebar-toggle i {
    transform: rotate(180deg);
}

.sidebar-content {
    padding: 50px 1.5rem 1.5rem 1.5rem; /* Верхний padding увеличен, чтобы не перекрываться с язычком */
    height: 100%;
    overflow-y: auto;
}

.sidebar-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
}

.sidebar-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.sidebar-section h6 {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #4b5563;
}

.sidebar-actions {
    padding-top: 1rem;
}

/* Стили для облегчения восприятия полей формы внутри iframe */
#certificateEditorIframe {
    background-color: #f9fafb;
}

@media (max-width: 768px) {
    .certificate-sidebar {
        height: 90%;
    }
}

.effect-card .card {
    transition: all 0.3s ease;
    height: 100%;
    border: 2px solid transparent;
}

.effect-card.selected .card {
    border-color: var(--bs-primary);
    box-shadow: 0 0 10px rgba(var(--bs-primary-rgb), 0.3);
}

.particles-preview {
    font-size: 1.5rem;
    margin-top: 0.5rem;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Анимация для предпросмотра */
@keyframes float-preview {
    0% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
    50% { transform: translateY(-15px) rotate(5deg); opacity: 1; }
    100% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
}

/* Анимация для выбранного эффекта */
.effect-card.selected .particles-preview {
    animation: pulse 2.5s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 0.7; }
    50% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); opacity: 0.7; }
}
</style>
@endpush

@push('scripts')
<script>
function confirmCancellation() {
    if (confirm('Вы уверены, что хотите отменить этот документ? Это действие необратимо.')) {
        document.getElementById('cancelForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('certificateEditorIframe');
    const loadingOverlay = document.getElementById('iframeLoading');
    const sidebar = document.getElementById('certificateSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const updateCertificateBtn = document.getElementById('updateCertificateBtn');
    const sidebarUpdateBtn = document.getElementById('sidebarUpdateBtn');
    const dataForm = document.getElementById('certificateDataForm');
    let iframeInitialized = false;
    
    // Функция форматирования даты в формате YYYY-MM-DD
    function formatDate(date) {
        const d = new Date(date);
        const month = '' + (d.getMonth() + 1);
        const day = '' + d.getDate();
        const year = d.getFullYear();
        
        return [year, month.padStart(2, '0'), day.padStart(2, '0')].join('-');
    }
    
    // Функция форматирования даты для отображения в формате DD.MM.YYYY
    function formatDateForDisplay(dateStr) {
        const d = new Date(dateStr);
        const month = '' + (d.getMonth() + 1);
        const day = '' + d.getDate();
        const year = d.getFullYear();
        
        return [day.padStart(2, '0'), month.padStart(2, '0'), year].join('.');
    }
    
    // Функция для получения значения из URL параметра
    function getUrlParam(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }
    
    // Функция для получения значения из cookie
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
        return null;
    }
    
    // Получаем путь к обложке из различных источников
    function getCoverPath() {
        let path = null;
        
        // Проверяем URL-параметр (приоритет выше)
        path = getUrlParam('cover');
        if (path) {
            console.log("Получен путь к обложке из URL:", path);
            return path;
        }
        
        // Проверяем sessionStorage
        path = sessionStorage.getItem('temp_certificate_cover');
        if (path) {
            console.log("Получен путь к обложке из sessionStorage:", path);
            return path;
        }
        
        // Проверяем localStorage
        path = localStorage.getItem('temp_certificate_cover_backup');
        if (path) {
            console.log("Получен путь к обложке из localStorage:", path);
            return path;
        }
        
        // Проверяем cookie
        path = getCookie('temp_certificate_cover');
        if (path) {
            console.log("Получен путь к обложке из cookie:", path);
            return path;
        }
        
        console.log("Путь к обложке не найден в локальных хранилищах");
        
        // Проверяем скрытое поле формы на случай, если оно уже было заполнено
        const hiddenField = document.getElementById('temp_cover_path_hidden');
        if (hiddenField && hiddenField.value) {
            console.log("Путь к обложке найден в скрытом поле формы:", hiddenField.value);
            return hiddenField.value;
        }
        
        return null;
    }
    
    // Функция получения значений из формы
    function getFormValues() {
        // Определяем тип номинала
        const isPercent = document.getElementById('amount_type_percent').checked;
        
        // Получаем номинал в зависимости от типа
        let amountValue, displayAmount;
        if (isPercent) {
            amountValue = document.getElementById('percent_value').value;
            displayAmount = amountValue ? amountValue + ' %' : '5 %';
        } else {
            amountValue = document.getElementById('amount').value;
            displayAmount = amountValue ? parseInt(amountValue).toLocaleString('ru-RU') + ' ₽' : '10 000 ₽';
        }
        
        return {
            recipient_name: document.getElementById('recipient_name').value,
            recipient_email: document.getElementById('recipient_email').value,
            company_name: document.getElementById('company_name').value,
            amount: displayAmount, // Отображаемый номинал (с единицами измерения)
            message: document.getElementById('message').value,
            valid_from: document.getElementById('valid_from').value ? 
                formatDateForDisplay(document.getElementById('valid_from').value) : '',
            valid_until: document.getElementById('valid_until').value ? 
                formatDateForDisplay(document.getElementById('valid_until').value) : ''
        };
    }
    
    // Обработчик события загрузки iframe
    iframe.addEventListener('load', function() {
        // Скрываем индикатор загрузки
        loadingOverlay.style.opacity = '0';
        setTimeout(() => {
            loadingOverlay.style.display = 'none';
        }, 300);
        
        // По умолчанию частично показываем панель (как язычок)
        setTimeout(() => {
            iframeInitialized = true;
            
            // Получаем путь к обложке из всех возможных источников
            const coverPath = getCoverPath();
            console.log("Обнаружен путь к обложке:", coverPath);
            
            if (coverPath) {
                // Сохраняем путь во всех возможных хранилищах для надежности
                sessionStorage.setItem('temp_certificate_cover', coverPath);
                localStorage.setItem('temp_certificate_cover_backup', coverPath);
                document.cookie = `temp_certificate_cover=${encodeURIComponent(coverPath)}; path=/; max-age=3600`;
                
                // Устанавливаем значение в скрытое поле формы
                const hiddenField = document.getElementById('temp_cover_path_hidden');
                if (hiddenField) {
                    hiddenField.value = coverPath;
                    console.log('Путь к обложке установлен в скрытое поле:', hiddenField.value);
                } else {
                    console.error('Не найдено скрытое поле temp_cover_path_hidden!');
                }
                
                // Добавляем индикатор, что обложка уже создана
                const coverBtn = document.querySelector('a[href*="photo.editor"]');
                
                if (coverBtn) {
                    coverBtn.innerHTML = '<i class="fa-solid fa-check me-2"></i>Обложка изменена';
                    coverBtn.classList.remove('btn-outline-primary');
                    coverBtn.classList.add('btn-success');
                }
            }
            
            // Обновляем значения в iframe при загрузке
            updateIframeFields();
        }, 500);
        
        // Инициализируем взаимодействие с iframe
        initIframeInteraction();
        
        console.log('Iframe загружен, ожидаем сообщения о готовности');
    });
    
    // Обработчик переключения боковой панели
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('open');
        console.log("Sidebar toggled:", sidebar.classList.contains('open'));
        
        // Иконка переворачивается автоматически через CSS трансформацию
    });
    
    // Функция инициализации взаимодействия с iframe
    function initIframeInteraction() {
        // Первичное обновление iframe с данными из формы
        updateIframeFields();
        
        // Настраиваем обработчики событий для двусторонней синхронизации
        setupTwoWaySync();
        
        console.log("Sidebar initialized");
    }
    
    // Функция настройки двусторонней синхронизации между формой и iframe
    function setupTwoWaySync() {
        // Обработчик сообщений от iframe
        window.addEventListener('message', function(event) {
            // Проверяем, что сообщение от нашего iframe
            if (event.source !== iframe.contentWindow) return;
            
            console.log('Получено сообщение от iframe:', event.data);
            
            // Обрабатываем разные типы сообщений
            if (event.data && event.data.type) {
                switch (event.data.type) {
                    case 'template_ready':
                        console.log('Шаблон сертификата готов');
                        // Сразу отправляем текущие значения формы в iframe
                        updateIframeFields();
                        break;
                        
                    case 'request_initial_data':
                        console.log('Получен запрос начальных данных от iframe');
                        // Отправляем текущие значения формы в iframe
                        updateIframeFields();
                        break;
                        
                    case 'field_update':
                        // Обрабатываем обновления полей из iframe
                        handleFieldUpdate(event.data.field, event.data.value);
                        break;
                        
                    case 'current_values':
                        // Обрабатываем текущие значения из iframe
                        console.log('Получены текущие значения из iframe:', event.data.values);
                        // Обновляем значения формы из iframe
                        if (event.data.values) {
                            Object.keys(event.data.values).forEach(fieldName => {
                                handleFieldUpdate(fieldName, event.data.values[fieldName]);
                            });
                        }
                        break;
                    
                    case 'fields_updated':
                        console.log('Поля в iframe успешно обновлены');
                        // Запрашиваем текущие значения полей из iframe для синхронизации
                        requestCurrentValues();
                        break;
                }
            }
        });
    }
    
    // Функция обработки обновления поля от iframe
    function handleFieldUpdate(fieldName, value) {
        console.log(`Обновление поля ${fieldName} из iframe: ${value}`);
        
        // Обновляем соответствующее поле в форме
        switch(fieldName) {
            case 'recipient_name':
                document.getElementById('recipient_name').value = value;
                document.getElementById('recipient_name_hidden').value = value;
                break;
                
            case 'recipient_email':
                document.getElementById('recipient_email').value = value;
                document.getElementById('recipient_email_hidden').value = value;
                break;
                
            case 'company_name':
                document.getElementById('company_name').value = value;
                document.getElementById('company_name_hidden').value = value;
                break;
                
            case 'amount':
                // Определяем тип номинала
                const isPercent = document.getElementById('amount_type_percent').checked;
                
                if (isPercent) {
                    // Убираем символ % и пробелы, оставляем только цифры
                    const cleanPercent = value.replace(/[^\d]/g, '');
                    document.getElementById('percent_value').value = cleanPercent;
                    document.getElementById('percent_value_hidden').value = cleanPercent;
                } else {
                    // Убираем символ валюты и пробелы, оставляем только цифры
                    const cleanAmount = value.replace(/[^\d]/g, '');
                    document.getElementById('amount').value = cleanAmount;
                    document.getElementById('amount_hidden').value = cleanAmount;
                }
                console.log(`Поле amount обновлено из iframe: ${value}`);
                break;
                
            case 'message':
                document.getElementById('message').value = value;
                document.getElementById('message_hidden').value = value;
                break;
                
            case 'valid_from':
                // Преобразуем дату из формата DD.MM.YYYY в формат YYYY-MM-DD
                if (value) {
                    const parts = value.split('.');
                    if (parts.length === 3) {
                        const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                        document.getElementById('valid_from').value = formattedDate;
                        document.getElementById('valid_from_hidden').value = formattedDate;
                    }
                }
                break;
                
            case 'valid_until':
                // Преобразуем дату из формата DD.MM.YYYY в формат YYYY-MM-DD
                if (value) {
                    const parts = value.split('.');
                    if (parts.length === 3) {
                        const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                        document.getElementById('valid_until').value = formattedDate;
                        document.getElementById('valid_until_hidden').value = formattedDate;
                    }
                }
                break;
                
            // Обновление дополнительных полей шаблона
            default:
                // Проверяем, является ли это одним из дополнительных полей custom_fields
                if (fieldName.startsWith('custom_')) {
                    const fieldKey = fieldName.replace('custom_', '');
                    const inputField = document.getElementById(fieldName);
                    const hiddenField = document.getElementById(`${fieldName}_hidden`);
                    
                    if (inputField) inputField.value = value;
                    if (hiddenField) hiddenField.value = value;
                }
                break;
        }
    }
    
    // Функция запроса текущих значений из iframe
    function requestCurrentValues() {
        if (!iframeInitialized || !iframe.contentWindow) return;
        
        iframe.contentWindow.postMessage({
            type: 'request_current_values'
        }, '*');
    }
    
    // Функция обновления полей в iframe
    function updateIframeFields() {
        // Проверяем, что iframe загружен
        if (!iframeInitialized || !iframe.contentWindow) {
            console.log('Iframe еще не инициализирован, отложим отправку данных');
            setTimeout(updateIframeFields, 500);
            return;
        }
        
        const formValues = getFormValues();
        console.log('Отправка данных формы в iframe:', formValues);
        
        // Отправляем данные в iframe
        iframe.contentWindow.postMessage({
            type: 'update_fields',
            data: formValues
        }, '*');
    }
    
    // Обработчики изменения полей формы
    document.querySelectorAll('#company_name, #recipient_name, #recipient_email, #amount, #percent_value, #message, #valid_from, #valid_until').forEach(input => {
        input.addEventListener('input', function() {
            // Обновляем скрытые поля формы
            const correspondingHidden = document.getElementById(`${this.id}_hidden`);
            if (correspondingHidden) {
                correspondingHidden.value = this.value;
            }
            updateIframeFields();
        });
        
        input.addEventListener('change', function() {
            // Обновляем скрытые поля формы
            const correspondingHidden = document.getElementById(`${this.id}_hidden`);
            if (correspondingHidden) {
                correspondingHidden.value = this.value;
            }
            updateIframeFields();
        });
    });
    
    // Обновление статуса в скрытом поле
    document.getElementById('status').addEventListener('change', function() {
        document.getElementById('status_hidden').value = this.value;
    });
    
    // Обработчики для переключения типа номинала
    const amountTypeRadios = document.querySelectorAll('input[name="amount_type"]');
    const moneyAmountBlock = document.getElementById('money_amount_block');
    const percentAmountBlock = document.getElementById('percent_amount_block');
    
    amountTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Обновляем скрытое поле с типом номинала
            document.getElementById('amount_type_hidden').value = this.value;
            
            if (this.value === 'money') {
                moneyAmountBlock.classList.remove('d-none');
                percentAmountBlock.classList.add('d-none');
                document.getElementById('amount').required = true;
                document.getElementById('percent_value').required = false;
            } else {
                moneyAmountBlock.classList.add('d-none');
                percentAmountBlock.classList.remove('d-none');
                document.getElementById('amount').required = false;
                document.getElementById('percent_value').required = true;
            }
            
            // Обновляем предпросмотр в iframe
            updateIframeFields();
        });
    });
    
    // Обработчик для логотипа
    document.querySelectorAll('input[name="logo_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('logo_type_hidden').value = this.value;
        });
    });
    
    // Обработчик кнопки обновления сертификата в шапке
    if (updateCertificateBtn) {
        updateCertificateBtn.addEventListener('click', submitForm);
    }
    
    // Обработчик кнопки обновления сертификата в боковой панели
    if (sidebarUpdateBtn) {
        sidebarUpdateBtn.addEventListener('click', submitForm);
    }
    
    // Функция отправки формы
    function submitForm() {
        // Проверяем обязательные поля
        // Сначала запрашиваем актуальные значения из iframe
        requestCurrentValues();
        
        // Небольшая задержка для получения обновленных значений
        setTimeout(() => {
            const recipientName = document.getElementById('recipient_name').value;
            const validUntil = document.getElementById('valid_until').value;
            
            if (!recipientName) {
                alert('Пожалуйста, введите имя получателя');
                return;
            }
            
            if (!validUntil) {
                alert('Пожалуйста, укажите дату окончания срока действия');
                return;
            }
            
            // Проверяем тип номинала и соответствующее значение
            const isPercent = document.getElementById('amount_type_percent').checked;
            if (isPercent) {
                const percentValue = document.getElementById('percent_value').value;
                if (!percentValue || percentValue < 1 || percentValue > 100) {
                    alert('Пожалуйста, введите корректный процент скидки (1-100)');
                    return;
                }
            } else {
                const amount = document.getElementById('amount').value;
                if (!amount || amount <= 0) {
                    alert('Пожалуйста, введите корректную сумму документа');
                    return;
                }
            }
            
            // Заполняем скрытые поля формы, если они еще не заполнены
            document.getElementById('recipient_name_hidden').value = recipientName;
            document.getElementById('recipient_email_hidden').value = document.getElementById('recipient_email').value;
            document.getElementById('valid_until_hidden').value = validUntil;
            document.getElementById('status_hidden').value = document.getElementById('status').value;
            document.getElementById('message_hidden').value = document.getElementById('message').value;
            document.getElementById('company_name_hidden').value = document.getElementById('company_name').value;
            
            if (isPercent) {
                document.getElementById('percent_value_hidden').value = document.getElementById('percent_value').value;
                document.getElementById('amount_hidden').value = 0;
            } else {
                document.getElementById('amount_hidden').value = document.getElementById('amount').value;
                document.getElementById('percent_value_hidden').value = 0;
            }
            
            // Получаем путь к обложке из всех возможных источников
            const coverPath = getCoverPath();
            
            if (coverPath) {
                document.getElementById('temp_cover_path_hidden').value = coverPath;
                console.log("Использую путь к обложке при отправке формы:", coverPath);
            }
            
            console.log("Отправка формы с данными:", {
                recipient_name: document.getElementById('recipient_name_hidden').value,
                recipient_email: document.getElementById('recipient_email_hidden').value,
                amount_type: document.getElementById('amount_type_hidden').value,
                amount: document.getElementById('amount_hidden').value,
                percent_value: document.getElementById('percent_value_hidden').value,
                status: document.getElementById('status_hidden').value,
                cover_path: document.getElementById('temp_cover_path_hidden').value,
                company_name: document.getElementById('company_name_hidden').value
            });
            
            // Отправляем форму
            dataForm.submit();
        }, 500);
    }
    
    // Функционал для работы с анимационными эффектами
    const effectsList = document.getElementById('effectsList');
    const selectEffectButton = document.getElementById('selectEffectButton');
    const animationEffectIdInput = document.getElementById('animation_effect_id');
    const selectedEffectNameInput = document.getElementById('selected_effect_name');
    let selectedEffectId = {{ $certificate->animation_effect_id ?? 'null' }};
    let effects = [];
    
    // Функция для загрузки списка эффектов
    function loadAnimationEffects() {
        fetch('{{ route("animation-effects.get") }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка сети: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                effects = data;
                renderEffectsList(data);
            })
            .catch(error => {
                console.error('Ошибка при загрузке эффектов:', error);
                effectsList.innerHTML = `
                    <div class="col-12 text-center py-4">
                        <i class="fa-solid fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                        <p>Не удалось загрузить анимационные эффекты</p>
                        <button class="btn btn-sm btn-outline-primary mt-2" onclick="loadAnimationEffects()">
                            <i class="fa-solid fa-refresh me-1"></i>Попробовать снова
                        </button>
                    </div>
                `;
            });
    }
    
    // Функция для отображения списка эффектов
    function renderEffectsList(effects) {
        effectsList.innerHTML = '';
        
        // Добавляем опцию "Без эффекта"
        const noEffectCard = document.createElement('div');
        noEffectCard.className = 'col-6 col-md-4 effect-card' + (selectedEffectId === null ? ' selected' : '');
        noEffectCard.innerHTML = `
            <div class="card rounded-3 border-0 shadow-sm" style="cursor: pointer;" onclick="selectEffect(null, 'Без эффекта')">
                <div class="card-body text-center">
                    <i class="fa-solid fa-ban fa-2x text-secondary mb-2"></i>
                    <h6 class="card-title fs-6 mb-1">Без эффекта</h6>
                    <div class="particles-preview"></div>
                    <p class="small text-muted">Документ без анимации</p>
                </div>
            </div>
        `;
        effectsList.appendChild(noEffectCard);
        
        // Добавляем все доступные эффекты
        effects.forEach(effect => {
            const effectCard = document.createElement('div');
            effectCard.className = 'col-6 col-md-4 effect-card' + (selectedEffectId === effect.id ? ' selected' : '');
            effectCard.innerHTML = `
                <div class="card rounded-3 border-0 shadow-sm" style="cursor: pointer;" onclick="selectEffect(${effect.id}, '${effect.name}')">
                    <div class="card-body text-center">
                        <i class="fa-solid fa-sparkles fa-2x text-primary mb-2"></i>
                        <h6 class="card-title fs-6 mb-1">${effect.name}</h6>
                        <div class="particles-preview">${effect.particles ? effect.particles.slice(0, 3).join(' ') : '✨'}</div>
                        <p class="small text-muted">${effect.description || 'Анимационный эффект'}</p>
                    </div>
                </div>
            `;
            effectsList.appendChild(effectCard);
        });
        
        // Активируем кнопку подтверждения, если выбран эффект
        selectEffectButton.disabled = false;
    }
    
    // Функция для выбора эффекта
    window.selectEffect = function(id, name) {
        selectedEffectId = id;
        
        // Обновляем визуальное состояние карточек эффектов
        document.querySelectorAll('.effect-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Находим выбранную карточку и выделяем её
        const selectedCard = id === null 
            ? document.querySelector('.effect-card:first-child')
            : Array.from(document.querySelectorAll('.effect-card')).find(card => 
                card.querySelector('.card').getAttribute('onclick').includes(`selectEffect(${id}`)
              );
        
        if (selectedCard) {
            selectedCard.classList.add('selected');
        }
        
        // Активируем кнопку подтверждения
        selectEffectButton.disabled = false;
        
        // Показываем предпросмотр, если выбран эффект с id
        if (id !== null) {
            const effect = effects.find(e => e.id === id);
            if (effect) {
                showEffectPreview(effect);
            }
        }
    }
    
    // Функция для предпросмотра эффекта
    function showEffectPreview(effect) {
        // Создаем временный контейнер для предпросмотра эффекта
        const previewContainer = document.createElement('div');
        previewContainer.className = 'effect-preview-container';
        previewContainer.style.position = 'absolute';
        previewContainer.style.top = '0';
        previewContainer.style.left = '0';
        previewContainer.style.width = '100%';
        previewContainer.style.height = '100%';
        previewContainer.style.pointerEvents = 'none';
        previewContainer.style.zIndex = '1050';
        document.body.appendChild(previewContainer);
        
        // Создаем частицы для эффекта
        const particleCount = Math.min(effect.quantity || 15, 20);
        const particles = Array.isArray(effect.particles) && effect.particles.length > 0
            ? effect.particles : ['✨'];
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            
            // Случайное расположение
            particle.style.position = 'absolute';
            particle.style.left = `${Math.random() * 80 + 10}%`;
            particle.style.top = `${Math.random() * 40 + 30}%`;
            
            // Случайный размер
            const size = Math.floor(Math.random() * 16) + 16;
            particle.style.fontSize = `${size}px`;
            
            // Анимация
            particle.style.animation = 'float-preview 2s ease-in-out infinite';
            
            // Содержимое частицы
            const particleText = particles[Math.floor(Math.random() * particles.length)];
            particle.textContent = particleText;
            
            // Добавляем частицу в контейнер
            previewContainer.appendChild(particle);
        }
        
        // Удаляем предпросмотр через несколько секунд
        setTimeout(() => {
            if (previewContainer.parentNode) {
                previewContainer.parentNode.removeChild(previewContainer);
            }
        }, 2000);
    }
    
    // Добавляем обработчик клика для кнопки подтверждения выбора эффекта
    selectEffectButton.addEventListener('click', function() {
        // Обновляем скрытое поле с ID выбранного эффекта
        animationEffectIdInput.value = selectedEffectId || '';
        
        // Обновляем отображаемое название эффекта
        if (selectedEffectId) {
            const selectedEffect = effects.find(e => e.id === selectedEffectId);
            selectedEffectNameInput.value = selectedEffect ? selectedEffect.name : 'Выбранный эффект';
        } else {
            selectedEffectNameInput.value = 'Без эффекта';
        }
    });
    
    // Инициализация при открытии модального окна
    document.getElementById('animationEffectsModal').addEventListener('show.bs.modal', function () {
        // Если список эффектов еще не загружен
        if (effects.length === 0) {
            loadAnimationEffects();
        }
    });
});
</script>
@endpush
@endsection
