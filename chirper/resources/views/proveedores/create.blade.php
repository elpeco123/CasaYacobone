@extends('layouts.app')

@section('title', 'Nuevo Proveedor')

@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1><i class="bi bi-plus-circle-fill me-2" style="color: var(--cy-gold);"></i>Nuevo Proveedor</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card-glass">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('proveedores.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nombre" class="form-label">Nombre del Proveedor *</label>
                                <input type="text" class="form-control form-control-dark @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control form-control-dark @error('telefono') is-invalid @enderror"
                                       id="telefono" name="telefono" value="{{ old('telefono') }}"
                                       placeholder="Ej: +54 11 1234-5678">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control form-control-dark @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}"
                                       placeholder="proveedor@ejemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr style="border-color: var(--cy-border); margin: 2rem 0;">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('proveedores.index') }}" class="btn btn-glass">
                                <i class="bi bi-arrow-left me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-gold">
                                <i class="bi bi-check-circle-fill me-1"></i>Guardar Proveedor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
