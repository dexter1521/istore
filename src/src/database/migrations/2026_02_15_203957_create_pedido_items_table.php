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
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pedido_id')
                ->constrained('pedidos')
                ->onDelete('cascade');

            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
                ->nullOnDelete();

            // Snapshot del producto al momento del pedido
            $table->string('nombre_snapshot', 255);
            $table->decimal('precio_snapshot', 10, 2);

            $table->unsignedInteger('cantidad');

            $table->decimal('subtotal', 12, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
    }
};
