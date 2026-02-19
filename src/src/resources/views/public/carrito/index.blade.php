@extends('layouts.public')

@section('content')

    <h2 class="mb-4">🛒 Tu Carrito</h2>

    @if(empty($carrito))
        <p>No tienes productos agregados.</p>
        <a href="/" class="btn btn-secondary">Volver al catálogo</a>
    @else

        <form method="POST" action="/carrito/actualizar">
            @csrf

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th width="120">Cantidad</th>
                        <th width="120">Precio</th>
                        <th width="120">Subtotal</th>
                        <th width="80"></th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp

                    @foreach($carrito as $item)
                        @php
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td>{{ $item['nombre'] }}</td>
                            <td>
                                <input type="number" name="cantidades[{{ $item['id'] }}]" value="{{ $item['cantidad'] }}" min="1"
                                    class="form-control">
                            </td>
                            <td>${{ number_format($item['precio'], 2) }}</td>
                            <td>${{ number_format($subtotal, 2) }}</td>
                            <td>
                                <a href="/carrito/eliminar/{{ $item['id'] }}" class="btn btn-sm btn-danger">
                                    X
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-end mb-3">
                <h4>Total: ${{ number_format($total, 2) }}</h4>
            </div>

            <button class="btn btn-warning">Actualizar carrito</button>

        </form>

        <hr>

        <h3>Checkout rápido</h3>

        <form method="POST" action="/checkout" class="mt-3">
            @csrf

            <div class="mb-2">
                <label>Nombre</label>
                <input type="text" name="cliente_nombre" class="form-control" required>
            </div>

            <div class="mb-2">
                <label>Teléfono</label>
                <input type="text" name="cliente_telefono" class="form-control" required>
            </div>

            <button class="btn btn-success w-100 mt-3">
                Enviar pedido por WhatsApp
            </button>

        </form>

    @endif

@endsection