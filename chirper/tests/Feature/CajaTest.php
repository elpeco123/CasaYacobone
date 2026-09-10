<?php

use App\Models\Caja;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Retiro;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('vendedor abre caja, vende dentro del período y al cerrar se reinicia', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);

    // Sin caja abierta: la página lo muestra y el panel de ventas está vacío
    $this->actingAs($vendedor)->get(route('caja.index'))
        ->assertStatus(200)
        ->assertViewIs('caja.index')
        ->assertViewHas('cajaAbierta', null);

    $this->actingAs($vendedor)->get(route('ventas.index'))
        ->assertStatus(200);

    // Apertura con $15.000
    $this->actingAs($vendedor)->post(route('caja.store'), [
        'monto_inicial' => 15000,
        'observaciones' => 'Cambio inicial',
    ])->assertSessionHas('success');

    $caja = Caja::where('user_id', $vendedor->id)->first();
    expect($caja)->not->toBeNull()
        ->and($caja->estado)->toBe(Caja::ESTADO_ABIERTA)
        ->and($caja->fecha_apertura)->not->toBeNull();

    // Nadie puede abrir otra sin cerrar la actual (caja única)
    $this->actingAs($vendedor)->post(route('caja.store'), ['monto_inicial' => 5000])
        ->assertSessionHas('error');
    expect(Caja::count())->toBe(1);

    // Ventas dentro del período quedan atadas a la caja
    $venta = Venta::create([
        'user_id' => $vendedor->id,
        'caja_id' => $caja->id,
        'tipo_pago' => 'efectivo',
        'subtotal' => 10000,
        'descuento_porcentaje' => 0,
        'monto_descuento' => 0,
        'total' => 10000,
    ]);

    $listado = $this->actingAs($vendedor)->get(route('ventas.index'))
        ->assertStatus(200);
    expect($listado->viewData('ventas')->pluck('id')->all())->toContain($venta->id);
    expect($listado->viewData('cajaAbierta')->id)->toBe($caja->id);

    // Dashboard del vendedor refleja la caja abierta
    $this->actingAs($vendedor)->get(route('dashboard'))
        ->assertStatus(200)
        ->assertViewHas('montoInicialCaja', 15000.0)
        ->assertViewHas('totalEfectivoEnCaja', 25000.0);

    // Cierre: guarda hora y totales por medio de pago
    $this->actingAs($vendedor)->post(route('caja.cerrar', $caja))
        ->assertSessionHas('success');

    $caja->refresh();
    expect($caja->estado)->toBe(Caja::ESTADO_CERRADA)
        ->and($caja->fecha_cierre)->not->toBeNull()
        ->and((float) $caja->total_efectivo)->toBe(10000.0)
        ->and($caja->cantidad_ventas)->toBe(1);

    // Panel reiniciado: ya no ve las ventas de la caja cerrada
    $reiniciado = $this->actingAs($vendedor)->get(route('ventas.index'))
        ->assertStatus(200);
    expect($reiniciado->viewData('ventas')->pluck('id')->all())->not->toContain($venta->id);
    expect($reiniciado->viewData('cajaAbierta'))->toBeNull();

    // Puede abrir una caja nueva
    $this->actingAs($vendedor)->post(route('caja.store'), ['monto_inicial' => 8000])
        ->assertSessionHas('success');
    expect(Caja::count())->toBe(2);
});

test('la caja es única y compartida entre admin y vendedor', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);
    $admin = User::factory()->create(['role' => 'admin']);

    // El vendedor abre la caja
    $this->actingAs($vendedor)->post(route('caja.store'), ['monto_inicial' => 10000])
        ->assertSessionHas('success');
    $caja = Caja::abierta();
    expect($caja)->not->toBeNull();

    // El admin no puede abrir otra mientras esté abierta, pero ve la misma
    $this->actingAs($admin)->post(route('caja.store'), ['monto_inicial' => 5000])
        ->assertSessionHas('error');
    expect(Caja::count())->toBe(1);

    $vistaAdmin = $this->actingAs($admin)->get(route('caja.index'))->assertStatus(200);
    expect($vistaAdmin->viewData('cajaAbierta')->id)->toBe($caja->id);

    // El admin puede cerrarla aunque la haya abierto el vendedor
    $this->actingAs($admin)->post(route('caja.cerrar', $caja))
        ->assertSessionHas('success');
    expect(Caja::abierta())->toBeNull();

    // Recién ahí el vendedor puede abrir el siguiente turno
    $this->actingAs($vendedor)->post(route('caja.store'), ['monto_inicial' => 7000])
        ->assertSessionHas('success');
    expect(Caja::count())->toBe(2);
});

test('vendedor sin caja abierta no puede registrar ventas', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);
    $categoria = Categoria::create(['nombre' => 'Test']);
    $producto = Producto::factory()->create(['stock' => 10, 'categoria_id' => $categoria->id]);

    $this->actingAs($vendedor)->post(route('ventas.store'), [
        'tipo_pago' => 'efectivo',
        'items' => [
            ['producto_id' => $producto->id, 'cantidad' => 1],
        ],
    ])->assertRedirect(route('caja.index'))
        ->assertSessionHas('error');

    expect(Venta::count())->toBe(0);
});

test('admin ve el historial de cajas en reportes', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('reportes.cajas'))
        ->assertStatus(200)
        ->assertViewIs('reportes.cajas');
});

test('vendedor registra un gasto que descuenta del efectivo y se guarda al cerrar', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);

    $this->actingAs($vendedor)->post(route('caja.store'), ['monto_inicial' => 20000])
        ->assertSessionHas('success');
    $caja = Caja::where('user_id', $vendedor->id)->first();

    Venta::create([
        'user_id' => $vendedor->id,
        'caja_id' => $caja->id,
        'tipo_pago' => 'efectivo',
        'subtotal' => 10000,
        'descuento_porcentaje' => 0,
        'monto_descuento' => 0,
        'total' => 10000,
    ]);

    // Gasto de $5.000 en yerba
    $this->actingAs($vendedor)->post(route('caja.retiros.store'), [
        'monto' => 5000,
        'concepto' => 'Yerba mate',
    ])->assertSessionHas('success');

    expect((float) Retiro::where('caja_id', $caja->id)->sum('monto'))->toBe(5000.0);

    // Físico: 20000 inicial + 10000 efectivo − 5000 gasto = 25000
    $this->actingAs($vendedor)->get(route('dashboard'))
        ->assertStatus(200)
        ->assertViewHas('totalEfectivoEnCaja', 25000.0);

    // Al cerrar queda el snapshot del gasto
    $this->actingAs($vendedor)->post(route('caja.cerrar', $caja))
        ->assertSessionHas('success');

    $caja->refresh();
    expect((float) $caja->total_retiros)->toBe(5000.0)
        ->and($caja->efectivoFisico())->toBe(25000.0);

    // Eliminar un gasto de caja cerrada está bloqueado
    $retiro = Retiro::where('caja_id', $caja->id)->first();
    $this->actingAs($vendedor)->delete(route('caja.retiros.destroy', $retiro))
        ->assertSessionHas('error');
    expect(Retiro::where('caja_id', $caja->id)->count())->toBe(1);
});
