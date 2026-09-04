@extends('layouts.app')

@section('title', 'Editar Categoría')

@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1><i class="bi bi-pencil-fill me-2" style="color: var(--cy-gold);"></i>Editar Categoría</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card-glass">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('categorias.update', $categoria) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la Categoría *</label>
                            <input type="text" class="form-control form-control-dark @error('nombre') is-invalid @enderror"
                                   id="nombre" name="nombre" value="{{ old('nombre', $categoria->nombre) }}" required>
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
                                <i class="bi bi-check-circle-fill me-1"></i>Actualizar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
