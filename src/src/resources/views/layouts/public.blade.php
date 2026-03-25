<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')

    <style>
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1050;
        }
    </style>

    <!-- Bootstrap JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Feather icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.feather) feather.replace();
        });
    </script>
</head>

<body class="d-flex flex-column min-vh-100">
    @php
        $carritoCount = collect(session('carrito', []))->sum('cantidad');
    @endphp
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('catalogo.index') }}">Catálogo</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Categorías</a>
                        <ul class="dropdown-menu">
                            @if(isset($categoriasMenu) && $categoriasMenu->count())
                            @foreach($categoriasMenu as $categoria)
                            <li><a class="dropdown-item" href="{{ route('categorias.show', $categoria->id) }}">{{ $categoria->nombre }}</a></li>
                            @endforeach
                            @else
                            <li><span class="dropdown-item-text">No hay categorías</span></li>
                            @endif
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="{{ route('carrito.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M7 4h-2l-1 2h2l2.4 9.6a2 2 0 0 0 2 1.4h7.9a2 2 0 0 0 2-1.5l1.4-6.5a1 1 0 0 0-1-1.2h-12.2l-.5-2.8a1 1 0 0 0-1-.8zm4.5 15a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm8 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                            </svg>
                            <span>Carrito</span>
                            <span class="badge rounded-pill bg-primary {{ $carritoCount > 0 ? '' : 'd-none' }}">{{ $carritoCount }}</span>
                        </a>
                    </li>
                </ul>
                <form class="d-flex position-relative" method="GET" action="{{ route('catalogo.index') }}" autocomplete="off">
                    @if(request()->has('categoria'))
                    <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                    @endif
                    <input class="form-control me-2" type="search" id="searchInput" name="q" value="{{ request('q') }}" placeholder="Buscar" aria-label="Buscar">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                    <div id="searchSuggestions" class="list-group search-suggestions"></div>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4 pb-5 flex-grow-1">
        <div class="container">
            @if(session('success'))
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
                <div id="carritoToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            @endif
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-light mt-auto py-3 border-top">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
            @auth
                <a class="nav-link d-inline-block" href="{{ url('/dashboard') }}">Dashboard</a>
            @else
                <a class="nav-link d-inline-block" href="{{ route('login') }}">Iniciar Sesión</a>
            @endauth
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastEl = document.getElementById('carritoToast');
            if (toastEl && window.bootstrap) {
                var toast = new bootstrap.Toast(toastEl, {
                    delay: 2500
                });
                toast.show();
            }
        });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('searchInput');
            const box = document.getElementById('searchSuggestions');
            if (!input || !box) return;

            let timer = null;

            const clearBox = () => {
                box.innerHTML = '';
                box.style.display = 'none';
            };

            const render = (items) => {
                if (!items.length) {
                    clearBox();
                    return;
                }
                box.innerHTML = items.map(item => {
                    const sku = item.sku ? `<small class="text-muted">${item.sku}</small>` : '';
                    return `<a class="list-group-item list-group-item-action" href="${item.url}">${item.nombre} ${sku}</a>`;
                }).join('');
                box.style.display = 'block';
            };

            const fetchSuggestions = async (q) => {
                const url = new URL("{{ route('catalogo.sugerencias') }}", window.location.origin);
                url.searchParams.set('q', q);
                const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return [];
                return await res.json();
            };

            input.addEventListener('input', function() {
                const q = input.value.trim();
                clearTimeout(timer);
                if (q.length < 2) {
                    clearBox();
                    return;
                }
                timer = setTimeout(async () => {
                    const items = await fetchSuggestions(q);
                    render(items);
                }, 200);
            });

            document.addEventListener('click', function(e) {
                if (!box.contains(e.target) && e.target !== input) {
                    clearBox();
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
