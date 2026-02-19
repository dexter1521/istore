<?php

namespace App\Services;

use App\Models\Pedido;

class WhatsAppService
{
    public function generarUrl(Pedido $pedido): string
    {
        $telefono = setting('whatsapp_numero');

        $items = "";

        foreach ($pedido->items as $item) {
            $items .= "{$item->cantidad}x {$item->nombre_snapshot} - $ {$item->subtotal}\n";
        }

        $template = setting('wa_template');

        $mensaje = str_replace(
            ['{cliente}', '{telefono}', '{items}', '{total}'],
            [$pedido->cliente_nombre, $pedido->cliente_telefono, $items, $pedido->total],
            $template
        );

        return "https://wa.me/{$telefono}?text=" . urlencode($mensaje);
    }
}
