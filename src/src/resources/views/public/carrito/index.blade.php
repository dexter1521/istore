@extends('layouts.public')

@section('content')

    <h2 class="mb-4">Tu Carrito</h2>

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
        <div id="carritoAutoToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Carrito actualizado.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    @if(empty($carrito))

        <p>No tienes productos agregados.</p>

        <a href="/" class="btn btn-secondary">Volver al catálogo</a>

    @else

        <form method="POST" action="/carrito/actualizar" id="carritoForm">

            @csrf

            <table class="table table-bordered" id="carritoTable">

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

                            <td>{{ $item['nombre'] }}

                                @if(!empty($item['sku']))

                                    <div class="small text-muted">SKU: {{ $item['sku'] }}</div>

                                @endif

                                @if(!empty($item['unidad']))

                                    <div class="small text-muted">Unidad: {{ $item['unidad'] }}</div>

                                @endif

                            </td>

                            <td>

                                <div class="position-relative">

                                    <input type="number" name="cantidades[{{ $item['id'] }}]" value="{{ $item['cantidad'] }}" min="1"

                                        class="form-control" data-autosubmit="1" data-item-id="{{ $item['id'] }}">

                                    <div class="spinner-border spinner-border-sm text-primary position-absolute top-50 end-0 translate-middle-y me-2 d-none"

                                        role="status" aria-hidden="true"></div>

                                </div>

                            </td>

                            @if(show_prices())

                                <td class="precio-cell" data-item-id="{{ $item['id'] }}">{{ currency_symbol() }}{{ number_format($item['precio'], 2) }}</td>

                                <td class="subtotal-cell" data-item-id="{{ $item['id'] }}">{{ currency_symbol() }}{{ number_format($subtotal, 2) }}</td>

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

                    <h4>Total: <span id="carritoTotal">{{ currency_symbol() }}{{ number_format($total, 2) }}</span></h4>

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

@push('scripts')

<script>

    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('carritoForm');

        if (!form) return;

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        let timer = null;

        const updateTotals = (data) => {

            if (!data || !data.items) return;

            Object.entries(data.items).forEach(([id, item]) => {

                const precioCell = document.querySelector(`.precio-cell[data-item-id="${id}"]`);

                const subtotalCell = document.querySelector(`.subtotal-cell[data-item-id="${id}"]`);

                if (precioCell) {

                    precioCell.textContent = `{{ currency_symbol() }}${parseFloat(item.precio).toFixed(2)}`;

                }

                if (subtotalCell) {

                    subtotalCell.textContent = `{{ currency_symbol() }}${parseFloat(item.subtotal).toFixed(2)}`;

                }

            });

            const totalEl = document.getElementById('carritoTotal');

            if (totalEl && data.total !== undefined) {

                totalEl.textContent = `{{ currency_symbol() }}${parseFloat(data.total).toFixed(2)}`;

            }

        };

        const submitAjax = async () => {

            const formData = new FormData(form);

            const inputs = form.querySelectorAll('input[data-autosubmit]');

            inputs.forEach(input => {

                const spinner = input.parentElement?.querySelector('.spinner-border');

                if (spinner) spinner.classList.remove('d-none');

            });

            try {

                const res = await fetch(form.action, {

                    method: 'POST',

                    headers: {

                        'X-CSRF-TOKEN': token,

                        'Accept': 'application/json'

                    },

                    body: formData

                });

                if (res.ok) {

                    const data = await res.json();

                    updateTotals(data);

                    if (window.bootstrap) {

                        let toastEl = document.getElementById('carritoAutoToast');

                        if (toastEl) {

                            const toast = new bootstrap.Toast(toastEl, { delay: 1800 });

                            toast.show();

                        }

                    }

                }

            } finally {

                inputs.forEach(input => {

                    const spinner = input.parentElement?.querySelector('.spinner-border');

                    if (spinner) spinner.classList.add('d-none');

                });

            }

        };

        form.querySelectorAll('input[data-autosubmit]')?.forEach(input => {

            const handler = () => {

                clearTimeout(timer);

                timer = setTimeout(submitAjax, 300);

            };

            input.addEventListener('change', handler);

            input.addEventListener('blur', handler);

        });

    });

</script>

@endpush

