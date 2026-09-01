const CACHE_NAME = 'goldhen-manager-v3-cache';

// Instalación del Service Worker
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// Activación y limpieza de cachés viejos
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Interceptar peticiones (dejamos que el servidor local de Termux haga el trabajo)
self.addEventListener('fetch', (event) => {
    event.respondWith(fetch(event.request));
});
