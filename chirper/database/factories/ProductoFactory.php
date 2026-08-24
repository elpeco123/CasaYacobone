<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $precioCompra = fake()->randomFloat(2, 500, 50000);

        return [
            'nombre' => fake()->words(3, true),
            'categoria_id' => Categoria::inRandomOrder()->first()?->id ?? 1,
            'talle' => fake()->randomElement(['S', 'M', 'L', 'XL', 'XXL', '38', '39', '40', '41', '42', '43', null]),
            'marca' => fake()->randomElement(['El Fogón', 'Pampa', 'Don Gaucho', 'Ranquel', 'El Palenque', 'Criollo', 'Sureño']),
            'precio_compra' => $precioCompra,
            'precio_venta' => round($precioCompra * fake()->randomFloat(2, 1.3, 2.0), 2),
            'stock' => fake()->numberBetween(0, 50),
            'stock_minimo' => fake()->numberBetween(2, 10),
        ];
    }
}
