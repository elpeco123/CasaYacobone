{{-- =====================================================================
  Vista "Sin conexión" — Casa Yacobone PWA
  ---------------------------------------------------------------------
  Página 100% autocontenida (CSS y JS en línea, sin CDNs ni @vite):
  el Service Worker la sirve desde caché cuando no hay red, así que
  NO debe depender de ningún asset externo.
  Ruta pública: GET /offline (ver routes/web.php)
  ================================================================== --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1f1510">
    <title>Sin conexión — Casa Yacobone</title>
    <link rel="icon" type="image/png" href="/images/logopag.png">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; }
        body {
            background: linear-gradient(135deg, #1f1510 0%, #2b1d15 50%, #170f0b 100%);
            min-height: 100vh; min-height: 100dvh;
            color: #f2e6d2;
            display: flex; align-items: center; justify-content: center;
            padding: 1.5rem; text-align: center;
        }
        .card {
            background: rgba(43, 29, 21, .6);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            max-width: 420px; width: 100%;
        }
        .logo {
            width: 84px; height: 84px; border-radius: 50%; object-fit: cover;
            border: 2px solid rgba(212, 154, 82, .5);
            margin-bottom: 1.25rem;
        }
        h1 { font-size: 1.35rem; font-weight: 800; color: #ecc58f; margin-bottom: .5rem; }
        p { font-size: .92rem; color: #cdb99c; line-height: 1.6; margin-bottom: 1.5rem; }
        .status {
            display: inline-flex; align-items: center; gap: .5rem;
            background: rgba(224, 163, 58, .12);
            border: 1px solid rgba(224, 163, 58, .35);
            color: #f39c12; font-size: .8rem; font-weight: 600;
            padding: .4rem .9rem; border-radius: 999px; margin-bottom: 1.5rem;
        }
        .status .dot { width: 8px; height: 8px; border-radius: 50%; background: #f39c12; animation: blink 1.4s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: .3; } }
        .btn {
            display: inline-block; width: 100%;
            background: linear-gradient(135deg, #d49a52, #b9803e);
            color: #1f1510; font-weight: 700; font-size: .95rem;
            padding: .8rem 1.25rem; border: none; border-radius: 12px; cursor: pointer;
        }
        .btn:active { transform: scale(.98); }
    </style>
</head>
<body>
    <main class="card">
        <img src="/images/logopag.png" alt="Casa Yacobone" class="logo">
        <div class="status"><span class="dot"></span> Sin conexión a internet</div>
        <h1>Estás offline</h1>
        <p>No pudimos conectarnos al servidor de Casa Yacobone. Revisá tu conexión Wi-Fi o tus datos móviles e intentá de nuevo.</p>
        <button class="btn" onclick="window.location.reload()">Reintentar conexión</button>
    </main>
</body>
</html>
