var taPlannerVersion = '0.9.3.1',
    cacheName = `ta-planner-pwa-${taPlannerVersion}`,
    filesToCache = [
        'planner.html',
        'data.js',
        'main.js',

        'ownstyle.css',
        'materialize.min.css',
        'materialize.min.js',

        'assets/Hochsprung.png',
        'assets/Huerdenlauf.png',
        'assets/Koordination.png',
        'assets/Lauf.png',
        'assets/Weitsprung.png',
        'assets/Wurf.png',
        'assets/MaterialSymbolsOutlined.woff2',
    ];

/* Start the service worker and cache all of the app's content */
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(cacheName)
              .then( (cache) => cache.addAll(filesToCache))
    );
});

self.addEventListener('activate', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(keyList.map((key) => {
                if (key !== cacheName) {
                    return caches.delete(key);
                }
            }));
        })
    );
    self.clients.claim();
});

/* Serve cached content when offline */
// self.addEventListener('fetch', (event) => event.respondWith( networkFirst(event.request)) );
// const networkFirst = async (request) => {
//     return fetch(request).catch( (exception) => {
//         return caches.open(cacheName).then(function(cache) {
//           return cache.match(request,
//                              {'ignoreSearch': true}).then(response => response);
//         });
//     });
// };

/* Serve cached content first */
self.addEventListener('fetch', event =>  event.respondWith(cacheFirst(event.request)));
const cacheFirst = async (request) => {
    const responseFromCache = await caches.match(request);
    if (responseFromCache) {
      return responseFromCache;
    }
    return fetch(request);
  };