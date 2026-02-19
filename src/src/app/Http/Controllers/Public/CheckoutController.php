<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cliente_nombre'   => 'required|string|max:150',
            'cliente_telefono' => 'required|string|max:30',
        ]);

        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect('/carrito')->with('error', 'Tu carrito está vacío');
        }

        $total = collect($carrito)->sum(function ($item) {
            return $item['precio'] * $item['cantidad'];
        });

        // Crear pedido
        $pedido = Pedido::create([
            'cliente_nombre'   => $request->cliente_nombre,
            'cliente_telefono' => $request->cliente_telefono,
            'total'            => $total,
            'estado'           => 'nuevo',
        ]);

        // Crear items snapshot
        foreach ($carrito as $item) {
            PedidoItem::create([
                'pedido_id'       => $pedido->id,
                'producto_id'     => $item['id'],
                'nombre_snapshot' => $item['nombre'],
                'precio_snapshot' => $item['precio'],
                'cantidad'        => $item['cantidad'],
                'subtotal'        => $item['precio'] * $item['cantidad'],
            ]);
        }

        // Limpiar carrito
        session()->forget('carrito');

        // Redirigir a WhatsApp
        return redirect("/checkout/whatsapp/{$pedido->id}");
    }
}
