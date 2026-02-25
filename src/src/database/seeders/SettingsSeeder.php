<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'clave' => 'nombre_negocio',
                'valor' => 'Mi Negocio',
                'descripcion' => 'Nombre que aparece en el sitio y WhatsApp',
            ],
            [
                'clave' => 'whatsapp_numero',
                'valor' => '521XXXXXXXXXX',
                'descripcion' => 'Número WhatsApp en formato internacional sin +',
            ],
            [
                'clave' => 'wa_template',
                'valor' =>
                "Hola, soy *{cliente}*\nMi teléfono: {telefono}\n\nQuiero cotizar:\n{items}\n\nTotal estimado: \${total}\nGracias.",
                'descripcion' => 'Plantilla del mensaje WhatsApp',
            ],
            [
                'clave' => 'moneda',
                'valor' => 'USD',
                'descripcion' => 'Código de moneda (USD, MXN, EUR)',
            ],
            [
                'clave' => 'mostrar_precios',
                'valor' => '1',
                'descripcion' => 'Mostrar precios en la tienda',
            ],
        ];

        foreach ($settings as $setting) {
            \DB::table('settings')->updateOrInsert(
                ['clave' => $setting['clave']],
                ['valor' => $setting['valor'], 'descripcion' => $setting['descripcion']]
            );
        }
    }
}
