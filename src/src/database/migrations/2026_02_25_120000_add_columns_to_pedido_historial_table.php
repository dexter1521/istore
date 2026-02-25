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
        Schema::table('pedido_historial', function (Blueprint $table) {
            if (!Schema::hasColumn('pedido_historial', 'pedido_id')) {
                $table->foreignId('pedido_id')
                    ->after('id')
                    ->constrained('pedidos')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('pedido_historial', 'estado_id')) {
                $table->foreignId('estado_id')
                    ->after('pedido_id')
                    ->constrained('pedido_estados');
            }

            if (!Schema::hasColumn('pedido_historial', 'usuario_id')) {
                $table->foreignId('usuario_id')
                    ->nullable()
                    ->after('estado_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('pedido_historial', 'nota')) {
                $table->string('nota')->nullable()->after('usuario_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedido_historial', function (Blueprint $table) {
            if (Schema::hasColumn('pedido_historial', 'usuario_id')) {
                $table->dropConstrainedForeignId('usuario_id');
            }
            if (Schema::hasColumn('pedido_historial', 'estado_id')) {
                $table->dropConstrainedForeignId('estado_id');
            }
            if (Schema::hasColumn('pedido_historial', 'pedido_id')) {
                $table->dropConstrainedForeignId('pedido_id');
            }
            if (Schema::hasColumn('pedido_historial', 'nota')) {
                $table->dropColumn('nota');
            }
        });
    }
};
