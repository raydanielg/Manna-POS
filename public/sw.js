/* MannaPOS Service Worker v1.0 */
const CACHE = 'mannapos-v1';
const STATIC = [
  '/',
  '/manifest.json',
  '/icons/icon.svg',
  '/icons8-dynamics-365-96.png',
  '/icons8-dynamics-365-100.png',
  '/logo.png',
];

self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(CACHE).then((c) => c.addAll(STATIC)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (e) => {
  e.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (e) => {
  const { request } = e;
  const url = new URL(request.url);

  // API calls — network only
  if (url.pathname.startsWith('/api/')) return;

  // Static assets — cache first
  if (
    request.destination === 'style' ||
    request.destination === 'script' ||
    request.destination === 'font' ||
    request.destination === 'image' ||
    url.pathname.match(/\.(?:css|js|woff2?|svg|png|jpg|jpeg|gif|webp|ico)$/)
  ) {
    e.respondWith(
      caches.match(request).then((cached) => cached || fetch(request).then((res) => {
        const clone = res.clone();
        caches.open(CACHE).then((c) => c.put(request, clone));
        return res;
      }))
    );
    return;
  }

  // HTML / navigation — network first, fallback to cache
  e.respondWith(
    fetch(request).then((res) => {
      const clone = res.clone();
      caches.open(CACHE).then((c) => c.put(request, clone));
      return res;
    }).catch(() => caches.match(request).then((cached) => cached || caches.match('/')))
  );
});
