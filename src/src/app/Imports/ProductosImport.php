<?php

namespace App\Imports;

use App\Models\Producto;
use App\Models\Categoria;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductosImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Find or create category
        $categoria = Categoria::firstOrCreate(
            ['nombre' => $row['categoria'] ?? 'Sin Categoría'],
            ['slug' => \Str::slug($row['categoria'] ?? 'sin-categoria')]
        );

        return new Producto([
            'sku' => $row['sku'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'] ?? null,
            'precio' => $row['precio'] ?? 0,
            'categoria_id' => $categoria->id,
            'activo' => isset($row['activo']) ? (bool) $row['activo'] : true,
            'mostrar_precio' => isset($row['mostrar_precio']) ? (bool) $row['mostrar_precio'] : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|unique:productos,sku',
            'nombre' => 'required|string|max:255',
            'precio' => 'nullable|numeric|min:0',
        ];
    }
}
