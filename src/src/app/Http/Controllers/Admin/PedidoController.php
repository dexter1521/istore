<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoEstado;
use App\Models\PedidoHistorial;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /** Mostrar vista Kanban de pedidos agrupados por estado */
    public function kanban()
    {
        // Obtener estados dinámicos desde la BD
        $estados = PedidoEstado::where('activo', true)->orderBy('orden')->get();

        $pedidosPorEstado = [];
        foreach ($estados as $estado) {
            // Usamos estado_id en lugar de la columna string 'estado'
            $pedidosPorEstado[$estado->slug] = Pedido::where('estado_id', $estado->id)->with('items')->get();
        }

        return view('admin.pedidos.kanban', compact('estados', 'pedidosPorEstado'));
    }

    /** Obtener detalles de un pedido para el modal (AJAX) */
    public function show(Pedido $pedido)
    {
        $pedido->load(['items', 'estado']);
        return response()->json($pedido);
    }

    /** Actualizar el estado de un pedido (llamado desde el Kanban) */
    public function updateEstado(Request $request, Pedido $pedido)
    {
        // Validar permiso
        // @todo: Descomentar cuando se instale spatie/laravel-permission (Sprint 5)
        // if (!auth()->user() || !auth()->user()->can('mover pedidos')) {
        //     return response()->json(['message' => 'No autorizado'], 403);
        // }

        $data = $request->validate([
            'estado_id' => 'required|exists:pedido_estados,id',
        ]);

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

        return response()->json(['success' => true]);
    }
}
