<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Código del artículo: los productos que comparten código son el
            // mismo artículo en distinto talle o color.
            $table->string('articulo')->nullable()->after('id');
            $table->string('color')->nullable()->after('talle');
        });

        // Lo ya cargado arranca con su propio nombre como artículo.
        DB::table('productos')->update(['articulo' => DB::raw('nombre')]);

        Schema::table('productos', function (Blueprint $table) {
            $table->string('articulo')->nullable(false)->change();
            $table->index('articulo');
            $table->dropColumn('marca');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('marca')->default('')->after('talle');
            $table->dropIndex(['articulo']);
            $table->dropColumn(['articulo', 'color']);
        });
    }
};
