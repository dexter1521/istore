<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'sku',
        'nombre',
        'descripcion',
        'precio',
        'precio1',
        'precio2',
        'precio3',
        'precio4',
        'precio5',
        'cantidad2',
        'cantidad3',
        'cantidad4',
        'cantidad5',
        'unidad_medida',
        'categoria_id',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'precio1' => 'decimal:2',
        'precio2' => 'decimal:2',
        'precio3' => 'decimal:2',
        'precio4' => 'decimal:2',
        'precio5' => 'decimal:2',
        'cantidad2' => 'integer',
        'cantidad3' => 'integer',
        'cantidad4' => 'integer',
        'cantidad5' => 'integer',
        'activo' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ProductoImagen::class);
    }

    public function getPrecioPorCantidad(int $cantidad): float
    {
        $cantidad = max(1, $cantidad);

        $precioBase = $this->precio1 ?? $this->precio ?? 0;

        $tiers = [
            5 => ['cantidad' => $this->cantidad5, 'precio' => $this->precio5],
            4 => ['cantidad' => $this->cantidad4, 'precio' => $this->precio4],
            3 => ['cantidad' => $this->cantidad3, 'precio' => $this->precio3],
            2 => ['cantidad' => $this->cantidad2, 'precio' => $this->precio2],
        ];

        foreach ($tiers as $tier) {
            if (!empty($tier['cantidad']) && $cantidad >= $tier['cantidad'] && $tier['precio'] !== null) {
                return (float) $tier['precio'];
            }
        }

        return (float) $precioBase;
    }
}
