@extends('layouts.public')

@section('content')

<div class="row g-4">
    <!-- Product Images -->
    <div class="col-md-6 mb-4">
        @if($producto->imagenes->count() > 0)
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($producto->imagenes as $index => $imagen)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ route('media.show', ['path' => $imagen->path]) }}"
                        class="d-block w-100 rounded"
                        alt="{{ $producto->nombre }}"
                        style="height: 420px; object-fit: cover;">
                </div>
                @endforeach
            </div>
            @if($producto->imagenes->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
            @endif
        </div>
        @else
        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
            <span class="text-muted fs-1">&box;</span>
        </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="col-md-6">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $producto->nombre }}</li>
            </ol>
        </nav>

        <h1 class="h2 mb-3">{{ $producto->nombre }}</h1>

        @if($producto->categoria)
        <p class="text-muted mb-2">
            <strong>Categoría:</strong> {{ $producto->categoria->nombre }}
        </p>
        @endif

        @if($producto->sku)
        <p class="text-muted mb-3">
            <strong>SKU:</strong> {{ $producto->sku }}
        </p>
        @endif

        @if(show_prices())
        <h3 class="text-success mb-4">
            {{ currency_symbol() }}{{ number_format($producto->getPrecioPorCantidad(1) ?? 0, 2) }}
            @if($producto->unidad_medida)
                <small class="text-muted">/ {{ $producto->unidad_medida }}</small>
            @endif
        </h3>
        @endif

        @if($producto->descripcion)
        <div class="mb-4">
            <h5>Descripción</h5>
            <p class="text-muted">{{ $producto->descripcion }}</p>
        </div>
        @endif

        @php
            $tieneTiers = collect([
                ['cantidad' => $producto->cantidad2, 'precio' => $producto->precio2],
                ['cantidad' => $producto->cantidad3, 'precio' => $producto->precio3],
                ['cantidad' => $producto->cantidad4, 'precio' => $producto->precio4],
                ['cantidad' => $producto->cantidad5, 'precio' => $producto->precio5],
            ])->filter(fn($tier) => !empty($tier['cantidad']) && $tier['precio'] !== null)->count() > 0;
        @endphp

        @if(show_prices() && $tieneTiers)
        <div class="mb-4">
            <h5>Precios por cantidad</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Desde</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1 {{ $producto->unidad_medida ?? 'unidad' }}</td>
                            <td>{{ currency_symbol() }}{{ number_format($producto->getPrecioPorCantidad(1), 2) }}</td>
                        </tr>
                        @foreach([
                            ['cantidad' => $producto->cantidad2, 'precio' => $producto->precio2],
                            ['cantidad' => $producto->cantidad3, 'precio' => $producto->precio3],
                            ['cantidad' => $producto->cantidad4, 'precio' => $producto->precio4],
                            ['cantidad' => $producto->cantidad5, 'precio' => $producto->precio5],
                        ] as $tier)
                            @if(!empty($tier['cantidad']) && $tier['precio'] !== null)
                                <tr>
                                    <td>{{ $tier['cantidad'] }} {{ $producto->unidad_medida ?? 'unidades' }}</td>
                                    <td>{{ currency_symbol() }}{{ number_format($tier['precio'], 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Add to Cart Form -->
        <form method="POST" action="{{ route('carrito.agregar') }}" class="mb-3">
            @csrf
            <input type="hidden" name="producto_id" value="{{ $producto->id }}">

            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <label for="cantidad" class="col-form-label">Cantidad:</label>
                </div>
                <div class="col-auto">
                    <input type="number" id="cantidad" name="cantidad" value="1" min="1" class="form-control"
                        style="width: 80px;">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                Agregar al carrito
            </button>
        </form>

        <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100">
            &larr; Volver al catálogo
        </a>
    </div>
</div>

@endsection
