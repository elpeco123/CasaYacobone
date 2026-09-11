<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('retiros', function (Blueprint $table) {
            // Categoría del gasto (nulleable para no romper gastos históricos).
            $table->foreignId('categoria_gasto_id')->nullable()->after('user_id')->constrained('categoria_gastos')->nullOnDelete();
        });

        // Los gastos históricos sin categoría van a "Varios" si existe.
        $varios = DB::table('categoria_gastos')->where('nombre', 'Varios')->value('id');
        if ($varios) {
            DB::table('retiros')->whereNull('categoria_gasto_id')->update(['categoria_gasto_id' => $varios]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retiros', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_gasto_id');
        });
    }
};
