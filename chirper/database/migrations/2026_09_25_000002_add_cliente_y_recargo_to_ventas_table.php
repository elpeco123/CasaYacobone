<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Cliente: solo en ventas a cuenta corriente.
            $table->foreignId('cliente_id')->nullable()->after('user_id')->constrained('clientes')->nullOnDelete();
            // Recargo del crédito, ya incluido en el total.
            $table->decimal('monto_recargo', 12, 2)->default(0)->after('monto_descuento');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropColumn(['cliente_id', 'monto_recargo']);
        });
    }
};
