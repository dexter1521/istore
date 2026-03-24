@php
    $productosDestacados = $productosDestacados ?? collect();
    $negocio = setting('nombre_negocio', config('app.name', 'Chiles y Semillas San José'));
    $whatsappRaw = setting('whatsapp_numero', '');
    $whatsapp = preg_replace('/\D+/', '', $whatsappRaw);
    $waLink = $whatsapp ? "https://wa.me/{$whatsapp}" : '#';

    // Categorías para el panel lateral y el grid principal
    $categoriasPrincipales = $categoriasMenu ?? collect();
    $categoriasDestacadasGrid = collect($categoriasMenu ?? [])->take(6);
@endphp

@extends('layouts.public')

@push('styles')
    <style>
        :root {
            --color-sj-red: #C12A2F;
            --color-sj-yellow: #FFF7D1;
        }

        .sidebar-category {
            transition: all 0.2s;
            border-radius: 8px;
            color: #444;
        }

        .sidebar-category:hover {
            background-color: var(--color-sj-yellow);
            color: var(--color-sj-red);
            padding-left: 12px;
        }

        .category-card {
            border: 1px solid #eee;
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 15px;
            overflow: hidden;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .bg-sj-gradient {
            background: linear-gradient(135deg, var(--color-sj-red) 0%, #8b1d21 100%);
            border-radius: 20px;
        }

        .category-initial {
            width: 40px;
            height: 40px;
            background: var(--color-sj-yellow);
            color: var(--color-sj-red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="row g-4">
        <aside class="col-lg-3 d-none d-lg-block">
            <div class="card border-0 shadow-sm p-3 sticky-top" style="top: 100px; z-index: 10;">
                <h5 class="fw-bold mb-4 px-2 border-bottom pb-2">
                    <i class="feather-grid me-2 text-danger"></i>Categorías
                </h5>
                <div class="nav flex-column">
                    @forelse($categoriasPrincipales as $cat)
                        <a href="{{ route('categorias.show', $cat->id) }}" class="nav-link sidebar-category py-2 mb-1">
                            {{ $cat->nombre }}
                        </a>
                    @empty
                        <p class="text-muted small ps-2">No hay categorías disponibles.</p>
                    @endforelse
                </div>

                <div class="mt-4 p-3 bg-light rounded-3">
                    <p class="small text-muted mb-0 text-center">
                        ¿Necesitas ayuda? <br>
                        <a href="{{ $waLink }}" class="text-success fw-bold text-decoration-none">
                            <i class="fab fa-whatsapp"></i> Chat de Ayuda
                        </a>
                    </p>
                </div>
            </div>
        </aside>

        <main class="col-lg-9">
            <div class="bg-sj-gradient p-4 p-md-5 text-white mb-5 position-relative overflow-hidden">
                <div class="position-relative" style="z-index: 2;">
                    <h1 class="display-6 fw-bold">¡Bienvenido a San José!</h1>
                    <p class="lead opacity-90">Negocio de tradición en Misantla, Veracruz. Selecciona una categoría para
                        armar tu pedido.</p>
                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('catalogo.index') }}" class="btn btn-light rounded-pill px-4">Explorar Todo</a>
                    </div>
                </div>
                <div class="position-absolute end-0 bottom-0 opacity-10 p-4">
                    <i class="fas fa-store fa-10x"></i>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <h5 class="fw-bold mb-2">Tradición en Misantla, Veracruz</h5>
                            <p class="text-muted mb-0">Dirección: Calle Melchor Ocampo SN, Colonia Centro, Misantla,
                                Veracruz, CP 93821.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a class="btn btn-outline-danger rounded-pill px-4"
                                href="https://www.google.com/maps/search/?api=1&query=Calle%20Melchor%20Ocampo%20SN%2C%20Colonia%20Centro%2C%20Misantla%2C%20Veracruz%2C%2093821"
                                target="_blank" rel="noopener">
                                Ver en Google Maps
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <section class="py-4">
                <h2 class="text-center section-title">Como hacer tu pedido</h2>
                <div class="row text-center mt-3">
                    <div class="col-md-4 mb-4">
                        <div class="mb-3 text-danger">
                            <i class="fas fa-search fa-3x"></i>
                        </div>
                        <h5 class="fw-semibold">1. Consulta el catálogo</h5>
                        <p class="text-muted">Explora nuestros productos por categoría.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="mb-3 text-danger">
                            <i class="fas fa-cart-plus fa-3x"></i>
                        </div>
                        <h5 class="fw-semibold">2. Agrega lo que necesitas</h5>
                        <p class="text-muted">Selecciona cantidades y arma tu carrito.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="mb-3 text-success">
                            <i class="fab fa-whatsapp fa-3x"></i>
                        </div>
                        <h5 class="fw-semibold">3. Confirma por WhatsApp</h5>
                        <p class="text-muted">Te atendemos de inmediato.</p>
                    </div>
                </div>
            </section>

            <div class="mb-5">
                <h3 class="fw-bold mb-4">Categorías Destacadas</h3>
                <div class="row g-4">
                    @forelse($categoriasDestacadasGrid as $categoria)
                        <div class="col-md-4 col-sm-6">
                            <a href="{{ route('categorias.show', $categoria->id) }}" class="text-decoration-none text-dark">
                                <div class="card h-100 category-card p-3 shadow-sm border-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="category-initial">
                                            {{ strtoupper(mb_substr($categoria->nombre, 0, 1)) }}
                                        </div>
                                        <h6 class="mb-0 fw-bold">{{ $categoria->nombre }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">Próximamente más categorías para ti.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($productosDestacados->count())
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <h3 class="fw-bold mb-0">Lo más pedido</h3>
                        <a href="{{ route('catalogo.index') }}" class="text-danger fw-bold text-decoration-none">Ver todo</a>
                    </div>
                    <div class="row g-3">
                        @foreach($productosDestacados->take(4) as $producto)
                            <div class="col-md-3 col-6">
                                @include('components.product-card-simple', ['producto' => $producto])
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </main>
    </div>

    <div class="d-lg-none position-fixed bottom-0 start-0 w-100 p-3 bg-white shadow-lg border-top" style="z-index: 1000;">
        <button class="btn btn-danger w-100 fw-bold py-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCategories">
            <i class="feather-menu me-2"></i>Ver Categorías
        </button>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasCategories">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold">Categorías</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="nav flex-column">
                @foreach($categoriasPrincipales as $cat)
                    <a href="{{ route('categorias.show', $cat->id) }}" class="nav-link py-3 border-bottom text-dark">
                        {{ $cat->nombre }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection