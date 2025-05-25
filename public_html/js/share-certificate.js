/**
 * Скрипт для функциональности обмена документами
 */

// Функция для копирования публичной ссылки документа в буфер обмена
function copyPublicUrl() {
    // Получаем URL из data-атрибута или формируем из известных данных
    const publicUrl = document.getElementById('certificate-public-url').value;
    
    // Используем современный Clipboard API с запасным вариантом
    if (navigator.clipboard) {
        navigator.clipboard.writeText(publicUrl)
            .then(() => {
                showToast('Ссылка скопирована в буфер обмена', 'success');
            })
            .catch(err => {
                console.error('Ошибка при копировании: ', err);
                fallbackCopyTextToClipboard(publicUrl);
            });
    } else {
        fallbackCopyTextToClipboard(publicUrl);
    }
}

// Запасной метод копирования для браузеров без поддержки Clipboard API
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    // Делаем элемент невидимым
    textArea.style.position = "fixed";
    textArea.style.left = "-999999px";
    textArea.style.top = "-999999px";
    document.body.appendChild(textArea);
    
    // Выбираем и копируем текст
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToast('Ссылка скопирована в буфер обмена', 'success');
        } else {
            showToast('Не удалось скопировать ссылку', 'danger');
        }
    } catch (err) {
        console.error('Ошибка при резервном копировании: ', err);
        showToast('Не удалось скопировать ссылку', 'danger');
    }
    
    document.body.removeChild(textArea);
}

// Функция для отображения всплывающего уведомления
function showToast(message, type = 'success') {
    // Проверяем, существует ли контейнер для тостов, если нет - создаем
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '1050';
        document.body.appendChild(toastContainer);
    }
    
    // Создаем элемент toast
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fa-solid ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Закрыть"></button>
            </div>
        </div>`;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Инициализируем и показываем toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000
    });
    toast.show();
    
    // Удаляем toast после скрытия
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Обработчик отправки формы для email
document.addEventListener('DOMContentLoaded', function() {
    const emailForm = document.getElementById('emailShareForm');
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Получаем данные из формы
            const formData = new FormData(emailForm);
            const submitBtn = emailForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Показываем индикатор загрузки
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка...';
            
            // Отправляем запрос
            fetch(emailForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Показываем сообщение об успехе
                    showToast('документ успешно отправлен на email', 'success');
                    
                    // Закрываем модальное окно
                    const modal = bootstrap.Modal.getInstance(document.getElementById('emailModal'));
                    modal.hide();
                    
                    // Сбрасываем форму
                    emailForm.reset();
                } else {
                    // Показываем сообщение об ошибке
                    showToast(data.message || 'Произошла ошибка при отправке', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Произошла ошибка при отправке', 'danger');
            })
            .finally(() => {
                // Восстанавливаем кнопку
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }
});
