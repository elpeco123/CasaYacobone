<?php

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('el producto no se guarda sin código de artículo', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $categoria = Categoria::create(['nombre' => 'Bombachas']);
    $proveedor = Proveedor::create(['nombre' => 'El Fogón']);

    $datos = [
        'nombre' => 'Bombacha de Campo',
        'categoria_id' => $categoria->id,
        'proveedor_id' => $proveedor->id,
        'talle' => 'M',
        'color' => 'Beige',
        'precio_compra' => 8000,
        'precio_venta' => 12000,
        'stock' => 5,
        'stock_minimo' => 2,
    ];

    $this->actingAs($admin)->post(route('productos.store'), $datos)
        ->assertSessionHasErrors('articulo');

    $this->actingAs($admin)->post(route('productos.store'), $datos + ['articulo' => 'BC-100'])
        ->assertSessionHas('success');

    expect(Producto::first()->articulo)->toBe('BC-100');
});

test('la venta agrupa las variantes bajo un solo artículo', function () {
    $vendedor = User::factory()->create(['role' => 'vendedor']);
    $categoria = Categoria::create(['nombre' => 'Bombachas']);

    // Mismo artículo, dos talles, y el grande más caro.
    foreach ([['M', 12500], ['XXL', 14200]] as [$talle, $precio]) {
        Producto::create([
            'articulo' => 'BC-100',
            'nombre' => 'Bombacha de Campo Clásica',
            'categoria_id' => $categoria->id,
            'talle' => $talle,
            'color' => 'Beige',
            'precio_compra' => 8000,
            'precio_venta' => $precio,
            'stock' => 5,
            'stock_minimo' => 1,
        ]);
    }

    // Otro artículo distinto, sin variantes.
    Producto::create([
        'articulo' => 'CU-200',
        'nombre' => 'Facón Artesanal',
        'categoria_id' => $categoria->id,
        'precio_compra' => 25000,
        'precio_venta' => 38000,
        'stock' => 3,
        'stock_minimo' => 1,
    ]);

    $formulario = $this->actingAs($vendedor)->get(route('ventas.create'))->assertStatus(200);
    $porArticulo = $formulario->viewData('porArticulo');

    expect($porArticulo)->toHaveCount(2)
        ->and($porArticulo['BC-100'])->toHaveCount(2)
        ->and($porArticulo['CU-200'])->toHaveCount(1)
        ->and($porArticulo['BC-100']->pluck('precio_venta')->map(fn ($p) => (float) $p)->all())
        ->toBe([12500.0, 14200.0]);
});

test('la variante se describe con talle y color', function () {
    $categoria = Categoria::create(['nombre' => 'Boinas']);

    $conAmbos = new Producto(['talle' => 'M', 'color' => 'Negro']);
    $soloColor = new Producto(['color' => 'Negro']);
    $sinNada = new Producto(['categoria_id' => $categoria->id]);

    expect($conAmbos->variante())->toBe('M · Negro')
        ->and($soloColor->variante())->toBe('Negro')
        ->and($sinNada->variante())->toBe('Único');
});
