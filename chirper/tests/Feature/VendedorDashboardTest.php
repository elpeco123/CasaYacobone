<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('vendedor is directed to seller dashboard and restricted from admin pages', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);

    // Vendedor dashboard access
    $dashboardResponse = $this->actingAs($vendedor)->get(route('dashboard'));
    $dashboardResponse->assertStatus(200);
    $dashboardResponse->assertViewIs('dashboard-vendedor');
    $dashboardResponse->assertViewHas('ventasHoy');
    $dashboardResponse->assertViewHas('cantidadVentasHoy');
    $dashboardResponse->assertViewHas('ventasHoyPorForma');
    $dashboardResponse->assertViewHas('ventasHoyLista');

    // Access restricted for productos and reportes
    $productosResponse = $this->actingAs($vendedor)->get(route('productos.index'));
    $productosResponse->assertStatus(403);

    $reportesResponse = $this->actingAs($vendedor)->get(route('reportes.diario'));
    $reportesResponse->assertStatus(403);
});

test('admin is directed to main admin dashboard and can access admin pages', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $dashboardResponse = $this->actingAs($admin)->get(route('dashboard'));
    $dashboardResponse->assertStatus(200);
    $dashboardResponse->assertViewIs('dashboard');

    $productosResponse = $this->actingAs($admin)->get(route('productos.index'));
    $productosResponse->assertStatus(200);

    $reportesResponse = $this->actingAs($admin)->get(route('reportes.diario'));
    $reportesResponse->assertStatus(200);
});
