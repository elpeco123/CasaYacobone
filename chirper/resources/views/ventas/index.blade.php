@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-cart-fill me-2" style="color: var(--cy-gold);"></i>Ventas</h1>
        </div>
        <a href="{{ route('ventas.create') }}" class="btn btn-accent">
            <i class="bi bi-cart-plus-fill me-1"></i>Nueva Venta
        </a>
    </div>

    <div class="card-glass">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Vendedor</th>
                            <th class="text-center">Forma de Pago</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td class="fw-bold text-white">{{ $venta->id }}</td>
                            <td class="text-light">{{ $venta->created_at->format('d/m/Y') }}</td>
                            <td style="color: #cbd5e1;">{{ $venta->created_at->format('H:i') }}</td>
                            <td class="text-white">
                                <i class="bi bi-person-fill me-1" style="color: var(--cy-gold);"></i>
                                {{ $venta->user->name }}
                            </td>
                            <td class="text-center">
                                @if(($venta->tipo_pago ?? 'efectivo') === 'tarjeta')
                                    <span class="badge" style="background: rgba(155, 89, 182, 0.25); color: #c084fc; border: 1px solid rgba(155, 89, 182, 0.5); font-weight: 600;">
                                        💳 Tarjeta
                                    </span>
                                @elseif(($venta->tipo_pago ?? 'efectivo') === 'factura')
                                    <span class="badge" style="background: rgba(52, 152, 219, 0.25); color: #38bdf8; border: 1px solid rgba(52, 152, 219, 0.5); font-weight: 600;">
                                        📄 Factura
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(46, 204, 113, 0.25); color: #4ade80; border: 1px solid rgba(46, 204, 113, 0.5); font-weight: 600;">
                                        💵 Efectivo
                                    </span>
                                @endif
                            </td>
                            <td class="text-end fw-bold" style="font-size: 1.05rem; color: var(--cy-gold);">
                                ${{ number_format($venta->total, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-glass btn-sm text-light">
                                    <i class="bi bi-eye me-1"></i>Ver Detalle
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-cart-x" style="font-size: 2.2rem; color: #94a3b8;"></i>
                                <p class="mt-2 mb-0" style="color: #cbd5e1;">No hay ventas registradas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ventas->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $ventas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
