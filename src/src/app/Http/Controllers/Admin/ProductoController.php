<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\ProductoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductosImport;
use Illuminate\Support\Str;

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
                    ->with('error', 'Importación finalizada con errores en ' . $failures->count() . ' fila(s)' . $firstInfo . '. Revisa el archivo.');
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
            'imagenes' => 'nullable|array|max:3',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $producto = Producto::create($validated);

        // Handle image uploads
        if ($request->hasFile('imagenes')) {
            if (count($request->file('imagenes')) > 3) {
                return redirect()->route('admin.productos.index')
                    ->with('error', 'Solo puedes subir hasta 3 imagenes por producto.');
            }
            foreach ($request->file('imagenes') as $index => $imagen) {
                $path = $this->storeResizedImage($imagen);
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
        $producto->load('imagenes');
        $categorias = Categoria::all();
        return view('admin.productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
            'imagenes' => 'nullable|array|max:3',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $producto->update($validated);

        // Handle new image uploads
        if ($request->hasFile('imagenes')) {
            $currentCount = $producto->imagenes()->count();
            $newCount = count($request->file('imagenes'));
            if (($currentCount + $newCount) > 3) {
                return redirect()->route('admin.productos.edit', $producto)
                    ->with('error', 'Solo puedes tener hasta 3 imagenes por producto.');
            }
            foreach ($request->file('imagenes') as $index => $imagen) {
                $path = $this->storeResizedImage($imagen);
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

    public function destroyImagen(Producto $producto, ProductoImagen $imagen)
    {
        if ($imagen->producto_id !== $producto->id) {
            abort(404);
        }

        Storage::disk('public')->delete($imagen->path);
        $imagen->delete();

        return redirect()
            ->route('admin.productos.edit', $producto)
            ->with('success', 'Imagen eliminada.');
    }

    private function storeResizedImage($file): string
    {
        $realPath = $file->getRealPath();
        [$width, $height, $type] = getimagesize($realPath);

        $size = 800;
        $src = null;
        $extension = 'jpg';
        $output = 'imagejpeg';

        if ($type === IMAGETYPE_PNG && function_exists('imagecreatefrompng')) {
            $src = imagecreatefrompng($realPath);
            $extension = 'png';
            $output = 'imagepng';
        } elseif ($type === IMAGETYPE_WEBP && function_exists('imagecreatefromwebp')) {
            $src = imagecreatefromwebp($realPath);
            $extension = 'webp';
            $output = 'imagewebp';
        } else {
            $src = imagecreatefromjpeg($realPath);
            $extension = 'jpg';
            $output = 'imagejpeg';
        }

        $srcSize = min($width, $height);
        $srcX = (int)(($width - $srcSize) / 2);
        $srcY = (int)(($height - $srcSize) / 2);

        $dst = imagecreatetruecolor($size, $size);
        if ($extension !== 'jpg') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $size, $size, $srcSize, $srcSize);

        $filename = 'productos/' . Str::random(40) . '.' . $extension;
        ob_start();
        if ($output === 'imagejpeg') {
            imagejpeg($dst, null, 85);
        } elseif ($output === 'imagepng') {
            imagepng($dst);
        } elseif ($output === 'imagewebp') {
            imagewebp($dst, null, 85);
        }
        $binary = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        Storage::disk('public')->put($filename, $binary);

        return $filename;
    }
}
