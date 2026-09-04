@extends('layouts.app')

@section('title', 'Venta #' . $venta->id)

@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1><i class="bi bi-receipt me-2" style="color: var(--cy-gold);"></i>Venta #{{ $venta->id }}</h1>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-glass">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight: 700;">
                        <i class="bi bi-bag-fill me-2" style="color: var(--cy-gold);"></i>
                        Detalle de la Venta
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->items as $item)
                                <tr>
                                    <td>
                                        <strong class="text-white">{{ $item->producto->nombre }}</strong>
                                        <br><small style="color: #cbd5e1;">{{ $item->producto->marca }}</small>
                                    </td>
                                    <td class="text-light">{{ $item->producto->categoria->nombre }}</td>
                                    <td class="text-center fw-bold text-white">{{ $item->cantidad }}</td>
                                    <td class="text-end text-light">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold" style="color: var(--cy-gold);">
                                        ${{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @if(($venta->monto_descuento ?? 0) > 0)
                                    <tr>
                                        <td colspan="4" class="text-end" style="color: #e2e8f0; font-size: 0.95rem; font-weight: 600;">
                                            Subtotal:
                                        </td>
                                        <td class="text-end text-white" style="font-size: 1.05rem; font-weight: 700;">
                                            ${{ number_format($venta->subtotal ?? $venta->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end" style="color: #ff6b6b; font-size: 0.95rem; font-weight: 600;">
                                            Descuento ({{ number_format($venta->descuento_porcentaje, 0) }}%):
                                        </td>
                                        <td class="text-end fw-bold" style="color: #ff6b6b; font-size: 1.05rem;">
                                            -${{ number_format($venta->monto_descuento, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="text-end text-white" style="font-size: 1.15rem; font-weight: 700;">
                                        TOTAL COBRADO:
                                    </td>
                                    <td class="text-end" style="font-size: 1.35rem; font-weight: 800; color: var(--cy-gold); text-shadow: 0 0 10px rgba(212, 165, 116, 0.2);">
                                        ${{ number_format($venta->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-glass">
                <div class="card-body">
                    <h5 class="mb-3 text-white" style="font-weight: 700;">
                        <i class="bi bi-info-circle-fill me-2" style="color: var(--cy-gold);"></i>
                        Información
                    </h5>

                    <div class="mb-3">
                        <span class="form-label d-block" style="color: #cbd5e1 !important; font-weight: 600;">Fecha</span>
                        <span class="fw-bold text-white">{{ $venta->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="form-label d-block" style="color: #cbd5e1 !important; font-weight: 600;">Hora</span>
                        <span class="fw-bold text-white">{{ $venta->created_at->format('H:i:s') }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="form-label d-block" style="color: #cbd5e1 !important; font-weight: 600;">Vendedor</span>
                        <span class="fw-bold text-white">
                            <i class="bi bi-person-fill me-1" style="color: var(--cy-gold);"></i>{{ $venta->user->name }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="form-label d-block" style="color: #cbd5e1 !important; font-weight: 600;">Forma de Pago</span>
                        @if(($venta->tipo_pago ?? 'efectivo') === 'tarjeta')
                            <span class="badge" style="background: rgba(155, 89, 182, 0.25); color: #c084fc; border: 1px solid rgba(155, 89, 182, 0.5); font-size: 0.9rem; font-weight: 600;">
                                💳 Tarjeta
                            </span>
                        @elseif(($venta->tipo_pago ?? 'efectivo') === 'factura')
                            <span class="badge" style="background: rgba(52, 152, 219, 0.25); color: #38bdf8; border: 1px solid rgba(52, 152, 219, 0.5); font-size: 0.9rem; font-weight: 600;">
                                📄 Factura
                            </span>
                        @else
                            <span class="badge" style="background: rgba(46, 204, 113, 0.25); color: #4ade80; border: 1px solid rgba(46, 204, 113, 0.5); font-size: 0.9rem; font-weight: 600;">
                                💵 Efectivo
                            </span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="form-label d-block" style="color: #cbd5e1 !important; font-weight: 600;">Items</span>
                        <span class="fw-bold text-white">{{ $venta->items->count() }} productos ({{ $venta->items->sum('cantidad') }} unidades)</span>
                    </div>

                    <hr style="border-color: rgba(255,255,255,0.15);">

                    <a href="{{ route('ventas.index') }}" class="btn btn-glass w-100 text-light">
                        <i class="bi bi-arrow-left me-1"></i>Volver a Ventas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
