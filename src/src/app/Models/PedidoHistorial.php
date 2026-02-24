<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoHistorial extends Model
{
    protected $table = 'pedido_historial';

    protected $fillable = [
        'pedido_id',
        'estado_id',
        'usuario_id',
        'nota'
    ];
}
