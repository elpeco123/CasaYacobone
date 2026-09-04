@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1><i class="bi bi-person-plus-fill me-2" style="color: var(--cy-gold);"></i>Nuevo Usuario</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card-glass">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control form-control-dark @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico *</label>
                            <input type="email" class="form-control form-control-dark @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" class="form-control form-control-dark @error('password') is-invalid @enderror"
                                   id="password" name="password" required placeholder="Mínimo 6 caracteres">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label">Rol del Usuario *</label>
                            <select class="form-select form-select-dark @error('role') is-invalid @enderror"
                                    id="role" name="role" required>
                                <option value="vendedor" {{ old('role') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-glass">
                                <i class="bi bi-arrow-left me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-gold">
                                <i class="bi bi-check-circle-fill me-1"></i>Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
