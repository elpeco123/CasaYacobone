@extends('layouts.app')

@section('title', 'Venta #' . $venta->id)

@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1><i class="bi bi-receipt me-2" style="color: var(--cy-gold);"></i>Venta #{{ $venta->id }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Venta #{{ $venta->id }}</li>
            </ol>
        </nav>
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
                                        <strong>{{ $item->producto->nombre }}</strong>
                                        <br><small class="text-muted">{{ $item->producto->marca }}</small>
                                    </td>
                                    <td>{{ $item->producto->categoria->nombre }}</td>
                                    <td class="text-center fw-bold">{{ $item->cantidad }}</td>
                                    <td class="text-end">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold" style="color: var(--cy-gold);">
                                        ${{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @if(($venta->monto_descuento ?? 0) > 0)
                                    <tr>
                                        <td colspan="4" class="text-end text-muted" style="font-size: 0.95rem; font-weight: 600;">
                                            Subtotal:
                                        </td>
                                        <td class="text-end text-light" style="font-size: 1rem; font-weight: 700;">
                                            ${{ number_format($venta->subtotal ?? $venta->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end text-danger" style="font-size: 0.95rem; font-weight: 600;">
                                            Descuento ({{ number_format($venta->descuento_porcentaje, 0) }}%):
                                        </td>
                                        <td class="text-end text-danger" style="font-size: 1rem; font-weight: 700;">
                                            -${{ number_format($venta->monto_descuento, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="text-end" style="font-size: 1.1rem; font-weight: 700;">
                                        TOTAL COBRADO:
                                    </td>
                                    <td class="text-end" style="font-size: 1.3rem; font-weight: 800; color: var(--cy-gold);">
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
                    <h5 class="mb-3" style="font-weight: 700;">
                        <i class="bi bi-info-circle-fill me-2" style="color: var(--cy-gold);"></i>
                        Información
                    </h5>

                    <div class="mb-3">
                        <span class="form-label d-block">Fecha</span>
                        <span class="fw-bold">{{ $venta->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="form-label d-block">Hora</span>
                        <span class="fw-bold">{{ $venta->created_at->format('H:i:s') }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="form-label d-block">Vendedor</span>
                        <span class="fw-bold">
                            <i class="bi bi-person-fill me-1"></i>{{ $venta->user->name }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="form-label d-block">Forma de Pago</span>
                        @if(($venta->tipo_pago ?? 'efectivo') === 'tarjeta')
                            <span class="badge" style="background: rgba(155, 89, 182, 0.2); color: #af7ac5; border: 1px solid rgba(155, 89, 182, 0.4); font-size: 0.9rem;">
                                💳 Tarjeta
                            </span>
                        @elseif(($venta->tipo_pago ?? 'efectivo') === 'factura')
                            <span class="badge" style="background: rgba(52, 152, 219, 0.2); color: #5adeff; border: 1px solid rgba(52, 152, 219, 0.4); font-size: 0.9rem;">
                                📄 Factura
                            </span>
                        @else
                            <span class="badge" style="background: rgba(46, 204, 113, 0.2); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.4); font-size: 0.9rem;">
                                💵 Efectivo
                            </span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="form-label d-block">Items</span>
                        <span class="fw-bold">{{ $venta->items->count() }} productos ({{ $venta->items->sum('cantidad') }} unidades)</span>
                    </div>

                    <hr style="border-color: var(--cy-border);">

                    <a href="{{ route('ventas.index') }}" class="btn btn-glass w-100">
                        <i class="bi bi-arrow-left me-1"></i>Volver a Ventas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
