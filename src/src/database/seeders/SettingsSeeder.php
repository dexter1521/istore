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
                'clave' => 'negocio_nombre',
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
                'clave' => 'moneda_simbolo',
                'valor' => '$',
                'descripcion' => 'Símbolo de moneda',
            ],
            [
                'clave' => 'mostrar_precio',
                'valor' => '1',
                'descripcion' => 'Default global mostrar precio',
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
