<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimularDatosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('1. Verificando usuarios...');
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $users = User::all();
        if ($users->isEmpty()) {
            $this->call(UserSeeder::class);
            $users = User::all();
        }

        $this->command->info('2. Verificando categorías...');
        if (Categoria::count() === 0) {
            $this->call(CategoriaSeeder::class);
        }
        $categorias = Categoria::all();

        $this->command->info('3. Creando / verificando Proveedores...');
        $proveedoresData = [
            ['nombre' => 'Talabartería El Gaucho', 'telefono' => '+54 11 4321-8899', 'email' => 'contacto@elgaucho.com.ar'],
            ['nombre' => 'Indumentaria Pampa S.A.', 'telefono' => '+54 11 4888-2233', 'email' => 'ventas@pampasa.com.ar'],
            ['nombre' => 'Cuchillería San Martín', 'telefono' => '+54 221 455-9090', 'email' => 'info@cuchillos-sm.com'],
            ['nombre' => 'Calzados Don Mario', 'telefono' => '+54 351 422-3344', 'email' => 'donmario@calzados.com.ar'],
            ['nombre' => 'Accesorios Criollos & Co.', 'telefono' => '+54 223 499-1122', 'email' => 'proveedores@criollos.com.ar'],
        ];

        foreach ($proveedoresData as $pData) {
            Proveedor::firstOrCreate(['nombre' => $pData['nombre']], $pData);
        }
        $proveedores = Proveedor::all();

        // Asignar proveedor a productos existentes sin proveedor
        Producto::whereNull('proveedor_id')->get()->each(function ($prod) use ($proveedores) {
            $prod->update(['proveedor_id' => $proveedores->random()->id]);
        });

        $this->command->info('4. Creando 20 productos adicionales repartidos en todas las categorías...');
        $nuevosProductos = [
            // Bombachas
            ['nombre' => 'Bombacha Gaucha de Vestir', 'categoria' => 'Bombachas', 'talle' => 'XL', 'marca' => 'Pampa', 'precio_compra' => 12000, 'precio_venta' => 18500],
            ['nombre' => 'Bombacha de Gabardina Reforzada', 'categoria' => 'Bombachas', 'talle' => 'L', 'marca' => 'El Fogón', 'precio_compra' => 11000, 'precio_venta' => 16500],
            ['nombre' => 'Bombacha de Campo Térmica', 'categoria' => 'Bombachas', 'talle' => 'M', 'marca' => 'Don Gaucho', 'precio_compra' => 13500, 'precio_venta' => 19900],
            ['nombre' => 'Bombacha de Trabajo Fuerte', 'categoria' => 'Bombachas', 'talle' => 'XXL', 'marca' => 'Ranquel', 'precio_compra' => 9500, 'precio_venta' => 14200],

            // Boinas
            ['nombre' => 'Boina de Lana Fina Merino', 'categoria' => 'Boinas', 'talle' => null, 'marca' => 'Sureño', 'precio_compra' => 6500, 'precio_venta' => 9800],
            ['nombre' => 'Boina de Hilo Verano', 'categoria' => 'Boinas', 'talle' => null, 'marca' => 'El Palenque', 'precio_compra' => 4800, 'precio_venta' => 7200],
            ['nombre' => 'Sombrero de Carpincho Elegante', 'categoria' => 'Boinas', 'talle' => '58', 'marca' => 'Criollo', 'precio_compra' => 18000, 'precio_venta' => 27500],
            ['nombre' => 'Boina de Paño Tradicional', 'categoria' => 'Boinas', 'talle' => null, 'marca' => 'Pampa', 'precio_compra' => 5200, 'precio_venta' => 7900],

            // Cuchillos
            ['nombre' => 'Cuchillo Caza 8 pulgadas', 'categoria' => 'Cuchillos', 'talle' => null, 'marca' => 'Don Gaucho', 'precio_compra' => 18000, 'precio_venta' => 27000],
            ['nombre' => 'Daga de Plata y Alpaca', 'categoria' => 'Cuchillos', 'talle' => null, 'marca' => 'Sureño', 'precio_compra' => 42000, 'precio_venta' => 65000],
            ['nombre' => 'Cuchillo Fileteador de Asado', 'categoria' => 'Cuchillos', 'talle' => null, 'marca' => 'El Fogón', 'precio_compra' => 9800, 'precio_venta' => 14900],
            ['nombre' => 'Cuchillo de Campo Damasco', 'categoria' => 'Cuchillos', 'talle' => null, 'marca' => 'Criollo', 'precio_compra' => 31000, 'precio_venta' => 48000],

            // Monturas
            ['nombre' => 'Montura de Salto Profesional', 'categoria' => 'Monturas', 'talle' => null, 'marca' => 'Pampa', 'precio_compra' => 110000, 'precio_venta' => 165000],
            ['nombre' => 'Recado de Gala Completo', 'categoria' => 'Monturas', 'talle' => null, 'marca' => 'El Palenque', 'precio_compra' => 135000, 'precio_venta' => 195000],
            ['nombre' => 'Cincha de Lona y Cuero', 'categoria' => 'Monturas', 'talle' => null, 'marca' => 'Sureño', 'precio_compra' => 12500, 'precio_venta' => 18900],
            ['nombre' => 'Estribos de Bronce Labrados', 'categoria' => 'Monturas', 'talle' => null, 'marca' => 'Don Gaucho', 'precio_compra' => 22000, 'precio_venta' => 33000],

            // Botas
            ['nombre' => 'Bota Borcego Campero', 'categoria' => 'Botas', 'talle' => '41', 'marca' => 'Ranquel', 'precio_compra' => 32000, 'precio_venta' => 48000],
            ['nombre' => 'Bota de Montar Cuero Flor', 'categoria' => 'Botas', 'talle' => '43', 'marca' => 'El Fogón', 'precio_compra' => 45000, 'precio_venta' => 68000],
            ['nombre' => 'Alpargata de Carpincho', 'categoria' => 'Botas', 'talle' => '40', 'marca' => 'Criollo', 'precio_compra' => 8500, 'precio_venta' => 12900],
            ['nombre' => 'Zapatilla de Campo Urbana', 'categoria' => 'Botas', 'talle' => '42', 'marca' => 'Sureño', 'precio_compra' => 19500, 'precio_venta' => 29000],
        ];

        $catDict = $categorias->keyBy('nombre');
        foreach ($nuevosProductos as $prodData) {
            Producto::firstOrCreate(
                ['nombre' => $prodData['nombre']],
                [
                    'categoria_id' => $catDict[$prodData['categoria']]->id,
                    'proveedor_id' => $proveedores->random()->id,
                    'talle' => $prodData['talle'],
                    'marca' => $prodData['marca'],
                    'precio_compra' => $prodData['precio_compra'],
                    'precio_venta' => $prodData['precio_venta'],
                    'stock' => rand(20, 60),
                    'stock_minimo' => rand(3, 8),
                ]
            );
        }

        $allProductos = Producto::all();
        $this.command->info("Total productos listos: {$allProductos->count()}");

        $this->command->info('5. Generando simulación de 20 a 25 ventas por día durante los últimos 12 meses (365 días)...');

        $tiposPago = ['efectivo', 'efectivo', 'tarjeta', 'tarjeta', 'factura']; // 40% efectivo, 40% tarjeta, 20% factura
        $userIDs = $users->pluck('id')->toArray();
        $prodList = $allProductos->toArray();
        $prodCount = count($prodList);

        $ventasInsert = [];
        $itemsInsert = [];
        $unidadesVendidas = []; // seguimiento para compensación de stock

        // Desactivar temporalmente logs de queries para máxima velocidad
        DB::connection()->disableQueryLog();

        $startDate = Carbon::now()->subDays(365);
        $ventaIdCounter = (int) (Venta::max('id') ?? 0) + 1;

        DB::transaction(function () use (&$ventasInsert, &$itemsInsert, &$unidadesVendidas, &$ventaIdCounter, $startDate, $tiposPago, $userIDs, $prodList, $prodCount) {
            for ($day = 0; $day < 365; $day++) {
                $currentDate = $startDate->copy()->addDays($day);
                $ventasDelDiaCount = rand(20, 25);

                for ($v = 0; $v < $ventasDelDiaCount; $v++) {
                    $hora = rand(9, 19);
                    $minuto = rand(0, 59);
                    $segundo = rand(0, 59);
                    $ventaDate = $currentDate->copy()->setTime($hora, $minuto, $segundo);

                    $userId = $userIDs[array_rand($userIDs)];
                    $tipoPago = $tiposPago[array_rand($tiposPago)];

                    // Cantidad de items por venta (1 a 3 items)
                    $numItems = rand(1, 3);
                    $totalVenta = 0;
                    $currentVentaId = $ventaIdCounter++;

                    for ($i = 0; $i < $numItems; $i++) {
                        $pIndex = rand(0, $prodCount - 1);
                        $p = $prodList[$pIndex];
                        $cantidad = rand(1, 3);
                        $subtotal = $p['precio_venta'] * $cantidad;
                        $totalVenta += $subtotal;

                        $itemsInsert[] = [
                            'venta_id' => $currentVentaId,
                            'producto_id' => $p['id'],
                            'cantidad' => $cantidad,
                            'precio_compra' => $p['precio_compra'],
                            'precio_unitario' => $p['precio_venta'],
                            'subtotal' => $subtotal,
                            'created_at' => $ventaDate,
                            'updated_at' => $ventaDate,
                        ];

                        if (! isset($unidadesVendidas[$p['id']])) {
                            $unidadesVendidas[$p['id']] = 0;
                        }
                        $unidadesVendidas[$p['id']] += $cantidad;
                    }

                    $ventasInsert[] = [
                        'id' => $currentVentaId,
                        'user_id' => $userId,
                        'tipo_pago' => $tipoPago,
                        'total' => $totalVenta,
                        'created_at' => $ventaDate,
                        'updated_at' => $ventaDate,
                    ];

                    // Insertar en lotes de 1000 para optimizar memoria
                    if (count($ventasInsert) >= 1000) {
                        DB::table('ventas')->insert($ventasInsert);
                        DB::table('venta_items')->insert($itemsInsert);
                        $ventasInsert = [];
                        $itemsInsert = [];
                    }
                }
            }

            // Insertar remanentes
            if (count($ventasInsert) > 0) {
                DB::table('ventas')->insert($ventasInsert);
                DB::table('venta_items')->insert($itemsInsert);
            }
        });

        $totalVentasGeneradas = DB::table('ventas')->count();
        $this->command->info("¡Ventas generadas con éxito! Total de ventas registradas: {$totalVentasGeneradas}");

        $this->command->info('6. Aplicando compensación de stock...');
        // Garantizar que todos los productos mantengan un stock positivo realista
        foreach ($allProductos as $producto) {
            $vendidas = $unidadesVendidas[$producto->id] ?? 0;
            // Compensamos el stock para que quede en un rango saludable entre 5 y 40 unidades
            $stockRemanente = rand(5, 45);
            $producto->update(['stock' => $stockRemanente]);
        }

        $this->command->info('¡Compensación de stock finalizada con éxito!');
    }
}
