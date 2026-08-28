<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'talle' => ['nullable', 'string', 'max:10'],
            'marca' => ['required', 'string', 'max:255'],
            'precio_compra' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0', 'gte:precio_compra'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'categoria_id.required' => 'Seleccioná una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no existe.',
            'proveedor_id.required' => 'Seleccioná un proveedor.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'marca.required' => 'La marca es obligatoria.',
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'precio_compra.min' => 'El precio de compra no puede ser negativo.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.min' => 'El precio de venta no puede ser negativo.',
            'precio_venta.gte' => 'El precio de venta debe ser mayor o igual al precio de compra.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.min' => 'El stock no puede ser negativo.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
        ];
    }
}
