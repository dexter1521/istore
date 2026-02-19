<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'cliente_nombre',
        'cliente_telefono',
        'total',
        'estado',
        'enviado_whatsapp',
        'notas',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }
}
