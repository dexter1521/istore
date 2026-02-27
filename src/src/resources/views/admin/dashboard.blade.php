@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Pedidos Pendientes</div>
            <div class="card-body">
                <h5 class="card-title">{{ $pedidosPendientes }}</h5>
                <p class="card-text">Pendientes de procesar.</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">Pedidos Hoy</div>
            <div class="card-body">
                <h5 class="card-title">{{ $pedidosHoy }}</h5>
                <p class="card-text">Nuevos pedidos del dia.</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Productos Activos</div>
            <div class="card-body">
                <h5 class="card-title">{{ $productosActivos }}</h5>
                <p class="card-text">Total de productos en catalogo.</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-secondary mb-3">
            <div class="card-header">Categorias Activas</div>
            <div class="card-body">
                <h5 class="card-title">{{ $categoriasActivas }}</h5>
                <p class="card-text">Total de categorias visibles.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Ventas</div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-muted">Hoy</div>
                        <div class="h4 mb-0">{{ currency_symbol() }}{{ number_format($ventasHoy, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-muted">Mes</div>
                        <div class="h4 mb-0">{{ currency_symbol() }}{{ number_format($ventasMes, 2) }}</div>
                    </div>
                    <div>
                        <div class="text-muted">Total pedidos</div>
                        <div class="h4 mb-0">{{ $pedidosTotal }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Resumen por estado</div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @forelse($resumenEstados as $estado)
                        <span class="badge bg-{{ $estado->color }}">
                            {{ $estado->nombre }}: {{ $estado->total }}
                        </span>
                    @empty
                        <span class="text-muted">Sin datos.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Ultimos pedidos</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosPedidos as $pedido)
                                <tr>
                                    <td>#{{ $pedido->id }}</td>
                                    <td>{{ $pedido->cliente_nombre }}</td>
                                    <td>
                                        @php
                                            $estadoNombre = $pedido->estado ? $pedido->estado->nombre : $pedido->estado;
                                            $estadoColor = $pedido->estado ? $pedido->estado->color : 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $estadoColor }}">{{ $estadoNombre }}</span>
                                    </td>
                                    <td>{{ currency_symbol() }}{{ number_format($pedido->total, 2) }}</td>
                                    <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Sin pedidos recientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">Top productos mas pedidos</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProductos as $row)
                                <tr>
                                    <td>{{ $row->nombre_snapshot }}</td>
                                    <td>{{ $row->total_cantidad }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Sin datos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
