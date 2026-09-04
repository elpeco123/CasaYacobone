@extends('layouts.app')

@section('title', 'Reporte Semanal de Faltantes')

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-calendar-week me-2" style="color: var(--cy-gold);"></i>Reporte Semanal de Faltantes</h1>
        </div>
        <div class="text-muted" style="font-size: 0.9rem;">
            Período: <strong>{{ $fechaInicio->format('d/m/Y') }}</strong> al <strong>{{ $fechaFin->format('d/m/Y') }}</strong>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Productos Faltantes / Bajo Stock --}}
        <div class="col-lg-6">
            <div class="card-glass">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight: 700;">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                        Productos con Stock Bajo / Faltantes
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-dark-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Mín.</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productosFaltantes as $prod)
                                <tr>
                                    <td><strong>{{ $prod->nombre }}</strong></td>
                                    <td>{{ $prod->categoria->nombre }}</td>
                                    <td class="text-center fw-bold">{{ $prod->stock }}</td>
                                    <td class="text-center text-muted">{{ $prod->stock_minimo }}</td>
                                    <td>
                                        @if($prod->stock == 0)
                                            <span class="badge-stock-critico">Agotado</span>
                                        @else
                                            <span class="badge-stock-bajo">Bajo</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        ¡No hay productos con stock bajo!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Vendidos en la Semana --}}
        <div class="col-lg-6">
            <div class="card-glass">
                <div class="card-body">
                    <h5 class="mb-3" style="font-weight: 700;">
                        <i class="bi bi-trophy-fill text-warning me-2"></i>
                        Productos Más Vendidos de la Semana
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-dark-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th class="text-center">Cant. Vendida</th>
                                    <th class="text-end">Monto Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productosMasVendidos as $item)
                                <tr>
                                    <td><strong>{{ $item->producto->nombre }}</strong></td>
                                    <td>{{ $item->producto->categoria->nombre }}</td>
                                    <td class="text-center fw-bold" style="color: var(--cy-gold);">{{ $item->total_vendido }}</td>
                                    <td class="text-end fw-bold">${{ number_format($item->total_monto, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No hubo ventas registradas en la semana.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
