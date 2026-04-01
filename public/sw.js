// Service Worker pour la PWA Dépi'Stage
const CACHE_NAME = 'depistage-app-v1';
const urlsToCache = [
  '/',
  '/css/styles.css',
  '/css/components.css',
  '/css/pages.css',
  '/css/home.css',
  '/js/navigation.js',
  '/js/components.js',
  '/logo/depistage.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        // Optionnel: on utilise addAll avec un catch pour ne pas bloquer si un fichier n'est pas 200 OK
        return Promise.allSettled(urlsToCache.map(url => {
            return fetch(url).then(response => {
                if(response.ok) return cache.put(url, response);
            });
        }));
      })
  );
  self.skipWaiting();
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Redirection vers le réseau si pas dans le cache
        // En mode dynamique (online-first avec fallback PWA minimal)
        return fetch(event.request).catch(() => response);
      })
  );
});

self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});
