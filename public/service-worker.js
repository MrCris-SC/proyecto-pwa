const CACHE_NAME = "app-cache-v1";
const urlsToCache = [
    "/",
    "/css/app.css",
    "/js/app.js",
    "/images/icons/icon-192x192.png",
    "/images/icons/icon-512x512.png"
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(urlsToCache);
        })
    );
});

self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
