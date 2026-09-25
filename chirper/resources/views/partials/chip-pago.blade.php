{{-- Chip de forma de pago. Uso: @include('partials.chip-pago', ['tipo' => $venta->tipo_pago]) --}}
<span class="pay-chip pay-{{ $tipo ?? 'efectivo' }}">{{ \App\Models\Venta::etiquetaPago($tipo ?? 'efectivo') }}</span>
