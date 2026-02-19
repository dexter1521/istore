<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'slug',
        'activo',
    ];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    protected static function booted()
    {
        static::creating(function ($categoria) {
            if (empty($categoria->slug)) {
                $categoria->slug = Str::slug($categoria->nombre);
            }
        });
    }
}
