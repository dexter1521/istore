<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedidoEstadoSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Pendiente', 'slug' => 'pendiente', 'color' => 'warning', 'orden' => 1],
            ['nombre' => 'Confirmado', 'slug' => 'confirmado', 'color' => 'primary', 'orden' => 2],
            ['nombre' => 'Preparando', 'slug' => 'preparando', 'color' => 'info', 'orden' => 3],
            ['nombre' => 'Listo', 'slug' => 'listo', 'color' => 'success', 'orden' => 4],
            ['nombre' => 'Entregado', 'slug' => 'entregado', 'color' => 'dark', 'orden' => 5],
            ['nombre' => 'Cancelado', 'slug' => 'cancelado', 'color' => 'danger', 'orden' => 6],
        ];

        foreach ($estados as $estado) {
            \App\Models\PedidoEstado::updateOrCreate(
                ['slug' => $estado['slug']],
                $estado
            );
        }
    }
}
