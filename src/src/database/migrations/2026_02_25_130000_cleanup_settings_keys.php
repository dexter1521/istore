<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        // Migrar nombre de negocio
        $negocio = DB::table('settings')->where('clave', 'negocio_nombre')->first();
        $actual = DB::table('settings')->where('clave', 'nombre_negocio')->first();
        if ($negocio && !$actual) {
            DB::table('settings')->updateOrInsert(
                ['clave' => 'nombre_negocio'],
                ['valor' => $negocio->valor, 'descripcion' => $negocio->descripcion]
            );
        }

        // Migrar mostrar_precio -> mostrar_precios si aplica
        $mp = DB::table('settings')->where('clave', 'mostrar_precio')->first();
        $mps = DB::table('settings')->where('clave', 'mostrar_precios')->first();
        if ($mp && !$mps) {
            DB::table('settings')->updateOrInsert(
                ['clave' => 'mostrar_precios'],
                ['valor' => $mp->valor, 'descripcion' => 'Mostrar precios en la tienda']
            );
        }

        // Migrar whatsapp_mensaje -> wa_template si falta
        $wm = DB::table('settings')->where('clave', 'whatsapp_mensaje')->first();
        $wt = DB::table('settings')->where('clave', 'wa_template')->first();
        if ($wm && !$wt) {
            DB::table('settings')->updateOrInsert(
                ['clave' => 'wa_template'],
                ['valor' => $wm->valor, 'descripcion' => 'Plantilla del mensaje WhatsApp']
            );
        }

        // Eliminar claves obsoletas
        DB::table('settings')->whereIn('clave', [
            'negocio_nombre',
            'moneda_simbolo',
            'mostrar_precio',
            'whatsapp_mensaje',
        ])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertimos limpieza de claves para evitar reintroducir datos obsoletos.
    }
};
