<?php

namespace App\Imports;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

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

        return new Producto([
            'sku' => $this->normalizeString($row['sku'] ?? null),
            'nombre' => $this->normalizeString($row['nombre'] ?? null),
            'descripcion' => $this->normalizeString($row['descripcion'] ?? null),
            'precio' => $this->normalizePrice($row['precio'] ?? null),
            'categoria_id' => $categoria->id,
            'activo' => $this->normalizeBool($row['activo'] ?? null, true),
            'mostrar_precio' => $this->normalizeBool($row['mostrar_precio'] ?? null, true),
        ]);
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:100',
            'nombre' => 'required|string|max:255',
            'precio' => 'nullable|numeric|min:0',
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

    private function getOrCreateCategoria(string $nombre): Categoria
    {
        $key = Str::lower($nombre);
        if (isset($this->categoriaCache[$key])) {
            return $this->categoriaCache[$key];
        }

        $categoria = Categoria::firstOrCreate(
            ['nombre' => $nombre],
            ['slug' => Str::slug($nombre)]
        );

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
