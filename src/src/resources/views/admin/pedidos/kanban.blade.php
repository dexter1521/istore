@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Tablero Kanban - Pedidos</h1>
    </div>

    <div class="row" id="kanban">
        @foreach($estados as $clave => $label)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>{{ $label }}</strong>
                    </div>
                    <div class="card-body p-2">
                        <div class="list-group kanban-column" data-estado="{{ $clave }}" style="min-height:200px;">
                            @foreach($pedidosPorEstado[$clave] ?? [] as $pedido)
                                <div class="list-group-item mb-2" data-id="{{ $pedido->id }}">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>#{{ $pedido->id }}</strong>
                                            <div class="small text-muted">Total: {{ $pedido->total }}</div>
                                        </div>
                                        <div class="text-end small">
                                            {{ $pedido->cliente_nombre ?? 'N/D' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const columns = document.querySelectorAll('.kanban-column');
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            columns.forEach(el => {
                new Sortable(el, {
                    group: 'kanban',
                    animation: 150,
                    onAdd: function (evt) {
                        const pedidoEl = evt.item;
                        const pedidoId = pedidoEl.getAttribute('data-id');
                        const nuevoEstado = evt.to.getAttribute('data-estado');

                        fetch(`{{ url('admin/pedidos') }}/${pedidoId}/estado`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ estado: nuevoEstado })
                        }).then(res => {
                            if (!res.ok) throw new Error('Error al actualizar estado');
                            return res.json();
                        }).then(data => {
                            // opcional: mostrar notificación
                        }).catch(err => {
                            alert('No fue posible mover el pedido: ' + err.message);
                            // revertir movimiento: recargar página para simplicidad
                            location.reload();
                        });
                    }
                });
            });
        });
    </script>
@endsection
