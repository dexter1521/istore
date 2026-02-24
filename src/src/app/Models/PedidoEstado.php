<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoEstado extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'color',
        'orden',
        'activo'
    ];
}
