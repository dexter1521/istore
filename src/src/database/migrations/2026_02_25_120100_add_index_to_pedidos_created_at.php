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
        if (!Schema::hasColumn('pedidos', 'created_at')) {
            return;
        }

        try {
            DB::statement('CREATE INDEX pedidos_created_at_index ON pedidos (created_at)');
        } catch (\Throwable $e) {
            // Index may already exist; ignore to keep migration idempotent.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('DROP INDEX pedidos_created_at_index ON pedidos');
        } catch (\Throwable $e) {
            // Ignore if missing.
        }
    }
};
