@extends('layouts.public')

@section('content')

<h2 class="mb-3">Catálogo de Productos</h2>

@php
$filtroNombre = null;
if(request()->filled('q')) {
$filtroNombre = "'".e(request('q'))."'";
}
$filtroCategoria = null;
if(request()->routeIs('categorias.show')) {
// Intentar resolver nombre de categoría desde la colección compartida
$cat = collect($categoriasMenu ?? [])->firstWhere('id', request()->route('id'));
$filtroCategoria = $cat ? $cat->nombre : null;
} elseif(request()->filled('categoria')) {
$cat = collect($categoriasMenu ?? [])->firstWhere('id', request('categoria'));
$filtroCategoria = $cat ? $cat->nombre : null;
}
@endphp

@if($filtroNombre || $filtroCategoria)
<p class="text-muted mb-4">Resultados @if($filtroNombre) para {{ $filtroNombre }}@endif @if($filtroCategoria) @if($filtroNombre) en @else para @endif la categoría "{{ $filtroCategoria }}"@endif</p>
@endif

<div class="row">
    @forelse($productos as $producto)
    <div class="col-md-3 mb-4">
        <div class="card h-100 shadow-sm">
            @php
            $imagenPrincipal = $producto->imagenes->sortBy('orden')->first();
            @endphp

            @if($imagenPrincipal)
                        <img src="{{ route('media.show', ['path' => $imagenPrincipal->path]) }}"
                            class="card-img-top"
                            alt="{{ $producto->nombre }}"
                            style="height: 180px; object-fit: cover;">
            @else
            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                <span class="text-muted">Sin imagen</span>
            </div>
            @endif

            <div class="card-body">
                <h6 class="card-title">
                    <a href="{{ route('producto.show', $producto->id) }}" class="text-decoration-none text-dark">
                        {{ $producto->nombre }}
                    </a>
                </h6>

                @if(show_prices())
                <p class="text-success fw-bold">
                    {{ currency_symbol() }}{{ number_format($producto->precio ?? 0, 2) }}
                </p>
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
    </div>
    @empty
    <div class="col-12">
        <p class="text-center text-muted">No hay productos disponibles en este momento.</p>
    </div>
    @endforelse
</div>

{{ $productos->links() }}

@endsection
