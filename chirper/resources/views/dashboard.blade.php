@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="fade-in">
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-grid-1x2-fill me-2" style="color: var(--cy-gold);"></i>Dashboard</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Resumen general de Casa Yacobone</p>
        </div>
        <div>
            <span class="text-muted" style="font-size: 0.85rem;">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </span>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="bi bi-box-seam-fill"></i></div>
                <div class="kpi-value">{{ number_format($totalProductos) }}</div>
                <div class="kpi-label">Productos Registrados</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-gold">
                <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
                <div class="kpi-value">${{ number_format($valorStock, 0, ',', '.') }}</div>
                <div class="kpi-label">Valor Total del Stock</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="bi bi-cart-check-fill"></i></div>
                <div class="kpi-value">${{ number_format($ventasHoy, 0, ',', '.') }}</div>
                <div class="kpi-label">Ventas del Día ({{ $cantidadVentasHoy }})</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-red">
                <div class="kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="kpi-value">{{ $productosBajoStock->count() }}</div>
                <div class="kpi-label">Productos Stock Bajo</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Productos con Stock Bajo --}}
        <div class="col-lg-7">
            <div class="card-glass">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight: 700;">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                        Productos con Stock Bajo
                    </h5>
                    @if($productosBajoStock->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-2 mb-0">¡Todo el stock está en orden!</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-dark-custom table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Categoría</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Mínimo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productosBajoStock as $producto)
                                    <tr>
                                        <td>
                                            <strong>{{ $producto->nombre }}</strong>
                                            <br><small class="text-muted">{{ $producto->marca }}</small>
                                        </td>
                                        <td>{{ $producto->categoria->nombre }}</td>
                                        <td class="text-center fw-bold">{{ $producto->stock }}</td>
                                        <td class="text-center">{{ $producto->stock_minimo }}</td>
                                        <td>
                                            @if($producto->stock == 0)
                                                <span class="badge-stock-critico">
                                                    <i class="bi bi-x-circle-fill me-1"></i>Sin Stock
                                                </span>
                                            @else
                                                <span class="badge-stock-bajo">
                                                    <i class="bi bi-exclamation-circle-fill me-1"></i>Bajo
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Últimas Ventas --}}
        <div class="col-lg-5">
            <div class="card-glass">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight: 700;">
                        <i class="bi bi-clock-history me-2" style="color: var(--cy-gold);"></i>
                        Últimas Ventas
                    </h5>
                    @if($ultimasVentas->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-cart-x" style="font-size: 2.5rem; color: var(--cy-text-muted);"></i>
                            <p class="text-muted mt-2 mb-0">No hay ventas registradas aún.</p>
                        </div>
                    @else
                        @foreach($ultimasVentas as $venta)
                        <div class="d-flex justify-content-between align-items-center py-2 px-2 mb-2"
                             style="background: rgba(255,255,255,0.02); border-radius: 10px; border: 1px solid rgba(255,255,255,0.04);">
                            <div>
                                <div style="font-size: 0.88rem; font-weight: 600;">
                                    Venta #{{ $venta->id }}
                                </div>
                                <div style="font-size: 0.78rem; color: var(--cy-text-muted);">
                                    {{ $venta->user->name }} · {{ $venta->created_at->format('d/m H:i') }}
                                </div>
                            </div>
                            <div>
                                <span style="font-weight: 700; color: var(--cy-gold);">
                                    ${{ number_format($venta->total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card-glass mt-4">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight: 700;">
                        <i class="bi bi-lightning-fill me-2" style="color: var(--cy-accent);"></i>
                        Acciones Rápidas
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('ventas.create') }}" class="btn btn-accent">
                            <i class="bi bi-cart-plus-fill me-2"></i>Nueva Venta
                        </a>
                        <a href="{{ route('productos.create') }}" class="btn btn-gold">
                            <i class="bi bi-plus-circle-fill me-2"></i>Nuevo Producto
                        </a>
                        <a href="{{ route('reportes.diario') }}" class="btn btn-glass">
                            <i class="bi bi-file-bar-graph me-2"></i>Ver Reporte Diario
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
