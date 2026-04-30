<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function index()
    {
        $carrito = session()->get('carrito', []);

        return view('public.carrito.index', compact('carrito'));
    }

    public function agregar(Request $request)
    {
        $producto = Producto::findOrFail($request->producto_id);

        $carrito = session()->get('carrito', []);

        if (isset($carrito[$producto->id])) {
            $carrito[$producto->id]['cantidad']++;
            $carrito[$producto->id]['precio'] = $producto->getPrecioPorCantidad($carrito[$producto->id]['cantidad']);
        } else {
            $carrito[$producto->id] = [
                'id'       => $producto->id,
                'nombre'   => $producto->nombre,
                'precio'   => $producto->getPrecioPorCantidad(1),
                'cantidad' => 1,
                'sku'      => $producto->sku,
                'unidad'   => $producto->unidad_medida,
            ];
        }

        session()->put('carrito', $carrito);

        return redirect()->back()->with('success', 'Producto agregado al carrito');
    }

    public function eliminar($id)
    {
        $carrito = session()->get('carrito', []);

        unset($carrito[$id]);

        session()->put('carrito', $carrito);

        return redirect()->back()->with('success', 'Producto eliminado');
    }

    public function actualizar(Request $request)
    {
        $carrito = session()->get('carrito', []);

        foreach ($request->cantidades as $id => $cantidad) {
            if (isset($carrito[$id])) {
                $carrito[$id]['cantidad'] = max(1, (int)$cantidad);
                $producto = Producto::find($id);
                if ($producto) {
                    $carrito[$id]['precio'] = $producto->getPrecioPorCantidad($carrito[$id]['cantidad']);
                }
            }
        }

        session()->put('carrito', $carrito);

        if ($request->expectsJson()) {
            $total = 0;
            $items = [];
            foreach ($carrito as $item) {
                $subtotal = $item['precio'] * $item['cantidad'];
                $total += $subtotal;
                $items[$item['id']] = [
                    'precio' => $item['precio'],
                    'subtotal' => $subtotal,
                    'cantidad' => $item['cantidad'],
                ];
            }
            return response()->json([
                'success' => true,
                'total' => $total,
                'items' => $items,
            ]);
        }

        return redirect()->back()->with('success', 'Carrito actualizado');
    }
}
