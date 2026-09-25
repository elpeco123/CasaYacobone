<?php

use App\Models\Caja;
use App\Models\Categoria;
use App\Models\CategoriaGasto;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Retiro;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Producto de prueba con precio y stock conocidos.
 */
function productoDePrueba(int $precioVenta = 10000, int $stock = 10): Producto
{
    return Producto::create([
        'nombre' => 'Bombacha de campo',
        'marca' => 'El Fogón',
        'categoria_id' => Categoria::create(['nombre' => 'Bombachas'])->id,
        'precio_compra' => 6000,
        'precio_venta' => $precioVenta,
        'stock' => $stock,
        'stock_minimo' => 1,
    ]);
}

test('la venta con crédito suma el recargo del 20% sobre el total con descuento', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);
    $producto = productoDePrueba(10000);

    Caja::create([
        'user_id' => $vendedor->id,
        'fecha' => now()->toDateString(),
        'fecha_apertura' => now(),
        'estado' => Caja::ESTADO_ABIERTA,
        'monto_inicial' => 0,
    ]);

    $this->actingAs($vendedor)->post(route('ventas.store'), [
        'tipo_pago' => 'credito',
        'descuento_porcentaje' => 10,
        'items' => [['producto_id' => $producto->id, 'cantidad' => 2]],
    ])->assertSessionHas('success');

    $venta = Venta::latest('id')->first();

    // 20.000 − 10% = 18.000, más 20% de recargo = 21.600
    expect((float) $venta->monto_descuento)->toBe(2000.0)
        ->and((float) $venta->monto_recargo)->toBe(3600.0)
        ->and((float) $venta->total)->toBe(21600.0);
});

test('el débito no lleva recargo', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);
    $producto = productoDePrueba(10000);

    Caja::create([
        'user_id' => $vendedor->id,
        'fecha' => now()->toDateString(),
        'fecha_apertura' => now(),
        'estado' => Caja::ESTADO_ABIERTA,
        'monto_inicial' => 0,
    ]);

    $this->actingAs($vendedor)->post(route('ventas.store'), [
        'tipo_pago' => 'debito',
        'items' => [['producto_id' => $producto->id, 'cantidad' => 1]],
    ])->assertSessionHas('success');

    $venta = Venta::latest('id')->first();
    expect((float) $venta->monto_recargo)->toBe(0.0)
        ->and((float) $venta->total)->toBe(10000.0);
});

test('la venta a cuenta corriente exige los datos del cliente y le genera la deuda', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);
    $producto = productoDePrueba(10000, 10);

    Caja::create([
        'user_id' => $vendedor->id,
        'fecha' => now()->toDateString(),
        'fecha_apertura' => now(),
        'estado' => Caja::ESTADO_ABIERTA,
        'monto_inicial' => 0,
    ]);

    // Sin datos del cliente no se registra
    $this->actingAs($vendedor)->post(route('ventas.store'), [
        'tipo_pago' => 'cuenta_corriente',
        'items' => [['producto_id' => $producto->id, 'cantidad' => 1]],
    ])->assertSessionHasErrors(['cliente_nombre', 'cliente_apellido', 'cliente_telefono', 'cliente_direccion']);

    expect(Venta::count())->toBe(0);

    $this->actingAs($vendedor)->post(route('ventas.store'), [
        'tipo_pago' => 'cuenta_corriente',
        'cliente_nombre' => 'Juan',
        'cliente_apellido' => 'Pérez',
        'cliente_telefono' => '2954 123456',
        'cliente_direccion' => 'Belgrano 400',
        'items' => [['producto_id' => $producto->id, 'cantidad' => 2]],
    ])->assertSessionHas('success');

    $cliente = Cliente::first();
    expect($cliente->nombreCompleto())->toBe('Pérez, Juan')
        ->and($cliente->saldo())->toBe(20000.0)
        ->and($cliente->deudaDesde())->not->toBeNull();

    // La segunda compra del mismo teléfono se acumula en el mismo cliente
    $this->actingAs($vendedor)->post(route('ventas.store'), [
        'tipo_pago' => 'cuenta_corriente',
        'cliente_nombre' => 'Juan',
        'cliente_apellido' => 'Pérez',
        'cliente_telefono' => '2954 123456',
        'cliente_direccion' => 'Belgrano 400',
        'items' => [['producto_id' => $producto->id, 'cantidad' => 1]],
    ])->assertSessionHas('success');

    expect(Cliente::count())->toBe(1)
        ->and($cliente->fresh()->saldo())->toBe(30000.0);
});

test('el admin cobra un adelanto y después salda toda la deuda', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $cliente = Cliente::create([
        'nombre' => 'Ana',
        'apellido' => 'Gómez',
        'telefono' => '2954 999888',
        'direccion' => 'San Martín 1200',
    ]);

    Venta::create([
        'user_id' => $admin->id,
        'cliente_id' => $cliente->id,
        'tipo_pago' => 'cuenta_corriente',
        'subtotal' => 50000,
        'descuento_porcentaje' => 0,
        'monto_descuento' => 0,
        'total' => 50000,
    ]);

    $this->actingAs($admin)->post(route('cuentas-corrientes.pagar', $cliente), [
        'monto' => 20000,
        'tipo_pago' => 'efectivo',
    ])->assertSessionHas('success');

    expect($cliente->fresh()->saldo())->toBe(30000.0);

    // No se puede cobrar más que la deuda
    $this->actingAs($admin)->post(route('cuentas-corrientes.pagar', $cliente), [
        'monto' => 40000,
        'tipo_pago' => 'efectivo',
    ])->assertSessionHasErrors('monto');

    $this->actingAs($admin)->post(route('cuentas-corrientes.pagar', $cliente), [
        'monto' => 30000,
        'tipo_pago' => 'debito',
    ])->assertSessionHas('success');

    expect($cliente->fresh()->saldo())->toBe(0.0)
        ->and($cliente->fresh()->deudaDesde())->toBeNull();

    // El panel deja de listarlo entre los deudores
    $listado = $this->actingAs($admin)->get(route('cuentas-corrientes.index'))->assertStatus(200);
    expect($listado->viewData('clientes'))->toHaveCount(0);
});

test('el vendedor no entra al panel de cuentas corrientes', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);

    $this->actingAs($vendedor)->get(route('cuentas-corrientes.index'))->assertStatus(403);
});

test('el dashboard del admin descuenta los gastos del día del efectivo en caja', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $caja = Caja::create([
        'user_id' => $admin->id,
        'fecha' => now()->toDateString(),
        'fecha_apertura' => now(),
        'estado' => Caja::ESTADO_ABIERTA,
        'monto_inicial' => 10000,
    ]);

    Venta::create([
        'user_id' => $admin->id,
        'caja_id' => $caja->id,
        'tipo_pago' => 'efectivo',
        'subtotal' => 25000,
        'descuento_porcentaje' => 0,
        'monto_descuento' => 0,
        'total' => 25000,
    ]);

    // Una venta a cuenta corriente no entra al efectivo ni al total cobrado del día
    Venta::create([
        'user_id' => $admin->id,
        'caja_id' => $caja->id,
        'tipo_pago' => 'cuenta_corriente',
        'cliente_id' => Cliente::create([
            'nombre' => 'Luis',
            'apellido' => 'Ruiz',
            'telefono' => '2954 111222',
            'direccion' => 'Rivadavia 55',
        ])->id,
        'subtotal' => 7000,
        'descuento_porcentaje' => 0,
        'monto_descuento' => 0,
        'total' => 7000,
    ]);

    Retiro::create([
        'caja_id' => $caja->id,
        'user_id' => $admin->id,
        'categoria_gasto_id' => CategoriaGasto::create(['nombre' => 'Almacén'])->id,
        'monto' => 4000,
        'concepto' => 'yerba',
    ]);

    $panel = $this->actingAs($admin)->get(route('dashboard'))->assertStatus(200);

    // 10.000 de cambio + 25.000 en efectivo − 4.000 de gastos
    expect($panel->viewData('totalEfectivoEnCaja'))->toBe(31000.0)
        ->and($panel->viewData('totalRetirosHoy'))->toBe(4000.0)
        ->and($panel->viewData('totalCierreGeneral'))->toBe(31000.0)
        ->and($panel->viewData('ventasHoyPorForma')['cuenta_corriente'])->toBe(7000.0);
});
