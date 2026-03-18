@extends('layouts.admin')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-2">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pedidos pendientes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pedidosPendientes }}</div>
                        <div class="text-muted small">Pendientes de procesar.</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pedidos hoy</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pedidosHoy }}</div>
                        <div class="text-muted small">Nuevos pedidos del día.</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ventas hoy</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ currency_symbol() }}{{ number_format($ventasHoy, 2) }}</div>
                        <div class="text-muted small">Total vendido hoy.</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ventas del mes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ currency_symbol() }}{{ number_format($ventasMes, 2) }}</div>
                        <div class="text-muted small">Ingresos del mes.</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">?ltimos pedidos</h6>
                <a href="{{ route('admin.pedidos.kanban') }}" class="btn btn-sm btn-outline-primary">Ver pedidos</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-sm mb-0">
                        <thead class="thead-light">
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
                                            $estadoNombre = is_object($pedido->estado) ? $pedido->estado->nombre : $pedido->estado;
                                            $estadoColor = is_object($pedido->estado) ? $pedido->estado->color : 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $estadoColor }}">{{ $estadoNombre }}</span>
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

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Resumen por estado</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    @forelse($resumenEstados as $estado)
                        <span class="badge badge-{{ $estado->color }}">{{ $estado->nombre }}: {{ $estado->total }}</span>
                    @empty
                        <span class="text-muted">Sin datos.</span>
                    @endforelse
                </div>
                <hr>
                <div class="small text-muted">Total pedidos: <strong>{{ $pedidosTotal }}</strong></div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top productos más pedidos</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-sm mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-right">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProductos as $row)
                                <tr>
                                    <td>{{ $row->nombre_snapshot }}</td>
                                    <td class="text-right">{{ $row->total_cantidad }}</td>
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
