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
        } else {
            $carrito[$producto->id] = [
                'id'       => $producto->id,
                'nombre'   => $producto->nombre,
                'precio'   => $producto->precio,
                'cantidad' => 1,
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
            }
        }

        session()->put('carrito', $carrito);

        return redirect()->back()->with('success', 'Carrito actualizado');
    }
}
