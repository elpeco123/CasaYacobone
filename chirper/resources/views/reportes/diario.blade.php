@extends('layouts.app')

@section('title', 'Reporte Diario')

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-calendar-day me-2" style="color: var(--cy-gold);"></i>Reporte Diario</h1>
        </div>
        <form method="GET" action="{{ route('reportes.diario') }}" class="d-flex gap-2 align-items-center">
            <input type="date" name="fecha" class="form-control form-control-dark" value="{{ $fecha->format('Y-m-d') }}">
            <button type="submit" class="btn btn-accent"><i class="bi bi-filter me-1"></i>Ver Fecha</button>
        </form>
    </div>

    {{-- Cards Summary --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="kpi-value">${{ number_format($totalVendido, 0, ',', '.') }}</div>
                <div class="kpi-label">Total Vendido en el Día</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-gold">
                <div class="kpi-icon"><i class="bi bi-safe2-fill"></i></div>
                <div class="kpi-value">${{ number_format($totalEfectivoEnCaja, 0, ',', '.') }}</div>
                <div class="kpi-label">Total Efectivo en Caja</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="bi bi-bag-check-fill"></i></div>
                <div class="kpi-value">{{ number_format($cantidadVendida) }}</div>
                <div class="kpi-label">Unidades Vendidas</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-red">
                <div class="kpi-icon"><i class="bi bi-boxes"></i></div>
                <div class="kpi-value">${{ number_format($valorStockRestante, 0, ',', '.') }}</div>
                <div class="kpi-label">Valor Stock Restante</div>
            </div>
        </div>
    </div>

    {{-- Desglose de Caja y Formas de Pago --}}
    <div class="card-glass mb-4" style="background: rgba(15, 52, 96, 0.35); border: 1px solid rgba(212, 165, 116, 0.25);">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <div class="p-2.5 rounded-3" style="background: rgba(212, 165, 116, 0.12); border: 1px solid rgba(212, 165, 116, 0.25);">
                        <span class="d-block small" style="color: #cbd5e1;">🪙 Cambio Inicial:</span>
                        <strong class="fs-6" style="color: var(--cy-gold);">${{ number_format($montoInicialCaja, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-2.5 rounded-3" style="background: rgba(74, 222, 128, 0.1); border: 1px solid rgba(74, 222, 128, 0.25);">
                        <span class="d-block small" style="color: #cbd5e1;">💵 Ventas Efectivo:</span>
                        <strong class="fs-6 text-success">+${{ number_format($ventasEfectivoDia, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-2.5 rounded-3" style="background: rgba(192, 132, 252, 0.1); border: 1px solid rgba(192, 132, 252, 0.25);">
                        <span class="d-block small" style="color: #cbd5e1;">💳 Ventas Tarjeta:</span>
                        <strong class="fs-6" style="color: #c084fc;">${{ number_format($ventasTarjetaDia, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-2.5 rounded-3" style="background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25);">
                        <span class="d-block small" style="color: #cbd5e1;">📄 Ventas Factura:</span>
                        <strong class="fs-6" style="color: #38bdf8;">${{ number_format($ventasFacturaDia, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Ventas del Día --}}
    <div class="card-glass">
        <div class="card-body">
            <h5 class="mb-3" style="font-weight: 700;">
                <i class="bi bi-receipt me-2" style="color: var(--cy-gold);"></i>
                Ventas del {{ $fecha->format('d/m/Y') }}
            </h5>

            <div class="table-responsive">
                <table class="table table-dark-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th># Venta</th>
                            <th>Hora</th>
                            <th>Vendedor</th>
                            <th>Productos</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventasDelDia as $venta)
                        <tr>
                            <td class="fw-bold">#{{ $venta->id }}</td>
                            <td>{{ $venta->created_at->format('H:i') }} hs</td>
                            <td>{{ $venta->user->name }}</td>
                            <td>
                                @foreach($venta->items as $item)
                                    <span class="badge bg-secondary me-1">
                                        {{ $item->cantidad }}x {{ $item->producto->nombre }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="text-end fw-bold" style="color: var(--cy-gold);">
                                ${{ number_format($venta->total, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-glass btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No se registraron ventas en esta fecha.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
