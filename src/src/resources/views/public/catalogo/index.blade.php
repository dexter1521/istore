@extends('layouts.public')

@section('content')

    <h2 class="mb-4">Catálogo de Productos</h2>

    <div class="row">
        @forelse($productos as $producto)
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">

                    <div class="card-body">
                        <h6 class="card-title">
                            <a href="{{ route('producto.show', $producto->id) }}" class="text-decoration-none text-dark">
                                {{ $producto->nombre }}
                            </a>
                        </h6>

                        @if($producto->mostrar_precio)
                            <p class="text-success fw-bold">
                                ${{ number_format($producto->precio ?? 0, 2) }}
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