<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'cliente_nombre',
        'cliente_telefono',
        'total',
        'estado_id',
        'estado',
        'enviado_whatsapp',
        'notas',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(PedidoEstado::class, 'estado_id');
    }
}
