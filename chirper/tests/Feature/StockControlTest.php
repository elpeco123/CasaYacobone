<?php

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('updating product purchase price updates individual and total stock value', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $categoria = Categoria::create(['nombre' => 'Test Categoria']);
    $proveedor = Proveedor::create(['nombre' => 'Test Proveedor']);

    $producto = Producto::create([
        'nombre' => 'Producto Test',
        'categoria_id' => $categoria->id,
        'proveedor_id' => $proveedor->id,
        'marca' => 'Test Marca',
        'precio_compra' => 100.00,
        'precio_venta' => 200.00,
        'stock' => 10,
        'stock_minimo' => 2,
    ]);

    // Initial stock value at cost: 100 * 10 = 1000
    expect($producto->valor_stock_compra)->toBe(1000.0);

    // Update initial purchase price (precio_compra) to 150
    $response = $this->actingAs($user)->put(route('productos.update', $producto), [
        'nombre' => 'Producto Test',
        'categoria_id' => $categoria->id,
        'proveedor_id' => $proveedor->id,
        'marca' => 'Test Marca',
        'precio_compra' => 150.00,
        'precio_venta' => 200.00,
        'stock' => 10,
        'stock_minimo' => 2,
    ]);

    $response->assertRedirect(route('productos.index'));

    $producto->refresh();

    // Updated stock value at cost: 150 * 10 = 1500
    expect($producto->precio_compra)->toEqual(150.00);
    expect($producto->valor_stock_compra)->toBe(1500.0);

    // Dashboard total stock value should reflect the updated purchase price (1500)
    $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
    $dashboardResponse->assertStatus(200);
    $dashboardResponse->assertViewHas('valorStock', 1500.0);

    // Reporte diario total stock value should also reflect 1500
    $reporteResponse = $this->actingAs($user)->get(route('reportes.diario'));
    $reporteResponse->assertStatus(200);
    $reporteResponse->assertViewHas('valorStockRestante', 1500.0);
});

test('deleting a product updates total inventory stock value immediately', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $categoria = Categoria::create(['nombre' => 'Test Categoria']);
    $proveedor = Proveedor::create(['nombre' => 'Test Proveedor']);

    $prod1 = Producto::create([
        'nombre' => 'Producto 1',
        'categoria_id' => $categoria->id,
        'proveedor_id' => $proveedor->id,
        'marca' => 'Marca 1',
        'precio_compra' => 100.00,
        'precio_venta' => 200.00,
        'stock' => 10,
        'stock_minimo' => 2,
    ]);

    $prod2 = Producto::create([
        'nombre' => 'Producto 2',
        'categoria_id' => $categoria->id,
        'proveedor_id' => $proveedor->id,
        'marca' => 'Marca 2',
        'precio_compra' => 50.00,
        'precio_venta' => 100.00,
        'stock' => 20,
        'stock_minimo' => 5,
    ]);

    // Initial total stock value: (100*10) + (50*20) = 1000 + 1000 = 2000
    $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
    $dashboardResponse->assertViewHas('valorStock', 2000.0);

    $productosIndexResponse = $this->actingAs($user)->get(route('productos.index'));
    $productosIndexResponse->assertViewHas('valorTotalStockCompra', 2000.0);
    $productosIndexResponse->assertViewHas('totalStockUnidades', 30);

    // Delete product 1
    $deleteResponse = $this->actingAs($user)->delete(route('productos.destroy', $prod1));
    $deleteResponse->assertRedirect(route('productos.index'));

    // Check soft deleted
    $this->assertSoftDeleted('productos', ['id' => $prod1->id]);

    // After deleting product 1, total stock value should be 1000 (only product 2 remains)
    $dashboardResponsePost = $this->actingAs($user)->get(route('dashboard'));
    $dashboardResponsePost->assertViewHas('valorStock', 1000.0);

    $productosIndexResponsePost = $this->actingAs($user)->get(route('productos.index'));
    $productosIndexResponsePost->assertViewHas('valorTotalStockCompra', 1000.0);
    $productosIndexResponsePost->assertViewHas('totalStockUnidades', 20);
});
