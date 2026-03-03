<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('surtido_por', 120)->nullable()->after('estado_id');
            $table->string('revisado_por', 120)->nullable()->after('surtido_por');
            $table->string('folio_mbp', 120)->nullable()->after('revisado_por');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['surtido_por', 'revisado_por', 'folio_mbp']);
        });
    }
};
