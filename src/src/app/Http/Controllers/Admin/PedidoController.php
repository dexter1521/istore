<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /** Mostrar vista Kanban de pedidos agrupados por estado */
    public function kanban()
    {
        // Definir estados en el orden deseado (puedes ajustar según tu negocio)
        $estados = [
            'pendiente' => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'listo' => 'Listo para Envío',
            'completado' => 'Completado',
        ];

        $pedidosPorEstado = [];
        foreach (array_keys($estados) as $clave) {
            $pedidosPorEstado[$clave] = Pedido::where('estado', $clave)->with('items')->get();
        }

        return view('admin.pedidos.kanban', compact('estados', 'pedidosPorEstado'));
    }

    /** Actualizar el estado de un pedido (llamado desde el Kanban) */
    public function updateEstado(Request $request, Pedido $pedido)
    {
        // Validar permiso
        if (!auth()->user() || !auth()->user()->can('mover pedidos')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $data = $request->validate([
            'estado' => 'required|string',
        ]);

        $pedido->estado = $data['estado'];
        $pedido->save();

        return response()->json(['success' => true, 'pedido_id' => $pedido->id, 'nuevo_estado' => $pedido->estado]);
    }
}
