@extends('layouts.admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Tablero de pedidos</h1>
        <div class="text-muted small">Arrastra las tarjetas para cambiar el estado</div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
    </div>
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-12 col-md-3">
                <label class="form-label mb-1">Desde</label>
                <input type="date" class="form-control" name="desde" value="{{ $desde ?? '' }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label mb-1">Hasta</label>
                <input type="date" class="form-control" name="hasta" value="{{ $hasta ?? '' }}">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label mb-1">?ltimos días</label>
                <input type="number" min="1" max="365" class="form-control" name="dias" value="{{ $dias ?? '' }}">
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('admin.pedidos.kanban', ['limpiar' => 1]) }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
            <div class="col-12">
                <small class="text-muted">Si defines fechas, se ignora "?ltimos días".</small>
            </div>
        </form>
    </div>
</div>

<div class="row flex-nowrap overflow-auto pb-4" style="min-height: 80vh;">
    @foreach($estados as $estado)
    <div class="col-10 col-md-4 col-xl-3 px-2">
        <div class="card h-100 shadow-sm bg-light">
            <div class="card-header bg-white font-weight-bold text-uppercase text-{{ $estado->color }} d-flex justify-content-between align-items-center border-left-{{ $estado->color }}">
                <div>
                    {{ $estado->nombre }}
                    <div class="small text-muted">{{ currency_symbol() }}{{ number_format($resumenPorEstado[$estado->slug]['total'] ?? 0, 2) }}</div>
                </div>
                <span class="badge badge-pill badge-light border">{{ $resumenPorEstado[$estado->slug]['count'] ?? 0 }}</span>
            </div>

            <div class="card-body p-2 kanban-column overflow-auto"
                data-estado-id="{{ $estado->id }}"
                data-estado-slug="{{ $estado->slug }}"
                style="max-height: 75vh;">

                @foreach($pedidosPorEstado[$estado->slug] as $pedido)
                <div class="card mb-2 shadow-sm cursor-move pedido-card border-left-{{ $estado->color }}" data-id="{{ $pedido->id }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">#{{ $pedido->id }}</span>
                            <small class="text-muted">{{ $pedido->created_at->format('d/m H:i') }}</small>
                        </div>
                        <h6 class="font-weight-bold mb-1 text-dark">{{ $pedido->cliente_nombre }}</h6>
                        <p class="mb-2 small text-muted">{{ $pedido->items->count() }} items &bull; {{ currency_symbol() }}{{ number_format($pedido->total, 2) }}</p>
                        <button class="btn btn-sm btn-outline-secondary btn-block" onclick="verPedido({{ $pedido->id }})">Ver detalle</button>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Modal Detalle Pedido -->
<div class="modal fade" id="pedidoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pedido #<span id="modalPedidoId"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Cliente:</strong> <span id="modalCliente"></span></p>
                <p><strong>Teléfono:</strong> <span id="modalTelefono"></span></p>
                <p><strong>Total:</strong> {{ currency_symbol() }}<span id="modalTotal"></span></p>
                <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
                <p><strong>Surtido por:</strong> <span id="modalSurtido"></span></p>
                <p><strong>Revisado por:</strong> <span id="modalRevisado"></span></p>
                <p><strong>Folio MBP:</strong> <span id="modalFolioMbp"></span></p>
                <hr>
                <h6>Items:</h6>
                <ul id="modalItems" class="list-group list-group-flush"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const columns = document.querySelectorAll('.kanban-column');

        columns.forEach(col => {
            new Sortable(col, {
                group: 'pedidos',
                animation: 150,
                ghostClass: 'bg-secondary',
                onEnd: async function(evt) {
                    const itemEl = evt.item;
                    const newEstadoId = evt.to.getAttribute('data-estado-id');
                    const newEstadoSlug = evt.to.getAttribute('data-estado-slug');
                    const pedidoId = itemEl.getAttribute('data-id');

                    if (evt.from === evt.to) return;

                    const revertMove = () => {
                        const refNode = evt.from.children[evt.oldIndex] || null;
                        evt.from.insertBefore(itemEl, refNode);
                    };

                    const payload = { estado_id: newEstadoId };

                    if (newEstadoSlug === 'proceso') {
                        const result = await Swal.fire({
                            title: 'Datos de proceso',
                            html: `
                                <input id="swal-surtido" class="swal2-input" placeholder="Quien surte?">
                                <input id="swal-revisado" class="swal2-input" placeholder="Quien revisa?">
                            `,
                            focusConfirm: false,
                            showCancelButton: true,
                            confirmButtonText: 'Guardar',
                            cancelButtonText: 'Cancelar',
                            preConfirm: () => {
                                const surtido = document.getElementById('swal-surtido').value.trim();
                                const revisado = document.getElementById('swal-revisado').value.trim();
                                if (!surtido || !revisado) {
                                    Swal.showValidationMessage('Debes indicar quien surte y quien revisa.');
                                    return false;
                                }
                                return { surtido, revisado };
                            }
                        });

                        if (!result.isConfirmed) {
                            revertMove();
                            return;
                        }

                        payload.surtido_por = result.value.surtido;
                        payload.revisado_por = result.value.revisado;
                    }

                    if (newEstadoSlug === 'finalizado') {
                        const result = await Swal.fire({
                            title: 'Folio de ticket MBP',
                            input: 'text',
                            inputPlaceholder: 'Folio',
                            showCancelButton: true,
                            confirmButtonText: 'Guardar',
                            cancelButtonText: 'Cancelar',
                            inputValidator: (value) => {
                                if (!value || !value.trim()) {
                                    return 'Debes indicar el folio del ticket MBP.';
                                }
                                return null;
                            }
                        });

                        if (!result.isConfirmed) {
                            revertMove();
                            return;
                        }

                        payload.folio_mbp = result.value.trim();
                    }

                    fetch(`/admin/pedidos/${pedidoId}/estado`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                Swal.fire('Error', 'Error al actualizar el estado. Recarga la pagina.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Error al actualizar el estado. Recarga la pagina.', 'error');
                        });
                }
            });
        });
    });

    window.verPedido = function(id) {
        document.getElementById('modalItems').innerHTML = '<li class="list-group-item">Cargando...</li>';
        $('#pedidoModal').modal('show');

        fetch(`/admin/pedidos/${id}`)
            .then(response => response.json())
            .then(pedido => {
                document.getElementById('modalPedidoId').innerText = pedido.id;
                document.getElementById('modalCliente').innerText = pedido.cliente_nombre;
                document.getElementById('modalTelefono').innerText = pedido.cliente_telefono || '-';
                document.getElementById('modalTotal').innerText = parseFloat(pedido.total).toFixed(2);
                document.getElementById('modalEstado').innerText = pedido.estado ? pedido.estado.nombre : '';
                document.getElementById('modalSurtido').innerText = pedido.surtido_por || '-';
                document.getElementById('modalRevisado').innerText = pedido.revisado_por || '-';
                document.getElementById('modalFolioMbp').innerText = pedido.folio_mbp || '-';

                const list = document.getElementById('modalItems');
                list.innerHTML = '';

                if (pedido.items && pedido.items.length) {
                    pedido.items.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center px-0';
                        const nombre = item.nombre_snapshot || item.nombre || item.producto_nombre || 'Producto';
                        const precio = item.precio_snapshot ?? item.precio ?? item.subtotal ?? 0;
                        li.innerHTML = `
                            <span>${item.cantidad}x ${nombre}</span>
                            <span>{{ currency_symbol() }}${parseFloat(precio).toFixed(2)}</span>
                        `;
                        list.appendChild(li);
                    });
                }
            })
            .catch(err => console.error(err));
    };
</script>
@endsection
