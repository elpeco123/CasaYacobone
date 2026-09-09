/* ==========================================================================
 * Service Worker — Casa Yacobone PWA
 * --------------------------------------------------------------------------
 * Ubicación: public/service-worker.js  (debe servirse desde la raíz "/" para
 * tener alcance sobre toda la app).
 *
 * Estrategias:
 *  - Navegaciones (HTML)  → network-first con fallback a /offline
 *  - Assets propios (build/, images/, fonts) → stale-while-revalidate
 *  - Todo lo demás (CDN, POST, etc.) → pasa directo a red, sin caché
 * ========================================================================== */

// Cambiar este valor en cada despliegue para forzar la actualización del caché.
const VERSION = 'v1.0.1';
const STATIC_CACHE = `casayacobone-static-${VERSION}`;
const RUNTIME_CACHE = `casayacobone-runtime-${VERSION}`;

// App-shell mínimo que se precachea en la instalación.
// Son URLs estables (no llevan hash de Vite), por eso son seguras de precachear.
const PRECACHE_URLS = [
  '/offline',
  '/manifest.json',
  '/images/logopag.png',
  '/images/logo.jpeg',
  '/icons/icon-192.png',
  '/icons/maskable-192.png',
];

/* ---------- Instalación: precachea el app-shell y toma control rápido ---------- */
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches
      .open(STATIC_CACHE)
      .then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => self.skipWaiting())
      .catch((error) => console.error('[SW] Falló el precache:', error)),
  );
});

/* ---------- Activación: limpia cachés viejos y reclama los clientes ---------- */
self.addEventListener('activate', (event) => {
  event.waitUntil(
    (async () => {
      // Borra cachés de versiones anteriores.
      const keys = await caches.keys();
      await Promise.all(
        keys
          .filter((key) => key.startsWith('casayacobone-') && key !== STATIC_CACHE && key !== RUNTIME_CACHE)
          .map((key) => caches.delete(key)),
      );

      // Acelera las navegaciones cuando el navegador lo soporta.
      if ('navigationPreload' in self.registration) {
        try {
          await self.registration.navigationPreload.enable();
        } catch (error) {
          console.warn('[SW] navigationPreload no disponible:', error);
        }
      }

      await self.clients.claim();
    })(),
  );
});

/* ---------- Mensajes desde la página (ej: forzar actualización) ---------- */
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

/* ---------- Intercepción de peticiones ---------- */
self.addEventListener('fetch', (event) => {
  const { request } = event;

  // Solo interceptamos GET del mismo origen y fuera de extensiones.
  if (request.method !== 'GET' || !request.url.startsWith(self.location.origin)) {
    return;
  }

  // Las navegaciones (cambios de página) usan network-first + fallback offline.
  if (request.mode === 'navigate') {
    event.respondWith(handleNavigation(event));
    return;
  }

  // Assets propios versionados o estáticos: stale-while-revalidate.
  if (isCacheableAsset(new URL(request.url).pathname)) {
    event.respondWith(staleWhileRevalidate(request));
    return;
  }

  // Resto (ej: respuestas dinámicas de Laravel): solo red, sin caché.
});

/**
 * Network-first para navegaciones.
 * Si no hay red, sirve la página /offline precacheada.
 */
async function handleNavigation(event) {
  try {
    // Usa la respuesta precargada por el navegador si existe.
    const preload = await event.preloadResponse;
    if (preload) {
      return preload;
    }

    return await fetch(event.request);
  } catch (error) {
    const cache = await caches.open(STATIC_CACHE);
    const offline = await cache.match('/offline');

    return offline || Response.error();
  }
}

/**
 * Stale-while-revalidate: responde desde caché al instante y
 * actualiza la copia en segundo plano.
 */
async function staleWhileRevalidate(request) {
  const cache = await caches.open(RUNTIME_CACHE);
  const cached = await cache.match(request);

  const networkUpdate = fetch(request)
    .then((response) => {
      // Solo cachea respuestas válidas (200 y no opacas con error).
      if (response && (response.status === 200 || response.type === 'opaque')) {
        cache.put(request, response.clone());
      }

      return response;
    })
    .catch(() => cached);

  return cached || networkUpdate;
}

/** Rutas de assets propios que vale la pena cachear en runtime. */
function isCacheableAsset(pathname) {
  return (
    pathname.startsWith('/build/') ||
    pathname.startsWith('/images/') ||
    pathname.startsWith('/icons/') ||
    pathname.startsWith('/fonts/') ||
    pathname === '/favicon.ico' ||
    pathname === '/manifest.json' ||
    pathname === '/js/pwa-install.js'
  );
}
