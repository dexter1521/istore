<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoEstado;
use App\Models\PedidoHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /** Mostrar vista Kanban de pedidos agrupados por estado */
    public function kanban()
    {
        $request = request();
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');
        $dias = (int) $request->query('dias', 30);
        if ($dias < 1 || $dias > 365) {
            $dias = 30;
        }

        // Obtener estados dinámicos desde la BD
        $estados = PedidoEstado::where('activo', true)->orderBy('orden')->get();

        // Optimización: Cargar todos los pedidos relevantes en una sola consulta (Eager Loading)
        // Filtramos por fecha para evitar cargar todo el historial histórico en el tablero
        $query = Pedido::with(['items', 'estado'])
            ->whereIn('estado_id', $estados->pluck('id'))
            ->orderByDesc('created_at');

        if ($desde || $hasta) {
            if ($desde) {
                $query->whereDate('created_at', '>=', $desde);
            }
            if ($hasta) {
                $query->whereDate('created_at', '<=', $hasta);
            }
        } else {
            $query->where('created_at', '>=', now()->subDays($dias)); // Solo últimos N días en Kanban
        }

        $pedidos = $query->get();

        // Agrupar en memoria usando Colecciones de Laravel
        $pedidosPorEstado = $estados->mapWithKeys(function ($estado) use ($pedidos) {
            return [$estado->slug => $pedidos->where('estado_id', $estado->id)];
        });

        $resumenPorEstado = $estados->mapWithKeys(function ($estado) use ($pedidosPorEstado) {
            $items = $pedidosPorEstado[$estado->slug];
            return [
                $estado->slug => [
                    'count' => $items->count(),
                    'total' => $items->sum('total'),
                ],
            ];
        });

        return view('admin.pedidos.kanban', compact('estados', 'pedidosPorEstado', 'resumenPorEstado', 'desde', 'hasta', 'dias'));
    }

    /** Obtener detalles de un pedido para el modal (AJAX) */
    public function show(Pedido $pedido)
    {
        $pedido->load(['items', 'estado']);

        return response()->json([
            'id' => $pedido->id,
            'cliente_nombre' => $pedido->cliente_nombre,
            'cliente_telefono' => $pedido->cliente_telefono,
            'total' => $pedido->total,
            'estado' => $pedido->estado,
            'items' => $pedido->items->map(function ($item) {
                return [
                    'cantidad' => $item->cantidad,
                    'nombre_snapshot' => $item->nombre_snapshot,
                    'precio_snapshot' => $item->precio_snapshot,
                    'subtotal' => $item->subtotal,
                ];
            })->values(),
        ]);
    }

    /** Actualizar el estado de un pedido (llamado desde el Kanban) */
    public function updateEstado(Request $request, Pedido $pedido)
    {
        // Validar permiso
        // @todo: Descomentar cuando se instale spatie/laravel-permission (Sprint 5)
        if (!auth()->check()) {
             return response()->json(['message' => 'No autorizado'], 403);
        }

        $data = $request->validate([
            'estado_id' => 'required|exists:pedido_estados,id',
        ]);

        DB::transaction(function () use ($pedido, $data) {
            // Actualizar estado
            $pedido->estado_id = $data['estado_id'];
            $pedido->save();

            // Registrar historial
            PedidoHistorial::create([
                'pedido_id' => $pedido->id,
                'estado_id' => $data['estado_id'],
                'usuario_id' => auth()->id(), // Puede ser null si no hay auth estricto aún
                'nota' => 'Cambio de estado desde Kanban'
            ]);
        });

        return response()->json(['success' => true]);
    }
}
