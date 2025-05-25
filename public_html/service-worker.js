// Минимальный service worker без кеширования страниц
const CACHE_NAME = 'sticap-static-v1';

// При установке сервис-воркера не кешируем ничего
self.addEventListener('install', (event) => {
  self.skipWaiting();
});

// При активации очищаем старые кеши
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.filter((name) => {
          return name !== CACHE_NAME;
        }).map((name) => {
          return caches.delete(name);
        })
      );
    })
  );
  self.clients.claim();
});

// При fetch-запросе не используем кеш, а пропускаем запросы напрямую к серверу
self.addEventListener('fetch', (event) => {
  // Пропускаем все запросы напрямую, без кеширования
  return;
});
