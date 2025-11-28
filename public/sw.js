// public/sw.js
const CACHE = 'safeclick-v1';
const ASSETS = [
  '/',
  '/?action=home',
  '/public/css/style.css',
  '/public/js/app.js',
  '/public/manifest.json'
];

self.addEventListener('install', event => {
  event.waitUntil(caches.open(CACHE).then(cache => cache.addAll(ASSETS)));
});

self.addEventListener('activate', event => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
  const req = event.request;
  // try network then cache for API calls
  if (req.url.includes('/?action=api')) {
    event.respondWith(fetch(req).catch(()=>caches.match(req)));
    return;
  }
  event.respondWith(caches.match(req).then(resp => resp || fetch(req)));
});