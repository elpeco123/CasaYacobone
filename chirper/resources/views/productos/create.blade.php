@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1><i class="bi bi-plus-circle-fill me-2" style="color: var(--cy-gold);"></i>Nuevo Producto</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                <li class="breadcrumb-item active">Nuevo</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-glass">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('productos.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nombre" class="form-label">Nombre del Producto *</label>
                                <input type="text" class="form-control form-control-dark @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="categoria_id" class="form-label">Categoría *</label>
                                <select class="form-select form-select-dark @error('categoria_id') is-invalid @enderror"
                                        id="categoria_id" name="categoria_id" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="marca" class="form-label">Marca *</label>
                                <input type="text" class="form-control form-control-dark @error('marca') is-invalid @enderror"
                                       id="marca" name="marca" value="{{ old('marca') }}" required>
                                @error('marca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="talle" class="form-label">Talle</label>
                                <input type="text" class="form-control form-control-dark @error('talle') is-invalid @enderror"
                                       id="talle" name="talle" value="{{ old('talle') }}"
                                       placeholder="Ej: M, L, 42...">
                                @error('talle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="stock" class="form-label">Stock Inicial *</label>
                                <input type="number" class="form-control form-control-dark @error('stock') is-invalid @enderror"
                                       id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="precio_compra" class="form-label">Precio Compra ($) *</label>
                                <input type="number" step="0.01" class="form-control form-control-dark @error('precio_compra') is-invalid @enderror"
                                       id="precio_compra" name="precio_compra" value="{{ old('precio_compra') }}" min="0" required>
                                @error('precio_compra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="precio_venta" class="form-label">Precio Venta ($) *</label>
                                <input type="number" step="0.01" class="form-control form-control-dark @error('precio_venta') is-invalid @enderror"
                                       id="precio_venta" name="precio_venta" value="{{ old('precio_venta') }}" min="0" required>
                                @error('precio_venta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="stock_minimo" class="form-label">Stock Mínimo *</label>
                                <input type="number" class="form-control form-control-dark @error('stock_minimo') is-invalid @enderror"
                                       id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', 0) }}" min="0" required>
                                @error('stock_minimo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr style="border-color: var(--cy-border); margin: 2rem 0;">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('productos.index') }}" class="btn btn-glass">
                                <i class="bi bi-arrow-left me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-gold">
                                <i class="bi bi-check-circle-fill me-1"></i>Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
