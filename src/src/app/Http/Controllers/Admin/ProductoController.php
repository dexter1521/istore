<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\ProductoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductosImport;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    public function index()
    {
        return view('admin.productos.index');
    }


    public function data(Request $request)
    {
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value');

        $columns = [
            0 => 'productos.id',
            1 => 'productos.sku',
            2 => 'productos.nombre',
            3 => 'productos.precio1',
            4 => 'productos.unidad_medida',
            5 => 'categoria_nombre',
            6 => 'productos.activo',
        ];

        $baseQuery = Producto::query()
            ->leftJoin('categorias as c', 'productos.categoria_id', '=', 'c.id')
            ->select('productos.*', 'c.nombre as categoria_nombre');

        $recordsTotal = (clone $baseQuery)->count();

        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('productos.sku', 'like', "%{$search}%")
                    ->orWhere('productos.nombre', 'like', "%{$search}%")
                    ->orWhere('c.nombre', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $orderIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderCol = $columns[$orderIndex] ?? 'productos.id';

        if ($orderCol === 'categoria_nombre') {
            $baseQuery->orderBy('c.nombre', $orderDir);
        } else {
            $baseQuery->orderBy($orderCol, $orderDir);
        }

        $rows = $baseQuery->skip($start)->take($length)->get();

        $data = $rows->map(function ($producto) {
            return [
                'id' => $producto->id,
                'sku' => $producto->sku,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio1 ?? $producto->precio,
                'unidad' => $producto->unidad_medida,
                'categoria' => $producto->categoria_nombre ?? '-',
                'activo' => (bool) $producto->activo,
            ];
        });

        return response()->json([

            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
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
        $request->merge([
            'precio1' => $request->input('precio1', $request->input('precio')),
        ]);
        $validated = $this->validateProducto($request, true);
        $validated['precio'] = $validated['precio1'];

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
        $request->merge([
            'precio1' => $request->input('precio1', $request->input('precio')),
        ]);
        $validated = $this->validateProducto($request, false, $producto->id);
        $validated['precio'] = $validated['precio1'];

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

    private function validateProducto(Request $request, bool $isCreate, ?int $productoId = null): array
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'sku' => $isCreate
                ? 'required|string|unique:productos,sku'
                : 'nullable|string|unique:productos,sku,' . $productoId,
            'categoria_id' => 'required|exists:categorias,id',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'nullable|string|max:50',
            'precio1' => 'required|numeric|min:0',
            'precio2' => 'nullable|numeric|min:0',
            'precio3' => 'nullable|numeric|min:0',
            'precio4' => 'nullable|numeric|min:0',
            'precio5' => 'nullable|numeric|min:0',
            'cantidad2' => 'nullable|integer|min:1',
            'cantidad3' => 'nullable|integer|min:1',
            'cantidad4' => 'nullable|integer|min:1',
            'cantidad5' => 'nullable|integer|min:1',
            'activo' => 'boolean',
            'imagenes' => 'nullable|array|max:3',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $cantidades = [];
            for ($i = 2; $i <= 5; $i++) {
                $cantidadKey = 'cantidad' . $i;
                $precioKey = 'precio' . $i;
                $cantidad = $request->input($cantidadKey);
                $precio = $request->input($precioKey);

                if ($cantidad !== null || $precio !== null) {
                    if ($cantidad === null || $cantidad === '') {
                        $validator->errors()->add($cantidadKey, 'La cantidad es requerida cuando hay precio.');
                    }
                    if ($precio === null || $precio === '') {
                        $validator->errors()->add($precioKey, 'El precio es requerido cuando hay cantidad.');
                    }
                }

                if ($cantidad !== null && $cantidad !== '') {
                    $cantidades[] = (int) $cantidad;
                }
            }

            if (count($cantidades) > 1) {
                $sorted = $cantidades;
                sort($sorted);
                if ($sorted !== $cantidades) {
                    $validator->errors()->add('cantidad2', 'Las cantidades deben ir en orden ascendente.');
                }
                if (count($sorted) !== count(array_unique($sorted))) {
                    $validator->errors()->add('cantidad2', 'Las cantidades no deben repetirse.');
                }
            }
        });

        return $validator->validate();
    }
}
