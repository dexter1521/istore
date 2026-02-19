<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->string('cliente_nombre', 150);
            $table->string('cliente_telefono', 30);

            $table->decimal('total', 12, 2);

            $table->enum('estado', [
                'nuevo',
                'contactado',
                'en_proceso',
                'entregado',
                'cancelado'
            ])->default('nuevo');

            $table->boolean('enviado_whatsapp')->default(false);

            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
