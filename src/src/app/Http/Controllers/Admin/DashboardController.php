<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoEstado;
use App\Models\PedidoItem;

class DashboardController extends Controller
{
    public function index()
    {
        $estadoPendienteId = PedidoEstado::where('slug', 'pendiente')->value('id');
        $pedidosPendientes = Pedido::when(
            $estadoPendienteId,
            fn($q) => $q->where('estado_id', $estadoPendienteId),
            fn($q) => $q->where('estado', 'nuevo')
        )->count();

        $pedidosTotal = Pedido::count();
        $pedidosHoy = Pedido::whereDate('created_at', today())->count();
        $ventasHoy = (float) Pedido::whereDate('created_at', today())->sum('total');
        $ventasMes = (float) Pedido::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total');

        $ultimosPedidos = Pedido::with('estado')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $resumenEstados = Pedido::leftJoin('pedido_estados', 'pedidos.estado_id', '=', 'pedido_estados.id')
            ->selectRaw("COALESCE(pedido_estados.nombre, pedidos.estado) as nombre, COALESCE(pedido_estados.color, 'secondary') as color, COUNT(*) as total")
            ->groupByRaw("COALESCE(pedido_estados.nombre, pedidos.estado), COALESCE(pedido_estados.color, 'secondary')")
            ->orderByDesc('total')
            ->get();

        $topProductos = PedidoItem::selectRaw('COALESCE(producto_id, 0) as producto_id, nombre_snapshot, SUM(cantidad) as total_cantidad')
            ->groupBy('producto_id', 'nombre_snapshot')
            ->orderByDesc('total_cantidad')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'pedidosPendientes',
            'pedidosTotal',
            'pedidosHoy',
            'ventasHoy',
            'ventasMes',
            'ultimosPedidos',
            'resumenEstados',
            'topProductos'
        ));
    }
}
