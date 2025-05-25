// Функция инициализации сайдбара
export function initSidebar() {
    // Код инициализации сайдбара
    console.log('Sidebar initialized');
}

// Функция для управления сворачиванием/разворачиванием сайдбара
export function initSidebarCollapse() {
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const app = document.getElementById('app');
    
    // Проверяем сохраненное состояние меню
    const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    // Применяем сохраненное состояние при загрузке
    if (isSidebarCollapsed && app) {
        app.classList.add('sidebar-collapsed');
        if (sidebarToggleBtn && sidebarToggleBtn.querySelector('i')) {
            sidebarToggleBtn.querySelector('i').classList.replace('fa-bars', 'fa-bars-staggered');
        }
    }
    
    // Обработчик для кнопки в заголовке (переключение)
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function() {
            if (app) {
                app.classList.toggle('sidebar-collapsed');
                const isNowCollapsed = app.classList.contains('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', isNowCollapsed);
                
                // Обновляем иконку
                const icon = this.querySelector('i');
                if (icon) {
                    if (isNowCollapsed) {
                        icon.classList.replace('fa-bars', 'fa-bars-staggered');
                    } else {
                        icon.classList.replace('fa-bars-staggered', 'fa-bars');
                    }
                }
            }
        });
    }
    
    // На мобильных устройствах закрываем меню при клике на пункт
    const mobileLinks = document.querySelectorAll('.offcanvas-body .nav-link');
    mobileLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            const sidebarMenu = document.getElementById('sidebarMenu');
            // Проверяем размер экрана и наличие экземпляра offcanvas
            if (window.innerWidth < 992 && sidebarMenu) {
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebarMenu);
                if (bsOffcanvas) {
                    bsOffcanvas.hide();
                }
            }
        });
    });
    
    // Добавляем обработчик события изменения размера окна
    window.addEventListener('resize', function() {
        // Если окно стало шире 992px, и меню открыто, закрываем его программно
        if (window.innerWidth >= 992) {
            const sidebarMenu = document.getElementById('sidebarMenu');
            if (sidebarMenu) {
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebarMenu);
                if (bsOffcanvas) {
                    bsOffcanvas.hide();
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Инициализация всплывающих подсказок Bootstrap
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
    
    // Функционал для сворачивания/разворачивания бокового меню
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
    const app = document.getElementById('app');
    
    // Проверяем сохраненное состояние меню
    const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    // Применяем сохраненное состояние при загрузке
    if (isSidebarCollapsed) {
        app.classList.add('sidebar-collapsed');
    }
    
    // Обработчик для кнопки в заголовке (переключение)
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function() {
            app.classList.toggle('sidebar-collapsed');
            const isNowCollapsed = app.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isNowCollapsed);
            
            // Обновляем иконку
            const icon = sidebarToggleBtn.querySelector('i');
            icon.classList.toggle('fa-bars', !isNowCollapsed);
            icon.classList.toggle('fa-bars-staggered', isNowCollapsed);
        });
    }
    
    // Обработчик для кнопки в футере (сворачивание)
    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener('click', function() {
            app.classList.add('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', 'true');
            
            // Обновляем иконку основной кнопки
            if (sidebarToggleBtn) {
                const toggleIcon = sidebarToggleBtn.querySelector('i');
                toggleIcon.classList.replace('fa-bars', 'fa-bars-staggered');
            }
        });
    }
    
    // На мобильных устройствах закрываем меню при клике на пункт
    const mobileLinks = document.querySelectorAll('.offcanvas-body .nav-link');
    mobileLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            const sidebarMenu = document.getElementById('sidebarMenu');
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebarMenu);
            if (bsOffcanvas && window.innerWidth < 992) {
                bsOffcanvas.hide();
            }
        });
    });
    
    // Обработка состояния подменю при клике
    const dropdownLinks = document.querySelectorAll('.sidebar-dropdown > .nav-link');
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function() {
            const caret = this.querySelector('.sidebar-caret');
            if (caret) {
                caret.classList.toggle('rotate-caret');
            }
        });
    });
    
    // Проверка, нужно ли открыть подменю при загрузке (если оно активно)
    const activeDropdowns = document.querySelectorAll('.sidebar-dropdown > .nav-link.active');
    activeDropdowns.forEach(link => {
        const caret = link.querySelector('.sidebar-caret');
        if (caret) {
            caret.classList.add('rotate-caret');
        }
    });
});
