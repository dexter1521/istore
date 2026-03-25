<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio1', 10, 2)->nullable()->after('precio');
            $table->decimal('precio2', 10, 2)->nullable()->after('precio1');
            $table->decimal('precio3', 10, 2)->nullable()->after('precio2');
            $table->decimal('precio4', 10, 2)->nullable()->after('precio3');
            $table->decimal('precio5', 10, 2)->nullable()->after('precio4');
            $table->unsignedInteger('cantidad2')->nullable()->after('precio5');
            $table->unsignedInteger('cantidad3')->nullable()->after('cantidad2');
            $table->unsignedInteger('cantidad4')->nullable()->after('cantidad3');
            $table->unsignedInteger('cantidad5')->nullable()->after('cantidad4');
            $table->string('unidad_medida', 50)->nullable()->after('cantidad5');
        });

        DB::table('productos')->whereNull('precio1')->update([
            'precio1' => DB::raw('precio')
        ]);
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'precio1',
                'precio2',
                'precio3',
                'precio4',
                'precio5',
                'cantidad2',
                'cantidad3',
                'cantidad4',
                'cantidad5',
                'unidad_medida',
            ]);
        });
    }
};
