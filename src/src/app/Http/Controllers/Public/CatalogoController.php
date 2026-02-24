<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $categoriaId = $request->query('categoria');

        $productos = Producto::where('activo', 1)
            ->with('categoria')
            ->when($categoriaId, function ($query, $categoriaId) {
                return $query->where('categoria_id', $categoriaId);
            })
            ->when($q, function ($query, $q) {
                return $query->where(function ($sub) use ($q) {
                    $sub->where('nombre', 'like', "%{$q}%")
                        ->orWhere('descripcion', 'like', "%{$q}%");
                });
            })
            ->paginate(12)
            ->appends($request->except('page'));

        return view('public.catalogo.index', compact('productos'));
    }

    /**
     * Mostrar productos filtrados por categoría (ruta pública).
     */
    public function categoria(Request $request, $id)
    {
        // Reutilizar la lógica del index: pasar 'categoria' y opcional 'q'
        $request->merge(['categoria' => $id]);
        return $this->index($request);
    }

    public function show($id)
    {
        $producto = Producto::where('activo', 1)
            ->with(['categoria', 'imagenes'])
            ->findOrFail($id);

        return view('public.catalogo.show', compact('producto'));
    }
}
