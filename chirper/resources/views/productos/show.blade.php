@extends('layouts.app')

@section('title', $producto->nombre)

@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1><i class="bi bi-box-seam-fill me-2" style="color: var(--cy-gold);"></i>{{ $producto->nombre }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                <li class="breadcrumb-item active">{{ $producto->nombre }}</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-glass">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="form-label d-block">Categoría</span>
                                <span class="fw-bold" style="font-size: 1.05rem;">{{ $producto->categoria->nombre }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="form-label d-block">Marca</span>
                                <span class="fw-bold" style="font-size: 1.05rem;">{{ $producto->marca }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="form-label d-block">Talle</span>
                                <span class="fw-bold" style="font-size: 1.05rem;">{{ $producto->talle ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="form-label d-block">Precio Compra</span>
                                <span class="fw-bold" style="font-size: 1.05rem;">${{ number_format($producto->precio_compra, 2, ',', '.') }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="form-label d-block">Precio Venta</span>
                                <span class="fw-bold" style="font-size: 1.2rem; color: var(--cy-gold);">${{ number_format($producto->precio_venta, 2, ',', '.') }}</span>
                            </div>
                            <div class="mb-3">
                                <span class="form-label d-block">Margen</span>
                                @php
                                    $margen = $producto->precio_compra > 0
                                        ? (($producto->precio_venta - $producto->precio_compra) / $producto->precio_compra) * 100
                                        : 0;
                                @endphp
                                <span class="fw-bold" style="font-size: 1.05rem; color: var(--cy-success);">{{ number_format($margen, 1) }}%</span>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color: var(--cy-border);">

                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="form-label d-block">Stock Actual</span>
                                <span class="fw-bold" style="font-size: 2rem;">{{ $producto->stock }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="form-label d-block">Valor Stock (Costo)</span>
                                <span class="fw-bold" style="font-size: 1.5rem; color: var(--cy-gold);">${{ number_format($producto->valor_stock_compra, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="form-label d-block">Stock Mínimo</span>
                                <span class="fw-bold" style="font-size: 2rem;">{{ $producto->stock_minimo }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <span class="form-label d-block">Estado</span>
                                <div class="mt-1">
                                    @if($producto->stock == 0)
                                        <span class="badge-stock-critico" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            <i class="bi bi-x-circle-fill me-1"></i>Sin Stock
                                        </span>
                                    @elseif($producto->stock <= $producto->stock_minimo)
                                        <span class="badge-stock-bajo" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>Bajo
                                        </span>
                                    @else
                                        <span class="badge-stock-ok" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i>OK
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-color: var(--cy-border);">

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('productos.index') }}" class="btn btn-glass">
                            <i class="bi bi-arrow-left me-1"></i>Volver
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-gold">
                                <i class="bi bi-pencil-fill me-1"></i>Editar
                            </a>
                            @if(Auth::user()->isAdmin())
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                  onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-accent">
                                    <i class="bi bi-trash-fill me-1"></i>Eliminar
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
