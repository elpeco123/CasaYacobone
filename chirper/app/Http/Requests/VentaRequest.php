<?php

namespace App\Http\Requests;

use App\Models\Venta;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipo_pago' => ['required', 'string', 'in:'.implode(',', array_keys(Venta::PAGOS))],
            // Datos del cliente: obligatorios solo si la venta va a cuenta corriente.
            'cliente_nombre' => ['required_if:tipo_pago,cuenta_corriente', 'nullable', 'string', 'max:80'],
            'cliente_apellido' => ['required_if:tipo_pago,cuenta_corriente', 'nullable', 'string', 'max:80'],
            'cliente_telefono' => ['required_if:tipo_pago,cuenta_corriente', 'nullable', 'string', 'max:30'],
            'cliente_direccion' => ['required_if:tipo_pago,cuenta_corriente', 'nullable', 'string', 'max:160'],
            'descuento_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
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
            'descuento_porcentaje.numeric' => 'El descuento debe ser un valor numérico.',
            'descuento_porcentaje.min' => 'El descuento no puede ser menor a 0%.',
            'descuento_porcentaje.max' => 'El descuento no puede superar el 100%.',
            'cliente_nombre.required_if' => 'Para vender a cuenta corriente necesitás el nombre del cliente.',
            'cliente_apellido.required_if' => 'Para vender a cuenta corriente necesitás el apellido del cliente.',
            'cliente_telefono.required_if' => 'Para vender a cuenta corriente necesitás el teléfono del cliente.',
            'cliente_direccion.required_if' => 'Para vender a cuenta corriente necesitás la dirección del cliente.',
            'items.required' => 'Debés agregar al menos un producto a la venta.',
            'items.min' => 'Debés agregar al menos un producto a la venta.',
            'items.*.producto_id.required' => 'Seleccioná un producto.',
            'items.*.producto_id.exists' => 'El producto seleccionado no existe.',
            'items.*.cantidad.required' => 'La cantidad es obligatoria.',
            'items.*.cantidad.min' => 'La cantidad mínima es 1.',
        ];
    }
}
