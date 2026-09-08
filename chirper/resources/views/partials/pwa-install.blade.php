{{-- =====================================================================
  Parcial PWA — Banner de instalación + guía iOS + aviso de actualización
  ---------------------------------------------------------------------
  Incluir al final del <body> del layout principal:
      @include('partials.pwa-install')

  Los IDs usados aquí los controla public/js/pwa-install.js:
      #cy-pwa-banner, #cy-pwa-install-btn, #cy-pwa-close-btn,
      #cy-pwa-ios-guide, #cy-pwa-manual-guide,
      #cy-pwa-update, #cy-pwa-update-btn
  ================================================================== --}}

<style>
    /* ===== Banner de instalación (fijo abajo, discreto) ===== */
    .cy-pwa-banner {
        position: fixed;
        left: 1rem; right: 1rem; bottom: 1rem;
        z-index: 1080;
        display: flex; align-items: center; gap: .9rem;
        max-width: 520px; margin: 0 auto;
        background: rgba(22, 33, 62, .96);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(212, 165, 116, .3);
        border-radius: 16px;
        padding: .9rem 1rem;
        box-shadow: 0 12px 40px rgba(0, 0, 0, .5);
        transform: translateY(120%);
        transition: transform .35s cubic-bezier(.4, 0, .2, 1);
    }
    .cy-pwa-banner.cy-pwa-visible { transform: translateY(0); }
    .cy-pwa-banner[hidden] { display: none; }

    .cy-pwa-icon {
        width: 48px; height: 48px; border-radius: 12px; object-fit: cover; flex-shrink: 0;
        border: 1px solid rgba(212, 165, 116, .4);
    }
    .cy-pwa-text { flex: 1; min-width: 0; }
    .cy-pwa-text strong { display: block; font-size: .9rem; color: #e8c9a0; }
    .cy-pwa-text span { font-size: .78rem; color: #cbd5e1; }

    .cy-pwa-actions { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }
    .cy-pwa-install {
        background: linear-gradient(135deg, #d4a574, #c4955e);
        border: none; color: #1a1a2e; font-weight: 700; font-size: .82rem;
        padding: .55rem 1rem; border-radius: 10px; cursor: pointer; white-space: nowrap;
    }
    .cy-pwa-install:active { transform: scale(.97); }
    .cy-pwa-close {
        background: transparent; border: none; color: #cbd5e1;
        font-size: 1.2rem; line-height: 1; cursor: pointer; padding: .25rem .4rem;
    }

    /* ===== Guía paso a paso para iOS ===== */
    .cy-pwa-ios-steps { margin: .6rem 0 0; padding-left: 0; list-style: none; font-size: .8rem; color: #cbd5e1; }
    .cy-pwa-ios-steps li {
        display: flex; align-items: center; gap: .5rem;
        padding: .35rem 0; border-top: 1px solid rgba(255, 255, 255, .06);
    }
    .cy-pwa-ios-steps li:first-child { border-top: none; }
    .cy-pwa-ios-steps .bi { color: #d4a574; font-size: 1rem; }

    /* ===== Toast de nueva versión ===== */
    .cy-pwa-update {
        position: fixed;
        top: 1rem; left: 50%; transform: translateX(-50%);
        z-index: 1090;
        display: flex; align-items: center; gap: .75rem;
        background: rgba(22, 33, 62, .97);
        border: 1px solid rgba(46, 204, 113, .4);
        border-radius: 12px;
        padding: .7rem 1rem;
        font-size: .85rem; color: #f8fafc;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .5);
        max-width: calc(100vw - 2rem);
    }
    .cy-pwa-update[hidden] { display: none; }
    .cy-pwa-update .bi { color: #2ecc71; }
    .cy-pwa-update button {
        background: rgba(46, 204, 113, .15); border: 1px solid rgba(46, 204, 113, .4);
        color: #2ecc71; font-size: .8rem; font-weight: 600;
        padding: .35rem .8rem; border-radius: 8px; cursor: pointer; white-space: nowrap;
    }
</style>

{{-- Banner de instalación (oculto por defecto; lo muestra pwa-install.js) --}}
<div id="cy-pwa-banner" class="cy-pwa-banner" hidden role="dialog" aria-label="Instalar aplicación">
    <img src="{{ asset('images/logopag.png') }}" alt="Casa Yacobone" class="cy-pwa-icon">
    <div class="cy-pwa-text">
        <strong>Instalá Casa Yacobone</strong>
        <span>Acceso rápido desde tu pantalla de inicio.</span>

        {{-- Guía manual solo visible en iOS (la muestra pwa-install.js) --}}
        <ol id="cy-pwa-ios-guide" class="cy-pwa-ios-steps" hidden>
            <li><i class="bi bi-share"></i> Tocá <strong>&nbsp;Compartir&nbsp;</strong> en Safari</li>
            <li><i class="bi bi-plus-square"></i> Elegí <strong>&nbsp;"Añadir a pantalla de inicio"&nbsp;</strong></li>
            <li><i class="bi bi-check-circle"></i> Confirmá con <strong>&nbsp;Añadir</strong></li>
        </ol>

        {{-- Guía manual Android/Chrome cuando no hay prompt nativo (HTTP, webviews, etc.) --}}
        <ol id="cy-pwa-manual-guide" class="cy-pwa-ios-steps" hidden>
            <li><i class="bi bi-three-dots-vertical"></i> Abrí el menú <strong>&nbsp;⋮&nbsp;</strong> de Chrome</li>
            <li><i class="bi bi-plus-square"></i> Tocá <strong>&nbsp;"Instalar app" / "Añadir a pantalla de inicio"</strong></li>
            <li><i class="bi bi-check-circle"></i> Confirmá con <strong>&nbsp;Instalar</strong></li>
        </ol>
    </div>
    <div class="cy-pwa-actions">
        <button id="cy-pwa-install-btn" class="cy-pwa-install" type="button">Instalar</button>
        <button id="cy-pwa-close-btn" class="cy-pwa-close" type="button" aria-label="Cerrar">×</button>
    </div>
</div>

{{-- Aviso de nueva versión disponible (oculto por defecto) --}}
<div id="cy-pwa-update" class="cy-pwa-update" hidden role="status">
    <i class="bi bi-arrow-clockwise"></i>
    <span>Hay una nueva versión disponible.</span>
    <button id="cy-pwa-update-btn" type="button">Actualizar</button>
</div>
