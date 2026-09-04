@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="fade-in">
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h1><i class="bi bi-box-seam-fill me-2" style="color: var(--cy-gold);"></i>Productos</h1>
        </div>
        <a href="{{ route('productos.create') }}" class="btn btn-gold">
            <i class="bi bi-plus-circle-fill me-1"></i>Nuevo Producto
        </a>
    </div>

    {{-- Summary KPI Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="bi bi-box-seam-fill"></i></div>
                <div class="kpi-value">{{ number_format($totalProductosCount) }}</div>
                <div class="kpi-label">Productos Registrados</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="bi bi-layers-fill"></i></div>
                <div class="kpi-value">{{ number_format($totalStockUnidades) }}</div>
                <div class="kpi-label">Unidades Totales en Stock</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-gold">
                <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
                <div class="kpi-value">${{ number_format($valorTotalStockCompra, 0, ',', '.') }}</div>
                <div class="kpi-label">Valor Stock (al Costo)</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="kpi-card kpi-purple" style="background: linear-gradient(135deg, rgba(155, 89, 182, 0.15), rgba(142, 68, 173, 0.25)); border: 1px solid rgba(155, 89, 182, 0.3);">
                <div class="kpi-icon" style="color: #af7ac5;"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="kpi-value" style="color: #d7bde2;">${{ number_format($valorTotalStockVenta, 0, ',', '.') }}</div>
                <div class="kpi-label">Valor Stock (a Venta)</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card-glass mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('productos.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text"
                           name="buscar"
                           class="form-control form-control-dark"
                           placeholder="Nombre o marca..."
                           value="{{ request('buscar') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_id" class="form-select form-select-dark">
                        <option value="">Todas</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="stock_bajo" value="1"
                               id="stockBajo" {{ request('stock_bajo') ? 'checked' : '' }}>
                        <label class="form-check-label" for="stockBajo" style="color: #f39c12; font-size: 0.85rem;">
                            Stock Bajo
                        </label>
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-accent flex-fill">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('productos.index') }}" class="btn btn-glass">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="card-glass">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-dark-custom table-hover align-middle mb-0" style="font-size: 0.84rem;">
                    <thead>
                        <tr>
                            <th style="width: 35px;">#</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th>Proveedor</th>
                            <th>Talle</th>
                            <th>Marca</th>
                            <th class="text-end text-nowrap">P. Compra</th>
                            <th class="text-end text-nowrap">P. Venta</th>
                            <th class="text-center text-nowrap">Stock</th>
                            <th class="text-end text-nowrap">Valor Stock</th>
                            <th class="text-center text-nowrap">Mín.</th>
                            <th class="text-nowrap">Estado</th>
                            <th class="text-center text-nowrap" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                        <tr>
                            <td class="text-muted">{{ $producto->id }}</td>
                            <td class="fw-bold">{{ $producto->nombre }}</td>
                            <td>{{ $producto->categoria->nombre }}</td>
                            <td>{{ $producto->proveedor->nombre ?? '—' }}</td>
                            <td>{{ $producto->talle ?? '—' }}</td>
                            <td>{{ $producto->marca }}</td>
                            <td class="text-end text-nowrap">${{ number_format($producto->precio_compra, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-nowrap" style="color: var(--cy-gold);">${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
                            <td class="text-center fw-bold text-nowrap">{{ $producto->stock }}</td>
                            <td class="text-end fw-bold text-nowrap">${{ number_format($producto->valor_stock_compra, 0, ',', '.') }}</td>
                            <td class="text-center text-muted text-nowrap">{{ $producto->stock_minimo }}</td>
                            <td class="text-nowrap">
                                @if($producto->stock == 0)
                                    <span class="badge-stock-critico" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"><i class="bi bi-x-circle-fill me-1"></i>Sin Stock</span>
                                @elseif($producto->stock <= $producto->stock_minimo)
                                    <span class="badge-stock-bajo" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"><i class="bi bi-exclamation-circle-fill me-1"></i>Bajo</span>
                                @else
                                    <span class="badge-stock-ok" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"><i class="bi bi-check-circle-fill me-1"></i>OK</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('productos.show', $producto) }}" class="btn btn-glass btn-sm px-2 py-1" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('productos.edit', $producto) }}" class="btn btn-glass btn-sm px-2 py-1" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                    <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-glass btn-sm px-2 py-1"
                                                style="background: rgba(231,76,60,0.15); border: 1px solid rgba(231,76,60,0.3); color: #e74c3c;"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem; color: var(--cy-text-muted);"></i>
                                <p class="text-muted mt-2 mb-0">No se encontraron productos.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($productos->count() > 0)
                    <tfoot>
                        <tr style="border-top: 2px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.03);">
                            <td colspan="8" class="fw-bold text-end">Totales Generales:</td>
                            <td class="text-center fw-bold text-nowrap text-warning">{{ number_format($totalStockUnidades) }}</td>
                            <td class="text-end fw-bold text-nowrap" style="color: var(--cy-gold);">${{ number_format($valorTotalStockCompra, 0, ',', '.') }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            {{-- Pagination --}}
            @if($productos->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $productos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
