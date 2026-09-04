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
        <div class="col-md-4">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="bi bi-cart-check-fill"></i></div>
                <div class="kpi-value">${{ number_format($ventasHoy, 0, ',', '.') }}</div>
                <div class="kpi-label">Ventas del Día ({{ $cantidadVentasHoy }})</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="bi bi-calendar-month-fill"></i></div>
                <div class="kpi-value">${{ number_format($ventasMesActual, 0, ',', '.') }}</div>
                <div class="kpi-label">Ventas del Mes ({{ ucfirst(now()->locale('es')->monthName) }})</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-purple" style="background: linear-gradient(135deg, rgba(155, 89, 182, 0.15), rgba(142, 68, 173, 0.25)); border: 1px solid rgba(155, 89, 182, 0.3);">
                <div class="kpi-icon" style="color: #af7ac5;"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="kpi-value" style="color: #d7bde2;">${{ number_format($ventasAnoActual, 0, ',', '.') }}</div>
                <div class="kpi-label">Total Vendido Anual ({{ now()->year }})</div>
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

            {{-- Ranking de Proveedores por Compras (Últimos 12 Meses) --}}
            <div class="card-glass mt-4">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight: 700;">
                        <i class="bi bi-truck me-2" style="color: var(--cy-gold);"></i>
                        Ranking de Proveedores — Compras (Últimos 12 Meses)
                    </h5>
                    @if($rankingProveedores->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-box-seam" style="font-size: 2.5rem; color: var(--cy-text-muted);"></i>
                            <p class="text-muted mt-2 mb-0">No se registran compras/productos asociados a proveedores en los últimos 12 meses.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-dark-custom table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Proveedor</th>
                                        <th class="text-center">Productos</th>
                                        <th class="text-center">Unidades Compradas</th>
                                        <th class="text-end">Inversión Total (Costo)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rankingProveedores as $index => $proveedor)
                                    <tr>
                                        <td class="text-center fw-bold">
                                            @if($index == 0)
                                                <span class="badge bg-warning text-dark"><i class="bi bi-award-fill"></i> 1</span>
                                            @elseif($index == 1)
                                                <span class="badge bg-secondary"><i class="bi bi-award-fill"></i> 2</span>
                                            @elseif($index == 2)
                                                <span class="badge" style="background: #cd7f32; color: white;"><i class="bi bi-award-fill"></i> 3</span>
                                            @else
                                                <span class="text-muted">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold">{{ $proveedor->nombre }}</td>
                                        <td class="text-center">{{ number_format($proveedor->total_productos) }}</td>
                                        <td class="text-center fw-bold text-info">{{ number_format($proveedor->total_unidades) }}</td>
                                        <td class="text-end fw-bold" style="color: var(--cy-gold);">
                                            ${{ number_format($proveedor->total_inversion, 0, ',', '.') }}
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

            {{-- Ventas por Forma de Pago & Caja --}}
            <div class="card-glass mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0" style="font-weight: 700; color: #ffffff;">
                            <i class="bi bi-wallet2 me-2" style="color: var(--cy-gold);"></i>
                            Cobros del Día & Efectivo en Caja
                        </h5>
                        <a href="{{ route('caja.index') }}" class="btn btn-gold btn-sm py-1 px-2.5 fw-bold" style="font-size: 0.78rem;">
                            <i class="bi bi-cash-stack me-1"></i>{{ $cajaHoy ? 'Editar Cambio' : 'Abrir Caja' }}
                        </a>
                    </div>

                    <div class="p-3 mb-3" style="background: rgba(15, 23, 42, 0.6); border-radius: 12px; border: 1px solid var(--cy-border);">
                        <div class="text-muted mb-2" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                            Caja y Cobros de Hoy
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.88rem;">
                            <span style="color: #cbd5e1;">🪙 Cambio Inicial (Apertura):</span>
                            <span class="fw-bold" style="color: var(--cy-gold);">${{ number_format($montoInicialCaja, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1.5" style="font-size: 0.88rem;">
                            <span style="color: #cbd5e1;">💵 Ventas en Efectivo:</span>
                            <span class="fw-bold text-success">+${{ number_format($ventasHoyPorForma['efectivo'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 px-2 my-1.5 rounded-2" style="background: rgba(212, 165, 116, 0.15); border: 1px solid rgba(212, 165, 116, 0.35); font-size: 0.92rem;">
                            <span style="color: #ffffff; font-weight: 700;">💰 Total Efectivo en Caja:</span>
                            <span class="fw-extrabold" style="color: #f6c078; font-size: 1.15rem;">${{ number_format($totalEfectivoEnCaja, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.88rem;">
                            <span style="color: #cbd5e1;">💳 Tarjeta:</span>
                            <span class="fw-bold" style="color: #c084fc;">${{ number_format($ventasHoyPorForma['tarjeta'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.88rem;">
                            <span style="color: #cbd5e1;">📄 Factura:</span>
                            <span class="fw-bold" style="color: #38bdf8;">${{ number_format($ventasHoyPorForma['factura'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-2" style="border-top: 1px dashed var(--cy-border); font-size: 0.95rem; font-weight: 700;">
                            <span style="color: #ffffff;">Total Ventas del Día:</span>
                            <span style="color: var(--cy-gold); font-size: 1.1rem;">${{ number_format($ventasHoy, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="p-3" style="background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px solid var(--cy-border);">
                        <div class="text-muted mb-2" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                            Ventas del Mes Actual
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.88rem;">
                            <span>💵 Efectivo:</span>
                            <span class="fw-bold text-success">${{ number_format($ventasMesPorForma['efectivo'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.88rem;">
                            <span>💳 Tarjeta:</span>
                            <span class="fw-bold" style="color: #af7ac5;">${{ number_format($ventasMesPorForma['tarjeta'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 0.88rem;">
                            <span>📄 Factura:</span>
                            <span class="fw-bold text-info">${{ number_format($ventasMesPorForma['factura'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 mt-2" style="border-top: 1px dashed var(--cy-border); font-size: 1rem; font-weight: 800;">
                            <span>Total del Mes:</span>
                            <span style="color: var(--cy-gold); font-size: 1.15rem;">${{ number_format($ventasMesPorForma['total'], 0, ',', '.') }}</span>
                        </div>
                    </div>
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
                        <a href="{{ route('ventas.create') }}" class="btn btn-accent text-uppercase fw-bold py-2">
                            <i class="bi bi-cart-plus-fill me-1"></i>Registrar Venta
                        </a>
                        <a href="{{ route('caja.index') }}" class="btn btn-gold text-uppercase fw-bold py-2">
                            <i class="bi bi-safe2-fill me-1"></i>Abrir / Gestionar Caja
                        </a>
                        <a href="{{ route('productos.create') }}" class="btn btn-glass py-2">
                            <i class="bi bi-plus-circle me-1"></i>Nuevo Producto
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
