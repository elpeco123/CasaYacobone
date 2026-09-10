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
        Schema::table('cajas', function (Blueprint $table) {
            // Período de la caja: apertura obligatoria, cierre al finalizar.
            $table->timestamp('fecha_apertura')->nullable()->after('fecha');
            $table->timestamp('fecha_cierre')->nullable()->after('fecha_apertura');
            $table->string('estado', 20)->default('abierta')->after('fecha_cierre');
            // Totales por medio de pago (snapshot al cerrar la caja).
            $table->decimal('total_efectivo', 12, 2)->default(0)->after('estado');
            $table->decimal('total_tarjeta', 12, 2)->default(0)->after('total_efectivo');
            $table->decimal('total_factura', 12, 2)->default(0)->after('total_tarjeta');
            $table->decimal('total_retiros', 12, 2)->default(0)->after('total_factura');
            $table->unsignedInteger('cantidad_ventas')->default(0)->after('total_factura');
        });

        // Las cajas históricas (sin apertura registrada) toman created_at como apertura.
        DB::table('cajas')->whereNull('fecha_apertura')->update([
            'fecha_apertura' => DB::raw('created_at'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_apertura',
                'fecha_cierre',
                'estado',
                'total_efectivo',
                'total_tarjeta',
                'total_factura',
                'total_retiros',
                'cantidad_ventas',
            ]);
        });
    }
};
