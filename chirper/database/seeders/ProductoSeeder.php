<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Los productos que comparten código de artículo son el mismo modelo en
     * distinto talle o color. El precio puede cambiar entre variantes.
     */
    public function run(): void
    {
        $categorias = Categoria::all()->keyBy('nombre');

        $productos = [
            // Bombachas: un artículo con varios talles al mismo precio.
            ['articulo' => 'BC-100', 'nombre' => 'Bombacha de Campo Clásica', 'categoria' => 'Bombachas', 'talle' => 'S', 'color' => 'Beige', 'precio_compra' => 8500, 'precio_venta' => 12500, 'stock' => 6, 'stock_minimo' => 2],
            ['articulo' => 'BC-100', 'nombre' => 'Bombacha de Campo Clásica', 'categoria' => 'Bombachas', 'talle' => 'M', 'color' => 'Beige', 'precio_compra' => 8500, 'precio_venta' => 12500, 'stock' => 15, 'stock_minimo' => 5],
            ['articulo' => 'BC-100', 'nombre' => 'Bombacha de Campo Clásica', 'categoria' => 'Bombachas', 'talle' => 'L', 'color' => 'Verde', 'precio_compra' => 8500, 'precio_venta' => 12500, 'stock' => 9, 'stock_minimo' => 3],
            // Mismo modelo, pero el talle grande sale más caro.
            ['articulo' => 'BC-100', 'nombre' => 'Bombacha de Campo Clásica', 'categoria' => 'Bombachas', 'talle' => 'XXL', 'color' => 'Beige', 'precio_compra' => 9800, 'precio_venta' => 14200, 'stock' => 4, 'stock_minimo' => 2],

            ['articulo' => 'BC-200', 'nombre' => 'Bombacha Bataraza', 'categoria' => 'Bombachas', 'talle' => 'M', 'color' => 'Gris', 'precio_compra' => 9200, 'precio_venta' => 13800, 'stock' => 10, 'stock_minimo' => 3],
            ['articulo' => 'BC-200', 'nombre' => 'Bombacha Bataraza', 'categoria' => 'Bombachas', 'talle' => 'L', 'color' => 'Gris', 'precio_compra' => 9200, 'precio_venta' => 13800, 'stock' => 7, 'stock_minimo' => 3],
            ['articulo' => 'BC-300', 'nombre' => 'Bombacha Infantil', 'categoria' => 'Bombachas', 'talle' => 'S', 'color' => null, 'precio_compra' => 5500, 'precio_venta' => 8200, 'stock' => 12, 'stock_minimo' => 4],

            // Boinas: un artículo, varios colores al mismo precio.
            ['articulo' => 'BO-100', 'nombre' => 'Boina Vasca Clásica', 'categoria' => 'Boinas', 'talle' => null, 'color' => 'Negro', 'precio_compra' => 3500, 'precio_venta' => 5200, 'stock' => 20, 'stock_minimo' => 5],
            ['articulo' => 'BO-100', 'nombre' => 'Boina Vasca Clásica', 'categoria' => 'Boinas', 'talle' => null, 'color' => 'Azul', 'precio_compra' => 3500, 'precio_venta' => 5200, 'stock' => 11, 'stock_minimo' => 5],
            ['articulo' => 'BO-200', 'nombre' => 'Boina de Fieltro Premium', 'categoria' => 'Boinas', 'talle' => null, 'color' => 'Marrón', 'precio_compra' => 5800, 'precio_venta' => 8700, 'stock' => 6, 'stock_minimo' => 3],
            ['articulo' => 'BO-300', 'nombre' => 'Boina Tejida a Mano', 'categoria' => 'Boinas', 'talle' => null, 'color' => 'Natural', 'precio_compra' => 4200, 'precio_venta' => 6500, 'stock' => 2, 'stock_minimo' => 4],

            // Cuchillos: artículo único, sin variantes.
            ['articulo' => 'CU-100', 'nombre' => 'Cuchillo Criollo 6"', 'categoria' => 'Cuchillos', 'talle' => null, 'color' => null, 'precio_compra' => 12000, 'precio_venta' => 18500, 'stock' => 7, 'stock_minimo' => 2],
            ['articulo' => 'CU-200', 'nombre' => 'Facón Artesanal', 'categoria' => 'Cuchillos', 'talle' => null, 'color' => null, 'precio_compra' => 25000, 'precio_venta' => 38000, 'stock' => 3, 'stock_minimo' => 2],
            ['articulo' => 'CU-300', 'nombre' => 'Cuchillo de Asado', 'categoria' => 'Cuchillos', 'talle' => null, 'color' => null, 'precio_compra' => 7500, 'precio_venta' => 11200, 'stock' => 12, 'stock_minimo' => 3],
            ['articulo' => 'CU-400', 'nombre' => 'Cuchillo Verijero', 'categoria' => 'Cuchillos', 'talle' => null, 'color' => null, 'precio_compra' => 15000, 'precio_venta' => 22500, 'stock' => 1, 'stock_minimo' => 2],

            // Monturas.
            ['articulo' => 'MO-100', 'nombre' => 'Montura Criolla Completa', 'categoria' => 'Monturas', 'talle' => null, 'color' => null, 'precio_compra' => 85000, 'precio_venta' => 125000, 'stock' => 2, 'stock_minimo' => 1],
            ['articulo' => 'MO-200', 'nombre' => 'Recado de Campo', 'categoria' => 'Monturas', 'talle' => null, 'color' => null, 'precio_compra' => 65000, 'precio_venta' => 95000, 'stock' => 3, 'stock_minimo' => 1],
            ['articulo' => 'MO-300', 'nombre' => 'Cabezada de Cuero Trenzado', 'categoria' => 'Monturas', 'talle' => null, 'color' => null, 'precio_compra' => 18000, 'precio_venta' => 27000, 'stock' => 5, 'stock_minimo' => 2],

            // Botas: un artículo con varios números.
            ['articulo' => 'BT-100', 'nombre' => 'Bota de Potro', 'categoria' => 'Botas', 'talle' => '41', 'color' => 'Suela', 'precio_compra' => 35000, 'precio_venta' => 52000, 'stock' => 3, 'stock_minimo' => 2],
            ['articulo' => 'BT-100', 'nombre' => 'Bota de Potro', 'categoria' => 'Botas', 'talle' => '42', 'color' => 'Suela', 'precio_compra' => 35000, 'precio_venta' => 52000, 'stock' => 4, 'stock_minimo' => 2],
            ['articulo' => 'BT-200', 'nombre' => 'Bota Texana Clásica', 'categoria' => 'Botas', 'talle' => '40', 'color' => 'Negro', 'precio_compra' => 28000, 'precio_venta' => 42000, 'stock' => 6, 'stock_minimo' => 3],
            ['articulo' => 'BT-300', 'nombre' => 'Bota de Campo Engrasada', 'categoria' => 'Botas', 'talle' => '43', 'color' => 'Marrón', 'precio_compra' => 22000, 'precio_venta' => 33000, 'stock' => 0, 'stock_minimo' => 2],
            ['articulo' => 'BT-400', 'nombre' => 'Alpargata Reforzada', 'categoria' => 'Botas', 'talle' => '41', 'color' => 'Negro', 'precio_compra' => 4500, 'precio_venta' => 6800, 'stock' => 25, 'stock_minimo' => 5],
        ];

        foreach ($productos as $data) {
            Producto::create([
                'articulo' => $data['articulo'],
                'nombre' => $data['nombre'],
                'categoria_id' => $categorias[$data['categoria']]->id,
                'talle' => $data['talle'],
                'color' => $data['color'],
                'precio_compra' => $data['precio_compra'],
                'precio_venta' => $data['precio_venta'],
                'stock' => $data['stock'],
                'stock_minimo' => $data['stock_minimo'],
            ]);
        }
    }
}
