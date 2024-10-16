// sw.js
const CACHE_NAME = 'oni-mexicano-cache-v1';
const urlsToCache = [
    '/',
    '/index.php',
    '/styles.css',
    '/login.js',
    '/icon-192x192.png',
    '/icon-512x512.png'
];

// Instalar el service worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
});

// Activar el service worker
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Interceptar las solicitudes
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Si hay una respuesta en la caché, devuélvela, si no, haz la solicitud
                return response || fetch(event.request);
            })
    );
});
