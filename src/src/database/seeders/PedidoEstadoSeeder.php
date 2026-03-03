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
        DB::table('pedido_estados')->update(['activo' => false]);

        $estados = [
            [
                'nombre' => 'Pendiente',
                'slug' => 'pendiente',
                'color' => 'warning',
                'orden' => 10,
                'activo' => true,
            ],
            [
                'nombre' => 'Proceso',
                'slug' => 'proceso',
                'color' => 'primary',
                'orden' => 20,
                'activo' => true,
            ],
            [
                'nombre' => 'Finalizado',
                'slug' => 'finalizado',
                'color' => 'success',
                'orden' => 30,
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
