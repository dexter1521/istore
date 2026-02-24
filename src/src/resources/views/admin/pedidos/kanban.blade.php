@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ __('Tablero de Pedidos') }}</h1>
</div>

<div class="container-fluid p-0">
    <div class="row flex-nowrap overflow-auto pb-4" style="min-height: 75vh;">
        @foreach($estados as $estado)
        <div class="col-12 col-md-6 col-lg-3" style="min-width: 320px;">
            <div class="card h-100 bg-light border-0 shadow-sm">
                <!-- Encabezado de Columna -->
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3 sticky-top" style="border-top: 3px solid var(--bs-{{ $estado->color ?? 'secondary' }});">
                    <h6 class="mb-0 fw-bold text-uppercase text-muted small" style="letter-spacing: 0.5px; color: var(--bs-{{ $estado->color }}) !important;">
                        {{ $estado->nombre }}
                    </h6>
                    <span class="badge bg-{{ $estado->color ?? 'secondary' }} rounded-pill count-badge" data-status-id="{{ $estado->id }}">
                        {{ $pedidosPorEstado[$estado->slug]->count() }}
                    </span>
                </div>

                <!-- Zona de Drop -->
                <div class="card-body p-2 kanban-column overflow-auto" data-status-id="{{ $estado->id }}" style="max-height: 70vh;">
                    @foreach($pedidosPorEstado[$estado->slug] as $pedido)
                    <div class="card mb-2 border-0 shadow-sm kanban-card" style="cursor: grab;" data-id="{{ $pedido->id }}">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-light text-dark border">#{{ str_pad($pedido->id, 5, '0', STR_PAD_LEFT) }}</span>
                                <small class="text-muted" style="font-size: 0.8rem;">{{ $pedido->created_at->diffForHumans() }}</small>
                            </div>

                            <h6 class="card-title mb-1 text-truncate" title="{{ $pedido->cliente_nombre }}">
                                {{ $pedido->cliente_nombre ?? 'Cliente Invitado' }}
                            </h6>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">{{ $pedido->items->count() }} ítems</small>
                                <span class="fw-bold text-success">${{ number_format($pedido->total ?? 0, 2) }}</span>
                            </div>

                            <div class="mt-2 pt-2 border-top text-end">
                                <button class="btn btn-sm btn-outline-primary btn-ver-detalle" data-id="{{ $pedido->id }}">
                                    Ver Detalle
                                </button>
                            </div>
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
<div class="modal fade" id="pedidoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pedido #<span id="modal-pedido-id"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Cliente</h6>
                        <p class="mb-1" id="modal-cliente"></p>
                        <p class="mb-0 text-muted"><i data-feather="phone" class="feather-sm"></i> <span id="modal-telefono"></span></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="fw-bold">Información</h6>
                        <p class="mb-1" id="modal-fecha"></p>
                        <span id="modal-estado" class="badge"></span>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Productos</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center" width="80">Cant.</th>
                                <th class="text-end" width="100">Precio</th>
                                <th class="text-end" width="100">Total</th>
                            </tr>
                        </thead>
                        <tbody id="modal-items"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total General:</td>
                                <td class="text-end fw-bold text-success" id="modal-total"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div id="modal-notas-container" class="alert alert-light mt-3 d-none">
                    <strong>Notas:</strong> <span id="modal-notas"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Modal
        const pedidoModal = new bootstrap.Modal(document.getElementById('pedidoModal'));

        const columns = document.querySelectorAll('.kanban-column'); // Seleccionamos las columnas
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        columns.forEach(column => {
            new Sortable(column, {
                group: 'kanban', // Permite mover items entre listas del mismo grupo
                animation: 150,
                ghostClass: 'bg-secondary',
                opacity: 0.5,
                onEnd: function(evt) {
                    const itemEl = evt.item;
                    const newStatusId = evt.to.getAttribute('data-status-id');
                    const oldStatusId = evt.from.getAttribute('data-status-id');
                    const orderId = itemEl.getAttribute('data-id');

                    if (newStatusId === oldStatusId) return;

                    updateBadgeCount(evt.from);
                    updateBadgeCount(evt.to);

                    updateOrderStatus(orderId, newStatusId);
                }
            });
        });

        function updateBadgeCount(columnEl) {
            const statusId = columnEl.getAttribute('data-status-id');
            const count = columnEl.children.length;
            const badge = document.querySelector(`.count-badge[data-status-id="${statusId}"]`);
            if (badge) badge.textContent = count;
        }

        function updateOrderStatus(orderId, statusId) {
            // Usamos la ruta nombrada correctamente
            const url = "{{ url('admin/pedidos') }}/" + orderId + "/estado";

            fetch(url, {
                    method: 'POST', // Usamos POST para coincidir con rutas típicas de acciones
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // Token directo de Blade
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        estado_id: statusId
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error en la respuesta de red');
                    return response.json();
                })
                .then(data => {
                    console.log('Estado actualizado:', data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al actualizar el estado. Recarga la página.');
                });
        }

        // Evento para abrir el modal
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-ver-detalle')) {
                const btn = e.target;
                const pedidoId = btn.getAttribute('data-id');

                // Limpiar modal previo
                document.getElementById('modal-items').innerHTML = '<tr><td colspan="4" class="text-center">Cargando...</td></tr>';

                // Cargar datos
                fetch("{{ url('admin/pedidos') }}/" + pedidoId)
                    .then(res => res.json())
                    .then(data => {
                        // Header
                        document.getElementById('modal-pedido-id').textContent = String(data.id).padStart(5, '0');
                        document.getElementById('modal-cliente').textContent = data.cliente_nombre;
                        document.getElementById('modal-telefono').textContent = data.cliente_telefono || 'N/A';
                        document.getElementById('modal-fecha').textContent = new Date(data.created_at).toLocaleString();

                        // Estado
                        const estadoBadge = document.getElementById('modal-estado');
                        if(data.estado) {
                            estadoBadge.textContent = data.estado.nombre;
                            estadoBadge.className = `badge bg-${data.estado.color}`;
                        } else {
                            estadoBadge.textContent = 'Sin Estado';
                            estadoBadge.className = 'badge bg-secondary';
                        }

                        // Items
                        const tbody = document.getElementById('modal-items');
                        tbody.innerHTML = '';
                        data.items.forEach(item => {
                            tbody.innerHTML += `
                                <tr>
                                    <td>${item.nombre_snapshot}</td>
                                    <td class="text-center">${item.cantidad}</td>
                                    <td class="text-end">$${parseFloat(item.precio_snapshot).toFixed(2)}</td>
                                    <td class="text-end">$${parseFloat(item.subtotal).toFixed(2)}</td>
                                </tr>
                            `;
                        });

                        // Footer
                        document.getElementById('modal-total').textContent = '$' + parseFloat(data.total).toFixed(2);

                        // Notas
                        const notasContainer = document.getElementById('modal-notas-container');
                        const notasText = document.getElementById('modal-notas');
                        if (data.notas) {
                            notasText.textContent = data.notas;
                            notasContainer.classList.remove('d-none');
                        } else {
                            notasContainer.classList.add('d-none');
                        }

                        pedidoModal.show();

                        // Refrescar iconos feather dentro del modal si es necesario
                        if(window.feather) feather.replace();
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Error al cargar los detalles del pedido');
                    });
            }
        });
    });
</script>
@endsection
