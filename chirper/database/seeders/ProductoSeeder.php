<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = Categoria::all()->keyBy('nombre');

        $productos = [
            // Bombachas (4)
            ['nombre' => 'Bombacha de Campo Clásica', 'categoria' => 'Bombachas', 'talle' => 'M', 'marca' => 'El Fogón', 'precio_compra' => 8500, 'precio_venta' => 12500, 'stock' => 15, 'stock_minimo' => 5],
            ['nombre' => 'Bombacha Bataraza', 'categoria' => 'Bombachas', 'talle' => 'L', 'marca' => 'Pampa', 'precio_compra' => 9200, 'precio_venta' => 13800, 'stock' => 10, 'stock_minimo' => 3],
            ['nombre' => 'Bombacha de Campo Reforzada', 'categoria' => 'Bombachas', 'talle' => 'XL', 'marca' => 'Don Gaucho', 'precio_compra' => 10500, 'precio_venta' => 15900, 'stock' => 8, 'stock_minimo' => 3],
            ['nombre' => 'Bombacha Infantil', 'categoria' => 'Bombachas', 'talle' => 'S', 'marca' => 'Ranquel', 'precio_compra' => 5500, 'precio_venta' => 8200, 'stock' => 12, 'stock_minimo' => 4],

            // Boinas (4)
            ['nombre' => 'Boina Vasca Clásica', 'categoria' => 'Boinas', 'talle' => null, 'marca' => 'El Palenque', 'precio_compra' => 3500, 'precio_venta' => 5200, 'stock' => 20, 'stock_minimo' => 5],
            ['nombre' => 'Boina de Fieltro Premium', 'categoria' => 'Boinas', 'talle' => null, 'marca' => 'Criollo', 'precio_compra' => 5800, 'precio_venta' => 8700, 'stock' => 6, 'stock_minimo' => 3],
            ['nombre' => 'Boina Tejida a Mano', 'categoria' => 'Boinas', 'talle' => null, 'marca' => 'Sureño', 'precio_compra' => 4200, 'precio_venta' => 6500, 'stock' => 2, 'stock_minimo' => 4],
            ['nombre' => 'Boina Campera', 'categoria' => 'Boinas', 'talle' => null, 'marca' => 'Pampa', 'precio_compra' => 3000, 'precio_venta' => 4500, 'stock' => 18, 'stock_minimo' => 5],

            // Cuchillos (4)
            ['nombre' => 'Cuchillo Criollo 6"', 'categoria' => 'Cuchillos', 'talle' => null, 'marca' => 'Don Gaucho', 'precio_compra' => 12000, 'precio_venta' => 18500, 'stock' => 7, 'stock_minimo' => 2],
            ['nombre' => 'Facón Artesanal', 'categoria' => 'Cuchillos', 'talle' => null, 'marca' => 'El Fogón', 'precio_compra' => 25000, 'precio_venta' => 38000, 'stock' => 3, 'stock_minimo' => 2],
            ['nombre' => 'Cuchillo de Asado', 'categoria' => 'Cuchillos', 'talle' => null, 'marca' => 'Ranquel', 'precio_compra' => 7500, 'precio_venta' => 11200, 'stock' => 12, 'stock_minimo' => 3],
            ['nombre' => 'Cuchillo Verijero', 'categoria' => 'Cuchillos', 'talle' => null, 'marca' => 'Criollo', 'precio_compra' => 15000, 'precio_venta' => 22500, 'stock' => 1, 'stock_minimo' => 2],

            // Monturas (4)
            ['nombre' => 'Montura Criolla Completa', 'categoria' => 'Monturas', 'talle' => null, 'marca' => 'El Palenque', 'precio_compra' => 85000, 'precio_venta' => 125000, 'stock' => 2, 'stock_minimo' => 1],
            ['nombre' => 'Recado de Campo', 'categoria' => 'Monturas', 'talle' => null, 'marca' => 'Don Gaucho', 'precio_compra' => 65000, 'precio_venta' => 95000, 'stock' => 3, 'stock_minimo' => 1],
            ['nombre' => 'Montura de Paseo', 'categoria' => 'Monturas', 'talle' => null, 'marca' => 'Pampa', 'precio_compra' => 72000, 'precio_venta' => 108000, 'stock' => 1, 'stock_minimo' => 1],
            ['nombre' => 'Cabezada de Cuero Trenzado', 'categoria' => 'Monturas', 'talle' => null, 'marca' => 'Sureño', 'precio_compra' => 18000, 'precio_venta' => 27000, 'stock' => 5, 'stock_minimo' => 2],

            // Botas (4)
            ['nombre' => 'Bota de Potro', 'categoria' => 'Botas', 'talle' => '42', 'marca' => 'El Fogón', 'precio_compra' => 35000, 'precio_venta' => 52000, 'stock' => 4, 'stock_minimo' => 2],
            ['nombre' => 'Bota Texana Clásica', 'categoria' => 'Botas', 'talle' => '40', 'marca' => 'Ranquel', 'precio_compra' => 28000, 'precio_venta' => 42000, 'stock' => 6, 'stock_minimo' => 3],
            ['nombre' => 'Bota de Campo Engrasada', 'categoria' => 'Botas', 'talle' => '43', 'marca' => 'Criollo', 'precio_compra' => 22000, 'precio_venta' => 33000, 'stock' => 0, 'stock_minimo' => 2],
            ['nombre' => 'Alpargata Reforzada', 'categoria' => 'Botas', 'talle' => '41', 'marca' => 'Sureño', 'precio_compra' => 4500, 'precio_venta' => 6800, 'stock' => 25, 'stock_minimo' => 5],
        ];

        foreach ($productos as $data) {
            Producto::create([
                'nombre' => $data['nombre'],
                'categoria_id' => $categorias[$data['categoria']]->id,
                'talle' => $data['talle'],
                'marca' => $data['marca'],
                'precio_compra' => $data['precio_compra'],
                'precio_venta' => $data['precio_venta'],
                'stock' => $data['stock'],
                'stock_minimo' => $data['stock_minimo'],
            ]);
        }
    }
}
