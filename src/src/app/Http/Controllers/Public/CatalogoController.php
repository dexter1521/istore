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
            ->with(['categoria', 'imagenes'])
            ->when($categoriaId, function ($query, $categoriaId) {
                return $query->where('categoria_id', $categoriaId);
            })
            ->when($q, function ($query, $q) {
                return $query->where(function ($sub) use ($q) {
                    $sub->where('sku', 'like', "%{$q}%")
                        ->orWhere('nombre', 'like', "%{$q}%")
                        ->orWhere('descripcion', 'like', "%{$q}%");
                });
            })
            ->paginate(12)
            ->appends($request->except('page'));

        return view('public.catalogo.index', compact('productos'));
    }


    public function sugerencias(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $productos = Producto::where('activo', 1)
            ->where(function ($sub) use ($q) {
                $sub->where('sku', 'like', "%{$q}%")
                    ->orWhere('nombre', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%");
            })
            ->orderBy('nombre')
            ->limit(8)
            ->get(['id', 'sku', 'nombre']);

        $result = $productos->map(function ($producto) {
            return [
                'id' => $producto->id,
                'sku' => $producto->sku,
                'nombre' => $producto->nombre,
                'url' => route('producto.show', $producto->id),
            ];
        });

        return response()->json($result);
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
