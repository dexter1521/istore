<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductosImport;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->latest()->paginate(10);
        return view('admin.productos.index', compact('productos'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new ProductosImport();
            Excel::import($import, $request->file('file'));

            $failures = $import->failures();
            if ($failures->count() > 0) {
                $first = $failures->first();
                $firstInfo = '';
                if ($first) {
                    $firstInfo = ' (primera fila: ' . $first->row() . ')';
                }
                return redirect()
                    ->route('admin.productos.index')
                    ->with('error', 'ImportaciÃ³n finalizada con errores en ' . $failures->count() . ' fila(s)' . $firstInfo . '. Revisa el archivo.');
            }

            return redirect()
                ->route('admin.productos.index')
                ->with('success', 'Productos importados exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.productos.index')->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('admin.productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'required|string|unique:productos,sku',
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
            'mostrar_precio' => 'boolean',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $producto = Producto::create($validated);

        // Handle image uploads
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $index => $imagen) {
                $path = $imagen->store('productos', 'public');
                $producto->imagenes()->create([
                    'path' => $path,
                    'orden' => $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();
        return view('admin.productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'required|string|unique:productos,sku,' . $producto->id,
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
            'mostrar_precio' => 'boolean',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $producto->update($validated);

        // Handle new image uploads
        if ($request->hasFile('imagenes')) {
            $currentCount = $producto->imagenes()->count();
            foreach ($request->file('imagenes') as $index => $imagen) {
                $path = $imagen->store('productos', 'public');
                $producto->imagenes()->create([
                    'path' => $path,
                    'orden' => $currentCount + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado exitosamente.');
    }
}
