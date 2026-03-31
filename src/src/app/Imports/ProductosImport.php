<?php

namespace App\Imports;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductosImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithUpserts,
    WithBatchInserts,
    WithChunkReading,
    SkipsOnFailure,
    SkipsOnError
{
    use SkipsFailures;
    use SkipsErrors;

    private array $categoriaCache = [];

    public function model(array $row)
    {
        $nombreCategoria = $this->normalizeString($row['categoria'] ?? null) ?? 'Sin Categoria';
        $categoria = $this->getOrCreateCategoria($nombreCategoria);

        $precio1 = $this->normalizePrice($row['precio1'] ?? $row['precio'] ?? null);
        $precio2 = $this->normalizePrice($row['precio2'] ?? null);
        $precio3 = $this->normalizePrice($row['precio3'] ?? null);
        $precio4 = $this->normalizePrice($row['precio4'] ?? null);
        $precio5 = $this->normalizePrice($row['precio5'] ?? null);

        return new Producto([
            'sku' => $this->normalizeString($row['sku'] ?? null),
            'nombre' => $this->normalizeString($row['nombre'] ?? null),
            'descripcion' => $this->normalizeString($row['descripcion'] ?? null),
            'precio' => $precio1,
            'precio1' => $precio1,
            'precio2' => $precio2,
            'precio3' => $precio3,
            'precio4' => $precio4,
            'precio5' => $precio5,
            'cantidad2' => $this->normalizeCantidad($row['cantidad2'] ?? null),
            'cantidad3' => $this->normalizeCantidad($row['cantidad3'] ?? null),
            'cantidad4' => $this->normalizeCantidad($row['cantidad4'] ?? null),
            'cantidad5' => $this->normalizeCantidad($row['cantidad5'] ?? null),
            'unidad_medida' => $this->normalizeString($row['unidad_medida'] ?? null),
            'categoria_id' => $categoria->id,
            'activo' => $this->normalizeBool($row['activo'] ?? null, true),
        ]);
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:100',
            'nombre' => 'required|string|max:255',
            'precio' => 'nullable|numeric|min:0',
            'precio1' => 'nullable|numeric|min:0',
            'precio2' => 'nullable|numeric|min:0',
            'precio3' => 'nullable|numeric|min:0',
            'precio4' => 'nullable|numeric|min:0',
            'precio5' => 'nullable|numeric|min:0',
            'cantidad2' => 'nullable|integer|min:1',
            'cantidad3' => 'nullable|integer|min:1',
            'cantidad4' => 'nullable|integer|min:1',
            'cantidad5' => 'nullable|integer|min:1',
        ];
    }

    public function uniqueBy()
    {
        return 'sku';
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function prepareForValidation($data, $index)
    {
        $data['sku'] = $this->normalizeString($data['sku'] ?? null);
        $data['nombre'] = $this->normalizeString($data['nombre'] ?? null);
        $data['precio'] = $this->normalizePrice($data['precio'] ?? null);
        $data['precio1'] = $this->normalizePrice($data['precio1'] ?? null);
        $data['precio2'] = $this->normalizePrice($data['precio2'] ?? null);
        $data['precio3'] = $this->normalizePrice($data['precio3'] ?? null);
        $data['precio4'] = $this->normalizePrice($data['precio4'] ?? null);
        $data['precio5'] = $this->normalizePrice($data['precio5'] ?? null);
        $data['cantidad2'] = $this->normalizeCantidad($data['cantidad2'] ?? null);
        $data['cantidad3'] = $this->normalizeCantidad($data['cantidad3'] ?? null);
        $data['cantidad4'] = $this->normalizeCantidad($data['cantidad4'] ?? null);
        $data['cantidad5'] = $this->normalizeCantidad($data['cantidad5'] ?? null);

        return $data;
    }

    private function getOrCreateCategoria(string $nombre): Categoria
    {
        $key = Str::lower($nombre);
        if (isset($this->categoriaCache[$key])) {
            return $this->categoriaCache[$key];
        }

        $slug = Str::slug($nombre);
        $categoria = Categoria::query()
            ->whereRaw('LOWER(slug) = ?', [Str::lower($slug)])
            ->orWhereRaw('LOWER(nombre) = ?', [Str::lower($nombre)])
            ->first();

        if (!$categoria) {
            $categoria = Categoria::create([
                'nombre' => $nombre,
                'slug' => $slug,
            ]);
        }

        $this->categoriaCache[$key] = $categoria;
        return $categoria;
    }

    private function normalizeString($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function normalizePrice($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        $value = str_replace(',', '.', (string) $value);
        return (float) $value;
    }

    private function normalizeCantidad($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (float) str_replace(',', '.', (string) $value);
        if ($value <= 0) {
            return null;
        }

        return (int) $value;
    }

    private function normalizeBool($value, bool $default): bool
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        $v = Str::lower(trim((string) $value));
        if (in_array($v, ['1', 'true', 'si', 'sí', 'yes', 'y'], true)) {
            return true;
        }
        if (in_array($v, ['0', 'false', 'no', 'n'], true)) {
            return false;
        }

        return $default;
    }
}
