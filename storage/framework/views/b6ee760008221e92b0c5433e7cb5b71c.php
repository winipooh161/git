<!-- Стили -->
<link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">

<!-- Иконки -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">

<!-- Стили -->
<style>
/* Кнопка для получения сертификата */
.get-certificate-btn {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #3b82f6;
    color: white;
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    cursor: pointer;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.get-certificate-btn:hover {
    background-color: #2563eb;
    transform: translateX(-50%) translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
}

.get-certificate-btn i {
    margin-right: 8px;
}

/* Стили для модального окна авторизации */
.auth-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.auth-modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.auth-modal {
    background-color: white;
    border-radius: 12px;
    padding: 24px;
    width: 90%;
    max-width: 450px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    transform: translateY(-20px);
    transition: transform 0.3s ease;
}

.auth-modal-overlay.active .auth-modal {
    transform: translateY(0);
}

.auth-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.auth-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
}

.auth-form-tabs {
    display: flex;
    margin-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.auth-tab {
    padding: 8px 16px;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
}

.auth-tab.active {
    border-bottom-color: #3b82f6;
    color: #3b82f6;
    font-weight: 600;
}

.auth-form {
    margin-top: 16px;
}

.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    margin-bottom: 4px;
    font-size: 14px;
    color: #4b5563;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 16px;
}

.auth-submit-btn {
    width: 100%;
    padding: 12px;
    background-color: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 8px;
}

.auth-submit-btn:hover {
    background-color: #2563eb;
}

/* Стили для индикатора свайпа/скролла */
.swipe-indicator {
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(255, 255, 255, 0.8);
    color: #333;
    padding: 10px 20px;
    border-radius: 30px;
    text-align: center;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 100;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    pointer-events: auto; /* Важно для корректной работы событий */
}

.swipe-indicator i {
    font-size: 18px;
}

.swipe-indicator:hover {
    background-color: rgba(255, 255, 255, 0.95);
    transform: translateX(-50%) translateY(-5px);
}

.mobile-text, .desktop-text {
    display: inline-block;
}

/* Показываем соответствующий текст в зависимости от устройства */
@media (max-width: 768px) {
    .desktop-text {
        display: none;
    }
}

@media (min-width: 769px) {
    .mobile-text {
        display: none;
    }
}

/* Стили для QR-кода предпринимателя */
.entrepreneur-qr-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px dashed #ccc;
}

.entrepreneur-qr-section h4 {
    color: #3b82f6;
    margin-bottom: 10px;
}

.entrepreneur-note {
    font-size: 14px;
    color: #666;
    margin-top: 10px;
}

/* Улучшенные стили для модального окна с QR-кодами */
.qr-fullscreen-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.qr-fullscreen-overlay.active {
    opacity: 1;
    visibility: visible;
}

.qr-fullscreen-container {
    background-color: white;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    max-width: 90%;
    width: 400px;
    position: relative;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.qr-close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    cursor: pointer;
    color: #6b7280;
}

/* Стили для информации о серии сертификатов */
.series-info-container {
    margin: 2rem auto;
    max-width: 650px;
    padding: 0 1rem;
}

.series-info-box {
    background-color: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

.series-info-box h4 {
    color: #3b82f6;
    font-size: 1.25rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.text-danger {
    color: #dc3545;
    font-weight: 500;
}

.series-depleted {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #dc3545;
    color: white;
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    text-align: center;
}
</style>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/certificates/partials/styles.blade.php ENDPATH**/ ?>