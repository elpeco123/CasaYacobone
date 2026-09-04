<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Casa Yacobone</title>
    <link rel="icon" type="image/jpeg" href="/images/logo.jpeg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0a0a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(22, 33, 62, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-brand .logo-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            box-shadow: 0 8px 30px rgba(233, 69, 96, 0.4);
            border: 3px solid rgba(212, 165, 116, 0.4);
        }
        .login-brand h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #d4a574;
            margin-bottom: 0.3rem;
            letter-spacing: -0.5px;
        }
        .login-brand p {
            color: #a0a0b0;
            font-size: 0.88rem;
        }
        .form-control-login {
            background: rgba(15, 15, 30, 0.6);
            border: 1px solid rgba(255,255,255,0.08);
            color: #eaeaea;
            border-radius: 12px;
            padding: 0.75rem 1rem 0.75rem 2.8rem;
            font-size: 0.92rem;
            transition: all 0.25s ease;
        }
        .form-control-login:focus {
            background: rgba(15, 15, 30, 0.8);
            border-color: #d4a574;
            color: #eaeaea;
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.15);
        }
        .form-control-login::placeholder { color: #666; }
        .input-group-icon {
            position: relative;
        }
        .input-group-icon i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #a0a0b0;
            z-index: 5;
            font-size: 1rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #e94560, #c73e54);
            border: none;
            color: white;
            font-weight: 700;
            padding: 0.75rem;
            border-radius: 12px;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(233, 69, 96, 0.4);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(233, 69, 96, 0.5);
            color: white;
        }
        .form-check-input:checked {
            background-color: #d4a574;
            border-color: #d4a574;
        }
        .form-check-label { color: #a0a0b0; font-size: 0.85rem; }
        .alert-login {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            border-radius: 12px;
            font-size: 0.88rem;
        }
        .alert-login-success {
            background: rgba(46, 204, 113, 0.1);
            border: 1px solid rgba(46, 204, 113, 0.3);
            color: #2ecc71;
            border-radius: 12px;
            font-size: 0.88rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <img src="/images/logologin.jpeg" alt="Casa Yacobone" class="logo-img">
            <h1>Casa Yacobone</h1>
            <p>Sistema de Control de Stock</p>
        </div>

        @if(session('success'))
            <div class="alert alert-login-success mb-3">
                <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-login mb-3">
                @foreach($errors->all() as $error)
                    <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="mb-3">
                <div class="input-group-icon">
                    <i class="bi bi-envelope-fill"></i>
                    <input type="email"
                           class="form-control form-control-login"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Correo electrónico"
                           required
                           autofocus>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group-icon">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password"
                           class="form-control form-control-login"
                           id="password"
                           name="password"
                           placeholder="Contraseña"
                           required>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Recordarme</label>
                </div>
            </div>

            <button type="submit" class="btn btn-login mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>

            <div class="text-center">
                <a href="{{ route('register') }}" style="color: #d4a574; font-size: 0.88rem; text-decoration: none;">
                    ¿No tenés cuenta? Registrarse
                </a>
            </div>
        </form>
    </div>
</body>
</html>
