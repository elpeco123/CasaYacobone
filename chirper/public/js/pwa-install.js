/* ==========================================================================
 * pwa-install.js — Casa Yacobone PWA
 * --------------------------------------------------------------------------
 * Vanilla JS (sin dependencias). Se carga con `defer` desde el layout
 * principal vía {{ asset('js/pwa-install.js') }}.
 *
 * Responsabilidades:
 *  1. Registrar el Service Worker (/service-worker.js).
 *  2. Capturar `beforeinstallprompt` (Android / Chromium) y mostrar el
 *     banner personalizado de instalación. Si el evento no llega
 *     (HTTP, webviews, navegador no compatible), muestra guía manual.
 *  3. Detectar iOS/Safari (sin `beforeinstallprompt`) y mostrar la guía
 *     manual "Compartir → Añadir a pantalla de inicio".
 *  4. Avisar cuando hay una nueva versión del SW lista para activarse.
 *  5. Exponer `CasaYacobonePWA.debug()` para diagnóstico en consola.
 * ========================================================================== */
(() => {
  'use strict';

  // --- Configuración -------------------------------------------------------
  const SW_URL = '/service-worker.js';
  const DISMISS_KEY = 'cy-pwa-banner-dismissed-at';
  const DISMISS_DAYS = 7; // No volver a molestar durante 7 días si lo cierra.
  const SHOW_DELAY_MS = 2500; // Espera antes de mostrar el banner (UX menos invasiva).
  const MANUAL_FALLBACK_MS = 6000; // Si el navegador no ofrece instalación, muestra guía manual.

  // --- Utilidades ----------------------------------------------------------
  const $ = (id) => document.getElementById(id);

  /** ¿Ya está instalada la PWA (modo standalone o tab de app iOS)? */
  const isStandalone = () =>
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true; // Safari iOS (< 16.4 y apps añadidas).

  /** ¿Es un dispositivo iOS (iPhone / iPad / iPod)? */
  const isIOS = () => {
    const ua = window.navigator.userAgent || '';
    // iPadOS 13+ se reporta como "Macintosh": se detecta por táctil + Mac.
    const iPadOS = navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1;
    return /iPhone|iPad|iPod/i.test(ua) || iPadOS;
  };

  /** ¿El usuario descartó el banner hace poco? */
  const recentlyDismissed = () => {
    try {
      const at = Number(localStorage.getItem(DISMISS_KEY) || 0);
      return Date.now() - at < DISMISS_DAYS * 24 * 60 * 60 * 1000;
    } catch (error) {
      return false;
    }
  };

  const markDismissed = () => {
    try {
      localStorage.setItem(DISMISS_KEY, String(Date.now()));
    } catch (error) {
      /* localStorage no disponible: se ignora sin romper nada. */
    }
  };

  // --- 1. Registro del Service Worker --------------------------------------
  async function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) {
      return;
    }

    // El SW solo funciona en HTTPS o localhost: en HTTP de producción falla.
    if (window.location.protocol === 'http:' && !/localhost|127\.0\.0\.1/.test(window.location.hostname)) {
      console.warn('[PWA] El Service Worker requiere HTTPS en producción.');
      return;
    }

    try {
      const registration = await navigator.serviceWorker.register(SW_URL, { scope: '/' });
      console.info('[PWA] Service Worker registrado:', registration.scope);

      // Si hay un SW nuevo esperando, ofrece recargar para actualizar.
      if (registration.waiting) {
        showUpdateToast(registration);
      }

      registration.addEventListener('updatefound', () => {
        const worker = registration.installing;
        if (!worker) {
          return;
        }
        worker.addEventListener('statechange', () => {
          if (worker.state === 'installed' && navigator.serviceWorker.controller) {
            showUpdateToast(registration);
          }
        });
      });
    } catch (error) {
      console.error('[PWA] No se pudo registrar el Service Worker:', error);
    }
  }

  /** Muestra el aviso "Nueva versión disponible" con botón de actualizar. */
  function showUpdateToast(registration) {
    const toast = $('cy-pwa-update');
    const btn = $('cy-pwa-update-btn');
    if (!toast || !btn) {
      return;
    }
    toast.hidden = false;

    btn.addEventListener(
      'click',
      () => {
        registration.waiting?.postMessage({ type: 'SKIP_WAITING' });
        // Cuando el SW nuevo toma el control, recarga para aplicar cambios.
        let reloaded = false;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
          if (!reloaded) {
            reloaded = true;
            window.location.reload();
          }
        });
      },
      { once: true },
    );
  }

  // --- 2. Prompt de instalación (Android / Chromium) ------------------------
  let deferredPrompt = null;
  let promptFired = false; // true si el navegador disparó `beforeinstallprompt`.

  window.addEventListener('beforeinstallprompt', (event) => {
    // Evita el mini-banner automático del navegador: usamos el nuestro.
    event.preventDefault();
    deferredPrompt = event;
    promptFired = true;
    console.info('[PWA] beforeinstallprompt recibido: instalación disponible.');

    if (!isStandalone() && !recentlyDismissed()) {
      setTimeout(() => showBanner('android'), SHOW_DELAY_MS);
    }
  });

  /** Dispara el diálogo nativo de instalación al tocar "Instalar". */
  async function promptNativeInstall() {
    hideBanner();
    if (!deferredPrompt) {
      return;
    }
    deferredPrompt.prompt();
    try {
      await deferredPrompt.userChoice; // { outcome: 'accepted' | 'dismissed' }
    } finally {
      deferredPrompt = null;
    }
  }

  window.addEventListener('appinstalled', () => {
    deferredPrompt = null;
    hideBanner();
    console.info('[PWA] App instalada correctamente.');
  });

  // --- 3. Banner personalizado + guías manuales --------------------------------
  function showBanner(mode) {
    const banner = $('cy-pwa-banner');
    const iosGuide = $('cy-pwa-ios-guide');
    const manualGuide = $('cy-pwa-manual-guide');
    const installBtn = $('cy-pwa-install-btn');
    if (!banner) {
      return;
    }

    // Resetea el contenido según el modo.
    if (iosGuide) {
      iosGuide.hidden = true;
    }
    if (manualGuide) {
      manualGuide.hidden = true;
    }
    if (installBtn) {
      installBtn.hidden = mode !== 'android';
    }

    if (mode === 'ios' && iosGuide) {
      // En iOS mostramos la guía paso a paso en lugar del botón instalar.
      iosGuide.hidden = false;
    }

    if (mode === 'manual' && manualGuide) {
      // Fallback: el navegador no disparó `beforeinstallprompt`
      // (HTTP sin HTTPS, webview de WhatsApp/Instagram, etc.).
      manualGuide.hidden = false;
    }

    banner.hidden = false;
    // Pequeña animación de entrada (la clase la define el parcial Blade).
    requestAnimationFrame(() => banner.classList.add('cy-pwa-visible'));
  }

  function hideBanner() {
    const banner = $('cy-pwa-banner');
    if (!banner) {
      return;
    }
    banner.classList.remove('cy-pwa-visible');
    banner.hidden = true;
  }

  function wireBannerButtons() {
    $('cy-pwa-install-btn')?.addEventListener('click', promptNativeInstall);
    // El botón cerrar sirve tanto para el banner Android como para la guía iOS.
    $('cy-pwa-close-btn')?.addEventListener('click', () => {
      markDismissed();
      hideBanner();
    });
  }

  // --- Arranque --------------------------------------------------------------
  document.addEventListener('DOMContentLoaded', () => {
    registerServiceWorker();
    wireBannerButtons();

    // iOS/Safari no dispara `beforeinstallprompt`: si es iOS, no está
    // instalada y no se descartó hace poco → muestra la guía manual.
    if (isIOS() && !isStandalone() && !recentlyDismissed()) {
      setTimeout(() => showBanner('ios'), SHOW_DELAY_MS);
      return;
    }

    // Fallback Android/Chromium: si tras unos segundos el navegador NO
    // ofreció instalación (típico en HTTP sin HTTPS, webviews de
    // WhatsApp/Instagram/Facebook o navegador no compatible), muestra
    // igual el banner pero con instrucciones manuales paso a paso.
    if (!isIOS() && !isStandalone() && !recentlyDismissed()) {
      setTimeout(() => {
        if (!promptFired && !deferredPrompt) {
          console.info('[PWA] Sin beforeinstallprompt: muestro guía manual.');
          showBanner('manual');
        }
      }, MANUAL_FALLBACK_MS);
    }
  });

  // --- Diagnóstico remoto --------------------------------------------------
  // Desde la consola del celular (chrome://inspect) o DevTools ejecutar:
  //   CasaYacobonePWA.debug()
  window.CasaYacobonePWA = {
    debug() {
      return {
        standalone: isStandalone(),
        ios: isIOS(),
        serviceWorkerSoportado: 'serviceWorker' in navigator,
        protocolo: window.location.protocol,
        host: window.location.host,
        contextoSeguro: window.isSecureContext,
        promptRecibido: promptFired,
        bannerDescartado: recentlyDismissed(),
        userAgent: window.navigator.userAgent,
      };
    },
  };
})();
