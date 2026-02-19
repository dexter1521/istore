<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ===========================
        // Categorías demo
        // ===========================

        $dulces = Categoria::firstOrCreate([
            'nombre' => 'Dulces'
        ], [
            'slug' => 'dulces'
        ]);

        $snacks = Categoria::firstOrCreate([
            'nombre' => 'Snacks'
        ], [
            'slug' => 'snacks'
        ]);

        $refacciones = Categoria::firstOrCreate([
            'nombre' => 'Refacciones'
        ], [
            'slug' => 'refacciones'
        ]);

        // ===========================
        // Productos demo
        // ===========================

        $productos = [
            [
                'sku' => 'DUL-001',
                'nombre' => 'Gomitas Enchiladas',
                'descripcion' => 'Gomitas con chile y limón, bolsa de 100g.',
                'precio' => 35.00,
                'categoria_id' => $dulces->id,
            ],
            [
                'sku' => 'DUL-002',
                'nombre' => 'Pulparindo Extra Picante',
                'descripcion' => 'Dulce de tamarindo clásico.',
                'precio' => 18.00,
                'categoria_id' => $dulces->id,
            ],
            [
                'sku' => 'SNK-001',
                'nombre' => 'Papas Artesanales',
                'descripcion' => 'Papas fritas estilo casero.',
                'precio' => 42.00,
                'categoria_id' => $snacks->id,
            ],
            [
                'sku' => 'SNK-002',
                'nombre' => 'Cacahuates Japoneses',
                'descripcion' => 'Bolsa de cacahuates japoneses 200g.',
                'precio' => 28.00,
                'categoria_id' => $snacks->id,
            ],
            [
                'sku' => 'REF-001',
                'nombre' => 'Balata Delantera Italika FT150',
                'descripcion' => 'Juego de balatas delanteras para FT150.',
                'precio' => 220.00,
                'categoria_id' => $refacciones->id,
            ],
            [
                'sku' => 'REF-002',
                'nombre' => 'Filtro de Aceite Universal',
                'descripcion' => 'Filtro compatible con varias motos comerciales.',
                'precio' => 95.00,
                'categoria_id' => $refacciones->id,
            ],
        ];

        foreach ($productos as $p) {
            Producto::updateOrCreate(
                ['sku' => $p['sku']],
                [
                    'nombre' => $p['nombre'],
                    'descripcion' => $p['descripcion'],
                    'precio' => $p['precio'],
                    'mostrar_precio' => true,
                    'categoria_id' => $p['categoria_id'],
                    'activo' => true,
                ]
            );
        }
    }
}
