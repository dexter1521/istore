<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        return view('admin.categorias.index');
    }


    public function data(Request $request)
    {
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value');

        $columns = [
            0 => 'categorias.id',
            1 => 'categorias.nombre',
            2 => 'categorias.slug',
            3 => 'productos_count',
            4 => 'categorias.created_at',
        ];

        $baseQuery = Categoria::query()->withCount('productos');

        $recordsTotal = (clone $baseQuery)->count();

        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $orderIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderCol = $columns[$orderIndex] ?? 'categorias.id';

        $baseQuery->orderBy($orderCol, $orderDir);

        $rows = $baseQuery->skip($start)->take($length)->get();

        $data = $rows->map(function ($categoria) {
            return [
                'id' => $categoria->id,
                'nombre' => $categoria->nombre,
                'slug' => $categoria->slug,
                'productos' => $categoria->productos_count,
                'fecha' => $categoria->created_at->format('d/m/Y'),
            ];
        });

        return response()->json([

            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        return view('admin.categorias.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
            'descripcion' => 'nullable|string',
        ]);

        Categoria::create($validated);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Categoria $categoria)
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($validated);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->count() > 0) {
            return redirect()->route('admin.categorias.index')
                ->with('error', 'No se puede eliminar una categoría con productos asociados.');
        }

        $categoria->delete();
        return redirect()->route('admin.categorias.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}
