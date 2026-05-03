// Simple Service Worker to make the PWA "valid" for iOS/Android
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    // Standard fetch behavior
    event.respondWith(fetch(event.request));
});
