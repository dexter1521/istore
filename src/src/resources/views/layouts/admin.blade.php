<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-size: .875rem;
        }

        .feather {
            width: 16px;
            height: 16px;
            vertical-align: text-bottom;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 56px 0 0;
            /* Altura del navbar */
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            overflow-y: auto;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                top: 0;
                width: 100%;
                height: auto;
                background-color: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
            }
        }

        .nav-link {
            font-weight: 500;
            color: #333;
        }

        .nav-link .feather {
            margin-right: 4px;
            color: #727272;
        }

        .nav-link.active {
            color: #007bff;
        }

        .nav-link.active .feather {
            color: inherit;
        }

        .nav-link:hover .feather,
        .nav-link.active .feather {
            color: inherit;
        }

        /* Asegurar que el header quede por encima y la sidebar no solape el contenido */
        header.navbar {
            z-index: 1030;
        }

        /* Posicionamiento del botón hamburguesa en móviles */
        .navbar .navbar-toggler {
            top: .25rem;
            right: 1rem;
        }

        /* Ancho fijo de la sidebar en pantallas medias/anchas y dejar espacio al main */
        @media (min-width: 768px) {
            .sidebar {
                width: 240px;
            }

            main {
                margin-left: 240px;
            }
        }

        /* Espaciado para el contenido principal */
        main {
            padding-top: 56px;
        }
    </style>
</head>

<body>

    <header class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">iStore Admin</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="nav-link px-3" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();">Cerrar Sesión</a>
                </form>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ url('/admin/dashboard') }}">
                                <span data-feather="home"></span>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.pedidos.kanban') }}">
                                <span data-feather="shopping-cart"></span>
                                Pedidos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.productos.index') }}">
                                <span data-feather="shopping-bag"></span>
                                Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.categorias.index') }}">
                                <span data-feather="layers"></span>
                                Categorías
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.settings.index') }}">
                                <span data-feather="settings"></span>
                                Configuración
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace()
    </script>
</body>

</html>
