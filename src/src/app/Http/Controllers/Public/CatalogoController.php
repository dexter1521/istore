<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Producto;

class CatalogoController extends Controller
{
    public function index()
    {
        $productos = Producto::where('activo', 1)
            ->with('categoria')
            ->paginate(12);

        return view('public.catalogo.index', compact('productos'));
    }

    public function show($id)
    {
        $producto = Producto::where('activo', 1)
            ->with(['categoria', 'imagenes'])
            ->findOrFail($id);

        return view('public.catalogo.show', compact('producto'));
    }
}
