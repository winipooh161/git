// Регистрация Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/service-worker.js')
      .then((registration) => {
        console.log('ServiceWorker registered with scope:', registration.scope);
      })
      .catch((error) => {
        console.error('ServiceWorker registration failed:', error);
      });
  });
}

// Проверка установки PWA
function isPwaInstalled() {
  // Проверяем режим отображения
  if (window.matchMedia('(display-mode: standalone)').matches 
      || window.navigator.standalone === true) {
    return true;
  }
  return false;
}

// Экспортируем функцию для использования в других модулях
export { isPwaInstalled };
