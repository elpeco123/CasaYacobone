<?php

// Scratch script to generate Supabase SQL dump for simulated sales & 20 productos

$categorias = [
    'Bombachas' => 1,
    'Boinas' => 2,
    'Cuchillos' => 3,
    'Monturas' => 4,
    'Botas' => 5,
];

$proveedores = [
    1 => 'Talabartería El Gaucho',
    2 => 'Indumentaria Pampa S.A.',
    3 => 'Cuchillería San Martín',
    4 => 'Calzados Don Mario',
    5 => 'Accesorios Criollos & Co.',
];

$sql = [];
$sql[] = '-- SQL SEED DUMP FOR SUPABASE (20 PRODUCTOS + 12 MESES DE VENTAS DIARIAS 20-25 VENTAS/DIA)';
$sql[] = 'BEGIN;';

// 1. Insert Proveedores
$sql[] = '-- PROVEEDORES';
$sql[] = 'INSERT INTO public.proveedores (id, nombre, telefono, email, created_at, updated_at) VALUES';
$sql[] = "(1, 'Talabartería El Gaucho', '+54 11 4321-8899', 'contacto@elgaucho.com.ar', NOW(), NOW()),";
$sql[] = "(2, 'Indumentaria Pampa S.A.', '+54 11 4888-2233', 'ventas@pampasa.com.ar', NOW(), NOW()),";
$sql[] = "(3, 'Cuchillería San Martín', '+54 221 455-9090', 'info@cuchillos-sm.com', NOW(), NOW()),";
$sql[] = "(4, 'Calzados Don Mario', '+54 351 422-3344', 'donmario@calzados.com.ar', NOW(), NOW()),";
$sql[] = "(5, 'Accesorios Criollos & Co.', '+54 223 499-1122', 'proveedores@criollos.com.ar', NOW(), NOW())";
$sql[] = 'ON CONFLICT (id) DO UPDATE SET nombre = EXCLUDED.nombre;';

// 2. Insert 20 Productos
$nuevosProductos = [
    // Bombachas
    ['id' => 101, 'nombre' => 'Bombacha Gaucha de Vestir', 'cat' => 1, 'prov' => 2, 'talle' => 'XL', 'marca' => 'Pampa', 'pc' => 12000, 'pv' => 18500, 'stock' => 35],
    ['id' => 102, 'nombre' => 'Bombacha de Gabardina Reforzada', 'cat' => 1, 'prov' => 1, 'talle' => 'L', 'marca' => 'El Fogón', 'pc' => 11000, 'pv' => 16500, 'stock' => 28],
    ['id' => 103, 'nombre' => 'Bombacha de Campo Térmica', 'cat' => 1, 'prov' => 5, 'talle' => 'M', 'marca' => 'Don Gaucho', 'pc' => 13500, 'pv' => 19900, 'stock' => 40],
    ['id' => 104, 'nombre' => 'Bombacha de Trabajo Fuerte', 'cat' => 1, 'prov' => 2, 'talle' => 'XXL', 'marca' => 'Ranquel', 'pc' => 9500, 'pv' => 14200, 'stock' => 22],

    // Boinas
    ['id' => 105, 'nombre' => 'Boina de Lana Fina Merino', 'cat' => 2, 'prov' => 5, 'talle' => null, 'marca' => 'Sureño', 'pc' => 6500, 'pv' => 9800, 'stock' => 50],
    ['id' => 106, 'nombre' => 'Boina de Hilo Verano', 'cat' => 2, 'prov' => 1, 'talle' => null, 'marca' => 'El Palenque', 'pc' => 4800, 'pv' => 7200, 'stock' => 45],
    ['id' => 107, 'nombre' => 'Sombrero de Carpincho Elegante', 'cat' => 2, 'prov' => 5, 'talle' => '58', 'marca' => 'Criollo', 'pc' => 18000, 'pv' => 27500, 'stock' => 15],
    ['id' => 108, 'nombre' => 'Boina de Paño Tradicional', 'cat' => 2, 'prov' => 2, 'talle' => null, 'marca' => 'Pampa', 'pc' => 5200, 'pv' => 7900, 'stock' => 38],

    // Cuchillos
    ['id' => 109, 'nombre' => 'Cuchillo Caza 8 pulgadas', 'cat' => 3, 'prov' => 3, 'talle' => null, 'marca' => 'Don Gaucho', 'pc' => 18000, 'pv' => 27000, 'stock' => 18],
    ['id' => 110, 'nombre' => 'Daga de Plata y Alpaca', 'cat' => 3, 'prov' => 3, 'talle' => null, 'marca' => 'Sureño', 'pc' => 42000, 'pv' => 65000, 'stock' => 8],
    ['id' => 111, 'nombre' => 'Cuchillo Fileteador de Asado', 'cat' => 3, 'prov' => 3, 'talle' => null, 'marca' => 'El Fogón', 'pc' => 9800, 'pv' => 14900, 'stock' => 30],
    ['id' => 112, 'nombre' => 'Cuchillo de Campo Damasco', 'cat' => 3, 'prov' => 3, 'talle' => null, 'marca' => 'Criollo', 'pc' => 31000, 'pv' => 48000, 'stock' => 12],

    // Monturas
    ['id' => 113, 'nombre' => 'Montura de Salto Profesional', 'cat' => 4, 'prov' => 1, 'talle' => null, 'marca' => 'Pampa', 'pc' => 110000, 'pv' => 165000, 'stock' => 6],
    ['id' => 114, 'nombre' => 'Recado de Gala Completo', 'cat' => 4, 'prov' => 1, 'talle' => null, 'marca' => 'El Palenque', 'pc' => 135000, 'pv' => 195000, 'stock' => 4],
    ['id' => 115, 'nombre' => 'Cincha de Lona y Cuero', 'cat' => 4, 'prov' => 5, 'talle' => null, 'marca' => 'Sureño', 'pc' => 12500, 'pv' => 18900, 'stock' => 25],
    ['id' => 116, 'nombre' => 'Estribos de Bronce Labrados', 'cat' => 4, 'prov' => 5, 'talle' => null, 'marca' => 'Don Gaucho', 'pc' => 22000, 'pv' => 33000, 'stock' => 16],

    // Botas
    ['id' => 117, 'nombre' => 'Bota Borcego Campero', 'cat' => 5, 'prov' => 4, 'talle' => '41', 'marca' => 'Ranquel', 'pc' => 32000, 'pv' => 48000, 'stock' => 20],
    ['id' => 118, 'nombre' => 'Bota de Montar Cuero Flor', 'cat' => 5, 'prov' => 4, 'talle' => '43', 'marca' => 'El Fogón', 'pc' => 45000, 'pv' => 68000, 'stock' => 14],
    ['id' => 119, 'nombre' => 'Alpargata de Carpincho', 'cat' => 5, 'prov' => 4, 'talle' => '40', 'marca' => 'Criollo', 'pc' => 8500, 'pv' => 12900, 'stock' => 42],
    ['id' => 120, 'nombre' => 'Zapatilla de Campo Urbana', 'cat' => 5, 'prov' => 4, 'talle' => '42', 'marca' => 'Sureño', 'pc' => 19500, 'pv' => 29000, 'stock' => 26],
];

$sql[] = '-- 20 NUEVOS PRODUCTOS';
$sql[] = 'INSERT INTO public.productos (id, nombre, categoria_id, proveedor_id, talle, marca, precio_compra, precio_venta, stock, stock_minimo, created_at, updated_at) VALUES';
$prodValues = [];
foreach ($nuevosProductos as $p) {
    $talleVal = $p['talle'] ? "'{$p['talle']}'" : 'NULL';
    $prodValues[] = "({$p['id']}, '{$p['nombre']}', {$p['cat']}, {$p['prov']}, {$talleVal}, '{$p['marca']}', {$p['pc']}, {$p['pv']}, {$p['stock']}, 5, NOW(), NOW())";
}
$sql[] = implode(",\n", $prodValues)."\nON CONFLICT (id) DO UPDATE SET stock = EXCLUDED.stock, proveedor_id = EXCLUDED.proveedor_id;";

// Also update existing products if any
$sql[] = '-- ASIGNAR PROVEEDORES A PRODUCTOS ANTERIORES QUE NO TENGAN PROVEEDOR';
$sql[] = 'UPDATE public.productos SET proveedor_id = 1 WHERE proveedor_id IS NULL AND id % 5 = 0;';
$sql[] = 'UPDATE public.productos SET proveedor_id = 2 WHERE proveedor_id IS NULL AND id % 5 = 1;';
$sql[] = 'UPDATE public.productos SET proveedor_id = 3 WHERE proveedor_id IS NULL AND id % 5 = 2;';
$sql[] = 'UPDATE public.productos SET proveedor_id = 4 WHERE proveedor_id IS NULL AND id % 5 = 3;';
$sql[] = 'UPDATE public.productos SET proveedor_id = 5 WHERE proveedor_id IS NULL AND id % 5 = 4;';

// 3. Simulación de 20 a 25 Ventas al día por 12 meses
$sql[] = '-- VENTAS Y VENTA_ITEMS DE 12 MESES (20 A 25 VENTAS POR DIA)';

$tiposPago = ['efectivo', 'efectivo', 'tarjeta', 'tarjeta', 'factura'];
$userIDs = [1, 2]; // Admin and Vendedor

$ventaId = 1000;
$itemCount = 0;

$startDate = new DateTime('2025-08-28');

$ventasRows = [];
$itemsRows = [];

for ($day = 0; $day < 365; $day++) {
    $currentDate = clone $startDate;
    $currentDate->modify("+{$day} days");
    $dateStr = $currentDate->format('Y-m-d');

    $ventasHoyCount = rand(20, 25);

    for ($v = 0; $v < $ventasHoyCount; $v++) {
        $hora = rand(9, 19);
        $min = rand(0, 59);
        $sec = rand(0, 59);
        $timestamp = "{$dateStr} {$hora}:{$min}:{$sec}";

        $userId = $userIDs[array_rand($userIDs)];
        $tipoPago = $tiposPago[array_rand($tiposPago)];

        $numItems = rand(1, 3);
        $totalVenta = 0;
        $vId = $ventaId++;

        for ($i = 0; $i < $numItems; $i++) {
            $p = $nuevosProductos[array_rand($nuevosProductos)];
            $cant = rand(1, 3);
            $subtotal = $p['pv'] * $cant;
            $totalVenta += $subtotal;

            $itemsRows[] = "({$vId}, {$p['id']}, {$cant}, {$p['pc']}, {$p['pv']}, {$subtotal}, '{$timestamp}', '{$timestamp}')";
        }

        $ventasRows[] = "({$vId}, {$userId}, '{$tipoPago}', {$totalVenta}, '{$timestamp}', '{$timestamp}')";
    }
}

// Write in chunks to avoid single massive string memory overflow
$file = fopen('c:/Users/Victus/Desktop/proyecto/CasaYaco/chirper/simular_datos_supabase.sql', 'w');
fwrite($file, implode("\n", $sql)."\n");

fwrite($file, "INSERT INTO public.ventas (id, user_id, tipo_pago, total, created_at, updated_at) VALUES\n");
fwrite($file, implode(",\n", $ventasRows).";\n\n");

fwrite($file, "INSERT INTO public.venta_items (venta_id, producto_id, cantidad, precio_compra, precio_unitario, subtotal, created_at, updated_at) VALUES\n");
fwrite($file, implode(",\n", $itemsRows).";\n\n");

fwrite($file, "COMMIT;\n");
fclose($file);

echo "Archivo SQL generado con exito: simular_datos_supabase.sql\n";
