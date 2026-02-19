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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 100)->unique();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();

            $table->decimal('precio', 10, 2);

            $table->boolean('mostrar_precio')->default(true);

            $table->foreignId('categoria_id')
                  ->constrained('categorias');

            $table->boolean('activo')->default(true);

            $table->softDeletes(); // deleted_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
