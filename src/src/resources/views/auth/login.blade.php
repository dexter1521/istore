<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name', 'Laravel') }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }
    </style>
</head>

<body class="text-center">
    <main class="form-signin">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <h1 class="h3 mb-3 fw-normal">Iniciar Sesión</h1>

            <!-- Session Status -->
            @if (session('status'))
            <div class="alert alert-success mb-3" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0 list-unstyled">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
                <label for="email">Correo Electrónico</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password">Contraseña</label>
            </div>

            <div class="checkbox mb-3 text-start">
                <label>
                    <input type="checkbox" name="remember" value="1"> Recordarme
                </label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Ingresar</button>

            @if (Route::has('password.request'))
            <p class="mt-3 mb-3 text-muted">
                <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            </p>
            @endif
            <p class="mt-5 mb-3 text-muted">&copy; {{ date('Y') }}</p>
        </form>
    </main>
</body>

</html>
