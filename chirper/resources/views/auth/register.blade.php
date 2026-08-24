<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — Casa Yacobone</title>
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
            padding: 2rem 1rem;
        }
        .register-card {
            background: rgba(22, 33, 62, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .register-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-brand .icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #d4a574, #e94560);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 0.8rem;
            box-shadow: 0 8px 30px rgba(233, 69, 96, 0.4);
        }
        .register-brand h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #d4a574;
            margin-bottom: 0.2rem;
            letter-spacing: -0.5px;
        }
        .register-brand p {
            color: #a0a0b0;
            font-size: 0.88rem;
        }
        .form-control-custom {
            background: rgba(15, 15, 30, 0.6);
            border: 1px solid rgba(255,255,255,0.08);
            color: #eaeaea;
            border-radius: 12px;
            padding: 0.75rem 1rem 0.75rem 2.8rem;
            font-size: 0.92rem;
            transition: all 0.25s ease;
        }
        .form-control-custom:focus {
            background: rgba(15, 15, 30, 0.8);
            border-color: #d4a574;
            color: #eaeaea;
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.15);
        }
        .form-control-custom::placeholder { color: #666; }
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
        .btn-register {
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
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(233, 69, 96, 0.5);
            color: white;
        }
        .alert-custom {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            border-radius: 12px;
            font-size: 0.88rem;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-brand">
            <div class="icon"><i class="bi bi-person-plus-fill"></i></div>
            <h1>Crear Cuenta</h1>
            <p>Registro de usuario para Casa Yacobone</p>
        </div>

        @if($errors->any())
            <div class="alert alert-custom mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}">
            @csrf

            <div class="mb-3">
                <div class="input-group-icon">
                    <i class="bi bi-person-fill"></i>
                    <input type="text"
                           class="form-control form-control-custom"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Nombre completo"
                           required
                           autofocus>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group-icon">
                    <i class="bi bi-envelope-fill"></i>
                    <input type="email"
                           class="form-control form-control-custom"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Correo electrónico"
                           required>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group-icon">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password"
                           class="form-control form-control-custom"
                           id="password"
                           name="password"
                           placeholder="Contraseña (mínimo 6 caracteres)"
                           required>
                </div>
            </div>

            <div class="mb-4">
                <div class="input-group-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                    <input type="password"
                           class="form-control form-control-custom"
                           id="password_confirmation"
                           name="password_confirmation"
                           placeholder="Confirmar contraseña"
                           required>
                </div>
            </div>

            <button type="submit" class="btn btn-register mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>Registrarse
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" style="color: #d4a574; font-size: 0.88rem; text-decoration: none;">
                    ¿Ya tenés cuenta? Iniciar sesión
                </a>
            </div>
        </form>
    </div>
</body>
</html>
