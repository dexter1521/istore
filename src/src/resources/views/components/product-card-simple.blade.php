@php
    $imagenPrincipal = $producto->imagenes->sortBy('orden')->first();
@endphp

<div class="card h-100 shadow-sm border-0">
    @if($imagenPrincipal)
        <img src="{{ route('media.show', ['path' => $imagenPrincipal->path]) }}"
            class="card-img-top"
            alt="{{ $producto->nombre }}"
            style="height: 140px; object-fit: cover;">
    @else
        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 140px;">
            <span class="text-muted small">Sin imagen</span>
        </div>
    @endif

    <div class="card-body p-3">
        <h6 class="card-title mb-2">
            <a href="{{ route('producto.show', $producto->id) }}" class="text-decoration-none text-dark">
                {{ $producto->nombre }}
            </a>
        </h6>
        @if($producto->sku)
            <div class="small text-muted mb-1">SKU: {{ $producto->sku }}</div>
        @endif

        @if(show_prices())
            <div class="text-success fw-bold mb-2">
                {{ currency_symbol() }}{{ number_format($producto->getPrecioPorCantidad(1) ?? 0, 2) }}
            </div>
        @endif

        <div class="d-grid gap-2">
            <a href="{{ route('producto.show', $producto->id) }}" class="btn btn-sm btn-outline-primary">
                Ver Detalles
            </a>
            <form method="POST" action="/carrito/agregar">
                @csrf
                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                <button class="btn btn-sm btn-primary w-100">
                    Agregar
                </button>
            </form>
        </div>
    </div>
</div>
