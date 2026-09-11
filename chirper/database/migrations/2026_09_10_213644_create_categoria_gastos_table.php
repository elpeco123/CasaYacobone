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
        Schema::create('categoria_gastos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->timestamps();
        });

        // Categorías base.
        DB::table('categoria_gastos')->insert([
            ['nombre' => 'Impuestos', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Mantenimiento', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Varios', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_gastos');
    }
};
