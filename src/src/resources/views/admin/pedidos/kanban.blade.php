@extends('layouts.admin')

@section('content')
<div class="container-fluid h-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 text-gray-800">Tablero de Pedidos</h1>
        <span class="text-muted small">Arrastra las tarjetas para cambiar el estado</span>
    </div>

    <form class="row g-2 align-items-end mb-3" method="GET">
        <div class="col-12 col-md-3">
            <label class="form-label mb-1">Desde</label>
            <input type="date" class="form-control" name="desde" value="{{ $desde ?? '' }}">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label mb-1">Hasta</label>
            <input type="date" class="form-control" name="hasta" value="{{ $hasta ?? '' }}">
        </div>
        <div class="col-12 col-md-2">
            <label class="form-label mb-1">Últimos días</label>
            <input type="number" min="1" max="365" class="form-control" name="dias" value="{{ $dias ?? 30 }}">
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="{{ route('admin.pedidos.kanban') }}" class="btn btn-outline-secondary">Limpiar</a>
        </div>
        <div class="col-12">
            <small class="text-muted">Si defines fechas, se ignora “Últimos días”.</small>
        </div>
    </form>

    <!-- Contenedor con scroll horizontal para las columnas -->
    <div class="row flex-nowrap overflow-auto pb-4" style="min-height: 80vh;">
        @foreach($estados as $estado)
        <div class="col-10 col-md-4 col-xl-3 px-2">
            <div class="card h-100 shadow-sm bg-light">
                <!-- Encabezado de Columna -->
                <div class="card-header border-top-{{ $estado->color }} bg-white font-weight-bold text-uppercase text-{{ $estado->color }} d-flex justify-content-between align-items-center">
                    <div>
                        {{ $estado->nombre }}
                        <div class="small text-muted">
                            ${{ number_format($resumenPorEstado[$estado->slug]['total'] ?? 0, 2) }}
                        </div>
                    </div>
                    <span class="badge badge-pill badge-light border">{{ $resumenPorEstado[$estado->slug]['count'] ?? 0 }}</span>
                </div>

                <!-- Zona de Drop -->
                <div class="card-body p-2 kanban-column overflow-auto"
                    data-estado-id="{{ $estado->id }}"
                    style="max-height: 75vh;">

                    @foreach($pedidosPorEstado[$estado->slug] as $pedido)
                    <div class="card mb-2 shadow-sm cursor-move pedido-card border-left-{{ $estado->color }}" data-id="{{ $pedido->id }}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-muted">#{{ $pedido->id }}</span>
                                <small class="text-muted">{{ $pedido->created_at->format('d/m H:i') }}</small>
                            </div>
                            <h6 class="font-weight-bold mb-1 text-dark">{{ $pedido->cliente_nombre }}</h6>
                            <p class="mb-2 small text-muted">
                                {{ $pedido->items->count() }} items • ${{ number_format($pedido->total, 2) }}
                            </p>
                            <button class="btn btn-sm btn-outline-secondary btn-block" onclick="verPedido({{ $pedido->id }})">
                                Ver Detalle
                            </button>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Detalle Pedido -->
<div class="modal fade" id="pedidoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pedido #<span id="modalPedidoId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Cliente:</strong> <span id="modalCliente"></span></p>
                <p><strong>Teléfono:</strong> <span id="modalTelefono"></span></p>
                <p><strong>Total:</strong> $<span id="modalTotal"></span></p>
                <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
                <hr>
                <h6>Items:</h6>
                <ul id="modalItems" class="list-group list-group-flush"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para Drag & Drop y AJAX -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const columns = document.querySelectorAll('.kanban-column');

        columns.forEach(col => {
            new Sortable(col, {
                group: 'pedidos', // Permite mover entre columnas del mismo grupo
                animation: 150,
                ghostClass: 'bg-secondary', // Clase visual mientras se arrastra
                onEnd: function(evt) {
                    const itemEl = evt.item;
                    const newEstadoId = evt.to.getAttribute('data-estado-id');
                    const pedidoId = itemEl.getAttribute('data-id');

                    // Si no cambió de columna, no hacemos nada
                    if (evt.from === evt.to) return;

                    // Llamada AJAX para actualizar el estado
                    fetch(`/admin/pedidos/${pedidoId}/estado`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                estado_id: newEstadoId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                alert('Error al actualizar el estado. Recarga la página.');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });
    });

    // Función para ver detalles del pedido en Modal
    window.verPedido = function(id) {
        // Resetear modal
        document.getElementById('modalItems').innerHTML = '<li class="list-group-item">Cargando...</li>';
        const modalEl = document.getElementById('pedidoModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        fetch(`/admin/pedidos/${id}`)
            .then(response => response.json())
            .then(pedido => {
                document.getElementById('modalPedidoId').innerText = pedido.id;
                document.getElementById('modalCliente').innerText = pedido.cliente_nombre;
                document.getElementById('modalTelefono').innerText = pedido.cliente_telefono || '-';
                document.getElementById('modalTotal').innerText = parseFloat(pedido.total).toFixed(2);
                document.getElementById('modalEstado').innerText = pedido.estado ? pedido.estado.nombre : '';

                const list = document.getElementById('modalItems');
                list.innerHTML = '';

                if (pedido.items && pedido.items.length) {
                    pedido.items.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center px-0';
                        // Se intenta usar 'nombre' (snapshot) o fallback genérico
                        const nombre = item.nombre_snapshot || item.nombre || item.producto_nombre || 'Producto';
                        const precio = item.precio_snapshot ?? item.precio ?? item.subtotal ?? 0;
                        li.innerHTML = `
                            <span>${item.cantidad}x ${nombre}</span>
                            <span>$${parseFloat(precio).toFixed(2)}</span>
                        `;
                        list.appendChild(li);
                    });
                }
            })
            .catch(err => console.error(err));
    };
</script>
@endsection
