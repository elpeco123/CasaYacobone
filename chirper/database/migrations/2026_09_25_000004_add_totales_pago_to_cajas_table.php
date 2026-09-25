<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            // total_tarjeta queda para las cajas viejas, cuando débito y crédito iban juntos.
            $table->decimal('total_debito', 12, 2)->default(0)->after('total_tarjeta');
            $table->decimal('total_credito', 12, 2)->default(0)->after('total_debito');
            $table->decimal('total_cuenta_corriente', 12, 2)->default(0)->after('total_factura');
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn(['total_debito', 'total_credito', 'total_cuenta_corriente']);
        });
    }
};
