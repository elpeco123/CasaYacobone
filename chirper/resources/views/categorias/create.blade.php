@extends('layouts.app')

@section('title', 'Nueva Categoría')

@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1><i class="bi bi-plus-circle-fill me-2" style="color: var(--cy-gold);"></i>Nueva Categoría</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
                <li class="breadcrumb-item active">Nueva</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card-glass">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('categorias.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Categoría *</label>
                            <input type="text" class="form-control form-control-dark @error('nombre') is-invalid @enderror"
                                   id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr style="border-color: var(--cy-border); margin: 2rem 0;">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categorias.index') }}" class="btn btn-glass">
                                <i class="bi bi-arrow-left me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-gold">
                                <i class="bi bi-check-circle-fill me-1"></i>Guardar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
