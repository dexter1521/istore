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
                        @if(show_prices())
                            <th width="120">Precio</th>
                            <th width="120">Subtotal</th>
                        @endif
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
                            @if(show_prices())
                                <td>{{ currency_symbol() }}{{ number_format($item['precio'], 2) }}</td>
                                <td>{{ currency_symbol() }}{{ number_format($subtotal, 2) }}</td>
                            @endif
                            <td>
                                <form method="POST" action="{{ route('carrito.eliminar', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">X</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if(show_prices())
                <div class="text-end mb-3">
                    <h4>Total: {{ currency_symbol() }}{{ number_format($total, 2) }}</h4>
                </div>
            @endif

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
