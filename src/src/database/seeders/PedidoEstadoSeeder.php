<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedidoEstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            [
                'nombre' => 'Pendiente',
                'slug' => 'pendiente',
                'color' => 'warning',
                'orden' => 10,
                'activo' => true,
            ],
            [
                'nombre' => 'Confirmado',
                'slug' => 'confirmado',
                'color' => 'info',
                'orden' => 20,
                'activo' => true,
            ],
            [
                'nombre' => 'En Preparación',
                'slug' => 'en-preparacion',
                'color' => 'primary',
                'orden' => 30,
                'activo' => true,
            ],
            [
                'nombre' => 'Enviado',
                'slug' => 'enviado',
                'color' => 'secondary',
                'orden' => 40,
                'activo' => true,
            ],
            [
                'nombre' => 'Entregado',
                'slug' => 'entregado',
                'color' => 'success',
                'orden' => 50,
                'activo' => true,
            ],
            [
                'nombre' => 'Cancelado',
                'slug' => 'cancelado',
                'color' => 'danger',
                'orden' => 99,
                'activo' => true,
            ],
        ];

        foreach ($estados as $estado) {
            DB::table('pedido_estados')->updateOrInsert(
                ['slug' => $estado['slug']],
                $estado
            );
        }
    }
}
