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
        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('subtotal', 12, 2)->default(0)->after('tipo_pago');
            $table->decimal('descuento_porcentaje', 5, 2)->default(0)->after('subtotal');
            $table->decimal('monto_descuento', 12, 2)->default(0)->after('descuento_porcentaje');
        });

        // Populate existing records subtotal with total if 0
        \DB::table('ventas')->where('subtotal', 0)->update([
            'subtotal' => \DB::raw('total')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'descuento_porcentaje', 'monto_descuento']);
        });
    }
};
