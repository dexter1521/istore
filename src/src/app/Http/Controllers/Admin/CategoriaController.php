<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('productos')->latest()->paginate(10);
        return view('admin.categorias.index', compact('categorias'));
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
