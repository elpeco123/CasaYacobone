<?php

use App\Models\Caja;
use App\Models\User;
use App\Models\Venta;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('vendedor and admin can access caja page and set initial cash', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);

    // Vendedor can access caja view
    $response = $this->actingAs($vendedor)->get(route('caja.index'));
    $response->assertStatus(200);
    $response->assertViewIs('caja.index');
    $response->assertViewHas('montoInicial', 0.0);

    // Vendedor opens caja with $15.000
    $postResponse = $this->actingAs($vendedor)->post(route('caja.store'), [
        'monto_inicial' => 15000,
        'observaciones' => 'Cambio inicial del día',
    ]);

    $postResponse->assertSessionHas('success');

    $this->assertDatabaseHas('cajas', [
        'user_id' => $vendedor->id,
        'monto_inicial' => 15000,
        'observaciones' => 'Cambio inicial del día',
    ]);

    // Admin can access and update caja
    $admin = User::factory()->create(['role' => 'admin']);
    $updateResponse = $this->actingAs($admin)->post(route('caja.store'), [
        'monto_inicial' => 20000,
        'observaciones' => 'Actualizado por admin',
    ]);

    $updateResponse->assertSessionHas('success');

    $this->assertDatabaseHas('cajas', [
        'user_id' => $admin->id,
        'monto_inicial' => 20000,
        'observaciones' => 'Actualizado por admin',
    ]);

    // Check dashboard reflects the cash
    $dashboardResponse = $this->actingAs($vendedor)->get(route('dashboard'));
    $dashboardResponse->assertStatus(200);
    $dashboardResponse->assertViewHas('montoInicialCaja', 20000.0);
    $dashboardResponse->assertViewHas('totalEfectivoEnCaja', 20000.0);
});
